<?php

namespace B24\Devtools\HighloadBlock\Operation;

use Bitrix\Main\Entity\EventResult;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Event;

trait EventTrait
{
    public static function setError(Event $event, string $error)
    {
        $eventResult = new EventResult();

        $eventResult->addError(
            new EntityError($error)
        );

        $event->addResult($eventResult);
    }
}