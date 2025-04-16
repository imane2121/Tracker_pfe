<?php

namespace App\Notifications;

use App\Models\Collecte;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributorRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $collecte;
    protected $status; // 'accepted' or 'rejected'
    protected $user;

    public function __construct(Collecte $collecte, string $status, $user)
    {
        $this->collecte = $collecte;
        $this->status = $status;
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['database']; // Only use database notifications
    }

    public function toMail($notifiable): MailMessage
    {
        $message = new MailMessage;
        
        if ($this->status === 'accepted') {
            $message->subject('Your request to join a collection has been accepted')
                   ->line('Your request to join the collection at ' . $this->collecte->location . ' has been accepted.')
                   ->action('View Collection', route('collecte.show', $this->collecte));
        } else {
            $message->subject('Your request to join a collection has been rejected')
                   ->line('Your request to join the collection at ' . $this->collecte->location . ' has been rejected.')
                   ->line('You can still join other collections.');
        }

        return $message;
    }

    public function toArray($notifiable): array
    {
        $status = $this->status;
        $collecte = $this->collecte;
        $user = $this->user;

        $title = match($status) {
            'pending' => 'New Join Request',
            'accepted' => 'Request Accepted',
            'rejected' => 'Request Rejected',
            default => 'Join Request Update'
        };

        $message = match($status) {
            'pending' => "{$user->name} wants to join the collection at {$collecte->location}",
            'accepted' => "Your request to join the collection at {$collecte->location} has been accepted",
            'rejected' => "Your request to join the collection at {$collecte->location} has been rejected",
            default => "Your join request status has been updated"
        };

        return [
            'title' => $title,
            'message' => $message,
            'collecte_location' => $collecte->location,
            'collecte_date' => $collecte->starting_date ? $collecte->starting_date->format('Y-m-d H:i') : null,
            'status' => $status,
            'user_id' => $user->id,
            'collecte_id' => $collecte->id,
            'action_url' => route('collecte.show', $collecte),
            'action_text' => 'View Collection'
        ];
    }
} 