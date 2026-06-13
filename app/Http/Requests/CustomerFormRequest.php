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
            'default_payment_ref' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $type = $this->default_payment_type;
                    if ($type === 'MB WAY' && !preg_match('/^9[0-9]{8}$/', str_replace(' ', '', $value))) {
                        $fail('O número MB WAY deve começar por 9 e ter 9 dígitos.');
                    } elseif ($type === 'Visa' && !preg_match('/^4[0-9]{15}$/', str_replace(' ', '', $value))) {
                        $fail('O cartão Visa deve começar por 4 e ter 16 dígitos.');
                    } elseif ($type === 'PayPal' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('O e-mail PayPal não é válido.');
                    }
                },
            ],
            'image_file' => 'nullable|image|max:4096',
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->has('default_payment_ref') && in_array($this->default_payment_type, ['Visa', 'MB WAY'])) {
            $this->merge([
                'default_payment_ref' => str_replace(' ', '', $this->default_payment_ref),
            ]);
        }
    }
}
