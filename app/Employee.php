<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    const UPDATED_AT = null;
    
    public $fillable = ['id', 'company_id', 'name', 'status', 'processed_at'];

    /**
    * Get the company that owns the Employee
    */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }
}
