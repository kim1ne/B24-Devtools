<?php

namespace B24\Devtools\Crm\Smart;

use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectNotFoundException;

Loader::includeModule('crm');

class SmartProcess extends SmartDynamic {

    public static array $ids = [];

    /**
     * @param string $code
     * @return int
     * @throws ObjectNotFoundException
     */
    public static function getIdByCode(string $code): int
    {
        return self::$ids[$code] ?? self::byCode($code);
    }

    /**
     * @param string $code
     * @return int
     * @throws ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function byCode(string $code): int
    {
        $arEntity = TypeTable::getList([
            'select' => ['ENTITY_TYPE_ID'],
            'filter' => [
                '=CODE' => $code
            ],
        ]);

        if (($entity = $arEntity->fetch()) === false) {
            throw new ObjectNotFoundException("Dynamic ' . $code . ' not found");
        }

        $entityType = (int) $entity['ENTITY_TYPE_ID'];

        self::$ids[$code] = $entityType;

        return $entityType;
    }
}