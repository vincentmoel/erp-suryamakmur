<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class GeneralSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct($data, $header, $fieldToExport, $sheetName){
        $this->data = $data;
        $this->header = $header;
        $this->fieldToExport = $fieldToExport;
        $this->sheetName = $sheetName;
    }

    public function view(): View
    {
        return view('export.general', [
            'data'          => $this->data,
            'header'        => $this->header,
            'fieldToExport' => $this->fieldToExport,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Apply bold font to header row
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
    
        // Get row and column count
        $rowCount = $sheet->getHighestRow();
        $colCount = $sheet->getHighestColumn();
    
        // Range for borders
        $range = 'A1:' . $colCount . $rowCount;
    
        // Apply borders to the range
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
    
        // Apply formats based on header definitions
        $formatMap = [
            'currency' => '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
            'decimal' => '#,##0.00',
            'integer' => '#,##0',
            'percentage' => '0.00%',
            'datetime' => 'yyyy-mm-dd hh:mm:ss',
            'date' => 'yyyy-mm-dd',
        ];
    
        // Find column indexes for each field
        $columnMap = [];
        $headerRow = 1;
        
        foreach ($sheet->getColumnIterator() as $column) {
            $colLetter = $column->getColumnIndex();
            $headerValue = $sheet->getCell($colLetter . $headerRow)->getValue();
            
            // Find which field this column represents
            foreach ($this->fieldToExport as $field) {
                if (isset($this->header[$field]) && $this->header[$field]['label'] === $headerValue) {
                    $columnMap[$field] = $colLetter;
                    break;
                }
            }
        }
    
        // Apply formats based on header definitions
        foreach ($this->fieldToExport as $field) {
            if (isset($this->header[$field]) && isset($columnMap[$field])) {
                $column = $columnMap[$field];
                $format = $this->header[$field]['format'] ?? null;
                
                if ($format && isset($formatMap[$format])) {
                    $cellRange = $column . '2:' . $column . $rowCount;
                    
                    // Apply appropriate number format
                    $sheet->getStyle($cellRange)
                        ->getNumberFormat()
                        ->setFormatCode($formatMap[$format]);
                }
            }
        }
        
        // For columns without special formatting, set as string
        foreach ($sheet->getColumnIterator() as $column) {
            $colLetter = $column->getColumnIndex();
            $isFormattedColumn = false;
            
            // Check if this column has special formatting
            foreach ($columnMap as $field => $mappedCol) {
                if ($mappedCol === $colLetter && isset($this->header[$field]['format'])) {
                    $isFormattedColumn = true;
                    break;
                }
            }
            
            // If no special formatting, set as string
            if (!$isFormattedColumn) {
                for ($i = 2; $i <= $rowCount; $i++) {
                    $cell = $sheet->getCell($colLetter . $i);
                    $cell->setDataType(\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
            }
        }
    }

    public function title(): string
    {
        return $this->sheetName;
    }
}