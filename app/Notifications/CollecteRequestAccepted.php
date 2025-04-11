<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Collecte;

class CollecteRequestAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $collecte;

    public function __construct(Collecte $collecte)
    {
        $this->collecte = $collecte;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Join Request Accepted',
            'message' => 'Your request to join the collection at ' . $this->collecte->location . ' has been accepted.',
            'details' => [
                'location' => $this->collecte->location,
                'date' => $this->collecte->starting_date->format('M d, Y'),
                'current_contributors' => $this->collecte->current_contributors,
                'contributors_needed' => $this->collecte->nbrContributors
            ],
            'action_url' => route('collecte.show', $this->collecte),
            'action_text' => 'View Collection'
        ];
    }
} 