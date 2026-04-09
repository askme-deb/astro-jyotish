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
            'experience' => ['nullable', 'numeric', 'min:0'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['nullable', 'integer', 'min:1'],
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

        if (!($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to update profile.',
                'errors' => $result['errors'] ?? [],
            ], (int) ($result['status_code'] ?? 422));
        }

        $profile = $this->extractProfileRecord($result['data'] ?? null, $payload);
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

                $from = trim((string) ($slot['from'] ?? ''));
                $to = trim((string) ($slot['to'] ?? ''));

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

        if (isset($payload['astrologer_signature_image'])) {
            $payload['astrologer_signature_image'] = trim((string) $payload['astrologer_signature_image']);

            if ($payload['astrologer_signature_image'] === '') {
                unset($payload['astrologer_signature_image']);
            }
        }

        return $payload;
    }

    private function normalizeProfileForView(array $profile): array
    {
        $profile = $this->extractProfileRecord($profile);

        return [
            'first_name' => (string) ($profile['first_name'] ?? data_get($profile, 'user.first_name') ?? ''),
            'last_name' => (string) ($profile['last_name'] ?? data_get($profile, 'user.last_name') ?? ''),
            'email' => (string) ($profile['email'] ?? data_get($profile, 'user.email') ?? ''),
            'mobile_no' => (string) ($profile['mobile_no'] ?? data_get($profile, 'user.mobile_no') ?? ''),
            'display_name' => (string) ($profile['display_name'] ?? data_get($profile, 'astrologer.display_name') ?? ''),
            'short_intro' => (string) ($profile['short_intro'] ?? data_get($profile, 'astrologer.short_intro') ?? ''),
            'details_bio' => (string) ($profile['details_bio'] ?? data_get($profile, 'astrologer.details_bio') ?? ''),
            'experience' => $profile['experience'] ?? data_get($profile, 'astrologer.experience') ?? '',
            'rate' => $profile['rate'] ?? data_get($profile, 'astrologer.rate') ?? '',
            'duration' => $profile['duration'] ?? data_get($profile, 'astrologer.duration') ?? 30,
            'photo_url' => $this->resolveAssetValue($profile, ['photo_url', 'photo', 'profile_photo', 'image', 'user.photo', 'astrologer.photo']),
            'aadhar_document_url' => $this->resolveAssetValue($profile, ['aadhar_document_url', 'aadhar_document', 'aadhaar_document', 'user.aadhar_document', 'astrologer.aadhar_document']),
            'pan_document_url' => $this->resolveAssetValue($profile, ['pan_document_url', 'pan_document', 'user.pan_document', 'astrologer.pan_document']),
            'astrologer_signature_image' => $this->resolveAssetValue($profile, ['astrologer_signature_image', 'signature_image', 'signature', 'user.signature', 'astrologer.signature']),
            'languages' => $this->normalizeSelectionValues($profile['languages'] ?? data_get($profile, 'astrologer.languages') ?? []),
            'skills' => $this->normalizeSelectionValues($profile['skills'] ?? data_get($profile, 'astrologer.skills') ?? []),
            'education' => array_values(array_map(function ($item) {
                return [
                    'degree' => (string) ($item['degree'] ?? ''),
                    'institution' => (string) ($item['institution'] ?? ''),
                    'year' => $item['year'] ?? '',
                    'document_url' => $this->resolveEducationDocumentValue($item),
                ];
            }, is_array($profile['education'] ?? null) ? $profile['education'] : (is_array(data_get($profile, 'astrologer.education')) ? data_get($profile, 'astrologer.education') : []))),
            'availabilities' => array_values(array_map(function ($item) {
                $slots = [];
                foreach ((array) ($item['slots'] ?? []) as $slot) {
                    $slots[] = [
                        'from' => (string) ($slot['from'] ?? ''),
                        'to' => (string) ($slot['to'] ?? ''),
                    ];
                }

                return [
                    'day' => (string) ($item['day'] ?? ''),
                    'slots' => $slots,
                ];
            }, is_array($profile['availabilities'] ?? null) ? $profile['availabilities'] : (is_array(data_get($profile, 'astrologer.availabilities')) ? data_get($profile, 'astrologer.availabilities') : []))),
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
}
