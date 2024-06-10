<?php

namespace B24\Devtools\HighloadBlock\Migration;

use B24\Devtools\Data\UserField;
use B24\Devtools\HighloadBlock\ActiveRecord;
use Bitrix\Main\Application;

class UserFields extends ActiveRecord
{
    public static function addUserFields(array $fields): void
    {
        $obUserField = new \CUserTypeEntity();

        foreach ($fields as $arCartField) {
            $userFieldId = $obUserField->Add($arCartField);

            if ($userFieldId === false) {
                throw new \Exception('Property ' . $arCartField['FIELD_NAME'] . ' Is Not Created.');
            }

            $userType = $arCartField['USER_TYPE_ID'];

            if (!empty($enums = $arCartField['ENUM']) && $userType === 'enumeration') {
                self::addEnum($userFieldId, $enums);
            }
        }
    }

    public static function addEnum(int $userFieldId, $enums): void
    {
        $newEnums = [];
        foreach ($enums as $k => $enum) {
            $newEnums['n'.$k] = $enum;
        }

        if (!empty($newEnums)) {
            $obEnum = new \CUserFieldEnum();
            $obEnum->SetEnumValues($userFieldId, $newEnums);
        }
    }

    public static function deleteEnum(int $userFieldId, array $enumsId): void
    {
        $ids = [];

        foreach ($enumsId as $id) {
            if (!is_int($id)) {
                $id = (int) $id;
            }
            $ids[] = $id;
        }

        if (!empty($ids)) {
            $sql = "DELETE FROM b_user_field_enum WHERE USER_FIELD_ID = " . $userFieldId . " AND ";
            $sql .= 'ID IN (' . implode(', ', $ids) . ');';
            Application::getConnection()->query($sql);
        }
    }

    public static function deleteAllEnumsByUserFieldId(int $userFieldId)
    {
        $obEnum = new \CUserFieldEnum();
        $obEnum->DeleteFieldEnum($userFieldId);
    }

    public static function updateEnum(int $userFieldId, string $xmlId, string $value, bool $def = false)
    {
        $value = "'" . $value . "'";
        $def = $def ? 'Y' : 'N';
        $def = "'" . $def . "'";
        $xmlId = "'" . $xmlId . "'";

        $sql = 'UPDATE b_user_field_enum SET VALUE = ' . $value . ', DEF = ' . $def;

        $sql .= ' WHERE XML_ID = ' . $xmlId . ' AND USER_FIELD_ID = ' . $userFieldId . ';';

        Application::getConnection()->query($sql);
    }

    public static function deleteUserFields(array $fieldsName, string $entityName): void
    {
        $userField = new UserField($entityName);

        foreach ($fieldsName as $field) {
            $userField->getUserField($field);
            $userField->delete();
        }
    }

    protected function getFields(string $entityId): array
    {
        return [];
    }

    public function getName(): string
    {
        return '';
    }

    public function ruName(): string
    {
        return '';
    }

    public function enName(): string
    {
        return '';
    }

    public function getTableName(): string
    {
        return '';
    }
}