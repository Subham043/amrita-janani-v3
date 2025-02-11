<?php

namespace App\Modules\Web\Requests;

use App\Requests\InputRequest;
use App\Services\RateLimitService;


class ContentPostRequest extends InputRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        (new RateLimitService($this))->ensureIsNotRateLimited(5);
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
            'message' => ['required', 'string', 'max:500'],
            'g-recaptcha-response' => 'required|captcha',
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
        ];
    }

}
