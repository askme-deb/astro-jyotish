<?php

namespace App\Http\Controllers;

use App\Services\Api\Clients\AstrologerApiService;
use App\Services\Api\Clients\UserApiService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    private const CATEGORY_OPTIONS = [
        'consultation' => 'Consultation',
        'account' => 'Account',
        'payments' => 'Payments',
        'technical' => 'Technical',
        'other' => 'Other',
    ];

    private const STATUS_OPTIONS = [
        'all' => 'All',
        'open' => 'Open',
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public function index(Request $request)
    {
        if (!session('api_user_id')) {
            return redirect()->route('home');
        }

        $panel = $this->resolvePanel($request);
        $filters = [
            'context' => $panel['context'],
            'status' => $request->query('status', 'open'),
            'per_page' => (int) $request->query('per_page', 15),
        ];

        if ($filters['status'] === 'all') {
            unset($filters['status']);
        }

        $result = $this->getApiService($panel)->listSupportTickets($filters, $this->getApiToken());
        $ticketsPayload = $this->extractTicketCollectionPayload($result);
        $tickets = array_map(function ($ticket) use ($panel) {
            return $this->normalizeTicket($ticket, $panel['context']);
        }, $ticketsPayload['items']);

        $viewData = [
            'tickets' => $tickets,
            'meta' => $ticketsPayload['meta'],
            'filters' => [
                'status' => $request->query('status', 'open'),
                'per_page' => (int) $request->query('per_page', 15),
            ],
            'categoryOptions' => self::CATEGORY_OPTIONS,
            'statusOptions' => self::STATUS_OPTIONS,
            'pageError' => isset($result['error']) && $result['error'] ? ($result['message'] ?? 'Unable to load support tickets right now.') : null,
            'supportTicketPanel' => $panel,
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => !($viewData['pageError']),
                'message' => $viewData['pageError'],
                'tickets' => $viewData['tickets'],
                'meta' => $viewData['meta'],
                'filters' => $viewData['filters'],
            ]);
        }

        return view('support-tickets.index', $viewData);
    }

    public function store(Request $request)
    {
        if (!session('api_user_id')) {
            return $this->jsonOrRedirectError($request, 'Unauthenticated.', 401);
        }

        $panel = $this->resolvePanel($request);
        $validator = Validator::make($request->all(), [
            'category' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'reason' => ['required', 'string', 'max:255'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx,txt,webp'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first() ?: 'Please correct the highlighted fields.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return Redirect::back()->withErrors($validator, 'supportTicket')->withInput();
        }

        $validated = $validator->validated();
        $attachments = array_values(array_filter($request->file('attachments', []), function ($file) {
            return $file instanceof UploadedFile;
        }));

        $result = $this->getApiService($panel)->createSupportTicket([
            'context' => $panel['context'],
            'category' => $validated['category'],
            'subject' => trim($validated['subject']),
            'description' => trim($validated['description']),
            'reason' => trim($validated['reason']),
        ], $attachments, $this->getApiToken());

        if (isset($result['error']) && $result['error']) {
            $errors = $this->normalizeExternalValidationErrors($result['errors'] ?? null);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $this->firstExternalValidationMessage($errors, $result['message'] ?? 'Failed to create support ticket.'),
                    'errors' => $errors,
                ], (int) ($result['status_code'] ?? 422));
            }

            return Redirect::back()
                ->withErrors($errors, 'supportTicket')
                ->withInput()
                ->with('error', $this->firstExternalValidationMessage($errors, $result['message'] ?? 'Failed to create support ticket.'));
        }

        $ticket = $this->normalizeTicket($this->extractTicketItem($result), $panel['context']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Support ticket created successfully.',
                'ticket' => $ticket,
            ]);
        }

        return Redirect::route($panel['routeIndex'])->with('success', $result['message'] ?? 'Support ticket created successfully.');
    }

    public function show($ticketId, Request $request)
    {
        if (!session('api_user_id')) {
            return $this->jsonOrRedirectError($request, 'Unauthenticated.', 401);
        }

        $panel = $this->resolvePanel($request);
        $result = $this->getApiService($panel)->getSupportTicketDetails($ticketId, $this->getApiToken());

        if (isset($result['error']) && $result['error']) {
            return $this->jsonOrRedirectError($request, $result['message'] ?? 'Unable to load support ticket details.', (int) ($result['status_code'] ?? 404));
        }

        $ticket = $this->normalizeTicket($this->extractTicketItem($result), $panel['context']);

        return response()->json([
            'success' => true,
            'ticket' => $ticket,
        ]);
    }

    private function resolvePanel(Request $request): array
    {
        if ($request->routeIs('astrologer.supportTickets.*')) {
            return [
                'panel' => 'astrologer',
                'context' => 'astrologer',
                'routeIndex' => 'astrologer.supportTickets.index',
                'routeStore' => 'astrologer.supportTickets.store',
                'routeShow' => 'astrologer.supportTickets.show',
                'backRoute' => 'astrologer.dashboard',
                'backLabel' => 'Back to Dashboard',
                'headerSubtitle' => 'Create and track help requests for consultation, payout, and technical issues.',
                'createSubtitle' => 'Support will receive this request under the astrologer context automatically.',
                'emptySubtitle' => 'Create a ticket to contact support about consultation workflow, payouts, or technical issues.',
                'descriptionPlaceholder' => 'Describe the issue in detail, including booking IDs or steps to reproduce when relevant.',
            ];
        }

        return [
            'panel' => 'customer',
            'context' => 'user',
            'routeIndex' => 'customer.supportTickets.index',
            'routeStore' => 'customer.supportTickets.store',
            'routeShow' => 'customer.supportTickets.show',
            'backRoute' => 'dashboard',
            'backLabel' => 'Back to Dashboard',
            'headerSubtitle' => 'Create and track help requests for booking, payment, and technical issues.',
            'createSubtitle' => 'Support will receive this request under the customer context automatically.',
            'emptySubtitle' => 'Create a ticket to contact support about consultation workflow, wallet issues, or technical problems.',
            'descriptionPlaceholder' => 'Describe the issue in detail, including booking IDs, payment IDs, or exact steps to reproduce.',
        ];
    }

    private function getApiService(array $panel): AstrologerApiService|UserApiService
    {
        if ($panel['panel'] === 'astrologer') {
            return app(AstrologerApiService::class);
        }

        return new UserApiService(config('auth_api'));
    }

    private function getApiToken(): ?string
    {
        return request()->cookie('auth_api_token') ?? session('auth.api_token') ?? session('auth_api_token');
    }

    private function extractTicketCollectionPayload(array $result): array
    {
        $payload = $result['data'] ?? $result;
        $items = [];

        if (isset($payload['data']) && is_array($payload['data'])) {
            $items = $payload['data'];
        } elseif (isset($payload['items']) && is_array($payload['items'])) {
            $items = $payload['items'];
        } elseif (isset($payload['support_tickets']) && is_array($payload['support_tickets'])) {
            $items = $payload['support_tickets'];
        } elseif (array_is_list($payload)) {
            $items = $payload;
        }

        return [
            'items' => array_values(array_filter($items, 'is_array')),
            'meta' => [
                'total' => (int) ($payload['total'] ?? count($items)),
                'current_page' => (int) ($payload['current_page'] ?? 1),
                'last_page' => (int) ($payload['last_page'] ?? 1),
                'per_page' => (int) ($payload['per_page'] ?? count($items)),
            ],
        ];
    }

    private function extractTicketItem(array $result): array
    {
        $payload = $result['data'] ?? $result;

        if (isset($payload['ticket']) && is_array($payload['ticket'])) {
            return $payload['ticket'];
        }

        if (isset($payload['support_ticket']) && is_array($payload['support_ticket'])) {
            return $payload['support_ticket'];
        }

        if (isset($payload['data']) && is_array($payload['data']) && !array_is_list($payload['data'])) {
            return $payload['data'];
        }

        return is_array($payload) ? $payload : [];
    }

    private function normalizeTicket(array $ticket, string $defaultContext): array
    {
        $attachments = $ticket['attachments'] ?? data_get($ticket, 'media') ?? [];

        return [
            'id' => $ticket['id'] ?? null,
            'reference' => $ticket['ticket_number'] ?? $ticket['reference'] ?? $ticket['ticket_id'] ?? ('TKT-' . ($ticket['id'] ?? '')),
            'subject' => (string) ($ticket['subject'] ?? 'Untitled support ticket'),
            'category' => (string) ($ticket['category'] ?? 'other'),
            'reason' => (string) ($ticket['reason'] ?? ''),
            'description' => (string) ($ticket['description'] ?? ''),
            'status' => strtolower((string) ($ticket['status'] ?? 'open')),
            'context' => (string) ($ticket['context'] ?? $defaultContext),
            'created_at' => $ticket['created_at'] ?? null,
            'updated_at' => $ticket['updated_at'] ?? null,
            'attachments' => $this->normalizeAttachments(is_array($attachments) ? $attachments : []),
        ];
    }

    private function normalizeAttachments(array $attachments): array
    {
        return array_values(array_filter(array_map(function ($attachment) {
            if (!is_array($attachment)) {
                return null;
            }

            $url = $attachment['url'] ?? $attachment['file_url'] ?? $attachment['path'] ?? null;
            $name = $attachment['name'] ?? $attachment['file_name'] ?? $attachment['original_name'] ?? basename((string) $url);

            return [
                'name' => (string) $name,
                'url' => $url ? (string) $url : null,
            ];
        }, $attachments)));
    }

    private function normalizeExternalValidationErrors(mixed $errors): array
    {
        if (!is_array($errors)) {
            return [];
        }

        $normalized = [];

        foreach ($errors as $field => $messages) {
            $key = is_string($field) && $field !== '' ? $field : 'general';

            if (is_array($messages)) {
                $normalized[$key] = array_values(array_filter(array_map(function ($message) {
                    return is_scalar($message) ? trim((string) $message) : null;
                }, $messages)));
            } elseif (is_scalar($messages)) {
                $normalized[$key] = [trim((string) $messages)];
            }
        }

        return array_filter($normalized, function ($messages) {
            return is_array($messages) && $messages !== [];
        });
    }

    private function firstExternalValidationMessage(array $errors, string $default): string
    {
        foreach ($errors as $messages) {
            if (is_array($messages) && isset($messages[0]) && is_string($messages[0]) && trim($messages[0]) !== '') {
                return trim($messages[0]);
            }
        }

        return $default;
    }

    private function jsonOrRedirectError(Request $request, string $message, int $statusCode)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $statusCode);
        }

        $panel = $this->resolvePanel($request);

        return Redirect::route($panel['routeIndex'])->with('error', $message);
    }
}
