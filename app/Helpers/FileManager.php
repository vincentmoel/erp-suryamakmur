<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileManager
{
    public static function store($file, $folderName, $disk = 'public')
    {
        $filePath = $file->storeAs($folderName, $file->hashName(), $disk);
        return $filePath;
    }
}
