<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = ['name'];
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
