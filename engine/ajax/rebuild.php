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
 Назначение: перестроение новостей
=====================================================
*/

@session_start();
@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );

define('DATALIFEENGINE', true);
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -12 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

include ENGINE_DIR.'/data/config.php';

if ($config['http_home_url'] == "") {

	$config['http_home_url'] = explode("engine/ajax/rebuild.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require_once ENGINE_DIR.'/inc/include/functions.inc.php';

require_once ENGINE_DIR.'/modules/sitelogin.php';

if(($member_id['user_group'] != 1)) {die ("error");}

if ($_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash) {

	  die ("error");

}

require_once ROOT_DIR.'/language/'.$config['langs'].'/adminpanel.lng';
include_once ENGINE_DIR . '/classes/parse.class.php';

$parse = new ParseFilter( Array (), Array (), 1, 1 );
$parse->edit_mode = false;
if ( $config['allow_admin_wysiwyg'] == "yes" ) $parse->allow_code = false;

$parsexf = new ParseFilter( Array (), Array (), 1, 1 );
$parsexf->edit_mode = false;
if ( $config['allow_admin_wysiwyg'] == "yes" ) $parsexf->allow_code = false;

if( $config['safe_xfield'] ) {
	$parsexf->ParseFilter();
	$parsexf->safe_mode = true;
	$parsexf->edit_mode = false;
}

$startfrom = intval($_POST['startfrom']);
$buffer = "";
$step = 0;

$result = $db->query("SELECT id, short_story, full_story, xfields, allow_br FROM " . PREFIX . "_post LIMIT ".$startfrom.", 50");

while($row = $db->get_row($result))
{

	if( $row['allow_br'] != '1' OR $config['allow_admin_wysiwyg'] == "yes" ) {
		$row['short_story'] = $parse->decodeBBCodes( $row['short_story'], true, $config['allow_admin_wysiwyg'] );
		$row['full_story'] = $parse->decodeBBCodes( $row['full_story'], true, $config['allow_admin_wysiwyg'] );
	} else {
		$row['short_story'] = $parse->decodeBBCodes( $row['short_story'], false );
		$row['full_story'] = $parse->decodeBBCodes( $row['full_story'], false );
	}

	$short_story = $parse->process( $row['short_story'] );
	$full_story = $parse->process( $row['full_story'] );

	if( $config['allow_admin_wysiwyg'] == "yes" OR $row['allow_br'] != '1' ) {
		
		$full_story = $db->safesql( $parse->BB_Parse( $full_story ) );
		$short_story = $db->safesql( $parse->BB_Parse( $short_story ) );
	
	} else {
		
		$full_story = $db->safesql( $parse->BB_Parse( $full_story, false ) );
		$short_story = $db->safesql( $parse->BB_Parse( $short_story, false ) );
	
	}

	if ($row['xfields'] != "") {

		$xfields = xfieldsload();
		$postedxfields = xfieldsdataload($row['xfields']);
		$filecontents = array ();

		if( ! empty( $postedxfields ) ) {

			foreach ($xfields as $name => $value) {
			
				if ($value[3] == "textarea" AND $postedxfields[$value[0]] != "" ) {
			
					if( $config['allow_admin_wysiwyg'] == "yes" or $row['allow_br'] != '1' ) {
						$postedxfields[$value[0]] = $parsexf->decodeBBCodes($postedxfields[$value[0]], true, "yes");					
						$postedxfields[$value[0]] = $db->safesql($parsexf->BB_Parse($parsexf->process($postedxfields[$value[0]])));
							
					} else {
						$postedxfields[$value[0]] = $parsexf->decodeBBCodes($postedxfields[$value[0]], false);
						$postedxfields[$value[0]] = $db->safesql($parsexf->BB_Parse($parsexf->process($postedxfields[$value[0]]), false));
							
					}
			
				} elseif ( $postedxfields[$value[0]] != "" ) {
			
					$postedxfields[$value[0]] = $db->safesql($parsexf->process(stripslashes($postedxfields[$value[0]])));
			
				}
			
			}

			foreach ( $postedxfields as $xfielddataname => $xfielddatavalue ) {
				if( $xfielddatavalue == "" ) {
					continue;
				}
				
				$xfielddataname = $db->safesql( $xfielddataname );

				$xfielddatavalue = str_replace( "|", "&#124;", $xfielddatavalue );
				$filecontents[] = "$xfielddataname|$xfielddatavalue";
			}
			
			$filecontents = implode( "||", $filecontents );
		
		} else	$filecontents = '';

	} else	$filecontents = '';

	$db->query( "UPDATE " . PREFIX . "_post SET short_story='$short_story', full_story='$full_story', xfields='$filecontents' WHERE id='{$row['id']}'" );

	$step++;
}

clear_cache();
$rebuildcount = $startfrom + $step;
$buffer = "{\"status\": \"ok\",\"rebuildcount\": {$rebuildcount}}";


@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer;
?>