<?php

namespace App\Modules\Account\Requests;

use App\Requests\InputRequest;
use App\Services\RateLimitService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordValidation;


class SetupSocialAccountPostRequest extends InputRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        (new RateLimitService($this))->ensureIsNotRateLimited(3);
        return Auth::check() && !$this->user()->hasCompletedAccountSetup();
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
            'phone' => ['nullable','string', 'regex:/^\+?[1-9]\d{1,14}$/', 'unique:users'],
            'password' => ['required',
                'string',
                PasswordValidation::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
            ],
            'cpassword' => ['required_with:password','same:password'],
            'g-recaptcha-response' => 'required|captcha'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'cpassword' => 'Confirm Password',
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
            'captcha.captcha' => 'Invalid Captcha. Please try again.',
            'phone.regex' => 'Invalid phone number. Please enter a valid phone number.'
        ];
    }

}
