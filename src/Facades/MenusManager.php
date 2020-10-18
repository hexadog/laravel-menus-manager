<?php

namespace Hexadog\MenusManager\Facades;

use Illuminate\Support\Facades\Facade;
use Hexadog\MenusManager\MenusManager as Manager;

class MenusManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
