<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Activity Logs</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Activity Logs</h2>
    <table>
        <thead>
            <tr>
                <th>Log Name</th>
                <th>Description</th>
                <th>Subject</th>
                <th>Causer</th>
                <th>Properties</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->log_name }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->subject->name ?? $log->subject_type ?? '-' }}</td>
                    <td>{{ $log->causer->name ?? '-' }}</td>
                    <td>
                        <pre style="white-space: pre-wrap; word-break: break-all;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </td>
                    <td>{{ $log->created_at->toDateTimeString() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
