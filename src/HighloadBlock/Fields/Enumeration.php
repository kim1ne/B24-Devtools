<?php

namespace B24\Devtools\HighloadBlock\Fields;

class Enumeration
{
    public function __construct(
        /**
         * @var EnumValue[] $enumValue
         */
        private array $enumValue,
    ) {}

    public function toArray(): array
    {
        $field = [];
        foreach ($this->enumValue as $value) {
            $field[] = $value->toArray();
        }
        return $field;
    }
}
