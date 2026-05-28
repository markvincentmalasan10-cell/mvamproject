@extends('layout.format')

@section('title')
    Degrees
@endsection

@section('Content')
    <div class="toolbar">
        <h3>Degree List</h3>
        <a href="{{ route('degrees.create') }}" class="btn">Add Degree</a>
    </div>

    @if(session('success'))
        <p class="success-message">{{ session('success') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Degree Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($degrees as $degree)
                <tr>
                    <td>{{ $degree->id }}</td>
                    <td>{{ $degree->degree_title }}</td>
                    <td>
                        <div class="action-links">
                            <a href="{{ route('degrees.show', $degree->id) }}" class="btn">View</a>
                            <a href="{{ route('degrees.edit', $degree->id) }}" class="btn btn-secondary">Edit</a>
                            <button type="button" class="btn btn-danger" onclick="deleteDegree({{ $degree->id }}, this)">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No degree records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
