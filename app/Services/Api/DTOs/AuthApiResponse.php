<?php

namespace App\Services\Api\DTOs;

class AuthApiResponse
{
    public function __construct(
        public bool $success,
        public ?string $message,
        public mixed $data,
        public ?string $errorCode,
        public string $correlationId,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload, string $correlationId): self
    {
        $success = (bool) ($payload['status'] ?? ($payload['success'] ?? false));
        $message = $payload['message'] ?? null;
        $data = $payload['data'] ?? null;
        $errorCode = $payload['error'] ?? null;

        return new self($success, $message, $data, $errorCode, $correlationId);
    }
}
