<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public string $gender = '';
    public string $nif = '';
    public string $address = '';
    public string $default_payment_type = '';
    public string $default_payment_ref = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->gender = Auth::user()->gender ?? '';
        if (Auth::user()->user_type === 'C' && Auth::user()->customer) {
            $this->nif = Auth::user()->customer->nif ?? '';
            $this->address = Auth::user()->customer->address ?? '';
            $this->default_payment_type = Auth::user()->customer->default_payment_type ?? '';
            $this->default_payment_ref = Auth::user()->customer->default_payment_ref ?? '';
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            ...$this->profileRules($user->id),
            'nif' => ['nullable', 'string', 'regex:/^[0-9]{9}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'default_payment_type' => ['nullable', 'in:Visa,PayPal,MB WAY'],
            'default_payment_ref' => ['nullable', 'string', 'max:255'],
        ], [
            'nif.regex' => 'O NIF deve conter exatamente 9 dígitos numéricos.',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->user_type === 'C' && $user->customer) {
            $user->customer->update([
                'nif' => $validated['nif'] ?? null,
                'address' => $validated['address'] ?? null,
                'default_payment_type' => $validated['default_payment_type'] ?? null,
                'default_payment_ref' => $validated['default_payment_ref'] ?? null,
            ]);
        }

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast(text: __('A new verification link has been sent to your email address.'));
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                    </div>
                @endif
            </div>

            <flux:radio.group wire:model="gender" :label="__('Gender')" required>
                <flux:radio value="M" :label="__('Male')" />
                <flux:radio value="F" :label="__('Female')" />
            </flux:radio.group>

            @if (Auth::user()->user_type === 'C')
                <hr class="my-6 border-zinc-200 dark:border-zinc-700" />

                <flux:input wire:model="nif" :label="__('NIF (Optional)')" type="text" :placeholder="__('9-digit NIF')" />
                <flux:input wire:model="address" :label="__('Default Address (Optional)')" type="text" :placeholder="__('Your shipping address')" />

                <div x-data="{ 
                        paymentType: $wire.entangle('default_payment_type', true),
                        useAccountEmail: $wire.default_payment_type === 'PayPal' && $wire.default_payment_ref === $wire.email
                     }" 
                     x-init="$watch('paymentType', val => { if(val === 'PayPal') { useAccountEmail = true; $wire.default_payment_ref = $wire.email; } else { useAccountEmail = false; } });
                             $watch('$wire.email', val => { if(paymentType === 'PayPal' && useAccountEmail) $wire.default_payment_ref = val; });
                             $watch('useAccountEmail', val => { if(val && paymentType === 'PayPal') $wire.default_payment_ref = $wire.email; else if (paymentType === 'PayPal') $wire.default_payment_ref = ''; });"
                     class="flex flex-col gap-6">
                    <flux:select wire:model="default_payment_type" :label="__('Default Payment Type (Optional)')" x-model="paymentType">
                        <option value="">{{ __('None') }}</option>
                        <option value="Visa">Visa</option>
                        <option value="PayPal">PayPal</option>
                        <option value="MB WAY">MB WAY</option>
                    </flux:select>

                    <div x-show="paymentType !== ''" x-transition x-cloak>
                        <flux:input
                            wire:model="default_payment_ref"
                            :label="__('Default Payment Reference')"
                            type="text"
                            x-bind:placeholder="paymentType === 'Visa' ? 'Card Number (16 digits)' : (paymentType === 'PayPal' ? 'PayPal Email' : 'MB WAY Phone Number')"
                            x-bind:readonly="paymentType === 'PayPal' && useAccountEmail"
                            x-bind:class="paymentType === 'PayPal' && useAccountEmail ? 'opacity-60 cursor-not-allowed pointer-events-none bg-gray-100 dark:bg-zinc-800' : ''"
                        />

                        <!-- Checkbox for PayPal -->
                        <div x-show="paymentType === 'PayPal'" class="mt-2">
                            <flux:checkbox
                                label="{{ __('Use my account email') }}"
                                x-model="useAccountEmail"
                            />
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>