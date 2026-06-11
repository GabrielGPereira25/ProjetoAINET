<x-layouts::main-content :title="__('Courses')" heading="List of orders"
    subheading="Manage the orders offered by the institution">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
        <div class="flex justify-start ">
            <div class="my-4 p-6 ">
                <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
                    <x-orders.filter-card :filterAction="route('orders.index')" :resetUrl="route('orders.index')" :nif="$filterByNif" :date="$filterByDate" :status="$filterByStatus" />
                    <x-orders.table :orders="$orders" :showView="true" />
                </div>
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts::main-content>
