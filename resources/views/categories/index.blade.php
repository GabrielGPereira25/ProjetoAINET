<x-layouts::main-content :title="__('Category')"
                        heading="List of categories"
                        subheading="Manage the categories offered by the institution">
  <div class="flex w-full flex-1 flex-col gap-4 rounded-xl ">
    <div class="flex justify-start ">
      <div class="my-4 p-6 ">
        <div class="flex items-center gap-4 mb-4">
          <flux:button variant="primary" href="{{ route('categories.create') }}">Create a new category</flux:button>
        </div>
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
            <x-categories.filter-card :filterAction="route('categories.index')" :name="$filterByName" :resetUrl="route('categories.index')" />
            <x-categories.table :categories="$categories"
                                    :showView="true"
                                    :showEdit="true"
                                    :showDelete="true"
            />
        </div>
        <div class="mt-4">
          {{ $categories->links() }}
        </div>
      </div>
    </div>
  </div>
</x-layouts::main-content>
