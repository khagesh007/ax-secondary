<?php

namespace App\Http\Requests\Api\V1\Listing;

use Illuminate\Foundation\Http\FormRequest;

class GetPropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'ref_no' => 'required|string|properties:ref_no,',
        ];
    }
}
