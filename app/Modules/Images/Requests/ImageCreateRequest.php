<?php

namespace App\Modules\Images\Requests;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ImageCreateRequest extends InputRequest
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
            'title' => ['required','string'],
            'deity' => ['nullable','string'],
            'version' => ['nullable','string'],
            'year' => ['nullable','integer'],
            'image' => ['required','image','mimes:jpeg,png,jpg,webp'],
            'status' => ['required', Rule::enum(Status::class)],
            'restricted' => ['required', Rule::enum(Restricted::class)],
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
            'title.required' => 'Please enter the title !',
            'title.regex' => 'Please enter the valid title !',
            'deity.regex' => 'Please enter the valid deity !',
            'version.regex' => 'Please enter the valid version !',
            'year.regex' => 'Please enter the valid year !',
            'image.image' => 'Please enter a valid image !',
            'image.mimes' => 'Please enter a valid image !',
        ];
    }

}
