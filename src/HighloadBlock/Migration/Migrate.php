<?php

namespace B24\Devtools\HighloadBlock\Migration;

use B24\Devtools\Data\UserField;
use B24\Devtools\HighloadBlock\ActiveRecord;
use B24\Devtools\HighloadBlock\Fields\Field;
use B24\Devtools\HighloadBlock\Fields\UserTypeEnum;
use Bitrix\Main\Application;

class Migrate extends ActiveRecord
{
    public function __construct(
        private readonly ActiveRecord $record
    ) {}

    public function init(): void
    {
        $userField = new UserField($entityName = $this->record->getEntityName());

        $fieldsExists = $userField->listName();

        $fieldsMigrate = [];

        foreach ($this->record->getFields($entityName) as $k => $v) {
            if ($v instanceof Field) {
                $v = $v->toArray();
                $name = $v['FIELD_NAME'];
                $fieldsMigrate[$name] = $v;
            } elseif (is_array($v)) {
                $name = $v['FIELD_NAME'];
                $fieldsMigrate[$name] = $v;
            }
        }

        $newFields = [];

        $enumExists = [];

        foreach ($fieldsMigrate as $field) {
            $fieldName = $field['FIELD_NAME'];

            $exists = $fieldsExists[$fieldName] ?? false;

            if ($exists === false) {
                $newFields[] = $field;
            }

            if ($exists['USER_TYPE_ID'] === UserTypeEnum::ENUMERATION->value) {
                $enumExists[$exists['FIELD_NAME']] = $exists;
            }
        }

        $deleteFields = [];

        $enumMigrates = [];

        foreach ($fieldsExists as $fieldName => $int) {
            $exists = $fieldsMigrate[$fieldName] ?? false;

            if ($exists === false) {
                $deleteFields[] = $fieldName;
            }

            if ($exists['USER_TYPE_ID'] === UserTypeEnum::ENUMERATION->value) {
                $enumMigrates[$exists['FIELD_NAME']] = $exists;
            }
        }

        $connection = Application::getConnection();

        try {
            $connection->startTransaction();

            $this->checkEnum($enumExists, $enumMigrates);

            UserFields::addUserFields($newFields);
            UserFields::deleteUserFields($deleteFields, $entityName);

            $connection->commitTransaction();
        } catch (\Throwable $exception) {
            $connection->rollbackTransaction();
            throw new \Exception($exception->getMessage());
        }
    }

    private function checkEnum(array $enumExists, array $enumMigrates): void
    {
        foreach ($enumMigrates as $k => $v) {
            $exists = $enumExists[$k] ?? false;

            if ($exists === false) {
                unset($enumMigrates[$k]);
            }
        }

        $newEnums = [];
        $updatedEnums = [];

        foreach ($enumExists as $fieldName => $data) {
            $userFieldId = $data['ID'];

            $userField = new UserField($this->record->getEntityName());

            $userField->setUserFieldId($userFieldId);

            $userField->isOne = false;

            $dataFieldExists = [];

            foreach ($userField->getUserFieldValue() as $value) {
                $dataFieldExists[$value['XML_ID']] = $value;
            }

            $enumMigration = $enumMigrates[$fieldName]['ENUM'];

            foreach ($enumMigration as $k => $v) {
                $xmlId = $v['XML_ID'];
                unset($enumMigration[$k]);
                $enumMigration[$v['XML_ID']] = $v;

                $enum = $dataFieldExists[$xmlId] ?? false;

                if ($enum === false) {
                    $newEnums[$userFieldId][] = $v;
                    continue;
                }

                if (
                    $enum['VALUE'] !== $v['VALUE'] ||
                    $enum['DEF'] !== $v['DEF']
                ) {
                    $updatedEnums[$userFieldId][] = $v;
                }
            }

            $deletedEnums = [];
            foreach ($dataFieldExists as $k => $v) {
                $xmlId = $v['XML_ID'];

                $enum = $enumMigration[$xmlId] ?? false;

                if ($enum === false && !empty($id = $v['ID'])) {
                    $deletedEnums[$userFieldId][] = $id;
                }
            }

            UserFields::addEnum($userFieldId, $newEnums[$userFieldId] ?? []);
            UserFields::deleteEnum($userFieldId, $deletedEnums[$userFieldId] ?? []);

            foreach ($updatedEnums[$userFieldId] ?? [] as $enum) {
                UserFields::updateEnum($userFieldId, $enum['XML_ID'],  $enum['VALUE'], $enum['DEF']);
            }
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