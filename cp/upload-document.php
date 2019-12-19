<?php
	
	include_once('includes/config.php'); 	
	include_once('includes/function.php'); 			
	if(isset($_REQUEST['drag']) && $_REQUEST['drag'] == true && isset($_FILES['file']) && !empty($_FILES['file']) && !$_FILES['file']['error']){
			
		$fileData = $_FILES;
		
		$_FILES = array();
		
		$_FILES = array("filename" => $fileData['file']);
		
		$_REQUEST['title'] = $fileData['file']['name'];
		
		$_REQUEST['filelocationtype'] = "I";
	}
	
	if(isset($_REQUEST['title']) && $_REQUEST['title']!=""){
       
		$parent_id = $_SESSION['ID'];
		$customerid = $_SESSION['ID'];
	
		$title = $_REQUEST['title'];
		$description = $_REQUEST['description'];
		$filelocationtype = $_REQUEST['filelocationtype'];
		
		$upload_error = '';

		$params = Array(
			'id'=>$customerid,
			'title' => $title,
			'description' => $description,
			'filelocationtype' => $filelocationtype,
			'parent_id'=>$parent_id,
			'module'=>$module,
		);
		if($_REQUEST['doc_folder_id'])
		    $params['doc_folder_id'] = $_REQUEST['doc_folder_id'];
		if($filelocationtype == 'E')
			$params['filename'] = $_REQUEST['filename'];
		else if($filelocationtype == 'I' && isset($_FILES['filename']) && !empty($_FILES['filename'])){
		
			$filename = $_FILES['filename']['name'];
			$filetype = $_FILES['filename']['type'];
			$filesize = $_FILES['filename']['size'];
			$fileerror = $_FILES['filename']['error'];
			
			if($fileerror == 4){
				$upload_error = 'LBL_GIVE_VALID_FILE';
			} elseif($fileerror == 2){
				$upload_error = 'LBL_UPLOAD_FILE_LARGE';
			} elseif($fileerror == 3){
				$upload_error = 'LBL_PROBLEM_UPLOAD';
			}
			
			$upload_dir = 'files';
		
			if($filesize > 0){
			
				if(move_uploaded_file($_FILES["filename"]["tmp_name"],$upload_dir.'/'.$filename)){
					$filecontents = base64_encode(fread(fopen($upload_dir.'/'.$filename, "r"), $filesize));
				}
	
				$params['filename'] = $filename;
				$params['filetype'] = $filetype;
				$params['filesize'] = $filesize;
				$params['filecontents'] = $filecontents;
			} else {
				$upload_error = 'LBL_UPLOAD_VALID_FILE';
			}
		}		
		
		$result = add_document_attachment(($params));
		$docid = $result['new_document']['document_id'];
		if(isset($docid) && $docid != ''){
			
			unlink($upload_dir.'/'.$filename);

			if(isset($_REQUEST['drag']) && $_REQUEST['drag'] == true){
				
				echo json_encode(array("success" => true, "message" => "Document Uploaded Successfully."));
				
			} else
				header("Location: document.php?module=Documents&id=".$docid);
		}
		
		if($upload_error)
			echo json_encode(array("error" => true, "message" => $upload_error));
	}
?>