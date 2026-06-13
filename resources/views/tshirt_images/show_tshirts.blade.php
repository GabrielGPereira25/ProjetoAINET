<x-layouts::main-content title="Catalog" heading="FunShirt" subheading="Our Catalog">
    <div class="flex w-full flex-1 flex-col gap-4 rounded-xl ">
        <div class="flex justify-start w-full">
            <div class="my-4 p-6 w-full">
                <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300 w-full">
                    <div>
                        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
                            <div class="flex flex-col md:flex-row md:items-end justify-between w-full gap-4 mb-8">
                                <h2 class="text-2xl font-bold tracking-tight mb-4 md:mb-0">Choose an image</h2>

                                <form method="GET" action="{{ route('home') }}" class="flex flex-col sm:flex-row items-end gap-4">
                                    <div class="w-full sm:w-48">
                                        <flux:input name="name" :label="__('Name')" type="text" placeholder="Search by name..." value="{{ $filterByName }}" />
                                    </div>
                                    <div class="w-full sm:w-48">
                                        <flux:input name="description" :label="__('Description')" type="text" placeholder="Search by description..." value="{{ $filterByDescription }}" />
                                    </div>
                                    <div class="w-full sm:w-48">
                                        <flux:select name="category_id" :label="__('Category')">
                                            <option value="">{{ __('All Categories') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $filterByCategory == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </flux:select>
                                    </div>
                                    <div class="flex gap-2 w-full sm:w-auto">
                                        <flux:button type="submit" variant="primary">{{ __('Filter') }}</flux:button>
                                        <flux:button href="{{ route('home') }}">{{ __('Clear') }}</flux:button>
                                    </div>
                                </form>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                                @foreach ($tshirt_images as $tshirt_image)
                                    <div class="group relative">
                                        <a href="{{ route('show_tshirt', ['tshirt_image' => $tshirt_image]) }}">
                                            <img src="{{ $tshirt_image->imageFullUrl }}" alt="{{ $tshirt_image->name }}"
                                                class="aspect-square w-full rounded-md object-contain group-hover:opacity-75 lg:aspect-auto lg:h-80" />
                                        </a>
                                        <div class="mt-4 flex justify-between">
                                            <div>
                                                <h3 class="text-sm text-zinc-300">
                                                    <a
                                                        href="{{ route('show_tshirt', ['tshirt_image' => $tshirt_image]) }}">
                                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                                        "{{ $tshirt_image->name }}"
                                                    </a>
                                                </h3>
                                                <p class="mt-1 text-sm text-zinc-400">
                                                    {{ $tshirt_image->category?->name ?? 'Sem categoria' }}</p>
                                            </div>
                                            <p class="text-sm font-medium text-white">
                                                {{ $tshirt_image->customer ? $price->unit_price_own : $price->unit_price_catalog }}€
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
                <div class="mt-4">
                    {{ $tshirt_images->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts::main-content>
