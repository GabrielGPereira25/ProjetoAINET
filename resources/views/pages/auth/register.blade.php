<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6"
            x-data="{ 
                email: '{{ old('email') }}', 
                paymentType: '{{ old('default_payment_type', '') }}', 
                useAccountEmail: {{ old('default_payment_type') === 'PayPal' ? 'true' : 'false' }},
                paymentRef: '{{ old('default_payment_ref', '') }}'
            }"
            x-init="$watch('paymentType', val => { if (val === 'PayPal') { useAccountEmail = true; paymentRef = email; } else { useAccountEmail = false; } });
                    $watch('email', val => { if (paymentType === 'PayPal' && useAccountEmail) paymentRef = val; });
                    $watch('useAccountEmail', val => { if (val && paymentType === 'PayPal') paymentRef = email; else if (paymentType === 'PayPal') paymentRef = ''; });"
        >
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
                x-model="email"
            />

            <!-- Gender -->
            <flux:radio.group name="gender" :label="__('Gender')" required>
                <flux:radio value="M" :label="__('Male')" />
                <flux:radio value="F" :label="__('Female')" />
            </flux:radio.group>

            <hr class="my-2 border-zinc-200 dark:border-zinc-700" />

            <!-- NIF -->
            <flux:input
                name="nif"
                :label="__('NIF (Optional)')"
                :value="old('nif')"
                type="text"
                :placeholder="__('9-digit NIF')"
            />

            <!-- Address -->
            <flux:input
                name="address"
                :label="__('Default Address (Optional)')"
                :value="old('address')"
                type="text"
                :placeholder="__('Your shipping address')"
            />

            <div class="flex flex-col gap-6">
                <!-- Default Payment Type -->
                <flux:select name="default_payment_type" :label="__('Default Payment Type (Optional)')" x-model="paymentType">
                    <option value="">{{ __('None') }}</option>
                    <option value="Visa">Visa</option>
                    <option value="PayPal">PayPal</option>
                    <option value="MB WAY">MB WAY</option>
                </flux:select>

                <!-- Default Payment Ref -->
                <div x-show="paymentType !== ''" x-transition x-cloak>
                    <flux:input
                        name="default_payment_ref"
                        :label="__('Default Payment Reference')"
                        type="text"
                        x-model="paymentRef"
                        x-bind:placeholder="paymentType === 'Visa' ? 'Card Number (16 digits)' : (paymentType === 'PayPal' ? 'PayPal Email' : 'MB WAY Phone Number')"
                        x-bind:readonly="paymentType === 'PayPal' && useAccountEmail"
                        x-bind:class="paymentType === 'PayPal' && useAccountEmail ? 'opacity-60 cursor-not-allowed pointer-events-none bg-gray-100 dark:bg-zinc-800' : ''"
                    />

                    <!-- Checkbox for PayPal -->
                    <div x-show="paymentType === 'PayPal'" class="mt-2">
                        <flux:checkbox
                            name="use_account_email"
                            label="{{ __('Use my account email') }}"
                            x-model="useAccountEmail"
                        />
                    </div>
                </div>
            </div>
            
            <hr class="my-2 border-zinc-200 dark:border-zinc-700" />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
