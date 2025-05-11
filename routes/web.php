<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Task;
use App\Models\User;
use App\Mail\TaskReminder;
use App\Http\Controllers\TaskController;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root URL based on authentication status
Route::get('/', function () {
    return response('✅ Laravel is fully working.', 200);
});

// Authentication routes (Laravel UI)
Auth::routes();

// After login, redirect to tasks
Route::get('/home', [TaskController::class, 'index'])->middleware('auth')->name('home');

// Task routes protected by auth middleware
Route::middleware('auth')->group(function () {
    Route::resource('tasks', TaskController::class)->except(['show']);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
});

// Route to queue task reminder emails
Route::get('/send-task-reminders', function () {
    $date = Carbon::now()->addDays(3)->format('Y-m-d');
    $users = User::all();

    foreach ($users as $user) {
        $tasks = Task::where('user_id', $user->id)
                     ->whereDate('due_date', $date)
                     ->where('is_completed', false)
                     ->get();

        if ($tasks->isNotEmpty()) {
            Mail::to($user->email)->queue(new TaskReminder($tasks, $user));
        }
    }

    return "Queued task reminders!";
});