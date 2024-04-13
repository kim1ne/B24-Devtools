<?php

namespace B24\Devtools\Crm;

use Bitrix\Main\Error;

class ProcessResult
{
    /**
     * @param Error[] $errors
     * @return string
     */
    public static function getOneByList(array $errors, $separator = ', '): string
    {
        $message = [];

        foreach ($errors as $error) {
            $message[] = $error->getMessage();
        }

        return implode($separator, $message);
    }
}