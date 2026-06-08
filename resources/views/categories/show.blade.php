<x-layouts::main-content :title="$category->name" :heading="'Category ' . $category->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <div class="mt-6 space-y-4">
                    @include('categories.partials.fields', ['mode' => 'show'])
                </div>
                @include('partials.form-buttons', [
                    'entity' => 'category',
                    'value' => $category,
                    'new' => true,
                    'edit' => true,
                    'delete' => true,
                ])
            </section>
        </div>
    </div>
    <form id="delete-form" method="POST" action="{{ route('categories.destroy', ['category' => $category]) }}"
        class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-layouts::main-content>
