<?php

namespace B24\Devtools\Data;

use B24\Devtools\Crm\Smart\SmartDynamic;

class UserField
{
    public bool $isOne = true;
    private int $userFieldId = 0;

    /**
     * @throws \Exception
     */
    public function __construct(
        private readonly SmartDynamic|string $smartProcess
    ) {}

    public function setUserFieldId(int $id): void
    {
        $this->userFieldId = $id;
    }

    public function getUserFieldsValue(array $filter = []): array|false
    {
        $filter = [
            'USER_FIELD_ID' => $this->userFieldId,
            ...$filter
        ];
        $obEnum = new \CUserFieldEnum;
        $res = $obEnum->GetList(
            array(),
            $filter
        );

        if ($this->isOne) {
            return $res->Fetch();
        }

        $result = [];

        while($arr = $res->Fetch()) {
            $result[] = $arr;
        }

        return $result;
    }

    public function getUserFields(string $fieldName): array
    {
        $entityId = (is_string($this->smartProcess)) ? $this->smartProcess : $this->smartProcess->getEntityName();

        $filter = [
            'ENTITY_ID' => $entityId,
            'FIELD_NAME' => $fieldName
        ];

        $res = \Bitrix\Main\UserFieldTable::getList([
            'filter' => $filter
        ]);

        if (($res = $res->fetch()) === false) {
            throw new \Exception('Not found ElementName = ' .$fieldName);
        }

        $this->userFieldId = $res['ID'];

        return $res;
    }
}