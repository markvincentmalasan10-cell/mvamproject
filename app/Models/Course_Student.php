<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course_Student extends Model
{
    protected $table = 'course__students';

    protected $fillable = [
        'course_id',
        'student_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
