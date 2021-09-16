<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LevelPresetChallenge extends Model
{
    protected $table = 'challenge';
    protected $fillable = [
        'image','challenge_text','company_id', 'status', 'point', 'category_id','subcategory_id','sent_in','end_on', 'preset_type','type','is_active','employee_id','sendto_level','sendto_region'
    ];
}
