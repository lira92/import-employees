<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    const UPDATED_AT = null;
    
    public $fillable = ['id', 'company_id', 'name', 'processed_at'];

    /**
     * Create a new Employee instance.
     *
     * @return void
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->status = 1;
    }

    public function inactivate()
    {
        $this->status = 0;
    }

    /**
    * Get the company that owns the Employee
    */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }
}
