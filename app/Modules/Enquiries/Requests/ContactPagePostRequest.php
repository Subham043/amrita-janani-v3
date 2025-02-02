<?php

namespace App\Modules\Enquiries\Requests;

use App\Requests\InputRequest;
use App\Services\RateLimitService;


class ContactPagePostRequest extends InputRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        (new RateLimitService($this))->ensureIsNotRateLimited(3);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required','email:rfc,dns'],
            'phone' => ['nullable','string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:500'],
            'g-recaptcha-response' => 'required|captcha',
            'system_info' => 'nullable|json'
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'g-recaptcha-response.captcha' => 'Invalid Captcha. Please try again.',
            'phone.regex' => 'Invalid phone number. Please enter a valid phone number.',
            'system_info.json' => 'Invalid system info. Please try again.',
        ];
    }

}
