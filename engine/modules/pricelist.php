<?php
require_once ROOT_DIR.'/engine/modules/xls.php';
require_once ROOT_DIR.'/engine/modules/pclzip.lib.php';

$xls = new XLS();

$save = str_replace(" ", "_", $_REQUEST['plCategories']);

$save = explode("(", $save);
if($save[0] == "Teknos_Oy_Пром") {
	$save[0] = "Teknos_Oy_Prom";
}
$plOptions = "";
$display   = "";

if(isset($_REQUEST['action']) && $_REQUEST['action'] == "printpricelist") { 
	$display = " display:none;";
}

$resultParts = $db->query("SELECT name, proizvoditel FROM " . PREFIX . "_razdeli ORDER BY name ASC");
$myrowParts  = $db->get_array($resultParts);
if($db->num_rows($resultParts) != 0) {
	$plOptions .= "<option value='' style='font-size:14px;' class='option'>Выберите категорию</option>";
	do {
		$explodeTemp = explode(" ", $myrowParts['proizvoditel']);
		$explodeCategory = explode(",", $explodeTemp[0]);
		if($explodeCategory[0] == "TIKKURILA") {
			$current = $explodeTemp[0] . " " . $explodeTemp[1];
		} else if($explodeCategory[0] == "Tikkurila"){
			$current = $explodeTemp[0] . " " . $explodeTemp[1];
		}else if($explodeCategory[0] == "Teknos"){
			$current = $explodeTemp[0] . " " . $explodeTemp[1] . " " . $explodeTemp[2];
		} else { 
			$current = $explodeCategory[0];
		}
		$plOptions .= "<option value='{$current}' style='font-size:14px;' class='option'>{$myrowParts['name']}</option>";
	} while($myrowParts  = $db->get_array($resultParts));
}


if(isset($_REQUEST['plCategories']) && $_REQUEST['plCategories'] == "all") {
	$printHref = "index.php?do=pricelist&plCategories=all&action=printpricelist";
	$exelHref  = "excel/allprice.xls";
	$gzipHref  = "excel/allprice.zip";
} else if(isset($_REQUEST['plCategories']) && $_REQUEST['plCategories'] != "all"){
	$printHref = "index.php?do=pricelist&plCategories=" . $_REQUEST['plCategories'] . "&action=printpricelist";
	$exelHref  = "excel/" . $save[0] . ".xls";
	$gzipHref  = "excel/" . $save[0] . ".zip";
}

$act = "";
if(isset($_SESSION['dle_user_id'])) {
	$resUser = $db->query("SELECT * FROM " . PREFIX . "_pricelist WHERE user_id='{$_SESSION[dle_user_id]}'");
	$myrUser = $db->get_array($resUser);
	if($db->num_rows($resUser) != 0 && $myrUser['action'] == 'yes') {
		$act = "
            <a href='{$exelHref}' class='nazvPrice' >Excel Форма</a>
            <a href='{$gzipHref}' class='nazvPrice' >Excel Форма GZIP</a> 
		";
	}
}
$pricelist = "<div style='clear:both; float:left; background-color:#e9f0f6;'>
				  <div style='clear:both; float:left; background-color:#fff; margin:15px;' id='white'>
			          <div class='a_block_22' id='poltitle'>
					  ";
					  if(!isset($_REQUEST['action']) || $_REQUEST['action'] != "printpricelist") {
					  
			$pricelist .= "<div style='padding-top:7px;'>
			              <p class='ntitle'>
			                  <img src='{THEME}/images/dlet_artblock_22_01.gif'>ПРАЙС-ЛИСТ
			                
			              </p>
			              </div>";
					  }
		$pricelist .= "</div>
			          <div style='clear:both; margin:10px;'>
			          	  <table width='100%'>";
if(!isset($_REQUEST['action']) || $_REQUEST['action'] != "printpricelist") {
 		    $pricelist .= "<form action='' name='formSelect'>
			          	  	<tr>
			          	  		<td>
			          	  			<table width='100%'>
			          	  				<tr>
						          	  		<td align='right' valign='top'>
						          	  			<select name='plCategories' style='font-size:14px;' class='select'>{$plOptions}</select>
						          	  		</td>
						          	  		<td align='left' valign='top'>
						          	  			<input type='hidden' name='do' value='pricelist'>
						          	  			<input type='submit' class='btn2' value='Показать'>
						          	  		</td>
			          	  				</tr>
			          	  			</table>
			          	  		</td> 
			          	  	</tr>
			          	  	</form>
			          	  	<tr>
			          	  		<td align='center'><br><br>
			          	  			<a href='index.php?do=pricelist&plCategories=all' class='nazvPrice'>Вывести все позиции</a>
									<a href='{$printHref}' target='_blank' class='nazvPrice' >Печатная форма</a>
 		   						    {$act}
			          	  		</td>
			          	  	</tr>";
}						

			  $pricelist .= "<tr>
			          	    	<td> 
			          	    		<table width='100%' border='1' style='border:1px #000 solid; margin-top:20px;'>
			          	    			";
$tableHeader = "<tr>
              		<td width='30%' align='center'><p class='nazv123' id='td1'>Продукция</p></td>
          	    	<td width='13%' align='center'><p class='nazv123' id='td1'>Артикул</p></td>
          	    	
          	    	<td width='13%' align='center'><p class='nazv123' id='td1'>База и цвет</p></td>
          	    	<td width='12%' align='center'><p class='nazv123' id='td1'>Степень блеска</p></td>
          	    	<td width='8%' align='center'><p class='nazv123' id='td1'>Упаковка</p></td>
           	    	<td width='8%' align='center'><p class='nazv123' id='td1'>Стоимость уп.</p></td>
          	    	<td width='8%' align='center'><p class='nazv123' id='td1'>Стоимость за 1л/шт</p></td>
          	    	<td width='8%' align='center' style='{$display}'><p class='nazv123' id='td1'>Действие</p></td>
              	</tr>";
				$xls->add_cell(0,0,"Продукция");
				$xls->add_cell(1,0,"Артикул");
				$xls->add_cell(2,0,"База и цвет");
				$xls->add_cell(3,0,"Степень блеска");
				$xls->add_cell(4,0,"Упаковка");
				$xls->add_cell(5,0,"Стоимость уп");
				$xls->add_cell(6,0,"Стоимость за 1л/шт");

				$n = 2;
if(isset($_REQUEST['plCategories']) && $_REQUEST['plCategories'] != "" && $_REQUEST['plCategories'] != "all") {
	$pricelist .= $tableHeader;
	$resultRazdeli = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '{$_REQUEST[plCategories]}%'");
	$myrowRazdeli  = $db->get_array($resultRazdeli);
	
	do {
		
		$catArray = array();
		$discount = 0;
		$togrn = $myrowRazdeli['togrn'];
		$nacenka = $myrowRazdeli['nacenka'];
		$pricelist .= "<tr><td colspan='8'><hr /></td></tr>"; 
		$pricelist .= "<tr><td colspan='8' align='center'><p class='nazvPrice' style='font-weight:bold;'>{$myrowRazdeli['name']}</p></td></tr>";
		$xls->add_cell(0,$n,$myrowRazdeli['name']);
		$n = $n+2;
		$explodeTemp = explode(" ", $myrowRazdeli['proizvoditel']);
		if($explodeTemp[0] == "TIKKURILA") {
			$current = $explodeTemp[0] . " " . $explodeTemp[1];
		} else if($explodeTemp[0] == "Tikkurila"){
			$current = $explodeTemp[0] . " " . $explodeTemp[1];
		}else if($explodeTemp[0] == "Teknos"){
			$current = $explodeTemp[0] . " " . $explodeTemp[1] . " " . $explodeTemp[2];
		} else { 
			$current = $explodeTemp[0];
		}
		
		if(isset($_REQUEST['dle_user_id'])) {
			$resultDisc = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='{$_SESSION[dle_user_id]}' AND proizv LIKE '$current%'");
			$myrowDisc = $db->get_array($resultDisc);
			if($db->num_rows($resultDisc) != 0) {
				$discount = $myrowDisc['discount'];
			}
		}
		
		$postIdArray = array();
		
		$resultCat = $db->query("SELECT id FROM " . PREFIX . "_category WHERE name LIKE '{$current}%'");
		$myrowCat  = $db->get_array($resultCat);
		
		do {
			array_push($catArray, $myrowCat['id']);
		}while($myrowCat  = $db->get_array($resultCat));
		
		$resultPost = $db->query("SELECT id,title,category FROM " . PREFIX . "_post ORDER BY title ASC");
		$myrowPost  = $db->get_array($resultPost);
		
		do {
			$explode = explode(",", $myrowPost['category']);
			
			if(in_array($explode[0], $catArray)) {
				array_push($postIdArray, $myrowPost['id']);
			}
			
		}while($myrowPost  = $db->get_array($resultPost));

		foreach($postIdArray as $key =>$value){
			$artikul = "---//---";
			$cvet = "---//---";
			$blesk = "---//---";
			$pack = "---//---";
			$priceYp = "---//---";
			$priceLitr = "---//---";
			$buy = "---//---";
			
			$reulstPst = $db->query("SELECT * FROM " . PREFIX . "_post WHERE id='$value'");
			$myrowPst  = $db->get_array($resultPst);
			
			$resultPrice = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$value}'");
			$myrowPrice = $db->get_array($resultPrice);
			if($db->num_rows($resultPrice) != 0) {
				
				$num = 1;
				do {
					$artikul = $myrowPrice['artikyl'];
					$cvet = $myrowPrice['cvet'];
					$blesk = $myrowPrice['blesk'];
					$pack = $myrowPrice['pack'];
					
					if($togrn != 0 && $nacenka != 0) {
						$priceYp   = $myrowPrice['price_yp']*$togrn+($myrowPrice['price_yp']*$nacenka/100);
						if($pack != 0) {
							$priceLitr = $priceYp/$pack;
							$priceLitr = round($priceLitr, 2);
						}
					} else if($togrn != 0 && $nacenka == 0) {
						$priceYp   = $myrowPrice['price_yp']*$togrn;
						if($pack != 0) {
							$priceLitr = $priceYp/$pack;
							$priceLitr = round($priceLitr, 2);
						}
					} else if($togrn == 0 && $nacenka != 0) {
						$priceYp   = $myrowPrice['price_yp']+($myrowPrice['price_yp']*$nacenka/100);
						if($pack != 0) {
							$priceLitr = $priceYp/$pack;
							$priceLitr = round($priceLitr, 2);
						}
					} else if($togrn == 0 && $nacenka == 0) {
						$priceYp   = $myrowPrice['price_yp'];
						$priceLitr = $myrowPrice['price_litr'];
					}
					if($discount != 0) {
						$priceYpTemp   = ($priceYp*$discount)/100;
						$priceLitrTemp = ($priceLitr*$discount)/100;
						
						$priceYp   = $priceYp-$priceYpTemp;
						$priceLitr = $priceLitr-$priceLitrTemp;
						$priceLitr = round($priceLitr, 2);
					}
					
					$priceYp   = round($priceYp, 2);
					
					
					$priceYp .= " грн";
					$priceLitr .= " грн";
					
					$buy = "<a onclick='intobusket($value); return false;' style='cursor:pointer; margin:0px; color:red; padding:0px; text-decoration:underline;'>Купить</a>";
			
					$buy .= "<form><input type='hidden' id='idd{$value}' value='{$myrowPrice[id]}'></form>";
					
				
					if($num == 1) {
						
					if( $config['allow_alt_url'] == "yes" ) {
			
							if( $myrowPst['flag'] and $config['seo_type'] ) {
								
								if( $myrowPst['category'] and $config['seo_type'] == 2 ) {
									
									$full_link = $config['http_home_url'] . get_url( $myrowPst['category'] ) . "/" . $myrowPst['id'] . "-" . $myrowPst['alt_name'] . ".html";
								
								} else {
									
									$full_link = $config['http_home_url'] . $myrowPst['id'] . "-" . $myrowPst['alt_name'] . ".html";
								
								}
							
							} else {
								
								$full_link = $config['http_home_url'] . date( 'Y/m/d/', $myrowPst['date'] ) . $myrowPst['alt_name'] . ".html";
							}
						
						} else {
							
							$full_link = $config['http_home_url'] . "index.php?newsid=" . $myrowPst['id'];
						
						}
						$pricelist .= "<tr>
							<td width='30%'><p class='nazvPrice'><a href='{$full_link}' class='nazvPrice' target='_blank'>{$myrowPst['title']}</a></p></td>
							<td width='13%' align='center'><p class='nazvPrice'>{$artikul}</p></td>
							<td width='13%' align='center'><p class='nazvPrice'>{$cvet}</p></td>
							<td width='12%' align='center'><p class='nazvPrice'>{$blesk}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$pack}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$priceYp}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$priceLitr}</p></td>
							<td width='8%' align='center' style='{$display}'><p class='nazvPrice' style='margin-top:7px;'>{$buy}</p></td>
						 </tr>";
						
						$num++;
						
						$xls->add_cell(0,$n,$myrowPst['title']);
						$xls->add_cell(1,$n,$artikul);
						$xls->add_cell(2,$n,$cvet);
						$xls->add_cell(3,$n,$blesk);
						$xls->add_cell(4,$n,$pack);
						$xls->add_cell(5,$n,$priceYp);
						$xls->add_cell(6,$n,$priceLitr);
						$n++;
					} else {
						$pricelistnext = "";
						
						$pricelistnext .= "<tr>
							<td width='30%' align='center'><p class='nazvPrice'>---------</p></td>
							<td width='13%' align='center'><p class='nazvPrice'>{$artikul}</p></td>
							<td width='13%' align='center'><p class='nazvPrice'>{$cvet}</p></td>
							<td width='12%' align='center'><p class='nazvPrice'>{$blesk}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$pack}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$priceYp}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$priceLitr}</p></td>
							<td width='8%' align='center' style='{$display}'><p class='nazvPrice' style='margin-top:7px;'>{$buy}</p></td>
						 </tr>";
						 $pricelist .= $pricelistnext;
						 
						$xls->add_cell(0,$n,"---------");
						$xls->add_cell(1,$n,$artikul);
						$xls->add_cell(2,$n,$cvet);
						$xls->add_cell(3,$n,$blesk);
						$xls->add_cell(4,$n,$pack);
						$xls->add_cell(5,$n,$priceYp);
						$xls->add_cell(6,$n,$priceLitr);
						$n++;
						
					}
					
				}while($myrowPrice = $db->get_array($resultPrice));	
			}
		}
	}while($myrowRazdeli  = $db->get_array($resultRazdeli));

} else if(isset($_REQUEST['plCategories']) && $_REQUEST['plCategories'] != "" && $_REQUEST['plCategories'] == "all"){
	$pricelist .= $tableHeader;
	$resultRazdeli = $db->query("SELECT * FROM " . PREFIX . "_razdeli ORDER BY name ASC");
	$myrowRazdeli  = $db->get_array($resultRazdeli);
	
	do {

		$catArray = array();
		$discount = 0;
		$togrn = $myrowRazdeli['togrn'];
		$nacenka = $myrowRazdeli['nacenka'];
		$pricelist .= "<tr><td colspan='8'><hr /></td></tr>"; 
		$pricelist .= "<tr><td colspan='8' align='center'><p class='nazvPrice' style='font-weight:bold;'>{$myrowRazdeli['name']}</p></td></tr>";
		$xls->add_cell(0,$n,$myrowRazdeli['name']);
		$n = $n+2;
		
		$explodeTemp = explode(" ", $myrowRazdeli['proizvoditel']);
		if($explodeTemp[0] == "TIKKURILA") {
			$current = $explodeTemp[0] . " " . $explodeTemp[1];
		} else if($explodeTemp[0] == "Tikkurila"){
			$current = $explodeTemp[0] . " " . $explodeTemp[1];
		}else if($explodeTemp[0] == "Teknos"){
			$current = $explodeTemp[0] . " " . $explodeTemp[1] . " " . $explodeTemp[2];
		} else { 
			$current = $explodeTemp[0];
		}
		
		if(isset($_REQUEST['dle_user_id'])) {
			$resultDisc = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='{$_SESSION[dle_user_id]}' AND proizv LIKE '$current%'");
			$myrowDisc = $db->get_array($resultDisc);
			if($db->num_rows($resultDisc) != 0) {
				$discount = $myrowDisc['discount'];
			}
		}
		
		$postIdArray = array();
		
		$resultCat = $db->query("SELECT id FROM " . PREFIX . "_category WHERE name LIKE '{$current}%'");
		$myrowCat  = $db->get_array($resultCat);
		
		do {
			array_push($catArray, $myrowCat['id']);
		}while($myrowCat  = $db->get_array($resultCat));
		
		$resultPost = $db->query("SELECT id,title,category FROM " . PREFIX . "_post ORDER BY title ASC");
		$myrowPost  = $db->get_array($resultPost);
		
		do {
			$explode = explode(",", $myrowPost['category']);
			
			if(in_array($explode[0], $catArray)) {
				array_push($postIdArray, $myrowPost['id']);
			}
			
		}while($myrowPost  = $db->get_array($resultPost));

		foreach($postIdArray as $key =>$value){
			$artikul = "---//---";
			$cvet = "---//---";
			$blesk = "---//---";
			$pack = "---//---";
			$priceYp = "---//---";
			$priceLitr = "---//---";
			$buy = "---//---";
			
			$reulstPst = $db->query("SELECT * FROM " . PREFIX . "_post WHERE id='$value'");
			$myrowPst  = $db->get_array($resultPst);
			
			$resultPrice = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$value}'");
			$myrowPrice = $db->get_array($resultPrice);
			if($db->num_rows($resultPrice) != 0) {
				
				$num = 1;
				
				do {
					$artikul = $myrowPrice['artikyl'];
					$cvet = $myrowPrice['cvet'];
					$blesk = $myrowPrice['blesk'];
					$pack = $myrowPrice['pack'];
					
					if($togrn != 0 && $nacenka != 0) {
						$priceYp   = $myrowPrice['price_yp']*$togrn+($myrowPrice['price_yp']*$nacenka/100);
						if($pack != 0) {
							$priceLitr = $priceYp/$pack;
							$priceLitr = round($priceLitr, 2);
						}
					} else if($togrn != 0 && $nacenka == 0) {
						$priceYp   = $myrowPrice['price_yp']*$togrn;
						if($pack != 0) {
							$priceLitr = $priceYp/$pack;
							$priceLitr = round($priceLitr, 2);
						}
					} else if($togrn == 0 && $nacenka != 0) {
						$priceYp   = $myrowPrice['price_yp']+($myrowPrice['price_yp']*$nacenka/100);
						if($pack != 0) {
							$priceLitr = $priceYp/$pack;
							$priceLitr = round($priceLitr, 2);
						}
					} else if($togrn == 0 && $nacenka == 0) {
						$priceYp   = $myrowPrice['price_yp'];
						$priceLitr = $myrowPrice['price_litr'];
					}

					if($discount != 0) {
						$priceYpTemp   = ($priceYp*$discount)/100;
						$priceLitrTemp = ($priceLitr*$discount)/100;
						
						$priceYp   = $priceYp-$priceYpTemp;
						$priceLitr = $priceLitr-$priceLitrTemp;
						$priceLitr = round($priceLitr, 2);
					}
					
					$priceYp   = round($priceYp, 2);
					
					$priceYp .= " грн";
					$priceLitr .= " грн";
					
					$buy = "<a onclick='intobusket($value); return false;' style='cursor:pointer; margin:0px; color:red; padding:0px; text-decoration:underline;'>Купить</a>";
			
					$buy .= "<form><input type='hidden' id='idd{$value}' value='{$myrowPrice[id]}'></form>";

					if($num == 1) {
						
						if( $config['allow_alt_url'] == "yes" ) {
			
							if( $myrowPst['flag'] and $config['seo_type'] ) {
								
								if( $myrowPst['category'] and $config['seo_type'] == 2 ) {
									
									$full_link = $config['http_home_url'] . get_url( $myrowPst['category'] ) . "/" . $myrowPst['id'] . "-" . $myrowPst['alt_name'] . ".html";
								
								} else {
									
									$full_link = $config['http_home_url'] . $myrowPst['id'] . "-" . $myrowPst['alt_name'] . ".html";
								
								}
							
							} else {
								
								$full_link = $config['http_home_url'] . date( 'Y/m/d/', $myrowPst['date'] ) . $myrowPst['alt_name'] . ".html";
							}
						
						} else {
							
							$full_link = $config['http_home_url'] . "index.php?newsid=" . $myrowPst['id'];
						
						}
						
						$pricelist .= "<tr>
							<td width='30%'><p class='nazvPrice'><a href='{$full_link}' class='nazvPrice' target='_blank'>{$myrowPst['title']}</a></p></td>
							<td width='13%' align='center'><p class='nazvPrice'>{$artikul}</p></td>
							<td width='13%' align='center'><p class='nazvPrice'>{$cvet}</p></td>
							<td width='12%' align='center'><p class='nazvPrice'>{$blesk}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$pack}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$priceYp}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$priceLitr}</p></td>
							<td width='8%' align='center' style='{$display}'><p class='nazvPrice' style='margin-top:7px;'>{$buy}</p></td>
						 </tr>";
						
						$xls->add_cell(0,$n,$myrowPst['title']);
						$xls->add_cell(1,$n,$artikul);
						$xls->add_cell(2,$n,$cvet);
						$xls->add_cell(3,$n,$blesk);
						$xls->add_cell(4,$n,$pack);
						$xls->add_cell(5,$n,$priceYp);
						$xls->add_cell(6,$n,$priceLitr);
						$n++;
						
						$num++;
					} else {
						$pricelistnext = "";
						
						$pricelistnext .= "<tr>
							<td width='30%' align='center'><p class='nazvPrice'>---------</p></td>
							<td width='13%' align='center'><p class='nazvPrice'>{$artikul}</p></td>
							<td width='13%' align='center'><p class='nazvPrice'>{$cvet}</p></td>
							<td width='12%' align='center'><p class='nazvPrice'>{$blesk}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$pack}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$priceYp}</p></td>
							<td width='8%' align='center'><p class='nazvPrice'>{$priceLitr}</p></td>
							<td width='8%' align='center' style='{$display}'><p class='nazvPrice' style='margin-top:7px;'>{$buy}</p></td>
						 </tr>";
						 $pricelist .= $pricelistnext;
						 
						$xls->add_cell(0,$n,"---------");
						$xls->add_cell(1,$n,$artikul);
						$xls->add_cell(2,$n,$cvet);
						$xls->add_cell(3,$n,$blesk);
						$xls->add_cell(4,$n,$pack);
						$xls->add_cell(5,$n,$priceYp);
						$xls->add_cell(6,$n,$priceLitr);
						$n++;
					}
					
				}while($myrowPrice = $db->get_array($resultPrice));
			}
		}
		$n++;
	}while($myrowRazdeli  = $db->get_array($resultRazdeli));
	
}
			          $pricelist .= "</table>
			          	    	</td>
			          	    </tr> 
			          	  </table>
			          </div>
			           
				  </div>
			  </div>"; 
			          
			        
			          
if(isset($_REQUEST['plCategories']) && $_REQUEST['plCategories'] == "all"){
	$xls->save_file(ROOT_DIR.'/engine/modules/excel/allprice.xls');
	$files_to_arch = "excel/allprice.xls";
	$name_arch = "excel/allprice.zip";
}
if(isset($_REQUEST['plCategories']) && $_REQUEST['plCategories'] != "all"){
	$xls->save_file("excel/" . $save[0] . ".xls");
	
	$files_to_arch = "excel/" . $save[0] . ".xls";
	$name_arch = "excel/" . $save[0] . ".zip";
}

$archive = new PclZip($name_arch);
$v_list = $archive->create($files_to_arch);


?>