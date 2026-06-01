<?php 
namespace App\Helpers;

class Encryption{

    private $privateKey = "PS";
    
    public static function encrypt($string)
    {
        $instance = new self();
        return base64_encode($instance->privateKey . $string);
    }

    public static function decrypt($encryptedString)
    {
        $instance = new self();
        $decoded = base64_decode($encryptedString);
        return str_replace($instance->privateKey, '', $decoded);
    }
}

?>