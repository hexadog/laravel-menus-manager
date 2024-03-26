<?php

namespace Hexadog\MenusManager\Components;

use Hexadog\MenusManager\Item as MenusManagerItem;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Item extends Component
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
    public function __construct(MenusManagerItem $item)
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
            if ($this->item->isHeader()) {
                return view('menus-manager::components.header');
            }

            if ($this->item->isDivider()) {
                return view('menus-manager::components.divider');
            }

            return view('menus-manager::components.item');
        }

        return '';
    }
}
