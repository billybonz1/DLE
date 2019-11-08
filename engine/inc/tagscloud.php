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
 Файл: tagscloud.php
-----------------------------------------------------
 Назначение: управление облаком тегов
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
  die("Hacking attempt!");
}

if( !$user_group[$member_id['user_group']]['admin_tagscloud'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

function compare_tags($a, $b) {
	
	if( $a['tag'] == $b['tag'] ) return 0;
	
	return strcasecmp( $a['tag'], $b['tag'] );

}

if ($_GET['action'] == "delete") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$_GET['name'] = convert_unicode( urldecode ( $_GET['name'] ), $config['charset']  );

	if( @preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $_GET['name'] ) ) $_GET['name'] = "";
	else $_GET['name'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_GET['name'] ) ) ), ENT_QUOTES ) );

	if (!$_GET['name']) { header( "Location: ?mod=tagscloud" ); die(); }

	$db->query ( "SELECT news_id FROM " . PREFIX . "_tags WHERE tag = '{$_GET['name']}'" );
			
	$tag_array = array ();
			
	while ( $row = $db->get_row () ) {
				
		$tag_array[] = $row['news_id'];
			
	}
	$db->free ();

	if (count ( $tag_array )) {
				
		$tag_array = "(" . implode ( ",", $tag_array ) . ")";

		$sql_result = $db->query( "SELECT id, tags FROM " . PREFIX . "_post WHERE id IN {$tag_array}" );

		while ( $row = $db->get_row( $sql_result ) ) {

			$row['tags'] = explode( ",", $row['tags'] );

			$tags = array ();
			
			foreach ( $row['tags'] as $value ) {
				
				$value = trim( $value );
				if ( $value == $_GET['name'] ) continue;
				$tags[] = $value;
			}

			$tags = array_unique($tags);

			if ( count($tags) ) $post_tags = implode( ", ", $tags ); else $post_tags = "";

			$db->query( "UPDATE " . PREFIX . "_post SET tags='{$post_tags}' WHERE id='{$row['id']}'" );

			$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$row['id']}'" );

			if ( count($tags) ) {

				$tagcloud = array ();
	
				foreach ( $tags as $value ) {
								
					$tagcloud[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
				}
	
				$tagcloud = implode( ", ", $tagcloud );
				$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tagcloud );
			}
		}

		$db->query( "DELETE FROM " . PREFIX . "_tags WHERE tag = '{$_GET['name']}'" );
	}

	clear_cache();
	header( "Location: ?mod=tagscloud" ); die();
}

if ($_GET['action'] == "edit") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$_GET['oldname'] = convert_unicode( urldecode ( $_GET['oldname'] ), $config['charset']  );
	$_GET['newname'] = convert_unicode( urldecode ( $_GET['newname'] ), $config['charset']  );

	if( @preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $_GET['oldname'] ) ) $_GET['oldname'] = "";
	else $_GET['oldname'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_GET['oldname'] ) ) ), ENT_QUOTES ) );

	if( @preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $_GET['newname'] ) ) $_GET['newname'] = "";
	else $_GET['newname'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_GET['newname'] ) ) ), ENT_QUOTES ) );

	$_GET['newname'] = str_replace (",", " ", $_GET['newname']);

	if (!$_GET['oldname'] OR !$_GET['newname']) { header( "Location: ?mod=tagscloud" ); die(); }

	$db->query ( "SELECT news_id FROM " . PREFIX . "_tags WHERE tag = '{$_GET['oldname']}'" );
			
	$tag_array = array ();
			
	while ( $row = $db->get_row () ) {
				
		$tag_array[] = $row['news_id'];
			
	}
	$db->free ();

	if (count ( $tag_array )) {
				
		$tag_array = "(" . implode ( ",", $tag_array ) . ")";

		$sql_result = $db->query( "SELECT id, tags FROM " . PREFIX . "_post WHERE id IN {$tag_array}" );

		while ( $row = $db->get_row( $sql_result ) ) {

			$row['tags'] = explode( ",", $row['tags'] );

			$tags = array ();
			
			foreach ( $row['tags'] as $value ) {
				
				$value = trim( $value );
				if ( $value == $_GET['oldname'] ) $value = $_GET['newname'];
				$tags[] = $value;
			}

			if ( count($tags) ) { 

				$tags = array_unique($tags);
				$post_tags = implode( ", ", $tags );

			} else $post_tags = "";

			$db->query( "UPDATE " . PREFIX . "_post SET tags='{$post_tags}' WHERE id='{$row['id']}'" );

			$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$row['id']}'" );

			if ( count($tags) ) {

				$tagcloud = array ();
	
				foreach ( $tags as $value ) {
								
					$tagcloud[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
				}

				$tagcloud = implode( ", ", $tagcloud );
				$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tagcloud );

			}
		}
	}

	clear_cache();
	header( "Location: ?mod=tagscloud" ); die();

}

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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['opt_tagscloud']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
HTML;


$tags = array();
$list = array();


$db->query("SELECT tag, COUNT(*) AS count FROM " . PREFIX . "_tags GROUP BY tag");

while($row = $db->get_row()){

	$tags[$row['tag']] = $row['count'];

}
$db->free();

if ( count($tags) ) {

	foreach ($tags as $tag => $value) {
	
		$list[$tag]['tag']   = $tag;
		$list[$tag]['count']  = $value;
	
	}
	usort ($list, "compare_tags");

	$i = 0;
	$entries = "";

	foreach ($list as $value) {

		if (trim($value['tag']) != "" ) {

		$i ++;

		if( $config['allow_alt_url'] == "yes" ) $link = "<a href=\"" . $config['http_home_url'] . "tags/" . urlencode( $value['tag'] ) . "/\" target=\"_blank\">" . $lang['comm_view'] . "</a>";
		else $link = "<a href=\"{$config['http_home_url']}index.php?do=tags&amp;tag=" . urlencode( $value['tag'] ) . "\" target=\"_blank\">" . $lang['comm_view'] . "</a>";

		$entries .= "<tr>
        <td style=\"padding:4px;\" nowrap><div id=\"content_{$i}\">{$value['tag']}</div></td>
        <td align=left><b>{$value['count']}</b></td>
        <td align=center>[&nbsp;{$link}&nbsp;]&nbsp;&nbsp;[&nbsp;<a uid=\"{$i}\" class=\"editlink\" href=\"?mod=tagscloud\">{$lang['word_ledit']}</a>&nbsp;]&nbsp;&nbsp;[&nbsp;<a uid=\"{$i}\" class=\"dellink\" href=\"?mod=tagscloud\">{$lang['word_ldel']}</a>&nbsp;]</td>
        </tr>
        <tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=3></td></tr>";

		}

	}

echo <<<HTML
<table width="100%" id="tagslist">
	<tr class="thead">
    <th width="350" style="padding:2px;">{$lang['tagscloud_name']}</th>
    <th>{$lang['tagscloud_count']}</th>
    <th width="100" align="center"><div style="text-align: center;">&nbsp;{$lang['user_action']}&nbsp;</div></th>
	</tr>
	<tr class="tfoot"><th colspan="3"><div class="hr_line"></div></td></th>
	{$entries}
	<tr class="tfoot"><th colspan="3"><div class="hr_line"></div></td></th>
</table>
<script type="text/javascript">
$(function(){

	$("#tagslist").delegate("tr", "hover", function(){
	  $(this).toggleClass("hoverRow");
	});

		var tag_name = '';

		$('.dellink').click(function(){

			tag_name = $('#content_'+$(this).attr('uid')).text();

		    DLEconfirm( '{$lang['tagscloud_del']} <b>«'+tag_name+'»</b> {$lang['tagscloud_del_1']}', '{$lang['p_confirm']}', function () {

				document.location='?mod=tagscloud&user_hash={$dle_login_hash}&action=delete&name=' + encodeURIComponent(tag_name) + '';

			} );

			return false;
		});


		$('.editlink').click(function(){

			tag_name = $('#content_'+$(this).attr('uid')).text();

			DLEprompt('{$lang['tagscloud_edit_1']}', tag_name, '{$lang['tagscloud_edit']}', function (r) {
				if (tag_name != r) {	
					document.location='?mod=tagscloud&user_hash={$dle_login_hash}&action=edit&oldname=' + encodeURIComponent(tag_name) + '&newname=' + encodeURIComponent(r);
				}		
			});

			return false;
		});

});
</script>
HTML;


}  else {

echo <<<HTML
<table width="100%">
    <tr>
        <td style="padding:2px;height:50px;"><div align="center">{$lang['tagscloud_not_found']}<br /><br> <a class="main" href="javascript:history.go(-1)">{$lang['func_msg']}</a></div></td>
    </tr>
</table>
HTML;

}

echo <<<HTML
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
HTML;


echofooter();
?>