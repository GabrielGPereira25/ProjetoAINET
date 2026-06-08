<div {{ $attributes }}>
    <table class="table-auto border-collapse">
        <thead>
            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left">Code</th>
                <th class="px-2 py-2 text-left">Name</th>
                @if ($showView)
                    <th></th>
                @endif
                @if ($showEdit)
                    <th></th>
                @endif
                @if ($showDelete)
                    <th></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($colors as $color)
                <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                    <td class="px-2 py-2 text-left"><div class="w-4 h-4 rounded-full" style="background-color: #{{ $color->code }};"></div></td>
                    <td class="px-2 py-2 text-left">{{ $color->name }}</td>
                    @if ($showView)
                        <td class="ps-2 px-0.5">
                            <a href="{{ route('colors.show', ['color' => $color]) }}">
                                <flux:icon.eye class="size-5 hover:text-green-600" />
                            </a>
                        </td>
                    @endif
                    @if ($showEdit)
                        <td class="px-0.5">
                            <a href="{{ route('colors.edit', ['color' => $color]) }}">
                                <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                            </a>
                        </td>
                    @endif
                    @if ($showDelete)
                        <td class="px-0.5">
                            <form method="POST" action="{{ route('colors.destroy', ['color' => $color]) }}"
                                class="flex items-center">
                                @csrf
                                @method('DELETE')
                                <button type="submit">
                                    <flux:icon.trash class="size-5 hover:text-red-600" />
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
