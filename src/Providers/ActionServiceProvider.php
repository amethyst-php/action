<?php

namespace Amethyst\Providers;

use Amethyst\Actions;
use Amethyst\Common\CommonServiceProvider;
use Amethyst\Models\Workflow;
use Amethyst\Models\Relation;
use Amethyst\Models\WorkflowNode;
use Amethyst\Observers\WorkflowObserver;
use Amethyst\Observers\WorkflowNodeObserver;
use Amethyst\Observers\RelationObserver;

class ActionServiceProvider extends CommonServiceProvider
{
    /**
     * @inherit
     */
    public function register()
    {
        parent::register();

        $this->app->register(\Amethyst\Providers\RelationServiceProvider::class);
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

        app('amethyst')->pushMorphRelation('workflow-node', 'target', 'workflow');
        app('amethyst')->pushMorphRelation('workflow-node', 'target', 'action');

        app('amethyst')->pushMorphRelation('relation', 'source', 'workflow-node');
        app('amethyst')->pushMorphRelation('relation', 'target', 'workflow-node');
        app('amethyst')->pushMorphRelation('relation', 'source', 'workflow');
        app('amethyst')->pushMorphRelation('relation', 'target', 'workflow');

        app('amethyst.action')->addType('log', Actions\Log::class);
        app('amethyst.action')->addType('listener', Actions\Listener::class);
        app('amethyst.action')->addType('data', Actions\Manager::class);
        app('amethyst.action')->addType('switcher', Actions\Switcher::class);
        app('amethyst.action')->addType('http', Actions\Http::class);

        Actions\Action::boot();

        Workflow::observe(WorkflowObserver::class);
        WorkflowNode::observe(WorkflowNodeObserver::class);
        Relation::observe(RelationObserver::class);
    }
}
