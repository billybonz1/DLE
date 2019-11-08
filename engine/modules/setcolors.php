<?php
$getDir = $db->get_array($getDirResult);

if($getDir['type'] == "1") {

$num = $getDir['stolb']*$getDir['stroki'];
if($num == 0) {$num = 402;}
$page = $_GET['page'];
$result = $db->query("SELECT * FROM " . PREFIX . "_colors WHERE name LIKE '{$getDir['translit']}_x_%'");
$posts = $db->num_rows($result); 

$total = intval(($posts - 1) / $num) + 1; 
$page = intval($page); 
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total; 
$start = $page * $num - $num; 


if($total != 0 && $total != 1) {
	$uri = $_SERVER['REQUEST_URI'];
	$uri = explode("&page", $uri);
	$uri = $uri[0];
	$mouseover = " onMouseOver=\"this.style.color='#000'\" onMouseOut=\"this.style.color='#333'\"";
	// Проверяем нужны ли стрелки назад
if ($page != 1) $pervpage = "<a href= ." . $uri . "&page=1 style='color:#333333;' $mouseover>Первая</a>
                               <a href= ." . $uri . "&page=". ($page - 1) ." style='color:#333333;' $mouseover>Предыдущая</a> ";
// Проверяем нужны ли стрелки вперед
if ($page != $total) $nextpage = " <a href= ." . $uri . "&page=". ($page + 1) ." style='color:#333333;' $mouseover>Следующая</a>
                                   <a href= ." . $uri . "&page=" .$total. " style='color:#333333;' $mouseover>Последняя</a>"; 

// Находим две ближайшие станицы с обоих краев, если они есть
if($page - 2 > 0) $page2left = " <a href= ." . $uri . "&page=". ($page - 2) ." style='color:#333333;' $mouseover>". ($page - 2) ."</a> | ";
if($page - 1 > 0) $page1left = "<a href= ." . $uri . "&page=". ($page - 1) ." style='color:#333333;' $mouseover>". ($page - 1) ."</a> | ";
if($page + 2 <= $total) $page2right = " | <a href= ." . $uri . "&page=". ($page + 2) ." style='color:#333333;' $mouseover>". ($page + 2) ."</a>";
if($page + 1 <= $total) $page1right = " | <a href= ." . $uri . "&page=". ($page + 1) ." style='color:#333333;' $mouseover>". ($page + 1) ."</a>";


$select = "
<input type='hidden' name='do' value='{$_REQUEST['do']}'>
<input type='hidden' name='category' value='{$_REQUEST['category']}'>
<input type='hidden' name='num' value='{$_REQUEST['num']}'>
<select name='page' style='margin-left:25px;' onchange=\"javascript:document.getElementById('thisform').submit();\">";
	$selected = "";
	for($z=1; $z<=$total; $z++) {
		
		if($z==$_REQUEST['page']) {
			$selected = "selected";
		}
		$select .= "<option value='{$z}' {$selected}>{$z}</option>";
		$selected = "";
	}
$select .= "</select>
</form>";

$select2 = "
<input type='hidden' name='do' value='{$_REQUEST['do']}'>
<input type='hidden' name='category' value='{$_REQUEST['category']}'>
<input type='hidden' name='num' value='{$_REQUEST['num']}'>
<select name='page' style='margin-left:25px;' onchange=\"javascript:document.getElementById('thisform2').submit();\">";
	$selected2 = "";
	for($z=1; $z<=$total; $z++) {
		
		if($z==$_REQUEST['page']) {
			$selected2 = "selected";
		}
		
		$select2 .= "<option value='{$z}' {$selected2}>{$z}</option>";
		$selected2 = "";
	}
$select2 .= "</select>
</form>";

}


$getPics = $db->query("SELECT * FROM " . PREFIX . "_colors WHERE name LIKE '{$getDir['translit']}_x_%' ORDER BY id ASC  LIMIT {$start}, {$num}");
$pics = $db->get_array($getPics);

$nativeNumber = "";

$resultCheck = $db->query("SELECT * FROM " . PREFIX . "_colors_allow WHERE user_id='{$_SESSION['dle_user_id']}'");

$colorPhoto = "";
$n = 0;
if($db->num_rows($getPics) != 0) {
	if($total != 0 && $total != 1) {
				// Вывод меню
$colorPhoto .= "<div id='navig'><form action='" . $_SERVER['PHP_SELF'] . "' id='thisform'>" . $pervpage.$page2left.$page1left."<b>".$page."</b>".$page1right.$page2right.$nextpage . "{$select}</div>"; 
	}
	$colorPhoto .= "<table bordercolor='silver' border='1' align='center' style='margin-top:15px;'>";
		do {
		$padding = 0;
		if($n == 0) {$colorPhoto .= "<tr>";}
			$thumbSize = explode("x", $pics['thumbSize']);
			$bigSize = explode("x", $pics['bigSize']);
			if($db->num_rows($resultCheck) != 0) {
				$padding = strlen($pics['nativeNumber'])*10;
				$nativeNumber = "<p style='font-weight:bold; color:#dc5716; padding:0px; margin:0px;'>" . $pics['nativeNumber'] . "</p>";
			}
			
			$colorPhoto .= "
				<td valign='top'>
					<div style=''\">
					<a href='uploads/color/{$pics['name']}' class='highslide' onclick='return hs.expand(this);'>
						<div style='background-image:url(uploads/color/{$pics['name']}); width:{$thumbSize[1]}px; height:{$thumbSize[0]}px; margin:0 auto; margin-top:3px;' alt='Highslide JS' class='thisdiv' title='Увеличить' bigwidth='{$bigSize[0]}'  bigheight='{$bigSize[1]}'>{$nativeNumber}</div>
							</a>
					<p style='width:{$thumbSize[1]}; text-align:center; padding:2px; margin:0px; font-weight:bold;'>{$pics['ourNumber']}</p>
					<p style='width:{$thumbSize[1]}; text-align:center; padding:2px; margin:0px;'>{$pics['opic']}</p>
					<div class='highslide-caption' style='clear:both; float:left; width:{$bigSize[0]}px;'>
						<div style='clear:both; float:right; margin-top:-50px; width:{$padding}px; margin-right:10px;'><p style='text-align:right; padding:0px; margin:0px;'>{$nativeNumber}</p></div>
						<div style='clear:both; float:left; width:70%;'>{$pics['comments']}</div>
						<div float:left; width:30%;><p style='text-align:right; padding:0px; margin:0px; padding-right:10px;'>{$pics['ourNumber']}</p></div>
					</div>
					</div>
				</td>
			";
		$n++;
		if($n == $getDir['stolb']) {$colorPhoto .= "</tr>"; $n = 0;}
		} while($pics = $db->get_array($getPics));
	$colorPhoto .= "</table>";
	if($total != 0 && $total != 1) {
				// Вывод меню
$colorPhoto .= "<div id='navig'><form action='" . $_SERVER['PHP_SELF'] . "' id='thisform2'>" . $pervpage.$page2left.$page1left."<b>".$page."</b>".$page1right.$page2right.$nextpage . "{$select2}</div>"; 
	}
}

$setcolors = "
<div style='clear:both; float:left; background-color:#e9f0f6;'>
	<div style='clear:both; float:left; background-color:#fff; margin:15px;' id='white'>
        <div class='a_block_22' id='poltitle'>
        	<div style='padding-top:7px;'>
            <p class='ntitle'>
                <img src='{THEME}/images/dlet_artblock_22_01.gif'>{$getDir['name']}
                
            </p>
            </div>
        </div>
        <div style='clear:both; margin:10px;'>
			{$getDir['text']}
			{$colorPhoto}
		</div>
	</div>
</div>
";

} elseif($getDir['type'] == 2) {


?>

<script type="text/javascript" src="https://laki-kraski.com.ua/engine/classes/highslide/highslide3.js"></script>
<link rel="stylesheet" type="text/css" href="https://laki-kraski.com.ua/engine/classes/highslide/highslide.css" />

<script type="text/javascript">
	hs.graphicsDir = 'https://laki-kraski.com.ua/engine/classes/highslide/graphics/';
	hs.wrapperClassName = 'wide-border';
</script>

<?php

$num = $getDir['stolb']*$getDir['stroki'];
if($num == 0) {$num = 402;}

$page = $_GET['page'];
$result = $db->query("SELECT * FROM " . PREFIX . "_color_p WHERE translit='{$getDir['translit']}'");
$posts = $db->num_rows($result); 

$total = intval(($posts - 1) / $num) + 1; 
$page = intval($page); 
if(empty($page) or $page < 0) $page = 1;
if($page > $total) $page = $total; 
$start = $page * $num - $num; 


if($total != 0 && $total != 1) {
	$uri = $_SERVER['REQUEST_URI'];
	$uri = explode("&page", $uri);
	$uri = $uri[0];
	$mouseover = " onMouseOver=\"this.style.color='#000'\" onMouseOut=\"this.style.color='#333'\"";
	// Проверяем нужны ли стрелки назад
if ($page != 1) $pervpage = "<a href= ." . $uri . "&page=1 style='color:#333333;' $mouseover>Первая</a>
                               <a href= ." . $uri . "&page=". ($page - 1) ." style='color:#333333;' $mouseover>Предыдущая</a> ";
// Проверяем нужны ли стрелки вперед
if ($page != $total) $nextpage = " <a href= ." . $uri . "&page=". ($page + 1) ." style='color:#333333;' $mouseover>Следующая</a>
                                   <a href= ." . $uri . "&page=" .$total. " style='color:#333333;' $mouseover>Последняя</a>";

// Находим две ближайшие станицы с обоих краев, если они есть
if($page - 2 > 0) $page2left = " <a href= ." . $uri . "&page=". ($page - 2) ." style='color:#333333;' $mouseover>". ($page - 2) ."</a> | ";
if($page - 1 > 0) $page1left = "<a href= ." . $uri . "&page=". ($page - 1) ." style='color:#333333;' $mouseover>". ($page - 1) ."</a> | ";
if($page + 2 <= $total) $page2right = " | <a href= ." . $uri . "&page=". ($page + 2) ." style='color:#333333;' $mouseover>". ($page + 2) ."</a>";
if($page + 1 <= $total) $page1right = " | <a href= ." . $uri . "&page=". ($page + 1) ." style='color:#333333;' $mouseover>". ($page + 1) ."</a>";

$select = "
<input type='hidden' name='do' value='{$_REQUEST['do']}'>
<input type='hidden' name='category' value='{$_REQUEST['category']}'>
<input type='hidden' name='num' value='{$_REQUEST['num']}'>
<select name='page' style='margin-left:25px;' onchange=\"javascript:document.getElementById('thisform').submit();\">";
	$selected = "";
	for($z=1; $z<=$total; $z++) {
		
		if($z==$_REQUEST['page']) {
			$selected = "selected";
		}
		
		$select .= "<option value='{$z}' {$selected}>{$z}</option>";
		$selected = "";
	}
$select .= "</select>
</form>";

$select2 = "
<input type='hidden' name='do' value='{$_REQUEST['do']}'>
<input type='hidden' name='num' value='{$_REQUEST['num']}'>
<input type='hidden' name='category' value='{$_REQUEST['category']}'>
<select name='page' style='margin-left:25px;' onchange=\"javascript:document.getElementById('thisform2').submit();\">";
	$selected2 = "";
	for($z=1; $z<=$total; $z++) {
		
		if($z==$_REQUEST['page']) {
			$selected2 = "selected";
		}
		
		$select2 .= "<option value='{$z}' {$selected2}>{$z}</option>";
		$selected2 = "";
	}
$select2 .= "</select>
</form>";

}


$getPics = $db->query("SELECT * FROM " . PREFIX . "_color_p WHERE translit='{$getDir['translit']}' ORDER BY id ASC  LIMIT {$start}, {$num}");
$pics = $db->get_array($getPics);

$nativeNumber = "";


$colorPhoto = "";
$n = 0;
if($db->num_rows($getPics) != 0) {
	if($total != 0 && $total != 1) {
				// Вывод меню
$colorPhoto .= "<div id='navig'><form action='" . $_SERVER['PHP_SELF'] . "' id='thisform'>" . $pervpage.$page2left.$page1left."<b>".$page."</b>".$page1right.$page2right.$nextpage . "{$select}</div>"; 
	}
	
	
	
	
	$size = $db->super_query("SELECT * FROM " . PREFIX . "_color_p_size WHERE translit='{$getDir['translit']}'");
	
	$colorPhoto .= "<table bordercolor='silver' border='1' align='center' style='margin-top:15px;'>";
	$n = 0;
		do {
		
		$padding = 0;
		if($n == 0) {$colorPhoto .= "<tr>";}
			$colorPhoto .= "
				<td valign='top'>
				<a href='uploads/hsbb.jpg' class='highslide' onclick='return hs.expand(this);'>
					<div style='background-color:#{$pics['color']}; width:{$size['width']}; height:{$size['height']};  margin:0 auto; margin-top:3px;' alt='Highslide JS' class='thisdiv' title='Увеличить' bigwidth='380'  bigheight='380'>{$nativeNumber}</div>
				</a>
				
				<p style='text-align:center; padding:2px; margin:0px; font-weight:bold;'>{$pics['name']}</p>
				<div style='width:135px; margin:5px;'>{$pics['text']}</div>
				<div class='highslide-caption' style='clear:both; float:left; position:relative; top:0px; width:380px; '>
					<div style='clear:both; float:left; width:280px;'>{$pics['text']}<br></div>
					<div style='float:left; width:100px;'><p style='text-align:right; margin-top:0px;'>{$pics['name']}</p></div>
					<div class='highslide-image' style=' margin-top:-400px; margin-left:-10px; background-color:{$pics['color']}; width:380px; height:380px;'></div>
				</div>
				
				</td>
			";
		$n++;
		if($n == $getDir['stolb']) {$colorPhoto .= "</tr>"; $n = 0;}
		} while($pics = $db->get_array($getPics));
	$colorPhoto .= "</table>";
	if($total != 0 && $total != 1) {
				// Вывод меню
$colorPhoto .= "<div id='navig'><form action='" . $_SERVER['PHP_SELF'] . "' id='thisform2'>" . $pervpage.$page2left.$page1left."<b>".$page."</b>".$page1right.$page2right.$nextpage . "{$select2}</div>"; 
	}
}






$setcolors = "
<div style='clear:both; float:left; background-color:#e9f0f6;'>
	<div style='clear:both; float:left; background-color:#fff; margin:15px;' id='white'>
        <div class='a_block_22' id='poltitle'>
        	<div style='padding-top:7px;'>
            <p class='ntitle'>
                <img src='{THEME}/images/dlet_artblock_22_01.gif'>{$getDir['name']}
                
            </p>
            </div>
        </div>
        <div style='clear:both; margin:10px;'>
			{$getDir['text']}
			{$colorPhoto}
		</div>
	</div>
</div>
";
}
?>
