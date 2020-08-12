<?php
if($_REQUEST['source'] == 'MailManager' || $_REQUEST['source'] == 'DocuSign'){
    echo '<script>window.opener.RefreshPage();window.close();</script>';
} else if($_REQUEST['source']  == 'Calendar' || $_REQUEST['source'] == 'Office365Calendar'){
    echo '<script>window.opener.sync();window.close();</script>';
} else if($_REQUEST['source'] == 'ValidateLogin'){
    echo '<script>window.opener.RefreshPage("'.$_REQUEST['code'].'");window.close();</script>';
} else {
    echo '<script>window.close();</script>';
}

exit;