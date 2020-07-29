<?php


class Dividend{
    public $data;

    public static function DetermineFrequency($dividend_data){
        $frequency = "None";
        if(count($dividend_data) > 0){
            switch(count((array)$dividend_data)){
                case 2:
                    $frequency = "SemiAnnual";
                    break;
                case 4:
                case 5:
                case 6:
                    $frequency = "Quarterly";
                    break;
                case 7:
                case 8:
                case 9:
                case 10:
                case 11:
                case 12:
                    $frequency = "Monthly";
                    break;
                default:
                    $frequency = "Annual";
                    break;
            }
        }
        return $frequency;
    }

    public function __construct($dividend_data){

    }

}