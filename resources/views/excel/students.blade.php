<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #999999;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #e2e8f0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>All Students</h2>
    <p>Generated on {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Degree</th>
                <th>Image Path</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->id }}</td>
                    <td>{{ $student->lname }}, {{ $student->fname }} {{ $student->mname }}</td>
                    <td>{{ $student->userAccount->email ?? $student->email ?? 'No email' }}</td>
                    <td>{{ $student->contactno ?? 'N/A' }}</td>
                    <td>{{ $student->degree?->degree_title ?? 'No Degree' }}</td>
                    <td>{{ $student->image_path ?? 'No image uploaded' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No student records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
