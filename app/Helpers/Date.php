<?php

namespace App\Helpers;

class Date
{
    public static function year()
    {
        return range(2025, date('Y'));
    }
}
