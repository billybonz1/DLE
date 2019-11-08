<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004,2011 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: index.php
-----------------------------------------------------
 Назначение: Главная страница
=====================================================
*/

set_time_limit (600);
@session_start ();
@ob_start ();
@ob_implicit_flush ( 0 );

@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );


define ( 'DATALIFEENGINE', true );

$member_id = FALSE;
$is_logged = FALSE;

define ( 'ROOT_DIR', dirname ( __FILE__ ) );
define ( 'ENGINE_DIR', ROOT_DIR . '/engine' );

require_once ROOT_DIR . '/engine/init.php';
//require_once ROOT_DIR.'/engine/modules/catlist.php';
require_once ROOT_DIR.'/engine/modules/xlist.php';

if (clean_url ( $_SERVER['HTTP_HOST'] ) != clean_url ( $config['http_home_url'] )) {
	
	$replace_url = array ();
	$replace_url[0] = clean_url ( $config['http_home_url'] );
	$replace_url[1] = clean_url ( $_SERVER['HTTP_HOST'] );

} else
	$replace_url = false;


$tpl->load_template ( 'main.tpl' );

$tpl->set ( '{calendar}', $tpl->result['calendar'] );
$tpl->set ( '{archives}', $tpl->result['archive'] );
$tpl->set ( '{tags}', $tpl->result['tags_cloud'] );
$tpl->set ( '{tags_all_view}', $tpl->result['tags_all_view']);
$tpl->set ( '{vote}', $tpl->result['vote'] );
$tpl->set ( '{topnews}', $topnews );
$tpl->set ( '{login}', $tpl->result['login_panel'] );
if($_REQUEST['do'] == "lostpassword") {
	$tpl->set ( '{info}', "<div id='dle-info'>" . $tpl->result['info'] . "</div>" );
} else {
	$tpl->set ( '{info}', "" );
}
$tpl->set ( '{speedbar}', $tpl->result['speedbar'] );
//$tpl->set ( '{catlist}', $catlist);
//$tpl->set ( '{catlist2}', $catlist2);
//$tpl->set ( '{catlist3}', $catlist3);
//$tpl->set ( '{catlist4}', $catlist4);
//$tpl->set ( '{catlist5}', $catlist5);
$tpl->set ( '{xlist}', $xlist[1]);
$tpl->set ( '{xlist2}', $xlist[2]);
$tpl->set ( '{xlist3}', $xlist[3]);
$tpl->set ( '{xlist4}', $xlist[4]);
$tpl->set ( '{xlist5}', $xlist[5]);
$tpl->set ( '{xlist6}', $xlist[6]);
$tpl->set ( '{pmenu}', $pmenu);

if(!isset($_SESSION['dle_user_id']) || ($_SESSION['dle_user_id'] != "97" && $_SESSION['dle_user_id'] != "308" && $_SESSION['dle_user_id'] != "1")) {
//if(!isset($_SESSION['dle_user_id']) || $_SESSION['dle_user_id'] != "97") {
	$tpl->set ( '{dntshw}', " style='display:none;'");
}
$tpl->set("[busket]", "<a href='https://laki-kraski.com.ua/index.php?do=basket' class='a'>");
$tpl->set("[/busket]", "</a>");
$ex = explode(".", $_SERVER['REQUEST_URI']);


//if(isset($_REQUEST['dochange'])) {
//	$res = $db->query("SELECT * FROM dle_post");
//	while($myr = $db->get_array($res)) {
//		$expl = explode("<script", $myr['short_story']);
//		$expl2 = explode("<script", $myr['full_story']);
//		$db->query("UPDATE dle_post SET short_story='" . $expl[0]. "', full_story='" . $expl2[0] . "' WHERE id='" . $myr['id'] . "'");
//	}
//}


if((isset($_REQUEST['num']) && $_REQUEST['num'] == 3) || $ex[1] == "html" || $_REQUEST['do'] == "pricelist"){
$tpl->set ( '{scriptlink}', "<script type='text/javascript' src='https://laki-kraski.com.ua/templates/real/css/modifiers.js'></script>");
} else {
$tpl->set ( '{scriptlink}', "<script type='text/javascript' src='https://laki-kraski.com.ua/templates/real/css/modifiers2.js'></script>");
}


if(isset($_SESSION['dle_user_id']) && $_SESSION['dle_user_id'] == '97') {
	$tpl->set ( "{ssss}", "display:block;");
} else {
	$tpl->set ( "{ssss}", "display:none;");
}

if(isset($_SESSION['dle_user_id'])){
	$activeResult = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='{$_SESSION[dle_user_id]}'");
	if($db->num_rows($activeResult) > 0) {
		$tpl->set("{activePrice}", "Ваши цены активированы");
	} else {
		$tpl->set("{activePrice}", "Свяжитесь и активируйте скидки");
	}
}


if ($config['allow_skin_change'] == "yes") $tpl->set ( '{changeskin}', ChangeSkin ( ROOT_DIR . '/templates', $config['skin'] ) );

if (count ( $banners ) and $config['allow_banner']) {
	
	foreach ( $banners as $name => $value ) {
		$tpl->copy_template = str_replace ( "{banner_" . $name . "}", $value, $tpl->copy_template );
	}

}

if(isset($_REQUEST['doaction']) && $_REQUEST['doaction'] == 'validating') {
	$reportRegistration = "
	<div align='center' class='regrep'>
	<p>Интернет Магазин «Диллер-Краска»</p>
    <p>Регистрация прошла успешно. Благодарим за сотрудничество.</p>
	<p>Чтобы активировать цены со скидкой после регистрации свяжитесь с нами по телефонам</p>
	</div>
";
}

if(isset($_REQUEST['srt'])) {
	$qq = new db;
	
	$m1 = $qq->query("SELECT * FROM " . PREFIX . "_post");
	$r1 = $qq->get_array($m1);


	
	do {
		$ssssss = explode(",", $r1['category']);
		$zxc = $qq->query("SELECT * FROM " . PREFIX . "_category WHERE id='$ssssss[0]'");
			$mzxc = $qq->get_array($zxc);


		$asd = $qq->query("SELECT * FROM " . PREFIX . "_catt WHERE name='$mzxc[name]'");
		if($qq->num_rows($asd) == 0) {
			
			$qq->query("INSERT INTO " . PREFIX . "_catt (id, name) VALUES(null, '$mzxc[name]')");
		}
	} while($r1 = $qq->get_array($m1));
	exit;	
}

$rb = $db->query("SELECT * FROM " . PREFIX . "_basket WHERE user='$_SESSION[dle_user_id]'");
$mb = $db->get_array($rb);
$ct = $db->num_rows($rb);

do {
$resultPriceId = $db->query("SELECT * FROM " . PREFIX . "_price WHERE id='$mb[id]'");
$myrowPriceId  = $db->get_array($resultPriceId);

$resultCatPost = $db->query("SELECT category, title FROM " . PREFIX . "_post WHERE id='$myrowPriceId[thisid]'");
$myrowCatPost  = $db->get_array($resultCatPost);

$explodeCategory = explode(",", $myrowCatPost['category']);

$resultName = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='$explodeCategory[0]'");
$myrowName  = $db->get_array($resultName);

$explTemp 	 = explode(" ", $myrowName['name']);
$explodeName = explode(",", $explTemp[0]);
if($explodeName[0] == "TIKKURILA") {
	$current = $explTemp[0] . " " . $explTemp[1];
} else if ($explodeName[0] == "Tikkurila") {
	$current = $explTemp[0] . " " . $explTemp[1];
} else if ($explodeName[0] == "Teknos") {
	$current = $explTemp[0] . " " . $explTemp[1] . " " . $explTemp[2];
} else {
	$current = $explodeName[0];
}

$resultRazdeli = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '{$current}%'");
$myrowRazdeli  = $db->get_array($resultRazdeli);

$priceYp = 0;
if($myrowRazdeli['togrn'] == 0) {
	if($myrowRazdeli['nacenka'] == 0){
		$priceYp = $myrowPriceId['price_yp'];
	} else {
		$priceYpCur = ($myrowPriceId['price_yp']*$myrowRazdeli['nacenka'])/100;
		$priceYp = round($priceYpCur, 2);
		$priceYp = $priceYp+$myrowPriceId['price_yp'];
	}
} else {
	if($myrowRazdeli['nacenka'] == 0){
		$priceYp = $myrowPriceId['price_yp']*$myrowRazdeli['togrn'];
		$priceYp = round($priceYp, 2);
	} else {
		$priceYp = $myrowPriceId['price_yp']*$myrowRazdeli['togrn'];
		$priceYpCur = ($priceYp*$myrowRazdeli['nacenka'])/100;
		$priceYpNac = round($priceYpCur, 2);
		$priceYp = $priceYp +$priceYpNac;
	}
}

$cls = $mb['cols'];
$cost = $priceYp * $cls;

$rdisc = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$user' AND proizv LIKE '{$current}%'");
$cdisc = $db->num_rows($rdisc);

$disc = 0;
$itog = 0;
if($cdisc > 0) {

	$mdisc = $db->get_array($rdisc);
	$discc = ($cost*$mdisc['discount'])/100;
	$disc = round($discc, 2);
	$endsumm = $cost-$disc;
	$prs += $endsumm;
} else {
	$prs += $cost;

}

} while($mb = $db->get_array($rb));
$tpl->set('{prs}', $prs);
$tpl->set('{nums}', $ct);


$tpl->set_block ( "'{banner_(.*?)}'si", "" );

if (count ( $informers ) and $config['rss_informer']) {
	foreach ( $informers as $name => $value ) {
		$tpl->copy_template = str_replace ( "{inform_" . $name . "}", $value, $tpl->copy_template );
	}
}

if ($allow_active_news AND $config['allow_change_sort'] AND $do != "userinfo") {
	
	$tpl->set ( '[sort]', "" );
	$tpl->set ( '{sort}', news_sort ( $do ) );
	$tpl->set ( '[/sort]', "" );

} else {
	
	$tpl->set_block ( "'\\[sort\\](.*?)\\[/sort\\]'si", "" );

}

if ($dle_module == "showfull" ) {

	if (is_array($cat_list) AND count($cat_list) > 1 ) $category_id = implode(",", $cat_list);

}

if($_REQUEST['do'] == "pricelist") {
	require_once ROOT_DIR.'/engine/modules/pricelist.php';
} else if($_REQUEST['do'] == "basket") {
if(isset($_REQUEST['otpr'])) {
	
	$tovar = $_SESSION['tovar'];
	
	$explodeTovar = explode(",", $tovar);
	
	$countTovar = count($explodeTovar)-1;
	
	$busk = "";
	if($countTovar != 0) {
	
	$busk= "
		<form action='' method='post'>
		<table style='background-color:#eff6ff;' width='98%' id='basktable' align='center'>
			<tr>
				<td width='36%'><p class='nazv123' id='td1'>Название</p></td>
				<td width='8%'><p class='nazv123' id='td2'>Артикул</p></td>
				<td width='8%'><p class='nazv123' id='td3'>Тара</p></td>
				<td width='11%'><p class='nazv123' id='td4'>Цена</p></td>
				<td width='8%'><p class='nazv123' id='td5'>Кол-во</p></td>
				<td width='10%'><p class='nazv123' id='td6'>Сумма</p></td>
				<td width='9%'><p class='nazv123' id='td7'>Скидка</p></td>
				<td width='10%'><p class='nazv123' id='td8'>Итог</p></td>
			</tr>
			";
			require_once ROOT_DIR.'/engine/modules/xls.php';
			$xls = new XLS(); 
			$xls->add_cell(0,0,"Название");
			$xls->add_cell(1,0,"Артикул");
			$xls->add_cell(2,0,"Тара");
			$xls->add_cell(3,0,"Цена");
			$xls->add_cell(4,0,"Количество");
			$xls->add_cell(5,0,"Сумма");
			$xls->add_cell(6,0,"Скидка");
			$xls->add_cell(7,0,"Итог");
			$b = 3;
			foreach($explodeTovar as $key => $value){
				
				if($value != "") {
				if(!isset($_SESSION["col_" . $value])) {$_SESSION["col_" . $value] = 1;}
				$cls = $_SESSION["col_" . $value];
					
					
					$resultPriceId = $db->query("SELECT * FROM " . PREFIX . "_price WHERE id='$value'");
					$myrowPriceId  = $db->get_array($resultPriceId);
					
					$resultCatPost = $db->query("SELECT category, title FROM " . PREFIX . "_post WHERE id='$myrowPriceId[thisid]'");
					$myrowCatPost  = $db->get_array($resultCatPost);
					
					$expCat = explode(",", $myrowCatPost['category']);
					
					$resultPostCategory = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='{$expCat[0]}'");
					$myrowPostCategory  = $db->get_array($resultPostCategory);
					
					$expName1 = explode(" ", $myrowPostCategory['name']);
					$expName2 = explode(",", $expName1[0]);
					
					if($expName2[0] == "TIKKURILA") {
						$current = $expName1[0] . " " . $expName1[1];
					} else if ($expName2[0] == "Tikkurila") {
						$current = $expName1[0] . " " . $expName1[1];
					} else if ($expName2[0] == "Teknos") {
						$current = $expName1[0] . " " . $expName1[1] . " " . $expName1[2];
					} else {
						$current = $expName2[0];
					}
					
					$resuleRazd = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '{$current}%'");
					$myrowRazd  = $db->get_array($resultRazd);
					
					
					if(!isset($_COOKIE['dle_user_id'])) {
						if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] != 0) {
													
							$t = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
							$priceYp = $t+(($t*$myrowRazd['nacenka'])/100);
						
						} else  if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] != 0){
							$priceYp = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazd['nacenka'])/100);
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp'];
						}
						
						$priceYp = round($priceYp, 2);
						$cost    = $priceYp*$cls;
						$disc    = 0;
						$itog    = $cost;
					} else {
						if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] != 0) {
							
							$t = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
							$priceYp = $t+(($t*$myrowRazd['nacenka'])/100);
						} else  if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] != 0){
							$priceYp = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazd['nacenka'])/100);
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp'];
						}
						
						$priceYp = round($priceYp, 2);
						
						$resultDisc = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE '{$current}%'");
						$myrowDisc  = $db->get_array($resultDisc);
						$countDisc  = $db->num_rows($resultDisc);
						
						if($countDisc == 0) {
							$cost    = $priceYp*$cls;
							$disc    = 0;
							$itog    = $cost;
						} else {
							$disc    = ($priceYp*$myrowDisc['discount'])/100;
							$disc    = round($disc, 2);
							$priceY  = $priceYp - $disc;
							$disc    = $disc*$cls;
							$cost    = $priceYp*$cls;
							$itog    = $priceY*$cls;;
						}
					}
					
					
					$atall += $itog;
					
					$busk .= "
						<tr>
							<td width='36%'><p class='nazv12' id='td1'>{$myrowCatPost['title']}</p></td>
							<td width='8%'><p class='nazv123' id='td2'>{$myrowPriceId['artikyl']}</p></td>
							<td width='8%'><p class='nazv123' id='td2'>{$myrowPriceId['pack']}</p></td>
							<td width='10%'><p class='nazv123' id='td3'><span style='color:#0a3a6a;'>{$priceYp} грн</span></p></td>
							<td width='8%'><p class='nazv123' id='td4'>{$cls}</p></td>
							<td width='10%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$cost} грн</span></p></td>
							<td width='9%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$disc} грн</span></p></td>
							<td width='10%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$itog} грн</span></p></td>
							
						</tr>
					";
					
					$xls->add_cell(0,$b,$myrowCatPost['title']);
					$xls->add_cell(1,$b,$myrowPriceId['artikyl']);
					$xls->add_cell(2,$b,$myrowPriceId['pack']);
					$xls->add_cell(3,$b,$priceYp . " грн.");
					$xls->add_cell(4,$b,$cls);
					$xls->add_cell(5,$b,$cost . " грн.");
					$xls->add_cell(6,$b,$disc . " грн.");
					$xls->add_cell(7,$b,$itog . " грн.");
					$b++;
				}
			}
			
			$busk .= "
				<tr>
					<td width='100%' colspan='9'><p class='nazv12' id='td1' style='float:right'><span style='color:#0a3a6a;'>Итого: </span><span style='color:#be6601;'>{$atall} грн.</span></p></td>
					
				</tr>
			";
			$b++;
			$xls->add_cell(0,$b,"Итого: " . $atall . " грн.");
			
			$xls->save_file(ROOT_DIR.'/excel/basket/basket_' . $_COOKIE['dle_user_id'] . '.xls');
	$busk .= "

		<tr>
			<td width='100%' colspan='8'><p class='nazv12' id='td1' style='float:right'><a href='https://laki-kraski.com.ua/index.php?do=print' target='_blank' style='color:#be6601;'>Распечатать</a></p></td>
			</td>
		</tr>
		";
		if(isset($_COOKIE['dle_user_id'])) {
		$busk .= "
		<tr>
			<td width='100%' colspan='8'><p class='nazv12' id='td1' style='float:right'><a href='https://laki-kraski.com.ua/excel/basket/basket_{$_COOKIE['dle_user_id']}.xls' style='color:#be6601;'>Сохранить в excel</a></p>
		</tr>
		";
		}
		$busk .= "
		</table>
		</form>
	";
	
	if(isset($_REQUEST['sendml'])) {
		$email    = $_REQUEST['email'];
		$contlico = $_REQUEST['contlico'];
		$company  = $_REQUEST['company'];
		$videjat  = $_REQUEST['videjat'];
		$telkod   = $_REQUEST['telkod'];
		$tel      = $_REQUEST['tel'];
		$dostavka = $_REQUEST['dostavka'];
		$dostot   = $_REQUEST['dostot'];
		$dostdo   = $_REQUEST['dostdo'];
		$dopinfo  = $_REQUEST['dopinfo'];
		$oplata   = $_REQUEST['oplata'];
		$my       = $_REQUEST['my'];
		$cotalog  = $_REQUEST['cotalog'];
		
		if($email == "" || $contlico == "" || $telkod == "" || $tel == "" || $dostavka == "" || $oplata == "") {
			$repportingin = "Не все поля заполнены.";
		} else {
			
			$admResult = $db->query("SELECT * FROM " . PREFIX . "_users WHERE user_id='1'");
			$admMyrow = $db->get_array($admResult);
			
			//$mailto  = $admMyrow['email'];
			//$mailto  = "bslmeisterhans@gmail.com";
			//$mailto  = "goncharuk.andrey@mail.ru";
			$mailto  = "infokraska@gmail.com";
			$charset = "windows-1251";
			$content = "text/plain";
			$subject = "Заказ";
			$message = "Контактное лицо: $contlico.\n";
			$message .= "E-mail покупателя: $email.\n";
			
			if($company != "") {$message .= "Компания: $company\n";}
			if($videjat != "") {$message .= "Вид деятельности: $videjat\n";}
			$message .= "Телефон: $telkod - $tel\n";
			if($dostavka == 1) {$message .= "Доставка: Перевозка грузов по Украине.\n";}
			if($dostavka == 2) {$message .= "Доставка: Николаев (Автомобилем, Вес > 10 кг)\n";}
			if($dostavka == 3) {$message .= "Доставка: Николаев Самовывоз\n";}
			if($dostavka == 4) {$message .= "Доставка: Николаевская обл. (Автовокзал)\n";}
			
			if($dostot != "" || $dostdo != ""){$message .= "Время доставки: от $dostot - до $dostdo\n";}
			if($cotalog != "") {$message .= "Каталог и номер цвета: $catalog.";}
			if($dopinfo != "") {$message .= "Дополнительная информация: $dopinfo\n";}
			if($oplata == "op1") {$message .= "Вид оплаты: Оплата по счету\n";}
			if($oplata == "op2") {$message .= "Вид оплаты: Пополнить карточный счет\n";}
			if($oplata == "op3") {$message .= "Вид оплаты: Оплата по счету БН\n";}
			if($oplata == "op4") {$message .= "Вид оплаты: Перевод частному лицу\n";}
			if($oplata == "op5") {$message .= "Вид оплаты: Николаев Наличная оплата\n";}
			$message .= "\n\n\n";
			$message .= "Заказ:\n";

	$tovar = $_SESSION['tovar'];
	
	$explodeTovar = explode(",", $tovar);
	
	$countTovar = count($explodeTovar)-1;
	

	if($countTovar != 0) {
			
			foreach($explodeTovar as $key => $value){
				
				if($value != "") {
				if(!isset($_SESSION["col_" . $value])) {$_SESSION["col_" . $value] = 1;}
				$cls = $_SESSION["col_" . $value];
					
					
					$resultPriceId = $db->query("SELECT * FROM " . PREFIX . "_price WHERE id='$value'");
					$myrowPriceId  = $db->get_array($resultPriceId);
					
					$resultCatPost = $db->query("SELECT category, title FROM " . PREFIX . "_post WHERE id='$myrowPriceId[thisid]'");
					$myrowCatPost  = $db->get_array($resultCatPost);
					
					$expCat = explode(",", $myrowCatPost['category']);
					
					$resultPostCategory = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='{$expCat[0]}'");
					$myrowPostCategory  = $db->get_array($resultPostCategory);
					
					$expName1 = explode(" ", $myrowPostCategory['name']);
					$expName2 = explode(",", $expName1[0]);
					
					if($expName2[0] == "TIKKURILA") {
						$current = $expName1[0] . " " . $expName1[1];
					} else if ($expName2[0] == "Tikkurila") {
						$current = $expName1[0] . " " . $expName1[1];
					} else if ($expName2[0] == "Teknos") {
						$current = $expName1[0] . " " . $expName1[1] . " " . $expName1[2];
					} else {
						$current = $expName2[0];
					}
					
					$resuleRazd = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '{$current}%'");
					$myrowRazd  = $db->get_array($resultRazd);
					
					
					if(!isset($_COOKIE['dle_user_id'])) {
						if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] != 0) {
							$t = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
							$priceYp = $t+(($t*$myrowRazd['nacenka'])/100);
						} else  if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] != 0){
							$priceYp = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazd['nacenka'])/100);
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp'];
						}
						
						$priceYp = round($priceYp, 2);
						$cost    = $priceYp*$cls;
						$disc    = 0;
						$itog    = $cost;
					} else {
						if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] != 0) {
							$t = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
							$priceYp = $t+(($t*$myrowRazd['nacenka'])/100);
						} else  if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] != 0){
							$priceYp = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazd['nacenka'])/100);
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp'];
						}
						
						$priceYp = round($priceYp, 2);
						
						$resultDisc = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE '{$current}%'");
						$myrowDisc  = $db->get_array($resultDisc);
						$countDisc  = $db->num_rows($resultDisc);
						
						if($countDisc == 0) {
							$cost    = $priceYp*$cls;
							$disc    = 0;
							$itog    = $cost;
						} else {
							$disc    = ($priceYp*$myrowDisc['discount'])/100;
							$disc    = round($disc, 2);
							$priceY  = $priceYp - $disc;
							$disc    = $disc*$cls;
							$cost    = $priceYp*$cls;
							$itog    = $priceY*$cls;;
						}
					}
					
					
					$atall += $itog;
					
					$message .= "\"" . $myrowCatPost['title'] . "\"(упаровка - \"" . $myrowPriceId['pack'] . "\") Количество - \"" . $cls . "\" на сумму - \"" . $itog . "\" грн.\n";
					
				}
				
			}
			$atall = $atall/2;
			$message .= "Общая сумма - $atall грн.";
	
		}
		
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: $content  charset=$charset\r\n";
			$headers .= "Date: ".date("Y-m-d (H:i:s)",time())."\r\n";
			$headers .= "X-Mailer: My Send E-mail\r\n";
			
			if($my == "on") {
				$headers .= "BCC: $email\r\n";
			}
			
			$dds = mail("$mailto","$subject","$message","$headers");
			if($dds) {$repportingin = "Заказ успешно отправлен.";}
		}
		
	}
	
	$busk .= "
	

			<form action='' method='post'>
			<table border='0' align='center' width='550'>
				<tr>
					<td align='center' colspan='2'><p style='color:#be6601;'><b>Форма заказа<br><br></b></p></td>
				</tr>
				<tr>
					<td align='center' colspan='2'><p style='color:#FF0000; font-size:12px;'><b>{$repportingin}<br><br></b></p></td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>E-mail *:</p></td>
					<td width='350'><input type='text' name='email' value='' class='frminput'></td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Контактное лицо *:</p></td>
					<td width='350'><input type='text' name='contlico' value='' class='frminput'></td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Компания:</p></td>
					<td width='350'><input type='text' name='company' value='' class='frminput'></td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Вид деятельности:</p></td>
					<td width='350'><input type='text' name='videjat' value='' class='frminput'></td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Телефон *:</p></td>
					<td width='350'><span class='frmtext2'>+3</span><input type='text' name='telkod' value='' style='width:50px; margin-left:7px; margin-bottom:7px; margin-top:7px; padding:0px;'> - <input type='text' name='tel' value='' style='width:170px; padding:0px;'></td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Каталог и номер цвета:</p></td>
					<td width='350'><input type='text' name='cotalog' value='' class='frminput' style='margin-bottom:7px;'></td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Доставка *:</p></td>
					<td width='350'>
						<select name='dostavka'  class='frmtext2'>
							<option value=''>Выберите доставку</option>
							<option value='1'>Перевозка грузов по Украине</option>
							<option value='2'>Николаев (Автомобилем, Вес > 10 кг)</option>
							<option value='3'>Николаев Самовывоз</option>
							<option value='4'>Николаевская обл. (Автовокзал)</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Время доставки:</p></td>
					<td width='350'><span class='frmtext2'>от</span><input type='text' name='dostot' value='' style='width:50px; margin-left:7px; margin-top:7px; padding:0px;'><span class='frmtext2'>ч</span> - <span class='frmtext2'>до</span><input type='text' name='dostdo' value='' style='width:50px; padding:0px;'><span class='frmtext2'>ч</span></td>
				</tr>
				<tr>
					<td width='200' align='right' valign='top'><p class='frmtext' style='margin-top:10px;'>Дополнительная информация:</p></td>
					<td width='350'>
						<textarea name='dopinfo' cols='40' rows='5' class='frmtext2' style='margin-top:7px; margin-bottom:7px;'></textarea>
					</td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Вид оплаты *:</p></td>
					<td width='350'>
						<select name='oplata'  class='frmtext2'>
						    <option value=''>Выберите вид оплаты</option>
							<option value='op1'>Оплата по счету</option>
							<option value='op2'>Пополнить карточный счет</option>
							<option value='op3'>Оплата по счету БН</option>
							<option value='op4'>Перевод частному лицу</option>
							<option value='op5'>Николаев Наличная оплата</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width='200' align='right'><p class='frmtext'>Отправить на мой e-mail:</p></td>
					<td width='350'><input type='checkbox' name='my' class='frminput2'></td>
				</tr>
				<tr>
					<td colspan='2' align='center'><p class='frmtext2'><br>Поля, отмеченые (*) обязательны для заполнения.<br><br><br></p></td>
				</tr>
				<tr>
					<td colspan='2' align='center'>
						<input type='hidden' name='otpr' value='basket'>
						<input type='hidden' name='do' value='basket'>
						<input type='submit' name='sendml' value='Отправить заказ' class='btn'>
						<br><br>
					</td>
				</tr>
				
			</table>
			</form>
	
	";
	
	}
	
	
} else {
		if(isset($_REQUEST['per'])) {
			$cls = $_REQUEST['cls'];
			foreach($cls as $key => $value) {
				unset($_SESSION["col_" . $key]);
				$_SESSION["col_" . $key] = $value;
			}
		}
		if(isset($_REQUEST['del']) && $_REQUEST['del'] == 'yes') {
			$did = $_REQUEST['id'];
			$tovar = $_SESSION['tovar'];
			$sssd = "";
			$explodeTovar = explode(",", $tovar);
			foreach($explodeTovar as $key => $value) {
				if($value == $did) {
					unset($explodeTovar[$key]);
					unset($_SESSION["col_" . $value]);
				}
			}
			$implodeTovar = implode(",", $explodeTovar);
			unset ($_SESSION['tovar']);
			$_SESSION['tovar'] = $implodeTovar;
			
		}
		
		if(isset($_REQUEST['delall'])) {
			
			$explodeTovar = explode(",", $tovar);
			foreach($explodeTovar as $key => $value) {
				unset($_SESSION["col_" . $value]);
			}
			unset ($_SESSION['tovar']);
		}
		
		
	
	$tovar = $_SESSION['tovar'];
	
	$explodeTovar = explode(",", $tovar);
	
	$countTovar = count($explodeTovar)-1;
	

	
	$busk = "";
	if($countTovar != 0) {
	
	$busk= "
		<form action='' method='post'>
		<table style='background-color:#eff6ff;' width='98%' id='basktable' align='center'>
			<tr>
				<td width='36%'><p class='nazv123' id='td1'>Название</p></td>
				<td width='8%'><p class='nazv123' id='td2'>Артикул</p></td>
				<td width='8%'><p class='nazv123' id='td3'>Тара</p></td>
				<td width='10%'><p class='nazv123' id='td4'>Цена</p></td>
				<td width='7%'><p class='nazv123' id='td5'>Кол-во</p></td>
				<td width='9%'><p class='nazv123' id='td6'>Сумма</p></td>
				<td width='8%'><p class='nazv123' id='td7'>Скидка</p></td>
				<td width='9%'><p class='nazv123' id='td8'>Итог</p></td>
				<td width='5%'><p class='nazv123' id='td9'>Удалить</p></td>
			</tr>
			";
			
			foreach($explodeTovar as $key => $value){
				
				if($value != "") {
				if(!isset($_SESSION["col_" . $value])) {$_SESSION["col_" . $value] = 1;}
				$cls = $_SESSION["col_" . $value];
					
					
					$resultPriceId = $db->query("SELECT * FROM " . PREFIX . "_price WHERE id='$value'");
					$myrowPriceId  = $db->get_array($resultPriceId);
					
					$resultCatPost = $db->query("SELECT category, title FROM " . PREFIX . "_post WHERE id='$myrowPriceId[thisid]'");
					$myrowCatPost  = $db->get_array($resultCatPost);
					
					$expCat = explode(",", $myrowCatPost['category']);
					
					$resultPostCategory = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='{$expCat[0]}'");
					$myrowPostCategory  = $db->get_array($resultPostCategory);
					
					$expName1 = explode(" ", $myrowPostCategory['name']);
					$expName2 = explode(",", $expName1[0]);
					if($expName2[0] == "TIKKURILA") {
						$current = $expName1[0] . " " . $expName1[1];
					} else if ($expName2[0] == "Tikkurila") {
						$current = $expName1[0] . " " . $expName1[1];
					} else if ($expName2[0] == "Teknos") {
						$current = $expName1[0] . " " . $expName1[1] . " " . $expName1[2];
					} else {
						$current = $expName2[0];
					}
					
					$resuleRazd = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '{$current}%'");
					$myrowRazd  = $db->get_array($resultRazd);
					
					
					if(!isset($_COOKIE['dle_user_id'])) {
						if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] != 0) {
							$t = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
							$priceYp = $t+(($t*$myrowRazd['nacenka'])/100);
						} else  if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] != 0){
							$priceYp = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazd['nacenka'])/100);
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp'];
						}
						
						$priceYp = round($priceYp, 2);
						$cost    = $priceYp*$cls;
						$disc    = 0;
						$itog    = $cost;
					} else {
						if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] != 0) {
							$t = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
							$priceYp = $t+(($t*$myrowRazd['nacenka'])/100);
						} else  if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] != 0){
							$priceYp = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazd['nacenka'])/100);
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp'];
						}
						
						$priceYp = round($priceYp, 2);
						
						$resultDisc = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE '{$current}%'");
						$myrowDisc  = $db->get_array($resultDisc);
						$countDisc  = $db->num_rows($resultDisc);
						
						if($countDisc == 0) {
							$cost    = $priceYp*$cls;
							$disc    = 0;
							$itog    = $cost;
						} else {
							$disc    = ($priceYp*$myrowDisc['discount'])/100;
							$disc    = round($disc, 2);
							$priceY  = $priceYp - $disc;
							$disc    = $disc*$cls;
							$cost    = $priceYp*$cls;
							$itog    = $priceY*$cls;;
						}
					}
					
					
					$atall += $itog;
					
					$busk .= "
						<tr>
							<td width='36%'><p class='nazv12' id='td1'>{$myrowCatPost['title']}</p></td>
							<td width='8%'><p class='nazv123' id='td2'>{$myrowPriceId['artikyl']}</p></td>
							<td width='8%'><p class='nazv123' id='td2'>{$myrowPriceId['pack']}</p></td>
							<td width='1,%'><p class='nazv123' id='td3'><span style='color:#0a3a6a;'>{$priceYp} грн</span></p></td>
							<td width='7%'><p class='nazv123' id='td4'>
								<input type='text' name='cls[{$value}]' value='$cls' style='width:50px;'>
							</p></td>
							<td width='9%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$cost} грн</span></p></td>
							<td width='8%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$disc} грн</span></p></td>
							<td width='9%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$itog} грн</span></p></td>
							<td width='5%'><p class='nazv123' id='td6'>
								<a href='https://laki-kraski.com.ua/index.php?do=basket&id={$myrowPriceId[id]}&del=yes' style='color:red; text-decoration:none;'>Х</a>
							</p></td>
						</tr>
					";
				}
			}
			$busk .= "
				<tr>
					<td width='100%' colspan='9'><p class='nazv12' id='td1' style='float:right'><span style='color:#0a3a6a;'>Итого: </span><span style='color:#be6601;'>{$atall} грн.</span></p></td>
					
				</tr>
			";
	$busk .= "
		<tr>
			<td colspan='9' align='center'>
				<input type='hidden' name='do' value='basket'>
				<input type='submit' name='per' value='Пересчитать' style='border:1px #03446d solid; color:#022942; background-color:#f6fbfe;'>
				<input type='submit' name='otpr' value='Оформить заказ' class='bbtn' style='border:1px #03446d solid; color:#022942; background-color:#f6fbfe;'>
				<input type='submit' name='delall' value='Очистить корзину' class='bbtn' style='border:1px #03446d solid; color:#022942; background-color:#f6fbfe; float:right;'>
				
			</td>
		</tr>
		</table>
		</form>
	";
	
	} else {
		$busk = "<div align='center'><p align='center' style='font-size:22px; color:#333;'>Товары в корзине отсутствуют.</p></div>";
		
	}
	
}}

if (strpos ( $tpl->copy_template, "[category=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[category=(.+?)\\](.*?)\\[/category\\]#ies", "check_category('\\1', '\\2', '{$category_id}')", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "[not-category=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[not-category=(.+?)\\](.*?)\\[/not-category\\]#ies", "check_category('\\1', '\\2', '{$category_id}', false)", $tpl->copy_template );
}


if (strpos ( $tpl->copy_template, "[static=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[static=(.+?)\\](.*?)\\[/static\\]#ies", "check_static('\\1', '\\2')", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "[not-static=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[not-static=(.+?)\\](.*?)\\[/not-static\\]#ies", "check_static('\\1', '\\2', false)", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "{custom" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\{custom category=['\"](.+?)['\"] template=['\"](.+?)['\"] aviable=['\"](.+?)['\"] from=['\"](.+?)['\"] limit=['\"](.+?)['\"] cache=['\"](.+?)['\"]\\}#ies", "custom_print('\\1', '\\2', '\\3', '\\4', '\\5', '\\6', '{$dle_module}')", $tpl->copy_template );
}

$config['http_home_url'] = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
$config['http_home_url'] = reset ( $config['http_home_url'] );

if (! $user_group[$member_id['user_group']]['allow_admin']) $config['admin_path'] = "";

$ajax .= <<<HTML
<div id="loading-layer" style="display:none"><div id="loading-layer-text">{$lang['ajax_info']}</div></div>{$pm_alert}
<script language="javascript" type="text/javascript">
<!--
var dle_root       = '{$config['http_home_url']}';
var dle_admin      = '{$config['admin_path']}';
var dle_login_hash = '{$dle_login_hash}';
var dle_group      = {$member_id['user_group']};
var dle_skin       = '{$config['skin']}';
var dle_wysiwyg    = '{$config['allow_comments_wysiwyg']}';
var quick_wysiwyg  = '{$config['allow_quick_wysiwyg']}';
var dle_act_lang   = ["{$lang['p_yes']}", "{$lang['p_no']}", "{$lang['p_enter']}", "{$lang['p_cancel']}"];
var menu_short     = '{$lang['menu_short']}';
var menu_full      = '{$lang['menu_full']}';
var menu_profile   = '{$lang['menu_profile']}';
var menu_send      = '{$lang['menu_send']}';
var menu_uedit     = '{$lang['menu_uedit']}';
var dle_info       = '{$lang['p_info']}';
var dle_confirm    = '{$lang['p_confirm']}';
var dle_prompt     = '{$lang['p_prompt']}';
var dle_req_field  = '{$lang['comm_req_f']}';
var dle_del_agree  = '{$lang['news_delcom']}';
var dle_del_news   = '{$lang['news_delnews']}';\n
HTML;

if ($user_group[$member_id['user_group']]['allow_all_edit']) {
	
	$ajax .= <<<HTML
var dle_notice     = '{$lang['btn_notice']}';
var dle_p_send     = '{$lang['p_send']}';
var dle_p_send_ok  = '{$lang['p_send_ok']}';
var dle_p_text     = '{$lang['p_text']}';
var dle_del_msg    = '{$lang['p_message']}';
var allow_dle_delete_news   = true;\n
HTML;

} else {
	
	$ajax .= <<<HTML
var allow_dle_delete_news   = false;\n
HTML;

}

if ($config['fast_search'] AND $user_group[$member_id['user_group']]['allow_search']) {

	$ajax .= <<<HTML
var dle_search_delay   = false;
var dle_search_value   = '';
$(function(){
	FastSearch();
});
HTML;

}

$ajax .= <<<HTML
//-->
</script>
<script type="text/javascript" src="https://laki-kraski.com.ua/engine/modules/ajax/intobusket.js"></script>
HTML;

if ($allow_comments_ajax AND ($config['allow_comments_wysiwyg'] == "yes" OR $config['allow_quick_wysiwyg'])) $js_array[] ="engine/editor/jscripts/tiny_mce/jquery.tinymce.js";

if (strpos ( $tpl->result['content'], "hs.expand" ) !== false or strpos ( $tpl->copy_template, "hs.expand" ) !== false) {
	
	if ($config['thumb_dimming']) $dimming = "hs.dimmingOpacity = 0.60;"; else $dimming = "";

	if ($config['thumb_gallery'] AND ($dle_module == "showfull" OR $dle_module == "static") ) {

	$gallery = "
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.addSlideshow({
		interval: 4000,
		repeat: false,
		useControls: true,
		fixedControls: 'fit',
		overlayOptions: {
			opacity: .75,
			position: 'bottom center',
			hideOnMouseOut: true
		}
	});";

	} else {

		$gallery = "";

	}

	$js_array[] = "engine/classes/highslide/highslide.js";

	switch ( $config['outlinetype'] ) {

		case 1 :
			$type = "hs.wrapperClassName = 'wide-border';";
			break;

		case 2 :
			$type = "hs.wrapperClassName = 'borderless';";
			break;

		case 3 :
			$type = "hs.wrapperClassName = 'less';\nhs.outlineType = null;";
			break;
	
		default :
			$type = "hs.outlineType = 'rounded-white';";
			break;


	}
	
	$ajax .= <<<HTML
<script language="javascript" type="text/javascript">  
<!--  
	hs.graphicsDir = '{$config['http_home_url']}engine/classes/highslide/graphics/';
	{$type}
	hs.numberOfImagesToPreload = 0;
	hs.showCredits = false;
	{$dimming}
	hs.lang = {
		loadingText :     '{$lang['loading']}',
		playTitle :       '{$lang['thumb_playtitle']}',
		pauseTitle:       '{$lang['thumb_pausetitle']}',
		previousTitle :   '{$lang['thumb_previoustitle']}',
		nextTitle :       '{$lang['thumb_nexttitle']}',
		moveTitle :       '{$lang['thumb_movetitle']}',
		closeTitle :      '{$lang['thumb_closetitle']}',
		fullExpandTitle : '{$lang['thumb_expandtitle']}',
		restoreTitle :    '{$lang['thumb_restore']}',
		focusTitle :      '{$lang['thumb_focustitle']}',
		loadingTitle :    '{$lang['thumb_cancel']}'
	};
	{$gallery}
//-->
</script>
HTML;

}

$tpl->set ( '{AJAX}', $ajax );
$tpl->set ( '{headers}', $metatags."\n".build_js($js_array, $config) );
if($_REQUEST['do'] == "print") {
		
	
	$tovar = $_SESSION['tovar'];
	
	$explodeTovar = explode(",", $tovar);
	
	$countTovar = count($explodeTovar)-1;
	

	
	$busk = "";
	if($countTovar != 0) {
	
	$busk= "
		<form action='' method='post'>
		<table style='background-color:#fff; border:1px #000 solid;' border='1' width='98%' id='basktable' align='center'>
			<tr>
				<td width='36%'><p class='nazv123' id='td1'>Название</p></td>
				<td width='8%'><p class='nazv123' id='td2'>Артикул</p></td>
				<td width='8%'><p class='nazv123' id='td3'>Тара</p></td>
				<td width='11%'><p class='nazv123' id='td4'>Цена</p></td>
				<td width='8%'><p class='nazv123' id='td5'>Кол-во</p></td>
				<td width='10%'><p class='nazv123' id='td6'>Сумма</p></td>
				<td width='9%'><p class='nazv123' id='td7'>Скидка</p></td>
				<td width='10%'><p class='nazv123' id='td8'>Итог</p></td>
			</tr>
			";
			
			foreach($explodeTovar as $key => $value){
				
				if($value != "") {
				if(!isset($_SESSION["col_" . $value])) {$_SESSION["col_" . $value] = 1;}
				$cls = $_SESSION["col_" . $value];
					
					
					$resultPriceId = $db->query("SELECT * FROM " . PREFIX . "_price WHERE id='$value'");
					$myrowPriceId  = $db->get_array($resultPriceId);
					
					$resultCatPost = $db->query("SELECT category, title FROM " . PREFIX . "_post WHERE id='$myrowPriceId[thisid]'");
					$myrowCatPost  = $db->get_array($resultCatPost);
					
					$expCat = explode(",", $myrowCatPost['category']);
					
					$resultPostCategory = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='{$expCat[0]}'");
					$myrowPostCategory  = $db->get_array($resultPostCategory);
					
					$expName1 = explode(" ", $myrowPostCategory['name']);
					$expName2 = explode(",", $expName1[0]);
                    
                    if($expName2[0] == "TIKKURILA") {
						$current = $expName1[0] . " " . $expName1[1];
					} else if ($expName2[0] == "Tikkurila") {
						$current = $expName1[0] . " " . $expName1[1];
					} else if ($expName2[0] == "Teknos") {
						$current = $expName1[0] . " " . $expName1[1] . " " . $expName1[2];
					} else {
						$current = $expName2[0];
					}
					
					$resuleRazd = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '{$current}%'");
					$myrowRazd  = $db->get_array($resultRazd);
					
					
					if(!isset($_COOKIE['dle_user_id'])) {
						if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] != 0) {
							$t = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
							$priceYp = $t+(($t*$myrowRazd['nacenka'])/100);
						} else  if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] != 0){
							$priceYp = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazd['nacenka'])/100);
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp'];
						}
						
						$priceYp = round($priceYp, 2);
						$cost    = $priceYp*$cls;
						$disc    = 0;
						$itog    = $cost;
					} else {
						if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] != 0) {
							$t = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
							$priceYp = $t+(($t*$myrowRazd['nacenka'])/100);
						} else  if($myrowRazd['togrn'] != 0 && $myrowRazd['nacenka'] == 0){

							$priceYp = $myrowPriceId['price_yp']*$myrowRazd['togrn'];
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] != 0){
							$priceYp = $myrowPriceId['price_yp']+(($myrowPriceId['price_yp']*$myrowRazd['nacenka'])/100);
						} else if($myrowRazd['togrn'] == 0 && $myrowRazd['nacenka'] == 0){
							$priceYp = $myrowPriceId['price_yp'];
						}
						
						$priceYp = round($priceYp, 2);
						
						$resultDisc = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE '{$current}%'");
						$myrowDisc  = $db->get_array($resultDisc);
						$countDisc  = $db->num_rows($resultDisc);
						
						if($countDisc == 0) {
							$cost    = $priceYp*$cls;
							$disc    = 0;
							$itog    = $cost;
						} else {
							$disc    = ($priceYp*$myrowDisc['discount'])/100;
							$disc    = round($disc, 2);
							$priceY  = $priceYp - $disc;
							$disc    = $disc*$cls;
							$cost    = $priceYp*$cls;
							$itog    = $priceY*$cls;;
						}
					}
					
					
					$atall += $itog;
					
					$busk .= "
						<tr>
							<td width='36%'><p class='nazv12' id='td1'>{$myrowCatPost['title']}</p></td>
							<td width='8%'><p class='nazv123' id='td2'>{$myrowPriceId['artikyl']}</p></td>
							<td width='8%'><p class='nazv123' id='td2'>{$myrowPriceId['pack']}</p></td>
							<td width='11%'><p class='nazv123' id='td3'><span style='color:#0a3a6a;'>{$priceYp} грн</span></p></td>
							<td width='8%'><p class='nazv123' id='td4'>{$cls}</p></td>
							<td width='10%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$cost} грн</span></p></td>
							<td width='9%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$disc} грн</span></p></td>
							<td width='10%'><p class='nazv123' id='td5'><span style='color:#0a3a6a;'>{$itog} грн</span></p></td>
							
						</tr>
					";
				}
			}
			$busk .= "
				<tr>
					<td width='100%' colspan='8'><p class='nazv12' id='td1' style='float:right'><span style='color:#0a3a6a;'>Итого: </span><span style='color:#be6601;'>{$atall} грн.</span></p></td>
					
				</tr>
			";
	$busk .= "
		</table>
		</form>
	";
	
	}
	
	echo $busk;
	
} else if(isset($_REQUEST['action']) && $_REQUEST['action'] == "printpricelist"){
	echo $pricelist;
} else {

if(isset($_REQUEST['category']) && isset($_REQUEST['num']) && $_REQUEST['num'] == 3) {
$rer = $db->super_query("SELECT id FROM " . PREFIX . "_category WHERE num='3' AND alt_name='{$_REQUEST['category']}'");
$getDirResult = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE catId LIKE '%{$rer['id']}%'");
if($db->num_rows($getDirResult) != 0) {
	require_once ROOT_DIR.'/engine/modules/setcolors.php';
}
}

if(isset($_REQUEST['category']) && isset($_REQUEST['num']) && $_REQUEST['num'] == 4) {
$rer = $db->super_query("SELECT id FROM " . PREFIX . "_category WHERE num='4' AND alt_name='{$_REQUEST['category']}'");
$getDirResult = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE catId LIKE '%{$rer['id']}%'");
if($db->num_rows($getDirResult) != 0) {
	require_once ROOT_DIR.'/engine/modules/setcolors.php';
}
}

if($_REQUEST['do'] == "compare" && $_REQUEST['act'] == "print") {
	$_SESSION['compare'] = $_SESSION['comparer'];
	include (ROOT_DIR.'/engine/modules/compare.php');
} else {
if($_REQUEST['do'] == 'pricelist'){
	$tpl->set ( '{content}', "<div id='dle-content'>" . $pricelist . "</div>" );
}else if($_REQUEST['do'] == 'basket') {
	$tpl->set ( '{content}', "<div id='dle-content'>" . $busk . "</div>" );
} else if($_REQUEST['do'] == 'compare') { 
	if(isset($_REQUEST['del'])) {
		if($_REQUEST['del'] == "all") {
			unset($_SESSION['comparer']);
		}
		
		if($_REQUEST['del'] != "all") {
			$es = explode(",", $_SESSION['comparer']);
			foreach($es as $key => $value){
				
				if($value == $_REQUEST['del'] || $value == "undefined") {
					unset($es[$key]);
				}	
			}
			$ims = implode(",",$es);
			$_SESSION['comparer'] = $ims;
			
		}
	}
	$_SESSION['compare'] = $_SESSION['comparer'];
	include (ROOT_DIR.'/engine/modules/compare.php');
	$tpl->set ( '{content}', $compare );
} else if (isset($_REQUEST['doaction']) && $_REQUEST['doaction'] == 'validating'){
	$tpl->set ( '{content}', "$reportRegistration" );
} else if(isset($_REQUEST['send']) && $_REQUEST['send']=="send"){
	$tpl->set ( '{content}', "<p class='frmtext' style='text-align:center; margin-top:25px; font-size:18px;'>Ваше письмо успешно отправлено.</p>" );
} else if($db->num_rows($getDirResult) != 0 && isset($_REQUEST['category']) && $_REQUEST['num'] == 3) {
	$tpl->set ( '{content}', "$setcolors" );
} else if($db->num_rows($getDirResult) != 0 && isset($_REQUEST['category']) && $_REQUEST['num'] == 4) {
	$tpl->set ( '{content}', "$setcolors" );
} else { 
$tpl->set ( '{content}', "<div id='dle-content'>" . $tpl->result['content'] . "</div>" );
}

$tpl->compile ( 'main' );
if( $member_id['user_group'] == '5' ) {
$tpl->result['main'] = str_replace( '</body>', "", $tpl->result['main'] );
$tpl->result['main'] = str_replace( '</html>', "", $tpl->result['main'] );
}
$tpl->result['main'] = str_replace ( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['main'] );
if ($replace_url) $tpl->result['main'] = str_replace ( $replace_url[0]."/", $replace_url[1]."/", $tpl->result['main'] );
$tpl->result['main'] = str_replace ( 'img src="http://'.$_SERVER['HTTP_HOST'].'/', 'img src="/', $tpl->result['main'] );

echo $tpl->result['main'];
$tpl->global_clear ();
$db->close ();
if( $member_id['user_group'] == '5' ) {
include "engine/skins/images/footer.png";
echo "\n</body>\n</html>";
}
echo "\n<!-- DataLife Engine Nulled -->\r\n";

GzipOut();

}
}



?>