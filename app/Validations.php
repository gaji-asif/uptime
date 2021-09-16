<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Validations extends Model
{
    protected $table = 'validations';

    protected $fillable = [
        'employee_id','status','build_id', 'win', 'batch_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function build()
    {
        return $this->belongsTo(Builds::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeApproved($query)
    {
        return $query->status('1');
    }

    public function scopeRejected($query)
    {
        return $query->status('0');
    }

    public function scopeReviewedBy($query, $id)
    {
        return $query->where(function ($query) use ($id) {
            $query->where('employee_id', $id)
                ->orWhere('batch_id', $id)
                ->orWhereHas('batch', function ($query) use ($id) {
                    $query->whereRaw(DB::raw("REPLACE(REPLACE(REPLACE(email_to,'-',''),'(',''),')','') like '%{$id}%'"));
                })
                ->orWhereHas('batch.reviewer', function ($query) use ($id) {
                    $query->where('id', $id);
                });
        });
    }
}
