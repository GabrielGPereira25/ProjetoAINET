@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp


<div class="flex flex-col sm:flex-row sm:justify-between space-x-8">
    <div class="grow mt-6 space-y-4">
        <div class="w-full flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <div class="w-full sm:w-64">
                <flux:input name="code" label="Code" value="{{ old('code', $color->code) }}" :disabled="$readonly"
                    :readonly="$mode == 'edit'" />
            </div>
        </div>

        <flux:input name="name" label="Name" value="{{ old('name', $color->name) }}" :disabled="$readonly" />
    </div>
</div>
