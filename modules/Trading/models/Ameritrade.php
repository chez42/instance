<?php

class Trading_Ameritrade_Model extends Vtiger_Base_Model{

    public static $api_key = "3a22502f-fe75-437c-bdd9-a96e5179cf63";
    public $session_key;
    public $expires;
    public $logout_link;
    public $userid;
    public $password;
    public $duration_minutes;
    public $start_time;
    public $url;
    
    public function __construct($userid="Omniscient", $password="test123456", $duration_minutes=1) {
        $this->userid = $userid;
        $this->password = $password;
        $this->duration_minutes = $duration_minutes;
    }

    static public function GetAmeritradeUsersInformation($user_name=null){
        global $adb;
        $info = array();
        $params = array();
        if($user_name) {
            $questions = generateQuestionMarks($user_name);
            $where = " WHERE userid IN ({$questions})";
            $params[] = $user_name;
        }
        $query = "SELECT userid, password FROM vtiger_ameritrade_users {$where}";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $info[] = $v;
            }
            return $info;
        }
        return 0;
    }

    /**
     * Get the curl return data
     * @param type $url
     * @param type $header
     * @param type $xml_data
     */
    private function GetCurlJsonData($url, $header, $xml_data, $assoc=false, $timeout=5)
    {
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $output = curl_exec($ch);
        curl_close($ch);
        if(strlen($output) > 0) {
            $output = json_decode($output, $assoc);
            return $output;
        }
        return $output;
    }
    
    private function CheckSessionExists()
    {
        global $adb;
        
        $query = "SELECT * FROM ameritrade_session WHERE api_key = ? AND user = ?";
        $api_key = self::$api_key;
        $result = $adb->pquery($query, array($api_key, $this->userid));

        if($adb->num_rows($result) > 0)
        {
            $created_time = $adb->query_result($result, 0, "created_time");
            $expiration_time = $adb->query_result($result, 0, "expiration_time");
            $session_key = $adb->query_result($result, 0, "session_key");
            $logout_link = $adb->query_result($result, 0, "logout_link");
            $current_time = new DateTime(date("Y-m-d H:i:s"));
            $current_time->setTimezone(new DateTimeZone('America/Los_Angeles'));
            $current_time = $current_time->format("Y-m-d H:i:s");
            if($current_time > $expiration_time)
                return false;
            else
            {//Load the current session data
                $this->session_key = $session_key;
                $this->expires = $expiration_time;
                $this->logout_link = $logout_link;
                return true;
            }
        }
        return false;
    }
    
    public function VerifyUser($url){
        $output = "Trying to Connect...<br />";
        $output .= $this->OpenSession($url) . "<br />";
        $output .= $this->CloseSession($url);
        return $output;
    }
    
    /**
     * Open a new session
     * @param type $url
     */
    public function OpenSession($url)
    {
        global $adb;
        $api_key = self::$api_key;
        $this->url = $url;
        
        $getSession = $this->CheckSessionExists();
        $url .= "/sessions/json";
        $header = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;');
        
        if(!$getSession)//If the session doesn't already exist or isn't valid
        {//Start a brand new session
            $xml_data="inputXml=<AuthenticationRequest xmlns='urn:trading.api.institutional.tda.com/Common'>
             <apikey>{$api_key}</apikey>
             <authscheme>TDA-VEO-CREDENTIALS</authscheme>
             <tdauserid>{$this->userid}</tdauserid>
             <tdapassword>{$this->password}</tdapassword>
             <tokenduration>{$this->duration_minutes}</tokenduration>
            </AuthenticationRequest>";

            $output = $this->GetCurlJsonData($url, $header, $xml_data);
            if($output)
            {
                if(is_object($output->model->loginApiJson)){
                    $this->session_key = $output->model->loginApiJson->sessionKey;
                    $this->expires = $output->model->loginApiJson->expires;
                    $this->logout_link = $output->model->loginApiJson->logoutLink;

                    $query = "INSERT INTO ameritrade_session (api_key, created_time, expiration_time, session_key, logout_link, user)
                              VALUES (?, NOW(), ?, ?, ?, ?)
                              ON DUPLICATE KEY
                              UPDATE api_key=?, created_time=NOW(), expiration_time=?, session_key=?, logout_link=?";
/*                    echo $query . "<br />";
                    echo $api_key . "<br />";
                    echo $this->expires . "<br />"; 
                    echo $this->session_key . "<br />"; 
                    echo $this->logout_link . "<br />";
                    echo $this->userid . "<br />";
                    echo $api_key . "<br />";
                    echo $this->expires . "<br />";
                    echo $this->session_key . "<br />";
                    echo $this->logout_link . "<br />";*/
    //                echo    test123456
                    $adb->pquery($query, array($api_key, $this->expires, $this->session_key, $this->logout_link, $this->userid, $api_key, $this->expires, $this->session_key, $this->logout_link));
                }
                else
                    return "Login Credentials Error";
            }
            else
            {
                return "No output received from Ameritrade";
                return;
            }
            return "New Session Created";
        }
        else
            return "Continuing Session";
    }

    public function GetAllAccounts($url, $accounts=null, $startIndex=null, $endIndex=null){
        if(!is_array($accounts))
            $account_numbers[] = $accounts;
        else
            $account_numbers = $accounts;
        $account_text = "";
        if(sizeof($account_numbers) > 0) {
            foreach ($account_numbers AS $k => $v) {
                $account_text .= "<accountNumber>{$v}</accountNumber>";
            }
        }else{
            $account_text = "<accountNumber></accountNumber>";
        }

        $this->KillSession();
        $getSession = $this->CheckSessionExists();
        if(!$getSession) {
            $this->OpenSession($url);
            $getSession = $this->CheckSessionExists();
        }
        if($getSession)
        {#echo 'here';exit;
            $api_key = self::$api_key;
            $session = $this->session_key;
            $header = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;',
                "Authorization: TDAToken {$api_key} {$session}");
            $extension = "/getaccounts/json";
            $xml_data = "inputXml=
                <getAccounts>
                    <multipleAccountRequest>
                        <accountNumbers>";
                        $xml_data .= $account_text;
            $xml_data .= "</accountNumbers>
                    </multipleAccountRequest>
                        <responseCriteria>
                            <sortCriteria>
                                <accountSortAttribute></accountSortAttribute>
                                <sortDirection>ASCENDING</sortDirection>
                            </sortCriteria>
                            <paginationCriteria>
                                <startIndex>{$startIndex}</startIndex>
                                <endIndex>{$endIndex}</endIndex>
                            </paginationCriteria>
                            <responseAttributes>
                                <accountResponseAttribute></accountResponseAttribute>
                            </responseAttributes>
                        </responseCriteria>
                    </getAccounts>";
            $url .= $extension;

            return $this->GetCurlJsonData($url, $header, $xml_data, true);
        }
        else
            return 0;
    }

    public function GetBalances($url, $accounts, $date = null, $startIndex=null, $endIndex=null){
        if(!is_array($accounts))
            $account_numbers[] = $accounts;
        else
            $account_numbers = $accounts;

        $this->KillSession();
        $getSession = $this->CheckSessionExists();
        if(!$getSession) {
            $this->OpenSession($url);
            $getSession = $this->CheckSessionExists();
        }
        if($getSession)
        {
            $api_key = self::$api_key;
            $session = $this->session_key;
            $header = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;',
                "Authorization: TDAToken {$api_key} {$session}");
            $extension = "/getbalances/json";
            $xml_data = "inputXml=
                <getBalances>
                    <accountNumbers>";
                    foreach($account_numbers AS $k => $v){
                        $xml_data .= "<accountNumber>{$v}</accountNumber>";
                    }
        $xml_data .="</accountNumbers>
                     <previousDayOnlyFlag>true</previousDayOnlyFlag>
                     <responseCriteria>
                        <sortCriteria>
                            <balanceSortAttribute></balanceSortAttribute>
                            <sortDirection>ASCENDING</sortDirection>
                        </sortCriteria>
                        <paginationCriteria>
                            <startIndex>{$startIndex}</startIndex>
                            <endIndex>{$endIndex}</endIndex>
                        </paginationCriteria>
                        <responseAttributes>
                            <balanceResponseAttribute></balanceResponseAttribute>
                        </responseAttributes>
                    </responseCriteria>
                </getBalances>";
            $url .= $extension;

            return $this->GetCurlJsonData($url, $header, $xml_data, true);
        }
        else
            return 0;
    }

    public function GetUsers($url)
    {
        $getSession = $this->CheckSessionExists();
        if($getSession)
        {
            $api_key = self::$api_key;
            $session = $this->session_key;
            $header = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;',
                            "Authorization: TDAToken {$api_key} {$session}");
            $extension = "/getusers/json";
            $xml_data = "inputXml=
                         <getUsers>
                            <includeAssociatedQuoteLists>true</includeAssociatedQuoteLists>
                            <responseCriteria>
                                    <sortCriteria>
                                            <usersSortAttribute></usersSortAttribute>
                                            <sortDirection>ASCENDING</sortDirection>
                                    </sortCriteria>
                                    <paginationCriteria>
                                            <startIndex></startIndex>
                                            <endIndex></endIndex>
                                    </paginationCriteria>
                                    <responseAttributes>
                                            <usersResponseAttribute></usersResponseAttribute>
                                    </responseAttributes>
                            </responseCriteria>
                         </getUsers>";
            $url .= $extension;
            $output = $this->GetCurlJsonData($url, $header, $xml_data);
            return $output;
        }
        else
            return 0;
    }

    /**
     * Get quote for the given symbol
     * @param type $url
     * @param type $symbol
     * @return int
     */
    public function GetQuote($url, $symbol)
    {
        $this->KillSession();
        $getSession = $this->CheckSessionExists();
        if(!$getSession){
            $this->OpenSession($url);
            $getSession = $this->CheckSessionExists();
        }
        if($getSession)
        {
            $api_key = self::$api_key;
            $session = $this->session_key;
            $header = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;',
                            "Authorization: TDAToken {$api_key} {$session}");
            $extension = "/getquotes/json";
            $xml_data = "inputXml=
                <getQuotes>
                        <symbols>
                                <symbol>{$symbol}</symbol>
                        </symbols>
                        <responseCriteria>
                                <sortCriteria>
                                        <quoteSortAttribute>symbol</quoteSortAttribute>
                                        <sortDirection>ASCENDING</sortDirection>
                                </sortCriteria>
                                <paginationCriteria>
                                        <startIndex></startIndex>
                                        <endIndex></endIndex>
                                </paginationCriteria>
                                <responseAttributes>
                                        <quoteResponseAttribute></quoteResponseAttribute>
                                </responseAttributes>
                        </responseCriteria>
                </getQuotes>";
            $url .= $extension;        
            $output = $this->GetCurlJsonData($url, $header, $xml_data);
            return $output;
        }
        else
            return 0;        
    }


    public function GetSecurity($url, $symbol)
    {
        $this->KillSession();
        $getSession = $this->CheckSessionExists();
        if(!$getSession){
            $this->OpenSession($url);
            $getSession = $this->CheckSessionExists();
        }
        if($getSession)
        {
            $api_key = self::$api_key;
            $session = $this->session_key;
            $header = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;',
                "Authorization: TDAToken {$api_key} {$session}");
            $extension = "/SecurityDetails/json";
            $xml_data = "inputXml=
                <SecurityDetailsRequest>
                        <symbols>
                                <symbol>{$symbol}</symbol>
                        </symbols>

                </SecurityDetailsRequest>";
            $url .= $extension;
            $output = $this->GetCurlJsonData($url, $header, $xml_data);
            return $output;
        }
        else
            return 0;
    }


    public function GetPositions($url, $account=null, $asset_type=null)
    {
        $this->KillSession();
        $getSession = $this->CheckSessionExists();
        if (!$getSession) {
            $this->OpenSession($url);
            $getSession = $this->CheckSessionExists();
        }
        if ($getSession) {
            $api_key = self::$api_key;
            $session = $this->session_key;
            $header = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;',
                "Authorization: TDAToken {$api_key} {$session}");
            $extension = "/getpositions/json";
            $xml_data = "inputXml=
                         <getPositions>
                            <accountNumbers>
                                <accountNumber>{$account}</accountNumber>
                            </accountNumbers>
                            <repCodes>
                                <repCode></repCode>
                            </repCodes>
                            <accountGroupIds>
                                <accountGroupId></accountGroupId>
                            </accountGroupIds>
                            <symbols>
                                <symbol></symbol>
                            </symbols>
                            <cusips>
                                <cusip></cusip>
                            </cusips>
                            <securityDescription></securityDescription>
                            <assetTypes>
                                <assetType>{$asset_type}</assetType>
                            </assetTypes>
                            <previousDayOnlyFlag>true</previousDayOnlyFlag>
                            <consolidateByPositionsFlag>false</consolidateByPositionsFlag>
                            <responseCriteria>
                                <sortCriteria>
                                    <positionSortAttribute>securityDescription
                                    </positionSortAttribute>
                                    <sortDirection>ASCENDING</sortDirection>
                                </sortCriteria>
                                <paginationCriteria>
                                    <startIndex></startIndex>
                                    <endIndex></endIndex>
                                </paginationCriteria>
                                <responseAttributes>
                                    <positionResponseAttribute></positionResponseAttribute>
                                </responseAttributes>
                            </responseCriteria>
                        </getPositions>";
            $url .= $extension;
            $output = $this->GetCurlJsonData($url, $header, $xml_data, false, 5000);
            return $output;
        } else
            return 0;
    }
    
    /**
     * Close the session
     * @param type $url
     */
    public function CloseSession($url){
        global $adb;
        $api_key = self::$api_key;
        
        $getSession = $this->CheckSessionExists();
        
        if($getSession)
        {
            $xml_data="inputXml=<AuthenticationRequest xmlns='urn:trading.api.institutional.tda.com/Common'>
             <apikey>{$api_key}</apikey>
             <sessiontoken>{$this->session_key}</sessiontoken
             <authscheme>TDA-VEO-CREDENTIALS</authscheme>
             <tdauserid>{$this->userid}</tdauserid>
             <tdapassword>{$this->password}</tdapassword>
             <tokenduration>{$this->duration_minutes}</tokenduration>
            </AuthenticationRequest>";

            $ch = curl_init($url . $this->logout_link . "&method=delete");
            //curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $output = curl_exec($ch);

            curl_close($ch);

//            if($output)
  //              $output = json_decode($output);
            $query = "DELETE FROM ameritrade_session WHERE user = ?";
            $adb->pquery($query, array($this->userid));
            
            return "Session Closed";
//            return ($url . $this->logout_link . "&method=delete");
//        echo "Logged out...";
        }
    }
    
    public function KillSession(){
        global $adb;
        
        $query = "DELETE FROM ameritrade_session WHERE user = ?";
        $adb->pquery($query, array($this->userid));
        return "Session Closed";
    }
    
    public function __destruct() {
        
    }
}

?>
