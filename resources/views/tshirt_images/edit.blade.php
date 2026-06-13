<x-layouts::main-content :title="auth()->user()->user_type === 'C' ? __('Edit Custom Image') : __('Edit Catalog Image')"
                        :heading="auth()->user()->user_type === 'C' ? __('Edit Custom Image') : __('Edit Catalog Image')"
                        :subheading="__('Update the details below.')">
    
    <div class="flex w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="my-4 p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg max-w-2xl">
            <form action="{{ route('tshirt_images.update', $tshirt_image) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Current Image:</p>
                    <img src="{{ $tshirt_image->image_full_url }}" alt="{{ $tshirt_image->name }}" class="h-32 object-cover rounded border border-zinc-200 dark:border-zinc-700">
                </div>

                <flux:input name="name" :label="__('Name')" value="{{ old('name', $tshirt_image->name) }}" required />
                
                <flux:textarea name="description" :label="__('Description (Optional)')" rows="3">{{ old('description', $tshirt_image->description) }}</flux:textarea>
                
                @if(auth()->user()->user_type !== 'C')
                    <flux:select name="category_id" :label="__('Category (Optional)')">
                        <option value="">{{ __('None') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $tshirt_image->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </flux:select>
                @endif
                
                <div class="flex flex-col gap-2">
                    <flux:input type="file" name="image_file" :label="__('Replace Image File (Optional)')" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp" />
                    <p class="text-xs text-zinc-500">Max size: 4MB. Formats: JPG, PNG, SVG, WEBP. Leave empty to keep the current image.</p>
                </div>

                <div class="flex items-center gap-4 mt-4">
                    <flux:button type="submit" variant="primary">Save Changes</flux:button>
                    <flux:button href="{{ route('tshirt_images.index') }}">Cancel</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::main-content>
