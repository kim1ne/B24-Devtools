<?php

namespace B24\Devtools\HighloadBlock;

use B24\Devtools\Data\UserField;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\Result;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Path;
use Bitrix\Main\IO\Directory;

class Transfer
{
    private array $properties = [];

    public function __construct(
        private ActiveRecord $record
    ){}

    public function toArray(): array
    {
        return $this->properties;
    }

    public function set(string $name, $value): self
    {
        if ($name === 'ID') {
            $this->properties[$name] = $value;
            return $this;
        }

        if (substr($name, 0, 3) !== ($uf = 'UF_')) {
            $name = $uf . $name;
        }

        $this->properties[$name] = $this->multipleRule(...func_get_args());
        return $this;
    }

    public function setBoolean(string $fieldName, bool $bool = true): self
    {
        return $this->set($fieldName, $bool);
    }

    public function setAddress(string $fieldName, string $address, string $city, string $region, string $postalCode, string $country)
    {
        $argNames = ['ADDRESS', 'CITY', 'REGION', 'POSTAL_CODE', 'COUNTRY'];

        $args = func_get_args();
        array_shift($args);

        $data = array_combine($argNames, $args);

        $this->set($fieldName, $data);

        return $this->set($fieldName, $data);
    }

    private function multipleRule(string $name, $value)
    {
        $description = $this->record->getDescriptionFields();

        $multiple = $description[$name] ?? throw new \Exception('Field ' . $name . ' Is Not Found.');

        if ((bool) $multiple) {
            $property = $this->properties[$name] ?? [];

            if (!empty($property)) {
                $value = [...$property, $value];
            } else {
                $value = [$value];
            }
        }

        return $value;
    }

    public function setDateTime(string $field, DateTime $dateTime = new DateTime()): self
    {
        return $this->set($field, $dateTime);
    }

    public function saveExistingFile(string $field, string $fileName): self
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $fileName;

        if (!file_exists($filePath)) {
            throw new \Exception('File ' . $filePath . ' Is Not Found');
        }

        $file = \CFile::MakeFileArray($filePath);

        if (is_array($file)) {
            $file['MODULE_ID'] = 'main';
            $this->set($field, $file);
        }

        return $this;
    }

    public function setEnumByXmlId(string $field, string $xmlId): self
    {
        $userField = new UserField($this->record->getEntityName());
        $userField->getUserField($field);
        $data = $userField->getUserFieldValue([
            'XML_ID' => $xmlId
        ]);
        if ($data === false) {
            throw new \Exception('Element by XML_ID ' . $xmlId . ' Is Not Found.');
        }

        $id = $data['ID'];

        return $this->set($field, $id);
    }

    public function reset(string ...$fields): self
    {
        foreach ($fields as $field) {
            if (isset($this->properties[$field])) {
                unset($this->properties[$field]);
            }
        }

        return $this;
    }

    public function unsetId(): self
    {
        if (isset($this->properties['ID'])) {
            unset($this->properties['ID']);
        }

        return $this;
    }

    public function save(): bool|Result
    {
        if (isset($this->properties['ID'])) {
            $result = $this->update();
        } else {
            $result = $this->add();
        }

        return $result;
    }

    private function add(): bool|AddResult
    {
        $record = $this->record;

        unset($this->record);

        return $record->add($this->properties);
    }

    private function update(): UpdateResult|bool
    {
        $record = $this->record;
        $id = $this->properties['ID'];

        unset($this->record);
        unset($this->ID);

        return $record->update($id, $this->properties);
    }
}