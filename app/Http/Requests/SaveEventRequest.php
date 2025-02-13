<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class SaveEventRequest extends FormRequest
{
    /**
     *
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape([])] public function rules(): array
    {
        return [
            'day' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
            'time_slot' => 'string|max:255',
            'detail' => 'string|max:255',
        ];
    }

    /**
     * Customize messages
     *
     * @return array
     */
    #[ArrayShape([])] public function messages(): array
    {
        return [
            'required' => __("validation.required"),
        ];
    }
}
