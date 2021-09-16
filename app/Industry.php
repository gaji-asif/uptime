<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $table = 'industry';
    protected $fillable = [
        'industry_name','company_id','longitude','latitude','location'];
}
