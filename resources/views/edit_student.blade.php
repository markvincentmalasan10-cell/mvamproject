@extends('layout.format')

@section('title')
    Edit Student
@endsection

@section('Header')
    @parent
@endsection

@section('Content')
    <div class="form-card">
        <h2 class="page-title">Edit Student</h2>

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <form id="editStudentForm" action="/students/{{ $student->id }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="studentId" value="{{ $student->id }}">

            <div class="form-grid">
                <div class="form-group">
                    <label for="fname">First Name:</label>
                    <input type="text" id="fname" name="fname" value="{{ $student->fname }}">
                </div>

                <div class="form-group">
                    <label for="mname">Middle Name:</label>
                    <input type="text" id="mname" name="mname" value="{{ $student->mname }}">
                </div>

                <div class="form-group">
                    <label for="lname">Last Name:</label>
                    <input type="text" id="lname" name="lname" value="{{ $student->lname }}">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" value="{{ $student->email }}">
                </div>

                <div class="form-group full-width">
                    <label for="contactno">Contact No:</label>
                    <input type="text" id="contactno" name="contactno" value="{{ $student->contactno }}">
                </div>

                <div class="form-group full-width">
                    <label for="degree_id">Degree:</label>
                    <select id="degree_id" name="degree_id">
                        <option value="">Select Degree</option>
                        @foreach ($degrees as $degree)
                            <option value="{{ $degree->id }}" @selected($student->degree_id == $degree->id)>
                                {{ $degree->degree_title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" id="updateStudentBtn">Update</button>
                <a href="/student" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
