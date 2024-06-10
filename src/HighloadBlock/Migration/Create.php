<?php

namespace B24\Devtools\HighloadBlock\Migration;

use B24\Devtools\HighloadBlock\ActiveRecord;
use B24\Devtools\HighloadBlock\Fields\Field;
use Bitrix\Main\Application;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\ORM\Data\AddResult;

class Create extends ActiveRecord
{
    public function __construct(
        private readonly ActiveRecord $record
    ) {}

    public function init()
    {
        $connection = Application::getConnection();

        try {
            $connection->startTransaction();
            $res = $this->record->getHLIblock()->fetch();

            if ($res !== false) {
                $this->record->id = $res['ID'];
                return true;
            }

            $result = $this->add();

            if ($result->isSuccess() === false) {
                throw new \Exception(implode(', ', $result->getErrorMessages()));
            }

            $id = $result->getId();
            $this->record->id = $id;

            $this->addLang($id);

            UserFields::addUserFields($this->getUserFields());

            $connection->commitTransaction();
            return true;
        } catch (\Throwable $exception) {
            $this->record->errors = [$exception->getMessage()];
            $connection->rollbackTransaction();
            return false;
        }
    }

    private function addLang(int $id): void
    {
        $lang = [
            'ru' => $this->record->ruName(),
            'en' => $this->record->enName(),
        ];

        foreach ($lang as $lang_key => $lang_val) {
            HL\HighloadBlockLangTable::add([
                'ID' => $id,
                'LID' => $lang_key,
                'NAME' => $lang_val
            ]);
        }
    }

    private function getUserFields(): array
    {
        $tmpField = $this->record->getFields($this->record->getEntityName());
        $fields = [];

        foreach ($tmpField as $v) {
            if ($v instanceof Field) {
                $v = $v->toArray();
                $fields[] = $v;
            } elseif (is_array($v)) {
                $fields[] = $v;
            }
        }

        return $fields;
    }

    private function add(): AddResult
    {
        return HL\HighloadBlockTable::add([
            'NAME' => $this->record->getName(),
            'TABLE_NAME' => $this->record->getTableName(),
        ]);
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