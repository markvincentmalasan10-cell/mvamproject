@extends('layout.format')

@section('title')
    Add Degree
@endsection

@section('Content')
    <div class="form-card">
        <h2 class="page-title">Add Degree</h2>

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <form id="addDegreeForm" action="{{ route('degrees.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="degree_title">Degree Title:</label>
                <input type="text" id="degree_title" name="degree_title" value="{{ old('degree_title') }}">
            </div>

            <div class="form-actions">
                <button type="submit" id="saveDegree">Save</button>
                <a href="{{ route('degrees.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection
