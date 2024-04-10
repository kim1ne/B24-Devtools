<?php

namespace B24\Devtools\Agent;

class Serializer
{
    public static function serialize(object $object): string
    {
        self::isSerialize($object);

        return serialize($object);
    }

    public static function isSerialize(object $object): void
    {
        $reflector = new \ReflectionClass($object);

        if (!$reflector->implementsInterface($className = SerializerInterface::class)) {
            throw new \Exception('class ' . $object::class . ' must be implemented ' . $className);
        }
    }
}