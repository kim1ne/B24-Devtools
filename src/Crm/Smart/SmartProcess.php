<?php

namespace B24\Devtools\Crm\Smart;

use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectNotFoundException;

Loader::includeModule('crm');

class SmartProcess extends SmartDynamic {

    private static array $ids = [];

    /**
     * @param string $code
     * @return array
     * @throws ObjectNotFoundException
     */
    public static function getIdByCode(string $code): array
    {
        return self::$ids[$code] ?? self::byCode($code);
    }

    /**
     * @param string $code
     * @return array
     * @throws ObjectNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function byCode(string $code): array
    {
        $arEntity = TypeTable::getList([
            'select' => ['ENTITY_TYPE_ID', 'ID'],
            'filter' => [
                '=CODE' => $code
            ],
        ]);

        if (($entity = $arEntity->fetch()) === false) {
            throw new ObjectNotFoundException("Dynamic ' . $code . ' not found");
        }

        self::$ids[$code] = $entity;

        return $entity;
    }
}