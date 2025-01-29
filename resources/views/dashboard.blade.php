@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold text-center mb-6">Welcome to the Dashboard</h1>

        @auth
            <div class="text-center mb-6">
                <p class="text-gray-700 text-lg">Hello, <span class="font-semibold">{{ auth()->user()->name }}</span>!</p>
                <p class="text-gray-600">This is your private dashboard.</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-center">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Task Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Your Tasks</h3>
                    <a href="{{ route('tasks.create') }}"
                       class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                        + Create Task
                    </a>
                </div>

                <!-- Task Table -->
                @if ($tasks->isEmpty())
                    <p class="text-gray-500 text-center">No tasks available. Start by creating a new task!</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                            <tr class="bg-gray-200 text-gray-700">
                                <th class="border p-3 text-left">Title</th>
                                <th class="border p-3 text-left">Due Date</th>
                                <th class="border p-3 text-left">Status</th>
                                <th class="border p-3 text-left">Attachment</th>
                                <th class="border p-3 text-left">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($tasks as $task)
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="p-3">{{ $task->title }}</td>
                                    <td class="p-3">{{ $task->due_date }}</td>
                                    <td class="p-3 capitalize">
                                            <span class="px-2 py-1 text-white text-sm rounded-lg
                                                @if($task->status == 'pending') bg-yellow-500
                                                @elseif($task->status == 'in_progress') bg-blue-500
                                                @else bg-green-500 @endif">
                                                {{ $task->status }}
                                            </span>
                                    </td>
                                    <td class="p-3">
                                        @if ($task->attachment_path)
                                            <a href="{{ asset('storage/' . $task->attachment_path) }}" target="_blank" class="text-blue-500 underline">View</a>
                                        @else
                                            <span class="text-gray-400">No file</span>
                                        @endif
                                    </td>
                                    <td class="p-3 flex space-x-2">
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="text-yellow-500 hover:text-yellow-600">Edit</a>
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-600" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endauth
    </div>
@endsection
