<?php

namespace Hexadog\MenusManager;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Hexadog\MenusManager\Traits\HasItems;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class Item implements Arrayable
{
    use HasItems;
    
    /**
     * Item parent
     *
     * @var mixed
     */
    protected $parent = null;

    /**
     * Item properties
     *
     * @var array
     */
    protected $properties = [
        'attributes'    => [],
        'icon'          => null,
        'order'         => 0,
        'route'         => null,
        'title'         => '',
        'type'          => 'link', // link | divider | header
        'url'           => null,
    ];

    /**
     * The hide callbacks collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $visibleCallbacks;
    
    /**
     * Constructor.
     *
     * @param array $properties
     * @param mixed $parent
     */
    public function __construct(array $properties = [], $parent = null)
    {
        $this->visibleCallbacks = collect();
        $this->items = collect();

        $this->parent = $parent;
        
        // Generate id attribute if not provided
        if (is_null(Arr::get($properties, 'attributes.id'))) {
            Arr::set($properties, 'attributes.id', str_replace('.', '', uniqid('id-', true)));
        }

        $this->fill($properties);
    }

    /**
     * Get item attribute
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'properties') {
            return $this->properties;
        }

        return Arr::get($this->properties, $key);
    }
    
    /**
     * Set item attribute
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        return Arr::set($this->properties, $key, $value);
    }
    
    /**
     * Set the current item as header
     *
     * @return Item
     */
    public function asHeader(): Item
    {
        return $this->fill([
            'type' => 'header'
        ]);
    }
    
    /**
     * Set the current item as divider
     *
     * @return Item
     */
    public function asDivider(): Item
    {
        return $this->fill([
            'type' => 'divider'
        ]);
    }
    
    /**
     * Get the curent item children
     *
     * @return Collection
     */
    public function children(): Collection
    {
        return $this->items->sortBy(function ($item) {
            return $item->order;
        });
    }

    /**
     * Fill the item properties
     *
     * @param array $properties
     *
     * @return Item
     */
    public function fill($properties): Item
    {
        $this->properties = array_merge($this->properties, $properties);

        return $this;
    }

    /**
     * Get the item attributes as HTML String
     *
     * @param mixed $except
     *
     * @return string
     */
    public function getAttributes($except = null)
    {
        return $this->htmlAttributes(Arr::except($this->attributes, $except));
    }

    /**
     * Get item url
     *
     * @return string
     */
    public function getUrl(): string
    {
        if ($this->route) {
            if (is_array($this->route)) {
                return URL::route(Arr::get($this->route, 0), Arr::get($this->route, 1, []));
            } elseif (is_string($this->route)) {
                return URL::route($this->route);
            }
        }

        if ($this->url) {
            if (is_array($this->route)) {
                return URL::to(Arr::get($this->url, 0), Arr::get($this->url, 1, []), true);
            } else {
                return URL::to($this->url, [], true);
            }
        }

        return '';
    }
    
    /**
     * Check if the current item has children
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->items->isNotEmpty();
    }

    /**
     * Check if icon is set for the current item
     *
     * @return boolean
     */
    public function hasIcon(): bool
    {
        return !is_null($this->icon);
    }

    /**
     * Check if item is active
     * If a child is active then item is active too
     */
    public function isActive()
    {
        if ($this->route) {
            if (is_array($this->route)) {
                return Route::is(Arr::get($this->route, 0));
            } elseif (is_string($this->route)) {
                return Route::is($this->route);
            }
        }

        if ($this->url) {
            return Request::is($this->url);
        }

        return $this->children()->contains(function ($child) {
            return $child->isActive();
        }) ?? false;
    }

    /**
     * Check if the current item is divider
     *
     * @return bool
     */
    public function isDivider(): bool
    {
        return $this->type === 'divider';
    }

    /**
     * Check if the current item is header
     *
     * @return bool
     */
    public function isHeader(): bool
    {
        return $this->type === 'header';
    }
    
    /**
     * Check if the current item is hidden
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return !$this->isVisible();
    }

    /**
     * Check if the current item is visible
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return (bool) $this->visibleCallbacks->every(function ($callback) {
            return call_user_func($callback);
        });
    }

    /**
     * Set the current item icon
     *
     * @param string $icon
     *
     * @return Item
     */
    public function icon(string $icon): Item
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set visible callback for current menu item
     *
     * @param mixed $callback
     *
     * @return Item
     */
    public function if($callback): Item
    {
        if (!is_callable($callback)) {
            $callback = function () use ($callback) {
                return $callback;
            };
        }
        
        $this->visibleCallbacks->push($callback);

        return $this;
    }

    /**
     * Set the current item order
     *
     * @param integer $order
     *
     * @return Item
     */
    public function order(int $order = 0): Item
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get Item parent
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * Search item by key and value recursively
     *
     * @param string $key
     * @param string $value
     * @param callable $callback
     *
     * @return mixed
     */
    public function searchBy($key, $value, callable $callback = null): ?Item
    {
        $matchItem = null;

        if ($this->{$key} === $value) {
            $matchItem = $this;
        } else {
            $this->items->each(function ($item) use (&$matchItem, $key, $value) {
                if ($foundItem = $item->findBy($key, $value)) {
                    $matchItem = $foundItem;
                }
            });
        }

        if (is_callable($callback) && $matchItem) {
            call_user_func($callback, $matchItem);
        }

        return $matchItem;
    }
    
    /**
     * Return attributes in html format
     *
     * @param  array $attributes
     *
     * @return string
     */
    private function htmlAttributes($attributes)
    {
        return new HtmlString(join(' ', array_map(function ($key) use ($attributes) {
            if (is_bool($attributes[$key])) {
                return $attributes[$key] ? $key : '';
            }
            return $key . '="' . $attributes[$key] . '"';
        }, array_keys($attributes))));
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'attributes' => $this->attributes,
            'active' => $this->isActive(),
            'children' => $this->hasChildren() ? $this->children()->toArray() : [],
            'icon' => $this->icon,
            'order' => $this->order,
            'title' => $this->title,
            'type' => $this->type,
            'url' => $this->getUrl()
        ];
    }
}
