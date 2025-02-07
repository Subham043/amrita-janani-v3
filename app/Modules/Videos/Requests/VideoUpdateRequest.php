<?php

namespace App\Modules\Videos\Requests;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VideoUpdateRequest extends InputRequest
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
            'language' => ['required','array','min:1'],
            'language.*' => ['required','numeric', 'exists:languages,id'],
            'video' => ['required', 'active_url'],
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
        ];
    }

}
