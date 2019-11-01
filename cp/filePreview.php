<?php

    include_once('includes/config.php');
    include_once('includes/function.php'); 	
    
    global $api_username, $api_accesskey, $api_url;
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    $_REQUEST['ID'] = $_SESSION['ID'];
    
    $postParams = array(
        'operation'=>'download_documents',
        'sessionName'=>$session_id,
        'element'=>json_encode($_REQUEST)
    );
    $response = postHttpRequest($ws_url, $postParams);
   
    $response = json_decode($response,true);
    
    $data = $response['result'];
   
    $basicFileTypes = array('txt','ics');
    $imageFileTypes = array('image/gif','image/png','image/jpeg');
    $videoFileTypes = array('video/mp4','video/ogg','audio/ogg','video/webm');
    $audioFileTypes = array('audio/mp3','audio/mpeg','audio/wav');
    $opendocumentFileTypes = array('odt','ods','odp','fodt');
    
    $extn = 'txt';
    if(count($data['parts']) > 1){
        $extn = end($data['parts']);
    }
    $type = $data['type'];
    
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
    
    $DOWNLOAD_URL = $data['downloadUrl'];
    $FILE_NAME = $data['filename'];
    $FILE_TYPE = $data['type'];
    $FILE_CONTENTS = base64_decode($data['contents']);
    $SITE_URL = $data['site_URL'];
    
    $mode = $_REQUEST['mode'];
    
?>
<?php if ($FILE_PREVIEW_NOT_SUPPORTED != 'yes' && $mode == 'preview'){?>
    <div class="modal-dialog modal-lg" role="document">
    	<div class="modal-content">
        	<div class="modal-header">
        		<div class="filename  col-lg-11 " style="word-break: break-word;">
                	<h5 class="modal-title" style="font-size: 1.2rem;"><?php echo $FILE_NAME ?></h5>
                </div>
                <?php /* if ($FILE_PREVIEW_NOT_SUPPORTED != 'yes'){?>
                    <div class="col-lg-3">
                    	<a class="btn btn-default btn-small pull-right" target="_blank" href="<?php echo $SITE_URL.'/'.$DOWNLOAD_URL?>">Download File</a>
                 	</div>
                 <?php }*/ ?>
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
                            <b>Preview Not Available</b>
                            <br><br><br>
                            <a class="btn btn-default btn-large" target="_blank" href="<?php echo $SITE_URL.'/'.$DOWNLOAD_URL ?>">Download File</a>
                            <br><br><br><br>
                            <div class='span11 offset1 alert-info' style="padding:10px">
                                <span class='span offset1 alert-info'>
                                    <i class="icon-info-sign"></i>
                                    <b><strong>Supported File Types : </strong></b>
                                    <br><br><b>Pdf files</b><br>
                                    <b>Text files - </b>txt,csv,ics<br>
                                    <b>Open Document Files - </b>open document text(odt),open document spreadsheet(ods) and open document presentation(odp)<br>
                                    <b>Multimedia files - </b>image, audio & video files<br>
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
                        <iframe id="viewer" src="assets/plugins/Viewer.js/#../../../<?php echo $SITE_URL.'/'.$DOWNLOAD_URL?>" width="100%" height="500px" allowfullscreen webkitallowfullscreen></iframe>
                    <?php }else if ($PDF_FILE_TYPE == 'yes'){?>
                        <iframe id='viewer' src="assets/plugins/pdfjs/web/viewer.html?file=<?php echo $SITE_URL.'/'.urlencode($DOWNLOAD_URL)?>" height="500px" width="100%"></iframe>
                    
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
                            <link href="assets/plugins/video-js/video-js.css" rel="stylesheet">
                            <script src="assets/plugins/video-js/video.js"></script>
                            <video class="video-js vjs-default-skin" controls preload="auto" data-setup="{'techOrder': ['flash', 'html5']}" width="100%" height="500px">
                                <source src="<?php echo $SITE_URL.'/'.$DOWNLOAD_URL ?>" type='<?php $FILE_TYPE ?>' />
                            </video>
                        </div>
                    <?php } ?>
                <?php  } ?>
            </div>
        </div>
      </div>  
<?php }elseif ($FILE_PREVIEW_NOT_SUPPORTED == 'yes' || $mode == 'download'){
	
    echo json_encode(array('downloadUrl'=>$SITE_URL.$DOWNLOAD_URL,'success'=>'true'));
}?>