<?php

namespace App\Modules\Videos\Requests;

use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;

class VideoMultiDeleteRequest extends InputRequest
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
        ];
    }

}
