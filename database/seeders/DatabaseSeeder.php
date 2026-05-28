<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use App\Models\UserAccount;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        UserAccount::updateOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'must_change_password' => false,
                'is_first_login' => false,
            ]
        );

        $juan = Student::updateOrCreate(
            ['email' => 'juan.delacruz@example.com'],
            [
                'fname' => 'Juan',
                'mname' => null,
                'lname' => 'Dela Cruz',
                'contactno' => '09123456789',
            ]
        );

        foreach (['ELEC1', 'ELEC2'] as $courseName) {
            $course = Course::updateOrCreate(
                ['course_name' => $courseName],
                ['course_name' => $courseName]
            );

            DB::table('course__students')->updateOrInsert([
                'student_id' => $juan->id,
                'course_id' => $course->id,
            ], [
                'updated_at' => now(),
                'created_at' => now(),
            ]);
        }
    }
}
