<?php

namespace App\Modules\Images\Requests;

use App\Enums\Restricted;
use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ImageMultiRestrictionRequest extends InputRequest
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
            'images' => 'required|array|min:1',
            'images.*' => ['required','numeric','exists:images,id'],
            'restricted' => ['required', Rule::enum(Restricted::class)],
        ];
    }

}
