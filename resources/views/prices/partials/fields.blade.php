@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp


<div class="flex flex-col sm:flex-row sm:justify-between space-x-8">
    <div class="grow mt-6 space-y-4">
        <div class="w-full flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <flux:input name="unit_price_catalog" label="Unit Price Catalog" value="{{ old('unit_price_catalog', $price->unit_price_catalog) }}"
                :disabled="$readonly" />
            <flux:input name="unit_price_own" label="Unit Price Own" value="{{ old('unit_price_own', $price->unit_price_own) }}"
                :disabled="$readonly" />
        </div>
        <div class="w-full flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <flux:input name="unit_price_catalog_discount" label="Unit Price Catalog Discount" value="{{ old('unit_price_catalog_discount', $price->unit_price_catalog_discount) }}"
                :disabled="$readonly" />
            <flux:input name="unit_price_own_discount" label="Unit Price Own Discount" value="{{ old('unit_price_own_discount', $price->unit_price_own_discount) }}"
                :disabled="$readonly" />
        </div>
        <div class="w-full flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <flux:input name="qty_discount" label="Quantity Discount" value="{{ old('qty_discount', $price->qty_discount) }}"
                :disabled="$readonly" />
        </div>
      </div>
</div>
