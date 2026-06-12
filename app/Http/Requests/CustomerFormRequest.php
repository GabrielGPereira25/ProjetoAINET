<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerFormRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'gender' => 'required|in:M,F',
            'nif' => 'nullable|string|size:9',
            'address' => 'nullable|string',
            'default_payment_type' => 'nullable|in:Visa,PayPal,MB WAY',
            'default_payment_ref' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|max:4096',
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }
}
