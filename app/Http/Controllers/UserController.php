<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function showLogin()
    {
        return view('loginPage');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = Schema::hasTable('user_account')
            ? UserAccount::orderBy('id', 'desc')->get()
            : collect();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'users' => $users,
            ]);
        }

        $user = session('logged_user', 'Admin');

        return view('users', compact('users', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:user_account,username',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,teacher,student',
            'is_active' => 'nullable|boolean',
        ]);

        $user = UserAccount::create([
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
            'must_change_password' => false,
            'is_first_login' => true,
        ]);

        return response()->json([
            'message' => 'User account added successfully.',
            'user' => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = UserAccount::findOrFail($id);

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('user_account', 'username')->ignore($user->id),
            ],
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:admin,teacher,student',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
            $data['must_change_password'] = false;
            $data['is_first_login'] = true;
        }

        $user->update($data);

        return response()->json([
            'message' => 'User account updated successfully.',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = UserAccount::findOrFail($id);

        if ((int) session('user_account_id') === (int) $user->id) {
            return response()->json([
                'message' => 'You cannot delete the account currently logged in.',
            ], 422);
        }

        DB::transaction(function () use ($user) {
            $user->student?->delete();
            $user->teacher?->delete();
            $user->delete();
        });

        return response()->json([
            'message' => 'User account deleted successfully.',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = UserAccount::where('username', $request->input('username'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
           
             return back()->with('msg', 'Wrong credentials. Please try again.')->withInput();
        }

        Session::put('logged_user', $user->username);
        Session::put('logged_id', $user->id);

        if ($user->is_first_login || $user->must_change_password) {
            return redirect('/change-password')
                ->with('message', 'Please change your password before continuing.');
        }

        return redirect()->intended('/students')->with('message', 'Login Successful!');
    }

    public function showChangePasswordForm(Request $request)
    {
        $userId = $request->query('user_id');

        if (!$userId) {
            return redirect('/')->with('message', 'User ID is required to change password.');
        }

        return view('changePassword', compact('userId'));
    }

    public function changePassword(Request $request)
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return redirect('/')->with('message', 'User ID is required to change password.');
        }

        $request->validate([
            'user_id' => 'required|exists:user_account,id',
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = UserAccount::find($request->user_id);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->is_first_login = false;
        $user->must_change_password = false;
        $user->save();

        if ($user->role === 'student') {
            return redirect('/')->with('message', 'Password changed successfully! Please log in with your new password.');
        }

        if ($user->role === 'admin') {
            return redirect('/admin/login')->with('message', 'Password changed successfully! Please log in with your new password.');
        }

        return redirect('/')->with('message', 'Password changed successfully! Please log in with your new password.');
    }

    public function sessionUserAccount()
    {
        return response()->json([
            'user_account_id' => Session::get('user_account_id'),
            'logged_user' => Session::get('logged_user'),
            'user_role' => Session::get('user_role'),
        ]);
    }
}
