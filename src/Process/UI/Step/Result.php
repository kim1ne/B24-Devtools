<?php

namespace B24\Devtools\Process\UI\Step;

use B24\Devtools\Data\StringHelper;
use B24\Devtools\Process\UI\Fillers\FillerInterface;

class Result implements FillerInterface
{
    const COMPLETED = 'COMPLETED';
    const PROGRESS = 'PROGRESS';

    public function __construct(
        public readonly bool $status,
        public readonly int $processedItems,
        public readonly int $totalItems
    ) {}

    public function toArray(): array
    {
        $data = [];

        foreach ($this as $k => $v) {
            if ($k === 'status') {
                $v = $v ? self::COMPLETED : self::PROGRESS;
            }
            $data[StringHelper::stringToUnderscore($k)] = $v;
        }

        return $data;
    }

    public static function cancel(string $summary = null, $status = self::COMPLETED): array
    {
        return [
            'SUMMARY' => $summary ?? 'Stop!',
            'STATUS' => $status
        ];
    }
}