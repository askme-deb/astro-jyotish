<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $mobile = preg_replace('/[^0-9]/', '', (string) $this->input('mobile_no'));
        $countryCode = (string) $this->input('country_code', '+91');

        $countryCode = preg_replace('/[^0-9+]/', '', $countryCode ?? '');

        $otp = preg_replace('/[^0-9]/', '', (string) $this->input('otp'));

        $this->merge([
            'mobile_no' => $mobile,
            'country_code' => $countryCode ?: '+91',
            'otp' => $otp,
        ]);
    }

    public function rules(): array
    {
        return [
            'mobile_no' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'country_code' => ['required', 'string', 'max:5', 'regex:/^\+?[0-9]{1,4}$/'],
            'otp' => ['required', 'string', 'digits_between:4,8'],
            'context' => ['nullable', 'string', 'in:checkout,header'],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'Please enter the OTP sent to your phone.',
            'otp.digits_between' => 'The OTP code format is invalid.',
        ];
    }
}
