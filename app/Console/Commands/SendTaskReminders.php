<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Task;
use App\Mail\TaskReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send task reminder emails to users with tasks due in 3 days';

    public function handle()
    {
        $date = Carbon::now()->addDays(3)->format('Y-m-d');
        $users = User::all();

        foreach ($users as $user) {
            $tasks = Task::where('user_id', $user->id)
                         ->whereDate('due_date', $date)
                         ->where('is_completed', false)
                         ->get();

            if ($tasks->isNotEmpty()) {
                Mail::to($user->email)->queue(new TaskReminder($user, $tasks));
            }
        }

        $this->info('Task reminders queued!');
    }
}
