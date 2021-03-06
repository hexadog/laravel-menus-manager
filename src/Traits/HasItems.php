<?php

namespace Hexadog\MenusManager\Traits;

use Hexadog\MenusManager\Item;
use Illuminate\Support\Collection;

trait HasItems
{
    /**
     * The items collection
     *
     * @var Collection
     */
    protected $items;

    /**
     * Add new item
     *
     * @param array $attributes
     *
     * @return Item
     */
    public function add(array $attributes = []): Item
    {
        $this->items->push($item = new Item($attributes, $this));

        return $item;
    }

    /**
     * Add new divider menu item
     *
     * @param array $attributes
     *
     * @return Item
     */
    public function divider(array $attributes = []): Item
    {
        return $this->add(compact('attributes'))->asDivider();
    }

    /**
     * Find item by key and value
     *
     * @param string $key
     * @param string $value
     *
     * @return mixed
     */
    public function findBy(string $key, string $value): ?Item
    {
        return $this->items->filter(function ($item) use ($key, $value) {
            return $item->{$key} === $value;
        })->first();
    }

    /**
     * Find item by given title or add it
     *
     * @param string   $title
     * @param array    $attributes
     *
     * @return mixed
     */
    public function findByTitleOrAdd(string $title, array $attributes = []): ?Item
    {
        if (! ($item = $this->findBy('title', $title))) {
            $item = $this->add(compact('title', 'attributes'));
        }

        return $item;
    }

    /**
     * Add new header menu item
     *
     * @param string $title
     * @param array  $attributes
     *
     * @return Item
     */
    public function header(string $title, array $attributes = []): Item
    {
        return $this->add(compact('title', 'attributes'))->asHeader();
    }

    /**
     * Get items
     *
     * @return Collection
     */
    public function items()
    {
        return $this->items->sortBy(function ($item) {
            return $item->order;
        });
    }
    
    /**
     * Register new menu item using registered route
     *
     * @param mixed $route
     * @param string $title
     * @param array  $attributes
     *
     * @return Item
     */
    public function route($route, string $title, array $attributes = []): Item
    {
        return $this->add(compact('route', 'title', 'attributes'));
    }

    /**
     * Register new menu item using url
     *
     * @param string $url
     * @param string $title
     * @param array  $attributes
     *
     * @return Item
     */
    public function url(string $url, string $title, array $attributes = []): Item
    {
        return $this->add(compact('url', 'title', 'attributes'));
    }

    /**
     * Magic method to manipulate items Collection with ease
     *
     * @param string $method_name
     * @param array $args
     *
     * @return void
     */
    public function __call($method_name, $args)
    {
        if (! method_exists($this, $method_name)) {
            return call_user_func_array([$this->items, $method_name], $args);
        }
    }
}
