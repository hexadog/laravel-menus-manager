<?php

namespace Hexadog\MenusManager\Components;

use Hexadog\MenusManager\Item;
use Illuminate\View\Component;

class Icon extends Component
{
    /**
     * The item icon.
     *
     * @var string
     */
    public $icon;

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
        $this->icon = $item && $item->hasIcon() ? $item->icon : null;
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function render()
    {
        if ($this->item && $this->icon) {
            return view('menus-manager::components.icon');
        }

        return '';
    }
}
