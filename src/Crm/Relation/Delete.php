<?php

namespace B24\Devtools\Crm\Relation;

use Bitrix\Crm\ItemIdentifier;
use Bitrix\Crm\Relation\EntityRelationTable;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;

class Delete extends AbstractRelation
{
    /**
     * @throws SqlQueryException
     */
    public static function on(ItemIdentifier $parent, ItemIdentifier $child): Result
    {
        $result = new Result();
        try {
            self::connection()->query(
                self::generateSql(self::createArrayForRelationTable($parent, $child))
            );
        } catch (\Exception $exception) {
            $result->addError(new Error($exception->getMessage()));
        }

        return $result;
    }

    public static function onEntityTypeId(ItemIdentifier $parent, $childEntityTypeId): Result
    {
        $data = Helper::createArrayForRelationTable($parent, new ItemIdentifier($childEntityTypeId,1));

        unset($data['DST_ENTITY_ID']);

        $result = new Result();

        try {
            self::connection()->query(
                self::generateSql($data)
            );
        } catch (\Exception $exception) {
            $result->addError(new Error($exception->getMessage()));
        }

        return $result;
    }

    private static function generateSql(array $filter): string
    {
        return 'DELETE FROM ' .
            EntityRelationTable::getTableName() .
            ' WHERE ' . self::toStringFilter($filter);
    }

    private static function toStringFilter(array $filter): string
    {
        $sqlAnd = [];

        foreach ($filter as $k => $v) {
            $sqlAnd[] = $k . ' = ' . $v;
        }

        return implode(' AND ', $sqlAnd);
    }

    private static function connection(): \Bitrix\Main\Data\Connection|\Bitrix\Main\DB\Connection
    {
        return Application::getConnection();
    }
}