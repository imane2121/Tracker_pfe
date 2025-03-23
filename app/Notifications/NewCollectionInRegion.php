<?php

namespace App\Notifications;

use App\Models\Collecte;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCollectionInRegion extends Notification implements ShouldQueue
{
    use Queueable;

    protected $collecte;

    public function __construct(Collecte $collecte)
    {
        $this->collecte = $collecte;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Collection in Your Subscribed Region')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('A new collection has been created in your subscribed region.')
            ->line('Collection Details:')
            ->line('- Location: ' . $this->collecte->location)
            ->line('- Date: ' . $this->collecte->starting_date->format('M d, Y'))
            ->line('- Region: ' . $this->collecte->region)
            ->action('Join Collection', url('/collectes/' . $this->collecte->id))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Collection in ' . $this->collecte->region,
            'message' => 'A new collection has been created in your subscribed region. Join now to help clean up the environment!',
            'details' => [
                'location' => $this->collecte->location,
                'date' => $this->collecte->starting_date->format('M d, Y'),
                'region' => $this->collecte->region,
                'description' => $this->collecte->description,
                'contributors_needed' => $this->collecte->nbrContributors,
                'current_contributors' => $this->collecte->current_contributors
            ],
            'action_url' => url('/collectes/' . $this->collecte->id),
            'action_text' => 'Join Collection'
        ];
    }
} 