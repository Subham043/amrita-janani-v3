<?php

namespace App\Modules\Audios\Requests;

use App\Enums\Status;
use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AudioMultiStatusRequest extends InputRequest
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
            'audios' => 'required|array|min:1',
            'audios.*' => ['required','numeric','exists:audios,id'],
            'status' => ['required', Rule::enum(Status::class)],
        ];
    }

}
