<?php

namespace B24\Devtools\Crm\Smart;

use B24\Devtools\Crm\Replacement\Container;
use Bitrix\Crm\Controller\Type;
use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;

Loader::includeModule('crm');

class Mapper
{
    public static function create(string $title , string $code, string $name = null)
    {
        if (empty($name)) throw new \Exception("name don't must be empty");

        if (empty($code)) throw new \Exception("code don't must be empty");

        $controller = new Type();

        $newEntityTypeId = self::generateEntityTypeId();

        $fields = self::getFields($newEntityTypeId, $name, $title);

        try {
            $controller->addAction($fields);

            if (!empty($errors = $controller->getErrors())) {
                throw new \Exception(self::getStringError($errors));
            }

            return self::setCodeByEntityId($code, $newEntityTypeId, $name);
        } catch (\Throwable $exception) {

            self::deleteByCodeOrEntityIdIfExists($newEntityTypeId);

            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @return string
     * @param \Bitrix\Main\Error[] $errors
     */
    private static function getStringError(array $errors): string
    {
        $arr = [];
        foreach ($errors as $error) {
            $arr[] = $error->getMessage();
        }

        return implode(', ', $arr);
    }

    public static function setCodeByEntityId(string $code, int $entityTypeId, string $name = null): DTO
    {
        $res = TypeTable::getList([
            'select' => ['ID'],
            'filter' => ['CODE' => $code]
        ]);

        $res = $res->fetch();

        if ($res !== false) {
            throw new \Exception('Smart-process with code = `' . $code . '` already exists.');
        }

        $res = TypeTable::getList([
            'select' => ['ID'],
            'filter' => ['ENTITY_TYPE_ID' => $entityTypeId]
        ]);

        $res = $res->fetch();

        if ($res === false) {
            throw new \Exception('Smart-process with entityTypeId = `' . $entityTypeId . '` Not Found.');
        }

        $id = $res['ID'];

        $data = [
            'CODE' => $code,
            'ENTITY_TYPE_ID' => $entityTypeId
        ];

        if ($name !== null) {
            $data['NAME'] = $name;
        }

        $result = TypeTable::update($id, $data);

        if (!$result->isSuccess()) {
            throw new \Exception(self::getStringError($result->getErrors()));
        }

        return new DTO($id, $entityTypeId, $code);
    }

    private static function getFields(int $id, string $name, string $title): array
    {
        return [
            'entityTypeId' => $id,
            'isCategoriesEnabled' => false,
            'isStagesEnabled' => false,
            'isBeginCloseDatesEnabled' => false,
            'isClientEnabled' => false,
            'isUseInUserfieldEnabled' => false,
            'isLinkWithProductsEnabled' => false,
            'isMycompanyEnabled' => false,
            'isDocumentsEnabled' => false,
            'isSourceEnabled' => false,
            'isObserversEnabled' => false,
            'isRecyclebinEnabled' => false,
            'isAutomationEnabled' => false,
            'isBizProcEnabled' => false,
            'isSetOpenPermissions' => false,
            'isPaymentsEnabled' => false,
            'isCountersEnabled' => false,
            'customSections' => false,
            'customSectionId' => 0,
            'title' => $title,
            'name' => $name,
            'relations' => [
                'parent' => false,
                'child' => false
            ],
            'isExternal' => false,
            'isSaveFromTypeDetail' => true
        ];
    }

    private static function generateEntityTypeId(): int
    {
        $res = TypeTable::getList([
            'select' => ['ENTITY_TYPE_ID'],
            'order' => ['ENTITY_TYPE_ID' => 'ASC']
        ]);

        $entityTypes = [];

        while($item = $res->fetch()) {
            $entityTypes[] = $item['ENTITY_TYPE_ID'];
        }

        $entityTypes = array_flip($entityTypes);

        $i = 128;

        return self::uniqId($i, $entityTypes);
    }

    private static function uniqId(int $i, array $entityTypes): int
    {
        $entityId = $entityTypes[$i] ?? false;

        if ($entityId !== false) {
            return self::uniqId(++$i, $entityTypes);
        }

        return $i;
    }

    public static function deleteByCodeOrEntityId(string|int $code): bool
    {
        $smart = new SmartProcess($code);

        $type = Container::getInstance()->getType($smart->getId());

        $controller = new Type();

        $controller->deleteAction($type);

        if (!empty($errors = $controller->getErrors())) {
            throw new \Exception(self::getStringError($errors));
        }

        return true;
    }

    public static function deleteByCodeOrEntityIdIfExists(string|int $code): bool
    {
        try {
            self::deleteByCodeOrEntityId($code);
        } catch (\Throwable $exception) {}

        return true;
    }

    public static function deleteById(int $id): bool
    {
        $type = Container::getInstance()->getType($id);

        $controller = new Type();

        $controller->deleteAction($type);

        if (!empty($errors = $controller->getErrors())) {
            throw new \Exception(self::getStringError($errors));
        }

        return true;
    }

    public static function deleteByIdIfExists(int $id): bool
    {
        try {
            self::deleteById($id);
        } catch (\Throwable $exception) {}

        return true;
    }
}