<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PriceFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'unit_price_catalog' => ['required', 'numeric', 'min:1'],
            'unit_price_own' => ['required', 'numeric', 'min:1'],
            'unit_price_catalog_discount' => ['required', 'numeric', 'min:0'],
            'unit_price_own_discount' => ['required', 'numeric', 'min:0'],
            'qty_discount' => ['required', 'integer', 'min:1'],
        ];
    }
}
