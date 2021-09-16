<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'position';
    protected $fillable = [
        'company_id','user_id','position_id'
    ];
}
