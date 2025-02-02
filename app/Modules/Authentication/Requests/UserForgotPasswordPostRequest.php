<?php

namespace App\Modules\Authentication\Requests;

use App\Requests\InputRequest;
use App\Services\RateLimitService;


class UserForgotPasswordPostRequest extends InputRequest
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
            'email' => ['required','email','exists:users'],
            'g-recaptcha-response' => 'required|captcha'
        ];
    }

}
