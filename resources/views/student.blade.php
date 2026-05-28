@extends('layout.format')

@section('title')
    Students
@endsection

@section('Header')
    @parent
@endsection 

@section('Content')

Welcome, {{$user}}! <br>

    <div class="toolbar">
        <h3>Student List</h3>
        <a href="/student/create" class="btn">Add Student</a>
    </div>

    @if(session('success'))
        <p class="success-message">{{ session('success') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Degree</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($students as $student)
            <tr>
                <td>{{ $student['lname'] }}, {{ $student['fname'] }}, {{ $student['mname'] }}</td>
                <td>{{ $student['email'] }}</td>
                <td>{{ $student['contactno'] }}</td>
                <td>{{ $student->degree->degree_title ?? 'N/A' }}</td>
                <td>
                    <div class="icon-actions">
                        <a href="/student/{{ $student['id'] }}" class="icon-btn icon-view" title="View Student" aria-label="View Student">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>

                        <button type="button" class="icon-btn icon-delete" title="Delete Student" aria-label="Delete Student" onclick="deleteStudent({{ $student['id'] }}, this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 6h18"></path>
                                <path d="M8 6V4h8v2"></path>
                                <path d="M19 6l-1 14H6L5 6"></path>
                                <path d="M10 11v6"></path>
                                <path d="M14 11v6"></path>
                            </svg>
                        </button>

                        <a href="/student/{{ $student['id'] }}/edit" class="icon-btn icon-edit" title="Edit Student" aria-label="Edit Student">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                            </svg>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $students->links() }}
    </div>
@endsection

@section('Footer')
    @parent
@endsection
