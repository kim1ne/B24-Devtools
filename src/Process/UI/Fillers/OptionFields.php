<?php

namespace B24\Devtools\Process\UI\Fillers;

class OptionFields extends AbstractFiller
{
    protected ?string $container = 'optionsFields';

    public readonly array $fields;

    public function __construct(
        Field ...$fields,
    )
    {
        $this->fields = $fields;
    }
}