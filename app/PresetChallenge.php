<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PresetChallenge extends Model
{
    protected $table = 'challenge';
    protected $fillable = [
        'image','challenge_text','company_id', 'status', 'point', 'category_id','subcategory_id','sent_in','end_on','type','preset_type','employee_id','is_active','sendto_level','sendto_region'
    ];
}
