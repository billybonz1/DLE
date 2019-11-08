<?php
 session_start();
 header('Content-Type: text/xml');
 echo "<?xml version='1.0' encoding='windows-1251' standalone='yes'?>";
 
 $id = $_REQUEST['id'];
 
 $it = 0;
 
 $expl = explode(",", $_SESSION['tovar']);
 
 if(!in_array($id, $expl)){
	$_SESSION['tovar'] .= $id . ",";
	$it = 1;
 }
	echo "<basket>";
		echo $it;
	echo "</basket>";

 
?>