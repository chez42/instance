<?php

    include_once('includes/config.php');
    include_once('includes/function.php'); 	
    
    $moduleName = 'Documents';
    
    $recordId = $_REQUEST['file_id'];
    
    $basicFileTypes = array('txt','ics');
    $imageFileTypes = array('image/gif','image/png','image/jpeg');
    $videoFileTypes = array('video/mp4','video/ogg','audio/ogg','video/webm');
    $audioFileTypes = array('audio/mp3','audio/mpeg','audio/wav');
    $opendocumentFileTypes = array('odt','ods','odp','fodt');
    
    $fileDetail = Vtiger_ExternalDownloadLink_Action::getFileDetails($recordId);
    foreach($fileDetail as $fileData){
        $fileDetails = $fileData;
    }
    
    $fileContent = false;
    
    if (!empty ($fileDetails)) {
        
        $filePath = $fileDetails['path'];
        $fileName = $fileDetails['name'];
        
        $fileName = html_entity_decode($fileName, ENT_QUOTES, vglobal('default_charset'));
        $savedFile = $fileDetails['attachmentsid']."_".$fileName;
        
        $fileSize = filesize($filePath.$savedFile);
        $fileSize = $fileSize + ($fileSize % 1024);
        
        if (fopen($filePath.$savedFile, "r")) {
            $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
        }
    }
    
    $path = $fileDetails['path'].$fileDetails['attachmentsid'].'_'.$fileDetails['name'];
    $type = $fileDetails['type'];
    $contents = $fileContent;
    $filename = $fileDetails['name'];
    $parts = explode('.',$filename);
    
    $downloadUrl =  "index.php?module=Vtiger&action=ExternalDownloadLink&record=".$recordId;
    
    $extn = 'txt';
    if(count($parts) > 1){
        $extn = end($parts);
    }
    
    if(in_array($extn,$basicFileTypes))
        $BASIC_FILE_TYPE = 'yes';
    else if(in_array($type,$videoFileTypes))
        $VIDEO_FILE_TYPE = 'yes';
    else if(in_array($type,$imageFileTypes))
        $IMAGE_FILE_TYPE = 'yes';
    else if(in_array($type,$audioFileTypes))
        $AUDIO_FILE_TYPE = 'yes';
    else if (in_array($extn, $opendocumentFileTypes)) {
        $OPENDOCUMENT_FILE_TYPE = 'yes';
        $downloadUrl .= "&type=$extn";
    } else if ($extn == 'pdf') {
        $PDF_FILE_TYPE = 'yes';
    } else {
        $FILE_PREVIEW_NOT_SUPPORTED = 'yes';
    }
    
    $DOWNLOAD_URL = $downloadUrl;
    $FILE_PATH = $path;
    $FILE_NAME = $filename;
    $FILE_EXTN = $extn;
    $FILE_TYPE = $type;
    $FILE_CONTENTS = $contents;
    global $site_URL;
    $SITE_URL = $site_URL;
    
?>
<?php if ($FILE_PREVIEW_NOT_SUPPORTED != 'yes'){?>
    <div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">
        	<div class="modal-header">
        		<div class="filename <?php if ($FILE_PREVIEW_NOT_SUPPORTED != 'yes'){?> col-lg-8 <?php }else{?> col-lg-11 <?php }?>">
                	<h5 class="modal-title" ><?php echo $FILE_NAME ?></h5>
                </div>
                <?php if ($FILE_PREVIEW_NOT_SUPPORTED != 'yes'){?>
                    <div class="col-lg-3">
                    	<a class="btn btn-default btn-small pull-right" target="_blank" href="<?php echo $SITE_URL.'/'.$DOWNLOAD_URL?>"><?php echo vtranslate('LBL_DOWNLOAD_FILE',$moduleName)?></a>
                 	</div>
                 <?php }?>
                 <div class="col-lg-1">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                 </div>
          	</div>
         	
            <div class="modal-body row" >
                <?php if ($FILE_PREVIEW_NOT_SUPPORTED == 'yes'){?>
                    <div class="well" style="height:500px;">
                        <center>
                            <b><?php echo vtranslate('LBL_PREVIEW_NOT_AVAILABLE',$moduleName)?></b>
                            <br><br><br>
                            <a class="btn btn-default btn-large" target="_blank" href="<?php echo $SITE_URL.'/'.$DOWNLOAD_URL ?>"><?php echo vtranslate('LBL_DOWNLOAD_FILE',$moduleName)?></a>
                            <br><br><br><br>
                            <div class='span11 offset1 alert-info' style="padding:10px">
                                <span class='span offset1 alert-info'>
                                    <i class="icon-info-sign"></i>
                                    <?php echo vtranslate('LBL_PREVIEW_SUPPORTED_FILES',$moduleName) ?>
                                </span>
                            </div>
                            <br>
                        </center>
                    </div>
                <?php }else{
                    if ($BASIC_FILE_TYPE == 'yes'){?>
                        <div style="overflow:auto;height:500px;">
                            <pre>
                                <?php echo $FILE_CONTENTS ?>
                            </pre>
                        </div>
                    <?php }else if ($OPENDOCUMENT_FILE_TYPE == 'yes'){?>
                        <iframe id="viewer" src="assets/global/plugins/Viewer.js/#../../../<?php echo $SITE_URL.'/'.$DOWNLOAD_URL?>" width="100%" height="500px" allowfullscreen webkitallowfullscreen></iframe>
                    <?php }else if ($PDF_FILE_TYPE == 'yes'){?>
                        <iframe id='viewer' src="assets/global/plugins/pdfjs/web/viewer.html?file=<?php echo $SITE_URL.'/'.urlencode($DOWNLOAD_URL)?>" height="500px" width="100%"></iframe>
                    
                    <?php }else if ($IMAGE_FILE_TYPE == 'yes'){?>
                        <div style="overflow:auto;height:500px;width:100%;float:left;background-image: url(<?php echo $SITE_URL.'/'.$DOWNLOAD_URL ?>);background-color: #EEEEEE;background-position: center 25%;background-repeat: no-repeat;display: block; background-size: contain;"></div>
                    <?php }else if ($AUDIO_FILE_TYPE == 'yes'){?>
                        <div style="overflow:auto;height:500px;width:100%;float:left;background-color: #EEEEEE;background-position: center 25%;background-repeat: no-repeat;display: block;text-align: center;">
                            <div style="display: inline-block;margin-top : 10%;">
                                <audio controls>
                                    <source src="<?php echo $SITE_URL.'/'.$DOWNLOAD_URL ?>" type="<?php echo $FILE_TYPE; ?>">
                                </audio>
                            </div>
                        </div>
                    <?php }else if ($VIDEO_FILE_TYPE == 'yes'){?>
                        <div style="overflow:auto;height:500px;">
                            <link href="assets/global/plugins/video-js/video-js.css" rel="stylesheet">
                            <script src="assets/global/plugins/video-js/video.js"></script>
                            <video class="video-js vjs-default-skin" controls preload="auto" data-setup="{'techOrder': ['flash', 'html5']}" width="100%" height="500px">
                                <source src="<?php echo $SITE_URL.'/'.$DOWNLOAD_URL ?>" type='<?php $FILE_TYPE ?>' />
                            </video>
                        </div>
                    <?php } ?>
                <?php  } ?>
            </div>
        </div>
      </div>  
<?php }elseif ($FILE_PREVIEW_NOT_SUPPORTED == 'yes'){
	
    echo json_encode(array('downloadUrl'=>$SITE_URL.$DOWNLOAD_URL,'success'=>'true'));
}?>