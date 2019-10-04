<?php

vimport ('~~/include/Webservices/Query.php');

class Trading_Bridge_Action extends Vtiger_BasicAjax_Action {
    public function process(Vtiger_Request $request) {
            try {
//                    $url = "https://216.105.250.15/InstitutionalAPIv2/api";
                    $url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";
                    $result = array();
                    $task = $request->get('task');
                    $current_user = Users_Privileges_Model::getCurrentUserPrivilegesModel();
                    if(strlen($request->get('username')) > 0)
                        $user = $request->get('username');
                    else
                        $user = $current_user->get('veo_username');
                    
                    if(strlen($request->get('password')) > 0)
                        $password = $request->get('password');
                    else
                        $password = $current_user->get('veo_password');
                    $user = "Omniscient";
                    $password = "test123456";
                    $bridge = new Trading_Ameritrade_Model($user, $password, 1);
                    switch($task){
                        case "login":
                                $bridge->OpenSession($url);
                            break;
                        case "logout":
                                $bridge->CloseSession($url);
                            break;
                        case "get_users":
                                $output = $bridge->GetUsers($url);
                                print_r($output);
                            break;
                        case "get_quote":
                                $symbol = $request->get('symbol');
                                $output = $bridge->GetQuote($url, $symbol);
                                return $output;
                            break;
                        case "verify_user":
                                $output = $bridge->VerifyUser($url);
                                return $output;
                            break;
/*                                $symbol_info = array();
                    //            print_r($output);
                                error_reporting(0);
                                foreach($output AS $a => $b)
                                    foreach($b AS $c => $d)
                                        foreach($d AS $e => $f)
                                            foreach($f AS $g => $h)
                                                foreach($h AS $k => $v)
                                                    $symbol_info[$k] = $v;
                                $quote = new Trading_Quote_View();
                                $quote->process($request);
                                return $symbol_info;*/
///                                $smarty->assign("SYMBOL_INFO", $symbol_info);
///                                $smarty->display("quote_information.tpl");
                    }
            } catch (Exception $ex) {
                    echo $ex->getMessage();
            }
    }
}
?>
