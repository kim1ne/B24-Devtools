<?php

namespace B24\Devtools\Crm\Relation;

use Bitrix\Crm\ItemIdentifier;
use Bitrix\Crm\Relation\RelationManager;
use Module\Helpers\Crm\Replacement\Container;

abstract class AbstractRelation
{
    protected static function instanceRelationManager(): RelationManager
    {
        return Container::getInstance()->getRelationManager();
    }

    public static function createArrayForRelationTable(
        ItemIdentifier $parent,
        ItemIdentifier $child
    ): array
    {
        return [
            'SRC_ENTITY_TYPE_ID' => $parent->getEntityTypeId(),
            'SRC_ENTITY_ID' => $parent->getEntityId(),
            'DST_ENTITY_TYPE_ID' => $child->getEntityTypeId(),
            'DST_ENTITY_ID' => $child->getEntityId()
        ];
    }
}