<?php

namespace App\Services;

use App\Events\ConsultationBookingStatusUpdated;
use App\Events\ConsultationStatusUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ConsultationBroadcastService
{
    private const PARTICIPANT_CACHE_TTL_SECONDS = 86400;

    public function broadcastReadyToStart(array $booking, ?int $durationMinutes = null): void
    {
        $this->broadcast($booking, 'ready_to_start', $durationMinutes);
    }

    public function broadcastLive(array $booking, ?int $durationMinutes = null): void
    {
        $this->broadcast($booking, 'live', $durationMinutes);
    }

    public function broadcastEnded(array $booking): void
    {
        $this->broadcast($booking, 'ended');
    }

    private function broadcast(array $booking, string $status, ?int $durationMinutes = null): void
    {
        $bookingId = $this->resolveIntegerValue($booking['id'] ?? null);
        $recipientUserIds = $this->resolveRecipientUserIds($booking);
        $resolvedDuration = $durationMinutes ?? $this->resolveIntegerValue($booking['duration'] ?? null);

        if ($recipientUserIds === [] || ! $bookingId) {
            return;
        }

        $this->cacheParticipants($bookingId, $recipientUserIds);

        $joinUrl = $status === 'ended' ? null : $this->buildJoinUrl($bookingId, $booking, $resolvedDuration);
        $astrologerName = $this->resolveAstrologerName($booking);
        $meetingStartedAt = $booking['meeting_started_at'] ?? null;

        $this->dispatchBroadcastEvent(
            new ConsultationBookingStatusUpdated(
                bookingId: $bookingId,
                status: $status,
                astrologerName: $astrologerName,
                joinUrl: $joinUrl,
                meetingStartedAt: $meetingStartedAt,
                durationMinutes: $resolvedDuration,
            ),
            $bookingId,
            $status
        );

        foreach ($recipientUserIds as $userId) {
            $this->dispatchBroadcastEvent(
                new ConsultationStatusUpdated(
                    userId: $userId,
                    bookingId: $bookingId,
                    status: $status,
                    astrologerName: $astrologerName,
                    joinUrl: $joinUrl,
                    meetingStartedAt: $meetingStartedAt,
                    durationMinutes: $resolvedDuration,
                ),
                $bookingId,
                $status,
                $userId
            );
        }
    }

    private function dispatchBroadcastEvent(object $event, int $bookingId, string $status, ?int $userId = null): void
    {
        try {
            event($event);
        } catch (Throwable $exception) {
            Log::warning('Consultation broadcast failed.', [
                'booking_id' => $bookingId,
                'status' => $status,
                'user_id' => $userId,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private function buildJoinUrl(int $bookingId, array $booking, ?int $durationMinutes = null): string
    {
        $resolvedDuration = $durationMinutes ?? $this->resolveIntegerValue($booking['duration'] ?? null);
        $routeParameters = [
            'meetingId' => (string) ($booking['meeting_id'] ?? ('astro-' . $bookingId)),
        ];

        if ($resolvedDuration && $resolvedDuration > 0) {
            $routeParameters['duration'] = $resolvedDuration;
        }

        return route('customer.consultation.video', $routeParameters);
    }

    private function resolveUserId(array $booking): ?int
    {
        return $this->resolveIntegerValue($this->firstFilledValue($booking, [
            'user_id',
            'customer_id',
            'user.id',
            'customer.id',
            'customer.user_id',
        ]));
    }

    private function resolveAstrologerUserId(array $booking): ?int
    {
        return $this->resolveIntegerValue($this->firstFilledValue($booking, [
            'astrologer_id',
            'astrologer.id',
            'assigned_astrologer_id',
        ]));
    }

    private function resolveRecipientUserIds(array $booking): array
    {
        $recipientUserIds = array_filter([
            $this->resolveUserId($booking),
            $this->resolveAstrologerUserId($booking),
        ], static fn (?int $userId): bool => $userId !== null && $userId > 0);

        $bookingId = $this->resolveIntegerValue($booking['id'] ?? null);

        if ($bookingId) {
            $recipientUserIds = array_merge($recipientUserIds, $this->getCachedParticipantUserIds($bookingId));
        }

        return array_values(array_unique($recipientUserIds));
    }

    private function resolveAstrologerName(array $booking): string
    {
        return (string) ($this->firstFilledValue($booking, [
            'astrologer.name',
            'astrologerName',
            'astrologer_name',
        ]) ?? 'Your astrologer');
    }

    private function firstFilledValue(array $payload, array $paths): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function resolveIntegerValue(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function cacheParticipants(int $bookingId, array $recipientUserIds): void
    {
        $mergedRecipientUserIds = array_values(array_unique(array_merge(
            $this->getCachedParticipantUserIds($bookingId),
            array_map('intval', $recipientUserIds)
        )));

        Cache::put(
            'consultation-participants:' . $bookingId,
            [
                'user_ids' => $mergedRecipientUserIds,
                'updated_at' => now()->toIso8601String(),
            ],
            self::PARTICIPANT_CACHE_TTL_SECONDS
        );
    }

    private function getCachedParticipantUserIds(int $bookingId): array
    {
        $participants = Cache::get('consultation-participants:' . $bookingId, []);

        return array_values(array_filter(
            array_map('intval', (array) data_get($participants, 'user_ids', [])),
            static fn (int $userId): bool => $userId > 0
        ));
    }
}
