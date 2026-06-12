<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderItemFormRequest extends FormRequest
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
            'order_id' => 'required|exists:orders,id',
            'tshirt_image_id' => 'required|exists:tshirt_images,id',
            'color_code' => 'required|exists:colors,code',
            'size' => 'required|in:XS,S,M,L,XL',
            'qty' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'sub_total' => 'nullable|numeric|min:0',
        ];
    }
}
