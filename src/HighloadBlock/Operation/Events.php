<?php

namespace B24\Devtools\HighloadBlock\Operation;

use B24\Devtools\HighloadBlock\ActiveRecord;
use Bitrix\Main\EventManager;
use Bitrix\Main\ORM\Event;

/**
 * @method self onBeforeAdd(string $className)
 * @method self onAdd(string $className)
 * @method self onAfterAdd(string $className)
 * @method self onBeforeUpdate(string $className)
 * @method self onUpdate(string $className)
 * @method self onAfterUpdate(string $className)
 * @method self onBeforeDelete(string $className)
 * @method self onDelete(string $className)
 * @method self onAfterDelete(string $className)
 */
class Events
{
    public function __construct(
        private readonly ActiveRecord $record
    ) {}

    public function __call(string $name, array $arguments)
    {
        $eventType = $this->record->getName() . ucfirst($name);

        $className = $arguments[0] ?? null;

        $this->validate($className);

        $this->addEvent(
            $eventType,
            [$className, $name]
        );

        return $this;
    }

    /**
     * @throws \Exception
     */
    private function validate($className): void
    {
        if (!is_string($className)) throw new \Exception('Argument must be string.');

        if (!class_exists($className)) throw new \Exception('Class ' . $className . ' Is Not Found.');
    }

    private function eventManager(): EventManager
    {
        return EventManager::getInstance();
    }

    private function addEvent(string $eventType, array $callback): void
    {
        $this->eventManager()->addEventHandler(
            '',
            $eventType,
            $callback
        );
    }
}