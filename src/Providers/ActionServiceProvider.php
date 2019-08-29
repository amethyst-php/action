<?php

namespace Amethyst\Providers;

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
    }

    /**
	 * @inherit
	 */
	public function boot()
    {
    	parent::boot();

        app('amethyst')->pushMorphRelation('workflow-node', 'target', 'workflow');
        app('amethyst')->pushMorphRelation('workflow-node', 'target', 'action');
    }
}
