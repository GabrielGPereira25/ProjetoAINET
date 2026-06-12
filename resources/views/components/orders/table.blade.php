<div {{ $attributes }}>
    <table class="mt-8 table-auto border-collapse">
        <thead>
            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left">NIF</th>
                <th class="px-2 py-2 text-left">Name</th>
                <th class="px-2 py-2 text-left">Date</th>
                <th class="px-2 py-2 text-left">Status</th>
                <th class="px-2 py-2 text-left">Total Price</th>
                @if ($showView)
                    <th></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                    <td class="px-2 py-2 text-left">{{ $order->nif }}</td>
                    <td class="px-2 py-2 text-left">{{ $order->customer->user->name }}</td>
                    <td class="px-2 py-2 text-left">{{ $order->date }}</td>

                        <td class="px-2 py-2 text-left">

                            @if ($order->status != 'pending' && auth()->user()->user_type === 'customer')

                                    {{ $order->status }}
                            @else
                                <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <flux:select name="status" label="" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="closed" {{ $order->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                        @can('admin')
                                        <option value="canceled" {{ $order->status === 'canceled' ? 'selected' : '' }}>Canceled</option>
                                        @endcan
                                    </flux:select>
                                </form>
                            @endif
                        </td>
                    <td class="px-2 py-2 text-left">{{ $order->total_price }}</td>
                    @if ($showView)
                        <td class="ps-2 px-0.5">
                            <a href="{{ route('orders.show', ['order' => $order]) }}">
                                <flux:icon.eye class="size-5 hover:text-green-600" />
                            </a>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
