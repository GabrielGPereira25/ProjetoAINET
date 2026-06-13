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
            'default_payment_ref' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($input) {
                    $type = $input['default_payment_type'] ?? null;
                    if ($type === 'MB WAY' && !preg_match('/^9[0-9]{8}$/', str_replace(' ', '', $value))) {
                        $fail('O número MB WAY deve começar por 9 e ter 9 dígitos.');
                    } elseif ($type === 'Visa' && !preg_match('/^4[0-9]{15}$/', str_replace(' ', '', $value))) {
                        $fail('O cartão Visa deve começar por 4 e ter 16 dígitos.');
                    } elseif ($type === 'PayPal' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('O e-mail PayPal não é válido.');
                    }
                },
            ],
        ], [
            'nif.regex' => 'O NIF deve conter exatamente 9 dígitos numéricos.',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'user_type' => 'C',
                'gender' => empty($input['gender']) ? 'M' : $input['gender'],
            ]);

            $paymentRef = $input['default_payment_ref'] ?? null;
            $custom = [];
            if ($paymentRef) {
                if (in_array($input['default_payment_type'] ?? '', ['Visa', 'MB WAY'])) {
                    $paymentRef = str_replace(' ', '', $paymentRef);
                }
                
                if (($input['default_payment_type'] ?? '') === 'Visa') $custom['visa_ref'] = $paymentRef;
                elseif (($input['default_payment_type'] ?? '') === 'PayPal') $custom['paypal_ref'] = $paymentRef;
                elseif (($input['default_payment_type'] ?? '') === 'MB WAY') $custom['mbway_ref'] = $paymentRef;
            }

            Customer::create([
                'id' => $user->id,
                'nif' => empty($input['nif']) ? null : $input['nif'],
                'address' => empty($input['address']) ? null : $input['address'],
                'default_payment_type' => empty($input['default_payment_type']) ? null : $input['default_payment_type'],
                'custom' => empty($custom) ? null : $custom,
            ]);

            return $user;
        });
    }
}
