<?php

namespace App\Http\Requests\Api\V1\Lead;

use Illuminate\Foundation\Http\FormRequest;

class GetLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [];
        return [
            'id' => 'required|integer|exists:leads,id',
        ];
    }
}
