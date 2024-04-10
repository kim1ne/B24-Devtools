<?php

namespace B24\Devtools\Crm\Relation;

use Bitrix\Crm\ItemIdentifier;

class Search extends AbstractRelation
{
    public static function children(ItemIdentifier $identifier): ChooseIdentifier
    {
        return new ChooseIdentifier(
            self::instanceRelationManager()->getChildElements($identifier)
        );
    }

    public static function parents(ItemIdentifier $identifier): ChooseIdentifier
    {
        return new ChooseIdentifier(
            self::instanceRelationManager()->getParentElements($identifier)
        );
    }
}