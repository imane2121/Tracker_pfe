@component('mail::message')
# Reply to Your Message

Hello {{ $message->name }},

We have received your message regarding "{{ $message->subject }}" and would like to respond:

{{ $message->admin_reply }}

If you have any further questions, please don't hesitate to contact us again.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 