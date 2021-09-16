<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Duels extends Model
{
     protected $table = 'tbl_duels';
    protected $fillable = [
        'id','sender','receiver', 'challenge_id', 'status','point','expiry_date','created_at'
    ];
}
