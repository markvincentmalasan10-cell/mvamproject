@if ($students->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Degree</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->lname }}, {{ $student->fname }} {{ $student->mname }}</td>
                        <td>
                            @if($student->relationLoaded('degree'))
                                {{ $student->degree?->degree_title ?? 'N/A' }}
                            @else
                                {{ $student->degree_title ?? 'N/A' }}
                            @endif
                        </td>
                        <td>{{ $student->contactno }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p>No students found.</p>
@endif
