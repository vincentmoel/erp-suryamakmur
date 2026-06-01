<?php 
namespace App\Helpers;

class MoneyFormatter{
    
    public static function rupiah($amount)
    {
        $formattedAmount = number_format($amount, 0, ',', '.');
        return 'Rp ' . $formattedAmount;
    }

    public static function rupiahToNumber($rupiah)
    {
        return intval(str_replace(array("Rp", ".", ","), array("", "", ""), $rupiah));
    }
}
