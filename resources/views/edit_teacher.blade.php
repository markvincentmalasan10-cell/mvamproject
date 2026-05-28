@extends('layout.format')

@section('title')
    Edit Teacher
@endsection

@section('Header')
    @parent
@endsection

@section('Content')
    <div class="form-card">
        <h2 class="page-title">Edit Teacher</h2>

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <form id="editTeacherForm">
            <input type="hidden" id="teacherId" value="{{ $teacher->id }}">

            <div class="form-grid">
                <div class="form-group">
                    <label for="fname">First Name:</label>
                    <input type="text" id="fname" name="fname" value="{{ old('fname', $teacher->fname) }}">
                </div>

                <div class="form-group">
                    <label for="mname">Middle Name:</label>
                    <input type="text" id="mname" name="mname" value="{{ old('mname', $teacher->mname) }}">
                </div>

                <div class="form-group">
                    <label for="lname">Last Name:</label>
                    <input type="text" id="lname" name="lname" value="{{ old('lname', $teacher->lname) }}">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $teacher->email) }}">
                </div>

                <div class="form-group">
                    <label for="contactno">Contact No:</label>
                    <input type="text" id="contactno" name="contactno" value="{{ old('contactno', $teacher->contactno) }}">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="updateTeacherBtn">Update</button>
                <a href="/teacher" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
