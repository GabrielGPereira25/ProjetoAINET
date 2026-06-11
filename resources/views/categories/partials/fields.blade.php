@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp


<div class="flex flex-col sm:flex-row sm:justify-between space-x-8">
    <div class="grow mt-6 space-y-4">
        <div class="w-full flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <flux:input name="name" label="Name" value="{{ old('name', $category->name) }}" :disabled="$readonly" />
            <div>
                <x-field.image name="image_file" label="Image" width="md" :readonly="$readonly" deleteTitle="Delete Image"
                    :deleteAllow="$mode == 'edit' && $category->image_url" deleteForm="form_to_delete_category_image" :imageUrl="$category->imageFullUrl"
                    class="sm:-mt-[1.5rem] w-full sm:w-64" />
            </div>
        </div>
