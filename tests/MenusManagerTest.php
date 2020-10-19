<?php

namespace Hexadog\MenusManager\Tests;

use Hexadog\MenusManager\Facades\Menus;
use Hexadog\MenusManager\Item;
use Hexadog\MenusManager\Menu;
use Illuminate\Support\Arr;

class MenusManagerTest extends TestCase
{
    /**
     * Main menu instance
     *
     * @var \Hexadog\MenusManager\Menu
     */
    protected $menu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menu = Menus::register('main');
    }

    /** @test */
    public function it_makes_a_menu()
    {
        self::assertInstanceOf(Menu::class, $this->menu);
    }

    /** @test */
    public function it_makes_multiple_menu()
    {
        Menus::register('secondary');

        self::assertEquals(2, Menus::count());
    }

    /** @test */
    public function it_makes_a_menu_singleton()
    {
        $menu2 = Menus::register('main');

        self::assertEquals($this->menu, $menu2);
    }

    /** @test */
    public function it_makes_an_empty_menu_item()
    {
        $menuItem = new Item();

        self::assertInstanceOf(Item::class, $menuItem);
    }

    /** @test */
    public function it_makes_a_menu_item()
    {
        $menuItem = $this->menu->url('https://hexadog.com', 'hexadog');

        self::assertInstanceOf(Item::class, $menuItem);
    }

    /** @test */
    public function it_makes_a_menu_item_as_header()
    {
        $menuItem = $this->menu->header('hexadog');

        self::assertEquals('header', $menuItem->type);
        self::assertEquals('hexadog', $menuItem->title);
    }

    /** @test */
    public function it_makes_a_menu_item_as_divider()
    {
        $menuItem = $this->menu->divider();

        self::assertEquals('divider', $menuItem->type);
    }
    
    /** @test */
    public function it_makes_a_menu_item_with_properties()
    {
        $properties = [
            'attributes' => [],
            'icon' => '<i class="fas fa-home"></i>',
            'order' => 1,
            'route' => 'index',
            'title' => 'hexadog',
            'type' => 'header',
            'url' => 'https://hexadog.com',
        ];

        $menuItem = new Item($properties);

        self::assertEquals($properties, Arr::except($menuItem->properties, 'attributes.id'));
    }

    /** @test */
    public function it_can_get_item_attributes()
    {
        $menuItem = new Item();

        self::assertNotNull($menuItem->attributes);
    }

    /** @test */
    public function it_set_default_attribute_id()
    {
        $menuItem = new Item();

        self::assertNotNull($menuItem->attributes['id']);
    }

    /** @test */
    public function it_can_get_item_attributes_as_html_string()
    {
        $menuItem = new Item([
            'attributes' => [
                'class' => 'link'
            ]
        ]);

        self::assertNotNull($menuItem->getAttributes());
        self::assertEquals('class="link"', $menuItem->getAttributes('id'));
    }

    /** @test */
    public function it_can_set_icon()
    {
        $menuItem = (new Item())->icon('<i class="fas fa-user"></i>');

        self::assertEquals('<i class="fas fa-user"></i>', $menuItem->icon);
    }
    
    /** @test */
    public function it_can_set_route()
    {
        $menuItem = new Item();
        $menuItem->route = 'index';

        self::assertEquals('index', $menuItem->route);
    }
    
    /** @test */
    public function it_can_set_url()
    {
        $menuItem = new Item();
        $menuItem->url = 'https://hexadog.com';

        self::assertEquals('https://hexadog.com', $menuItem->url);
    }
    
    /** @test */
    public function it_can_set_order()
    {
        $menuItem = (new Item())->order(2);

        self::assertEquals(2, $menuItem->order);
    }
    
    /** @test */
    public function it_can_add_multiple_items()
    {
        $this->menu->route('index', 'Home');
        $this->menu->url('https://hexadog.com', 'hexadog');

        self::assertEquals(2, $this->menu->items()->count());
    }
    
    /** @test */
    public function it_can_add_item_children()
    {
        $menuItem = $this->menu->route('index', 'Home');
        $menuItem->url('https://hexadog.com', 'hexadog');

        self::assertEquals(1, $menuItem->children()->count());
    }
    
    /** @test */
    public function it_can_add_order_items()
    {
        $this->menu->route('index', 'Home')->order(3);
        $this->menu->url('https://hexadog.com', 'hexadog')->order(1);
        $this->menu->url('https://laravel.com', 'laravel')->order(2);

        $items = $this->menu->items();

        self::assertEquals('hexadog', $items->first()->title);
        self::assertEquals('laravel', $items->get(2)->title);
        self::assertEquals('Home', $items->last()->title);
    }

    /** @test */
    public function it_can_get_the_correct_url_for_url_type()
    {
        $menuItem = $this->menu->url('https://hexadog.com', 'hexadog');

        self::assertEquals('https://hexadog.com', $menuItem->getUrl());
    }

    /** @test */
    public function it_can_get_the_correct_url_for_route_type()
    {
        $this->app['router']->get('/', ['as' => 'index']);
        $this->app['router']->get('/contact', ['as' => 'contact']);

        $menuItem = $this->menu->route('index', 'Home');
        $menuItem2 = $this->menu->route('contact', 'Contact');
        $menuItem3 = $this->menu->route(['contact', ['type' => 'support']], 'Contact');

        self::assertEquals('http://localhost', $menuItem->getUrl());
        self::assertEquals('http://localhost/contact', $menuItem2->getUrl());
        self::assertEquals('http://localhost/contact?type=support', $menuItem3->getUrl());
    }

    /** @test */
    public function it_can_get_item_as_array()
    {
        $menuItem = new Item();
        $itemAsArray = [
            'attributes' => $menuItem->attributes,
            'active' => false,
            'children' => [],
            'icon' => '',
            'order' => 0,
            'title' => '',
            'type' => 'link',
            'url' => ''
        ];

        self::assertEquals($itemAsArray, $menuItem->toArray());
    }

    /** @test */
    public function it_can_get_menu_as_array()
    {
        $menuItem = $this->menu->add();
        $itemAsArray = [
            'name' => 'main',
            'items' => [
                0 => [
                    'attributes' => $menuItem->attributes,
                    'active' => false,
                    'children' => [],
                    'icon' => '',
                    'order' => 0,
                    'title' => '',
                    'type' => 'link',
                    'url' => ''
                ]
            ]
        ];

        self::assertEquals($itemAsArray, $this->menu->toArray());
    }
}
