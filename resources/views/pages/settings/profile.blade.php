<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;
    use \App\Traits\UserPhotoFileStorage;
    
    public $photo;
    public string $name = '';
    public string $email = '';
    public string $gender = '';
    public string $nif = '';
    public string $address = '';
    public string $default_payment_type = '';
    public string $visa_ref = '';
    public string $paypal_ref = '';
    public string $mbway_ref = '';

    public function mount(): void
    {
        \Illuminate\Support\Facades\Gate::authorize('admin-or-customer');

        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->gender = Auth::user()->gender ?? '';
        if (Auth::user()->user_type === 'C' && Auth::user()->customer) {
            $this->nif = Auth::user()->customer->nif ?? '';
            $this->address = Auth::user()->customer->address ?? '';
            $this->default_payment_type = Auth::user()->customer->default_payment_type ?? '';
            $custom = Auth::user()->customer->custom ?? [];
            $this->visa_ref = $custom['visa_ref'] ?? '';
            $this->paypal_ref = $custom['paypal_ref'] ?? '';
            $this->mbway_ref = $custom['mbway_ref'] ?? '';
        }
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            ...$this->profileRules($user->id),
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'nif' => ['nullable', 'string', 'regex:/^[0-9]{9}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'default_payment_type' => ['nullable', 'in:Visa,PayPal,MB WAY'],
            'visa_ref' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value && !preg_match('/^4[0-9]{15}$/', str_replace(' ', '', $value))) {
                    $fail('O cartão Visa deve começar por 4 e ter 16 dígitos.');
                }
            }],
            'paypal_ref' => ['nullable', 'email'],
            'mbway_ref' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value && !preg_match('/^9[0-9]{8}$/', str_replace(' ', '', $value))) {
                    $fail('O número MB WAY deve começar por 9 e ter 9 dígitos.');
                }
            }],
        ], [
            'nif.regex' => 'O NIF deve conter exatamente 9 dígitos numéricos.',
            'paypal_ref.email' => 'O e-mail PayPal não é válido.',
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

        if ($this->photo) {
            $this->deleteUserPhoto($user);
            $this->storeUserPhoto($this->photo, $user);
        } // ESTA CHAVETA FALTAVA NO TEU CÓDIGO

        if ($user->user_type === 'C' && $user->customer) {
            $custom = $user->customer->custom ?? [];
            if (isset($validated['visa_ref'])) $custom['visa_ref'] = str_replace(' ', '', $validated['visa_ref']);
            if (isset($validated['paypal_ref'])) $custom['paypal_ref'] = $validated['paypal_ref'];
            if (isset($validated['mbway_ref'])) $custom['mbway_ref'] = str_replace(' ', '', $validated['mbway_ref']);

            $user->customer->update([
                'nif' => $validated['nif'] ?? null,
                'address' => $validated['address'] ?? null,
                'default_payment_type' => $validated['default_payment_type'] ?? null,
                'custom' => empty($custom) ? null : $custom,
            ]);
        }

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

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
            <div class="flex items-center gap-6">
                <div class="shrink-0 relative">
                    @if ($photo)
                        <img class="h-16 w-16 object-cover rounded-full border border-zinc-600" src="{{ $photo->temporaryUrl() }}" alt="Preview">
                    @elseif (Auth::user()->photo_url)
                        <img class="h-16 w-16 object-cover rounded-full border border-zinc-600" src="{{ asset('storage/photos/' . Auth::user()->photo_url) }}" alt="Avatar">
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-800 border border-zinc-700 text-zinc-400">
                            <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Profile Photo</label>
                    <input type="file" wire:model="photo" accept="image/*" class="block w-full text-sm text-zinc-400 file:mr-4 file:rounded-full file:border-0 file:bg-zinc-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-zinc-600" />
                    
                    <div wire:loading wire:target="photo" class="text-xs text-zinc-400 mt-1">A carregar preview...</div>
                    
                    @error('photo') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
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
                        useAccountEmail: $wire.default_payment_type === 'PayPal' && $wire.paypal_ref === $wire.email,
                        
                        formatVisa(el) {
                            let val = el.value.replace(/\D/g, '');
                            el.value = val.replace(/(\d{4})(?=\d)/g, '$1 ');
                            $wire.visa_ref = el.value;
                        },
                        formatMbway(el) {
                            let val = el.value.replace(/\D/g, '');
                            el.value = val.replace(/(\d{3})(?=\d)/g, '$1 ');
                            $wire.mbway_ref = el.value;
                        }
                     }" 
                     x-init="$watch('paymentType', val => { if(val === 'PayPal' && !$wire.paypal_ref) { useAccountEmail = true; $wire.paypal_ref = $wire.email; } });
                             $watch('$wire.email', val => { if(paymentType === 'PayPal' && useAccountEmail) $wire.paypal_ref = val; });
                             $watch('useAccountEmail', val => { if(val) $wire.paypal_ref = $wire.email; else $wire.paypal_ref = ''; });
                             
                             if ($wire.visa_ref) {
                                 let val = $wire.visa_ref.replace(/\D/g, '');
                                 $wire.visa_ref = val.replace(/(\d{4})(?=\d)/g, '$1 ');
                             }
                             if ($wire.mbway_ref) {
                                 let val = $wire.mbway_ref.replace(/\D/g, '');
                                 $wire.mbway_ref = val.replace(/(\d{3})(?=\d)/g, '$1 ');
                             }
                             "
                     class="flex flex-col gap-6">
                    <flux:select wire:model="default_payment_type" :label="__('Default Payment Type (Optional)')" x-model="paymentType">
                        <option value="">{{ __('None') }}</option>
                        <option value="Visa">Visa</option>
                        <option value="PayPal">PayPal</option>
                        <option value="MB WAY">MB WAY</option>
                    </flux:select>

                    <div class="space-y-4">
                        <flux:input
                            wire:model="visa_ref"
                            :label="__('Visa Card Number')"
                            type="text"
                            placeholder="4000 0000 0000 0000"
                            pattern="^4[0-9]{3}( [0-9]{4}){3}$|^4[0-9]{15}$"
                            title="O cartão Visa deve começar por 4 e ter 16 dígitos."
                            x-on:input="formatVisa($event.target)"
                        />

                        <flux:input
                            wire:model="mbway_ref"
                            :label="__('MB WAY Phone Number')"
                            type="text"
                            placeholder="900 000 000"
                            pattern="^9[0-9]{2}( [0-9]{3}){2}$|^9[0-9]{8}$"
                            title="O número MB WAY deve começar por 9 e ter 9 dígitos."
                            x-on:input="formatMbway($event.target)"
                        />

                        <div>
                            <flux:input
                                wire:model="paypal_ref"
                                :label="__('PayPal Email Address')"
                                type="email"
                                placeholder="example@email.com"
                                x-bind:readonly="useAccountEmail"
                                x-bind:class="useAccountEmail ? 'opacity-60 cursor-not-allowed pointer-events-none bg-gray-100 dark:bg-zinc-800' : ''"
                            />
                            
                            <div class="mt-2">
                                <flux:checkbox
                                    label="{{ __('Use my account email') }}"
                                    x-model="useAccountEmail"
                                />
                            </div>
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