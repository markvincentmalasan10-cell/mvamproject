@extends('layout.format')

@section('title')
    Degree Details
@endsection

@section('Content')
    <h2 class="page-title">Degree Details</h2>

    <div class="details-list">
        <div class="detail-item">
            <strong>ID</strong>
            <span>{{ $degree->id }}</span>
        </div>
        <div class="detail-item">
            <strong>Degree Title</strong>
            <span>{{ $degree->degree_title }}</span>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('degrees.edit', $degree->id) }}" class="btn">Edit</a>
        <a href="{{ route('degrees.index') }}" class="btn btn-secondary">Back</a>
    </div>
@endsection
