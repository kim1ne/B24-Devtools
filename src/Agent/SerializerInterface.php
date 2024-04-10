<?php

namespace B24\Devtools\Agent;

interface SerializerInterface
{
    public function __serialize(): array;

    public function __unserialize(array $data): void;
}