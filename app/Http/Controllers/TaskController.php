<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'due_date' => 'required|date|after:today',
            'status' => 'required|in:pending,in_progress,completed',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,webp|max:5120',
        ]);
        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $request->user()->tasks()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View|Factory|Application
    {
        if (auth()->user()->role === 'admin' || auth()->id() === $task->user_id) {
            return view('tasks.show', compact('task'));
        }

        abort(Response::HTTP_FORBIDDEN, 'You do not have permission to view this task.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // Allow access if the user is an admin OR the task owner
        if (auth()->user()->role === 'admin' || auth()->id() === $task->user_id) {
            return view('tasks.edit', compact('task'));
        }

        // If the user is not allowed, abort with a 403 error
        abort(Response::HTTP_FORBIDDEN, 'You do not have permission to edit this task.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'due_date' => 'required|date|after:today',
            'status' => 'required|in:pending,in_progress,completed',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,webp|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete the old file if it exists
            if ($task->attachment_path) {
                Storage::disk('public')->delete($task->attachment_path);
            }

            // Store the new file
            $validated['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        // Preserve user_id if the current user is an admin
        if (auth()->user()->role === 'admin') {
            $validated['user_id'] = $task->user_id;
        }

        $task->update($validated);

        return redirect()->route('dashboard')->with('success', 'Task updated successfully!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        // Check if the task has an attachment
        if ($task->attachment_path) {
            // Delete the file from storage
            Storage::disk('public')->delete($task->attachment_path);
        }

        // Delete the task from database
        $task->delete();

        return redirect()->route('dashboard')->with('success', 'Task deleted successfully!');
    }

    public function removeAttachment(Task $task)
    {
        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
            $task->update(['attachment_path' => null]);
        }

        return redirect()->route('tasks.edit', $task->id)->with('success', 'Attachment removed successfully!');
    }

    public function download(Task $task): BinaryFileResponse|RedirectResponse
    {
        if (!$task->attachment_path || !Storage::disk('public')->exists($task->attachment_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download(storage_path("app/public/{$task->attachment_path}"));
    }

}
