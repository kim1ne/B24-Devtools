<?php

namespace B24\Devtools\Crm\Relation;

use Bitrix\Crm\ItemIdentifier;
use Bitrix\Main\Result;
use Module\Helpers\Crm\ProcessResult;

class Create extends AbstractRelation
{
    public static function on(ItemIdentifier $parent, ItemIdentifier $child): Result
    {
        $relationManager = self::instanceRelationManager();

        if (!$relationManager->areItemsBound($parent, $child)) {
            $result = $relationManager->bindItems($parent, $child);
            throw new \Exception(ProcessResult::getOneByList($result->getErrors()));
        }

        return new Result();
    }
}