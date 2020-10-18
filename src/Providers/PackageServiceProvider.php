<?php

namespace Hexadog\MenusManager\Providers;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Hexadog\MenusManager\Components;
use Hexadog\MenusManager\MenusManager;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Hexadog\MenusManager\Facades\Menus as MenusFacade;
use Hexadog\MenusManager\Facades\MenusManager as MenusManagerFacade;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Our root directory for this package to make traversal easier
     */
    const PACKAGE_DIR = __DIR__ . '/../../';

    /**
     * Name for this package to publish assets
     */
    const PACKAGE_NAME = 'menus-manager';

    /**
     * Pblishers list
     */
    protected $publishers = [];

    /**
     * Get Package absolute path
     *
     * @param string $path
     * @return void
     */
    protected function getPath($path = '')
    {
        // We get the child class
        $rc = new ReflectionClass(get_class($this));

        return dirname($rc->getFileName()) . '/../../' . $path;
    }

    /**
     * Get Module normalized namespace
     *
     * @return void
     */
    protected function getNormalizedNamespace($prefix = '')
    {
        return Str::start(Str::lower(self::PACKAGE_NAME), $prefix);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->strapPublishers();

        $this->loadViewsFrom($this->getPath('resources/views'), 'menus-manager');
        $this->loadViewComponentsAs('menus', [
            Components\Children::class,
            Components\Divider::class,
            Components\Header::class,
            Components\Icon::class,
            Components\Item::class,
            Components\Menu::class
        ]);
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton(MenusManager::class, function () {
            return new MenusManager();
        });

        AliasLoader::getInstance()->alias('MenusManager', MenusManagerFacade::class);
        AliasLoader::getInstance()->alias('Menus', MenusFacade::class);
    }

    /**
     * Bootstrap our Publishers
     */
    protected function strapPublishers()
    {
        $this->publishes([
            $this->getPath('resources/views') => resource_path('views/vendor/menus-manager'),
        ], 'views');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [MenusManager::class];
    }
}
