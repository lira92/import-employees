<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    const UPDATED_AT = null;

    public $fillable = ['id', 'name'];
}
