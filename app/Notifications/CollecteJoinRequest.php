<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Collecte;
use App\Models\User;

class CollecteJoinRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $collecte;
    protected $user;

    public function __construct(Collecte $collecte, User $user)
    {
        $this->collecte = $collecte;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Join Request',
            'message' => $this->user->name . ' has requested to join your collection at ' . $this->collecte->location,
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