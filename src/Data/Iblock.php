<?php

namespace B24\Devtools\Data;

use Bitrix\Main\ORM\Data\DataManager;

\CModule::IncludeModule('iblock');

class Iblock
{
    public readonly string $iblockCode;

    private DataManager|string $className;

    public function __construct(string $iblockCode)
    {
        $this->iblockCode = ucfirst(strtolower($iblockCode));
        $this->className = $this->getClass();
    }

    private function getClass(): string
    {
        return '\Bitrix\Iblock\Elements\Element' . StringHelper::stringToCamelCase($this->iblockCode) . 'Table';
    }

    public function getClassName(): DataManager|string
    {
        return $this->className;
    }

    public static function generateClassName(string $iblockCode): DataManager|string
    {
        return (new self($iblockCode))->getClassName();
    }
}