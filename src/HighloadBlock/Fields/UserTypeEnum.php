<?php

namespace B24\Devtools\HighloadBlock\Fields;

enum UserTypeEnum: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case DOUBLE = 'double';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case BOOLEAN = 'boolean';
    case FILE = 'file';
    case EMPLOYEE = 'employee';
    case ENUMERATION = 'enumeration';
}
