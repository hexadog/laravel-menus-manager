<?php

namespace Hexadog\MenusManager\Facades;

use Hexadog\MenusManager\MenusManager;
use Illuminate\Support\Facades\Facade;

class Menus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MenusManager::class;
    }
}
