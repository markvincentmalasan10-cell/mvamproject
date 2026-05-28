<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    protected $fillable = [
        'degree_title',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
