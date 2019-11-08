<?php
 session_start();
 header('Content-Type: text/xml');
 echo "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>"; //windows-1251
 $ddb = mysql_connect ("localhost", "lakikras_laki", "Hli1wtwtobz");
 mysql_select_db("lakikras_laki", $ddb);
 mysql_query("SET NAMES cp1251");
 
  
 
 $sesExplode = explode(",", $_SESSION['tovar']);
 $kol = 0;//0
 $prs = 0;//0
 foreach($sesExplode as $key => $value) {
 	if($value != "" && $value != 0) {
		if(!isset($_SESSION["col_" . $value])) {
			$cls = 1; //1
		} else {
			$cls = $_SESSION["col_" . $value];
		}
		 $resultPriceId = mysql_query("SELECT * FROM dle_price WHERE id='$value'");
		 $myrowPriceId  = mysql_fetch_array($resultPriceId);
		 
		 $resultPost = mysql_query("SELECT category FROM dle_post WHERE id='$myrowPriceId[thisid]'");
		 $myrowPost  = mysql_fetch_array($resultPost);
		 
		 $explodeCategory = explode(",", $myrowPost['category']);
		 
		 $resultCategory = mysql_query("SELECT name FROM dle_category WHERE id='$explodeCategory[0]'");
		 $myrowCategory  = mysql_fetch_array($resultCategory);
		 
		 $ex1 = explode(" ", $myrowCategory['name']);
		 $ex2 = explode(",", $ex1[0]);
		 
		 if($ex2[0] == "TIKKURILA") {
			$current = $ex1[0] . " " . $ex1[1];
		} else if ($ex2[0] == "Tikkurila") {
			$current = $ex1[0] . " " . $ex1[1];
		} else if ($ex2[0] == "Teknos") {
			$current = $ex1[0] . " " . $ex1[1] . " " . $ex1[2];
		} else {
			$current = $ex2[0];
		}

		 $resultRazdeli = mysql_query("SELECT * FROM dle_razdeli WHERE proizvoditel LIKE '{$current}%'");
		 $myrowRazdeli  = mysql_fetch_array($resultRazdeli);
		 
		 if(!isset($_COOKIE['dle_user_id'])) {
		 	if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
				$t =  $myrowPriceId['price_yp']*$myrowRazdeli['togrn'];
				$pr = $t+(($t*$myrowRazdeli['nacenka'])/100);
			} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
				$pr = $myrowPriceId['price_yp']*$myrowRazdeli['togrn'];
			} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
				$pr = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazdeli['nacenka'])/100);
			} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
				$pr = $myrowPriceId['price_yp'];
			}
			$pr = round($pr, 2);
			$pr = $pr*$cls;
			$prs += $pr;
			
		 } else {
			if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
				$t =  $myrowPriceId['price_yp']*$myrowRazdeli['togrn'];
				$pr = $t+(($t*$myrowRazdeli['nacenka'])/100);
			} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
				$pr = $myrowPriceId['price_yp']*$myrowRazdeli['togrn'];
			} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
				$pr = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazdeli['nacenka'])/100);
			} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
				$pr = $myrowPriceId['price_yp'];
			}
			$pr = round($pr, 2);
			
			$resultUser = mysql_query("SELECT * FROM dle_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE '{$current}%'");
			$myrowUser  = mysql_fetch_array($resultUser);
			$countUser  = mysql_num_rows($resultUser);
			
			if($countUser != 0) {
				$disc = ($pr*$myrowUser['discount'])/100;
				$disc = round($disc, 2);
				$pr   = $pr-$disc;
			}
			
			$pr = $pr*$cls;
			$prs += $pr;
		 }
		 $kol++;
	}
 }
 $compExplode = explode(",", $_SESSION['comparer']);

 if($compExplode[0] != "" && $compExplode[0]) {
 	$comp = count($compExplode);
 } else {
 	$comp = 0; //0
 }

 
 echo "<response>";
 	echo "<kols>";
		echo $kol;
	echo "</kols>";
	echo "<prss>";
		echo $prs;
	echo "</prss>";
	echo "<compa>";
		echo $comp;
	echo "</compa>";
 echo "</response>";
 

?>