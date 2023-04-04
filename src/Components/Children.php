<?php

namespace Hexadog\MenusManager\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Children extends Component
{
    /**
     * The item items.
     *
     * @var Collection
     */
    public $items;

    /**
     * Create the component instance.
     */
    public function __construct(Collection $items)
    {
        $this->items = $items ?? collect();
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Closure|\Illuminate\Contracts\Support\Htmlable|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if ($this->items->count()) {
            return view('menus-manager::components.children');
        }

        return '';
    }
}
