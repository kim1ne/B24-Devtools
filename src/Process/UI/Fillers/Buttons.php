<?php

namespace B24\Devtools\Process\UI\Fillers;

class Buttons extends AbstractFiller
{
    public function __construct(
        public readonly bool $start = true,
        public readonly bool $close = true,
        public readonly bool $stop = true,
    ) {}
}