<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Stevebauman\Purify\Facades\Purify;

class InputRequest extends FormRequest
{

	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Handle a passed validation attempt.
	 *
	 * @return void
	 */
	protected function prepareForValidation(): void
	{
		$request = Purify::clean($this->all());
		$this->replace([...$request]);
	}
}
