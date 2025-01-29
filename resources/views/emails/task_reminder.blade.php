<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Reminder</title>
</head>
<body>
<h2>⏳ Task Reminder: {{ $task->title }}</h2>
<p><strong>Description:</strong> {{ $task->description }}</p>
<p><strong>Due Date:</strong> {{ $task->due_date }}</p>

<p>Don't forget to complete your task before the deadline!</p>

<a href="{{ route('tasks.show', $task->id) }}">View Task</a>
</body>
</html>
