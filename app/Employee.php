<?php

namespace App;

use App\Utils\Uptime;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class Employee extends Authenticatable
{
    protected $table = 'employee';

    use HasApiTokens, Notifiable;

    protected $fillable = [
        'email',
        'company_id',
        'website',
        'password',
        'industry',
        'is_deleted',
        'phone_number',
        'image',
        'access_level',
        'full_name',
        'point_note',
        'is_request',
        'myplan',
        'past_jobs',
        'emp_reference',
        'myobjective',
        'forget_token',
        'userType',
        'business_url',
        'independent_category_id',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $appends = [
        'businessURL',
    ];

    public function builds()
    {
        return $this->hasMany(Builds::class);
    }

    public function wonBuilds()
    {
        return $this->builds()
            ->where('challenge_id', '=', 0)
            ->where('status', '1');
    }

    public function lostBuilds()
    {
        return $this->builds()
            ->where('challenge_id', '=', 0)
            ->where('status', '0');
    }

    public function allBuilds()
    {
        return $this->builds()
            ->where('status', '=', '1');
    }

    public function validations()
    {
        return $this->hasMany(Validations::class);
    }

    public function batchValidations()
    {
        return $this->hasManyThrough(Validations::class, Batch::class, 'email_to', 'batch_id', 'email', 'id');
    }

    public function submissionBatches()
    {
        return $this->hasMany(Batch::class);
    }

    public function reviewBatches()
    {
        return $this->hasMany(Batch::class, 'email_to', 'email');
    }

    // Define all resuable attribute accessors & mutators below

    // Automatically set default image if image present
    public function getImageAttribute()
    {
        if (empty($this->attributes['image'])) {
            return asset('images/avatar.png');
        }

        return Storage::disk('s3')->url('images/employee/' . $this->attributes['image']);
    }

    // Encapsulate all employee relation data in getter/setter methods below,
    // which can be reused throughout the application.

    // Get testimonials/promoters for an employee
    public function getPromoters($fromDate, $toDate, $search = null)
    {
        $validationsTable = (new Validations())->getTable();

        $employeeId = $this->getKey();

        $validations = Validations::query()
            ->whereHas('build', function ($query) use ($employeeId, $fromDate, $toDate, $search) {
                $query->submittedBy($employeeId)
                    ->between($fromDate, $toDate)
                    ->search($search)
                    ->active();
            })
//            ->where('employee_id', '!=', $employeeId) // Something weird, taken from old code
            ->get();

        $validationIds = $validations->pluck('id')->toArray();

        $employeeIds = $validations->where('employee_id', '!=', null)
            ->pluck('employee_id')
            ->toArray();

        $batchIds = $validations->where('batch_id', '!=', null)
            ->unique('batch_id')
            ->pluck('batch_id')
            ->toArray();

        $employees = Employee::query()
            ->with(['reviewBatches' => function ($query) use ($batchIds)  {
                $query->whereIn('id', $batchIds);
            }])
            ->withCount([
                'validations as approved_count' => function ($query) use ($validationIds) {
                    $query->approved()->whereIn('id', $validationIds);
                },
                'validations as rejected_count' => function ($query) use ($validationIds) {
                    $query->rejected()->whereIn('id', $validationIds);
                },
                'batchValidations as batch_approved_count' => function ($query) use ($validationIds, $validationsTable) {
                    $query->approved()->whereIn("{$validationsTable}.id", $validationIds);
                },
                'batchValidations as batch_rejected_count' => function ($query) use ($validationIds, $validationsTable) {
                    $query->rejected()->whereIn("{$validationsTable}.id", $validationIds);
                },
            ])
            ->where(function ($query) use ($employeeIds, $batchIds) {
                $query->whereIn('id', $employeeIds)
                    ->orWhereHas('reviewBatches', function ($query) use ($batchIds) {
                        $query->whereIn('id', $batchIds);
                    });
            })
            ->get();

        $employees = $employees->map(function ($employee) {
            $employee->approved_count += $employee->batch_approved_count;
            $employee->rejected_count += $employee->batch_rejected_count;

            return $employee;
        });


        $nonEmployees = Batch::query()
            ->select([
                'id',
                DB::raw("CONCAT(firstname,' ',lastname) as full_name"),
                DB::raw('email_to as email'),
                DB::raw(' \'\' as image'),
                DB::raw(' \'\' as phone_number'),
//                DB::raw("REPLACE(REPLACE(REPLACE(email_to,'-',''),'(',''),')','') as email"),
            ])
            ->withCount([
                'validations as approved_count' => function ($query) use ($validationIds) {
                    $query->approved()->whereIn('id', $validationIds);
                },
                'validations as rejected_count' => function ($query) use ($validationIds) {
                    $query->rejected()->whereIn('id', $validationIds);
                },
            ])
            ->doesntHave('reviewer')
            ->where('employee_id', $employeeId)
            ->whereIn('id', $batchIds)
            ->get();

        $nonEmployees = $nonEmployees->map(function ($nonEmployee) {
            $phoneNumber = Uptime::formatPhoneNumber($nonEmployee->email);
            $nonEmployee->email = $phoneNumber;

            return $nonEmployee;
        })->groupBy('email')
            ->map(function ($groupedNonEmployee, $email) {
                $first = $groupedNonEmployee->first();

                $totalAccepted = $groupedNonEmployee->reduce(function ($total, $emp) {
                    return $total + $emp->approved_count;
                }, 0);

                $totalRejected = $groupedNonEmployee->reduce(function ($total, $emp) {
                    return $total + $emp->rejected_count;
                }, 0);

                // IMPORTANT: Set email (actually phone number) as id,
                // to prevent breaking on other api/services.
                $first->id = $first->email;

                $first->approved_count = $totalAccepted;
                $first->rejected_count = $totalRejected;

                return $first;
            })->flatten();

        $promoters = $employees->merge($nonEmployees)->sortByDesc('approved_count');

        // Wrap inside an array to match the expected data
        return ['employee' => $promoters];
    }

    public function getProfileStatus()
    {
        $id = $this->getKey();

        $totalBuilds = Builds::where('employee_id', $id)->whereIn('status',array('1','2'))->count();
        $approvedBuilds = Builds::where('employee_id', $id)->where('status','1')->count();

        $buildSuccessRate = 0;

        if ($totalBuilds != 0) {
            $buildSuccessRate = floor(($approvedBuilds/ $totalBuilds) * 100);
        }

        $status = new \stdClass();
        $status->total = $totalBuilds;
        $status->approved = $approvedBuilds;
        $status->successRate = $buildSuccessRate;

        return $status;
    }

    public function getBusinessURLAttribute($business_url)
    {
        return $business_url;
    }
}
