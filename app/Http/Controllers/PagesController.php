<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Degree;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;



class PagesController extends Controller
{
    public function userProfile()
    {
        $user = User::find(1);

        if (! $user) {
            return response('User not found.', 404);
        }

        $bio = $user->profile?->bio ?? 'No bio available.';

        return response("{$user->name} - {$bio}");
    }
    public function userPost()
    {
        $user = User::find(1);
        foreach ($user->posts as $post) {
            echo "$user->name: $post->title - $post->content <br>";
        }
    }
    public function studentCourses()
    {
        $students = Student::with('courses')->orderBy('lname')->orderBy('fname')->get();

        $output = '';

        foreach ($students as $student) {
            foreach ($student->courses as $course) {
                $studentName = collect([$student->fname, $student->mname, $student->lname])
                    ->filter()
                    ->implode(' ');

                $output .= "{$studentName}&nbsp;&nbsp;&nbsp;&nbsp;{$course->course_name}<br>";
            }
        }

        return response($output ?: 'No student courses found.');
    }   

    public function sessionUserAccount()
    {
        return response()->json([
            'user_account_id' => Session::get('user_account_id'),
            'logged_user' => Session::get('logged_user'),
            'user_role' => Session::get('user_role'),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);
        $loginName = strtolower(trim($credentials['username']));

        if ($loginName === 'admin' && hash_equals('admin123', $credentials['password'])) {
            $request->session()->regenerate();
            $request->session()->forget(['student_id', 'student_name', 'teacher_id', 'teacher_name']);
            $request->session()->put('user_account_id', 1);
            $request->session()->put('logged_user', 'admin');
            $request->session()->put('user_role', 'admin');
            $request->session()->put('is_first_login', false);

            return redirect('/dashboard')->with('success', 'Login successful.');
        }

        if (! Schema::hasTable('user_account')) {
            return redirect('/login')->withErrors([
                'username' => 'Login database is not ready yet. Please try again in a moment.',
            ])->withInput();
        }

        $account = UserAccount::query()
            ->where(function ($query) use ($loginName) {
                $query->where('username', $loginName)
                    ->orWhere('email', $loginName);
            })
            ->first();

        if (!$account) {
            return redirect('/login')->withErrors([
                'username' => 'Username not found.',
            ])->withInput();
        }

        if (! $account->is_active) {
            return redirect('/login')->withErrors([
                'username' => 'This account is inactive. Please contact administrator.',
            ])->withInput();
        }

        if (!$account->password) {
            return redirect('/login')->withErrors([
                'username' => 'Account not set up for login. Please contact administrator.',
            ])->withInput();
        }

        $passwordValid = false;
        try {
            $passwordValid = Hash::check($credentials['password'], $account->password);
        } catch (\Exception $e) {
            if (password_verify($credentials['password'], $account->password)) {
                $passwordValid = true;
                $account->update(['password' => Hash::make($credentials['password'])]);
            }
        }

        if ($passwordValid) {
            $request->session()->regenerate();

            $student = null;
            $teacher = null;

            if ($account->role === 'student' && Schema::hasTable('students') && Schema::hasColumn('students', 'user_account_id')) {
                $student = $account->student()->first();
            }

            if ($account->role === 'teacher' && Schema::hasTable('teachers') && Schema::hasColumn('teachers', 'user_account_id')) {
                $teacher = $account->teacher()->first();
            }

            $displayName = match ($account->role) {
                'student' => $student ? trim($student->fname . ' ' . $student->lname) : $account->username,
                'teacher' => $teacher ? trim($teacher->fname . ' ' . $teacher->lname) : $account->username,
                default => $account->username,
            };

            if ($account->role === 'student' && $student) {
                $request->session()->put('student_id', $student->id);
                $request->session()->put('student_name', $displayName);
            } else {
                $request->session()->forget(['student_id', 'student_name']);
            }

            if ($account->role === 'teacher' && $teacher) {
                $request->session()->put('teacher_id', $teacher->id);
                $request->session()->put('teacher_name', $displayName);
            } else {
                $request->session()->forget(['teacher_id', 'teacher_name']);
            }

            $request->session()->put('user_account_id', $account->id);
            $request->session()->put('logged_user', $displayName);
            $request->session()->put('user_role', $account->role);
            $request->session()->put('is_first_login', $account->is_first_login);

            if ($account->is_first_login || $account->must_change_password) {
                return redirect('/change-password')->with('info', 'Please change your password.');
            }

            return redirect('/dashboard')->with('success', 'Login successful.');
        }

        return redirect('/login')->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully.');
    }

    public function showChangePassword()
    {
        if (!session()->has('user_account_id')) {
            return redirect('/login');
        }
        return view('change_password');
    }

    public function changePassword(Request $request)
    {
        if (!session()->has('user_account_id')) {
            return redirect('/login');
        }

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $account = UserAccount::find(session('user_account_id'));

        if (!$account || !Hash::check($validated['current_password'], $account->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $account->update([
            'password' => Hash::make($validated['new_password']),
            'is_first_login' => false,
            'must_change_password' => false,
        ]);

        // Update session
        $request->session()->put('is_first_login', false);

        return redirect('/dashboard')->with('success', 'Password changed successfully.');
    }

    public function dashboard()
    {
        $user = session('logged_user', 'User');
        $role = session('user_role');
        $student = null;

        if ($role === 'student' && session('student_id')) {
            $student = Student::with('degree')->find(session('student_id'));
        }

        return match ($role) {
            'admin' => view('dashboards.admin', compact('user')),
            'teacher' => view('dashboards.teacher', compact('user')),
            default => view('dashboards.student', compact('user', 'student')),
        };
    }

     public function demo()
    {
        return view('vendor.pagination.demo');
    }
}
