<?php

namespace App\View\Components\Orders;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FilterCard extends Component
{
    public array $listStatus = [
        null => 'All',
        'pending' => 'Pending',
        'closed' => 'Closed',
        'canceled' => 'Canceled',
    ];
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $filterAction,
        public string $resetUrl,
        public ?string $date = null,
        public ?string $status = null,
        public ?string $nif = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.orders.filter-card');
    }
}
