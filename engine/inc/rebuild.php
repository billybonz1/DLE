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
 Файл: rebuild.php
-----------------------------------------------------
 Назначение: Перестроение новостей
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied']); }

$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post" );

echoheader("", "");

echo <<<HTML
<form action="" method="post">
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['opt_srebuild']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" colspan="2">{$lang['rebuild_info']}</td>
    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td style="padding:2px;height:50px;"><div id="progressbar"></div>{$lang['stat_allnews']}&nbsp;{$row['count']},&nbsp;{$lang['rebuild_count']}&nbsp;<font color="red"><span id="newscount">0</span></font>&nbsp;<span id="progress"></span></td>
    </tr>

	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:2px;" colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td style="padding:2px;"><input type="submit" id="button" class="buttons" value="{$lang['rebuild_start']}" style="width:190px;"><input type="hidden" id="rebuild_ok" name="rebuild_ok" value="0"></td>
    </tr>
</table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div></form>
<script language="javascript" type="text/javascript">

  var total = {$row['count']};

	$(function() {

		$("#progress").ajaxError(function(event, request, settings){
		   $(this).html('{$lang['nl_error']}');
			$('#button').attr("disabled", false);
		 });

		$( "#progressbar" ).progressbar({
			value: 0
		});

		$('#button').click(function() {
			$('#progress').html('{$lang['rebuild_status']}');
			$('#button').attr("disabled", "disabled");
			$('#button').val("{$lang['rebuild_forw']}");
			senden( $('#rebuild_ok').val() );
			return false;
		});

	});

function senden( startfrom ){

	$.post("engine/ajax/rebuild.php?user_hash={$dle_login_hash}", { startfrom: startfrom },
		function(data){

			if (data) {

				if (data.status == "ok") {

					$('#newscount').html(data.rebuildcount);
					$('#rebuild_ok').val(data.rebuildcount);

					var proc = Math.round( (100 * data.rebuildcount) / total );

					if ( proc > 100 ) proc = 100;

					$('#progressbar').progressbar( "option", "value", proc );

			         if (data.rebuildcount >= total) 
			         {
			              $('#progress').html('{$lang['rebuild_status_ok']}');
			         }
			         else 
			         { 
			              setTimeout("senden(" + data.rebuildcount + ")", 5000 );
			         }


				}

			}
		}, "json");

	return false;
}
</script>
HTML;


echofooter();
?>