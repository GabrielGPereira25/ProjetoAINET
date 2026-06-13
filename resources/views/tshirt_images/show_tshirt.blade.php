<x-layouts::main-content title="Tshirt" :heading="'Choose an image '">
    <div class="bg-transparent">
        <div class="py-6 flex space-x-10 px-6">


            <!-- Image gallery -->
            <div class="relative w-5/12 lg:w-400px shrink-0 aspect-3/4 rounded-lg overflow-hidden max-lg:hidden">

                <img src="{{ asset('storage/tshirt_base/plain_white.png') }}"
                     alt="T-shirt Base"
                     class="absolute inset-0 size-full object-contain object-top p-8" />

                <div class="absolute inset-0 flex items-start justify-center pt-[30%]">
                    <img src="{{ $tshirt_image->imageFullUrl }}"
                         alt="{{ $tshirt_image->name }}"
                         class="w-1/3 h-auto object-contain drop-shadow-sm" />
                </div>
            </div>


            <!-- Product info -->
            <div
                class="grow">
                <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
                    <h1 class="text-2xl font-bold tracking-tight text-white-900 sm:text-3xl">{{ $tshirt_image->name }}</h1>
                </div>

                <!-- Options -->
                <div class="mt-4 lg:row-span-3 lg:mt-0">
                    <h2 class="sr-only">Tshirt information</h2>
                    <p class="text-3xl tracking-tight text-white-900">{{ $tshirt_image->category->name }}</p>


                    <form class="mt-10" action="{{ route('cart.add', ['tshirt_image' => $tshirt_image]) }}"
                        method="post">
                        @csrf
                        <div>
                            <h3 class="text-sm font-medium text-white-900">Color</h3>

                            <fieldset aria-label="Choose a color" class="mt-4">
                                <div class="flex flex-wrap items-center gap-x-3 ">
                                    @foreach ($colors as $color)
                                        <div class="flex mb-2 rounded-full outline -outline-offset-1 outline-black/10">
                                            <input style="background-color:#{{ $color->code }}" type="radio"
                                                name="color" value="{{ $color->code }}" checked
                                                aria-label="{{ $color->code }}"
                                                class="size-8 appearance-none rounded-full forced-color-adjust-none checked:outline-2 checked:outline-offset-2 checked:outline-gray-400 focus-visible:outline-3 focus-visible:outline-offset-3" />
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>

                        <!-- Sizes -->
                        <div class="mt-10">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-medium text-white-900">Size</h3>
                            </div>

                            <fieldset aria-label="Choose a size" class="mt-4">
                                <div class="grid grid-cols-5 gap-3">

                                    <label aria-label="XS"
                                        class="group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-indigo-600 has-checked:bg-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600 has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                                        <input type="radio" name="size" value="XS"
                                            class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                        <span
                                            class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">XS</span>
                                    </label>
                                    <label aria-label="S"
                                        class="group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-indigo-600 has-checked:bg-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600 has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                                        <input type="radio" name="size" checked value="S"
                                            class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                        <span
                                            class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">S</span>
                                    </label>
                                    <label aria-label="M"
                                        class="group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-indigo-600 has-checked:bg-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600 has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                                        <input type="radio" name="size" value="M"
                                            class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                        <span
                                            class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">M</span>
                                    </label>
                                    <label aria-label="L"
                                        class="group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-indigo-600 has-checked:bg-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600 has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                                        <input type="radio" name="size" value="L"
                                            class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                        <span
                                            class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">L</span>
                                    </label>
                                    <label aria-label="XL"
                                        class="group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-indigo-600 has-checked:bg-indigo-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600 has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                                        <input type="radio" name="size" value="XL"
                                            class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                                        <span
                                            class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">XL</span>
                                    </label>

                                </div>
                            </fieldset>
                            <fieldset aria-label="Choose qty" class="mt-4">
                                <div>
                                    <label class="text-white-900 block mb-4">Quantity</label>
                                    <input type="number" class="w-16 border-white border text-white-900 px-2 py-1" min="1" name="qty" value="{{ old('qty', 1) }}">
                                </div>
                            </fieldset>
                        </div>

                        <button type="submit"
                            class="mt-10 flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">Add
                            to bag</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</x-layouts::main-content>
