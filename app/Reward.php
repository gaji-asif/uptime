<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $table = 'tbl_reward';
    protected $fillable = [
        'id','name','description','point','access_level','is_active','created_at','updated_at','image','company_id'
    ];
}
