<?php

namespace Hexadog\MenusManager\Components;

use Hexadog\MenusManager\Item;
use Illuminate\View\Component;

class Divider extends Component
{
    /**
     * The item
     *
     * @var Item
     */
    public $item;

    /**
     * Create the component instance
     *
     * @param  Item  $item
     *
     * @return void
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get the view / contents that represents the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        if ($this->item->isVisible()) {
            return view('menus-manager::components.divider');
        }

        return '';
    }
}
