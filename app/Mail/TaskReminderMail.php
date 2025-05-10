<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class TaskReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tasks;
    public $user;

    public function __construct($tasks, $user)
    {
        $this->tasks = $tasks;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Reminder',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}