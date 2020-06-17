<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-03-30
 * Time: 3:37 PM
 */

class JavaConnector{
    protected $curl;
    protected $tenant, $user, $password, $connection, $cloudDBName;
    public function JavaConnector($tenant, $user, $password, $connection, $cloudDBName){
        $this->tenant = $tenant;
        $this->user = $user;
        $this->password = $password;
        $this->connection = $connection;
        $this->cloudDBName = $cloudDBName;
    }

    protected function Connect($url, &$data){
        $this->curl = curl_init($url);
//        http://lanserver24:8085/OmniServ/AutoParse?custodian=fidelity&tenant=Omniscient&user=syncuser&password=Concert222&connection=192.168.100.224&dbname=custodian_omniscient&operation=updateportfolios&skipDays=2&dontIgnoreFileIfExists=1&vtigerDBName=live_omniscient

        $data['tenant'] = $this->tenant;
        $data['user'] = $this->user;
        $data['password'] = $this->password;
        $data['connection'] = $this->connection;
        $data['dbname'] = $this->cloudDBName;

        $post_data = http_build_query($data);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($this->curl, CURLOPT_PORT, 8085);
    }

    public function MakeCall($url, $data){
        self::Connect($url, $data);
        $curl_response = curl_exec($this->curl);
        if($curl_response === false){
            $info = curl_getinfo($this->curl);
            curl_close($this->curl);
            return "!!!!Error making call!!!!";
#            die('Error making call:  ' . var_export($info));
        }
        curl_close($this->curl);
        return $curl_response;
    }
}