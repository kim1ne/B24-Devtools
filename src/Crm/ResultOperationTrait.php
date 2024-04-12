<?php

namespace B24\Devtools\Crm;

use Bitrix\Main\Error;
use Bitrix\Main\Result;

trait ResultOperationTrait
{
    private ?Result $result = null;

    /**
     * @return Result
     */
    private function result(): Result
    {
        if(is_null($this->result)) {
            $this->result = new Result();
        }

        return $this->result;
    }

    private function error(string $error): self
    {
        $this->result()->addError(new Error($error));
        return $this;
    }
}