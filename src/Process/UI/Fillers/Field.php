<?php

namespace B24\Devtools\Process\UI\Fillers;

class Field extends AbstractFiller
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $title,
        public readonly string $value,
        public readonly ?int $size = null,
        public readonly ?array $list = null,
        public readonly bool $multiple = false,
    )
    {
        $this->container = $this->name;
    }
}