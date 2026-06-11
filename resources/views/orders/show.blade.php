<x-layouts::main-content :title="$order->name" :heading="'Category ' . $order->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <div class="mt-6 space-y-4">
                    @include('orders.partials.fields', ['mode' => 'show'])
                </div>
                @include('partials.form-buttons', [
                    'entity' => 'order',
                    'value' => $order,
                    'new' => true,
                    'edit' => true,
                    'delete' => true,
                ])
            </section>
        </div>
    </div>
    <form id="delete-form" method="POST" action="{{ route('orders.destroy', ['order' => $order]) }}"
        class="hidden">
        @csrf
    </form>
</x-layouts::main-content>
