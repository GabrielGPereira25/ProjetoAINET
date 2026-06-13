<x-layouts::main-content title="Cart" heading="Shopping Cart" subheading="Review and checkout your awesome T-shirts">
    <div class="flex w-full flex-1 flex-col gap-4 rounded-xl ">
        <div class="flex justify-start ">
            @if (count($cart) == 0)
                <div class="my-4 p-6 ">
                    <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300">Your cart is empty</h2>
                </div>
            @else
                <div class="my-4 p-6 w-full max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-16 xl:gap-24 items-start">
                    
                    <div class="flex-1 w-full">
                        <h2 class="mb-6 text-2xl font-bold text-gray-700 dark:text-gray-300">T-shirts in your cart:</h2>
                        
                        <div class="flex flex-col gap-4">
                            @foreach ($cart as $id => $item)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm gap-4">
                                    <!-- Info -->
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $item['tshirt_image_url'] }}"
                                            alt="{{ $item['tshirt_image_name'] }}" class="w-24 h-24 rounded-lg object-cover bg-gray-100 border border-gray-200 dark:border-gray-600">
                                        <div>
                                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100">{{ $item['tshirt_image_name'] }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Size: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $item['size'] }}</span> 
                                                <span class="mx-2">•</span> 
                                                Color: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $item['color'] }}</span>
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Unit Price: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $item['unit_price'] }}€</span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions & Price -->
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-6">
                                        <!-- Qty Control -->
                                        <form action="{{ route('cart.updateQty', ['id' => $id]) }}" method="post" class="flex items-center bg-gray-100 dark:bg-gray-900 rounded-lg p-1 border border-gray-200 dark:border-gray-700">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" onclick="this.nextElementSibling.stepDown(); this.form.submit()" class="p-2 text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                            </button>
                                            <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" class="w-12 text-center bg-transparent border-none text-gray-800 dark:text-gray-100 focus:ring-0 p-0 font-medium" onchange="this.form.submit()">
                                            <button type="button" onclick="this.previousElementSibling.stepUp(); this.form.submit()" class="p-2 text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </form>

                                        <!-- Subtotal -->
                                        <div class="text-right sm:w-24">
                                            <span class="font-bold text-xl text-gray-800 dark:text-gray-100">{{ $item['sub_total'] }}€</span>
                                        </div>
                                        
                                        <!-- Remove -->
                                        <form action="{{ route('cart.remove', ['id' => $id]) }}" method="post" onsubmit="return confirm('Are you sure you want to remove this item from the cart?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full transition-colors" title="Remove item">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right side: Sticky Sidebar -->
                    <div class="my-14 w-full lg:w-80 xl:w-96 lg:sticky lg:top-8 flex flex-col gap-6">
                        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm w-full">
                            <p class="text-gray-500 dark:text-gray-400 mb-4 text-sm uppercase tracking-widest font-bold border-b border-gray-200 dark:border-gray-700 pb-3">Order Summary</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-gray-600 dark:text-gray-300 font-medium">Total Items</span>
                                <span class="font-bold text-gray-800 dark:text-gray-100">{{ $total_items }}</span>
                            </div>
                            <div class="flex justify-between items-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-2">
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-100">Total</span>
                                <span class="font-black text-3xl text-indigo-600 dark:text-indigo-400">{{ $total_price }}€</span>
                            </div>
                        </div>

                        @can('customer-or-anonymous')
                            <div class="flex flex-col gap-3 w-full">
                                <flux:modal.trigger name="checkout-modal">
                                    <flux:button variant="primary" class="w-full py-6 text-lg shadow-md hover:shadow-lg transition-shadow" icon="credit-card">Proceed to Checkout</flux:button>
                                </flux:modal.trigger>
                                
                                <form action="{{ route('cart.destroy') }}" method="post" onsubmit="return confirm('Are you sure you want to clear the entire cart?');" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button variant="danger" type="submit" class="w-full" icon="trash">Clear Cart</flux:button>
                                </form>
                            </div>
                        @else
                            <div class="w-full">
                                <flux:button variant="primary" disabled class="w-full py-6 text-lg">Login to Checkout</flux:button>
                            </div>
                        @endcan
                    </div>

                    @can('customer-or-anonymous')
                    <flux:modal name="checkout-modal" class="md:w-[600px] sm:w-full space-y-6">
                        <div class="mb-6">
                            <flux:heading size="xl" class="mb-1">Checkout Details</flux:heading>
                            <flux:subheading>Fill in your information to complete your order.</flux:subheading>
                        </div>

                        <form action="{{ route('cart.confirm') }}" method="post" id="checkout-form" class="space-y-6">
                            @csrf
                            
                            <div class="grid grid-cols-1 gap-6">
                                <flux:input name="nif" label="NIF" value="{{ old('nif', auth()->user()?->customer?->nif) }}" placeholder="Optional: 9-digit NIF" pattern="^[0-9]{9}$" title="O NIF deve conter exatamente 9 dígitos numéricos." />
                                
                                <flux:textarea name="address" label="Shipping Address" placeholder="Enter your full shipping address" rows="3" required>{{ old('address', auth()->user()?->customer?->address) }}</flux:textarea>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-700 my-6" />
                            
                            <div x-data="{ 
                                userEmail: '{{ auth()->user()->email }}',
                                savedType: '{{ auth()->user()?->customer?->default_payment_type ?? '' }}',
                                savedVisa: '{{ auth()->user()?->customer?->custom['visa_ref'] ?? '' }}',
                                savedMbway: '{{ auth()->user()?->customer?->custom['mbway_ref'] ?? '' }}',
                                savedPaypal: '{{ auth()->user()?->customer?->custom['paypal_ref'] ?? '' }}',
                                paymentType: '{{ old('payment_type', auth()->user()?->customer?->default_payment_type ?? 'Visa') }}',
                                paymentRef: '{{ old('payment_ref', '') }}',
                                
                                init() {
                                    if (!this.paymentRef) {
                                        this.setType(this.paymentType);
                                    } else {
                                        this.formatRef();
                                    }
                                },

                                setType(type) { 
                                    this.paymentType = type;
                                    document.getElementById('hidden_payment_type').value = type;

                                    if (type === 'Visa') {
                                        this.paymentRef = this.savedVisa;
                                    } else if (type === 'MB WAY') {
                                        this.paymentRef = this.savedMbway;
                                    } else if (type === 'PayPal') {
                                        this.paymentRef = this.savedPaypal ? this.savedPaypal : this.userEmail;
                                    }
                                    
                                    this.$nextTick(() => {
                                        this.formatRef();
                                    });
                                },
                                
                                formatRef() {
                                    if (!this.paymentRef) return;
                                    
                                    if (this.paymentType === 'Visa') {
                                        let val = this.paymentRef.replace(/\D/g, ''); // Apenas números
                                        val = val.substring(0, 16); // Máximo 16 dígitos
                                        let parts = [];
                                        for (let i = 0; i < val.length; i += 4) {
                                            parts.push(val.substring(i, i + 4));
                                        }
                                        this.paymentRef = parts.join(' ');
                                    } else if (this.paymentType === 'MB WAY') {
                                        let val = this.paymentRef.replace(/\D/g, ''); // Apenas números
                                        val = val.substring(0, 9); // Máximo 9 dígitos
                                        let parts = [];
                                        for (let i = 0; i < val.length; i += 3) {
                                            parts.push(val.substring(i, i + 3));
                                        }
                                        this.paymentRef = parts.join(' ');
                                    }
                                }
                            }" x-init="formatRef()">
                                <flux:heading size="lg" class="mb-4">Payment Method</flux:heading>
                                <input type="hidden" name="payment_type" id="hidden_payment_type" x-model="paymentType">
                                
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <!-- Visa -->
                                    <button type="button" @click="setType('Visa')" 
                                        :class="paymentType === 'Visa' ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 border-indigo-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        class="flex flex-col items-center justify-center p-4 rounded-xl border transition-all cursor-pointer">
                                        <img src="{{ asset('img/payment/visa.png') }}" alt="Visa" class="h-8 mb-2 object-contain" :class="paymentType === 'Visa' ? 'opacity-100' : 'opacity-70'">
                                        <span class="font-semibold text-sm" :class="paymentType === 'Visa' ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400'">Visa</span>
                                    </button>

                                    <!-- PayPal -->
                                    <button type="button" @click="setType('PayPal')" 
                                        :class="paymentType === 'PayPal' ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 border-indigo-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        class="flex flex-col items-center justify-center p-4 rounded-xl border transition-all cursor-pointer">
                                        <img src="{{ asset('img/payment/paypal.png') }}" alt="PayPal" class="h-8 mb-2 object-contain" :class="paymentType === 'PayPal' ? 'opacity-100' : 'opacity-70'">
                                        <span class="font-semibold text-sm" :class="paymentType === 'PayPal' ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400'">PayPal</span>
                                    </button>

                                    <!-- MB WAY -->
                                    <button type="button" @click="setType('MB WAY')" 
                                        :class="paymentType === 'MB WAY' ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 border-indigo-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        class="flex flex-col items-center justify-center p-4 rounded-xl border transition-all cursor-pointer">
                                        <!-- Logo Modo Claro -->
                                        <img src="{{ asset('img/payment/mbway-light.png') }}" alt="MB WAY" class="h-8 mb-2 object-contain dark:hidden" :class="paymentType === 'MB WAY' ? 'opacity-100' : 'opacity-70'">
                                        <!-- Logo Modo Escuro -->
                                        <img src="{{ asset('img/payment/mbway-dark.png') }}" alt="MB WAY" class="h-8 mb-2 object-contain hidden dark:block" :class="paymentType === 'MB WAY' ? 'opacity-100' : 'opacity-70'">
                                        <span class="font-semibold text-sm" :class="paymentType === 'MB WAY' ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400'">MB WAY</span>
                                    </button>
                                </div>

                                <div x-show="paymentType !== ''" x-transition x-cloak class="bg-gray-50 dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 mb-6 relative overflow-hidden">
                                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                                    <flux:input 
                                        name="payment_ref" 
                                        label="Payment Reference" 
                                        x-model="paymentRef"
                                        @input="formatRef"
                                        x-bind:placeholder="paymentType === 'Visa' ? '0000 0000 0000 0000' : (paymentType === 'PayPal' ? 'PayPal Email Address' : '000 000 000')"
                                        x-bind:pattern="paymentType === 'Visa' ? '^4[0-9]{3} [0-9]{4} [0-9]{4} [0-9]{4}$' : (paymentType === 'MB WAY' ? '^9[0-9]{2} [0-9]{3} [0-9]{3}$' : '.*')"
                                        x-bind:type="paymentType === 'PayPal' ? 'email' : 'text'"
                                        x-bind:title="paymentType === 'Visa' ? 'O cartão Visa deve começar por 4 e ter 16 dígitos.' : (paymentType === 'MB WAY' ? 'O número MB WAY deve começar por 9 e ter 9 dígitos.' : '')"
                                        required 
                                    />
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span x-text="'Please provide your ' + (paymentType === 'Visa' ? 'Card Number' : (paymentType === 'PayPal' ? 'PayPal Email Address' : 'Phone Number')) + ' to complete the ' + paymentType + ' transaction.'"></span>
                                    </p>
                                </div>
                            </div>

                            <flux:textarea name="notes" label="Order Notes (Optional)" value="{{ old('notes') }}" placeholder="Any special instructions for delivery..." rows="2" />
                            
                            <div class="flex justify-end gap-3 mt-8">
                                <flux:modal.close>
                                    <flux:button variant="ghost">Cancel</flux:button>
                                </flux:modal.close>
                                <flux:button variant="primary" type="submit" class="px-8 shadow-lg hover:shadow-xl">Confirm Order</flux:button>
                            </div>
                        </form>
                    </flux:modal>

                    @if($errors->hasAny(['nif', 'address', 'payment_type', 'payment_ref', 'notes']))
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                setTimeout(() => {
                                    if(typeof Flux !== 'undefined') {
                                        Flux.modal('checkout-modal').show();
                                    }
                                }, 100);
                            });
                        </script>
                    @endif
                    @endcan
                </div>
            @endempty
    </div>
</div>
</x-layouts::main-content>
