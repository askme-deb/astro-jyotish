<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationBookingStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
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
            new Channel('consultation.booking.' . $this->bookingId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'consultation.booking.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'bookingId' => $this->bookingId,
            'status' => $this->status,
            'meetingStartedAt' => $this->meetingStartedAt,
            'durationMinutes' => $this->durationMinutes,
        ];
    }
}
