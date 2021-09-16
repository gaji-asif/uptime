<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'tbl_upload';
    protected $fillable = [
        'id','url_link','description','sendto_level','sendto_region','count','created_at','updated_at','image','company_id'
    ];
}
