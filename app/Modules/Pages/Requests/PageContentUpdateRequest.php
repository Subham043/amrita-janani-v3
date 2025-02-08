<?php

namespace App\Modules\Pages\Requests;

use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;

class PageContentUpdateRequest extends InputRequest
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
            'heading' => ['required','string'],
            'description_unformatted' => ['required', 'string'],
            'page_id' => ['required', 'exists:pages,id'],
            'id' => ['required', 'exists:page_contents,id'],
            'image_position' => ['required', 'string'],
            'image' => ['nullable','mimes:jpg,jpeg,png,webp'],
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
            'heading.required' => 'Please enter the heading !',
            'heading.regex' => 'Please enter the valid heading !',
            'description_unformatted.required' => 'Please enter the description !',
        ];
    }

}
