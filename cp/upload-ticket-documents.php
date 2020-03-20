<?php
include_once('includes/config.php');
include_once('includes/function.php'); 	

if($_REQUEST['ticket_id']){
    
    if(!empty($_FILES['files'])){
       
        $files = $_FILES['files'];
        
        foreach($files['name'] as $key => $file_name){
            
            $_FILES = array();
            
            $_FILES["filename"] = array(
                'name' => $file_name,
                'type' => $files['type'][$key],
                'size' => $files['size'][$key],
                'error' => $files['error'][$key],
                'tmp_name' => $files['tmp_name'][$key],
            );
            
            $_REQUEST['title'] = $file_name;
        
            $_REQUEST['filelocationtype'] = "I";
        
            if(isset($_REQUEST['title']) && $_REQUEST['title']!=""){
            
                $parent_id = $_REQUEST['ticket_id'];
                
                $customerid = $_SESSION['ID'];
            
                $title = $_REQUEST['title'];
                
                $filelocationtype = $_REQUEST['filelocationtype'];
            
                $upload_error = '';
            
                $params = Array(
                    'id' => $parent_id,
                    'title' => $title,
                    'filelocationtype' => $filelocationtype,
                    'parent_id' => $parent_id,
                    'module' => 'HelpDesk',
                    'customer'=>$customerid
                );
            
                if($filelocationtype == 'I' && isset($_FILES['filename']) && !empty($_FILES['filename'])){
                
                    $filename = $_FILES['filename']['name'];
                    $filetype = $_FILES['filename']['type'];
                    $filesize = $_FILES['filename']['size'];
                    $fileerror = $_FILES['filename']['error'];
                    
                    if($fileerror == 4 || $fileerror == 2 || $fileerror == 3){
                       continue;
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
                }
            }
        }
    
    }
    echo json_encode(array("success" => true, "message" => "Document Uploaded Successfully."));
}
