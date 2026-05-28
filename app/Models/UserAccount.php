<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $table = 'user_account';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'must_change_password',
        'is_first_login',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
        'is_first_login' => 'boolean',
    ];

    public function student()
    {
        return $this->hasOne(Student::class, 'user_account_id', 'id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_account_id', 'id');
    }
}
