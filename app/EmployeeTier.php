<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeTier extends Model
{
    protected $table = 'employee_tier';
    protected $fillable = [
        'id','employee_id','tier_id','created_at','updated_at'
    ];
}
