<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'batch';
    public $incrementing = false;
    protected $casts = [
	    'id' => 'string',
	];
    protected $fillable = [
        'id', 'email_to','employee_id', 'firstname', 'lastname', 'phonenumber'
    ];
    protected $keyType = 'string';

    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'email_to', 'email');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function builds()
    {
        return $this->hasMany(Builds::class);
    }

    public function validations()
    {
        return $this->hasMany(Validations::class);
    }
}

