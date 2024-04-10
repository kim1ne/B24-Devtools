<?php

namespace B24\Devtools\Crm\Relation;

use Bitrix\Crm\ItemIdentifier;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Result;

final class Manager
{
    public static function searchChildren(int $entityTypeId, int $entityId): ChooseIdentifier
    {
        return Search::children(
            new ItemIdentifier($entityTypeId, $entityId)
        );
    }

    public static function searchParents(int $entityTypeId, int $entityId): ChooseIdentifier
    {
        return Search::parents(
            new ItemIdentifier($entityTypeId, $entityId)
        );
    }

    public static function create(
        int $parentEntityTypeId,
        int $parentEntityId,
        int $childEntityTypeId,
        int $childEntityId
    ): Result
    {
        return Create::on(
            new ItemIdentifier($parentEntityTypeId, $parentEntityId),
            new ItemIdentifier($childEntityTypeId, $childEntityId),
        );
    }

    public static function update(
        int $parentEntityTypeId,
        int $parentEntityId,
        int $childEntityTypeId,
        int $childEntityId
    ): ChooseUpdate
    {
        return Update::on(
            new ItemIdentifier($parentEntityTypeId, $parentEntityId),
            new ItemIdentifier($childEntityTypeId, $childEntityId),
        );
    }

    public static function deleteOne(
        int $parentEntityTypeId,
        int $parentEntityId,
        int $childEntityTypeId,
        int $childEntityId
    ): Result
    {
        return Delete::on(
            new ItemIdentifier($parentEntityTypeId, $parentEntityId),
            new ItemIdentifier($childEntityTypeId, $childEntityId),
        );
    }

    public static function deleteWithType(
        int $parentEntityTypeId,
        int $parentEntityId,
        int $childEntityTypeId,
    ): Result
    {
        return Delete::onEntityTypeId(
            new ItemIdentifier($parentEntityTypeId, $parentEntityId),
            $childEntityTypeId
        );
    }
}