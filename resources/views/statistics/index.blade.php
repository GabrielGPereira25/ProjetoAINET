<x-layouts::main-content :title="__('Statistics')"
                        heading="Business Statistics"
                        subheading="">
  <div class="flex w-full flex-1 flex-col gap-6 rounded-xl">

    {{-- ===== KPI CARDS (TOP ROW) ===== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

      {{-- Total Revenue --}}
      <div class="rounded-xl border border-emerald-500/30 bg-emerald-950/40 p-5">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/20">
            <flux:icon.banknotes class="size-5 text-emerald-400" />
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">Total Revenue</p>
            <p class="text-2xl font-bold text-emerald-400">{{ number_format($totalRevenue, 2) }}€</p>
          </div>
        </div>
        <p class="mt-2 text-xs text-zinc-500">{{ $totalClosedOrders }} completed orders</p>
      </div>

      {{-- Average Order Value --}}
      <div class="rounded-xl border border-blue-500/30 bg-blue-950/40 p-5">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500/20">
            <flux:icon.calculator class="size-5 text-blue-400" />
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">Avg. Order Value</p>
            <p class="text-2xl font-bold text-blue-400">{{ number_format($avgOrderValue ?? 0, 2) }}€</p>
          </div>
        </div>
        <p class="mt-2 text-xs text-zinc-500">Min: {{ number_format($minOrderValue ?? 0, 2) }}€ · Max: {{ number_format($maxOrderValue ?? 0, 2) }}€</p>
      </div>

      {{-- Total Items Sold --}}
      <div class="rounded-xl border border-violet-500/30 bg-violet-950/40 p-5">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-500/20">
            <flux:icon.shopping-bag class="size-5 text-violet-400" />
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">Items Sold</p>
            <p class="text-2xl font-bold text-violet-400">{{ number_format($totalItemsSold) }}</p>
          </div>
        </div>
        <p class="mt-2 text-xs text-zinc-500">Across all completed orders</p>
      </div>

      {{-- Total Customers --}}
      <div class="rounded-xl border border-amber-500/30 bg-amber-950/40 p-5">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/20">
            <flux:icon.users class="size-5 text-amber-400" />
          </div>
          <div>
            <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">Total Customers</p>
            <p class="text-2xl font-bold text-amber-400">{{ number_format($totalCustomers) }}</p>
          </div>
        </div>
        <p class="mt-2 text-xs text-zinc-500">{{ $totalUsers }} total users</p>
      </div>

    </div>

    {{-- ===== ABSOLUTE TOTALS ROW ===== --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
      @php
        $absoluteTotals = [
          ['label' => 'Categories', 'value' => $totalCategories, 'color' => 'text-sky-400'],
          ['label' => 'Colors', 'value' => $totalColors, 'color' => 'text-pink-400'],
          ['label' => 'Users', 'value' => $totalUsers, 'color' => 'text-indigo-400'],
          ['label' => 'Customers', 'value' => $totalCustomers, 'color' => 'text-amber-400'],
          ['label' => 'T-shirt Images', 'value' => $totalTshirtImages, 'color' => 'text-teal-400'],
          ['label' => 'Orders', 'value' => $totalOrders, 'color' => 'text-rose-400'],
        ];
      @endphp
      @foreach ($absoluteTotals as $item)
        <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-4 text-center">
          <p class="text-2xl font-bold {{ $item['color'] }}">{{ $item['value'] }}</p>
          <p class="mt-1 text-xs text-zinc-400">{{ $item['label'] }}</p>
        </div>
      @endforeach
    </div>

    {{-- ===== TWO COLUMN GRID ===== --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

      {{-- Users by Type + Orders by Status --}}
      <div class="flex flex-col gap-6">

        {{-- Users by Type --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-5">
          <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-300">Users by Type</h3>
          <div class="space-y-3">
            @php
              $typeLabels = ['A' => 'Administrators', 'F' => 'Employees', 'C' => 'Customers'];
              $typeColors = ['A' => 'bg-red-500', 'F' => 'bg-blue-500', 'C' => 'bg-emerald-500'];
              $typeBadge = ['A' => 'danger', 'F' => 'warning', 'C' => 'success'];
              $maxUsers = max($usersByType ?: [1]);
            @endphp
            @foreach (['A', 'F', 'C'] as $type)
              @php $count = $usersByType[$type] ?? 0; @endphp
              <div>
                <div class="mb-1 flex items-center justify-between">
                  <span class="text-sm text-zinc-300">{{ $typeLabels[$type] }}</span>
                  <flux:badge size="sm" variant="pill" color="{{ $typeBadge[$type] }}">{{ $count }}</flux:badge>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-700">
                  <div class="h-full rounded-full {{ $typeColors[$type] }} transition-all duration-500" style="width: {{ $maxUsers > 0 ? ($count / $maxUsers * 100) : 0 }}%"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Orders by Status --}}
        <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-5">
          <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-300">Orders by Status</h3>
          <div class="space-y-3">
            @php
              $statusLabels = ['pending' => 'Pending', 'closed' => 'Closed', 'canceled' => 'Canceled'];
              $statusColors = ['pending' => 'bg-amber-500', 'closed' => 'bg-emerald-500', 'canceled' => 'bg-red-500'];
              $statusBadge = ['pending' => 'warning', 'closed' => 'success', 'canceled' => 'danger'];
              $maxOrders = max($ordersByStatus ?: [1]);
            @endphp
            @foreach (['pending', 'closed', 'canceled'] as $status)
              @php $count = $ordersByStatus[$status] ?? 0; @endphp
              <div>
                <div class="mb-1 flex items-center justify-between">
                  <span class="text-sm text-zinc-300">{{ $statusLabels[$status] }}</span>
                  <flux:badge size="sm" variant="pill" color="{{ $statusBadge[$status] }}">{{ $count }}</flux:badge>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-700">
                  <div class="h-full rounded-full {{ $statusColors[$status] }} transition-all duration-500" style="width: {{ $maxOrders > 0 ? ($count / $maxOrders * 100) : 0 }}%"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

      </div>

      {{-- T-shirts by Category --}}
      <div class="flex flex-col gap-6">

        <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-5">
          <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-300">T-shirts by Category</h3>
          <div class="space-y-3">
            @php
              $maxTshirts = $tshirtsByCategory->max('tshirts_images_count') ?: 1;
            @endphp
            @foreach ($tshirtsByCategory as $category)
              <div>
                <div class="mb-1 flex items-center justify-between">
                  <span class="text-sm text-zinc-300">{{ $category->name }}</span>
                  <span class="text-sm font-semibold text-teal-400">{{ $category->tshirts_images_count }}</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-700">
                  <div class="h-full rounded-full bg-teal-500 transition-all duration-500" style="width: {{ ($category->tshirts_images_count / $maxTshirts * 100) }}%"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

      </div>

    </div>

    {{-- ===== MONTHLY SALES ===== --}}
    <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-5">
      <div class="mb-4 flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
        <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-300">Monthly Sales</h3>
        <form method="GET" action="{{ route('statistics.index') }}" class="flex items-center gap-2">
          <select name="year" onchange="this.form.submit()" class="rounded-lg border border-zinc-600 bg-zinc-700 px-3 py-1.5 text-sm text-zinc-200 focus:border-blue-500 focus:outline-none">
            @foreach ($availableYears as $year)
              <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
          </select>
        </form>
      </div>

      {{-- Bar Chart --}}
      <div class="mt-4 flex h-[200px] items-end gap-2">
        @php
          $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        @endphp
        @for ($m = 1; $m <= 12; $m++)
          @php
            $data = $monthlyData[$m];
            $percentage = $maxMonthlyRevenue > 0 ? ($data['revenue'] / $maxMonthlyRevenue * 100) : 0;
            $barHeight = max($percentage, 2); 
          @endphp
          
          <div class="group relative flex h-full flex-1 flex-col items-center justify-end">
            {{-- Tooltip --}}
            <div class="pointer-events-none absolute -top-12 z-10 hidden rounded-lg border border-zinc-600 bg-zinc-800 px-2 py-1 text-center shadow-lg group-hover:block">
              <p class="whitespace-nowrap text-xs font-semibold text-emerald-400">{{ number_format($data['revenue'], 2) }}€</p>
              <p class="whitespace-nowrap text-xs text-zinc-400">{{ $data['orders'] }} orders</p>
            </div>
            
            <div class="flex w-full flex-1 items-end">
              <div class="w-full rounded-t-md bg-emerald-500/80 transition-all duration-300 hover:bg-emerald-400" style="height: {{ $barHeight }}%"></div>
            </div>
            
            <span class="mt-2 text-xs text-zinc-500">{{ $monthNames[$m - 1] }}</span>
          </div>
        @endfor
      </div>

    {{-- BOTTOM: TOP CUSTOMERS + TOP IMAGES --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

      {{-- Top 5 Customers --}}
      <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-5">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-300">Top 5 Customers</h3>
        @if($topCustomers->isEmpty())
          <p class="text-sm text-zinc-500">No data available.</p>
        @else
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-zinc-700">
                  <th class="pb-2 text-left font-medium text-zinc-400">#</th>
                  <th class="pb-2 text-left font-medium text-zinc-400">Customer</th>
                  <th class="pb-2 text-right font-medium text-zinc-400">Orders</th>
                  <th class="pb-2 text-right font-medium text-zinc-400">Total Spent</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($topCustomers as $index => $customer)
                  <tr class="border-b border-zinc-700/50">
                    <td class="py-2 text-zinc-500">{{ $index + 1 }}</td>
                    <td class="py-2 text-zinc-200">{{ $customer->user->name ?? 'N/A' }}</td>
                    <td class="py-2 text-right text-zinc-300">{{ $customer->total_orders }}</td>
                    <td class="py-2 text-right font-semibold text-emerald-400">{{ number_format($customer->total_spent, 2) }}€</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      {{-- Top 5 Images --}}
      <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-5">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-zinc-300">Top 5 Best-Selling Images</h3>
        @if($topImages->isEmpty())
          <p class="text-sm text-zinc-500">No data available.</p>
        @else
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-zinc-700">
                  <th class="pb-2 text-left font-medium text-zinc-400">#</th>
                  <th class="pb-2 text-left font-medium text-zinc-400">Image</th>
                  <th class="pb-2 text-right font-medium text-zinc-400">Units Sold</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($topImages as $index => $image)
                  <tr class="border-b border-zinc-700/50">
                    <td class="py-2 text-zinc-500">{{ $index + 1 }}</td>
                    <td class="py-2 text-zinc-200">{{ $image->name }}</td>
                    <td class="py-2 text-right font-semibold text-violet-400">{{ number_format($image->total_sold) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

    </div>

  </div>
</x-layouts::main-content>
