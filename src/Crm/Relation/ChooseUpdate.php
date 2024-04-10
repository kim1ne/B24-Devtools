<?php

namespace B24\Devtools\Crm\Relation;

use Bitrix\Crm\ItemIdentifier;
use Bitrix\Crm\Relation\EntityRelationTable;
use Bitrix\Main\DB\SqlQueryException;

class ChooseUpdate
{
    private ?ItemIdentifier $replaceItemIdentifier = null;
    private bool $isChild = true;

    public function __construct(
        public readonly ItemIdentifier $parent,
        public readonly ItemIdentifier $child
    ) {}

    public function on(int $entityTypeId, int $entityId): static
    {
        $this->replaceItemIdentifier = new ItemIdentifier($entityTypeId, $entityId);
        return $this;
    }

    public function isParent(): self
    {
        $this->isChild = false;
        return $this;
    }

    /**
     * @throws SqlQueryException
     * @throws \Exception
     */
    public function replace(): void
    {
        if (is_null($this->replaceItemIdentifier)) {
            throw new \Exception('Property `replaceItemIdentifier` is null');
        }

        $this->delete();
        $this->new();
    }

    /**
     * @throws SqlQueryException
     */
    private function delete(): void
    {
        Delete::on($this->parent, $this->child);
    }

    /**
     * @throws \Exception
     */
    private function new(): void
    {
        if ($this->isChild) {
            $data = [$this->parent, $this->replaceItemIdentifier];
        } else {
            $data = [$this->replaceItemIdentifier, $this->child];
        }

        $data = AbstractRelation::createArrayForRelationTable(...$data);

        $data['RELATION_TYPE'] = 'BINDING';

        EntityRelationTable::add($data);
    }
}