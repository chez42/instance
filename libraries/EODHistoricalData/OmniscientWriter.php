<?php


class OmniscientWriter{
    private $guz;

    public function __construct(){
        $this->guz = new cEodGuzzle();
    }

    public function WriteEodToOmni($symbol){
       $last_year_start = date("Y-01-01", strtotime("-1 years"));
       $last_year_end = date("Y-12-31", strtotime("-1 years"));
#       $type = OmnisolReader::DetermineSecurityTypeGivenByEOD($symbol, "USA");
       $div = json_decode($this->guz->getDividends($symbol, "US", $last_year_start, $last_year_end));
       $fundamental = json_decode($this->guz->getFundamentals($symbol));
       $type = $fundamental->General->Type;

        switch(strtolower($type)){
           case "etf":
               $fundamental = json_decode($this->guz->getFundamentals($symbol));
               $data = new TypeETF($fundamental, $div);
               $data->UpdateIntoOmni();
               break;
           case "fund":
               $fundamental = json_decode($this->guz->getFundamentals($symbol));
               $data = new TypeFund($fundamental, $div);
               $data->UpdateIntoOmni();
               break;
           case "stock":
           case "common stock":
           $fundamental = json_decode($this->guz->getFundamentals($symbol));
               $data = new TypeStock($fundamental);
#               print_r($data);exit;
#               $data->UpdateIntoOmni();

            default:
                echo "NO DEFINITION!";
               break;
       }
    }
}