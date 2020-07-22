<?php


class OptionsMapping{
    /**
     * The OCC option symbol consists of four parts:
       Root symbol of the underlying stock or ETF, padded with spaces to 6 characters
       Expiration date, 6 digits in the format yymmdd
       Option type, either P or C, for put or call
       Strike price, as the price x 1000, front padded with 0s to 8 digits
     * @param string $symbol
     * @param int $yy
     * @param int $mm
     * @param int $dd
     * @param string $type
     * @param string $strike
     */
    static public function MapToStandards($symbol, $yy, $mm, $dd, $type, $strike){
        $strike = str_pad($strike, 8, "0", STR_PAD_LEFT);
        $option = str_pad($symbol, 6, " ", STR_PAD_RIGHT);
        $option .= $yy . $mm . $dd . $type . $strike;

        return $option;
    }

    /**
     * Convert TD symbol to standardized version
     * @param $symbol
     */
    static public function MapTDToStandard($symbol){
        #ZUMZ Sep 20 2019 22.5 Put
        $elements = explode(" ", $symbol);
#        echo $symbol . '<br />';
        $security = $elements[0];
        $month = date('m',strtotime($elements[1]));
        $day = $elements[2];
        $dt = DateTime::createFromFormat('Y', $elements[3]);
        $year = $dt->format('y');
        $strike = $elements[4] * 1000;
        $type = $elements[5][0];

        return self::MapToStandards($security, $year, $month, $day, $type, $strike);
    }

    /**
     * Parse the standardized option to return the base security
     * @param $option
     * @return mixed
     */
    static public function GetSymbolFromStandardizedOption($option){
        $elements = explode(" ", $option);
        return $elements[0];
    }
}