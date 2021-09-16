<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification_list';
    protected $fillable = [
        'content_type','message','sender','receiver','receiver_type','is_read'
    ];
}
