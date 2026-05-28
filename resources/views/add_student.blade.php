@extends('layout.format')

@section('title')
    Add Student
@endsection

@section('Header')
    @parent
@endsection 

@section('Content')
    <div class="form-card">
        <h2 class="page-title">Add Student</h2>

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <form id="addStudentForm" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="fname">First Name:</label>
                    <input type="text" id="fname" name="fname"
                    value="{{ old('fname') }}">
                </div>

                <div class="form-group">
                    <label for="mname">Middle Name:</label>
                    <input type="text" id="mname" name="mname"
                    value="{{ old('mname') }}">
                </div>

                <div class="form-group">
                    <label for="lname">Last Name:</label>
                    <input type="text" id="lname" name="lname"
                    value="{{ old('lname') }}">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email"
                    value="{{ old('email') }}">
                </div>

                <div class="form-group full-width">
                    <label for="contactno">Contact No:</label>
                    <input type="text" id="contactno" name="contactno"
                    value="{{ old('contactno') }}">
                </div>

                <div class="form-group full-width">
                    <label for="degree_id">Degree:</label>
                    <select id="degree_id" name="degree_id">
                        <option value="">Select Degree</option>
                        @foreach ($degrees as $degree)
                            <option value="{{ $degree->id }}">{{ $degree->degree_title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-group full-width">
                    <label for="image">Upload Image:</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="saveStudent">Submit</button>
                <a href="/student" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
