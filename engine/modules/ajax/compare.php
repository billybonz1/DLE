<?php
session_start();
header('Content-Type: text/xml');
echo "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>"; //windows-1251

$idcomp = $_REQUEST['cid'];
$itcomp = 0; //0

$explcompare = explode(",",$_SESSION['comparer']);
if(!in_array($idcomp, $explcompare)) {
	if($_SESSION['comparer'] != "") {
		$_SESSION['comparer'] .= "," . $idcomp;
	} else {
		$_SESSION['comparer'] .= $idcomp;
	}
	$itcomp = 1;
}

 $compExplode = explode(",", $_SESSION['comparer']);
 $comp = 0; //0

 $comp = count($compExplode);

echo "<compare>";
	echo "<comparea>";
	echo $itcomp;
	echo "</comparea>";
	echo "<compa>";
	echo $comp;
	echo "</compa>";
echo "</compare>";
?>