<?php
if(isset($_SESSION['dle_user_id']) && $_SESSION['dle_user_id'] == "97") {
//$final = array();
//$b_st = $db->query("SELECT `name` FROM `dle_catt`");
//$cur = 1;
//$st = 100;
//while($row_st=$db->get_array($b_st)) {
//$st++;
//	$arr = array();
//	$first = array();
//	$rr = array();
//	$fid = array();
//	
//	if($row_st['name'] == "Herberts(Германия)" || $row_st['name'] == "Tikkurila Paints(Финляндия)" || $row_st['name'] == "TIKKURILA coatings(Финляндия)" || $row_st['name'] == "ICSAM(Италия)" || $row_st['name'] == "Teknos Oy (Финляндия)" || $row_st['name'] == "Teknos Oy Пром (Финляндия)") {
//		$name = explode("(", $row_st['name']);
//	} elseif($row_st['name'] == "Caskol, Casco Adhesives (Нидерланды)") {
//		$name = explode(",", $row_st['name']);
//	} else {
//		$name = explode(" ", $row_st['name']);
//	}
//	//if($row_st['name'] == "Herberts(Германия)" || $row_st['name'] == "Tikkurila Paints(Финляндия)") {
//		$res = $db->query("SELECT id FROM dle_category WHERE name LIKE '$name[0]%' AND num='1' ORDER BY posi DESC");
//		while($myr = $db->get_array($res)) {
//			$first[] = $myr['id'];
//		}
//		foreach($first as $k => $v) {
//			$res = $db->query("SELECT id,parentid FROM dle_category WHERE id='$v'");
//			$myr = $db->get_array($res);
//			$id = $myr['id'];
//			while($myr['parentid'] != 0) {
//				$res = $db->query("SELECT * FROM dle_category WHERE id='{$myr['parentid']}'");
//				$myr = $db->get_array($res);
//				
//				if(!array_key_exists($myr['id'], $arr)) {
//					$fid[$id][] = $myr['id'];
//					$arr[$myr['id']] = $id;
//				}
//				
//			}
//			$rr[] = $myr['id'];
//		}
//		$rr = array_unique($rr);
//		$res = $db->query("SELECT name, descr FROM dle_category WHERE name LIKE '$name[0]%' AND num='1' ORDER BY posi DESC");
//		$myr = $db->get_array($res);
//		$final[] = array("id" => "1000000".$cur, 
//						 "parentid" => '0',
//						 "name" => $myr['name'],
//						 "arr" => $rr,
//						 'cur' => $st,
//						 "descr" => $myr['descr'],
//						 "sub" => $fid);
//
//		
//	//}
//$cur++;
//}
//
////if(isset($_REQUEST['ane']) && $_REQUEST['ane'] == "yes") {
////echo "<pre>";
////print_r($final);
////}
//foreach($final as $key => $value) {
//	$link=$config['http_home_url'].'index.php?do=cat&category='.$value['alt_name'];
//	if (file_exists(ROOT_DIR."/templates/{$config['skin']}/images/icon/{$value['alt_name']}.gif"))
//		$icon="{THEME}/images/icon/{$value['alt_name']}.gif";
//	else
//		$icon='';
//	
//	
//
//	$db->query("INSERT INTO dle_category2 SET id='{$value['id']}',parentid='{$value['parentid']}',name='{$value['name']}',alt_name='{$value['alt_name']}',descr='{$value['descr']}', keywords='{$value['descr']}',num='1'");
//	$catlist5.="aaa.add({$value['id']},{$value['parentid']},'{$value['name']}','{$link}','{$value['descr']}','','{$icon}','{$icon}');";
//	
//
//	foreach($value['sub'] as $k => $v) {
//		$v = array_reverse($v);
//		if(count($v) > 1) {
//			if(in_array($v[0], $value['arr'])) {
//				$c = $value['id'];
//				
//			} else {
//				$r = $db->query("SELECT parentid FROM dle_category WHERE id='$v[0]'");
//				$m = $db->get_array($r);
//				$c = $value['cur'].$m['parentid'];
//			}
//		}
//		$rr = $res = $db->query("SELECT alt_name FROM dle_category WHERE id='$k'");
//		$mm = $db->get_array($rr);
//		foreach($v as $kk => $vv) {
//		
//			$res = $db->query("SELECT id,parentid,alt_name,name,descr FROM dle_category WHERE id='$vv'");
//			$myr = $db->get_array($res);
//			
//			if(count($v) == 1) {
//				$c = $value['cur'].$myr['parentid'];
//			}
//			$link=$config['http_home_url'].'index.php?do=cat&category='.$mm['alt_name'];
//			if (file_exists(ROOT_DIR."/templates/{$config['skin']}/images/icon/{$myr['alt_name']}.gif"))
//				$icon="{THEME}/images/icon/{$myr['alt_name']}.gif";
//			else
//				$icon='';
//			$db->query("INSERT INTO dle_category2 SET id='{$value['cur']}{$myr['id']}',parentid='$c',name='{$myr['name']}',alt_name='{$mm['alt_name']}',descr='{$myr['descr']}', keywords='{$value['descr']}', num='1'");
//			$catlist5.="aaa.add({$value['cur']}{$myr['id']},$c,'{$myr['name']}','{$link}','{$myr['descr']}','','{$icon}','{$icon}');";
//			$c = $value['cur'].$vv;
//			
//		}
//		
//	}
//
//}
//$cur = 1170;
//$res = $db->query("SELECT * FROM dle_category2");
//while($myr = $db->get_array($res)) {
//	$id = $myr['id'];
//	$db->query("UPDATE dle_category2 SET parentid='$cur' WHERE parentid='$id'");
//	$db->query("UPDATE dle_category2 SET id='$cur' WHERE id='$id'");
//$cur++;
//}

//$res = $db->query("SELECT * FROM dle_category2");
//while($myr = $db->get_array($res)) {
//	$db->query("INSERT INTO dle_category SET id='{$myr['id']}', parentid='{$myr['parentid']}', name='{$myr['name']}', alt_name='{$myr['alt_name']}', descr='{$myr['descr']}', keywords='{$myr['keywords']}', num='5'");
//}

//$db->query("DELETE FROM dle_category WHERE num='4'");

//$db->query("UPDATE dle_category SET num='4' WHERE num='0'");

}
$catlist5='';
$b=$db->query ("SELECT * from `dle_category` WHERE num='5' $cc ORDER BY `posi`");
while ($row=$db->get_array($b))
{
$link=$config['http_home_url'].'index.php?do=cat&category='.$row['alt_name'];
if (file_exists(ROOT_DIR."/templates/{$config['skin']}/images/icon/{$row['alt_name']}.gif"))
	$icon="{THEME}/images/icon/{$row['alt_name']}.gif";
else
	$icon='';

$catlist5.="aaa.add({$row['id']},{$row['parentid']},'{$row['name']}','{$link}','{$row['descr']}','','{$icon}','{$icon}');";
}

?>
