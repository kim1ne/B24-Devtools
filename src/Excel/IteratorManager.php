<?php

namespace B24\Devtools\Excel;

class IteratorManager
{
    private ?array $header = null;
    private array $rows = [];

    public function __construct(
        public readonly string $filePath,
        public readonly int $page,
        public readonly int $limit,
    ) {}

    public function setHeaderRow(array $header): static
    {
        $this->header = $header;
        return $this;
    }

    public function setRows(array $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    public function saveFile(): void
    {
        $startingRow = ($this->page - 1) * $this->limit + 2;

        $iterator = new Iterator(
            $this->filePath,
            $startingRow
        );

        if ($this->header !== null) {
            $iterator->setHeadingNames($this->header);
        }

        $iterator->setRows($this->rows);

        $iterator->saveFile();
    }
}