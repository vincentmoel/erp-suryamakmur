<?php

namespace App\Exports\Excel;

use App\Exports\Sheets\GeneralSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GeneralExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;
    protected $header;
    protected $fieldToExport;
    protected $sheetName;

    public function __construct($data, $header, $fieldToExport, $sheetName) {
        $this->data = $data;
        $this->header = $header;
        $this->fieldToExport = $fieldToExport;
        $this->sheetName = $sheetName;
    }

    public function sheets(): array {
        $sheets = [];

        $sheets[] = new GeneralSheet($this->data, $this->header, $this->fieldToExport, $this->sheetName);

        return $sheets;
    }
}
