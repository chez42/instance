<?php
include_once("includes/config.php");

include_once("includes/functions.php");

if(isset($_SESSION['ID'])){
	
    $startIndex = $_GET['start'];
	
    $pageLimit = $_GET['length'];
    
    $draw = $_GET['draw'];
    
    $customer_id = $_SESSION['ID'];
	
	global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
	$data = array();
    
	$total_records = 0;
	
	if($_GET['module'] == 'Tickets'){
	    
		$ticket_list = fetchData($ws_url, $session_id, $_GET['module'], $customer_id, $pageLimit, $startIndex);
		
		if(!empty($ticket_list['result']['count'])){
		    
		    $total_records = $ticket_list['result']['count'];
		    
		    foreach ($ticket_list['result']['data'] as $index => $ticket){
		        
				$row_data = array(
				    '<a href="ticket-detail.php?record='.$ticket['crmid'].'">
                        '.$ticket['label'].'
                    </a>',
				    $ticket['ticket_no'],
				    $ticket['ticketpriorities'],
				    $ticket['status'],
				    
				    //'<a href="edit-ticket.php?record='.$ticket['crmid'].'">
						//Edit
					//</a>'
				
				    
				);
				array_push($data, $row_data);
			}
		}
	}
	
    $result = array(
		'draw' => $draw,
		'recordsTotal' => $total_records,
		'recordsFiltered' => $total_records,
		'data'=> $data
	);
    
    echo json_encode($result);

}

function fetchData($ws_url, $sessionName, $module, $id, $pageLimit, $startIndex){
    
    $element = array(		
        'id' => $id,
        'module'=>$module,
        'pageLimit' => $pageLimit,
        'startIndex' => $startIndex
    );
    
	$postParams = array(
		'operation'=>'get_related_tickets',
		'sessionName' => $sessionName,
	    'element'=>json_encode($element)
	);
	
	$response = postHttpRequest($ws_url, $postParams);

	$response = json_decode($response, true);
	return $response;
}