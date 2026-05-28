@extends('layout.format')

@section('title')
    Welcome Student
@endsection

@section('Header')
    @parent
@endsection

@section('Content')
    <div class="welcome-container">
        <h1>Welcome, Student!</h1>
        <p>Welcome to the student portal.</p>
    </div>
@endsection

@section('Footer')
    @parent
@endsection