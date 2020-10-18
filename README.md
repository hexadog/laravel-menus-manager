<p align="center"><img src="https://i.ibb.co/31yzFSw/logo.png"></p>

<p align="center">
    <a href="https://packagist.org/packages/hexadog/laravel-menus-manager">
        <img src="https://poser.pugx.org/hexadog/laravel-menus-manager/v" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/hexadog/laravel-menus-manager">
        <img src="https://poser.pugx.org/hexadog/laravel-menus-manager/downloads" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/hexadog/laravel-themes-manager">
        <img src="https://poser.pugx.org/hexadog/laravel-menus-manager/license" alt="License">
    </a>
</p>

<!-- omit in toc -->
## Introduction
<code>hexadog/laravel-menus-manager</code> is a Laravel package to ease dynamic menus management.

<!-- omit in toc -->
## Installation
This package requires PHP 7.3 and Laravel 7.0 or higher.

To get started, install Menus Manager using Composer:
```shell
composer require hexadog/laravel-menus-manager
```

The package will automatically register its service provider.

<!-- omit in toc -->
## Usage
Menus Manager has many features to help you working with dynamic menus

- [Create Menu](#create-menu)
- [Menu hierarchy](#menu-hierarchy)
- [Menu Item](#menu-item)
  - [Item Types](#item-types)
    - [Route item](#route-item)
    - [Url item](#url-item)
    - [Divider item](#divider-item)
    - [Header item](#header-item)
  - [Item Icon](#item-icon)
  - [Item Order](#item-order)
  - [Item Visibility](#item-visibility)
  - [Active state](#active-state)
- [Search for item](#search-for-item)
- [Menu Tree](#menu-tree)
- [Blade components](#blade-components)

### Create Menu
Use the provided `Menus` facade with `register` method to create a new menu.
_**notice:** if menu with given name already exists it will be returned - this way you can access to any existing menu anywhere in your application_
```php
// Create new "main" Menu
$menu = Menus::register('main');
```

You can now access to the menu any time using:
```php
// Retreive "main" Menu
$menu = Menus::get('main');
```

And get all items for the Menu
```php
// Get all items from Menu
$items = $menu->items();
```
You can get a specific item by using search methods. See [Search for item](#search-for-item).

### Menu hierarchy
Menus Manager lets you create multi-level menus. Each Menu item can have as many children as you want. See [Menu Item](#menu-item) to find out how to create a new Menu Item.

You can easily retreive declared children using:
```php
// Check if item has children
if ($menuItem->hasChildren()) {
    // Get all item children as Collection
    $items = $menuItem->children();

    // Loop into items
    foreach($items as $item) {
        // Get item parent
        $parent = $item->parent();

        // ...
    }
}
```

### Menu Item
An item can be added at any level: the menu itself or any child item.
```php
// Create a new menu
$menu = Menus::register('main');

// Add a first-level item
$menu->route('index', 'Home');

// Create a first-level item with children
$menuItem = $menu->header('Our packages');
$menuItem->url('https://github.com/hexadog/laravel-menus-manager', 'Laravel Menus Manager')->order(1);
$menuItem->url('https://github.com/hexadog/laravel-themes-manager', 'Laravel Themes Manager')->order(3);
$menuItem->url('https://github.com/hexadog/laravel-theme-installer', 'Laravel Theme Installer')->order(2);

// Create first-level items with visibility condition
$menu->route('profile.show', __('Profile'))->if(Auth()->check());
$menu->route('login', __('Login'))->if(!Auth()->check());
```

You can access to the generated item url with `getUrl()` method on any item. 
```php
$menuItem->getUrl();
```

#### Item Types
Menus Manager handle multiple item types: Route item, URL item, Header item and Divider item

##### Route item
Add a menu item for a route by passing the route name and a title
```php
$menuItem = $menu->route('index', 'Home');
```

You can pass parameters to the route by passing an array instead of a string as first parameter
```php
$menuItem = $menu->route(['index', ['type' => 'anonymous']], 'Home');
```

##### Url item
Add a menu item for an URL with the given title
```php
$menuItem = $menu->url('https://hexadog.com', 'hexadog');
```

##### Divider item
A simple divider: no action available. No title required for this type of item.
```php
$menuItem = $menu->divider();
```

##### Header item
Add header item: no action available. Mainly used to visually group sub-menus
```php
$menuItem = $menu->header('General');
```

#### Item Icon
You can add an icon to your menu item by calling `icon()` method with the icon classes as parameter.
```php
$menu->route('index', 'Home')->icon('fas fa-home');
```

#### Item Order
By default items are displayed in order they are created. You can specify item order to organize your menu entries:
```php
$menu->route('index', 'Home')->order(1);
$menu->route('contact', 'Contact')->order(2);
```

#### Item Visibility
```php
$menuItem->isVisible();
```

You can condition item visibility by using `if()` method.
You can chain conditions. This way each condition must be filled to make the item visible.
```php
$menu->route('profile.show', __('Profile'))->if(Auth()->check());
$menu->route('login', __('Login'))->if(!Auth()->check());

$menu->route('post.create', __('New Post'))->if(Auth()->check())->if(Auth()->user()->can('create.post'));
```

#### Active state
Check if current item is active or has an active child.
Depending on the item type, active state is determined using `Request::is()` or `Route::is()` Laravel methods. 
```php
$menuItem->isActive();
```

### Search for item
Search an item recursively (in all hierarchy).
```php
$menu->search('title', 'Home'); // Return the found item or null
```

or search in first level item children
```php
$menu->findBy('title', 'Home');
```

You can search an item by title and add it if not found in one line with `findByTitleOrAdd()` helper method.
```php
$menu->findByTitleOrAdd('title');
```

### Menu Tree
Menu and Item implement `Illuminate\Contracts\Support\Arrayable` interface. Calling `toArray()` method on the follwing menu:
```php
// Create a new menu
$menu = Menus::register('main');

// Add a first-level item
$menu->route('index', 'Home');

// Create a first-level item with children
$menuItem = $menu->header('Our packages');
$menuItem->url('https://github.com/hexadog/laravel-menus-manager', 'Laravel Menus Manager')->order(1);
$menuItem->url('https://github.com/hexadog/laravel-themes-manager', 'Laravel Themes Manager')->order(3);
$menuItem->url('https://github.com/hexadog/laravel-theme-installer', 'Laravel Theme Installer')->order(2);

// Create first-level items with visibility condition
$menu->route('profile.show', __('Profile'))->if(Auth()->check());
$menu->route('login', __('Login'))->if(!Auth()->check());

// Get Menu Tree
$menu->toArray();
```

Returns an array of menu content
```php
[
    "name" => "main",
    "items" => [
        0 => [
            "attributes" => [
            "id" => "id-5f8c4a3d803dd817648152"
            ],
            "active" => true,
            "children" => [],
            "icon" => null,
            "order" => 0,
            "title" => "Home",
            "type" => "link",
            "url" => "http://127.0.0.1:8000",
        ],
        1 => [
            "attributes" => [
                "id" => "id-5f8c4a3d8045f366812051"
            ],
            "active" => false,
            "children" => [
                0 => [
                    "attributes" => [
                    "id" => "id-5f8c4a3d80476901878768"
                    ],
                    "active" => false,
                    "children" => [],
                    "icon" => null,
                    "order" => 1,
                    "title" => "Laravel Menus Manager",
                    "type" => "link",
                    "url" => "https://github.com/hexadog/laravel-menus-manager",
                ],
                2 => [
                    "attributes" => [
                    "id" => "id-5f8c4a3d80496954609369"
                    ],
                    "active" => false,
                    "children" => [],
                    "icon" => null,
                    "order" => 2,
                    "title" => "Laravel Theme Installer",
                    "type" => "link",
                    "url" => "https://github.com/hexadog/laravel-theme-installer",
                ],
                1 => [
                    "attributes" => [
                    "id" => "id-5f8c4a3d8048e808061014"
                    ],
                    "active" => false,
                    "children" => [],
                    "icon" => null,
                    "order" => 3,
                    "title" => "Laravel Themes Manager",
                    "type" => "link",
                    "url" => "https://github.com/hexadog/laravel-themes-manager",
                ]
            ],
            "icon" => null,
            "order" => 0,
            "title" => "Our packages",
            "type" => "header",
            "url" => "",
        ]
    ]
]
```

### Blade components
Menus Manager provides blade components to ease integration in your designs.

You just have to pass your Menu name to get full render.
```php
<x-menus-menu name="main" />
```
It uses `x-menus-children`, `x-menus-divider`, `x-menus-header`, `x-menus-icon` and `x-menus-item` dedicated components. By default Menus Manager will scaffold your application's integration with the [Tailwind CSS](https://tailwindcss.com/) and [Alpine.js](https://github.com/alpinejs/alpine).

You can customize component's views to fit to your need by publishing them into your application resources:
```shell
php artisan vendor:publish --provider="Hexadog\MenusManager\Providers\PackageServiceProvider"
```

<!-- omit in toc -->
## Credits
- Logo made by [DesignEvo free logo creator](https://www.designevo.com/logo-maker/)

<!-- omit in toc -->
## License
Laravel Menus Manager is open-sourced software licensed under the [MIT license](LICENSE).
