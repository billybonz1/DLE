<?php
echoheader( "pcat", $lang['images_head'] );
if(!isset($_REQUEST['pcatTranslit'])) {

if(isset($_REQUEST['addSCol'])) {

	if($_REQUEST['nameSCol'] != "") {
		$name = $_REQUEST['nameSCol'];
		$translit = totranslit($name);
		$checkTranslit = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE translit='{$translit}'");
		if($db->num_rows($checkTranslit) == 0){
			$check = $db->query("INSERT INTO " . PREFIX . "_directories(translit,name,type,text,stolb,catId,stroki) VALUES('$translit','$name','2','','','','')");
			$db->query("INSERT INTO " . PREFIX . "_color_p_size(translit, width, height) VALUES('$translit','','')");
			if($check){
				$message = "Раздел успешно создан.<br /><br />";
			}
		} else {
			$message = "Такой раздел уже существует.";
		}
	} else {
		$message = "Поле раздела не заполнено. <br /><br />";
	}
	

}
if(isset($_REQUEST['redSCol'])) {
	$name = $_REQUEST['nameSCol'];
	$tr = $_REQUEST['tr'];
	$translit = totranslit($name);
	$check = $db->query("UPDATE " . PREFIX . "_directories SET name='{$name}', translit='{$translit}' WHERE translit='$tr'");
	$db->query("UPDATE " . PREFIX . "_color_p_size SET translit='{$translit}' WHERE translit='$tr'");
	$db->query("UPDATE " . PREFIX . "_color_p SET translit='{$translit}' WHERE translit='$tr'");
	if($check) {
		$message = "Раздел успешно отредактирован.<br><br>";
	}
}

if(isset($_REQUEST['act']) && $_REQUEST['act'] == "del") {
	$db->query("DELETE FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['translit']}'");
	$db->query("DELETE FROM " . PREFIX . "_color_p WHERE translit='{$_REQUEST['translit']}'");
	$db->query("DELETE FROM " . PREFIX . "_color_p_size WHERE translit='{$_REQUEST['translit']}'");
}
	
echo "<br>";
echo "<table border='0' align='center'>";
	echo "<tr><td colspan='2' align='center'><p style='color:red;'>{$message}</p></td></tr>";
	echo "<tr>";
	echo "<form action='" . $_SERVER['PHP_SELF'] . "?mod=pcat' method='post'>";
		if(isset($_REQUEST['act']) && $_REQUEST['act'] == "red") {
			$sel = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE translit='" . $_REQUEST['translit'] . "'");
			$myr = $db->get_array($sel);
			echo "<td><input type='text' name='nameSCol' value='{$myr['name']}' style='padding:0px; margin:0px; font-size:12px; width:200px;'></td>";
			echo "<input type='hidden' name='tr' value='{$_REQUEST['translit']}'>";
			echo "<td><input type='submit' name='redSCol' value='Редактировать раздел' class='edit'></td>";
		} else {
			echo "<td><input type='text' name='nameSCol' value='' style='padding:0px; margin:0px; font-size:12px; width:200px;'></td>";
			echo "<td><input type='submit' name='addSCol' value='Добавить раздел' class='edit'></td>";
		}
	echo "</form>";
	echo "</tr>";
echo "</table>";
echo "<br>";

$resultRaz = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE type='2'");
if($db->num_rows($resultRaz) != 0){
$myrowRaz  = $db->get_array($resultRaz);
echo "
<style>
	#table {border:1px #7f9db9 solid; border-color:#7f9db9;}
	#firsttr p {color:#06538f;}
	#redd p {margin:1px; padding:1px;}
</style>
	<table cellpadding='0' cellspacing='0' border='1' width='100%' id='table'>
		<tr id='firsttr' style='background-color:#d7e2ed;'>
			<td align='center' width='85%'><p style='padding:7px;'>Название раздела</p></td>
			<td align='center' width='15%'><p style='padding:7px;'>Действие</p></td>
		</tr>
		";


do {
	echo "
		<tr id='redd'>
			<td align='left' width='85%'><p style='padding:7px;'><a href='/admin.php?mod=pcat&pcatTranslit={$myrowRaz['translit']}'>{$myrowRaz['name']}</a></p></td>
			<td align='center' width='15%'>
				<p><a href='/admin.php?mod=pcat&translit={$myrowRaz['translit']}&act=del' style='color:#e64c4c;'>Удалить</a></p>
				<p><a href='/admin.php?mod=pcat&translit={$myrowRaz['translit']}&act=red' style='color:#e64c4c;'>Редактировать</a></p>
			</td>
		</td>
	";
} while($myrowRaz  = $db->get_array($resultRaz));
		
echo "
	</table>
";
}

} else {
	
//----------------pcat-----------------
	
	
	
if(isset($_REQUEST['addOpis'])) {
	$err = 0;
	$cats = array();
	$db->query("UPDATE " . PREFIX . "_color_p_size SET width='{$_REQUEST['wid']}', height='{$_REQUEST['hid']}' WHERE translit='{$_REQUEST['pcatTranslit']}'");
	if($_REQUEST['wheretoshow'] != "") {
	$selectCats = $db->query("SELECT * fROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['pcatTranslit']}'");
	$myrowCats  = $db->get_array($selectCats);
	$explCats = explode(",", $myrowCats['catId']);
		foreach($_REQUEST['wheretoshow'] as $value) {
			if(!in_array($value, $explCats)) {
				$resultDubl = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE catId LIKE '%{$value}%'");
				if($db->num_rows($resultDubl) != 0) {
					$sel = $db->query("SELECT * FROM " . PREFIX . "_category WHERE id='{$value}'");
					$myr = $db->get_array($sel);
					$message .= "Категория <b>{$myr['name']}</b> уже используется, выберите другую.<br />";
				} else {
					array_push($explCats,$value);
				}
			}
		}
	}
	if($explCats != ""){
		foreach($explCats as $value) {
			if($value != ""){
				if(in_array($value,$_REQUEST['wheretoshow'])) {
					array_push($cats, $value);
				}
			}
		}
	}
	$implode = implode(",", $cats);
	if($err == 0) {
	$db->query("UPDATE " . PREFIX . "_directories SET text='{$_REQUEST['colorOpis']}',stolb='{$_REQUEST['stolb']}',catId='{$implode}', stroki='{$_REQUEST['stroki']}' WHERE translit='{$_REQUEST['pcatTranslit']}'");
	}
	
	if($_REQUEST['wheretoshow'] != "") {
		$nameRus = $db->super_query("SELECT * FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['pcatTranslit']}'");
	}
}
	
$colorText = $db->super_query("SELECT text,stolb,catId,stroki FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['pcatTranslit']}'");

$catId = $db->super_query("SELECT * FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['pcatTranslit']}'");
$explodeCatId = explode(",", $catId['catId']);
$option = CategoryNewsSelection($explodeCatId,0);
echo "<form action='{$_SERVER['PHP_SELF']}?mod=pcat&pcatTranslit={$_REQUEST['pcatTranslit']}' method='post'>";
echo "<p style='color:red; width:100%;'>{$message}</p>";
echo "<div class=\"navigation\">Выберите директорию для отоброжения страницы:</div>";
echo "<select name='wheretoshow[]' multiple size='7'>";
	

	echo $option;
	
echo "</select>";
echo "<div class=\"navigation\" style='margin-top:5px;'>Количество столбцов на странице: <input type='text' name='stolb' value='{$colorText['stolb']}' style='padding:0px; border:1px #ccc solid; width:25px;'></div>";
echo "<div class=\"navigation\" style='margin-top:5px;'>Количество строк на странице: <input type='text' name='stroki' value='{$colorText['stroki']}' style='padding:0px; border:1px #ccc solid; width:25px;'></div>";

$hw = $db->super_query("SELECT * FROM " . PREFIX . "_color_p_size WHERE translit='{$_REQUEST['pcatTranslit']}'");

echo "<div class=\"navigation\" style='margin-top:5px;'>
Размеры маленьких картинок:
ширина- <input type='text' name='wid' value='{$hw['width']}' style='padding:0px; border:1px #ccc solid; width:30px;'> -- высота- <input type='text' name='hid' value='{$hw['height']}' style='padding:0px; border:1px #ccc solid; width:30px;'></div>";

echo "<div class=\"navigation\" style='margin-top:5px;'>Описание раздела:</div><br>";
include (ENGINE_DIR . '/editor/color.php');
echo "<input type='hidden' name='pcatTranslit' value='{$_REQUEST['pcatTranslit']}'>";
echo "<center><input type='submit' class='edit' name='addOpis' value='Отправить' style='margin-top:3px; margin-bottom:3px;'></center><br><br>";
echo "</form>";




if(isset($_REQUEST['addsp'])) {
$str = $_REQUEST['cpiz'];
$str = str_replace(" ", "", $str);
$str = str_replace("<", "///", $str);

$e = explode("
", $str);
$str = implode("", $e);
$explode = explode("#", $str);
$color = array();
$name = array();
foreach($explode as $k => $v) {
	if($k != 0) {
		$toremove = substr($v, 0, 6);

			array_push($color, $toremove);
		
	}
}


$expl = explode(">", $str);
foreach($expl as $key => $value) {
$toremove2 = "";
	if($key != 0) {
			if(substr($value,0,1) != "/" && substr($value,0,1) != "&") {
			$toremove2 = substr($value, 0, strpos($value, "///"));
			if($toremove2 != "" && strlen($toremove2) != 1 && strlen($toremove2) != 2) {
				array_push($name, $toremove2);
				
			}
		}
		
	}
}

for($i=0; $i<count($color); $i++) {
	$db->query("INSERT INTO " . PREFIX . "_color_p(id,color,name,translit,text) VALUES (null,'" . $color[$i] . "', '" . $name[$i] . "', '" . $_REQUEST['pcatTranslit'] . "', '')");
}

$rep = "Операция выполнена успешно. Соответствие: " . count($color) . " --- " . count($name);
}

echo "<p style='color:red; width:100%;'>{$rep}</p>";
echo "<form action='" . $_SERVER['PHP_SELF'] . "?mod=pcat&pcatTranslit={$_REQUEST['pcatTranslit']}' method='post'>";
	echo "<p class=\"navigation\">Блок для HTML:</p>";
	echo "<textarea name='cpiz' id='ds' style='width:98%;' rows='12'></textarea>";
	echo "<center><input type='submit' name='addsp' value='Стырить :)' class='edit'></center>";
echo "</form>";	

$rr = "";
	if(isset($_REQUEST['doact']) && $_REQUEST['doact'] == "del") {
		$check = $db->query("DELETE FROM " . PREFIX . "_color_p WHERE id='" . $_REQUEST['colID'] . "'");
		if($check) {
			$rr = "Цвет успешно удален.";
		}
	}

	
	if(isset($_REQUEST['redAllColors'])) {
		$names = $_REQUEST['thisName'];
		$colors = $_REQUEST['thisColor'];
		$text = $_REQUEST['thisText'];
		
		foreach($names as $key => $value) {
			$db->query("UPDATE " . PREFIX . "_color_p SET name='" . $value . "' WHERE id='" . $key . "'");
		}
		
		foreach($colors as $key => $value) {
			$db->query("UPDATE " . PREFIX . "_color_p SET color='" . $value . "' WHERE id='" . $key . "'");
		}
		
		foreach($text as $key => $value) {
			$db->query("UPDATE " . PREFIX . "_color_p SET text='" . $value . "' WHERE id='" . $key . "'");
		}
		$rr = "Изменения успешно выполнены.";
	}
	
	
	$colorsResult = $db->query("SELECT * FROM " . PREFIX . "_color_p WHERE translit='{$_REQUEST['pcatTranslit']}'");
	if($db->num_rows($colorsResult) != 0) { 
	$colors = $db->get_array($colorsResult);
		echo "
		<form action='" . $_SERVER['PHP_SELF'] . "?mod=pcat&pcatTranslit={$_REQUEST['pcatTranslit']}' method='post'>
		<style>
			#table {border:1px #7f9db9 solid; border-color:#7f9db9;}
			#firsttr p {color:#06538f;}
			#redd p {margin:1px; padding:1px;}
			.thisin input {margin:0px; padding:0px; font-size:11px; color:#06538f;}
		</style><br><br>
		<p style='color:red; text-align:center;'>{$rr}</p>
		<br>
			<table cellpadding='0' cellspacing='0' border='1' width='100%' id='table'>
				<tr id='firsttr' style='background-color:#d7e2ed;'>
					<td align='center' width='40%'><p style='padding:7px;'>Изображение</p></td>
					<td align='center' width='20%'><p style='padding:7px;'>Код цвета</p></td>
					<td align='center' width='20%'><p style='padding:7px;'>Номер цвета</p></td>
					<td align='center' width='20%'><p style='padding:7px;'>Описание</p></td>
					<td align='center' width='20%'><p style='padding:7px;'>Удаление</p></td>
				</tr>
				";
		do {
			echo "
				<tr id='firsttr'>
					<td align='center' width='30%'>
						<div style='background-color:#" . $colors['color'] . "; width:98%; height:25px;'></div>
					</td>
					<td align='center' width='15%'><p style='padding:7px;' class='thisin'>#<input type='text' name='thisColor[" . $colors['id'] . "]' value='" . $colors['color'] . "' size='6' maxlength='6'></p></td>
					<td align='center' width='20%'><p style='padding:7px;' class='thisin'><input type='text' name='thisName[" . $colors['id'] . "]' value='" . $colors['name'] . "' size='20'></p></td>
					<td align='center' width='15%'><p style='padding:7px;' class='thisin'><input type='text' name='thisText[" . $colors['id'] . "]' value='" . $colors['text'] . "' size='20'></p></td>
					<td align='center' width='20%'><p style='padding:7px;'>
					<a href='/admin.php?mod=pcat&pcatTranslit=" . $_REQUEST['pcatTranslit'] . "&colID=" . $colors['id'] . "&doact=del' style='color:red;'>Удалить</a>
					</p></td>
				</tr>
			";
		} while($colors = $db->get_array($colorsResult));
		echo "
			<tr>
				<td colspan='4' align='center'><br><br>
					<input type='submit' name='redAllColors' value='Сохранить изменения' class='edit'>
					<br><br><br>
				</td>
			</tr>
		";
		echo "</form>";
		echo "</table>";
	}
}



echofooter();
?>