@extends('layout.format')

@section('title')
    Teachers
@endsection

@section('Header')
    @parent
@endsection

@section('Content')
    Welcome, {{ $user }}! <br>

    <div class="toolbar">
        <h3>Teacher List</h3>
        <a href="/teacher/create" class="btn">Add Teacher</a>
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
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teachers as $teacher)
                <tr>
                    <td>{{ $teacher->lname }}, {{ $teacher->fname }} {{ $teacher->mname }}</td>
                    <td>{{ $teacher->email }}</td>
                    <td>{{ $teacher->contactno }}</td>
                    <td>{{ $teacher->userAccount->username ?? 'N/A' }}</td>
                    <td>
                        <div class="icon-actions">
                            <a href="/teacher/{{ $teacher->id }}" class="icon-btn icon-view" title="View Teacher" aria-label="View Teacher">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>

                            <button type="button" class="icon-btn icon-delete" title="Delete Teacher" aria-label="Delete Teacher" onclick="deleteTeacher({{ $teacher->id }}, this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 6h18"></path>
                                    <path d="M8 6V4h8v2"></path>
                                    <path d="M19 6l-1 14H6L5 6"></path>
                                    <path d="M10 11v6"></path>
                                    <path d="M14 11v6"></path>
                                </svg>
                            </button>

                            <a href="/teacher/{{ $teacher->id }}/edit" class="icon-btn icon-edit" title="Edit Teacher" aria-label="Edit Teacher">
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
        {{ $teachers->links() }}
    </div>
@endsection

@section('Footer')
    @parent
@endsection
