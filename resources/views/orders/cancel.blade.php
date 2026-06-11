<x-layouts::main-content title="Cancel Order" :heading="'Order ' . $order->id">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <div class="mt-6 space-y-4">
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <flux:input name="reason" label="Reason for cancellation"/>
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Cancel Order
                        </button>
                    </form>
