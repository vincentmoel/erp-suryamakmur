<?php 
namespace App\Helpers;

use App\Exports\Excel\GeneralExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DefaultExport{
    
    public static function export(Request $request, $data, $fieldToExport, $fileName, $sheetName = 'Sheet 1')
    {
        $headers = self::headers();

        if ($request->type == 'excel') {
            $excelFile = Excel::download(new GeneralExport($data, $headers, $fieldToExport, $sheetName), $fileName . '.xlsx');
            $excelFile->headers->set("X-Filename", $fileName . '.xlsx');
            return $excelFile;
        } else {
            $pdf = Pdf::loadView('export.general', [
                'data'          => $data,
                'header'        => $headers,
                'fieldToExport' => $fieldToExport,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("$fileName.pdf");
        }
    }

    public static function headers()
    {
        $headers = [
            [
                "name"      => "code",
                "label"     => "CODE",
            ],
            [
                "name"      => "customer_name",
                "label"     => "CUSTOMER NAME",
            ],
            [
                "name"      => "subtotal",
                "label"     => "SUBTOTAL",
                "format"    => "currency",
            ],
            [
                "name"      => "discount_amount",
                "label"     => "DISCOUNT AMOUNT",
                "format"    => "currency",
            ],
            [
                "name"      => "total",
                "label"     => "TOTAL",
                "format"    => "currency",
            ],
            [
                "name"      => "created_at",
                "label"     => "CREATED AT",
                "format"    => "datetime",
            ],
            [
                "name"      => "updated_at",
                "label"     => "UPDATED AT",
                "format"    => "datetime",
            ],
            [
                "name"      => "created_by_name",
                "label"     => "CREATED BY",
            ],
            [
                "name"      => "updated_by_name",
                "label"     => "UPDATED BY",
            ],
            [
                "name"      => "name",
                "label"     => "NAME",
            ],
            [
                "name"      => "stock",
                "label"     => "STOCK",
            ],
        ];

        return array_column($headers, null, 'name');
    }
}
