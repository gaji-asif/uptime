<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Useruuid extends Model
{
    protected $table = 'user_uuid';
    protected $fillable = [
        'employee_id','uu_id','platform','token_id'
    ];
}
