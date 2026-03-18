<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public int $userId,
        public int $bookingId,
        public string $status,
        public string $astrologerName,
        public ?string $joinUrl,
        public ?string $meetingStartedAt = null,
        public ?int $durationMinutes = null,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('consultation.user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'consultation.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'bookingId' => $this->bookingId,
            'status' => $this->status,
            'astrologerName' => $this->astrologerName,
            'joinUrl' => $this->joinUrl,
            'meetingStartedAt' => $this->meetingStartedAt,
            'durationMinutes' => $this->durationMinutes,
        ];
    }
}
