@extends('layout.format')

@section('title')
    Teacher Details
@endsection

@section('Header')
    @parent
@endsection

@section('Content')
    <h2 class="page-title">Teacher Details</h2>

    <div class="details-list">
        <div class="detail-item">
            <strong>Full Name</strong>
            {{ $teacher->fname }} {{ $teacher->mname }} {{ $teacher->lname }}
        </div>

        <div class="detail-item">
            <strong>Email</strong>
            {{ $teacher->email }}
        </div>

        <div class="detail-item">
            <strong>Contact Number</strong>
            {{ $teacher->contactno }}
        </div>

        <div class="detail-item">
            <strong>Username</strong>
            {{ $teacher->userAccount->username ?? 'N/A' }}
        </div>
    </div>

    <div class="form-actions">
        <a href="/teacher/{{ $teacher->id }}/edit" class="btn">Edit</a>
        <a href="/teacher" class="btn btn-secondary">Back</a>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
