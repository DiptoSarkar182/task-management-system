@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 shadow rounded-lg">
        <h1 class="text-2xl font-bold text-gray-700 mb-4">{{ $task->title }}</h1>

        <p class="text-gray-600"><strong>Name:</strong> {{ $task->user->name }}</p>
        <p class="text-gray-600"><strong>Description:</strong> {{ $task->description }}</p>
        <p class="text-gray-600"><strong>Due Date:</strong> {{ $task->due_date }}</p>
        <p class="text-gray-600"><strong>Created At:</strong> {{ $task->created_at->format('d M Y, h:i A') }}</p>
        <p class="text-gray-600"><strong>Last Updated:</strong> {{ $task->updated_at->format('d M Y, h:i A') }}</p>
        <p class="text-gray-600"><strong>Status:</strong>
            <span class="px-2 py-1 text-white text-sm rounded-lg
                @if($task->status == 'pending') bg-yellow-500
                @elseif($task->status == 'in_progress') bg-blue-500
                @else bg-green-500 @endif">
                {{ ucfirst($task->status) }}
            </span>
        </p>

        @if ($task->attachment_path)
            <p class="text-gray-600"><strong>Attachment:</strong></p>

            @php
                $fileExtension = pathinfo($task->attachment_path, PATHINFO_EXTENSION);
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            @endphp

            @if(in_array(strtolower($fileExtension), $imageExtensions))
                <!-- Show Image -->
                <img src="{{ asset('storage/' . $task->attachment_path) }}" alt="Task Attachment"
                     class="w-32 h-32 rounded-lg shadow-md mt-3">
            @else
                <!-- Show Download Link for Non-Image Files -->
                <a href="{{ asset('storage/' . $task->attachment_path) }}" target="_blank" class="text-blue-500 underline">View Attachment</a>
            @endif
            <div class="mt-3">
                <a href="{{ route('tasks.download', $task->id) }}"
                   class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                    Download Attachment
                </a>
            </div>
        @endif

        <div class="mt-6">
            <h2 class="text-xl font-semibold text-gray-700">Comments</h2>

            @foreach ($task->comments as $comment)
                <div class="bg-gray-100 p-3 rounded-lg mt-3">
                    <p class="text-gray-600">{{ $comment->content }}</p>
                    <p class="text-sm text-gray-500">Posted by {{ $comment->user->name }} on {{ $comment->created_at->format('d M Y, h:i A') }}</p>
                </div>
            @endforeach

            <!-- Add Comment Form -->
            <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST" class="mt-4">
                @csrf
                <textarea name="content" rows="3" class="w-full p-2 border rounded" required placeholder="Add a comment..."></textarea>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-2">
                    Add Comment
                </button>
            </form>
        </div>

        <div class="mt-6 flex space-x-3">
            <a href="{{ route('tasks.edit', $task->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                Edit Task
            </a>

            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                    Delete Task
                </button>
            </form>
        </div>
    </div>
@endsection
