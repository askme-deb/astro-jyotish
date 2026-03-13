<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consultation Note</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.55;
            margin: 0;
        }

        .document {
            position: relative;
            border: 1px solid #d9d9d9;
            padding: 22px 24px 26px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 170px;
            height: 170px;
            margin-top: -85px;
            margin-left: -85px;
            opacity: 0.08;
            z-index: 0;
        }

        .watermark-text {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 280px;
            margin-top: -18px;
            margin-left: -140px;
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #9ca3af;
            opacity: 0.08;
            z-index: 0;
        }

        .header,
        .fields,
        .body,
        .footer {
            position: relative;
            z-index: 1;
        }

        .header {
            border-bottom: 2px solid #d8d8d8;
            padding-bottom: 12px;
        }

        .header-table,
        .fields-table,
        .specialties-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 56px;
            vertical-align: top;
        }

        .logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .logo-fallback {
            width: 40px;
            height: 40px;
            border: 1px solid #d1d5db;
            border-radius: 999px;
            text-align: center;
            line-height: 40px;
            font-size: 12px;
            font-weight: 700;
            color: #111827;
        }

        .brand-cell {
            vertical-align: top;
            padding-right: 14px;
        }

        .meta-cell {
            width: 210px;
            vertical-align: top;
            border-left: 1px solid #e5e7eb;
            padding-left: 14px;
        }

        .title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #111827;
        }

        .doctor {
            margin-top: 4px;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .subtitle {
            font-size: 11px;
            color: #6b7280;
        }

        .summary {
            margin-top: 4px;
        }

        .contact {
            margin-top: 7px;
            color: #374151;
        }

        .contact span {
            margin-right: 14px;
        }

        .meta-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #111827;
            margin-bottom: 6px;
        }

        .meta-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
        }

        .meta-value {
            font-size: 11px;
            font-weight: 700;
            color: #111827;
            padding-bottom: 6px;
        }

        .fields {
            margin-top: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #c7cdd6;
        }

        .field {
            vertical-align: bottom;
            padding-right: 12px;
        }

        .field:last-child {
            padding-right: 0;
        }

        .field-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .field-value {
            display: block;
            min-height: 18px;
            border-bottom: 1px dotted #9ca3af;
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        .specialties {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .specialty-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .specialty-value {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: #111827;
        }

        .specialty-spacer {
            height: 10px;
        }

        .body {
            margin-top: 12px;
            min-height: 480px;
        }

        .body-title {
            font-family: DejaVu Serif, serif;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #111827;
        }

        .note-box {
            border-top: 1px solid #eef2f7;
            background-image: linear-gradient(to bottom, transparent 31px, #eef2f7 31px, #eef2f7 32px);
            background-size: 100% 32px;
            padding-top: 4px;
            min-height: 460px;
        }

        .note-box p {
            margin: 0 0 14px;
        }

        .empty {
            color: #6b7280;
            font-style: italic;
        }

        .footer {
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .footer-card {
            text-align: right;
            color: #4b5563;
            font-size: 10px;
            line-height: 1.6;
        }

        .footer-name {
            font-size: 11px;
            font-weight: 700;
            color: #111827;
        }
    </style>
</head>
<body>
@php
    $appointmentRoot = is_array($appointment ?? null) ? $appointment : [];
    $astrologerSources = array_values(array_filter([
        is_array(data_get($appointmentRoot, 'astrologer')) ? data_get($appointmentRoot, 'astrologer') : null,
        is_array(data_get($appointmentRoot, 'assigned_astrologer')) ? data_get($appointmentRoot, 'assigned_astrologer') : null,
        is_array(data_get($appointmentRoot, 'consultant')) ? data_get($appointmentRoot, 'consultant') : null,
        is_array(data_get($appointmentRoot, 'astrologer.user')) ? data_get($appointmentRoot, 'astrologer.user') : null,
        is_array(data_get($appointmentRoot, 'assigned_astrologer.user')) ? data_get($appointmentRoot, 'assigned_astrologer.user') : null,
    ], function ($source) {
        return is_array($source) && $source !== [];
    }));

    $resolveRootValue = function (array $paths, $default = null) use ($appointmentRoot) {
        foreach ($paths as $path) {
            $value = data_get($appointmentRoot, $path);

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    };

    $resolveAstrologerValue = function (array $paths, $default = null) use ($astrologerSources) {
        foreach ($astrologerSources as $source) {
            foreach ($paths as $path) {
                $value = data_get($source, $path);

                if (is_string($value)) {
                    $value = trim($value);
                }

                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        }

        return $default;
    };

    $formatAppointmentList = function ($value) {
        if (!is_array($value)) {
            return trim((string) $value);
        }

        return collect($value)->map(function ($item) {
            if (is_array($item)) {
                return $item['name'] ?? $item['title'] ?? $item['label'] ?? $item['value'] ?? null;
            }

            return $item;
        })->filter(function ($item) {
            return $item !== null && trim((string) $item) !== '';
        })->join(', ');
    };

    $astrologerName = trim((string) ($resolveRootValue([
        'astrologer_name',
        'astrologer_full_name',
    ]) ?? $resolveAstrologerValue([
        'name',
        'full_name',
        'display_name',
    ], '')));

    if ($astrologerName === '') {
        $astrologerName = trim(
            ((string) ($resolveRootValue(['astrologer_first_name']) ?? $resolveAstrologerValue(['first_name'], '')))
            . ' '
            . ((string) ($resolveRootValue(['astrologer_last_name']) ?? $resolveAstrologerValue(['last_name'], '')))
        );
    }

    $astrologerEmail = trim((string) ($resolveRootValue(['astrologer_email']) ?? $resolveAstrologerValue(['email', 'user.email'], '')));
    $astrologerPhone = trim((string) ($resolveRootValue([
        'astrologer_mobile_no',
        'astrologer_phone',
        'astrologer_contact_no',
    ]) ?? $resolveAstrologerValue([
        'mobile_no',
        'phone',
        'contact_no',
        'user.mobile_no',
        'user.phone',
        'user.contact_no',
    ], '')));
    $astrologerExperience = $resolveRootValue(['astrologer_experience']) ?? $resolveAstrologerValue(['experience', 'exp_in_years']);
    $astrologerLanguages = $formatAppointmentList($resolveRootValue(['astrologer_languages']) ?? $resolveAstrologerValue(['languages', 'language'], []));
    $astrologerSkills = $formatAppointmentList($resolveRootValue(['astrologer_skills', 'astrologer_specializations']) ?? $resolveAstrologerValue(['skills', 'specializations', 'specialisations'], []));
    $astrologerDesignation = trim((string) ($resolveRootValue(['astrologer_designation', 'astrologer_qualification']) ?? $resolveAstrologerValue(['designation', 'qualification', 'title'], '')));
    $astrologerDisplayName = $astrologerName !== '' ? $astrologerName : 'Astrologer Consultant';
    $customerDisplayName = trim((string) data_get($appointment, 'user.first_name') . ' ' . (string) data_get($appointment, 'user.last_name'));
    $supportsPdfImages = extension_loaded('gd') && !empty($logoPath) && file_exists($logoPath);
    $logoFallbackText = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $appName), 0, 2) ?: 'AC');

    if ($customerDisplayName === '') {
        $customerDisplayName = trim((string) ($appointment['name'] ?? ''));
    }

    $finalizedNote = trim((string) ($appointment['astrologer_note'] ?? ''));
@endphp
    <div class="document">
        @if($supportsPdfImages)
            <img class="watermark" src="{{ $logoPath }}" alt="">
        @else
            <div class="watermark-text">{{ $appName }}</div>
        @endif

        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        @if($supportsPdfImages)
                            <img class="logo" src="{{ $logoPath }}" alt="{{ $appName }} logo">
                        @else
                            <div class="logo-fallback">{{ $logoFallbackText }}</div>
                        @endif
                    </td>
                    <td class="brand-cell">
                        <div class="title">Astrologer Consultation Notes</div>
                        <div class="doctor">{{ $astrologerDisplayName }}</div>
                        @if($astrologerDesignation !== '')
                            <div class="subtitle">{{ $astrologerDesignation }}</div>
                        @endif
                        <div class="subtitle summary">Consultation summary, remedies, and post-session guidance</div>
                        <div class="contact">
                            @if($astrologerEmail !== '')
                                <span><strong>Email:</strong> {{ $astrologerEmail }}</span>
                            @endif
                            @if($astrologerPhone !== '')
                                <span><strong>Phone:</strong> {{ $astrologerPhone }}</span>
                            @endif
                            @if($astrologerEmail === '' && $astrologerPhone === '')
                                <span class="subtitle">Professional consultation summary</span>
                            @endif
                        </div>
                    </td>
                    <td class="meta-cell">
                        <div class="meta-title">Consultation Record</div>
                        <div class="meta-label">Booking ID</div>
                        <div class="meta-value">BKNG{{ $appointment['id'] ?? '-' }}</div>
                        <div class="meta-label">Date</div>
                        <div class="meta-value">{{ !empty($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('d M Y') : ($generatedAt?->format('d M Y') ?? '-') }}</div>
                        <div class="meta-label">Time</div>
                        <div class="meta-value">{{ !empty($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('h:i A') : '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="fields">
            <table class="fields-table">
                <tr>
                    <td class="field" style="width:40%;">
                        <span class="field-label">Name</span>
                        <span class="field-value">{{ $customerDisplayName !== '' ? $customerDisplayName : 'Not provided' }}</span>
                    </td>
                    <td class="field" style="width:20%;">
                        <span class="field-label">Consultation</span>
                        <span class="field-value">{{ ucfirst($appointment['consultation_type'] ?? 'Consultation') }}</span>
                    </td>
                    <td class="field" style="width:20%;">
                        <span class="field-label">Date</span>
                        <span class="field-value">{{ !empty($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('d M Y') : '-' }}</span>
                    </td>
                    <td class="field" style="width:20%;">
                        <span class="field-label">Time</span>
                        <span class="field-value">{{ !empty($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('h:i A') : '-' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="specialties">
            <table class="specialties-table">
                <tr>
                    <td style="width:33.33%; padding-right: 12px; vertical-align: top;">
                        <span class="specialty-label">Experience</span>
                        <span class="specialty-value">{{ ($astrologerExperience !== null && $astrologerExperience !== '') ? $astrologerExperience . ' years' : 'Not specified' }}</span>
                    </td>
                    <td style="width:33.33%; padding-right: 12px; vertical-align: top;">
                        <span class="specialty-label">Languages</span>
                        <span class="specialty-value">{{ $astrologerLanguages !== '' ? $astrologerLanguages : 'Not specified' }}</span>
                    </td>
                    <td style="width:33.33%; vertical-align: top;">
                        <span class="specialty-label">Generated</span>
                        <span class="specialty-value">{{ $generatedAt?->format('d M Y, h:i A') }}</span>
                    </td>
                </tr>
                <tr><td colspan="3" class="specialty-spacer"></td></tr>
                <tr>
                    <td colspan="3" style="vertical-align: top;">
                        <span class="specialty-label">Specializations</span>
                        <span class="specialty-value">{{ $astrologerSkills !== '' ? $astrologerSkills : 'Not specified' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="body">
            <div class="body-title">Astrological Advice</div>
            <div class="note-box">
                @if($finalizedNote !== '')
                    {!! nl2br(e($finalizedNote)) !!}
                @else
                    <p class="empty">No astrologer note was provided for this appointment.</p>
                @endif
            </div>
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-card">
                        <div class="footer-name">{{ $astrologerDisplayName }}</div>
                        @if($astrologerPhone !== '')
                            <div>Phone: {{ $astrologerPhone }}</div>
                        @endif
                        @if($astrologerEmail !== '')
                            <div>Email: {{ $astrologerEmail }}</div>
                        @endif
                        <div>Reference: BKNG{{ $appointment['id'] ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>