<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\Collecte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // For admin users - show all chat rooms
        if ($user->role === 'admin') {
            $chatRooms = ChatRoom::with(['creator', 'collecte', 'participants', 'messages'])
                               ->latest()
                               ->paginate(10);
        } else {
            // For other users - show only their chat rooms
            $chatRooms = ChatRoom::with(['creator', 'collecte', 'participants', 'messages'])
                               ->whereHas('participants', function($query) use ($user) {
                                   $query->where('user_id', $user->id);
                               })
                               ->latest()
                               ->paginate(10);
        }

        return view('messaging.index', compact('chatRooms'));
    }

    public function show(ChatRoom $chatRoom)
    {
        $user = Auth::user();
        
        // Check if user has access to this chat
        if (!$user->role === 'admin' && !$chatRoom->isParticipant($user)) {
            abort(403, 'You do not have access to this chat room.');
        }

        $messages = $chatRoom->messages()
                           ->with('user')
                           ->latest()
                           ->paginate(50);

        $participants = $chatRoom->participants()->get();

        return view('messaging.show', compact('chatRoom', 'messages', 'participants'));
    }

    // This method will be called automatically when a collecte is created
    public function create(Collecte $collecte)
    {
        $chatRoom = ChatRoom::create([
            'collecte_id' => $collecte->id,
            'created_by' => $collecte->user_id  // use collecte's creator ID
        ]);

        // Add the supervisor (creator) as admin
        $chatRoom->addParticipant($collecte->creator, 'admin');

        // Add any existing contributors as participants
        foreach ($collecte->contributors as $contributor) {
            $chatRoom->addParticipant($contributor, 'participant');
        }

        return $chatRoom;
    }
}
