<?php

namespace App\Modules\Web\Requests;

use App\Requests\InputRequest;


class SearchPostRequest extends InputRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phrase' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }

}
