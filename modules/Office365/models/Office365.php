<?php
require_once 'libraries/Office365/autoload.php';
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;


class Office365_Office365_Model{
    
    private $accessToken;
    private $refreshToken;
    protected $ews;
    protected $maxEntriesReturned = 100;
    
    function __construct($accessToken = false, $refreshToken = false){
        
        if($accessToken){
            $this->accessToken = $accessToken;
        }
        
        if($refreshToken){
            $this->refreshToken = $refreshToken;
        }
        
    }
   
   
    public function getInstance($accessToken = false, $refreshToken = false){
        
        $modelClassName = get_called_class();
        
        $model = new $modelClassName($accessToken, $refreshToken);
        
        $graph = new Graph();
        
        try{
            
            $graph->setAccessToken($accessToken);
            
            $user = $graph->createRequest("GET", "/me")->setReturnType(Model\User::class)->execute();
            
        } catch(Exception $e){
            
            global $adb, $current_user;
            
            $clientId = MailManager_Office365Config_Connector::$clientId;
            
            $clientSecret = MailManager_Office365Config_Connector::$clientSecret;
            
            $token_request_data = array(
                "grant_type" => "refresh_token",
                "refresh_token" => $refreshToken,
                "client_id" => $clientId,
                "client_secret" => $clientSecret
            );
            
            $token_request_body = http_build_query($token_request_data);
            
            $curl = curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/token');
            
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            curl_setopt($curl, CURLOPT_POST, true);
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);
            
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            
            $response = curl_exec($curl);
            
            $response = json_decode($response, true);
            
            $graph->setAccessToken($response['access_token']);
            
            $adb->pquery("UPDATE vtiger_office365_sync_settings SET access_token=?, refresh_token=?
                    WHERE user=?",
                array($response['access_token'], $response['refresh_token'], $current_user->id));
            
        }
        
        $model->ews = $graph;
        
        $this->ews = $graph;
        
        return $model;
    }  
    
    function getItems($itemIds){
        
        $graph = $this->ews;

        $Items = array();
        
        foreach($itemIds as $id){
            
            $Item =  $graph->createCollectionRequest("GET", '/me/Events/'.$id)
            ->setReturnType(Model\Event::class);
               
            $Items[] = $Item->getPage();
            
        }
        
        return $Items;
    }
    
    
    function deleteItems($items){
        
        $graph = $this->ews;
        
        foreach($items as $item){
           
            try{
                $response = $graph->createRequest("DELETE", "/me/Events/".$item['Id'])
                ->execute();
            } catch(Exception $e){
                $response = $e->getMessage();
            }
            
        }
        
        return $response;
    }
    
    function isValidCredentials(){
        
        $request = array(
            'FolderShape' => array(
                'BaseShape' => array('_' => 'Default')
            ),
            'FolderIds' => array(
                'DistinguishedFolderId' => array(
                    'Id' => 'contacts'
                )
            )
        );
        
        $request = Type::buildFromArray($request);
        
        try{
            
            $response = $this->ews->getClient()->GetFolder($request);
            
            if($response->getFolderId()->getId()){
                return true;
            }
            
        } catch (Exception $e){
            return false;
        }
    }
}