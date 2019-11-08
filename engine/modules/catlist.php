 <?php
/*
Формирование дерева категорий

Автор: Karfax
E-mail: dimahome@ukr.net
ICQ: 217923413
(C) Karfax, 2007
*/

$cuser = $_SESSION['dle_user_id'];
$resUser = $db->query("SELECT * FROM dle_users WHERE user_id='$cuser'");
$myrUser = $db->get_array($resUser);
$user = $myrUser['name'];
$usl = "";
$temp = array();
$res = $db->query("SELECT * FROM dle_catt_hidden ORDER BY id ASC");
if($db->num_rows($res) != 0){
	while($myr = $db->get_array($res)){
		if($myr['users'] != "") {
			$ex = explode(",", $myr['users']);
			if($cuser != 0) {
				if(!in_array($user, $ex)) {
					$t = $db->super_query("SELECT * FROM dle_catt WHERE id='" . $myr['catID'] . "'");
					$temp[] = $t['name'];
				}
			} else {
				$t = $db->super_query("SELECT * FROM dle_catt WHERE id='" . $myr['catID'] . "'");
				$temp[] = $t['name'];
			}
		} else {
			$t = $db->super_query("SELECT * FROM dle_catt WHERE id='" . $myr['catID'] . "'");
			$temp[] = $t['name'];
		}
	}
}
//print_r($temp);
$count = count($temp);
$c = 1;
$cc = "";
foreach($temp as $k => $v) {
	$cc .= "'$v'";
	if($c < $count) {
		$cc .= ",";
	}
	$c++;
}


if(in_array("Tikkurila Paints(Финляндия)", $temp)) {$tpl->set ( '{yslovie1_1}', "style='display:none;'" );}
if(in_array("Teknos Oy (Финляндия)", $temp)) {$tpl->set ( '{yslovie1_2}', "style='display:none;'" );}
if(in_array("Sadolin (Швеция)", $temp)) {$tpl->set ( '{yslovie1_3}', "style='display:none;'" );}
if(in_array("Pinotex (Эстония)", $temp)) {$tpl->set ( '{yslovie1_4}', "style='display:none;'" );}
if(in_array("Hammerite (Англия)", $temp)) {$tpl->set ( '{yslovie1_5}', "style='display:none;'" );}
if(in_array("Benjamin Moore (Америка USA)", $temp)) {$tpl->set ( '{yslovie1_6}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie1_7}', "style='display:none;'" );}
if(in_array("Rilak (Латвия)", $temp)) {$tpl->set ( '{yslovie1_8}', "style='display:none;'" );}
if(in_array("V-33 (Франция)", $temp)) {$tpl->set ( '{yslovie1_9}', "style='display:none;'" );}
if(in_array("Alpina (Германия)", $temp)) {$tpl->set ( '{yslovie1_10}', "style='display:none;'" );}
if(in_array("Caparol (Германия)", $temp)) {$tpl->set ( '{yslovie1_11}', "style='display:none;'" );}
if(in_array("Beckers (Швеция)", $temp)) {$tpl->set ( '{yslovie1_12}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie1_13}', "style='display:none;'" );}
if(in_array("Triora (Украина)", $temp)) {$tpl->set ( '{yslovie1_14}', "style='display:none;'" );}

if(in_array("TIKKURILA coatings(Финляндия)", $temp)) {$tpl->set ( '{yslovie2_1}', "style='display:none;'" );}
if(in_array("Teknos Oy Пром (Финляндия)", $temp)) {$tpl->set ( '{yslovie2_2}', "style='display:none;'" );}
if(in_array("AkzoNobel Wood Coat (Швеция)", $temp)) {$tpl->set ( '{yslovie2_3}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie2_4}', "style='display:none;'" );}
if(in_array("Sayerlack (Италия)", $temp)) {$tpl->set ( '{yslovie2_5}', "style='display:none;'" );}
if(in_array("Sirca (Италия)", $temp)) {$tpl->set ( '{yslovie2_6}', "style='display:none;'" );}
if(in_array("ICSAM(Италия)", $temp)) {$tpl->set ( '{yslovie2_7}', "style='display:none;'" );}
if(in_array("Rilak (Латвия)", $temp)) {$tpl->set ( '{yslovie2_8}', "style='display:none;'" );}

if(in_array("Jowat (Германия)", $temp)) {$tpl->set ( '{yslovie3_1}', "style='display:none;'" );}
if(in_array("Kiilto (Финляндия)", $temp)) {$tpl->set ( '{yslovie3_2}', "style='display:none;'" );}
if(in_array("AkzoNobel Wood Coat (Швеция)", $temp)) {$tpl->set ( '{yslovie3_3}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_4}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_5}', "style='display:none;'" );}
if(in_array("Decollo Dudipren (Италия)", $temp)) {$tpl->set ( '{yslovie3_6}', "style='display:none;'" );}
if(in_array("Caskol, Casco Adhesives (Нидерланды)", $temp)) {$tpl->set ( '{yslovie3_7}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_8}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_9}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_10}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_11}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_12}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_13}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_14}', "style='display:none;'" );}
//if(in_array("", $temp)) {$tpl->set ( '{yslovie3_15}', "style='display:none;'" );}


if($cc != "") {
	$cc = "AND NOT name IN($cc)";
}

$catlist='';
$b=$db->query ("SELECT * from `dle_category` WHERE num='1' $cc ORDER BY `posi`");
while ($row=$db->get_array($b))
{
$link=$config['http_home_url'].'index.php?do=cat&category='.$row['alt_name'];
if (file_exists(ROOT_DIR."/templates/{$config['skin']}/images/icon/{$row['alt_name']}.gif"))
	$icon="{THEME}/images/icon/{$row['alt_name']}.gif";
else
	$icon='';

$catlist.="d.add({$row['id']},{$row['parentid']},'{$row['name']}','{$link}','{$row['descr']}','','{$icon}','{$icon}');";


}


$catlist2='';
$b=$db->query ("SELECT * from `dle_category` WHERE num='2' ORDER BY `posi`");
while ($row=$db->get_array($b))
{
$link=$config['http_home_url'].'index.php?do=cat&category='.$row['alt_name'];
if (file_exists(ROOT_DIR."/templates/{$config['skin']}/images/icon/{$row['alt_name']}.gif"))
	$icon="{THEME}/images/icon/{$row['alt_name']}.gif";
else
	$icon='';

$catlist2.="e.add({$row['id']},{$row['parentid']},'{$row['name']}','{$link}','{$row['descr']}','','{$icon}','{$icon}');";
}
$catlist2='';
$b=$db->query ("SELECT * from `dle_category` WHERE num='2' ORDER BY `posi`");
while ($row=$db->get_array($b))
{
$link=$config['http_home_url'].'index.php?do=cat&category='.$row['alt_name'];
if (file_exists(ROOT_DIR."/templates/{$config['skin']}/images/icon/{$row['alt_name']}.gif"))
	$icon="{THEME}/images/icon/{$row['alt_name']}.gif";
else
	$icon='';

$catlist2.="e.add({$row['id']},{$row['parentid']},'{$row['name']}','{$link}','{$row['descr']}','','{$icon}','{$icon}');";
}

$catlist3='';
$b=$db->query ("SELECT * from `dle_category` WHERE num='3' ORDER BY `posi`");
while ($row=$db->get_array($b))
{
$link=$config['http_home_url'].'index.php?do=cat&category='.$row['alt_name'];
if (file_exists(ROOT_DIR."/templates/{$config['skin']}/images/icon/{$row['alt_name']}.gif"))
	$icon="{THEME}/images/icon/{$row['alt_name']}.gif";
else
	$icon='';

$catlist3.="a.add({$row['id']},{$row['parentid']},'{$row['name']}','{$link}&num=3','{$row['descr']}','','{$icon}','{$icon}');";
}

$catlist4='';
$b=$db->query ("SELECT * from `dle_category` WHERE num='4' ORDER BY `posi`");
while ($row=$db->get_array($b))
{
$link=$config['http_home_url'].'index.php?do=cat&category='.$row['alt_name'];
if (file_exists(ROOT_DIR."/templates/{$config['skin']}/images/icon/{$row['alt_name']}.gif"))
	$icon="{THEME}/images/icon/{$row['alt_name']}.gif";
else
	$icon='';

//$catlist4.="aa.add({$row['id']},{$row['parentid']},'{$row['name']}','{$link}','{$row['descr']}','','{$icon}','{$icon}');";
$catlist4.="aa.add({$row['id']},{$row['parentid']},'{$row['name']}','{$link}&num=4','{$row['descr']}','','{$icon}','{$icon}');";
}


if(isset($_COOKIE['dle_user_id']) && ($_COOKIE['dle_user_id'] == '97' || $_COOKIE['dle_user_id'] == '308' || $_COOKIE['dle_user_id'] == '1')) {
	$nmenu = array();
	
	function createmenu($sub=0) {
		global $db;
		
		$res = $db->query("SELECT * FROM dle_category WHERE parentid='$sub' AND num='1'");
		while($myr = $db->get_array($res)) {
			
			$subres = $db->query("SELECT * FROM dle_category WHERE parentid='{$myr['id']}'");
			if($db->num_rows($subres) != 0) {
				$myr['sub'] = createmenu($myr['id']);
			}
			
			$nmenu[] = $myr;
		}
		
		return $nmenu;
	}
	$nmenu = createmenu();

	function showsubs($nmenu, $tt, $num) {
		foreach($nmenu as $key => $value) {
			if(isset($value['sub'])) {
				$false = "onclick='return false;'";
				$curr = "<ul id='$num'>";
				
				$curr .= showsubs($value['sub'], '2', $num);
				$curr .= "</ul>";
			} else {$false = "style='font-style:italic;'";}
			if($tt == 1) {$idd = "id='sli'";} else {$idd = "id='lis'";}

			$menu .= "<li $idd id='$num'>
						<a href='' $false>{$value['name']}</a>
						$curr
					  </li>";
					  $num++;
			if($tt == 1) {$menu .= "<div class='dots'>&nbsp;</div>";}
		}
		
		return $menu;
	}

	function showmenu($nmenu) {
		$menu .= "<ul id='nmenu'>";
			foreach($nmenu as $key => $value) {
				if(isset($value['sub'])) {
					$curr = "<ul id='sul'>";
					$curr .= showsubs($value['sub'], '1');
					$curr .= "</ul>";
				}
				$menu .= "<li id='fli'>
							<img src='{THEME}/images/arr-orange.gif' style='position:relative; top:-2px;'>&nbsp;
							<a href='' id='fa'>{$value['name']}</a>
							<div class='bgMenu'>
							$curr
							</div>
						  </li>
						  <p id='dots'>...........................................................................................................................................</p>
						  ";
						  
			}
		$menu .= "</ul>";
		return $menu;
	}

	$pmenu = showmenu($nmenu);






//$menu = "";
//function showsubs($nmenu) {
//	$end = count($nmenu)-1;
//	foreach($nmenu as $key => $value) {
//		if($key == 0) {$menu .= "<ul>";}
//		$menu .= "<li id='sli'>
//					&nbsp;&nbsp;<a href=''>{$value['name']}</a>
//				  </li>";
//		if($key == $end) {$menu .= showsubs($value['sub']); $menu .= "</ul>";}
//	}
//	return $menu;
//}

//$menu .= "<ul id='nmenu'>";
//foreach($nmenu as $key => $value) {
//	$menu .= "
//		<li id='fli'>
//			<img src='{THEME}/images/arr-orange.gif' style='position:relative; top:-2px;'>&nbsp;
//			<a href='' id='fa'>{$value['name']}</a>
//			<div class='bgMenu'>
//			";
//				if($value['sub'] != 0) {
//					$curr = showsubs($value['sub']);
//				} else {
//					$curr = "";
//				}
//			$menu .= "
//			{$curr}
//			</div>
//		</li>
//		<p id='dots'>...........................................................................................................................................</p>
//	";
//}
//$menu .= "</ul>";

//$pmenu = $menu;

//	echo "<pre>";
//	print_r($nmenu);

}



include("catlist_bv.php");






?>
