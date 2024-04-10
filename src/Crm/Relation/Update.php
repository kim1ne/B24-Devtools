<?php

namespace B24\Devtools\Crm\Relation;

use Bitrix\Crm\ItemIdentifier;

class Update extends AbstractRelation
{
    public static function on(ItemIdentifier $parent, ItemIdentifier $child): ChooseUpdate
    {
        return new ChooseUpdate($parent, $child);
    }
}