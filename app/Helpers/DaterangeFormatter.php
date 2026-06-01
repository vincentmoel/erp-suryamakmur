<?php 
namespace App\Helpers;

class DaterangeFormatter{
    
    public static function databaseFormat($daterange)
    {
        $dates = explode(' - ', $daterange);
        $startDate = date('Y-m-d', strtotime($dates[0]));
        $endDate = date('Y-m-d', strtotime($dates[1]));
        
        return [
            "startDate" => $startDate, 
            "endDate"   => $endDate
        ];
    }

    public static function dateTimeDatabaseFormat($dateTime)
    {
        if($dateTime == null){
            return null;
        }
        
        return date('Y-m-d H:i:s', strtotime($dateTime));
    }
}
