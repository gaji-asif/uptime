<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LevelChallenge extends Model
{
    protected $table = 'challenge';
    protected $fillable = [
        'image','challenge_text','company_id', 'status', 'point', 'category_id','subcategory_id','sent_in','end_on', 'preset_type','type','employee_id','is_active','sendto_level','sendto_region'
    ];
}
