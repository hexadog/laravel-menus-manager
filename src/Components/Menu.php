<?php

namespace Hexadog\MenusManager\Components;

use Illuminate\View\Component;
use Illuminate\Support\Collection;
use Hexadog\MenusManager\Facades\Menus;

class Menu extends Component
{
    /**
     * The menu items
     *
     * @var Collection
     */
    public $items;
   
    /**
     * The menu
     *
     * @var string
     */
    public $menu;

    /**
     * Create the component instance
     *
     * @param  string  $type
     * @param  string  $message
     * @return void
     */
    public function __construct($name)
    {
        $this->menu = Menus::get($name);
        $this->items = $this->menu ? $this->menu->items() : collect();
    }

    /**
     * Get the view / contents that represents the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('menus-manager::components.menu');
    }
}
