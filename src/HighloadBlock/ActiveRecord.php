<?php

namespace B24\Devtools\HighloadBlock;

use B24\Devtools\Data\UserField;
use B24\Devtools\HighloadBlock\Fields\Field;
use B24\Devtools\HighloadBlock\Fields\UserTypeEnum;
use B24\Devtools\HighloadBlock\Migration\Create;
use B24\Devtools\HighloadBlock\Migration\Drop;
use B24\Devtools\HighloadBlock\Migration\Migrate;
use B24\Devtools\HighloadBlock\Migration\Truncate;
use B24\Devtools\HighloadBlock\Operation\Events;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\ORM\Query\Result as QueryResult;
use Bitrix\Main\SystemException;

\CModule::IncludeModule('highloadblock');

/**
 * @method Entity getEntity()
 * @method unsetEntity($class)
 * @method string|EntityObject getObjectClass(string $url, array|string|\Closure $arguments)
 * @method string getObjectClassName()
 * @method string|Collection getCollectionClass()
 * @method string getCollectionClassName()
 * @method QueryResult getByPrimary($primary, array $parameters = array())
 * @method QueryResult getById($id)
 * @method array|null getRowById($id)
 * @method array|null getRow(array $parameters)
 * @method QueryResult getList(array $parameters = array())
 * @method int getCount($filter = array(), array $cache = array())
 * @method Query query()
 * @method checkFields(\Bitrix\Main\ORM\Data\Result $result, $primary, array $data)
 * @method AddResult add(array $data)
 * @method AddResult addMulti($rows, $ignoreEvents = false)
 * @method UpdateResult update($primary, array $data)
 * @method UpdateResult updateMulti($primaries, $data, $ignoreEvents = false)
 * @method DeleteResult delete($primary)
 * @method enableCrypto($field, $table = null, $mode = true)
 * @method bool cryptoEnabled($field, $table = null)
 * @method setCurrentDeletingObject($object)
 * @method cleanCache()
 */

abstract class ActiveRecord
{
    private static array $ormClass = [];
    protected array $errors = [];

    private array $descriptionFields = [];

    protected ?int $id = null;

    public function getDescriptionFields(): array
    {
        return $this->descriptionFields;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public static function events(): Events
    {
        return new Events(new static);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->getClassName()::$name(...$arguments);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function migrate(): bool
    {
        try {
            (new Migrate($this))->init();
            return true;
        } catch (\Throwable $exception) {
            $this->errors = [$exception->getMessage()];
            return false;
        }
    }

    public function toTransferWithArray(array $array): Transfer
    {
        $transfer = new Transfer($this);

        foreach ($array as $k => $v) {
            $transfer->set($k, $v);
        }

        return $transfer;
    }

    public function getTransfer(?int $id = null): ?Transfer
    {
        $this->loadDescriptionFields();

        if ($id !== null) {
            $res = $this->getList([
                'filter' => ['ID' => $id]
            ])->fetch();

            if ($res !== false) {
                return $this->toTransferWithArray($res);
            }
        }

        return new Transfer($this);
    }

    public function loadDescriptionFields():  void
    {
        if (!empty($this->descriptionFields)) {
            return;
        }

        $fields = $this->getFields('');

        foreach ($fields as $field) {
            $value = null;
            if ($field instanceof Field) {
                $value = $field->toArray();
            } elseif (is_array($field)) {
                $value = $field;
            } else {
                continue;
            }

            $this->descriptionFields[$value['FIELD_NAME']] = $value['MULTIPLE'] === 'Y';
        }
    }

    public function truncate(): Truncate
    {
        return new Truncate($this);
    }

    public function createHL(): bool
    {
        return (new Create($this))->init();
    }

    private function addUserFields(array $fields): void
    {
        $obUserField = new \CUserTypeEntity();

        foreach ($fields as $arCartField) {
            $userFieldId = $obUserField->Add($arCartField);

            if ($userFieldId === false) {
                throw new \Exception('Property ' . $arCartField['FIELD_NAME'] . ' Is Not Created.');
            }

            $userType = $arCartField['USER_TYPE_ID'];

            if (!empty($enums = $arCartField['ENUM']) && $userType === 'enumeration') {
                $this->addEnum($userFieldId, $enums);
            }
        }
    }

    public function getEntityName(): string
    {
        if ($this->id === null) {
            $res = $this->getHLIblock()->fetch();

            if ($res === false) throw new \Exception('HighloadBlock ' . $this->getName() . ' Is Not Found.');

            $this->id = $res['ID'];
        }

        return 'HLBLOCK_' . $this->id;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    protected function getClassName(): DataManager|string
    {
        if (!empty($class = self::$ormClass[static::class])) {
            return $class;
        }

        $res = $this->getHLIblock()->fetch();

        if ($res === false) throw new SystemException('Highload block ' . $this->getName() . ' Is Not Found.');

        $hlId = $res['ID'];
        $hlblock   = HL\HighloadBlockTable::getById($hlId)->fetch();
        $entity   = HL\HighloadBlockTable::compileEntity($hlblock);

        $class = $entity->getDataClass();

        self::$ormClass[static::class] = $class;

        return $class;
    }

    public function dropHL(): bool
    {
        return (new Drop($this))->init();
    }

    protected function getHLIblock(): QueryResult
    {
        return HL\HighloadBlockTable::getList([
            'filter' => [
                'NAME' => $this->getName(),
                'TABLE_NAME' => $this->getTableName()
            ]
        ]);
    }

    protected abstract function getFields(string $entityId): array;
    public abstract function getName(): string;
    public abstract function ruName(): string;
    public abstract function enName(): string;
    public abstract function getTableName(): string;
}