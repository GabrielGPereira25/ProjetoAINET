<x-layouts::main-content :title="auth()->user()->user_type === 'C' ? __('My Custom Images') : __('T-shirt Images Catalog')"
                        :heading="auth()->user()->user_type === 'C' ? __('My Custom Images') : __('Catalog Management')"
                        :subheading="auth()->user()->user_type === 'C' ? __('Manage your personal t-shirt designs.') : __('Manage all public t-shirt designs.')">
    
    <div class="flex w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="my-4">
            <div class="flex items-center gap-4 mb-4">
                <flux:button variant="primary" href="{{ route('tshirt_images.create') }}">
                    {{ auth()->user()->user_type === 'C' ? __('Upload new custom image') : __('Create new catalog image') }}
                </flux:button>
            </div>
            
            <div class="mb-4">
                <form action="{{ route('tshirt_images.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    <flux:input name="name" :label="__('Name')" value="{{ request('name') }}" placeholder="Filter by name..." />
                    <flux:input name="description" :label="__('Description')" value="{{ request('description') }}" placeholder="Filter by description..." />
                    @if(auth()->user()->user_type !== 'C')
                        <flux:select name="category_id" :label="__('Category')">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </flux:select>
                    @endif
                    <div class="flex gap-2">
                        <flux:button type="submit" variant="primary">Filter</flux:button>
                        <flux:button href="{{ route('tshirt_images.index') }}">Reset</flux:button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                    <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300">
                        <tr>
                            <th scope="col" class="px-6 py-3">Image</th>
                            <th scope="col" class="px-6 py-3">Name</th>
                            @if(auth()->user()->user_type !== 'C')
                                <th scope="col" class="px-6 py-3">Category</th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tshirt_images as $image)
                            <tr class="bg-white border-b dark:bg-zinc-900 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                <td class="px-6 py-4">
                                    <img src="{{ $image->image_full_url }}" alt="{{ $image->name }}" class="w-16 h-16 object-cover rounded-md">
                                </td>
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                    {{ $image->name }}
                                </td>
                                @if(auth()->user()->user_type !== 'C')
                                    <td class="px-6 py-4">
                                        {{ $image->category ? $image->category->name : 'N/A' }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" href="{{ route('tshirt_images.edit', $image) }}">Edit</flux:button>
                                        <form action="{{ route('tshirt_images.destroy', $image) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="danger">Delete</flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->user_type === 'C' ? 3 : 4 }}" class="px-6 py-4 text-center">
                                    No images found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $tshirt_images->links() }}
            </div>
        </div>
    </div>
</x-layouts::main-content>
