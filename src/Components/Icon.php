<?php

namespace Hexadog\MenusManager\Components;

use Hexadog\MenusManager\Item;
use Illuminate\View\Component;

class Icon extends Component
{
    /**
     * The item icon
     *
     * @var string
     */
    protected $icon;

    /**
     * The item
     *
     * @var Item
     */
    protected $item;

    /**
     * Create the component instance
     *
     * @param  Item  $item
     *
     * @return void
     */
    public function __construct(Item $item = null)
    {
        $this->item = $item;
        $this->icon = $item->hasIcon() ? $item->icon : null;
    }

    /**
     * Get the view / contents that represents the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        if ($this->icon) {
            return view('menus-manager::components.icon');
        }

        return '';
    }
}
