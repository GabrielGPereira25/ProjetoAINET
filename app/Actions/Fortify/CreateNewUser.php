<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'nif' => ['nullable', 'string', 'regex:/^[0-9]{9}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'default_payment_type' => ['nullable', 'in:Visa,PayPal,MB WAY'],
            'default_payment_ref' => ['nullable', 'string', 'max:255'],
        ], [
            'nif.regex' => 'O NIF deve conter exatamente 9 dígitos numéricos.',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'user_type' => 'C',
                'gender' => $input['gender'],
            ]);

            Customer::create([
                'id' => $user->id,
                'nif' => $input['nif'] ?? null,
                'address' => $input['address'] ?? null,
                'default_payment_type' => $input['default_payment_type'] ?? null,
                'default_payment_ref' => $input['default_payment_ref'] ?? null,
            ]);

            return $user;
        });
    }
}
