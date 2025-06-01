<?php

namespace B24\Devtools\UserField;

use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\UserFieldTable;

/**
 * @method null|UserField getFieldByEntityTypeId(int $entityTypeId, string $fieldName)
 * @method null|UserField getFieldBySmartProcessName(string $smartProcessName, string $fieldName)
 * @method null|UserField getFieldBySmartProcessCode(string $smartProcessCode, string $fieldName)
 * @method null|UserField getFieldByHlBlockName(string $hlBlockName, string $fieldName)
 * @method null|UserField getFieldByHlBlockId(string $hlBlockId, string $fieldName)
 */
class UserFieldService
{
    const CACHE_TTL = 86400;

    private static ?self $instance = null;

    private function __construct()
    {
        \CModule::IncludeModule("highloadblock");
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __call(string $name, array $arguments)
    {
        $search = 'getFieldBy';

        if (str_starts_with($name, $search) === false) {
            return null;
        }

        return $this->getFieldWrap(
            str_replace($search, 'by', $name),
            ...$arguments
        );
    }

    public function getField(string $entityId, string $fieldName): ?UserField
    {
        $data = $this->queryField($entityId, $fieldName)->fetch();

        if ($data === false) {
            return null;
        }

        return new UserField($data);
    }

    private function queryField(string $entityId, string $fieldName): Result
    {
        return UserFieldTable::getList([
            'filter' => [
                'ENTITY_ID' => $entityId,
                'FIELD_NAME' => $fieldName,
            ],
            'cache' => self::CACHE_TTL,
            'limit' => 1
        ]);
    }

    private function getFieldWrap(string $method, string $value, string $fieldName): ?UserField
    {
        $entityId = EntityName::$method($value);

        if ($entityId === null) {
            return null;
        }

        return $this->getField($entityId, $fieldName);
    }
}
