<?php

namespace B24\Devtools\Crm\Replacement;

use Bitrix\Crm\Service;
use Bitrix\Main\DI;

\CModule::IncludeModule('crm');

final class Container extends Service\Container
{
    public function __construct(
        private readonly array $smartCode2factory
    )
    {
        DI\ServiceLocator::getInstance()->addInstance('crm.service.container', $this);
    }

    public function getFactory(int $entityTypeId): ?Service\Factory
    {
        return $this->getOwnFactory($entityTypeId);
    }

    private function getOwnFactory(int $entityTypeId): ?Service\Factory
    {
        $type = $this->getTypeByEntityTypeId($entityTypeId);

        if (
            $type === null ||
            ($factoryClass = $this->smartCode2factory[$type->get('CODE')] ?? null) === null
        ) {
            return parent::getFactory($entityTypeId);
        }

        return new $factoryClass($type);
    }
}