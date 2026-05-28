
@extends('layout.format')
   
@section('title')
    Dashboard
@endsection

@section('Header')
    @parent

@endsection

@section('Content')
    Welcome, {{ $user }}! <br>

    <h2 class="page-title">Dashboard</h2>
    <p class="page-text">This is the dashboard page.</p>
@endsection

@section('Footer')
    @parent
@endsection
