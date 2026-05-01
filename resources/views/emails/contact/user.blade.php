<x-mail::message>
# Hello {{ $formData['first_name'] }},

Thank you for reaching out to **Sirateq Ghana Group Ltd**. We’ve successfully received your message regarding **{{ $formData['service'] }}**.

A member of our team is currently reviewing your inquiry and will contact you shortly to discuss how we can help propel your project forward.

For your reference, here is a copy of what you submitted:

<x-mail::panel>
**Company:** {{ $formData['company'] ?? 'N/A' }}<br>
**Service:** {{ $formData['service'] }}<br>
**Message:**<br>
{{ $formData['message'] }}
</x-mail::panel>

If you have any immediate questions, you can simply reply to this email to reach us.

Best Regards,<br>
**The Sirateq Team**
</x-mail::message>
