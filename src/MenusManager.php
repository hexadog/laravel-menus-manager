<?php

namespace Hexadog\MenusManager;

use Illuminate\Support\Collection;

class MenusManager
{
    /**
     * The menus collection.
     *
     * @var Collection
     */
    protected $menus;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->menus = collect();
    }

    /**
     * Magic method to manipulate menus Collection with ease.
     *
     * @param string $method_name
     * @param array  $args
     */
    public function __call($method_name, $args)
    {
        if (!method_exists($this, $method_name)) {
            return call_user_func_array([$this->menus, $method_name], $args);
        }
    }

    /**
     * Get all menus as array.
     */
    public function all(): array
    {
        return $this->menus->toArray();
    }

    /**
     * Register a menu or get the existing one.
     *
     * @param string $name
     */
    public function register($name): Menu
    {
        if (!$menu = $this->get($name)) {
            $menu = new Menu($name);

            $this->menus->put($name, $menu);
        }

        return $menu;
    }
}
