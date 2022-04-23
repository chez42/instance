<?php
/*
connect to MS SQL Server using PHP
*/
$serverName = "192.168.100.202";//server-ip 
$connectionInfo = array( "Database"=>"PortfolioCenter", "UID"=>"syncuser", "PWD"=>'Consec11');
$conn = sqlsrv_connect( $serverName, $connectionInfo);
 
if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br /><pre>";
     die( print_r( sqlsrv_errors(), true));
}
?>
