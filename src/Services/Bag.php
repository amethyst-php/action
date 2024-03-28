<?php

namespace Amethyst\Services;

use Illuminate\Contracts\Database\ModelIdentifier;
use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Queue\SerializesModels;
use Railken\Bag as BaseBag;
use Railken\EloquentMapper\Contracts\Map as MapContract;

class Bag extends BaseBag
{
    use SerializesModels;

    /**
     * Prepare the instance for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        $properties = $this->parameters;

        foreach ($properties as $name => $property) {
            $this->set($name, $this->getSerializedPropertyValue($property));
        }

        return ['parameters'];
    }

    /**
     * Restore the model after serialization.
     *
     * @return void
     */
    public function __wakeup()
    {
        $properties = $this->parameters;

        foreach ($properties as $name => $property) {
            $this->set($name, $this->getRestoredPropertyValue($property));
        }
    }

    /**
     * Restore the model from the model identifier instance.
     *
     * @param \Illuminate\Contracts\Database\ModelIdentifier $value
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function restoreModel($value)
    {
        return $this->getQueryForModelRestoration(
            app(MapContract::class)->keyToModel($value->class)->setConnection($value->connection),
            $value->id
        )->useWritePdo()->firstOrFail()->load($value->relations ?? []);
    }

    /**
     * Get the property value prepared for serialization.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function getSerializedPropertyValue($value)
    {
        if ($value instanceof QueueableCollection) {
            return new ModelIdentifier(
                $value->getQueueableClass(),
                $value->getQueueableIds(),
                $value->getQueueableRelations(),
                $value->getQueueableConnection()
            );
        }

        if ($value instanceof QueueableEntity) {
            return new ModelIdentifier(
                app(MapContract::class)->modelToKey($value),
                $value->getQueueableId(),
                $value->getQueueableRelations(),
                $value->getQueueableConnection()
            );
        }

        return $value;
    }
}
