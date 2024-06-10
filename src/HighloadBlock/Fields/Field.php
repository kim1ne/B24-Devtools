<?php

namespace B24\Devtools\HighloadBlock\Fields;

use B24\Devtools\Data\StringHelper;

class Field
{
    public function __construct(
        private string $entityId,
        private string $fieldName,
        private string|UserTypeEnum $userTypeId,
        private bool $multiple = false,
        private bool $mandatory = true,
        private array $editFormLabel = [],
        private array $listColumnLabel = [],
        private array $listFilterLabel = [],
        private array $errorMessage = [],
        private array $helpMessage = [],
        private ?array $settings = null,
        private ?Enumeration $enum = null
    ) {}

    public function toArray(): array
    {
        $str = substr($this->fieldName, 0, 3);

        if ($str !== ($uf = 'UF_')) {
            $this->fieldName = $uf . $this->fieldName;
        }

        $field = [];

        if ($this->settings === null) {
            unset($this->settings);
        }

        if ($this->enum === null) {
            unset($this->enum);
        }

        foreach ($this as $k => $v) {
            $newK = strtoupper(StringHelper::stringToUnderscore($k));

            $field[$newK] = $this->rules($k, $v);
        }

        return $field;
    }

    private function rules(string $k,  $v)
    {
        if (is_array($v)) {
            return $this->rulesArray($k, $v);
        }

        if (is_bool($v)) {
            return $this->rulesBool($k, $v);
        }

        if ($v instanceof Enumeration) {
            return $this->rulesEnum($v);
        }

        return $this->rulesAll($k, $v);
    }

    private function rulesEnum(Enumeration $enumeration): array
    {
        return $enumeration->toArray();
    }

    private function rulesAll(string $k, $v)
    {
        if ($k === 'userTypeId' && $v instanceof UserTypeEnum) {
            $v = $v->value;
        }

        return $v;
    }

    private function rulesArray(string $k, array $v): array
    {
        if (empty($v)) {
            $v = [
                'ru' => $this->fieldName,
                'en' => $this->fieldName
            ];

            $this->$k = $v;
        }

        return $v;
    }

    private function rulesBool(string $k, bool $v): string
    {
        if ($k === 'multiple') {

            $userTypeId = is_string($this->userTypeId) ? $this->userTypeId : $this->userTypeId->value;

            $v = $this->getModifyTypesMultiple()[$userTypeId] ?? $v;
        }
        return $v ? 'Y' : 'N';
    }

    private function getModifyTypesMultiple(): array
    {
        return [
            UserTypeEnum::BOOLEAN->value => false,
            UserTypeEnum::EMPLOYEE->value => true,
        ];
    }

    public function __serialize(): array
    {
        return $this->toArray();
    }
}