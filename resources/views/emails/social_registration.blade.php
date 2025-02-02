<x-mail::message>

<h3>Hello {{ $name }}!</h3><br/>

Welcome to {{ config('app.name') }}! We're thrilled to have you on board.<br>

By joining our community, you've unlocked access to a vast library of resources.<br>

If you have any questions or need assistance, our support team is here to help. Feel free to reach out at <a href="mailto:admin@amrita-janani.org">admin@amrita-janani.org</a> or visit our <a href="https://amrita-janani.org/contact" target="_blank">Help Desk</a>.<br>

Thank you for choosing {{ config('app.name') }}. We're excited to accompany you on this journey.<br>

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
