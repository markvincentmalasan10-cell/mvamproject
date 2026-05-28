@extends('layout.format')

@section('title')
    Add Teacher
@endsection

@section('Header')
    @parent
@endsection

@section('Content')
    <div class="form-card">
        <h2 class="page-title">Add Teacher</h2>

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <form id="addTeacherForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="teacher_fname">First Name:</label>
                    <input type="text" id="teacher_fname" name="fname" value="{{ old('fname') }}">
                </div>

                <div class="form-group">
                    <label for="teacher_mname">Middle Name:</label>
                    <input type="text" id="teacher_mname" name="mname" value="{{ old('mname') }}">
                </div>

                <div class="form-group">
                    <label for="teacher_lname">Last Name:</label>
                    <input type="text" id="teacher_lname" name="lname" value="{{ old('lname') }}">
                </div>

                <div class="form-group">
                    <label for="teacher_email">Email:</label>
                    <input type="email" id="teacher_email" name="email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="teacher_contactno">Contact No:</label>
                    <input type="text" id="teacher_contactno" name="contactno" value="{{ old('contactno') }}">
                </div>

                <div class="form-group">
                    <label for="teacher_username">Username:</label>
                    <input type="text" id="teacher_username" name="username" value="{{ old('username') }}">
                </div>

                <div class="form-group">
                    <label for="teacher_password">Password:</label>
                    <input type="password" id="teacher_password" name="password">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="saveTeacher">Submit</button>
                <a href="/teacher" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
