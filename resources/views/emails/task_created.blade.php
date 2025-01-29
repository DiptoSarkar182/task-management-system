<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Task Created</title>
</head>
<body>
<h2>New Task Created: {{ $task->title }}</h2>
<p><strong>Description:</strong> {{ $task->description }}</p>
<p><strong>Due Date:</strong> {{ $task->due_date }}</p>
<p><strong>Status:</strong> {{ ucfirst($task->status) }}</p>

<p>Click below to view the task:</p>
<a href="{{ route('tasks.show', $task->id) }}">View Task</a>

<p>Thank you!</p>
</body>
</html>
