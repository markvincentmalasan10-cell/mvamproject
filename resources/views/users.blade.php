@extends('layout.format')

@section('title')
    User Accounts
@endsection

@section('Header')
    @parent
@endsection

@section('Content')
    Welcome, {{ $user }}! <br>

    <div id="userAccountsPage" data-users-url="{{ url('/users') }}">
        <div class="toolbar">
            <h3>User Account List</h3>
            <button type="button" id="resetUserForm" class="btn btn-secondary">New User</button>
        </div>

        <p id="userAjaxMessage" class="success-message" style="display:none;"></p>
        <p id="userAjaxError" class="error-message" style="display:none;"></p>

        <form id="userForm" class="form-card" autocomplete="off">
            @csrf
            <input type="hidden" id="user_id" name="user_id">

            <div class="form-grid">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                        Active account
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" id="saveUserButton">Save User</button>
                <button type="button" id="cancelUserEdit" class="btn btn-secondary" style="display:none;">Cancel Edit</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @foreach ($users as $account)
                    <tr data-id="{{ $account->id }}">
                        <td>{{ $account->username }}</td>
                        <td>{{ $account->email ?? 'N/A' }}</td>
                        <td>{{ ucfirst($account->role) }}</td>
                        <td>{{ $account->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <div class="icon-actions">
                                <button type="button" class="icon-btn icon-edit edit-user"
                                    data-user='@json($account)'
                                    title="Edit User" aria-label="Edit User">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </button>

                                <button type="button" class="icon-btn icon-delete delete-user"
                                    data-id="{{ $account->id }}"
                                    title="Delete User" aria-label="Delete User">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4h8v2"></path>
                                        <path d="M19 6l-1 14H6L5 6"></path>
                                        <path d="M10 11v6"></path>
                                        <path d="M14 11v6"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
