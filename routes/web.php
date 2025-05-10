<?php

// routes/web.php
use Illuminate\Support\Facades\Mail;
use App\Models\Task;
use App\Models\User;
use App\Mail\TaskReminder;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\HomeController;
use Carbon\Carbon;


Route::get('/', function () {
    return redirect('/tasks');
});

Route::resource('tasks', TaskController::class);
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
// / Redirect root (/) to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes (Laravel UI)
Auth::routes();

// After login, redirect to tasks
Route::get('/home', [TaskController::class, 'index'])->middleware('auth')->name('home');

// Task routes with auth middleware
Route::middleware('auth')->group(function () {
    Route::resource('tasks', TaskController::class)->except(['show']);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
});

Route::get('/send-task-reminders', function () {
    $date = Carbon::now()->addDays(3)->format('Y-m-d');
    $users = User::all();

    foreach ($users as $user) {
        $tasks = Task::where('user_id', $user->id)
                     ->whereDate('due_date', $date)
                     ->where('is_completed', false) // 👈 Filter only incomplete tasks
                     ->get();

        if ($tasks->isNotEmpty()) {
            Mail::to($user->email)->queue(new TaskReminder($tasks, $user)); // 👈 Use queue
        }
    }

    return "Queued task reminders!";
});


?>
