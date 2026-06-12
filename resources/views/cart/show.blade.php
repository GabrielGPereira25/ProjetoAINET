<x-layouts::main-content title="Cart" heading="Shopping Cart" subheading="Disciplines to register for a student">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
        <div class="flex justify-start ">
            @if (count($cart) == 0)
                {{-- <div class="flex items-center justify-center w-full h-full"> --}}
                <div class="my-4 p-6 ">
                    <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300">Your cart is empty</h2>
                </div>
            @else
                <div class="my-4 p-6">
                    <h2 class="mb-4 text-2xl font-bold text-gray-700 dark:text-gray-300">Disciplines in your cart:</h2>
                    <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
                        <table>
                            <thead>
                                <tr
                                    class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                                    <th class="px-2 py-2 text-left">T-shirt</th>
                                    <th class="px-2 py-2 text-left">Size</th>
                                    <th class="px-2 py-2 text-left">Color</th>
                                    <th class="px-2 py-2 text-left">Quantity</th>
                                    <th class="px-2 py-2 text-left">Unit Price</th>
                                    <th class="px-2 py-2 text-left">Sub Total</th>
                                    <th class="px-2 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart as $id => $item)
                                    <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                                        <td class="px-2 py-2 text-left">
                                            <img src="{{ $item['tshirt_image_url'] }}"
                                                alt="{{ $item['tshirt_image_name'] }}" class="w-16 h-16 object-cover">
                                            <br>
                                            "{{ $item['tshirt_image_name'] }}"
                                        </td>
                                        <td class="px-2 py-2 text-left">{{ $item['size'] }}</td>
                                        <td class="px-2 py-2 text-left">{{ $item['color'] }}</td>
                                        <td class="px-2 py-2 text-left">{{ $item['qty'] }}</td>
                                        <td class="px-2 py-2 text-left">{{ $item['unit_price'] }}€</td>
                                        <td class="px-2 py-2 text-left">{{ $item['sub_total'] }}€</td>
                                        <td class="px-2 py-2 text-left">
                                            <form action="{{ route('cart.remove', ['id' => $id]) }}" method="post"
                                                onsubmit="return confirm('Are you sure you want to remove this item from the cart?');">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button variant="danger" type="submit">Remove</flux:button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-12">
                        @can('customer-or-anonymous')
                        <div>
                            <h3 class="mb-4 text-xl">Shopping Cart Confirmation </h3>
                        </div>
                        <div class="flex justify-between items-end space-x-4">
                            <form action="{{ route('cart.confirm') }}" method="post" class="flex items-end space-x-4">
                                @csrf
                                <flux:input name="nif" label="NIF"
                                    value="{{ old('nif', auth()->user()?->customer?->nif) }}" />
                                <flux:select name="payment_type" label="Payment Method">
                                    <option value="PayPal"
                                        {{ old('payment_type', auth()->user()?->customer?->default_payment_type) === 'PayPal' ? 'selected' : '' }}>
                                        PayPal</option>
                                    <option value="Visa"
                                        {{ old('payment_type', auth()->user()?->customer?->default_payment_type) === 'Visa' ? 'selected' : '' }}>
                                        Visa</option>
                                    <option value="MB WAY"
                                        {{ old('payment_type', auth()->user()?->customer?->default_payment_type) === 'MB WAY' ? 'selected' : '' }}>
                                        MB WAY</option>
                                </flux:select>
                                <flux:input name="payment_ref" label="Payment Reference"
                                    value="{{ old('payment_ref', auth()->user()?->customer?->default_payment_ref) }}" />
                                <flux:textarea name="address" label="Address"
                                    value="{{ old('address', auth()->user()?->customer?->address) }}" />
                                <flux:textarea name="notes" label="Notes" value="{{ old('notes') }}" />
                                <flux:button variant="primary" type="submit">Confirm</flux:button>
                            </form>
                            <form action="{{ route('cart.destroy') }}" method="post" class="flex items-end">
                                @csrf
                                @method('DELETE')
                                <flux:button variant="danger" type="submit">Clear Cart</flux:button>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>
            @endempty
    </div>
</div>
</x-layouts::main-content>
