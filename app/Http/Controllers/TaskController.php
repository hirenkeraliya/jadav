<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class TaskController extends Controller implements HasMiddleware
{
    use ScopedToCompany;

    public static function middleware(): array
    {
        return [
            new Middleware('can:tasks.view',   only: ['index', 'myTasks']),
            new Middleware('can:tasks.create', only: ['store']),
            new Middleware('can:tasks.edit',   only: ['update']),
            new Middleware('can:tasks.delete', only: ['destroy']),
        ];
    }

    public function index(Project $project): View
    {
        $this->authorizeProject($project);
        $cid = $this->companyId();
        $tasks = $project->tasks()->with('assignee')->orderBy('status')->orderBy('due_date')->get();
        $users = User::whereHas('companies', fn($q) => $q->where('companies.id', $cid))->get();
        return view('tasks.index', compact('project', 'tasks', 'users'));
    }

    public function myTasks(): View
    {
        $cid = $this->companyId();
        $tasks = Task::whereHas('project', fn($q) => $q->where('company_id', $cid))
            ->where('assigned_to', auth()->id())
            ->with(['project'])
            ->orderBy('due_date')
            ->get();
        return view('tasks.my', compact('tasks'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'due_date'    => ['nullable', 'date'],
            'priority'    => ['required', 'in:low,medium,high'],
            'status'      => ['required', 'in:pending,in_progress,completed'],
        ]);

        Task::create(array_merge($data, ['project_id' => $project->id]));

        return back()->with('success', 'Task created.');
    }

    public function update(Request $request, Project $project, Task $task): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($task->project_id !== $project->id, 403);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'due_date'    => ['nullable', 'date'],
            'priority'    => ['required', 'in:low,medium,high'],
            'status'      => ['required', 'in:pending,in_progress,completed'],
        ]);

        $task->update($data);
        return back()->with('success', 'Task updated.');
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($task->project_id !== $project->id, 403);
        $task->delete();
        return back()->with('success', 'Task removed.');
    }

    private function authorizeProject(Project $project): void
    {
        abort_if($project->company_id !== $this->companyId(), 403);
    }
}
