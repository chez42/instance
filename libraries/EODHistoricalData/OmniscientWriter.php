<?php


class OmniscientWriter{
    private $guz;

    public function __construct(){
        $this->guz = new cEodGuzzle();
    }

    public function WriteEodToOmni($symbol){
       $last_year_start = date("Y-01-01", strtotime("-1 years"));
       $last_year_end = date("Y-12-31", strtotime("-1 years"));
       $type = OmnisolReader::DetermineSecurityTypeGivenByEOD($symbol);
       switch(strtolower($type)){
           case "etf":
               $etf = json_decode($this->guz->getFundamentals($symbol));
               $div = json_decode($this->guz->getDividends($symbol, "US", $last_year_start, $last_year_end));
               $data = new TypeETF($etf, $div);
               $data->UpdateIntoOmni();
               break;
           case "stock":
           case "common stock":
               $stock = json_decode($this->guz->getFundamentals($symbol));
               $div = json_decode($this->guz->getDividends($symbol, "US", $last_year_start, $last_year_end));

               $data = new TypeStock($stock);
               print_r($data);exit;
#               $data->UpdateIntoOmni();

           echo 'here';exit;
               break;
       }
    }
}