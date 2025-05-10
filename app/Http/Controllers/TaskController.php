<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Tag;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id()); // 🔐 Only fetch tasks for the logged-in user

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 🎯 Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // ✅ Filter by completion status
        if ($request->status === 'completed') {
            $query->where('is_completed', true);
        } elseif ($request->status === 'incomplete') {
            $query->where('is_completed', false);
        }

        // 🏷️ Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        // 📅 Sorting
        $sort = $request->input('sort', 'latest');
        $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

        // 🎯 Finalize query
        $tasks = $query->get();

        // 📋 Get all tags for dropdown
        $allTags = Tag::pluck('name');

        // 🔁 Return the view
        return view('tasks.index', compact('tasks', 'allTags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'is_completed' => 'sometimes|boolean',
            'tags' => 'nullable|string',
        ]);

        $validated['is_completed'] = $request->has('is_completed') ? $request->is_completed : false;
        $validated['user_id'] = auth()->id(); // Optional: comment this out if not using auth

        $task = Task::create($validated);

        // Handle tags
        if ($request->filled('tags')) {
            $tagIds = collect(explode(',', $request->input('tags')))
                        ->map(fn($name) => Tag::firstOrCreate(['name' => trim($name)]))
                        ->pluck('id');

            $task->tags()->sync($tagIds);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $tasks = Task::latest()->get();
        $allTags = Tag::pluck('name'); // ✅ Include this so the dropdown doesn't break

        return view('tasks.index', compact('task', 'tasks', 'allTags'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'is_completed' => 'sometimes|boolean',
            'tags' => 'nullable|string',
        ]);

        $validated['is_completed'] = $request->has('is_completed') ? $request->is_completed : $task->is_completed;

        $task->update($validated);

        // Handle tags
        if ($request->filled('tags')) {
            $tagIds = collect(explode(',', $request->input('tags')))
                        ->map(fn($name) => Tag::firstOrCreate(['name' => trim($name)]))
                        ->pluck('id');

            $task->tags()->sync($tagIds);
        } else {
            $task->tags()->detach(); // Remove all tags if none submitted
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function toggle(Task $task)
    {
        $task->is_completed = !$task->is_completed;
        $task->save();

        return redirect()->back()->with('success', 'Task status updated.');
    }
}
