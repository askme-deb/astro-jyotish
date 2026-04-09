<?php

namespace App\Http\Controllers;

use App\Services\AstrologerApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AstrologerProfileController extends Controller
{
    public function __construct(private AstrologerApiService $astrologerApiService)
    {
    }

    public function show(Request $request)
    {
        if (!session('api_user_id')) {
            return redirect()->route('home');
        }

        $token = $this->resolveToken($request);
        if ($token === null) {
            return redirect()->route('home');
        }

        $profileResponse = $this->astrologerApiService->getProfile($token);
        $profile = $this->extractProfileRecord($profileResponse['data'] ?? null, (array) session('auth.user', []));

        if (($profileResponse['success'] ?? false) && $profile !== []) {
            $this->syncSessionProfile($profile);
        }

        return view('astrologer.profile', [
            'profile' => $this->normalizeProfileForView($profile),
            'languageOptions' => $this->astrologerApiService->getLanguages(),
            'skillOptions' => $this->astrologerApiService->getSkills(),
            'loadError' => ($profileResponse['success'] ?? false) ? null : ($profileResponse['message'] ?? null),
        ]);
    }

    public function update(Request $request): JsonResponse
    {

        if (!session('api_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $token = $this->resolveToken($request);
        if ($token === null) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing.',
            ], 401);
        }

        $input = array_replace_recursive($request->all(), $request->allFiles());

        $validator = Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'mobile_no' => ['required', 'string', 'max:20'],
            'display_name' => ['required', 'string', 'max:255'],
            'short_intro' => ['nullable', 'string', 'max:255'],
            'details_bio' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'state_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'pin_code' => ['nullable', 'string', 'max:10'],
            'consultation_mode' => ['nullable', 'string', 'max:50'],
            'ac_holder_name' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'ac_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:50'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'upi_id' => ['nullable', 'string', 'max:255'],
            'applicant_name' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'experience' => ['nullable', 'numeric', 'min:0'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'aadhar_number' => ['nullable', 'string', 'max:20'],
            'pan_number' => ['nullable', 'string', 'max:20'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['nullable'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['nullable'],
            'education' => ['nullable', 'array'],
            'education.*.degree' => ['required_with:education', 'nullable', 'string', 'max:255'],
            'education.*.institution' => ['required_with:education', 'nullable', 'string', 'max:255'],
            'education.*.year' => ['required_with:education', 'nullable', 'integer', 'digits:4'],
            'education.*.document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:1024'],
            'availabilities' => ['nullable', 'array'],
            'availabilities.*.day' => ['required_with:availabilities', 'nullable', 'string', 'max:20'],
            'availabilities.*.slots' => ['nullable', 'array'],
            'availabilities.*.slots.*.from' => ['required_with:availabilities.*.slots', 'nullable', 'date_format:H:i'],
            'availabilities.*.slots.*.to' => ['required_with:availabilities.*.slots', 'nullable', 'date_format:H:i'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:1024'],
            'aadhar_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:1024'],
            'pan_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:1024'],
            'signature' => ['nullable', 'string'],
            'astrologer_signature_image' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Please fix the highlighted fields.',
                'errors' => $errors,
            ], 422);
        }

        $payload = $this->sanitizeProfilePayload($validator->validated());
        $method = strtoupper($request->getMethod()) === 'PUT' ? 'PUT' : 'PATCH';
        $result = $this->astrologerApiService->updateProfile($payload, $token, $method);
        //dd($result);
        if (!($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to update profile.',
                'errors' => $result['errors'] ?? [],
            ], (int) ($result['status_code'] ?? 422));
        }

        $profile = $this->extractProfileRecord($result['data'] ?? null, $this->stripUploadedFiles($payload));
        $this->syncSessionProfile($profile);

        return response()->json([
            'success' => true,
            'message' => $result['message'] ?? 'Profile updated successfully.',
            'data' => $this->normalizeProfileForView($profile),
        ]);
    }

    private function resolveToken(Request $request): ?string
    {
        return $request->cookie('auth_api_token')
            ?? session('auth.api_token')
            ?? $request->bearerToken();
    }

    private function syncSessionProfile(array $profile): void
    {
        $user = session('auth.user', []);
        if (!is_array($user)) {
            $user = [];
        }

        $profile = $this->stripUploadedFiles($profile);
        $user = $this->stripUploadedFiles($user);

        session(['auth.user' => array_merge($user, $this->extractProfileRecord($profile, $user))]);
    }

    private function extractProfileRecord(mixed $profile, array $fallback = []): array
    {
        $profile = is_array($profile) ? $profile : [];

        $candidates = array_values(array_filter([
            $profile,
            is_array($profile['profile'] ?? null) ? $profile['profile'] : null,
            is_array($profile['astrologer'] ?? null) ? $profile['astrologer'] : null,
            is_array($profile['user'] ?? null) ? $profile['user'] : null,
            is_array(data_get($profile, 'data')) ? data_get($profile, 'data') : null,
            is_array(data_get($profile, 'data.profile')) ? data_get($profile, 'data.profile') : null,
            is_array(data_get($profile, 'data.astrologer')) ? data_get($profile, 'data.astrologer') : null,
            is_array(data_get($profile, 'data.user')) ? data_get($profile, 'data.user') : null,
            is_array($fallback) ? $fallback : null,
        ], function ($item) {
            return is_array($item) && $item !== [];
        }));

        $record = [];
        foreach (array_reverse($candidates) as $candidate) {
            $record = array_merge($record, $candidate);
        }

        $nestedUser = is_array($record['user'] ?? null) ? $record['user'] : [];
        $nestedAstrologer = is_array($record['astrologer'] ?? null) ? $record['astrologer'] : [];

        return array_merge($nestedUser, $nestedAstrologer, $record);
    }

    private function sanitizeProfilePayload(array $payload): array
    {
        foreach ([
            'first_name',
            'last_name',
            'email',
            'mobile_no',
            'display_name',
            'short_intro',
            'details_bio',
            'address',
            'pin_code',
            'consultation_mode',
            'ac_holder_name',
            'bank_name',
            'ac_number',
            'ifsc_code',
            'branch_name',
            'upi_id',
            'applicant_name',
            'password',
            'aadhar_number',
            'pan_number',
        ] as $field) {
            if (array_key_exists($field, $payload)) {
                $payload[$field] = trim((string) $payload[$field]);
            }
        }

        foreach (['state_id', 'city_id', 'duration'] as $field) {
            if (array_key_exists($field, $payload) && $payload[$field] !== '' && $payload[$field] !== null) {
                $payload[$field] = (int) $payload[$field];
            }
        }

        foreach (['experience', 'rate'] as $field) {
            if (array_key_exists($field, $payload) && $payload[$field] !== '' && $payload[$field] !== null) {
                $payload[$field] = $payload[$field] + 0;
            }
        }

        $payload['languages'] = array_values(array_filter(array_map(function ($value) {
            if (is_numeric($value)) {
                return (int) $value;
            }

            return is_string($value) ? trim($value) : $value;
        }, $payload['languages'] ?? []), function ($value) {
            return $value !== null && $value !== '';
        }));

        $payload['skills'] = array_values(array_filter(array_map(function ($value) {
            if (is_numeric($value)) {
                return (int) $value;
            }

            return is_string($value) ? trim($value) : $value;
        }, $payload['skills'] ?? []), function ($value) {
            return $value !== null && $value !== '';
        }));

        $payload['education'] = array_values(array_filter(array_map(function ($item) {
            if (!is_array($item)) {
                return null;
            }

            $education = [
                'degree' => trim((string) ($item['degree'] ?? '')),
                'institution' => trim((string) ($item['institution'] ?? '')),
                'year' => isset($item['year']) && $item['year'] !== '' ? (int) $item['year'] : null,
            ];

            if (($item['document'] ?? null) instanceof \Illuminate\Http\UploadedFile) {
                $education['document'] = $item['document'];
            }

            return $education;
        }, $payload['education'] ?? []), function ($item) {
            return is_array($item)
                && ($item['degree'] !== ''
                    || $item['institution'] !== ''
                    || $item['year'] !== null
                    || isset($item['document']));
        }));

        $payload['availabilities'] = array_values(array_filter(array_map(function ($item) {
            if (!is_array($item)) {
                return null;
            }

            $slots = array_values(array_filter(array_map(function ($slot) {
                if (!is_array($slot)) {
                    return null;
                }

                $from = $this->normalizeTimeValue($slot['from'] ?? '');
                $to = $this->normalizeTimeValue($slot['to'] ?? '');

                if ($from === '' || $to === '') {
                    return null;
                }

                return ['from' => $from, 'to' => $to];
            }, $item['slots'] ?? [])));

            $day = trim((string) ($item['day'] ?? ''));
            if ($day === '' && $slots === []) {
                return null;
            }

            return [
                'day' => $day,
                'slots' => $slots,
            ];
        }, $payload['availabilities'] ?? []), function ($item) {
            return is_array($item) && (($item['day'] ?? '') !== '' || ($item['slots'] ?? []) !== []);
        }));

        foreach (['photo', 'aadhar_document', 'pan_document'] as $fileField) {
            if (($payload[$fileField] ?? null) instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }

            unset($payload[$fileField]);
        }

        if (!isset($payload['signature']) && isset($payload['astrologer_signature_image'])) {
            $payload['signature'] = $payload['astrologer_signature_image'];
        }

        unset($payload['astrologer_signature_image']);

        if (isset($payload['signature'])) {
            $payload['signature'] = trim((string) $payload['signature']);

            if ($payload['signature'] === '') {
                unset($payload['signature']);
            }
        }

        if (($payload['password'] ?? '') === '') {
            unset($payload['password']);
        }

        return $payload;
    }

    private function normalizeProfileForView(array $profile): array
    {
        $profile = $this->extractProfileRecord($profile);

        $rawEducation = is_array($profile['education'] ?? null)
            ? $profile['education']
            : (is_array(data_get($profile, 'astrologer.education')) ? data_get($profile, 'astrologer.education') : []);
        $educationDocuments = is_array($profile['education_documents'] ?? null)
            ? $profile['education_documents']
            : (is_array(data_get($profile, 'astrologer.education_documents')) ? data_get($profile, 'astrologer.education_documents') : []);
        $rawAvailabilities = is_array($profile['availabilities'] ?? null)
            ? $profile['availabilities']
            : (is_array(data_get($profile, 'astrologer.availabilities')) ? data_get($profile, 'astrologer.availabilities') : []);

        return [
            'first_name' => (string) ($profile['first_name'] ?? data_get($profile, 'user.first_name') ?? ''),
            'last_name' => (string) ($profile['last_name'] ?? data_get($profile, 'user.last_name') ?? ''),
            'email' => (string) ($profile['email'] ?? data_get($profile, 'user.email') ?? ''),
            'mobile_no' => (string) ($profile['mobile_no'] ?? data_get($profile, 'user.mobile_no') ?? ''),
            'display_name' => (string) ($profile['display_name'] ?? data_get($profile, 'astrologer.display_name') ?? ''),
            'short_intro' => (string) ($profile['short_intro'] ?? data_get($profile, 'astrologer.short_intro') ?? ''),
            'details_bio' => (string) ($profile['details_bio'] ?? data_get($profile, 'astrologer.details_bio') ?? ''),
            'address' => (string) ($profile['address'] ?? data_get($profile, 'astrologer.address') ?? ''),
            'state_id' => $profile['state_id'] ?? data_get($profile, 'astrologer.state_id') ?? '',
            'city_id' => $profile['city_id'] ?? data_get($profile, 'astrologer.city_id') ?? '',
            'pin_code' => (string) ($profile['pin_code'] ?? data_get($profile, 'astrologer.pin_code') ?? ''),
            'consultation_mode' => (string) ($profile['consultation_mode'] ?? data_get($profile, 'astrologer.consultation_mode') ?? ''),
            'ac_holder_name' => (string) ($profile['ac_holder_name'] ?? data_get($profile, 'astrologer.ac_holder_name') ?? ''),
            'bank_name' => (string) ($profile['bank_name'] ?? data_get($profile, 'astrologer.bank_name') ?? ''),
            'ac_number' => (string) ($profile['ac_number'] ?? data_get($profile, 'astrologer.ac_number') ?? ''),
            'ifsc_code' => (string) ($profile['ifsc_code'] ?? data_get($profile, 'astrologer.ifsc_code') ?? ''),
            'branch_name' => (string) ($profile['branch_name'] ?? data_get($profile, 'astrologer.branch_name') ?? ''),
            'upi_id' => (string) ($profile['upi_id'] ?? data_get($profile, 'astrologer.upi_id') ?? ''),
            'applicant_name' => (string) ($profile['applicant_name'] ?? data_get($profile, 'astrologer.applicant_name') ?? ''),
            'experience' => $profile['experience'] ?? data_get($profile, 'astrologer.experience') ?? '',
            'rate' => $profile['rate'] ?? data_get($profile, 'astrologer.rate') ?? '',
            'duration' => $profile['duration'] ?? data_get($profile, 'astrologer.duration') ?? 30,
            'password' => '',
            'aadhar_number' => (string) ($profile['aadhar_number'] ?? data_get($profile, 'astrologer.aadhar_number') ?? ''),
            'pan_number' => (string) ($profile['pan_number'] ?? data_get($profile, 'astrologer.pan_number') ?? ''),
            'photo_url' => $this->resolveAssetValue($profile, ['photo_url', 'photo', 'profile_photo', 'image', 'user.photo', 'astrologer.photo']),
            'aadhar_document_url' => $this->resolveAssetValue($profile, ['aadhar_document_url', 'aadhar_document', 'aadhaar_document', 'user.aadhar_document', 'astrologer.aadhar_document']),
            'pan_document_url' => $this->resolveAssetValue($profile, ['pan_document_url', 'pan_document', 'user.pan_document', 'astrologer.pan_document']),
            'signature' => $this->resolveAssetValue($profile, ['signature_url', 'astrologer_signature_image', 'signature_image', 'signature', 'user.signature', 'astrologer.signature', 'astrologer.signature_url']),
            'languages' => $this->normalizeSelectionValues($profile['languages'] ?? data_get($profile, 'astrologer.languages') ?? []),
            'skills' => $this->normalizeSelectionValues($profile['skills'] ?? data_get($profile, 'astrologer.skills') ?? []),
            'education' => array_values(array_map(function ($item, $index) use ($educationDocuments) {
                return [
                    'degree' => (string) ($item['degree'] ?? ''),
                    'institution' => (string) ($item['institution'] ?? ''),
                    'year' => $item['year'] ?? '',
                    'document_url' => $this->resolveEducationDocumentValue($item) ?: $this->resolveEducationDocumentListValue($educationDocuments, $index),
                ];
            }, $rawEducation, array_keys($rawEducation))),
            'availabilities' => $this->normalizeAvailabilityValues($rawAvailabilities),
        ];
    }

    private function normalizeSelectionValues(mixed $value): array
    {
        if (is_string($value)) {
            $value = array_filter(array_map('trim', explode(',', $value)));
        }

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($item) {
            if (is_array($item)) {
                $candidate = $item['id'] ?? $item['name'] ?? $item['label'] ?? $item['value'] ?? null;

                return $candidate !== null ? (string) $candidate : null;
            }

            if (is_scalar($item)) {
                return trim((string) $item);
            }

            return null;
        }, $value), function ($item) {
            return $item !== null && $item !== '';
        }));
    }

    private function resolveAssetValue(array $profile, array $paths): string
    {
        foreach ($paths as $path) {
            $value = str_contains($path, '.') ? data_get($profile, $path) : ($profile[$path] ?? null);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }

    private function resolveEducationDocumentValue(array $item): string
    {
        foreach (['document_url', 'document', 'file', 'certificate', 'education_document'] as $key) {
            $value = $item[$key] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }

    private function resolveEducationDocumentListValue(array $documents, int $index): string
    {
        $document = $documents[$index] ?? null;

        if (!is_array($document)) {
            return '';
        }

        foreach (['url', 'document_url', 'file', 'path'] as $key) {
            $value = $document[$key] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }

    private function normalizeAvailabilityValues(array $items): array
    {
        $grouped = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $day = trim((string) ($item['day'] ?? ''));

            if (is_array($item['slots'] ?? null)) {
                $key = $day !== '' ? strtolower($day) : 'group_' . count($grouped);

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'day' => $day,
                        'slots' => [],
                    ];
                }

                foreach ($item['slots'] as $slot) {
                    if (!is_array($slot)) {
                        continue;
                    }

                    $from = $this->normalizeTimeValue($slot['from'] ?? $slot['from_time'] ?? '');
                    $to = $this->normalizeTimeValue($slot['to'] ?? $slot['to_time'] ?? '');

                    if ($from === '' && $to === '') {
                        continue;
                    }

                    $grouped[$key]['slots'][] = [
                        'from' => $from,
                        'to' => $to,
                    ];
                }

                continue;
            }

            $from = $this->normalizeTimeValue($item['from'] ?? $item['from_time'] ?? '');
            $to = $this->normalizeTimeValue($item['to'] ?? $item['to_time'] ?? '');

            if ($day === '' && $from === '' && $to === '') {
                continue;
            }

            $key = $day !== '' ? strtolower($day) : 'group_' . count($grouped);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'day' => $day,
                    'slots' => [],
                ];
            }

            if ($from !== '' || $to !== '') {
                $grouped[$key]['slots'][] = [
                    'from' => $from,
                    'to' => $to,
                ];
            }
        }

        return array_values($grouped);
    }

    private function normalizeTimeValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $time = trim((string) $value);

        if ($time === '') {
            return '';
        }

        if (preg_match('/^(\d{2}):(\d{2})$/', $time, $matches) === 1) {
            return sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
        }

        if (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $time, $matches) === 1) {
            return sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
        }

        try {
            return \Illuminate\Support\Carbon::parse($time)->format('H:i');
        } catch (\Throwable) {
            return '';
        }
    }

    private function stripUploadedFiles(mixed $value): mixed
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            return null;
        }

        if (!is_array($value)) {
            return $value;
        }

        $sanitized = [];

        foreach ($value as $key => $item) {
            $cleanItem = $this->stripUploadedFiles($item);

            if ($cleanItem === null) {
                continue;
            }

            $sanitized[$key] = $cleanItem;
        }

        return $sanitized;
    }
}
