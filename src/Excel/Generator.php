<?php

namespace B24\Devtools\Excel;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Generator
{
    protected Spreadsheet $spreadsheet;

    public function __construct(
        protected string $filePath,
        protected int $startingRow = 2,
    )
    {
        $this->setSpreadsheet(new Spreadsheet());
    }

    /**
     * Устанавливает заголовки колонок.
     *
     * @param array $columns
     * @param bool $bold
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setHeadingNames(array $columns, bool $bold = true): self
    {
        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->fromArray(
            [
                $columns,
            ],
            NULL,
            'A1'
        );

        if ($bold) {
            $styleArrayFirstRow = [
                'font' => [
                    'bold' => true,
                ],
            ];

            $highestColumn = $sheet->getHighestColumn();

            $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray($styleArrayFirstRow);
        }

        return $this;
    }

    /**
     * Устанавливает строки.
     *
     * @param array $rows
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setRows(array $rows): self
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $rowCounter = $this->startingRow;


        foreach ($rows as $row) {
            $sheet->fromArray(
                [
                    $row,
                ],
                NULL,
                'A' . $rowCounter++
            );
        }

        return $this;
    }

    /**
     * Устанавливает стили.
     *
     * @param array $columns
     *
     * @return $this
     *
     * @throws Exception
     */
    public function applyStyles(array $columns): self
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $columnIterator = $sheet->getColumnIterator();

        foreach ($columns as $column) {
            $columnIndex = $columnIterator->current()->getColumnIndex();
            $sheet->getColumnDimension($columnIndex)->setAutoSize(false);
            $sheet->getColumnDimension($columnIndex)->setWidth((int)$column['width'] ?: 20);
            if ($column['wrapText']) {
                $sheet
                    ->getStyle($columnIndex . '1:' . $columnIndex .  $sheet->getHighestDataRow())
                    ->getAlignment()
                    ->setWrapText(true);
            }
            $columnIterator->next();
        }

        return $this;
    }


    /**
     * @return string
     *
     * @throws WriterException
     */
    public function getAsBase64Link(): string
    {
        ob_start();
        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');

        $report = ob_get_contents();
        ob_end_clean();

        return "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($report);
    }

    /**
     * @return string
     *
     * @throws WriterException
     */
    public function saveAsBitrixFile(): int
    {
        $this->saveFile();

        $fileArray = \CFile::MakeFileArray($this->filePath);
        return \CFile::SaveFile($fileArray, $this->filePath);
    }

    public function saveFile(): void
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->filePath);
    }


    /**
     * @return Spreadsheet
     */
    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    /**
     * @param Spreadsheet $spreadsheet
     *
     * @return self
     */
    public function setSpreadsheet(Spreadsheet $spreadsheet): self
    {
        $this->spreadsheet = $spreadsheet;
        return $this;
    }
}