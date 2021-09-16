<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchase_log';
    protected $fillable = [
        'id','employee_id','rewarditem_id','created_at','updated_at'
    ];

    public function reward()
    {
        return $this->belongsTo(Reward::class, 'rewarditem_id');
    }
}
