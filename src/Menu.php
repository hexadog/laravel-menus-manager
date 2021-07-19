<?php

namespace Hexadog\MenusManager;

use Hexadog\MenusManager\Traits\HasItems;
use Illuminate\Contracts\Support\Arrayable;

class Menu implements Arrayable
{
    use HasItems;

    /**
     * The menu name.
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new Menu instance.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->items = collect();
    }

    /**
     * Search item by key and value recursively.
     *
     * @param string   $key
     * @param string   $value
     * @param callable $callback
     *
     * @return mixed
     */
    public function searchBy($key, $value, callable $callback = null): ?Item
    {
        $matchItem = null;

        $this->items->first(function ($item) use (&$matchItem, $key, $value) {
            if ($foundItem = $item->findBy($key, $value)) {
                $matchItem = $foundItem;
            }
        });

        if (is_callable($callback) && $matchItem) {
            call_user_func($callback, $matchItem);
        }

        return $matchItem;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'items' => $this->items->toArray(),
        ];
    }
}
