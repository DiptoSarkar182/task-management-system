@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded-lg shadow">
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    <strong>Oops! Something went wrong.</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Title:</label>
                    <input type="text" name="title" value="{{ old('title', $task->title) }}"
                           class="w-full p-2 border rounded" required>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Description:</label>
                    <textarea name="description" class="w-full p-2 border rounded" rows="3" required>{{ old('description', $task->description) }}</textarea>
                </div>

                <!-- Due Date -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Due Date:</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $task->due_date) }}"
                           class="w-full p-2 border rounded" required>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Status:</label>
                    <select name="status" class="w-full p-2 border rounded" required>
                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- File Attachment -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Attachment (Optional):</label>
                    <input type="file" name="attachment" class="w-full p-2 border rounded">
                    @if ($task->attachment_path)
                        <p class="text-sm text-gray-500 mt-2">Current File:
                            <a href="{{ asset('storage/' . $task->attachment_path) }}" target="_blank" class="text-blue-500 underline">View</a>
                        </p>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
                    Update Task
                </button>
            </form>
            @if ($task->attachment_path)
                <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                    <p class="text-sm text-gray-500">
                        Current File:
                        <a href="{{ asset('storage/' . $task->attachment_path) }}" target="_blank" class="text-blue-500 underline">View</a>
                    </p>

                    <!-- Remove Attachment Form -->
                    <form action="{{ route('tasks.removeAttachment', ['task' => $task->id]) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to remove the attachment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 mt-2">
                            Remove Attachment
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
