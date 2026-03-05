<?php

namespace App\Services\Api\Contracts;

use App\Services\Api\DTOs\AuthApiResponse;

interface AuthApiServiceInterface
{
    public function requestOtp(string $mobile, string $countryCode): AuthApiResponse;
    public function resendOtp(string $mobile, string $countryCode): AuthApiResponse;
    public function verifyOtp(string $mobile, string $countryCode, string $otp): AuthApiResponse;
}