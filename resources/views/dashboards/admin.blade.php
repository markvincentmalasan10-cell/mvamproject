@extends('layout.format')

@section('title')
    Admin
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
        <p class="page-text">This is the admin view. Admin accounts can manage students, degrees, and teacher accounts.</p>
    </div>
@endsection

@section('Footer')
    @parent
@endsection
