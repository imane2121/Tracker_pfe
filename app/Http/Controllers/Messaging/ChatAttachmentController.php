<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatAttachmentController extends Controller
{
    public function download(ChatMessage $message)
    {
        if (!$message->isFile()) {
            abort(404, 'No file attached to this message.');
        }

        // Verify user has access to this chat room
        if (!$message->chatRoom->isParticipant(auth()->user()) && auth()->user()->role !== 'admin') {
            abort(403, 'You do not have access to this file.');
        }

        if (!Storage::disk('public')->exists($message->file_path)) {
            abort(404, 'File not found.');
        }

        // Use response()->download() instead of Storage::download()
        return response()->download(
            storage_path('app/public/' . $message->file_path),
            $message->file_name
        );
    }
}
