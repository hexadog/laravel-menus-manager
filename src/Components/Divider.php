<?php

namespace Hexadog\MenusManager\Components;

use Hexadog\MenusManager\Item;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Divider extends Component
{
    /**
     * The item.
     *
     * @var Item
     */
    public $item;

    /**
     * Create the component instance.
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Closure|Htmlable|string|View
     */
    public function render()
    {
        if ($this->item && $this->item->isVisible()) {
            return view('menus-manager::components.divider');
        }

        return '';
    }
}
