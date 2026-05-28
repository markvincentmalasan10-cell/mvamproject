<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'course_name',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'course__students', 'course_id', 'student_id');
    }
}
