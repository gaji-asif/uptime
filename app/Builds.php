<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Builds extends Model
{
    protected $table = 'builds';
    protected $fillable = [
        'image','build_text','category_id', 'status', 'employee_id','company_id', 'challenge_id',
        'subcategory','duel_id','is_request','email_to','created_at','updated_at', 'batch_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function duel()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function validations()
    {
        return $this->hasMany(Validations::class);
    }

    public function scopeWin($query)
    {
        return $query->where('status', '1');
    }

    public function scopeLost($query)
    {
        return $query->where('status', '0');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', '-1');
    }

    public function scopeNotVerified($query)
    {
        return $query->where('status', '2');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', '-1');
    }

    public function scopeBetween($query, $fromDate, $toDate)
    {
        return $query->whereBetween('updated_at', [$fromDate, $toDate]);
    }

    public function scopeSearch($query, $text)
    {
        if (empty(trim($text))) {
            return $query;
        }

        $text = strtolower($text);

        return $query->whereRaw("LOWER(build_text) LIKE '%{$text}%'");
    }

    public function scopeSubmittedBy($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory');
    }
}
