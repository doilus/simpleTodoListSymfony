<?php


namespace App\Services\Generator;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WriteToExcel
{
    private CreateTaskDataToArray $createTaskDataToArray;

    public function __construct(
        CreateTaskDataToArray $createTaskDataToArray
    )
    {
        $this->createTaskDataToArray = $createTaskDataToArray;
    }

    public function writeToExcel(): void
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', $this->createTaskDataToArray->setTitle());

        $arrayHeaders = $this->createTaskDataToArray->setHeaders();

        $letterRow = 'A';
        foreach ($arrayHeaders as $header) {
            $sheet->setCellValue($letterRow . 2, $header);
            $letterRow++;
        }

        $taskArray = $this->createTaskDataToArray->putDataIntoArray();

        for ($countRow = 0; $countRow < count($taskArray); $countRow++) {
            $letterRow = 'A';
            for ($countColumn = 0; $countColumn < count($taskArray[$countRow]); $countColumn++) {
                $sheet->setCellValue($letterRow . $countRow + 3, $taskArray[$countRow][$countColumn]);
                $letterRow++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save("data/taskData.xlsx");
    }
}