<?php

namespace HabitTracking\Infrastructure;

use ReflectionObject;

class Reflection
{
    private function __construct(
        private ReflectionObject $reflection,
        private object $instance,
    ) {
    }

    public static function for(object $instance): self
    {
        return new self(
            new ReflectionObject($instance),
            $instance,
        );
    }

    public function mutate(string $name, mixed $value): self
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($this->instance, $value);

        return $this;
    }

    public function get(string $name): mixed
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($this->instance);
    }
}
