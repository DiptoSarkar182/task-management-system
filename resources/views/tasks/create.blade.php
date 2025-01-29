@extends('layouts.app')

@section('title', 'Create Task')

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
            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- User ID (Hidden) -->
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                <!-- Title -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Title:</label>
                    <input type="text" name="title" class="w-full p-2 border rounded" required>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Description:</label>
                    <textarea name="description" class="w-full p-2 border rounded" rows="3" required></textarea>
                </div>

                <!-- Due Date -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Due Date:</label>
                    <input type="date" name="due_date" class="w-full p-2 border rounded" required>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Status:</label>
                    <select name="status" class="w-full p-2 border rounded" required>
                        <option value="pending" selected>Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- File Attachment -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Attachment (Optional):</label>
                    <input type="file" name="attachment" class="w-full p-2 border rounded">
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
                    Create Task
                </button>
            </form>
        </div>
    </div>
@endsection
