<?php

namespace Hexadog\MenusManager\Providers;

use Hexadog\MenusManager\Components;
use Hexadog\MenusManager\Facades\Menus as MenusFacade;
use Hexadog\MenusManager\Facades\MenusManager as MenusManagerFacade;
use Hexadog\MenusManager\MenusManager;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Our root directory for this package to make traversal easier.
     */
    public const PACKAGE_DIR = __DIR__ . '/../../';

    /**
     * Name for this package to publish assets.
     */
    public const PACKAGE_NAME = 'menus-manager';

    /**
     * Pblishers list.
     */
    protected $publishers = [];

    /**
     * Bootstrap the application events.
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
            Components\Menu::class,
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
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [MenusManager::class];
    }

    /**
     * Get Package absolute path.
     *
     * @param string $path
     */
    protected function getPath($path = '')
    {
        // We get the child class
        $rc = new ReflectionClass(get_class($this));

        return dirname($rc->getFileName()) . '/../../' . $path;
    }

    /**
     * Get Module normalized namespace.
     *
     * @param mixed $prefix
     */
    protected function getNormalizedNamespace($prefix = '')
    {
        return Str::start(Str::lower(self::PACKAGE_NAME), $prefix);
    }

    /**
     * Bootstrap our Publishers.
     */
    protected function strapPublishers()
    {
        $this->publishes([
            $this->getPath('resources/views') => resource_path('views/vendor/menus-manager'),
        ], 'views');
    }
}
