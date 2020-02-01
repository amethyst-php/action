<?php

namespace Amethyst\Services;

use Illuminate\Queue\SerializesModels;
use Railken\Bag as BaseBag;

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
}
