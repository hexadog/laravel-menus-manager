<?php

namespace Hexadog\MenusManager;

use Hexadog\MenusManager\Traits\HasItems;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class Item implements Arrayable
{
    use HasItems;

    /**
     * Item parent.
     *
     * @var mixed
     */
    protected $parent;

    /**
     * Item properties.
     *
     * @var array
     */
    protected $properties = [
        'attributes' => [],
        'icon' => null,
        'order' => 0,
        'route' => null,
        'title' => '',
        'type' => 'link', // link | divider | header
        'url' => '#',
    ];

    /**
     * The hide callbacks collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $visibleCallbacks;

    /**
     * Constructor.
     *
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
     * Get item attribute.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if ('properties' === $key) {
            return $this->properties;
        }

        $value = Arr::get($this->properties, $key);

        if ($value instanceof \Closure) {
            $value = $value();
        }

        return $value;
    }

    /**
     * Set item attribute.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        return Arr::set($this->properties, $key, $value);
    }

    /**
     * Set the current item as header.
     */
    public function asHeader(): Item
    {
        return $this->fill([
            'type' => 'header',
        ]);
    }

    /**
     * Set the current item as divider.
     */
    public function asDivider(): Item
    {
        return $this->fill([
            'type' => 'divider',
        ]);
    }

    /**
     * Get the curent item children.
     */
    public function children(): Collection
    {
        return $this->items->sortBy(function ($item) {
            return $item->order;
        });
    }

    /**
     * Fill the item properties.
     *
     * @param array $properties
     */
    public function fill($properties): Item
    {
        $this->properties = array_merge($this->properties, $properties);

        return $this;
    }

    /**
     * Get the item attributes as HTML String.
     *
     * @param mixed $except
     *
     * @return string
     */
    public function getAttributes($except = null)
    {
        return $this->htmlAttributes(Arr::except($this->properties['attributes'], $except));
    }

    /**
     * Get item url.
     */
    public function getUrl(): string
    {
        if ($this->route) {
            if (is_array($this->route)) {
                return URL::route(Arr::get($this->route, 0), Arr::get($this->route, 1, []));
            }

            if (is_string($this->route)) {
                return URL::route($this->route);
            }
        }

        if ($this->url) {
            if (is_array($this->route)) {
                return URL::to(Arr::get($this->url, 0), Arr::get($this->url, 1, []), true);
            }

            return URL::to($this->url, [], true);
        }

        return '#';
    }

    /**
     * Check if the current item has children.
     */
    public function hasChildren(): bool
    {
        return $this->items->isNotEmpty();
    }

    /**
     * Check if icon is set for the current item.
     */
    public function hasIcon(): bool
    {
        return !is_null($this->icon);
    }

    /**
     * Check if item is active
     * If a child is active then item is active too.
     */
    public function isActive()
    {
        // Check if one of the children is active
        foreach ($this->children() as $child) {
            if ($child->isActive()) {
                return true;
            }
        }

        // Custom set active path
        if ($path = $this->getActiveWhen()) {
            return Request::is($path);
        }

        $path = ltrim(str_replace(url('/'), '', $this->getUrl()), '/');

        return Request::is(
            $path,
            $path . '/*'
        );
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function isActiveWhen($path)
    {
        // Remove unwanted chars
        $path = ltrim($path, '/');
        $path = rtrim($path, '/');
        $path = rtrim($path, '?');

        $this->activeWhen = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getActiveWhen()
    {
        return $this->activeWhen;
    }

    /**
     * Check if the current item is divider.
     */
    public function isDivider(): bool
    {
        return 'divider' === $this->type;
    }

    /**
     * Check if the current item is header.
     */
    public function isHeader(): bool
    {
        return 'header' === $this->type;
    }

    /**
     * Check if the current item is hidden.
     */
    public function isHidden(): bool
    {
        return !$this->isVisible();
    }

    /**
     * Check if the current item is visible.
     */
    public function isVisible(): bool
    {
        return (bool) $this->visibleCallbacks->every(function ($callback) {
            return call_user_func($callback);
        });
    }

    /**
     * Set the current item icon.
     */
    public function icon(string $icon): Item
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set visible callback for current menu item.
     *
     * @param mixed $callback
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
     * Set the current item order.
     */
    public function order(int $order = 0): Item
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get Item parent.
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * Search item by key and value recursively.
     *
     * @param string $key
     * @param string $value
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
            'url' => $this->getUrl(),
        ];
    }

    /**
     * Return attributes in html format.
     *
     * @param array $attributes
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
}
