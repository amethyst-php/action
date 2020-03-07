<?php

namespace Amethyst\Providers;

use Amethyst\Actions;
use Amethyst\Core\Providers\CommonServiceProvider;
use Amethyst\Core\Support\Router;
use Amethyst\Models\Relation;
use Amethyst\Models\Workflow;
use Amethyst\Models\WorkflowNode;
use Amethyst\Observers\RelationObserver;
use Amethyst\Observers\WorkflowNodeObserver;
use Amethyst\Observers\WorkflowObserver;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class ActionServiceProvider extends CommonServiceProvider
{
    /**
     * @inherit
     */
    public function register()
    {
        parent::register();

        $this->app->register(\Amethyst\Providers\RelationServiceProvider::class);
        $this->app->register(\Amethyst\Providers\FileServiceProvider::class);
        $this->app->register(\Amethyst\Providers\NotificationServiceProvider::class);
        $this->app->register(\Railken\Template\TemplateServiceProvider::class);

        $this->app->singleton('amethyst.action', function ($app) {
            return new \Amethyst\Services\Action();
        });
    }

    /**
     * @inherit
     */
    public function boot()
    {
        parent::boot();

        app('amethyst.action')->addType('log', Actions\Log::class);
        app('amethyst.action')->addType('listener', Actions\Listener::class);
        app('amethyst.action')->addType('data', Actions\Manager::class);
        app('amethyst.action')->addType('switcher', Actions\Switcher::class);
        app('amethyst.action')->addType('http', Actions\Http::class);
        app('amethyst.action')->addType('exporter', Actions\Exporter::class);
        app('amethyst.action')->addType('notification', Actions\Notification::class);

        if (Schema::hasTable(Config::get('amethyst.action.data.action.table'))) {
            app('amethyst.action')->starter();
        }

        Workflow::observe(WorkflowObserver::class);
        WorkflowNode::observe(WorkflowNodeObserver::class);
        Relation::observe(RelationObserver::class);
    }

    /**
     * Load routes.
     */
    public function loadRoutes()
    {
        $config = Config::get('amethyst.action.http.app.workflow');

        Router::group('app', Arr::get($config, 'router'), function ($router) use ($config) {
            $controller = Arr::get($config, 'controller');
            $router->post('/', ['as' => 'show', 'uses' => $controller.'@execute']);
        });
    }
}
