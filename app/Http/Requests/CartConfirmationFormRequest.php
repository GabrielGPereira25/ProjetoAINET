<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartConfirmationFormRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'nif' => 'required|digits:9',
            'payment_type' => 'required|in:PayPal,Visa,MB WAY',
            'address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
        if ($this->payment_type === 'MB WAY') {
            $rules['payment_ref'] = ['required', 'regex:/^9[0-9]{8}$/'];
        }elseif ($this->payment_type === 'Visa') {
            $rules['payment_ref'] = ['required', 'regex:/^4[0-9]{15}$/'];
        }elseif ($this->payment_type === 'PayPal') {
            $rules['payment_ref'] = 'required|email';
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->has('payment_ref') && in_array($this->payment_type, ['Visa', 'MB WAY'])) {
            $this->merge([
                'payment_ref' => str_replace(' ', '', $this->payment_ref),
            ]);
        }
    }
}
