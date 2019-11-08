<?php
 header('Content-Type: text/xml');
 echo "<?xml version='1.0' encoding='windows-1251' standalone='yes'?>";
 
 $ddb = mysql_connect ("localhost", "lakikras_laki", "Hli1wtwtobz");
 mysql_select_db("lakikras_laki", $ddb);
 mysql_query("SET NAMES cp1251");
 $b = $_REQUEST['b'];
 $g = $_REQUEST['g'];
	
	$re = mysql_query("SELECT * FROM dle_price WHERE id='$b'");
	$me = mysql_fetch_array($re);
	
	$result = mysql_query("SELECT category FROM dle_post WHERE id='$me[thisid]'");
	$myrow  = mysql_fetch_array($result);
	
	$explodeCat = explode(",", $myrow['category']);
	
	$resultIt = mysql_query("SELECT name FROM dle_category WHERE id='$explodeCat[0]'");
	$myrowIt  = mysql_fetch_array($resultIt);
	
	$fex = explode(" ", $myrowIt['name']);
	$sex = explode(",", $fex[0]);
	
	if($sex[0] == "TIKKURILA") {
		$current = $fex[0] . " " . $fex[1];
	} else if ($sex[0] == "Tikkurila") {
		$current = $fex[0] . " " . $fex[1];
	} else if ($sex[0] == "Teknos") {
		$current = $fex[0] . " " . $fex[1] . " " . $fex[2];
	} else {
		$current = $sex[0];
	}
	
	$resultGetPrice = mysql_query("SELECT togrn, nacenka FROM dle_razdeli WHERE proizvoditel LIKE '{$current}%'");
	$getPrice = mysql_fetch_array($resultGetPrice);
	
	if(mysql_num_rows($resultGetPrice) != 0) {
		$grn = $getPrice['togrn']; 
		$nac = $getPrice['nacenka'];
	} else {$grn = 0; $nac = 0;}
	
	if($grn <= 1) {$sverxPriceYp = $me['price_yp'];} else {
		$sverxPriceYp = $me['price_yp']*$grn;
		$sverxPriceYp = round($sverxPriceYp, 2);
	}
	$nacenkaPriceYp = ($sverxPriceYp*$nac)/100;
	$nacenkaPriceYp = round($nacenkaPriceYp, 2);
	$realPriceYp = $sverxPriceYp + $nacenkaPriceYp;
	
	$resultUser = mysql_query("SELECT * FROM dle_disc WHERE users_id='" . $_COOKIE['dle_user_id'] . "' AND proizv LIKE '{$current}%'");
	$myrowUser  = mysql_fetch_array($resultUser);
	$countUser  = mysql_num_rows($resultUser);
	
	if($countUser != 0) {
		$disc = ($realPriceYp*$myrowUser['discount'])/100;
		$disc = round($disc, 2);
		$realPriceYp   = $realPriceYp-$disc;

	}
	
	$explodePack = explode(" ",$me['pack']);

	$sverxPriceLitr = $realPriceYp/$explodePack[0];
	$sverxPriceLitr = round($sverxPriceLitr, 2);

	$realPriceLitr = $sverxPriceLitr;
	
	$kod = $me['id'];
	$lenkod = strlen($me['id']);
	if($lenkod < 5){
		if($lenkod==1) $kod = "0000".$kod;
		if($lenkod==2) $kod = "000".$kod;
		if($lenkod==3) $kod = "00".$kod;
		if($lenkod==4) $kod = "0".$kod;
	} 
	
	
	
	
	
	
 	echo "<response>";
			
			echo "<b>";
				echo $b;
			echo "</b>";
			
			
			echo "<g>";
				echo $g;
			echo "</g>";
			

			echo "<kod>";
				echo $kod;
			echo "</kod>";
			
			echo "<pack>";
				echo $me['pack'];
			echo "</pack>";


			echo "<cvet>";
				echo $me['cvet'];
			echo "</cvet>";

		
			echo "<blesk>";
				echo $me['blesk'];
			echo "</blesk>";
			
			
			echo "<priceyp>";
				echo $realPriceYp;
			echo "</priceyp>";
			
			
			echo "<pricelitr>";
				echo $realPriceLitr;
			echo "</pricelitr>";
		
		
		
	echo "</response>";
 
?>