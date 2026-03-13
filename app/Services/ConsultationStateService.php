<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ConsultationStateService
{
    private const CACHE_TTL_SECONDS = 86400;

    public function get(int $bookingId): ?array
    {
        $state = Cache::get($this->cacheKey($bookingId));

        return is_array($state) ? $state : null;
    }

    public function markReadyToStart(int $bookingId, ?string $meetingId = null, ?int $durationMinutes = null): array
    {
        $current = $this->get($bookingId) ?? [];

        return $this->put($bookingId, array_merge($current, [
            'status' => 'ready_to_start',
            'meeting_id' => $meetingId ?? ($current['meeting_id'] ?? ('astro-' . $bookingId)),
            'meeting_started_at' => null,
            'meeting_ended_at' => null,
            'duration' => $durationMinutes ?? ($current['duration'] ?? null),
        ]));
    }

    public function markInProgress(int $bookingId, ?string $meetingStartedAt = null, ?string $meetingId = null, ?int $durationMinutes = null): array
    {
        $current = $this->get($bookingId) ?? [];

        return $this->put($bookingId, array_merge($current, [
            'status' => 'in_progress',
            'meeting_id' => $meetingId ?? ($current['meeting_id'] ?? ('astro-' . $bookingId)),
            'meeting_started_at' => $meetingStartedAt ?? ($current['meeting_started_at'] ?? now()->utc()->toIso8601String()),
            'meeting_ended_at' => null,
            'duration' => $durationMinutes ?? ($current['duration'] ?? null),
        ]));
    }

    public function markCompleted(int $bookingId, ?string $meetingEndedAt = null): array
    {
        $current = $this->get($bookingId) ?? [];

        return $this->put($bookingId, array_merge($current, [
            'status' => 'completed',
            'meeting_ended_at' => $meetingEndedAt ?? now()->toDateTimeString(),
        ]));
    }

    public function mergeIntoBooking(?array $booking, int $bookingId): ?array
    {
        if (!is_array($booking)) {
            return $booking;
        }

        $state = $this->get($bookingId);
        if (!$state) {
            return $booking;
        }

        $bookingStatus = (string) ($booking['status'] ?? '');
        $localStatus = (string) ($state['status'] ?? '');

        // If the upstream booking is not completed, a cached completed state is stale.
        if ($localStatus === 'completed' && $bookingStatus !== '' && $bookingStatus !== 'completed') {
            $this->forget($bookingId);
            return $booking;
        }

        foreach (['status', 'meeting_id', 'meeting_started_at', 'meeting_ended_at', 'duration'] as $field) {
            if (array_key_exists($field, $state) && $state[$field] !== null) {
                $booking[$field] = $state[$field];
            }
        }

        return $booking;
    }

    private function put(int $bookingId, array $state): array
    {
        $state['updated_at'] = now()->toDateTimeString();
        Cache::put($this->cacheKey($bookingId), $state, self::CACHE_TTL_SECONDS);

        return $state;
    }

    public function forget(int $bookingId): void
    {
        Cache::forget($this->cacheKey($bookingId));
    }

    private function cacheKey(int $bookingId): string
    {
        return 'consultation-state:' . $bookingId;
    }
}
