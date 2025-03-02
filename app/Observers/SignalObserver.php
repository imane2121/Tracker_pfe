<?php

namespace App\Observers;

use App\Models\Signal;
use Illuminate\Support\Facades\Auth;

class SignalObserver
{
    /**
     * Handle the Signal "created" event.
     */
    public function creating(Signal $signal)
    {
        $signal->created_by = Auth::id(); // Automatically set created_by
    }

    /**
     * Handle the Signal "updated" event.
     */
    public function updated(Signal $signal): void
    {
        //
    }

    /**
     * Handle the Signal "deleted" event.
     */
    public function deleted(Signal $signal): void
    {
        //
    }

    /**
     * Handle the Signal "restored" event.
     */
    public function restored(Signal $signal): void
    {
        //
    }

    /**
     * Handle the Signal "force deleted" event.
     */
    public function forceDeleted(Signal $signal): void
    {
        //
    }
}
