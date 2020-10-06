<?php
include("includes/main/WebUI.php");
include_once("libraries/custodians/cCustodian.php");

echo StatusUpdate::ReadMessage($_POST['code']);