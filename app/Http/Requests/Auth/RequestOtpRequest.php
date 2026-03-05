<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RequestOtpRequest extends FormRequest
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

        $this->merge([
            'mobile_no' => $mobile,
            'country_code' => $countryCode ?: '+91',
        ]);
    }

    public function rules(): array
    {
        return [
            'mobile_no' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'country_code' => ['required', 'string', 'max:5', 'regex:/^\+?[0-9]{1,4}$/'],
            'context' => ['nullable', 'string', 'in:checkout,header'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile_no.required' => 'Please enter your mobile number.',
            'mobile_no.regex' => 'Please enter a valid mobile number.',
            'country_code.required' => 'Country code is required.',
            'country_code.regex' => 'Invalid country code format.',
        ];
    }
}
