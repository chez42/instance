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
	 
	$ticketIds = array();
	
	if($_GET['module'] == 'Tickets'){
	    $search = array();
	    if(isset($_REQUEST['title']) && $_REQUEST['title'] != ''){
	        $search['title'] = $_REQUEST['title'];
	    }
	    if(isset($_REQUEST['ticket_number']) && $_REQUEST['ticket_number'] != ''){
	        $search['ticket_no'] = $_REQUEST['ticket_number'];
	    }
	    if(isset($_REQUEST['priority']) && $_REQUEST['priority'] != ''){
	        $search['priority'] = $_REQUEST['priority'];
	    }
	    if(isset($_REQUEST['status']) && $_REQUEST['status'] != ''){
	        $search['status'] = $_REQUEST['status'];
	    }
	    if(isset($_REQUEST['open_days']) && $_REQUEST['open_days'] != ''){
	        $search['cf_3272'] = $_REQUEST['open_days'];
	    }
	    if(isset($_REQUEST['due_date']) && $_REQUEST['due_date'] != ''){
	        $search['cf_656'] = date('Y-m-d', strtotime($_REQUEST['due_date']));
	    }
	    if(isset($_REQUEST['last_modified']) && $_REQUEST['last_modified'] != ''){
	        $search['modifiedtime'] = date('Y-m-d', strtotime($_REQUEST['last_modified']));
	    }
	    if(isset($_REQUEST['category']) && $_REQUEST['category'] != ''){
	        $search['category'] = $_REQUEST['category'];
	    }
	   
	    $ticket_list = fetchData($ws_url, $session_id, $_GET['module'], $customer_id, $pageLimit, $startIndex,$search);
	    
		if(!empty($ticket_list['result']['count'])){
		    
		    $total_records = $ticket_list['result']['count'];
		    
		    $ticketIds[] = $ticket_list['result']['ticket_ids'];
		    //$data[] = array('Title','Ticket Number','Priority','Status');
		    foreach ($ticket_list['result']['data'] as $index => $ticket){
		        
				$row_data = array(
				    '<a href="ticket-detail.php?record='.$ticket['ticketid'].'">
                        '.$ticket['title'].'
                    </a>',
				    $ticket['ticket_no'],
				    $ticket['priority'],
				    $ticket['ticket_status'],
				    $ticket['cf_3272'],
				    date('m-d-Y', strtotime($ticket['cf_656'])),
				    date('m-d-Y', strtotime($ticket['modifiedtime']))
				    
				    //'<a href="edit-ticket.php?record='.$ticket['ticketid'].'">
						//Edit
					//</a>'
				
				    
				);
				array_push($data, $row_data);
			}
		}
		
		$result = array(
		    'draw' => $draw,
		    'recordsTotal' => $total_records,
		    'recordsFiltered' => $total_records,
		    'data'=> $data
		);
		
		$_SESSION['ticket_detail_navigation'] = $ticketIds;
		
		echo json_encode($result);
		
	}else if($_GET['module'] == 'TicketDocuments'){
	    
	    $element = array('ID' => $customer_id, 'ticket_id' => $_GET['ticket_id']);
	    
	    $postParams = array(
	        'operation'=>'get_ticket_documents',
	        'sessionName'=>$session_id,
	        'element'=>json_encode($element)
	    );
	    
	    $response = postHttpRequest($ws_url, $postParams);
	    
	    $response = json_decode($response,true);
	    
	    $ticket_docs = $response['result'];
	    
	    echo fetchTicketDocuments($ticket_docs);
	    
	}
	
    

}

function fetchData($ws_url, $sessionName, $module, $id, $pageLimit, $startIndex, $seacrh){
    
    $element = array(		
        'id' => $id,
        'module'=>$module,
        'pageLimit' => $pageLimit,
        'startIndex' => $startIndex
    );
    $element = array_merge($element, $seacrh);
    
	$postParams = array(
		'operation'=>'get_related_tickets',
		'sessionName' => $sessionName,
	    'element'=>json_encode($element)
	);
	
	$response = postHttpRequest($ws_url, $postParams);
	
	$response = json_decode($response, true);
	return $response;
}

function fetchTicketDocuments($ticket_docs){
    
    $html = '<div class="kt-portlet__body ticketDocList">
	    <div class="kt-widget4">';
    if(!empty($ticket_docs)){
        foreach($ticket_docs as $document){
            $html .= '<div class="kt-widget4__item">
					<div class="kt-widget4__pic kt-widget4__pic--pic">
						<img style="border-radius:10px;" src="images/'. $document['icon'] .'" />
					</div>
					<div class="kt-widget4__info ticketinfo">
						<a href="javascript:void(0)" data-filelocationtype="'. $document['filelocationtype'] .'"
							data-filename="'.$document['docname'] .'" data-fileid="'. $document['notesid'] .'"
							class="kt-widget4__username" style="font-size: 0.9rem !important;"title="Preview">
							'. $document['title'] .'
						</a>
						<p class="kt-widget4__text">
							<span class="document_preview" title="Preview" style="font-size:1.5em!important;">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
										<path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" opacity="0.3"/>
									</g>
								</svg>
							</span>&nbsp;&nbsp;
							<span class="document_download" title="Download" style="font-size:1.5em!important;">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
										<rect fill="#000000" opacity="0.3" transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) " x="11" y="1" width="2" height="14" rx="1"/>
										<path d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) "/>
									</g>
								</svg>
							</span>
						</p>
					</div>
				</div>';
        }
    }
    $html.= '</div>
    	</div>';
    return $html;
}