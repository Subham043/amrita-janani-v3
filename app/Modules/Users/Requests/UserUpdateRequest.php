<?php

namespace App\Modules\Users\Requests;

use App\Enums\UserType;
use App\Requests\InputRequest;
use App\Enums\UserStatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class UserUpdateRequest extends InputRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
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
            'email' => ['required','email:rfc,dns','unique:users,email,'.$this->route('id')],
            'phone' => ['nullable','string', 'regex:/^\+?[1-9]\d{1,14}$/', 'unique:users,phone,'.$this->route('id')],
            'user_type' => ['required', Rule::enum(UserType::class)],
            'status' => ['required', Rule::enum(UserStatus::class)],
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
            'phone.regex' => 'Invalid phone number. Please enter a valid phone number.'
        ];
    }

}
