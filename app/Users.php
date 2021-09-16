<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'name','first_name','last_name', 'email', 'password','role','pic','address','website_url','access_code'
    ];
}
