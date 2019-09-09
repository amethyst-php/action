<?php

namespace Amethyst\Providers;

use Amethyst\Actions;
use Amethyst\Common\CommonServiceProvider;

class ActionServiceProvider extends CommonServiceProvider
{
    /**
     * @inherit
     */
    public function register()
    {
        parent::register();

        $this->app->register(\Amethyst\Providers\AggregatorServiceProvider::class);

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

        app('amethyst')->pushMorphRelation('workflow-node', 'target', 'workflow');
        app('amethyst')->pushMorphRelation('workflow-node', 'target', 'action');

        app('amethyst')->pushMorphRelation('aggregator', 'source', 'workflow-node');
        app('amethyst')->pushMorphRelation('aggregator', 'aggregate', 'workflow-node');
        app('amethyst')->pushMorphRelation('aggregator', 'source', 'workflow');
        app('amethyst')->pushMorphRelation('aggregator', 'aggregate', 'workflow');

        app('amethyst.action')->addType('log', Actions\Log::class);
        app('amethyst.action')->addType('listener', Actions\Listener::class);
    }
}
