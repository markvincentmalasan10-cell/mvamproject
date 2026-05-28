<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class TeacherController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('teachers')) {
            $teachers = new LengthAwarePaginator([], 0, 5);
            $user = session('logged_user', 'Admin');

            return view('teacher', compact('teachers', 'user'));
        }

        $query = Teacher::query();

        if (Schema::hasTable('user_account') && Schema::hasColumn('teachers', 'user_account_id')) {
            $query->with('userAccount');
        }

        $teachers = $query->paginate(5);
        $user = session('logged_user', 'Admin');

        return view('teacher', compact('teachers', 'user'));
    }

    public function create()
    {
        return view('add_teacher');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactno' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:user_account,username',
            'password' => 'required|string|min:6',
        ]);

        DB::transaction(function () use ($validated) {
            $userAccount = UserAccount::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'teacher',
                'is_active' => true,
                'must_change_password' => false,
                'is_first_login' => true,
            ]);

            Teacher::create([
                'fname' => $validated['fname'],
                'mname' => $validated['mname'] ?? null,
                'lname' => $validated['lname'],
                'email' => $validated['email'],
                'contactno' => $validated['contactno'],
                'user_account_id' => $userAccount->id,
            ]);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Teacher added successfully.',
            ]);
        }

        return redirect('/teacher')->with('success', 'Teacher added successfully.');
    }

    public function show(Request $request, string $id)
    {
        $teacher = Teacher::with('userAccount')->findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json($teacher);
        }

        return view('showTeacherdetail', compact('teacher'));
    }

    public function edit(string $id)
    {
        $teacher = Teacher::findOrFail($id);

        return view('edit_teacher', compact('teacher'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactno' => 'required|string|max:255',
        ]);

        $teacher = Teacher::findOrFail($id);

        DB::transaction(function () use ($teacher, $validated) {
            $teacher->update($validated);

            if ($teacher->userAccount) {
                $teacher->userAccount->update([
                    'email' => $validated['email'],
                ]);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Teacher updated successfully.',
            ]);
        }

        return redirect('/teacher')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(string $id)
    {
        $teacher = Teacher::findOrFail($id);

        DB::transaction(function () use ($teacher) {
            $userAccount = $teacher->userAccount;
            $teacher->delete();

            if ($userAccount && $userAccount->role === 'teacher') {
                $userAccount->delete();
            }
        });

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'message' => 'Teacher deleted successfully.',
            ]);
        }

        return redirect('/teacher')->with('success', 'Teacher deleted successfully.');
    }
}
