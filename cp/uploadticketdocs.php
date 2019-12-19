<?php
include_once('includes/config.php');
include_once('includes/function.php'); 	
if($_REQUEST['ticket_id']){
    
    if(isset($_FILES['file']) && !empty($_FILES['file']) && !$_FILES['file']['error']){
       
        $fileData = $_FILES;
        
        $_FILES = array();
        
        $_FILES = array("filename" => $fileData['file']);
        
        $_REQUEST['title'] = $fileData['file']['name'];
        
        $_REQUEST['filelocationtype'] = "I";
        
        if(isset($_REQUEST['title']) && $_REQUEST['title']!=""){
            
            $parent_id = $_REQUEST['ticket_id'];
            $customerid = $_SESSION['ID'];
            
            $title = $_REQUEST['title'];
            $filelocationtype = $_REQUEST['filelocationtype'];
            
            $upload_error = '';
            
            $params = Array(
                'id'=>$parent_id,
                'title' => $title,
                'filelocationtype' => $filelocationtype,
                'parent_id'=>$parent_id,
                'module'=>'HelpDesk',
                );
            
            if($filelocationtype == 'I' && isset($_FILES['filename']) && !empty($_FILES['filename'])){
                
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
                echo json_encode(array("success" => true, "message" => "Document Uploaded Successfully."));
                    
            }
            
            if($upload_error)
                echo json_encode(array("error" => true, "message" => $upload_error));
            
        }
    
    }
        
}
