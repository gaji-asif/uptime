<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Auth\Authenticatable;


class Requests extends Model 
{
    protected $table = 'tbl_requests';
    protected $fillable = [
        'id','request_type','status','requested_id','from_table','data','employee_id','created_at','updated_at'
    ];

}
