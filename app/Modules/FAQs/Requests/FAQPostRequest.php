<?php

namespace App\Modules\FAQs\Requests;

use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;

class FAQPostRequest extends InputRequest
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
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
        ];
    }

}
