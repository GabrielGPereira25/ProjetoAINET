<x-layouts::main-content title="Catalog" :heading="'Choose an image '">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
        <div class="flex justify-start ">
            <div class="my-4 p-6 ">
                <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
                    <div class="bg-white">
                        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
                            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Customers also purchased</h2>

                            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                                @foreach ($tshirt_images as $tshirt_image)
                                    <div class="group relative">
                                        <a href="{{ route('show_tshirt', ['tshirt_image' => $tshirt_image]) }}">
                                            <img src="{{ $tshirt_image->imageFullUrl }}" alt="{{ $tshirt_image->name }}"
                                                class="aspect-square w-full rounded-md bg-gray-200 object-cover group-hover:opacity-75 lg:aspect-auto lg:h-80" />
                                        </a>
                                        <div class="mt-4 flex justify-between">
                                            <div>
                                                <h3 class="text-sm text-gray-700">
                                                    <a
                                                        href="{{ route('show_tshirt', ['tshirt_image' => $tshirt_image]) }}">
                                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                                        "{{ $tshirt_image->name }}"
                                                    </a>
                                                </h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ $tshirt_image->category->name }}</p>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900">
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
