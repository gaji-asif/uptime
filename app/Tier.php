<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
     protected $table = 'tbl_tier';
    protected $fillable = [
        'id','tier_name'
    ];
}
