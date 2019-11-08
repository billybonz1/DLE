$countPrice = 0;
			$ff = new db;
			$resultPrice = $ff->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}' ORDER BY id ASC");
			$myrowPrice = $ff->get_array($resultPrice);
			$countPrice = $ff->num_rows($resultPrice);
			
			
			if($countPrice != 0){
			
				$hidPack = "";
				$hidCvet = "";
				$hidBlesk = "";
				$hidPriceYp = "";
				$hidPriceLitr = "";
				
				
				$rowCa = explode(",", $row['category'] );

				$resultGetCat = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='{$rowCa[0]}'");
				$myrowGetCat = $db->get_array($resultGetCat);
				
				$fex = explode(" ", $myrowGetCat['name']);
				$sex = explode(",", $fex[0]);
				
				$resultGetPrice = $db->query("SELECT togrn, nacenka FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '$sex[0]%'");
				$getPrice = $db->get_array($resultGetPrice);
				
				if($db->num_rows($resultGetPrice) != 0) {
					$grn = $getPrice['togrn']; 
					$nac = $getPrice['nacenka'];
				} else {$grn = 0; $nac = 0;}
				
				if($grn <= 1) {$sverxPriceYp = $myrowPrice['price_yp'];} else {
					$sverxPriceYp = $myrowPrice['price_yp']*$grn;
					$sverxPriceYp = round($sverxPriceYp, 2);
				}
				$nacenkaPriceYp = ($sverxPriceYp*$nac)/100;
				$nacenkaPriceYp = round($nacenkaPriceYp, 2);
				$realPriceYp = $sverxPriceYp + $nacenkaPriceYp;
				
				$explodePack = explode(" ",$myrowPrice['pack']);
				if($grn <= 1) {$sverxPriceLitr = $myrowPrice['price_litr'];} else {
					if($explodePack[0] != 0) {
						$sverxPriceLitr = $realPriceYp/$explodePack[0];
						$sverxPriceLitr = round($sverxPriceLitr, 2);
					}
				}
				$realPriceLitr = $sverxPriceLitr;
				
				
				
				
				$numIt = 0;
				
				do {
				
				
				if($grn <= 1) {$sverxPriceYp1 = $myrowPrice['price_yp'];} else {
					$sverxPriceYp1 = $myrowPrice['price_yp']*$grn;
					$sverxPriceYp1 = round($sverxPriceYp1, 2);
				}
				$nacenkaPriceYp1 = ($sverxPriceYp1*$nac)/100;
				$nacenkaPriceYp1 = round($nacenkaPriceYp1, 2);
				$realPriceYp1 = $sverxPriceYp1 + $nacenkaPriceYp1;
				
				$explodePack1 = explode(" ",$myrowPrice['pack']);
				if($grn <= 1) {$sverxPriceLitr1 = $myrowPrice['price_litr'];} else {
					if($explodePack1[0] != 0) {
						$sverxPriceLitr1 = $realPriceYp1/$explodePack1[0];
						$sverxPriceLitr1 = round($sverxPriceLitr1, 2);
					}
				}
				$realPriceLitr1 = $sverxPriceLitr1;
				
				
					$hidPack .= "
					<div class='tag11{$row[id]}' id='tag11' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$myrowPrice['pack']}</p></div>
					";
					$hidCvet .= "<div class='tag44{$row[id]}' id='tag44' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$myrowPrice['cvet']}</p></div>";
					$hidBlesk .= "<div class='tag55{$row[id]}' id='tag55' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$myrowPrice['blesk']}</p></div>";
					$hidPriceYp .= "<div class='tag22{$row[id]}' id='tag22' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$realPriceYp1} грн</p></div>";
					$hidPriceLitr .= "<div class='tag33{$row[id]}' id='tag33' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$realPriceLitr1} грн</p></div>";
					

					$numIt++;

				} while($myrowPrice = $ff->get_array($resultPrice));
				
				
				$resultPrice = $ff->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}' ORDER BY id ASC");
				$myrowPrice = $ff->get_array($resultPrice);
				$countPrice = $ff->num_rows($resultPrice);
				
				if($countPrice > 1) {
					$raskrSpis = "<img src='{THEME}/images/razv.png' style='margin-left:5px;'>";
				} else {
					$raskrSpis = "";
				}
				
				$tf = new db;
				$resultTf = $tf->query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$row[id]}'");
				$myrowTf = $tf->get_array($resultTf);
				
				if($myrowPrice['price_yp'] == 0 || $myrowPrice['price_yp'] == "Нет") {
					$realPriceLitr = 0;
				}
				
				if($myrowTf['pack_g'] == "true") {
				
				$ypak = "<div style='float:left;'>
								<p id='nazvkol2'><span style='letter-spacing:2px; padding-right:0px;'>упаковка</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
								<div class='tag1' id='num{$row[id]}' onclick='cng({$row[id]})' style='cursor:pointer;'><p>{$myrowPrice['pack']}$raskrSpis</p></div>
								<div style='display:none;' id='{$row[id]}'><br><br>{$hidPack}<br><br></div>
							</div>";
				
				
			
				} else {$ypak = "<div style='float:left;' id='{$row[id]}'><div id='num{$row[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['cvet_g'] == "true") {
					$cvet = "<div style='float:left;'>
								<p id='nazvkol2'><span style='letter-spacing:5px; padding-right:0px;'>база и цвет</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
								<div class='tag4' id='num2{$row[id]}' onclick='cng({$row[id]})'><p>{$myrowPrice['cvet']}</p></div>
								<div style='display:none;' id='cvet{$row[id]}'><br><br>{$hidCvet}<br><br></div>
							</div>";
				} else {$cvet = "<div style='float:left;' id='cvet{$row[id]}'><div id='num2{$row[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['blesk_g'] == "true") {
				$blesk = "<div style='float:left;'>
							<p id='nazvkol'><span style='letter-spacing:2px; padding-right:0px;'>степень блеска</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
							<div class='tag5' id='num3{$row[id]}' onclick='cng({$row[id]})'><p>{$myrowPrice['blesk']}</p></div>
							<div style='display:none;' id='blesk{$row[id]}'><br><br>{$hidBlesk}<br><br></div>
						</div>";
				} else {$blesk = "<div style='float:left;' id='blesk{$row[id]}'><div id='num3{$row[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['price_yp_g'] == "true") {
				$styp = "<div style='float:left;'>
							<p id='nazvkol4'><span style='letter-spacing:2px; padding-right:0px;'>цена упаковки</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
							<div class='tag2' id='num4{$row[id]}' onclick='cng({$row[id]})'><p>{$realPriceYp} грн</p></div>
							<div style='display:none;' id='priceyp{$row[id]}'><br><br>{$hidPriceYp}<br><br></div>
						</div>";
				} else {$styp = "<div style='float:left;' id='priceyp{$row[id]}'><div id='num4{$row[id]}'> style='display:none;'</div></div>";}
				
				if($myrowTf['price_litr_g'] == "true") {
				$stl = "<div style='float:left;'>
							<p id='nazvkol' style='letter-spacing:2px; text-align:left; margin-left:5px;'> цена за 1л</p>
							<div class='tag3' id='num5{$row[id]}' onclick='cng({$row[id]})'><p>{$realPriceLitr} грн</p></div>
							<div style='display:none;' id='pricelitr{$row[id]}'><br><br>{$hidPriceLitr}<br><br></div>
						</div>";
				} else {$stl = "<div style='float:left;' id='pricelitr{$row[id]}'><div id='num5{$row[id]}' style='display:none;'></div></div>";}
				
				
				
				
				
				$price = "
				  <div style='clear:both; float;left; margin-right:130px; margin-top:-27px;'  id='showlogin'>	
						{$ypak}{$cvet}{$blesk}{$styp}{$stl}
						<div style='float:left; margin-right:-12px; margin-left:10px;'>
							<button onclick='compare({$row[id]})' style='border:0px; width:96px; margin-right:5px; height:27px; cursor:pointer; margin-top:30px;'><img src='{THEME}/images/buttons/compare.png'></button>
							<br>
							<button onclick='intobusket({$row[id]})' style='border:0px; width:96px; height:27px; margin-right:5px; cursor:pointer;'><img src='{THEME}/images/buttons/buy.png'></button>
						</div>
						
						<form>
							<input type='hidden' id='idd{$row[id]}' value='{$myrowPrice[id]}'>
						</form>
				  </div>
				  <div id='pojceny'><a href='/index.php?do=feedback'><img src='{THEME}/images/buttons/complane.png'></a></div>";
				  
				  ?>

                  	<script type="text/javascript">
						
						
						s=1;

						function cng(v) {
							
							if(s==1) {
								document.getElementById(v).style.display = "block"; 
								document.getElementById("cvet" + v).style.display = "block";
								document.getElementById("blesk" + v).style.display = "block";
								document.getElementById("priceyp" + v).style.display = "block";
								document.getElementById("pricelitr" + v).style.display = "block";
								document.getElementById("upack" + v).style.display = "block";
								s=2;
							} else if(s==2) {
								document.getElementById(v).style.display = "none";
								document.getElementById("cvet" + v).style.display = "none"; 
								document.getElementById("blesk" + v).style.display = "none";
								document.getElementById("priceyp" + v).style.display = "none";
								document.getElementById("pricelitr" + v).style.display = "none";
								document.getElementById("upack" + v).style.display = "none";
								s=1;
							}
							
						}
						
						function fade(f1,f2) {
							tagArray11 = document.getElementsByClassName("tag11" + f2);
							tagArray22 = document.getElementsByClassName("tag22" + f2);
							tagArray33 = document.getElementsByClassName("tag33" + f2);
							tagArray44 = document.getElementsByClassName("tag44" + f2);
							tagArray55 = document.getElementsByClassName("tag55" + f2);
							tagArray66 = document.getElementsByClassName("tag66" + f2);
							
						
								tagArray11[f1].style.color = "#015283";
								tagArray22[f1].style.color = "#015283";
								tagArray33[f1].style.color = "#015283";
								tagArray44[f1].style.color = "#015283";
								tagArray55[f1].style.color = "#015283";
								tagArray66[f1].style.color = "#015283";
								
							
							
						}
						
						function fadeout(f1,f2) {
							tagArray11 = document.getElementsByClassName("tag11" + f2);
							tagArray22 = document.getElementsByClassName("tag22" + f2);
							tagArray33 = document.getElementsByClassName("tag33" + f2);
							tagArray44 = document.getElementsByClassName("tag44" + f2);
							tagArray55 = document.getElementsByClassName("tag55" + f2);
							tagArray66 = document.getElementsByClassName("tag66" + f2);
							
							
								tagArray11[f1].style.color = "#007cc7";
								tagArray22[f1].style.color = "#007cc7";
								tagArray33[f1].style.color = "#007cc7";
								tagArray44[f1].style.color = "#007cc7";
								tagArray55[f1].style.color = "#007cc7";
								tagArray66[f1].style.color = "#007cc7";	
							
						}
						
						
					</script>
                  <?php
				  
			}
			?>