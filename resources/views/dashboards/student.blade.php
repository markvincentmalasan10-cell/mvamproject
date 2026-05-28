@extends('layout.format')

@section('title')
    Student
@endsection

@section('Header')
    @parent
@endsection

@section('Content')
    @if(session('success'))
        <p class="success-message">{{ session('success') }}</p>
    @endif

    <div class="dashboard-panel">
        <p class="dashboard-eyebrow">Welcome, {{ $user }}</p>
        @if($student?->image_path)
            <div class="student-profile-summary">
                <img src="{{ asset($student->image_path) }}" alt="Student image" class="student-profile-image">
                <div>
                    <h3>{{ $student->fname }} {{ $student->lname }}</h3>
                    <p class="page-text">{{ $student->degree?->degree_title ?? 'No degree assigned' }}</p>
                    <p class="page-text">Image path: {{ $student->image_path }}</p>
                </div>
            </div>
        @else
            <p class="page-text">No image uploaded for this student account yet.</p>
        @endif
    </div>
@endsection

@section('Footer')
    @parent
@endsection
