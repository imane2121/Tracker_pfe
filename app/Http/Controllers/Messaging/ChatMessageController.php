<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    public function store(Request $request, ChatRoom $chatRoom)
    {
        $user = Auth::user();
        
        // Verify user is a participant
        if (!$chatRoom->isParticipant($user)) {
            abort(403, 'You are not a participant in this chat room.');
        }

        $validated = $request->validate([
            'message_content' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240|mimes:jpeg,png,pdf,doc,docx',
        ]);

        // Ensure at least one of message_content or files is present
        if (empty($request->message_content) && !$request->hasFile('files')) {
            return back()->with('error', 'Please provide either a message or files.');
        }

        $messages = [];

        // Handle file uploads first
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $mimeType = $file->getMimeType();
                $isImage = str_starts_with($mimeType, 'image/');
                
                if ($isImage) {
                    $path = $file->store('chat_attachments/images/' . date('Y-m-d'), 'public');
                    
                    $messages[] = $chatRoom->messages()->create([
                        'user_id' => $user->id,
                        'message_content' => '', // Empty content for images
                        'message_type' => ChatMessage::TYPE_IMAGE,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $mimeType
                    ]);
                } else {
                    // Handle non-image files separately
                    $path = $file->store('chat_attachments/files/' . date('Y-m-d'), 'public');
                    
                    $messages[] = $chatRoom->messages()->create([
                        'user_id' => $user->id,
                        'message_content' => '',
                        'message_type' => ChatMessage::TYPE_FILE,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $mimeType
                    ]);
                }
            }
        }

        // Create text message only if there's content
        if (!empty($request->message_content)) {
            $messages[] = $chatRoom->messages()->create([
                'user_id' => $user->id,
                'message_content' => $request->message_content,
                'message_type' => ChatMessage::TYPE_TEXT,
            ]);
        }

        return back()->with('success', 'Message sent successfully.');
    }

    public function destroy(ChatMessage $message)
    {
        $user = Auth::user();
        
        // Only message owner or admin can delete
        if ($user->id !== $message->user_id && $user->role !== 'admin') {
            abort(403, 'You cannot delete this message.');
        }

        $message->delete();

        return back()->with('success', 'Message deleted successfully.');
    }
}
