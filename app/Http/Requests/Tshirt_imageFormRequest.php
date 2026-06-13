<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class Tshirt_imageFormRequest extends FormRequest
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
            'description' => 'nullable|string',
        ];

        // Se for criação (POST), a imagem é obrigatória. Se for edição (PUT/PATCH), é opcional.
        if ($this->isMethod('post')) {
            $rules['image_file'] = 'required|image|mimes:jpeg,png,jpg|max:4096';
        } else {
            $rules['image_file'] = 'nullable|image|mimes:jpeg,png,jpg|max:4096';
        }

        return $rules;
    }
}
