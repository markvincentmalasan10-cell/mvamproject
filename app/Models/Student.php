<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'email',
        'contactno',
        'degree_id',
        'user_account_id',
        'image_path',
    ];

    public function degree()
    {
        return $this->belongsTo(Degree::class, 'degree_id');
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course__students', 'student_id', 'course_id');
    }

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }
}
