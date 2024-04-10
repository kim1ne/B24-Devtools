<?php

namespace B24\Devtools\Data;

use Module\Helpers\Crm\Smart\SmartDynamic;

class UserField
{
    public bool $isOne = true;
    private int $userFieldId = 0;

    /**
     * @throws \Exception
     */
    public function __construct(
        private readonly ?SmartDynamic $smartProcess = null,
        private readonly bool          $isSmartProcess = true,
        private readonly ?string $entityName = null
    )
    {
        if ($this->isSmartProcess === true && $this->smartProcess === null) {
            throw new \Exception('Свойство smartProcess не может быть пустое');
        } else if ($this->isSmartProcess === false && $this->entityName === null) {
            throw new \Exception('Свойство entityName не может быть пустое');
        }
    }

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
        $filter = [
            'ENTITY_ID' => $this->isSmartProcess ? $this->smartProcess->getEntityIdPrefix() : $this->entityName,
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