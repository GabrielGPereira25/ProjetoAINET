<x-layouts::main-content :title="$price->name"
                         :heading="'Edit price '. $price->name"
                         subheading='Click on "Save" button to store the information.'>
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('prices.update', ['price' => $price]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mt-6 space-y-4">
                        @include('prices.partials.fields', ['mode' => 'edit'])
                    </div>
                    @include('partials.form-buttons', ['entity' => 'price', 'value' => $price,
                        'new' => false, 'show' => false, 'delete' => false, 'save' => true, 'cancel' => true])
                </form>
            </section>
        </div>
    </div>
</x-layouts::main-content>

