<p>Dear {{ $appointment['customer_name'] ?? 'Customer' }},</p>
<p>Your video consultation is scheduled. Please join at your appointment time using the link below:</p>
<p>
    <a href="{{ $link }}" style="background:#f98700;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;font-weight:bold;">Join Video Consultation</a>
</p>
<p>If the button above does not work, copy and paste this link into your browser:</p>
<p style="word-break:break-all;">{{ $link }}</p>
<p>Thank you,<br>The Astro Consultant Team</p>