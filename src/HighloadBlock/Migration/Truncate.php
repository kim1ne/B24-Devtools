<?php

namespace B24\Devtools\HighloadBlock\Migration;

use B24\Devtools\HighloadBlock\ActiveRecord;
use Bitrix\Main\Application;
use Bitrix\Main\DB\Result;

class Truncate
{
    public function __construct(
        private readonly ActiveRecord $record
    ) {}

    public function on(): Result
    {
        $connection = Application::getConnection();

        return $connection->truncateTable(
            $this->record->getTableName()
        );
    }
}