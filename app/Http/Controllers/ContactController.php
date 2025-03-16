<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $message = ContactMessage::create($validated);

        try {
            // Send notification email to admin
            Mail::to(config('mail.admin.address'))
                ->send(new \App\Mail\NewContactMessage($message));
        } catch (\Exception $e) {
            // Log the error but don't stop the process
            \Log::error('Failed to send contact notification email: ' . $e->getMessage());
        }

        return redirect()->route('contact.index')
            ->with('success', 'Your message has been sent successfully. We will get back to you soon.');
    }

    // Admin methods
    public function adminIndex()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.contact.index', compact('messages'));
    }

    public function adminShow(ContactMessage $message)
    {
        return view('admin.contact.show', compact('message'));
    }

    public function adminReply(Request $request, ContactMessage $message)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string'
        ]);

        $message->update([
            'admin_reply' => $validated['admin_reply'],
            'status' => 'replied',
            'replied_at' => now()
        ]);

        // Send reply email to user
        Mail::to($message->email)->send(new \App\Mail\ContactMessageReply($message));

        return redirect()->route('admin.contact.index')
            ->with('success', 'Reply sent successfully.');
    }

    public function adminMarkAsRead(ContactMessage $message)
    {
        $message->update(['status' => 'read']);
        return redirect()->back()->with('success', 'Message marked as read.');
    }

    public function adminDelete(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.contact.index')
            ->with('success', 'Message deleted successfully.');
    }
} 