<?php

namespace Hexadog\MenusManager\Tests;

use Hexadog\MenusManager\Providers\PackageServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PackageServiceProvider::class
        ];
    }
}
