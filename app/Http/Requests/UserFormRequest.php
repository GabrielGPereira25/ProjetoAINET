<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserFormRequest extends FormRequest
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
        // Vai buscar o ID do utilizador se estivermos numa rota de edição (update).
        // Se for criação (store), fica a null.
        $userId = $this->route('user') ? $this->route('user')->id : null;

        $rules = [
            'name' => 'required|string|max:255',
            // A regra unique ignora o ID do próprio utilizador durante a edição
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'user_type' => 'required|in:A,F',
            'gender' => 'required|in:M,F',
            // É sempre boa prática validar o ficheiro de imagem se o recebermos
            'image_file' => 'nullable|image|max:4096',
        ];

        // Regras condicionais para a password dependendo do método HTTP
        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        } else {
            $rules['password'] = ['nullable', 'confirmed', Password::defaults()];
        }

        return $rules;
    }
}
