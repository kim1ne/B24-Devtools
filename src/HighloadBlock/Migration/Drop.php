<?php

namespace B24\Devtools\HighloadBlock\Migration;

use B24\Devtools\Data\UserField;
use B24\Devtools\HighloadBlock\ActiveRecord;
use B24\Devtools\HighloadBlock\Fields\Field;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Application;

class Drop extends ActiveRecord
{
    public function __construct(
        private readonly ActiveRecord $record
    ) {}

    public function init()
    {
        try {
            $res = $this->record->getHLIblock()->fetch();

            if ($res !== false) {
                $this->dropEnum();
                $this->dropLangTable($id = $res['ID']);
                HL\HighloadBlockTable::delete($id);
            }

            $this->dropMultiple();

            return true;
        } catch (\Exception $exception) {
            debug($exception);
            $this->record->errors = [$exception->getMessage()];
            return false;
        }
    }

    private function dropEnum(): void
    {
        $this->loadDescriptionFields();
        $fields = $this->record->getFields($entityName = $this->record->getEntityName());

        $enumsName = [];

        foreach ($fields as $field) {
            if ($field instanceof Field) {
                $value = $field->toArray();
            } elseif (is_array($field)) {
                $value = $field;
            } else {
                continue;
            }

            if (!empty($value['ENUM'])) {
                $enumsName[] = $value['FIELD_NAME'];
            }
        }

        foreach ($enumsName as $name) {
            ($userField = new UserField($entityName))->getUserField($name);

            $userField->delete();
        }


    }

    private function dropMultiple(): void
    {
        $this->loadDescriptionFields();
        $description = $this->record->getDescriptionFields();

        $tables = [];
        foreach ($description as $fieldName => $multiple) {
            if ($multiple !== true) continue;

            $tables[] = $this->record->getTableName() . '_' . strtolower($fieldName);
        }

        foreach ($tables as $table) $this->dropTableIfExists($table);
    }

    private function dropLangTable($id): void
    {
        $res = HL\HighloadBlockLangTable::getList([
            'filter' => ['ID' => $id]
        ]);

        while($item = $res->fetch()) {
            HL\HighloadBlockLangTable::delete(['ID' => $item['ID'], 'LID' => $item['LID']]);
        }
    }

    private function dropTableIfExists(string $table): void
    {
        Application::getConnection()->query('DROP TABLE IF EXISTS ' . $table . ';');
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