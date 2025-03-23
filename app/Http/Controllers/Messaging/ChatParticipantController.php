<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatParticipantController extends Controller
{
    public function store(ChatRoom $chatRoom, User $user)
    {
        // Only admin or supervisor can add participants
        if (Auth::user()->role !== 'admin' && !$chatRoom->isParticipant(Auth::user(), 'admin')) {
            abort(403, 'You cannot add participants to this chat room.');
        }

        $chatRoom->addParticipant($user);

        return back()->with('success', 'Participant added successfully.');
    }

    public function destroy(ChatRoom $chatRoom, User $user)
    {
        // Only admin or supervisor can remove participants
        if (Auth::user()->role !== 'admin' && !$chatRoom->isParticipant(Auth::user(), 'admin')) {
            abort(403, 'You cannot remove participants from this chat room.');
        }

        $chatRoom->removeParticipant($user);

        return back()->with('success', 'Participant removed successfully.');
    }

    public function leave(ChatRoom $chatRoom)
    {
        $user = Auth::user();
        
        if (!$chatRoom->isParticipant($user)) {
            abort(403, 'You are not a participant in this chat room.');
        }

        $chatRoom->removeParticipant($user);

        return redirect()->route('messaging.index')
                        ->with('success', 'You have left the chat room.');
    }
}
