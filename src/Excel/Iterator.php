<?php

namespace B24\Devtools\Excel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Iterator extends Generator
{
    private const XLSX = 'Xlsx';

    private array $rows = [];

    public function __construct(
        string $filePath,
        int $startingRow = 2,
    )
    {
        parent::__construct(...func_get_args());

        $this->startingRow = $startingRow;

        if ($this->checkCondition()) {
            $this->spreadsheet = IOFactory::createReader(self::XLSX)
                ->load($this->filePath);
        } else {
            unlink($this->filePath);
        }
    }

    private function checkCondition(): bool
    {
        return $this->startingRow > 2 && file_exists($this->filePath);
    }

    public function getAsBase64Link(): never
    {
        throw new \Exception('->getAsBase64Link() Not supported');
    }

    public function setRows(array $rows): self
    {
        if ($this->startingRow <= 2) {
            parent::setRows($rows);
        } else {
            $this->rows = $rows;
        }

        return $this;
    }

    public function saveFile(): void
    {
        if ($this->checkCondition() === false) {
            $writer = new Xlsx($this->spreadsheet);
            $writer->save($this->filePath);
            return;
        }

        $this->write();
    }

    public function saveAsBitrixFile(string $filePath = null): int
    {
        if ($this->checkCondition() === false) {
            return parent::saveAsBitrixFile($this->filePath);
        }

        $this->write();
        return true;
    }

    private function write(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $alphabet = $this->getAlphabet();

        $startingRow = $this->startingRow;

        foreach ($this->rows as $row) {
            $i = 0;
            foreach ($row as $cell) {
                $worksheet->setCellValue(
                    $alphabet[$i] . $startingRow,
                    $cell
                );
                ++$i;
            }
            ++$startingRow;
        }

        IOFactory::createWriter($this->spreadsheet, self::XLSX)
            ->save($this->filePath, 1);
    }

    private function getAlphabet(): array
    {
        return [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
            'I', 'J', 'K', 'L', 'M', 'N', 'P', 'Q',
            'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y',
            'Z'
        ];
    }
}