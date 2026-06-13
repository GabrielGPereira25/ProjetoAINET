<x-layouts::main-content title="order" :heading="'Order ' . $order->id">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <div class="mt-6 space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Costumer Information</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">NIF: {{ $order->nif }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Name: {{ $order->customer->user->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Email: {{ $order->customer->user->email }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Address: {{ $order->address }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Order Details</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Date: {{ $order->date }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status: {{ $order->status }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Price: {{ $order->total_price }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Order Details</h3>
                    <table>
                    <head>
                        <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                            <th class="px-2 py-2 text-left">Product</th>
                            <th class="px-2 py-2 text-left">Color</th>
                            <th class="px-2 py-2 text-left">Quantity</th>
                            <th class="px-2 py-2 text-left">Price</th>
                        </tr>
                    </head>
                    <tbody>
                        @foreach ($order->order_items as $item)
                            <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                                <td class="px-2 py-2 text-left">
                                    <img src="{{ $item->tshirt_image->imageFullUrl }}" alt="{{ $item->tshirt_image->name }}" class="w-16 h-16 object-cover">
                                    <br>
                                    "{{ $item->tshirt_image->name }}"
                                </td>
                                <td class="px-2 py-2 text-left">{{ $item->color_code }}</td>
                                <td class="px-2 py-2 text-left">{{ $item->qty }}</td>
                                <td class="px-2 py-2 text-left">{{ $item->unit_price }}€</td>
                            </tr>
                        @endforeach
                </div>


            </section>
        </div>
    </div>
</x-layouts::main-content>
