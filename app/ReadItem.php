<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReadItem extends Model
{
    protected $table = 'readitem';
    protected $fillable = [
        'employee_id','readitem_id'
    ];
}
