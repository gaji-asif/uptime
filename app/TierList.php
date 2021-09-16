<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TierList extends Model
{
    protected $table = 'tbl_tierlist';
    protected $fillable = [
        'id','tier','access_level','uploads','challenges','points','subcategory','subcategory_value','validates'
    ];
}
