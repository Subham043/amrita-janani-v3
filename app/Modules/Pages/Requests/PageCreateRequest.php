<?php

namespace App\Modules\Pages\Requests;

use App\Requests\InputRequest;
use Illuminate\Support\Facades\Auth;

class PageCreateRequest extends InputRequest
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
            'page_name' => ['nullable','string','unique:pages,page_name'],
            'url' => ['nullable','string','unique:pages,url'],
        ];
    }

}
