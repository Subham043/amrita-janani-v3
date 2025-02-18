<?php

namespace App\Modules\Documents\Requests;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DocumentMultiStatusRequest extends InputRequest
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
            'documents' => 'required|array|min:1',
            'documents.*' => ['required','numeric','exists:documents,id'],
            'status' => ['required', Rule::enum(Status::class)],
        ];
    }

}
