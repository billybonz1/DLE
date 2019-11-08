<?php
if(isset($_REQUEST['do']) && $_REQUEST['do'] == "compare") {
	$userId = $_SESSION['dle_user_id'];
	
	
	
	require_once ROOT_DIR.'/engine/modules/show.short.php';
	
	
	$explodeCompare = explode(",", $_SESSION['compare']);
	$countCompare = count($explodeCompare);
	
	if($_REQUEST['act'] != "print") {
	if($countCompare != 0 && $explodeCompare[0] != "") {
	$compare  = "<table style='background-color:#eff6ff;' width='98%' id='basktable' align='center'>";
	$compare .= "
					<tr>
						<td width='50%'><p class='nazv123'>Название</p></td>
						<td width='25%'><p class='nazv123'>Производитель</p></td>
						<td width='25%'><p class='nazv123'>Действие</p></td>
					</tr>
				";
		foreach($explodeCompare as $key => $value) {
			if($value != "" && $value != 0) {
			$resultTovar = $db->query("SELECT id, title, category FROM " . PREFIX . "_post WHERE id='{$value}'"); 
			$myrowTovar  = $db->get_array($resultTovar);
			
			$proizvoditel = explode(",", $myrowTovar['category']);
			
			$resultProiz = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='{$proizvoditel[0]}'");
			$myrowProiz  = $db->get_array($resultProiz);
			$t = str_replace("\"","'",$myrowTovar['title']);
			$t = str_replace("\'","\"",$t);
			$compare .=  "
							<tr>
								<td width='50%'><p class='nazv12' style='color:#0a3a6a;'>{$t}</p></td>
								<td width='25%'><p class='nazv123' style='color:#0a3a6a;'>{$myrowProiz['name']}</p></td>
								<td width='25%'><p class='nazv123'><a 

href='https://laki-kraski.com.ua/index.php?do=compare&del={$value}' style='color:#FF0000; text-decoration:none;'>X <span style='color:#0a3a6a;'>[Убрать товар из 

сравнения]</span></a></p></td>
							</tr>
					     ";
			}
		}
	
	$compare .= "
		<tr>
			<td align='center'><br><a href='https://laki-kraski.com.ua/index.php?do=compare&del=all' style='color:#FF0000; text-decoration:none; 

font-size:12px;'><strong>X</strong> <span style='color:#0a3a6a;'><strong>Очистить таблицу сравнения</strong></span></a><br><br></td>
			<td align='center'><br><a href='https://laki-kraski.com.ua/index.php?do=compare&act=word' style='color:#FF0000; text-decoration:none; 

font-size:12px;'><span style='color:#0a3a6a;'><strong>Сохранить в документ Microsoft Word</strong></span></a><br><br>
			</td>
			<td>
				<p style='clear:both; float:right; margin-right:55px; margin-top:0px;'><a 

href='https://laki-kraski.com.ua/index.php?do=compare&act=print'><img src='https://laki-kraski.com.ua/templates/real/images/buttons/raspe4atatb.png'></a></p>
			</td>
		</tr>
	</table>";
	} else {
		$compare = "<div align='center'><p align='center' style='font-size:22px; color:#333;'>Товары для сравнения отсутствуют.</p></div>";
		$errr = "1";
	}
	}

	
	
	
	
	if($countCompare != 0) {
	foreach($explodeCompare as $key => $value) {
	if($value != "" && $value != 0) {
	$resultTovar = $db->query("SELECT * FROM " . PREFIX . "_post WHERE id='{$value}'");
    $myrowTovar  = $db->get_array($resultTovar);
	
	
	
	$countPrice = 0;
			$ff = new db;
			$resultPrice = $ff->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$myrowTovar[id]}' ORDER BY K DESC, tm DESC");
			$myrowPrice = $ff->get_array($resultPrice);
			$countPrice = $ff->num_rows($resultPrice);
			
			
			if($countPrice != 0){
				$hidkod = "";
				$hidPack = "";
				$hidPackword = "
					<tr><td><p style='font-size:11px; font-weight:bold;width:80px;text-align:center;'>код продукции</p></td><td><p style='font-size:11px; font-weight:bold;width:80px;text-align:center;'>упаковка</p></td><td><p style='font-size:11px; font-weight:bold;width:80px;text-align:center;'>база и цвет</p></td><td><p style='font-size:11px; font-weight:bold;width:80px;text-align:center;'>степень блеска</p></td><td><p style='font-size:11px; font-weight:bold;width:80px;text-align:center;'>цена упаковки</p></td><td><p style='font-size:11px; font-weight:bold;width:80px;text-align:center;'>цена за 1л</p></td></tr>";
				$hidCvet = "";
				$hidBlesk = "";
				$hidPriceYp = "";
				$hidPriceLitr = "";
				
			$numsCvet = 0;
				$numsBlesk = 0;
				$numsPriceYp = 0;
				$numsPriceLitr = 0;
				
				
				$resultWidth = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$myrowTovar[id]}'");
				$myrowWidth = $db->get_array($resultWidth);
				
				if($db->num_rows($resultWidth) != 0){
					do {
						if(strlen($myrowWidth['cvet']) > $numsCvet) {$numsCvet = strlen($myrowWidth['cvet']);}
						if(strlen($myrowWidth['blesk']) > $numsBlesk) {$numsBlesk = strlen($myrowWidth['blesk']);}
						if(strlen($myrowWidth['price_yp']) > $numsPriceYp) {$numsPriceYp = strlen($myrowWidth['price_yp']);}
						if(strlen($myrowWidth['price_litr']) > $numsPriceLitr) {$numsPriceLitr = strlen($myrowWidth['price_litr']);}
						
					}while($myrowWidth = $db->get_array($resultWidth));
					$numsPriceYp = $numsPriceYp+4;
					$numsPriceLitr = $numsPriceLitr+4;
					$widthCvet = $numsCvet*8;
					$widthBlesk = $numsBlesk*8;
					$widthPriceYp = $numsPriceYp*8;
					$widthPriceLitr = $numsPriceLitr*9;
					
					if($widthCvet < 77) {$widthCvet = 77;}
					if($widthCvet > 230) {$widthCvet = 230;}
					if($widthBlesk < 100) {$widthBlesk = 100;}
					if($widthPriceYp < 102) {$widthPriceYp = 102;}
					if($widthPriceLitr < 70) {$widthPriceLitr = 70;}
					
					
				}
				
				$resultRow = $db->query("SELECT category FROM " . PREFIX . "_post WHERE id='$myrowTovar[id]'");
				$row = $db->get_array($resultRow);
				$rowCa = explode(",", $row['category'] );
				
				$resultGetCat = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='{$rowCa[0]}'");
				$myrowGetCat = $db->get_array($resultGetCat);
				
				$ex1 = explode(" ", $myrowGetCat['name']);
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
				
				
				 $resultRazdeli = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '{$current}%'");
				 $myrowRazdeli  = $db->get_array($resultRazdeli);
				
				 if(!isset($_COOKIE['dle_user_id'])) {
					if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
						$t = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
						$pr = $t+(($t*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
						$pr = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
						$pr = $myrowPrice['price_yp']+(($myrowPrice['price_yp']*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
						$pr = $myrowPrice['price_yp'];
					}
					$pr = round($pr, 2);
					
					
					
				 } else {
					if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
						$t = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
						$pr = $t+(($t*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
						$pr = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
						$pr = $myrowPrice['price_yp']+(($myrowPrice['price_yp']*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
						$pr = $myrowPrice['price_yp'];
					}
					
					$pr = round($pr, 2);
					
					$resultUser = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE 

'{$current}%'");
					$myrowUser  = $db->get_array($resultUser);
					$countUser  = $db->num_rows($resultUser);
					
					if($countUser != 0) {
						$disc = ($pr*$myrowUser['discount'])/100;
						$disc = round($disc, 2);
						$pr   = $pr-$disc;
					}
					
					
					
					
				 }
				 	$mp = explode(" ", $myrowPrice['pack']);
					if($mp[0] != 0) {
						 $prl = $pr/$mp[0];
						 $prl = round($prl, 2);
					 } else {
						$prl = 0;
					 }
					/*----------------------*/	
				
				$numIt = 0;
				
				do {
				
					if(!isset($_COOKIE['dle_user_id'])) {
					if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
						$t = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
						$pr2 = $t+(($t*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
						$pr2 = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
						$pr2 = $myrowPrice['price_yp']+(($myrowPrice['price_yp']*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
						$pr2 = $myrowPrice['price_yp'];
					}
					$pr2 = round($pr2, 2);
					
					
					
				 } else {
					if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
						$t = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
						$pr2 = $t+(($t*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
						$pr2 = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
						$pr2 = $myrowPrice['price_yp']+(($myrowPrice['price_yp']*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
						$pr2 = $myrowPrice['price_yp'];
					}
					
					$pr2 = round($pr2, 2);
					
					$resultUser = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE 

'{$current}%'");
					$myrowUser  = $db->get_array($resultUser);
					$countUser  = $db->num_rows($resultUser);
					
					if($countUser != 0) {
						$disc2 = ($pr2*$myrowUser['discount'])/100;
						$disc2 = round($disc2, 2);
						$pr2   = $pr2-$disc2;
					}
					
					
					
					
				 }
				 	$mp2 = explode(" ", $myrowPrice['pack']);
					if ($mp2[0] == 0) $mp2[0] = 1;
					$prl2 = $pr2/$mp2[0];
					$prl2 = round($prl2, 2);
					
					$hidPack .= "
					<div class='tag11{$myrowTovar[id]}' id='tag11' onmouseover='fade({$numIt},{$myrowTovar[id]})' 

onmouseout='fadeout({$numIt},{$myrowTovar[id]})' onclick='process({$myrowTovar[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$myrowPrice['pack']}</p></div>
					";
					$hidCvet .= "<div class='tag44{$myrowTovar[id]}' id='tag44' onmouseover='fade({$numIt},{$myrowTovar[id]})' 

onmouseout='fadeout({$numIt},{$myrowTovar[id]})' style='width:{$widthCvet}px;'><p>{$myrowPrice['cvet']}</p></div>";
					$hidBlesk .= "<div class='tag55{$myrowTovar[id]}' id='tag55' onmouseover='fade({$numIt},{$myrowTovar[id]})' 

onmouseout='fadeout({$numIt},{$myrowTovar[id]})' style='width:{$widthBlesk}px;'><p>{$myrowPrice['blesk']}</p></div>";
					$hidPriceYp .= "<div class='tag22{$myrowTovar[id]}' id='tag22' onmouseover='fade({$numIt},{$myrowTovar[id]})' 

onmouseout='fadeout({$numIt},{$myrowTovar[id]})' style='width:{$widthPriceYp}px;'><p>{$pr2} грн</p></div>";
					$hidPriceLitr .= "<div class='tag33{$myrowTovar[id]}' id='tag33' onmouseover='fade({$numIt},{$myrowTovar[id]})' 

onmouseout='fadeout({$numIt},{$myrowTovar[id]})' style='width:{$widthPriceLitr}px;'><p>{$prl2} грн</p></div>";

					$kod = $myrowPrice['id'];
					$lenkod = strlen($myrowPrice['id']);
					if($lenkod < 5){
						if($lenkod==1) $kod = "0000".$kod;
						if($lenkod==2) $kod = "000".$kod;
						if($lenkod==3) $kod = "00".$kod;
						if($lenkod==4) $kod = "0".$kod;
					} 
				
					$hidkod .= "
					<div class='tag66{$myrowTovar[id]}' id='tag66' onmouseover='fade({$numIt},{$myrowTovar[id]})' onmouseout='fadeout({$numIt},{$myrowTovar[id]})' onclick='process({$myrowTovar[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$kod}</p></div>
					";

					$hidPackword .= "
					<tr><td><p style='text-align:center; font-size:12px;'>{$kod}</p></td><td><p style='text-align:center; font-size:12px;'>{$myrowPrice['pack']}</p></td><td><p style='text-align:center; font-size:12px;'>{$myrowPrice['cvet']}</p></td><td><p style='text-align:center; font-size:12px;'>{$myrowPrice['blesk']}</p></td><td><p style='text-align:center; font-size:12px;'>{$pr2} грн</p></td><td><p style='text-align:center; font-size:12px;'>{$prl2} грн</p></td></tr>";
					
					
					$numIt++;

				} while($myrowPrice = $ff->get_array($resultPrice));
				
				
				$resultPrice = $ff->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$myrowTovar[id]}' ORDER BY K DESC, tm DESC");
				$myrowPrice = $ff->get_array($resultPrice);
				$countPrice = $ff->num_rows($resultPrice);
				
				if($countPrice > 1) {
					$raskrSpis = "<img src='{THEME}/images/razv.png' style='margin-left:5px;'>";
				} else {
					$raskrSpis = "";
				}
				
				$tf = new db;
				$resultTf = $tf->query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$myrowTovar[id]}'");
				$myrowTf = $tf->get_array($resultTf);
				
				if($myrowTf['cvet_g'] == "true") {
				
				$ypak = "<div style='float:left;' id='toniz'>
								<p id='nazvkol2' style='letter-spacing:2px; text-align:center;'>упаковка</p>
								<div class='tag4' id='num2{$myrowTovar[id]}' onclick='cng({$myrowTovar[id]})' style='width:67px;'><p>{$myrowPrice['pack']}</p></div>
								<div style='display:none;' id='upack{$myrowTovar[id]}'><br><br>{$hidPack}<br><br></div>
							</div>";				
				
				$kod = $myrowPrice['id'];
				$lenkod = strlen($myrowPrice['id']);
				if($lenkod < 5){
					if($lenkod==1) $kod = "0000".$kod;
					if($lenkod==2) $kod = "000".$kod;
					if($lenkod==3) $kod = "00".$kod;
					if($lenkod==4) $kod = "0".$kod;
				} 
				$kodp = "<div style='float:left;' id='toniz'>
								<p id='nazvkol2' style='letter-spacing:2px; padding-right:5px;'>Код прод.</p>
								<div class='tag6' id='num{$myrowTovar[id]}' onclick='cng({$myrowTovar[id]})' style='cursor:pointer;'><p>{$kod}$raskrSpis</p></div>
								<div style='display:none;' id='{$myrowTovar[id]}'><br><br>{$hidkod}<br><br></div>
							</div>";
							
				} else {$ypak = "<div style='float:left;' id='{$myrowTovar[id]}'><div id='num{$myrowTovar[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['pack_g'] == "true") {
					$cvet = "<div style='float:left;' id='toniz'>
								<p id='nazvkol2' style='letter-spacing:2px; text-align:center;'>база и цвет</p>
								<div class='tag4' id='num3{$myrowTovar[id]}' onclick='cng({$myrowTovar[id]})' 

style='width:{$widthCvet}px;'><p>{$myrowPrice['cvet']}</p></div>
								<div style='display:none;' id='cvet{$myrowTovar[id]}'><br><br>{$hidCvet}<br><br></div>
							</div>";
				} else {$cvet = "<div style='float:left;' id='cvet{$myrowTovar[id]}'><div id='num3{$myrowTovar[id]}' 

style='display:none;'></div></div>";}
				
				if($myrowTf['blesk_g'] == "true") {
				$blesk = "<div style='float:left;' id='toniz'>
							<p id='nazvkol' style='letter-spacing:2px; text-align:center;'>степень блеска</p>
							<div class='tag5' id='num4{$myrowTovar[id]}' onclick='cng({$myrowTovar[id]})' 

style='width:{$widthBlesk}px;'><p>{$myrowPrice['blesk']}</p></div>
							<div style='display:none;' id='blesk{$myrowTovar[id]}'><br><br>{$hidBlesk}<br><br></div>
						</div>";
				} else {$blesk = "<div style='float:left;' id='blesk{$myrowTovar[id]}'><div id='num4{$myrowTovar[id]}' 

style='display:none;'></div></div>";}
				
				if($myrowTf['price_yp_g'] == "true") {
				$styp = "<div style='float:left;' id='toniz'>
							<p id='nazvkol4' style='letter-spacing:2px; text-align:center;'>цена упаковки</p>
							<div class='tag2' id='num5{$myrowTovar[id]}' onclick='cng({$myrowTovar[id]})' 

style='width:{$widthPriceYp}px;'><p>{$pr} грн</p></div>
							<div style='display:none;' id='priceyp{$myrowTovar[id]}'><br><br>{$hidPriceYp}<br><br></div>
						</div>";
				} else {$styp = "<div style='float:left;' id='priceyp{$myrowTovar[id]}'><div id='num5{$myrowTovar[id]}'> 

style='display:none;'</div></div>";}
				
				if($myrowTf['price_litr_g'] == "true") {
				$stl = "<div style='float:left;' id='toniz'>
							<p id='nazvkol' style='letter-spacing:2px; text-align:center;'> цена за 1л</p>
							<div class='tag3' id='num6{$myrowTovar[id]}' onclick='cng({$myrowTovar[id]})' 

style='width:{$widthPriceLitr}px;'><p>{$prl} грн</p></div>
							<div style='display:none;' id='pricelitr{$myrowTovar[id]}'><br><br>{$hidPriceLitr}<br><br></div>
						</div>";
				} else {$stl = "<div style='float:left;' id='pricelitr{$myrowTovar[id]}'><div id='num6{$myrowTovar[id]}' 

style='display:none;'></div></div>";}
				
				
				$price = "
				  <div style='clear:both; float;left; margin-right:130px; margin-top:-90px;'  id='showlogin' class='showlogin'>	
						{$kodp}{$ypak}{$cvet}{$blesk}{$styp}{$stl}
						<div class='showing'>
							<a href='' onclick='compare({$myrowTovar[id]}); return false;' style='margin-top:33px; cursor:pointer;'><img src='{THEME}/images/buttons/compare.png'></a>
							<a onclick='intobusket({$myrowTovar[id]}); return false;' style='cursor:pointer; margin:0px; padding:0px;'><img src='{THEME}/images/buttons/buy.png'></a>
							<a href='/index.php?do=feedback'><img src='{THEME}/images/buttons/complane.png'></a>
							<a href='{$full_link}'><img src='{THEME}/images/buttons/podrobnee.png' alt='Подробнее' border='0'></a>
						</div>
						<div style='float:left; width:75px; margin-top:35px; margin-left:7px; margin-right:-15px;' class='hiden'>
							<a href='' onclick='compare({$myrowTovar[id]}); return false;' style='margin-top:33px; cursor:pointer;'><img src='{THEME}/images/buttons/compare.png'></a>
							<a onclick='intobusket({$myrowTovar[id]}); return false;' style='cursor:pointer; margin:0px; padding:0px;'><img src='{THEME}/images/buttons/buy.png'></a>
						</div>
						
						<form>
							<input type='hidden' id='idd{$myrowTovar[id]}' value='{$myrowPrice[id]}'>
						</form>
				  </div>
				  <div id='pojceny' class='hiden'><a href='/index.php?do=feedback'><img src='{THEME}/images/buttons/complane.png'></a><br><br><br><br><br></div>";
				  
				  
				  
				  
				  
				 if($myrowTf['cvet_g'] == "true") {
				
				$ypak = "<div style='float:left;' id='toniz'>
								<p style='font-size:11px; font-weight:bold;width:80px;text-align:center;'>упаковка</p>
								<div style='text-align:center;'>{$hidPack}<br></div>
							</div>";
				
			
				$kodp = "<div style='float:left;' id='toniz'>
								<p style='font-size:11px; font-weight:bold;width:80px;text-align:center;'>Код прод.</p>
								<div style='text-align:center;'>{$hidkod}<br></div>
							</div>";
				
			
				} else {$ypak = "<div style='float:left;' id='{$myrowTovar[id]}'><div id='num{$myrowTovar[id]}' style='display:none;'></div></div>";}
				
				  
				  if($myrowTf['pack_g'] == "true") {
					$cvet = "<div style='float:left;' id='toniz'>
								<p style='font-size:11px; font-weight:bold;width:100px;text-align:center;'>база и цвет</p>
								
								<div style='text-align:center;'>{$hidCvet}<br></div>
							</div>";
				} else {$cvet = "<div style='float:left;' id='cvet{$myrowTovar[id]}'><div id='num3{$myrowTovar[id]}' 

style='display:none;'></div></div>";}
				
				if($myrowTf['blesk_g'] == "true") {
				$blesk = "<div style='float:left;' id='toniz'>
							<p style='font-size:11px; font-weight:bold;width:100px;text-align:center;'>степень блеска</p>
							
							<div style='text-align:center;'>{$hidBlesk}<br></div>
						</div>";
				} else {$blesk = "<div style='float:left;' id='blesk{$myrowTovar[id]}'><div id='num4{$myrowTovar[id]}' 

style='display:none;'></div></div>";}
				
				if($myrowTf['price_yp_g'] == "true") {
				$styp = "<div style='float:left;' id='toniz'>
							<p style='font-size:11px; font-weight:bold;width:100px;text-align:center;'>цена упаковки</p>
							
							<div style='text-align:center;'>{$hidPriceYp}<br></div>
						</div>";
				} else {$styp = "<div style='float:left;' id='priceyp{$myrowTovar[id]}'><div id='num5{$myrowTovar[id]}'> 

style='display:none;'</div></div>";}
				
				if($myrowTf['price_litr_g'] == "true") {
				$stl = "<div style='float:left;' id='toniz'>
							<p style='font-size:11px; font-weight:bold;width:100px;text-align:center;'> цена за 1л</p>
							
							<div style='text-align:center;'>{$hidPriceLitr}<br></div>
						</div>";
				} else {$stl = "<div style='float:left;' id='pricelitr{$myrowTovar[id]}'><div id='num6{$myrowTovar[id]}' 

style='display:none;'></div></div>";}
				
				  $price2 = "
				  <div style='margin-top:-27px;'  id='showlogin'>	
						{$kodp}{$ypak}{$cvet}{$blesk}{$styp}{$stl}
				  </div>
				  ";
				  

			}
	
	require_once ROOT_DIR.'/engine/modules/functions.php';

	$result = $db->query("SELECT * FROM " . PREFIX . "_post WHERE id='{$myrowCompare[idcat]}'");
	$row = $db->get_array($result);
	$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
	//$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "" . $row['id'] . "-" . $row['alt_name'] . ".html";
	
	
	
	$s = str_replace("\"","'",$myrowTovar['short_story']);
	$s = str_replace("\'","'",$s);
	
	$t = str_replace("\"","'",$myrowTovar['title']);
	$t = str_replace("\'","\"",$t);
	
	
	if(!isset($_REQUEST['act'])) {
	$compare .= "
              <table width='100%' border='0' cellspacing='0' cellpadding='0' class='productTable'>
              <tr>
                  <td colspan='3' align='left' valign='top' class='titleBgP'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <tr>
                        <td align='center' valign='middle' width='45'><img src='{THEME}/images/dlet_artblock_22_01.gif' style='clear:both; float:left; margin-top:1px; 

margin-left:20px;'></td>
                        <td align='left' style='padding:10px 0'><span class='ntitle'>{$t}</span></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td width='10' align='left' valign='top'>&nbsp;</td>
                  <td align='left' valign='top' class='news' id='comparenews'>{$s}<br></td>
                  <td width='10' align='right' valign='top'>&nbsp;</td>
                </tr>
                <tr>
                  <td width='10' align='left' valign='top'></td>
                  <td align='left' valign='top' style='text-align:right; padding-bottom:32px;'>
                    <div style='position:relative; top:25px;' class='costs'>
                    	
                        {$price}
						
                        <a href='{$full_link}' class='hiden'><img src='{THEME}/images/buttons/podrobnee.png' alt='Подробнее' border='0' style='float:left; margin-left:25px; position:relative; margin-top:-4px;></a>
                    </div>
                  </td>
                  <td width='10' align='right' valign='top'></td>
                </tr>
				
              </table>
	";
	
	} else if($_REQUEST['act'] == "print"){
		
		
			

	echo "
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<title>Распечатать</title>
</head>
<body>
              <table width='700' border='0' cellspacing='0' cellpadding='0' style='border:1px #000 dashed; page-break-inside: avoid' align='center'>
              <tr>
                  <td colspan='3' align='left' valign='top' class='titleBgP'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <tr>
                        <td align='left' style='padding:10px 0' colspan='2'><span class='ntitle' style='padding-left:10px;'><strong>{$t}</strong></span></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td width='10' align='left' valign='top'>&nbsp;</td>
                  <td align='left' valign='top' class='news' id='comparenews'>{$s}<br></td>
                  <td width='10' align='right' valign='top'>&nbsp;</td>
                </tr>
                <tr>
                  <td width='10' align='left' valign='top'></td>
                  <td align='left' valign='top' style='text-align:right; padding-bottom:5px;'>
                    <div style='margin-top:20px;'>
                        ";
	
	echo "{$price2}";			
	echo "
                    </div>
                  </td>
                  <td width='10' align='right' valign='top'>&nbsp;</td>
                </tr>
              </table>
</body>
</html>
	";
		
		
	}
	else if($_REQUEST['act'] == "word"){
	$get_text .= "
              <table width='700' border='0' cellspacing='0' cellpadding='0' style='border:1px #000 dashed; page-break-inside: avoid' align='center'>
              <tr>
                  <td colspan='3' align='left' valign='top' class='titleBgP'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                      <tr>
                        <td align='left' style='padding:10px 0' colspan='2'><span class='ntitle' style='padding-left:10px;'><strong>{$t}</strong></span></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td width='10' align='left' valign='top'>&nbsp;</td>
                  <td align='left' valign='top' class='news' id='comparenews'>{$s}<br></td>
                  <td width='10' align='right' valign='top'>&nbsp;</td>
                </tr>
                <tr>
                  <td width='10' align='left' valign='top'></td>
                  <td align='left' valign='top' style='text-align:right; padding-bottom:5px;'>
                    
					<table style='width:100%;'>
                    $hidPackword
                    </table>
					
                  </td>
                  <td width='10' align='right' valign='top'>&nbsp;</td>
                </tr>
              </table><br>
	";
	}
	}
	}
		if($_REQUEST['act'] == "word"){
			$filename = 'uploads/temp/'.$_REQUEST[PHPSESSID];
		//выделяем базовый домен, пригодится
		$base_link="https://laki-kraski.com.ua";
		require_once ROOT_DIR.'/engine/modules/libword.php';
		//создаём объект, который и будет генерировать нам конечный mht
		$MhtFileMaker = new MhtFileMaker();
		//подключаем картинки к файлу
		$pattern = '#<img.*?src=["\']*([\S]+)["\'].*?>#si';
		$get_text = "<html>
					<head>
					<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
					<title>Распечатать</title>
					</head>
					<body>".$get_text;
		preg_match_all($pattern, $get_text, $matches);
		
//print_r($matches[0]);
		foreach ($matches[0] as $img_old)
			{
			$img_new=str_replace('<img ','<img hspace="10" align="left" ',$img_old);
			$get_text=str_replace($img_old,$img_new,$get_text);
		};
		foreach ($matches[1] as $img)
			{
				$img_tmp=$img;
				$img_tmp_old=$img;
				if (strpos($img_tmp,"http")===FALSE) 
					$img_tmp=$base_link.$img_tmp;
				//выделяем путь картинки БЕЗ адреса домена
				$img_array=explode("//",$img_tmp);
				$img_name_only=$img_array[1];
				$img_name_only=explode("/",$img_name_only);
				unset($img_name_only[0]);
				$img_name_only=implode("/",$img_name_only);
				//заменяем адрес картинки на относительный (без домена)
				$get_text=str_replace($img_tmp_old,$img_name_only,$get_text);
				//добавляем картинку в конечный файлГ
				$MhtFileMaker->AddFile($img_tmp, $img_name_only, NULL);
		};
		//разобрали картинки, теперь создаём окончательный файл
		$MhtFileMaker->AddContents("index.html","text/html",$get_text);
		//сохраняем файл
		$MhtFileMaker->MakeFile($filename);
		
		header("Content-Type: application/download\n"); 
//		header("Content-Disposition: attachment; filename=\"".$filename."\"");
		header("Content-Disposition: attachment; filename=\"Коммерческое предложение.doc\"");
		$file_body=file_get_contents($filename);
		print $file_body;
		unlink($filename);
		}
	}

$ajax .= <<<HTML
<script type="text/javascript">
	$(document).ready(function(){
		$("#comparenews span").css("font-size","12px");
	});
</script>
HTML;
}

