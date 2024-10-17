<?php

namespace B24\Devtools\Process\UI\Fillers;

use B24\Devtools\Data\StringHelper;

abstract class AbstractFiller implements FillerInterface
{
    protected ?string $container = null;
    protected bool $toSnakeCase = false;

    public function toArray(): array
    {
        $data = [];

        foreach ($this as $k => $v) {
            if ($v === null || $k === 'container' || $k === 'toSnakeCase') {
                continue;
            }

            if ($v instanceof FillerInterface) {
                $v = $v->toArray();
            }

            if ($this->toSnakeCase) {
                $k = StringHelper::stringToUnderscore($k);
            }

            $data[$k] = $v;
        }

        if ($this->container !== null) {
            $data = [$this->container => $data];
        }

        return $data;
    }
}