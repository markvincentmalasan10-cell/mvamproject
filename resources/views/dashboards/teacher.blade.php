@extends('layout.format')

@section('title')
    Teacher
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
        <p class="page-text">This is the teacher view for class and student monitoring activities.</p>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
