<?php

namespace B24\Devtools\Crm\Smart;

use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Crm\Relation\RelationManager;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\Service\Factory;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;

abstract class SmartDynamic
{
    protected string $code;
    protected string $prefix;
    protected Container $container;
    protected Factory $factory;
    protected int $factoryId;
    protected string $entityIdPrefix;

    /**
     * @param string $symbolCode
     * передаётся символьный код смарт процесса
     */
    public function __construct(string|int $code)
    {
        if (is_int($code)) {
            $this->factoryId = $code;
        } else {
            $this->code = $code;
        }
    }

    public function add(array $data): array
    {
        $factory = $this->getFactory();
        $item = $factory->createItem($data);

        return $item->save()->getData();
    }

    /**
     * @return DataManager|string
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     * Возвращается неймспейс класса который управляет смарт процессом
     */
    public function compileClass(): DataManager|string
    {
        return $this->getFactory()->getDataClass();
    }

    /**
     * @throws LoaderException
     * @throws ObjectNotFoundException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * Отдаёт по символьному коду id смарт процесса
     */
    public function getFactoryId(): int
    {
        if (!isset($this->factoryId)) {
            $this->factoryId = SmartProcess::getIdByCode($this->code);
        }
        return $this->factoryId;
    }

    /**
     * @return RelationManager
     */
    public function getRelationManager(): RelationManager
    {
        return $this->getContainer()->getRelationManager();
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        if (!isset($this->container)) {
            $this->container = Container::getInstance();
        }
        return $this->container;
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     * Фабрика смарт процесса
     */
    public function getFactory(): Factory
    {
        if (!isset($this->factory)) {
            $this->factory = $this->getContainer()->getFactory($this->getFactoryId());
        }
        return $this->factory;
    }

    /**
     * @param string $field
     * @return string
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     * Отдаёт префикс по шаболну UF_CRM_{TYPE_ID}_{FIELD}
     */
    public function getField(string $field): string
    {
        return $this->getPrefix() . $field;
    }

    /**
     * @return string
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     * Отдаёт префикс по шаболну UF_CRM_{TYPE_ID}_
     */
    public function getPrefix(): string
    {
        if (!isset($this->prefix)) {
            $this->prefix = 'UF_' .  $this->getFactory()->getUserFieldEntityId();
        }
        return $this->prefix . '_';
    }

    /**
     * @return string
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     * Вернёт название объект (CRM_X)
     */
    public function getEntityName(): string
    {
        if (!isset($this->entityIdPrefix)) {
            $this->entityIdPrefix = $this->getFactory()->getUserFieldEntityId();
        }
        return $this->entityIdPrefix;
    }
}