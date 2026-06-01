<?php 
namespace App\Helpers;

class Response{

    public static function build($code, $message, $data = null)
    {
        $response = [
            "code"      => $code,
            "message"   => $message,
        ];

        if($data)
        {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function withErrors($code, $message, $errors, $redirectTo = null)
    {
        $response = [
            "code"      => $code,
            "message"   => $message,
            "errors"    => $errors
        ];

        if($redirectTo)
        {
            $response['redirect'] = $redirectTo;
        }

        return response()->json($response, $code);
    }
}

?>