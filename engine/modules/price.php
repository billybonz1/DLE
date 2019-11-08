<?php
$countPrice = 0;
$ff = new db;
$resultPrice = $ff->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}'");
$myrowPrice = $ff->get_array($resultPrice);
$countPrice = $ff->num_rows($resultPrice);
if($countPrice != 0){
$price = "<div style='clear:both; float;left; margin-right:130px; margin-top:-27px;'  id='showlogin'>
				
				
				<div style='float:left;'>
					<p id='nazvkol1'><span>упаковка</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'></p>
					<div id='tag1'><p>{$myrowPrice['pack']}</p></div>
				</div>
				<div style='float:left;'>
					<p id='nazvkol2'><span style='padding-right:3px;'>база и цвет</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
					<div id='tag4'><p>{$myrowPrice['cvet']}</p></div>
				</div>
				<div style='float:left;'>
					<p id='nazvkol'><span style='padding-right:3px;'>степень блеска</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
					<div id='tag5'><p>{$myrowPrice['blesk']}</p></div>
				</div>
				<div style='float:left;'>
					<p id='nazvkol4'><span style='padding-right:3px;'>стоимость упаковки</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
					<div id='tag2'><p>{$myrowPrice['price_yp']}</p></div>
				</div>
				<div style='float:left;'>
					<p id='nazvkol' style='text-align:left; margin-left:5px;'> стоимость 1 м2 покрытия</p>
					<div id='tag3'><p>{$myrowPrice['price_litr']}</p></div>
				</div>
				
				<div style='float:left; margin-right:-12px; margin-left:10px;'>
					<a href='#'><img src='{THEME}/images/sravnit.jpg'></a>
					<br>
					<a href='#'><img src='{THEME}/images/buy.jpg'></a>
				</div>

		  </div>{$row[id]}";
} else {
		$price = "<div style='clear:both; float;left; margin-right:130px; margin-top:-27px;'  id='showlogin'>
				
				
				<div style='float:left;'>
					<p id='nazvkol1'><span>упаковка</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'></p>
					<div id='tag1'><p>1.5 Л</p></div>
				</div>
				<div style='float:left;'>
					<p id='nazvkol2'><span style='padding-right:3px;'>база и цвет</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
					<div id='tag4'><p>........</p></div>
				</div>
				<div style='float:left;'>
					<p id='nazvkol'><span style='padding-right:3px;'>степень блеска</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
					<div id='tag5'><p>........</p></div>
				</div>
				<div style='float:left;'>
					<p id='nazvkol4'><span style='padding-right:3px;'>стоимость упаковки</span><img src='{THEME}/images/linev.jpg' style='margin-left:3px;'> </p>
					<div id='tag2'><p>84.00 грн</p></div>
				</div>
				<div style='float:left;'>
					<p id='nazvkol' style='text-align:left; margin-left:5px;'> стоимость 1 м2 покрытия</p>
					<div id='tag3'><p>11.67 грн</p></div>
				</div>
				
				<div style='float:left; margin-right:-12px; margin-left:10px;'>
					<a href='#'><img src='{THEME}/images/sravnit.jpg'></a>
					<br>
					<a href='#'><img src='{THEME}/images/buy.jpg'></a>
				</div>

		  </div>";
}
?>