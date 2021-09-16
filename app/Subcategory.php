<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'sub_category';
    protected $fillable = [
        'category_id','subcategory_name','user_access_level','region_id','status'
    ];

    public function category() {
		return $this->belongsTo('App\Categories');
	}
}
