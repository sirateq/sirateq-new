<x-mail::message>
# New Project Inquiry

You have received a new project inquiry from your website contact form.

Here are the details submitted by the client:

**Name:** {{ $formData['first_name'] }} {{ $formData['last_name'] }}<br>
**Company:** {{ $formData['company'] ?? 'N/A' }}<br>
**Phone:** {{ $formData['phone'] }}<br>
**Email:** [{{ $formData['email'] }}](mailto:{{ $formData['email'] }})<br>
**Service Required:** {{ $formData['service'] }}<br>

**Message:**
<x-mail::panel>
{{ $formData['message'] }}
</x-mail::panel>

You can reply directly to this email to get in touch with the client.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
