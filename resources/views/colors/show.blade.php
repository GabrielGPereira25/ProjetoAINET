<x-layouts::main-content :title="$color->name"
                        :heading="'color '. $color->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <div class="mt-6 space-y-4">
                    @include('colors.partials.fields', ['mode' => 'show'])
                </div>
                @include('partials.form-buttons', ['entity' => 'color', 'value' => $color, 'new' => true, 'edit' => true, 'delete' => true])
            </section>
        </div>
    </div>
    <form id="delete-form" method="POST" action="{{ route('colors.destroy', ['color' => $color]) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-layouts::main-content>
