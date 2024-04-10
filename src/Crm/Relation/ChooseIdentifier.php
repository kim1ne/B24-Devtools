<?php

namespace B24\Devtools\Crm\Relation;

use Bitrix\Crm\ItemIdentifier;

class ChooseIdentifier
{
    public function __construct(
        /**
         * @var ItemIdentifier[] $identifiers
         */
        private readonly array $identifiers
    ) {}

    public function withOne(callable $callable): array
    {
        $items = [];

        foreach ($this->identifiers as $identifier) {
            $value = $callable($identifier);

            if (empty($value)) continue;

            $items[] = $value;
        }

        return $items;
    }

    /**
     * @return ItemIdentifier[]
     */
    public function getAll(): array
    {
        return $this->identifiers;
    }

    /**
     * @return array
     */
    public function getAllEntityId(): array
    {
        return $this->sort();
    }

    /**
     * @param int $entityTypeId
     * @return array
     */
    public function withEntityTypeId(int $entityTypeId): array
    {
        return $this->sort($entityTypeId);
    }

    /**
     * @param int|null $entityTypeId
     * @return array
     */
    private function sort(?int $entityTypeId = null): array
    {
        $result = [];

        foreach ($this->identifiers as $identifier) {
            if (!is_null($entityTypeId))
                if ($entityTypeId !== $identifier->getEntityTypeId()) continue;

            $result[] = $identifier->getEntityId();
        }

        return $result;
    }
}