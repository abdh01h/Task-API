<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAdded extends Notification implements ShouldQueue // You have to run php artisan queue:work to get it up running
{
    use Queueable;

    private $task_id, $task_title, $due_date;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($id, $title, $due)
    {
        $this->task_id      = $id;
        $this->task_title   = $title;
        $this->due_date     = $due;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $task_url   = url('/tasks' . '/' . $this->task_id);
        $task_title = $this->task_title;
        $due_date   = $this->due_date;
        return (new MailMessage)
                    ->subject('New task have been added | ' . config('app.name'))
                    ->line('A new task have beeen added in your account')
                    ->line('Due date is ' . $due_date)
                    ->line('You can access the task using the link below: ')
                    ->action( $task_title, $task_url)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
