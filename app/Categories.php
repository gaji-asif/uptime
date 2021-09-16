<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'category_name','company_id'
    ];

      public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
}
