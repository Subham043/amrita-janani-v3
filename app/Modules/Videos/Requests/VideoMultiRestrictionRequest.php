<?php

namespace App\Modules\Videos\Requests;

use App\Enums\Restricted;
use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VideoMultiRestrictionRequest extends InputRequest
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
            'videos' => 'required|array|min:1',
            'videos.*' => ['required','numeric','exists:videos,id'],
            'restricted' => ['required', Rule::enum(Restricted::class)],
        ];
    }

}
