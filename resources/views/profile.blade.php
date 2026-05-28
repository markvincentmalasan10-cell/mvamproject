
@extends('layout.format')
   
@section('title')
    Profile
@endsection

@section('Header')
    @parent

@endsection

@section('Content')
    <h2 class="page-title">Profile</h2>

    @if($student)
        <div class="details-list">
            @if($student->image_path)
                <div class="detail-item">
                    <strong>Uploaded Image</strong>
                    <img src="{{ asset($student->image_path) }}" alt="Student image" class="student-profile-image">
                </div>
                <div class="detail-item">
                    <strong>Image Path</strong>
                    <span>{{ $student->image_path }}</span>
                </div>
            @else
                <div class="detail-item">
                    <strong>Uploaded Image</strong>
                    <span>No image uploaded yet.</span>
                </div>
            @endif

            <div class="detail-item">
                <strong>Name</strong>
                <span>{{ $student->fname }} {{ $student->mname }} {{ $student->lname }}</span>
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
        </div>
    @else
        <p class="page-text">No student profile found for this account.</p>
    @endif
@endsection

@section('Footer')
    @parent
@endsection
