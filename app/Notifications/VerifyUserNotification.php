<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Auth\Notifications\VerifyEmail;

class VerifyUserNotification extends VerifyEmail
{
    // No need to override anything unless you have custom logic
}