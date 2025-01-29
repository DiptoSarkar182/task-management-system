<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $search = $request->input('search');
        $status = $request->input('status');

        // If the user is an admin, fetch all tasks; otherwise, fetch only their own
        $tasksQuery = $user->role === 'admin' ? Task::query() : $user->tasks();

        // Apply search filter if a search term is provided
        if ($search) {
            $tasksQuery->where('title', 'LIKE', "%{$search}%");
        }

        // Apply status filter if a status is selected
        if ($status && in_array($status, ['pending', 'in_progress', 'completed'])) {
            $tasksQuery->where('status', $status);
        }

        $tasks = $tasksQuery->get();

        return view('dashboard', compact('tasks', 'search', 'status'));
    }
}
