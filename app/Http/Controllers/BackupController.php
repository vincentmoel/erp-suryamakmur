<?php

namespace App\Http\Controllers;

use App\DataTables\BaseDataTable;
use App\Http\Requests\BackupRequest;
use App\Models\Backup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Backup::class,
            'backups',
            'Backup',
            'backups',
            'Backup',
            BackupRequest::class,
            BaseDataTable::class,
            []
        );
    }

    public static function backupTable($tableName, $source)
    {
        // Validasi nama tabel
        if (!DB::getSchemaBuilder()->hasTable($tableName)) {
            return response()->json(['error' => 'Tabel tidak ditemukan'], 404);
        }

        // Ambil skema tabel
        $createTableSql = DB::select("SHOW CREATE TABLE `$tableName`")[0]->{'Create Table'};

        // Ambil data tabel
        $results = DB::table($tableName)->get();

        // Mulai generate SQL
        $sqlDump = "-- Backup Tabel: $tableName\n";
        $sqlDump .= "-- Waktu Backup: " . Carbon::now()->toDateTimeString() . "\n\n";
        $sqlDump .= "$createTableSql;\n\n";

        // Generate INSERT statements
        if ($results->count() > 0) {
            $sqlDump .= "INSERT INTO `$tableName` VALUES \n";
            $valueStrings = [];
            foreach ($results as $row) {
                $rowValues = [];
                foreach ($row as $value) {
                    $rowValues[] = is_null($value) ? 'NULL' : DB::connection()->getPdo()->quote($value);
                }
                $valueStrings[] = '(' . implode(', ', $rowValues) . ')';
            }
            $sqlDump .= implode(",\n", $valueStrings) . ";\n";
        }

        // Buat nama file
        $filename = "backup_{$tableName}_" . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $filepath = "backups/{$filename}";

        // Simpan di storage private
        Storage::disk('private')->put($filepath, $sqlDump);

        // Simpan record ke database
        $backup = Backup::create([
            'table_name'    => $tableName,
            'source'        => $source,
            'file_name'     => $filename,
            'file_path'     => $filepath,
            'file_size'     => Storage::disk('private')->size($filepath),
            'file_type'     => 'sql'
        ]);

        return $backup;
    }
}
