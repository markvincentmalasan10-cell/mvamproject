@extends('layout.format')

@section('title')
    Edit Degree
@endsection

@section('Content')
    <div class="form-card">
        <h2 class="page-title">Edit Degree</h2>

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <form id="editDegreeForm">
            <input type="hidden" id="degreeId" value="{{ $degree->id }}">

            <div class="form-group">
                <label for="edit_degree_title">Degree Title:</label>
                <input type="text" id="edit_degree_title" name="degree_title" value="{{ old('degree_title', $degree->degree_title) }}">
            </div>

            <div class="form-actions">
                <button type="button" id="updateDegreeBtn">Update</button>
                <a href="{{ route('degrees.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection
