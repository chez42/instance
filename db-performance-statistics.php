<?php
	include("includes/main/WebUI.php");

	global $adb;

	$result = $adb->pquery(" SHOW FULL PROCESSLIST ");
	echo "<table width = '100%'>";
	echo "<tr><td>host</td><td>db</td><td>command</td><td>time</td><td>State</td><td>Info</td></tr>";
	
	for($i = 0; $i < $adb->num_rows($result); $i++){
		$data = $adb->query_result_rowdata($result, $i);
		echo '<tr><td>'.$data['host'].'</td><td>'.$data['db'].'</td><td>'.$data['command'].'</td><td>'.$data['time'].'</td><td>'.$data['state'].'</td><td>'.$data['info'].'</td></tr>';
	}
	echo "</table>";
	
	$result = $adb->pquery("SHOW OPEN TABLES WHERE In_use > 0 ");
	
	echo "<table width = '100%'>";
	echo "<tr><td>database</td><td>table</td><td>in_use</td><td>name_locked</td></tr>";

	for($i = 0; $i < $adb->num_rows($result); $i++){
		$data = $adb->query_result_rowdata($result, $i);
		echo '<tr><td>'.$data['database'].'</td><td>'.$data['table'].'</td><td>'.$data['in_use'].'</td><td>'.$data['name_locked'].'</td></tr>';
	}
	echo "</table>";
	
	
	exit;