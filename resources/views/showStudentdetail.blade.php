@extends('layout.format')

@section('title')
    Students Details
@endsection

@section('Header')
    @parent
@endsection 

@section('Content')
    <h2 class="page-title">Details of the Student</h2>

    <div class="details-list">
        <div class="detail-item">
            <strong>ID</strong>
            <span>{{ $student->id }}</span>
        </div>
        <div class="detail-item">
            <strong>First Name</strong>
            <span>{{ $student->fname }}</span>
        </div>
        <div class="detail-item">
            <strong>Middle Name</strong>
            <span>{{ $student->mname }}</span>
        </div>
        <div class="detail-item">
            <strong>Last Name</strong>
            <span>{{ $student->lname }}</span>
        </div>
        <div class="detail-item">
            <strong>Email Address</strong>
            <span>{{ $student->email }}</span>
        </div>
        <div class="detail-item">
            <strong>Contact No.</strong>
            <span>{{ $student->contactno }}</span>
        </div>
        <div class="detail-item">
            <strong>Degree</strong>
            <span>{{ $student->degree?->degree_title ?? 'No degree assigned' }}</span>
        </div>
        <div class="detail-item">
            <strong>Image Path</strong>
            <span>{{ $student->image_path ?? 'No image uploaded' }}</span>
        </div>
        @if($student->image_path)
            <div class="detail-item">
                <strong>Uploaded Image</strong>
                <img src="{{ asset($student->image_path) }}" alt="Student image" style="max-width:180px;border-radius:8px;">
            </div>
        @endif
    </div>

    <div class="form-actions">
        <a href="/student/{{ $student->id }}/edit" class="btn">Edit</a>
        <a href="/student" class="btn btn-secondary">Back</a>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
