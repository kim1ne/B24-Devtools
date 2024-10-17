<?php

namespace B24\Devtools\Process\UI\Fillers;

class Queue extends AbstractFiller
{
    public function __construct(
        public readonly ?string $action,
        public readonly ?string $title = null,
        public readonly ?array $params = null,
    ) {}
}