<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authtoken extends Model
{
    protected $table = 'auth_tokens';
    protected $fillable = [
        'user_id','token','type'
    ];
}
