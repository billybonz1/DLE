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
 Файл: profile.php
-----------------------------------------------------
 Назначение: Профиль пользователя
=====================================================
*/
@session_start();
@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );

define( 'DATALIFEENGINE', true );
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -12 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

include ENGINE_DIR . '/data/config.php';

if( $config['http_home_url'] == "" ) {
	
	$config['http_home_url'] = explode( "engine/ajax/profile.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';
require_once ENGINE_DIR . '/classes/templates.class.php';

$_REQUEST['skin'] = trim(totranslit($_REQUEST['skin'], false, false));

if( $_REQUEST['skin'] == "" OR !@is_dir( ROOT_DIR . '/templates/' . $_REQUEST['skin'] ) ) {
	die( "Hacking attempt!" );
}

//################# Определение групп пользователей
$user_group = get_vars( "usergroup" );

if( ! $user_group ) {
	$user_group = array ();
	
	$db->query( "SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC" );
	
	while ( $row = $db->get_row() ) {
		
		$user_group[$row['id']] = array ();
		
		foreach ( $row as $key => $value ) {
			$user_group[$row['id']][$key] = stripslashes($value);
		}
	
	}
	set_vars( "usergroup", $user_group );
	$db->free();
}

if( $config["lang_" . $_REQUEST['skin']] ) {

	if ( file_exists( ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/website.lng' ) ) {	
		@include_once (ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/website.lng');
	} else die("Language file not found");

} else {
	
	@include_once ROOT_DIR . '/language/' . $config['langs'] . '/website.lng';

}
$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

$tpl = new dle_template( );
$tpl->dir = ROOT_DIR . '/templates/' . $_REQUEST['skin'];
define( 'TEMPLATE_DIR', $tpl->dir );
$PHP_SELF = $config['http_home_url'] . "index.php";

if (isset ( $_GET['name'] )) $name = @$db->safesql ( strip_tags ( urldecode ( $_GET['name'] ) ) ); else $name = '';

if (!$name ) die("Hacking attempt!");

if( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $name ) ) die("Not allowed user name!");

$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE name = '{$name}'" );

@header( "Content-type: text/css; charset=" . $config['charset'] );

if (!$row['user_id']) {

echo "<div id='dleprofilepopup' title='{$lang['all_err_1']}' style='display:none'><br />{$lang['news_err_26']}</div>";

} else {

$tpl->load_template( 'profile_popup.tpl' );

if( $row['foto'] and (file_exists( ROOT_DIR . "/uploads/fotos/" . $row['foto'] )) ) $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
else $tpl->set( '{foto}', "{THEME}/images/noavatar.png" );


$tpl->set( '{fullname}', stripslashes( $row['fullname'] ) );

if( $row['banned'] == 'yes' ) $user_group[$row['user_group']]['group_name'] = $lang['user_ban'];

$tpl->set( '{status}',  $user_group[$row['user_group']]['group_prefix'].$user_group[$row['user_group']]['group_name'].$user_group[$row['user_group']]['group_suffix'] );
$tpl->set( '{registration}', langdate( "j F Y H:i", $row['reg_date'] ) );
$tpl->set( '{lastdate}', langdate( "j F Y H:i", $row['lastdate'] ) );
$tpl->set( '{comm_num}', $row['comm_num'] );
$tpl->set( '{news_num}', $row['news_num'] );
$tpl->set( '{icq}', stripslashes( $row['icq'] ) );
$tpl->set( '{land}', stripslashes( $row['land'] ) );
$tpl->set( '{info}', stripslashes( $row['info'] ) );
$tpl->set( '{rate}', userrating( $row['name'] ) );

if( $row['signature'] and $user_group[$row['user_group']]['allow_signature'] ) {
		
	$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "\\1" );
	$tpl->set( '{signature}', stripslashes( $row['signature'] ) );
	
} else {
		
	$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "" );
	
}

if( $user_group[$row['user_group']]['icon'] ) $tpl->set( '{group-icon}', "<img src=\"" . $user_group[$row['user_group']]['icon'] . "\" border=\"0\" />" );
else $tpl->set( '{group-icon}', "" );

if( $row['news_num'] ) {
		
	if( $config['allow_alt_url'] == "yes" ) {
			
		$tpl->set( '{news}', "<a href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/news/" . "\">" . $lang['all_user_news'] . "</a>" );
		
	} else {
			
		$tpl->set( '{news}', "<a href=\"" . $PHP_SELF . "?subaction=allnews&amp;user=" . urlencode( $row['name'] ) . "\">" . $lang['all_user_news'] . "</a>" );

	}
} else {
		
	$tpl->set( '{news}', $lang['all_user_news'] );
	
}

if( $row['comm_num'] ) {
		
	$tpl->set( '{comments}', "<a href=\"$PHP_SELF?do=lastcomments&amp;userid=" . $row['user_id'] . "\">" . $lang['last_comm'] . "</a>" );
	
} else {
		
		$tpl->set( '{comments}', $lang['last_comm'] );
	
}

$tpl->compile( 'content' );

$tpl->result['content'] = str_replace( '{THEME}', $config['http_home_url'] . 'templates/' . $_REQUEST['skin'], $tpl->result['content'] );

echo "<div id='dleprofilepopup' title='{$lang['p_user']} {$row['name']}' style='display:none'>{$tpl->result['content']}</div>";

}
?>