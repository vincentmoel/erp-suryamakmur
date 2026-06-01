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
                "name"      => "rental_station_name",
                "label"     => "RENTAL STATION NAME",
            ],
            [
                "name"      => "duration_type",
                "label"     => "DURATION TYPE",
            ],
            [
                "name"      => "member_discount",
                "label"     => "MEMBER DISCOUNT",
            ],
            [
                "name"      => "subtotal",
                "label"     => "SUBTOTAL",
                "format"    => "currency",
            ],
            [
                "name"      => "member_discount_amount",
                "label"     => "MEMBER DISCOUNT AMOUNT",
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
                "name"      => "total_price_duration",
                "label"     => "TOTAL PRICE DURATION - ALL DISCOUNT",
                "format"    => "currency",
            ],
            [
                "name"      => "total_price_item",
                "label"     => "TOTAL PRICE ITEM - ALL DISCOUNT",
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
            [
                "name"      => "current_stock",
                "label"     => "CURRENT STOCK",
            ],
        ];

        return array_column($headers, null, 'name');
    }
}
