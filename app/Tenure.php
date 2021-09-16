<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tenure extends Model
{
    protected $table = 'tenure';
    protected $fillable = [
        'employee_id','point'
    ];
}
