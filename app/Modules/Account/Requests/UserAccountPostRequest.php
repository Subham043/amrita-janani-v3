<?php

namespace App\Modules\Account\Requests;

use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;


class UserAccountPostRequest extends InputRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
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
            'phone' => ['nullable','string', 'regex:/^\+?[1-9]\d{1,14}$/', 'unique:users,phone,'.Auth::user()->id],
            'email' => ['required','email:rfc,dns','unique:users,email,'.Auth::user()->id],
        ];
    }

}
