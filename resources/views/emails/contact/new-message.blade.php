@component('mail::message')
# New Contact Message

You have received a new contact message from {{ $message->name }}.

**Subject:** {{ $message->subject }}  
**Email:** {{ $message->email }}

**Message:**  
{{ $message->message }}

@component('mail::button', ['url' => route('admin.contact.show', $message)])
View Message
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 