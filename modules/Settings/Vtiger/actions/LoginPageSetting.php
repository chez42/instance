<?php

class Settings_Vtiger_LoginPageSetting_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {
        
        foreach($_FILES as $name => $file){
            if($file['name'] != ''){
                
                $filename = $file['name'];
                
                $filetype = $file['type'];
                
                $filesize = $file['size'];
                
                $filetmp_name = $file['tmp_name'];
                
                $fileError = $file['error'];
                
                if($filesize > 0 && $fileError == 0){
                    
                    $uploadDir = vglobal('root_directory');
                    
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    
                    $imgName = 'test/logo/'.$name.'.'.$ext;
                    
                    $logoName = $uploadDir.'/'.$imgName;
                    
                    move_uploaded_file($filetmp_name, $logoName);
                    
                    if($name == 'logo')
                        $logoPath = $imgName;
                    else if($name == 'background')
                        $backgroundPath = $imgName;
                    
                }
            }
        }
        
        $copyright = $request->get('copyright_text');
        $facebook = $request->get('facebook_link');
        $twitter = $request->get('twitter_link');
        $linkedin = $request->get('linkedin_link');
        $youtube = $request->get('youtube_link');
        $instagram = $request->get('instagram_link');
        
        $db = PearDatabase::getInstance();
        
        $check = $db->pquery("SELECT * FROM vtiger_login_page_settings");
        
        if($db->num_rows($check)){
            $query = "UPDATE vtiger_login_page_settings SET copyright_text=?, facebook_link=?, twitter_link=?, 
            linkedin_link=?, youtube_link=?, instagram_link=? ";
            
            $params = array($copyright, $facebook, $twitter,
                $linkedin, $youtube, $instagram);
            if($logoPath){
                $query .= ", login_logo=? ";
                $params[] = $logoPath; 
            }
            if($backgroundPath){
                $query .= ", login_background=? ";
                $params[] =  $backgroundPath;
            }
          
            $db->pquery($query,$params);
        }else{
            $db->pquery('INSERT INTO vtiger_login_page_settings(login_logo, 
            login_background, copyright_text, facebook_link, twitter_link, 
            linkedin_link, youtube_link, instagram_link) VALUES (?,?,?,?,?,?,?,?)',
            array($logoPath, $backgroundPath, $copyright, $facebook, $twitter,
                $linkedin, $youtube, $instagram));
        }
          
        header('Location: index.php?parent=Settings&module=Vtiger&view=LoginPageSettings');
    }
    
    public function validateRequest(Vtiger_Request $request) {
       
    }
    
}
