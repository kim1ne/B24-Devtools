<?php

namespace B24\Devtools\HighloadBlock\Fields;

use B24\Devtools\Data\StringHelper;

class EnumValue
{
    public function __construct(
        private string $value,
        private string $xmlId,
        private bool $def = false,
        private int $sort = 500,
    ) {}

    public function toArray(): array
    {
        $data = [];
        foreach ($this as $k => $v) {
            $newK = strtoupper(StringHelper::stringToUnderscore($k));
            if ($k === 'def') {
                $v = $this->$k ? 'Y' : 'N';
            }

            $data[$newK] = $v;
        }

        return $data;
    }
}
