<?php

namespace B24\Devtools\Crm\Smart;

class SmartDto
{
    public readonly SmartDynamic $smart;
    public readonly string $entityName;

    public function __construct(
        public readonly int $id,
        public readonly int $entityTypeId,
        public readonly string $code,
    )
    {
        $smart = new SmartProcess($this->entityTypeId);
        $this->entityName = $smart->getEntityName();
        $this->smart = $smart;
    }
}