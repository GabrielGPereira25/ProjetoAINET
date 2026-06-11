<div {{ $attributes }}>
    <form method="GET" action="{{ $filterAction }}">
        <div class="flex justify-between space-x-3">
            <div class="grow flex flex-col space-y-2">
                <div>
                    <flux:input nif="nif" label="NIF" class="grow" value="{{ $nif }}"/>
                </div>
                <div>
                    <flux:input name="date" label="Date" class="grow" value="{{ $date }}"/>
                </div>
                <div>
                    <flux:select name="status" label="Status" class="grow" :options="$listStatus" value="{{ $status }}"/>
                </div>
            </div>
            <div class="grow-0 flex flex-col space-y-3 justify-start">
                <div class="pt-6">
                    <flux:button variant="filled" type="submit" class="w-full">Filter</flux:button>
                </div>
                <div>
                    <flux:button variant="subtle" :href="$resetUrl"  class="w-full">Cancel</flux:button>
                </div>
            </div>
        </div>
    </form>
</div>
