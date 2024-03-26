<?php

namespace Hexadog\MenusManager\Components;

use Hexadog\MenusManager\Facades\Menus;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Menu extends Component
{
    /**
     * The menu items.
     *
     * @var Collection
     */
    public $items;

    /**
     * The menu.
     *
     * @var string
     */
    public $menu;

    /**
     * Create the component instance.
     *
     * @param mixed $name
     */
    public function __construct($name)
    {
        $this->menu = Menus::get($name);
        $this->items = $this->menu ? $this->menu->items() : collect();
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Closure|Htmlable|string|View
     */
    public function render()
    {
        if ($this->menu) {
            return view('menus-manager::components.menu');
        }

        return '';
    }
}
