<?PHP
/*
=====================================================
 Datalife Engine Nulled 
-----------------------------------------------------
 http://dle.org.ua/
-----------------------------------------------------
 Copyright (c) 2004,2009 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: files.php
-----------------------------------------------------
 Назначение: управление загруженными картинками
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if($member_id['user_group'] == 7) {
	$member_id['user_group'] = 1;
}

if( ! $user_group[$member_id['user_group']]['allow_image_upload'] or ($member_id['user_group'] != 1 and $action != "quick") ) {
	msg( "error", "Доступ запрещен", "Вы не можете выполнять операции с файлами" );
}

$allowed_extensions = array ("gif", "jpg", "png", "jpe", "jpeg" );
$allowed_video = array ("avi", "mp4", "wmv", "mpg", "flv", "mp3", "swf", "m4v", "m4a", "mov", "3gp", "f4v" );
$allowed_files = explode( ',', strtolower( $config['files_type'] ) );
$img_result_th = "";
$img_result = "";

$all_ext = "*." . implode( ";*.", $allowed_extensions );

if( $config['files_allow'] == "yes" and $user_group[$member_id['user_group']]['allow_file_upload'] ) $all_ext .= ";*." . implode( ";*.", $allowed_files );

if (@ini_get( 'safe_mode' ) == 1)
	define( 'FOLDER_PREFIX', "" );
else
	define( 'FOLDER_PREFIX', date( "Y-m" ) );

if( $_REQUEST['userdir'] ) $userdir = totranslit( $_REQUEST['userdir'] ) . DIRECTORY_SEPARATOR; else $userdir = "";
if( $_REQUEST['sub_dir'] ) $sub_dir = totranslit( $_REQUEST['sub_dir'] ) . DIRECTORY_SEPARATOR; else $sub_dir = "";

if( isset( $_REQUEST['area'] ) ) $area = totranslit( $_REQUEST['area'] ); else $area = "";
if( isset( $_REQUEST['wysiwyg'] ) ) $wysiwyg = totranslit( $_REQUEST['wysiwyg'] ); else $wysiwyg = 0;
if( isset( $_REQUEST['author'] ) ) $author = @$db->safesql( strip_tags( urldecode( $_REQUEST['author'] ) ) ); else $author = "";
if( intval( $_REQUEST['news_id'] ) ) $news_id = intval( $_REQUEST['news_id'] ); else $news_id = 0;

$config_path_image_upload = ROOT_DIR . "/uploads/" . $userdir . $sub_dir;


if( $member_id['user_group'] < 4 ) {
	
	$config['max_image'] = $_POST['t_size'] ? $_POST['t_size'] : $config['max_image'];

} else {
	
	$_POST['t_seite'] = 0;

}

$thumb_size = $config['max_image'];
$thumb_size = explode ("x", $thumb_size);

if ( count($thumb_size) == 2) {

	$thumb_size = intval($thumb_size[0]) . "x" . intval($thumb_size[1]);

} else {

	$thumb_size = intval( $thumb_size[0] );

}

$config['max_image'] = $thumb_size;

if( ! @is_dir( $config_path_image_upload ) ) msg( "error", $lang['addnews_denied'], "Directory {$userdir} not found" );

if( $action == "doimagedelete" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( ! isset( $_POST['images'] ) ) {
		msg( "info", $lang['images_delerr'], $lang['images_delerr_1'], "$PHP_SELF?mod=files" );
	}
	
	foreach ( $_POST['images'] as $image ) {
		@unlink( $config_path_image_upload . $image );
		@unlink( $config_path_image_upload . "thumbs/" . $image );
	}
	$action = "";
}


if(isset($_REQUEST['add_dir'])) {
	$name = $_REQUEST['mk_dir'];
	$folder = totranslit($name);
	@mkdir( ROOT_DIR . "/uploads/" . $folder, 0777 );

	@chmod( ROOT_DIR . "/uploads/" . $folder, 0777 );
	@mkdir( ROOT_DIR . "/uploads/" . $folder . "/thumbs", 0777 );

	@chmod( ROOT_DIR . "/uploads/" . $folder . "/thumbs", 0777 );
	$db->query("INSERT INTO " . PREFIX . "_directories (translit,name,type,text,stolb,catId,stroki) VALUES('{$folder}', '{$name}','0','','','','')");
}

if(isset($_REQUEST['addKol'])) {
	if($_REQUEST['cat_name'] != "") {
		$name = $_REQUEST['cat_name'];
		$folder = totranslit($name);
		$db->query("INSERT INTO " . PREFIX . "_directories (translit,name,type,text,catId,stroki) VALUES('{$folder}', '{$name}','1','','0','0')");
		
	}
}

if(isset($_REQUEST['delKol'])) {
	$db->query("DELETE FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['colorsDel']}'");
	
	$dir = opendir(ROOT_DIR . "/uploads/color/");
	while(false !== ($file = readdir($dir))) {
		if ($file != "." && $file != "..") {
			$explode = explode("_x_", $file);
			if($explode[0] == $_REQUEST['colorsDel']) {
				unlink(ROOT_DIR . "/uploads/color/" . $file);
			}
		}
	}
	closedir($dir);
}

if(isset($_REQUEST['del_dir'])) {
    
	if($_REQUEST['dirrrr'] != "") {
		
		function removeDirRec($dirrrr){
			if ($objs = glob("uploads/" . $dirrrr. "/thumbs/*")) {
				foreach($objs as $obj) {
					is_dir($obj) ? removeDirRec($obj) : unlink($obj);
				}
			}
			
			if ($objs = glob("uploads/" . $dirrrr."/*")) {
				foreach($objs as $obj) {
					is_dir($obj) ? removeDirRec($obj) : unlink($obj);
				}
			}

			
		}
		removeDirRec($_REQUEST['dirrrr']);
		rmdir("uploads/" . $dirrrr . "/thumbs/");
		rmdir("uploads/" . $dirrrr);
		$db->query("DELETE FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST[dirrrr]}' AND type='0'");
	} else {
		?>
			<script>
				alert("Выберите директорию для удаления.");
			</script>
		<?php
	}

}

// ********************************************************************************
// Вывод списка загруженных файлов
// ********************************************************************************


if( $action == "quick" ) {
	
	header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
	header( "Cache-Control: no-store, no-cache, must-revalidate" );
	header( "Cache-Control: post-check=0, pre-check=0", false );
	header( "Pragma: no-cache" );
	
	$sess_id = session_id();
	
	if( $user_group[$member_id['user_group']]['allow_file_upload'] ) {
		
		if( ! $config['max_file_size'] ) $max_file_size = 0;
		elseif( $config['max_file_size'] > $config['max_up_size'] ) $max_file_size = ( int ) $config['max_file_size'];
		else $max_file_size = ( int ) $config['max_up_size'];
		
		if( $max_file_size ) $max_file_size = $max_file_size . " KB";
	
	} else {
		
		$max_file_size = $config['max_up_size'] . " KB";
	
	}
	
	$config['max_file_count'] = intval( $config['max_file_count'] );
	
	echo <<<HTML
<html><head>
<base target="_self">
<BASE href="{$config['http_home_url']}">
<meta content="text/html; charset={$config['charset']}" http-equiv="content-type" />
<title>Upload</title>
<style type="text/css">
html,body{
height:100%;
margin:0px;
padding: 0px;
background: #F4F3EE;
}

form {
margin:0px;
padding: 0px;
}
input,
select,
textarea {
	outline:none;
}
table{
border:0px;
border-collapse:collapse;
}

table td{
padding:0px;
font-size: 11px;
font-family: verdana;
}

a:active,
a:visited,
a:link {
	color: #4b719e;
	text-decoration:none;
	}

a:hover {
	color: #4b719e;
	text-decoration: underline;
	}

.navigation {
	color: #999898;
	font-size: 11px;
	font-family: tahoma;
}
.unterline {
	background: url({$config['http_home_url']}engine/skins/images/line_bg.gif);
	width: 100%;
	height: 9px;
	font-size: 3px;
	font-family: tahoma;
	margin-bottom: 4px;
}
.hr_line {
	background: url({$config['http_home_url']}engine/skins/images/line.gif);
	width: 100%;
	height: 7px;
	font-size: 3px;
	font-family: tahoma;
	margin-top: 4px;
	margin-bottom: 4px;
}

.edit {
	border:1px solid #9E9E9E;
	color: #000000;
	font-size: 11px;
	font-family: Verdana; BACKGROUND-COLOR: #ffffff 
}

.upload input {
	border:1px solid #9E9E9E;
	color: #000000;
	font-size: 11px;
	margin-top: 5px;
	font-family: Verdana; BACKGROUND-COLOR: #ffffff 
}
.buttons {
	background: #FFF;
	border: 1px solid #9E9E9E;
	color: #666666;
	font-family: Verdana, Tahoma, helvetica, sans-serif;
	padding: 0px;
	vertical-align: absmiddle;
	font-size: 11px; 
	height: 21px;
}
select {
	color: #000000;
	font-size: 11px;
	font-family: Verdana; 
	background-color: #ffffff;
	border: 1px solid #9E9E9E; 
}

	.dle_tabPane{
		height:26px;	/* Height of tabs */
	}
	.dle_aTab{
		border:1px solid #CDCDCD;
		padding:5px;		
		
	}
	.dle_tabPane DIV{
		float:left;
		padding-left:3px;
		vertical-align:middle;
		background-repeat:no-repeat;
		background-position:bottom left;
		cursor:pointer;
		position:relative;
		bottom:-1px;

		margin-left:0px;
		margin-right:0px;
	}
	.dle_tabPane .tabActive{
		background-image:url('{$config['http_home_url']}engine/skins/images/tl_active.gif');
		margin-left:0px;
		margin-right:0px;	
	}
	.dle_tabPane .tabInactive{
		background-image:url('{$config['http_home_url']}engine/skins/images/tl_inactive.gif');
		margin-left:0px;
		margin-right:0px;
	}

	.dle_tabPane .inactiveTabOver{
		margin-left:0px;
		margin-right:0px;
	}
	.dle_tabPane span{
		font-family:tahoma;
		vertical-align:top;
		font-size:11px;
		line-height:26px;
		float:left;
	}
	.dle_tabPane .tabActive span{
		padding-bottom:0px;
		line-height:26px;
	}
	
	.dle_tabPane img{
		float:left;
	}

fieldset { 
	position: relative; 
	border:1px solid #CDCDCD;
	margin: 0;
	padding: 20px 10px;
	margin-bottom: 1em;
}

legend { 
	position:absolute; 
	top: -1px; 
	left: .5em; 
}

fieldset.flash {
	width: 90%;
	margin: 0;
	border-color: #CDCDCD;
}
.progressWrapper {
	width: 99%;
	overflow: hidden;
}

.progressContainer {
	margin: 5px;
	padding: 4px;
	border: solid 1px #E8E8E8;
	background-color: #F7F7F7;
	overflow: hidden;
}
/* Message */
.message {
	margin: 1em 0;
	padding: 10px 20px;
	border: solid 1px #FFDD99;
	background-color: #FFFFCC;
	overflow: hidden;
}
/* Error */
.red {
	border: solid 1px #B50000;
	background-color: #FFEBEB;
}

/* Current */
.green {
	border: solid 1px #DDF0DD;
	background-color: #EBFFEB;
}

/* Complete */
.blue {
	border: solid 1px #CEE2F2;
	background-color: #F0F5FF;
}

.progressName {
	font-size: 8pt;
	font-weight: 700;
	color: #555;
	width: 323px;
	height: 14px;
	text-align: left;
	white-space: nowrap;
	overflow: hidden;
}

.progressBarInProgress,
.progressBarComplete,
.progressBarError {
	font-size: 0;
	width: 0%;
	height: 2px;
	background-color: blue;
	margin-top: 2px;
}

.progressBarComplete {
	width: 100%;
	background-color: green;
	visibility: hidden;
}

.progressBarError {
	width: 100%;
	background-color: red;
	visibility: hidden;
}

.progressBarStatus {
	margin-top: 2px;
	width: 99%;
	font-size: 7pt;
	font-family: Arial;
	text-align: left;
	white-space: nowrap;
}

a.progressCancel {
	font-size: 0;
	display: block;
	height: 14px;
	width: 14px;
	background-image: url({$config['http_home_url']}engine/classes/swfupload/cancelbutton.gif);
	background-repeat: no-repeat;
	background-position: -14px 0px;
	float: right;
}

a.progressCancel:hover {
	background-position: 0px 0px;
}


</style>
<script type="text/javascript" src="{$config['http_home_url']}engine/classes/swfupload/swfupload.js"></script>
<script type="text/javascript" src="{$config['http_home_url']}engine/classes/swfupload/swfupload.queue.js"></script>
<script type="text/javascript" src="{$config['http_home_url']}engine/classes/swfupload/fileprogress.js"></script>
<script type="text/javascript" src="{$config['http_home_url']}engine/classes/swfupload/handlers.js"></script>

<script type="text/javascript">
		var swfu;

		window.onload = function() {
			var settings = {
				flash_url : "{$config['http_home_url']}engine/classes/swfupload/swfupload.swf",
				upload_url: "{$config['http_home_url']}engine/ajax/upload.php",	// Relative to the SWF file
				post_params: {"PHPSESSID" : "{$sess_id}", "news_id" : "{$news_id}", "area" : "{$area}", "author" : "{$author}"},
				file_size_limit : "{$max_file_size}",
				file_types : "{$all_ext}",
				file_types_description : "All Files",
				file_upload_limit : {$config['max_file_count']},
				file_queue_limit : {$config['max_file_count']},
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,
				flash_container_id : "flash_container",
				// The event handler functions are defined in handlers.js
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete	// Queue plugin event
			};

			swfu = new SWFUpload(settings);
	     };
	</script>
	
	

	
        </head>
<body>
<script language="javascript" type="text/javascript" src="{$config['http_home_url']}engine/skins/default.js"></script>
<script type="text/javascript" src="{$config['http_home_url']}engine/skins/tabs.js"></script>
<table align="center" width="97%">
    <tr>
        <td width="4" height="16"><img src="{$config['http_home_url']}engine/skins/images/tb_left.gif" width="4" height="16" border="0" /></td>
		<td background="{$config['http_home_url']}engine/skins/images/tb_top.gif"><img src="{$config['http_home_url']}engine/skins/images/tb_top.gif" width="1" height="16" border="0" /></td>
		<td width="4"><img src="{$config['http_home_url']}engine/skins/images/tb_right.gif" width="3" height="16" border="0" /></td>
    </tr>
	<tr>
        <td width="4" background="{$config['http_home_url']}engine/skins/images/tb_lt.gif"><img src="{$config['http_home_url']}engine/skins/images/tb_lt.gif" width="4" height="1" border="0" /></td>
		<td valign="top" style="padding:8px;" bgcolor="#FFFFFF">
HTML;
	
	echo <<<JSCRIPT
<script language='javascript' type="text/javascript">
<!--

var allow_focus = true;

function ckeck_uncheck_all() {
    var frm = document.delimages;
    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='checkbox') {
            if(frm.master_box.checked == true){ elmnt.checked=false; }
            else{ elmnt.checked=true; }
        }
    }
    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
    else{ frm.master_box.checked = true; }
}

function insert_all() {

    var frm = document.delimages;
    var wysiwyg = '{$wysiwyg}';

	allow_focus = false;

	if (wysiwyg == 'yes') {

		wysiwyg = '<br />';

	} else {

		wysiwyg = '\\n';

	}

    for (var i=0;i<frm.elements.length;i++) {
   
     var elmnt = frm.elements[i];
 
       if (elmnt.type=='checkbox') {

            if(elmnt.checked == true){ 

				if (elmnt.id == 'fullimage') {

					insertimage('{$config['http_home_url']}uploads/posts/' + elmnt.value);
					insertfile(wysiwyg);

				}

				if (elmnt.id == 'thumbimage') {

					insertthumb('{$config['http_home_url']}uploads/posts/' + elmnt.value);
					insertfile(wysiwyg);

				}

				if (elmnt.id == 'file') {

					insertfile('[attachment='+ elmnt.value + ']');
					insertfile(wysiwyg);

				}

				if (elmnt.id == 'fullstatic') {

					insertimage('{$config['http_home_url']}uploads/posts/' + elmnt.alt);
					insertfile(wysiwyg);

				}

				if (elmnt.id == 'thumbstatic') {

					insertthumb('{$config['http_home_url']}uploads/posts/' + elmnt.alt);
					insertfile(wysiwyg);

				}

			}
        }
    }

	window.focus();

}
-->
</script>
JSCRIPT;
	
	echo "<script language=\"javascript\" type=\"text/javascript\">
        <!--
        function insertimage(selectedImage) {

           imageAlign = document.forms['properties'].imageAlign.value;";
	
	if( ! $wysiwyg ) {
		echo "if (imageAlign == 'center') finalImage = \"[center][img]\"+ selectedImage +\"[/img][/center]\";
		else finalImage = \"[img=\"+ imageAlign +\"]\"+ selectedImage +\"[/img]\";
	     ";
	} else {
		echo "if (imageAlign == 'center') finalImage = \"<div style=\\\"text-align: center;\\\"><img src=\\\"\"+ selectedImage +\"\\\" border=0></div><div></div>\";
		else finalImage = \"<img align=\\\"\"+ imageAlign +\"\\\" src=\\\"\"+ selectedImage +\"\\\" border=0>\";";
	}
	
	if( ! $wysiwyg ) {
		echo "window.opener.doInsert(finalImage, '', false); if(allow_focus == true) { window.focus(); } ";
	} else {
		echo " window.opener.tinyMCE.execCommand('mceInsertContent',false,finalImage); if(allow_focus == true) { window.focus(); }";
	}
	
	echo "

                        }

				function ShowBild(sPicURL) {
				window.open('{$config['http_home_url']}engine/modules/imagepreview.php?image='+sPicURL, '', 'resizable=1,HEIGHT=200,WIDTH=200, scrollbars=yes');
                        }

        function insertthumb(selectedImage) {

           imageAlign = document.forms['properties'].imageAlign.value;";
	
	echo "if (imageAlign == 'center') finalImage = \"[center][thumb]\"+ selectedImage +\"[/thumb][/center]\";
		else finalImage = \"[thumb=\"+ imageAlign +\"]\"+ selectedImage +\"[/thumb]\";
	    ";
	
	if( ! $wysiwyg ) {
		echo "window.opener.doInsert(finalImage, '', false); if(allow_focus == true) { window.focus(); } ";
	} else {
		echo "	window.opener.tinyMCE.execCommand('mceInsertContent',false,finalImage); if(allow_focus == true) { window.focus(); }";
	}
	
	echo "

                        }

        function insertfile(selectedFile) {";
	
	if( ! $wysiwyg ) {
		echo "window.opener.doInsert(selectedFile, '', false); if(allow_focus == true) { window.focus(); } ";
	} else {
		echo "	window.opener.tinyMCE.execCommand('mceInsertContent',false,selectedFile); if(allow_focus == true) { window.focus(); }";
	}
	
	echo "

                        }

        //-->
        </script>";
} 

else {
	echoheader( "files", $lang['images_head'] );
	
	echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="{$config['http_home_url']}engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="{$config['http_home_url']}engine/skins/images/tl_oo.gif"><img src="{$config['http_home_url']}engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="{$config['http_home_url']}engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="{$config['http_home_url']}engine/skins/images/tl_lb.gif"><img src="{$config['http_home_url']}engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
HTML;
}

if( $_REQUEST['subaction'] == "deluploads" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images where author = '$author' AND news_id = '$news_id'" );
	
	$listimages = explode( "|||", $row['images'] );
	
	if( isset( $_POST['images'] ) ) foreach ( $_POST['images'] as $image ) {
		
		$i = 0;
		
		sort( $listimages );
		reset( $listimages );
		
		foreach ( $listimages as $dataimages ) {
			
			if( $dataimages == $image ) {
				
				$url_image = explode( "/", $image );
				
				if( count( $url_image ) == 2 ) {
					
					$folder_prefix = $url_image[0] . "/";
					$image = $url_image[1];
				
				} else {
					
					$folder_prefix = "";
					$image = $url_image[0];
				
				}
				
				unset( $listimages[$i] );
				@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $image );
				@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $image );
			
			}
			
			$i ++;
		}
	}
	
	if( count( $listimages ) ) $row['images'] = implode( "|||", $listimages );
	else $row['images'] = "";
	
	$db->query( "UPDATE " . PREFIX . "_images set images='$row[images]' where author = '$author' AND news_id = '$news_id'" );
	
	if( count( $_POST['static_files'] ) ) {
		
		foreach ( $_POST['static_files'] as $file ) {
			
			$file = intval( $file );
			
			$row = $db->super_query( "SELECT id, name, onserver FROM " . PREFIX . "_static_files WHERE author = '$author' AND static_id = '$news_id' AND id='$file'" );
			
			if( $row['id'] and $row['onserver'] ) {
				
				@unlink( ROOT_DIR . "/uploads/files/" . $row['onserver'] );
				$db->query( "DELETE FROM " . PREFIX . "_static_files WHERE id='{$row['id']}'" );
			
			} else {
				
				if( $row['id'] ) {
					$url_image = explode( "/", $row['name'] );
					
					if( count( $url_image ) == 2 ) {
						
						$folder_prefix = $url_image[0] . "/";
						$image = $url_image[1];
					
					} else {
						
						$folder_prefix = "";
						$image = $url_image[0];
					
					}
					
					@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $image );
					@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $image );
					$db->query( "DELETE FROM " . PREFIX . "_static_files WHERE id='{$row['id']}'" );
				
				}
			
			}
		}
	}
	
	if( count( $_POST['files'] ) ) {
		
		foreach ( $_POST['files'] as $file ) {
			
			$file = intval( $file );
			
			$row = $db->super_query( "SELECT id, onserver FROM " . PREFIX . "_files where author = '$author' AND news_id = '$news_id' AND id='$file'" );
			
			@unlink( ROOT_DIR . "/uploads/files/" . $row['onserver'] );
			$db->query( "DELETE FROM " . PREFIX . "_files WHERE id='{$row['id']}'" );
		
		}
	
	}

}

if( $_REQUEST['subaction'] == "upload" ) {
	
	if( $action == "quick" ) {
		
		$userdir = "posts/";
		
		if( ! is_dir( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX ) ) {
			
			@mkdir( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX, 0777 );
			@chmod( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX, 0777 );
			@mkdir( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX . "/thumbs", 0777 );
			@chmod( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX . "/thumbs", 0777 );
		}
		
		if( ! is_dir( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX ) ) {
			
			msg( "error", $lang['opt_error'], $lang['upload_error_0']." /uploads/posts/" . FOLDER_PREFIX . "/" );
		}

		if( ! is_writable( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX ) ) {
			
			msg( "error", $lang['opt_error'], $lang['upload_error_1']." /uploads/posts/" . FOLDER_PREFIX . "/ ".$lang['upload_error_2'] );
		}

		if( ! is_writable( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX . "/thumbs" ) ) {
			
			msg( "error", $lang['opt_error'], $lang['upload_error_1']." /uploads/posts/" . FOLDER_PREFIX . "/thumbs/ ".$lang['upload_error_2'] );
		}
		
		$config_path_image_upload = ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX . "/";
	
	}

	for($image_i = 1; $image_i < ($images_number + 2); $image_i ++) {
		echo "dsss";
		$file_prefix = time() + rand( 1, 100 );
		$file_prefix .= "_";
		
		$imageurl = trim( htmlspecialchars( strip_tags( $_POST['imageurl'] ) ) );
		
		$serverfile = trim( htmlspecialchars( strip_tags( $_POST['serverfile'] ) ) );
		
		if( $serverfile != '' and ! @file_exists( ROOT_DIR . "/uploads/files/" . $serverfile ) ) $serverfile = '';
		
		if( $imageurl != "" ) {
			
			$urlcopy = "yes";
			$imageurl = str_replace( "\\", "/", $imageurl );
			$image_name = explode( "/", $imageurl );
			$image_name = end( $image_name );
			
			$img_name_arr = explode( ".", $image_name );
			$image_size = @filesize_url( $imageurl );
			$type = totranslit( end( $img_name_arr ) );
			
			if( $image_name != "" ) {
				
				$curr_key = key( $img_name_arr );
				unset( $img_name_arr[$curr_key] );
				$image_name = totranslit( implode( ".", $img_name_arr ) ) . "." . $type;
			
			}
		
		} else {
			
			$urlcopy = "";
			$current_image = 'file_' . $image_i;
			$image = $_FILES[$current_image]['tmp_name'];
			$image_name = $_FILES[$current_image]['name'];
			$image_size = $_FILES[$current_image]['size'];
			$error_code = $_FILES[$current_image]['error'];

			if ($error_code !== UPLOAD_ERR_OK) {

			    switch ($error_code) { 
			        case UPLOAD_ERR_INI_SIZE: 
			            $error_code = 'PHP Error: The uploaded file exceeds the upload_max_filesize directive in php.ini'; break;
			        case UPLOAD_ERR_FORM_SIZE: 
			            $error_code = 'PHP Error: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'; break;
			        case UPLOAD_ERR_PARTIAL: 
			            $error_code = 'PHP Error: The uploaded file was only partially uploaded'; break;
			        case UPLOAD_ERR_NO_FILE: 
			            $error_code = 'PHP Error: No file was uploaded'; break;
			        case UPLOAD_ERR_NO_TMP_DIR: 
			            $error_code = 'PHP Error: Missing a PHP temporary folder'; break;
			        case UPLOAD_ERR_CANT_WRITE: 
			            $error_code = 'PHP Error: Failed to write file to disk'; break;
			        case UPLOAD_ERR_EXTENSION: 
			            $error_code = 'PHP Error: File upload stopped by extension'; break;
			        default: 
			            $error_code = 'Unknown upload error';  break;
			    } 


			}

			
			$img_name_arr = explode( ".", $image_name );
			$type = totranslit( end( $img_name_arr ) );
			
			if( $image_name != "" ) {
				
				$curr_key = key( $img_name_arr );
				unset( $img_name_arr[$curr_key] );
				
				$image_name = totranslit( implode( ".", $img_name_arr ) ) . "." . $type;
			}
		}
		
		if( $config['files_allow'] == "yes" and $user_group[$member_id['user_group']]['allow_file_upload'] and $_REQUEST['action'] == "quick" and (in_array( strtolower( $type ), $allowed_files ) or $serverfile != '') ) {
/*
=====================================================
 Загрузка файлов, но не картинок
=====================================================
*/
			if( $serverfile == '' ) {
				
				if( $urlcopy != "yes" ) @move_uploaded_file( $image, ROOT_DIR . "/uploads/files/" . $file_prefix . $image_name ) or $img_result = "<div><font color=red>{$lang['images_uperr_3']}<br /><br />{$error_code}</font></div>";
				else @copy( $imageurl, ROOT_DIR . "/uploads/files/" . $file_prefix . $image_name ) or $img_result = "<div><font color=red>$lang[images_uperr_3]</font></div>";
			
			} else {
				
				$file_prefix = '';
				$image_name = $serverfile;
			}
			
			if( @file_exists( ROOT_DIR . "/uploads/files/" . $file_prefix . $image_name ) ) {
				
				if( intval( $config['max_file_size'] ) and @filesize( ROOT_DIR . "/uploads/files/" . $file_prefix . $image_name ) > ($config['max_file_size'] * 1024) ) {
					
					@unlink( ROOT_DIR . "/uploads/files/" . $file_prefix . $image_name );
					$img_result .= "<div><font color=red>$image_name -> $lang[files_too_big]</font></div>";
				
				} else {
					
					@chmod( ROOT_DIR . "/uploads/files/" . $file_prefix . $image_name, 0666 );
					$img_result .= "<div><font color=green>$image_name -> $lang[files_upok]</font></div>";
					
					$added_time = time() + ($config['date_adjust'] * 60);
					
					if( $area == "template" ) {
						
						$db->query( "INSERT INTO " . PREFIX . "_static_files (static_id, author, date, name, onserver) values ('$news_id', '$author', '$added_time', '$image_name', '{$file_prefix}{$image_name}')" );
					
					} else {
						
						$db->query( "INSERT INTO " . PREFIX . "_files (news_id, name, onserver, author, date) values ('$news_id', '$image_name', '{$file_prefix}{$image_name}', '$author', '$added_time')" );
					
					}
				
				}
			
			}
		
		} elseif( $image_name == "" ) {
		
			if(!isset($_REQUEST['colors']) || $_REQUEST['colors'] == "") {
				$img_result .= "<div><font color=red>$current_image -> $lang[images_uperr]</font></div>";
			}
			
		} elseif( ! isset( $overwrite ) and file_exists( $config_path_image_upload . $image_name ) ) {
			
			$img_result .= "<div><font color=red>$current_image -> $lang[images_uperr_1]</font></div>";
		
		} elseif( ! (in_array( $type, $allowed_extensions ) or in_array( strtolower( $type ), $allowed_extensions )) ) {
			
			$img_result .= "<div><font color=red>$current_image -> $lang[images_uperr_2]</font></div>";
		
		} elseif( $image_size > ($config['max_up_size'] * 1024) and ! $config['max_up_side'] ) {
			
			$img_result .= "<div><font color=red>$current_image -> $lang[images_big]</font></div>";
		
		} else {
/*
=====================================================
 Загрузка картинок, но не файлов
=====================================================
*/

			if( $urlcopy != "yes" ) @move_uploaded_file( $image, $config_path_image_upload . $file_prefix . $image_name ) or $img_result = "<div><font color=red>{$lang['images_uperr_3']}<br /><br />{$error_code}</font></div>";
			else @copy( $imageurl, $config_path_image_upload . $file_prefix . $image_name ) or $img_result = "<div><font color=red>$lang[images_uperr_3]</font></div>";
			
			if( @file_exists( $config_path_image_upload . $file_prefix . $image_name ) ) {
				
				@chmod( $config_path_image_upload . $file_prefix . $image_name, 0666 );
				
				$img_result .= "<div><font color=green>$image_name -> $lang[images_upok]</font></div>";
				
				if( $action == "quick" and $area != "template" ) {
					
					$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_images where author = '$author' AND news_id = '$news_id'" );
					
					if( ! $row['count'] ) {
						
						$added_time = time() + ($config['date_adjust'] * 60);
						$inserts = FOLDER_PREFIX . "/" . $file_prefix . $image_name;
						$db->query( "INSERT INTO " . PREFIX . "_images (images, author, news_id, date) values ('$inserts', '$author', '$news_id', '$added_time')" );
					
					} else {
						
						$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images where author = '$author' AND news_id = '$news_id'" );
						
						if( $row['images'] == "" ) $listimages = array ();
						else $listimages = explode( "|||", $row['images'] );
						
						foreach ( $listimages as $dataimages ) {
							
							if( $dataimages == FOLDER_PREFIX . "/" . $file_prefix . $image_name ) $error_image = "stop";
						
						}
						
						if( $error_image != "stop" ) {
							
							$listimages[] = FOLDER_PREFIX . "/" . $file_prefix . $image_name;
							$row['images'] = implode( "|||", $listimages );
							
							$db->query( "UPDATE " . PREFIX . "_images set images='{$row['images']}' where author = '$author' AND news_id = '$news_id'" );
						
						}
					}
				}
				
				if( $area == "template" and $action == "quick" ) {
					
					$added_time = time() + ($config['date_adjust'] * 60);
					$inserts = FOLDER_PREFIX . "/" . $file_prefix . $image_name;
					$db->query( "INSERT INTO " . PREFIX . "_static_files (static_id, author, date, name) values ('$news_id', '$author', '$added_time', '$inserts')" );
				
				}
				
				include_once ENGINE_DIR . '/classes/thumb.class.php';
				if( $member_id['user_group'] > 3 ) {
					$_POST['make_thumb'] = true;
					$_POST['make_watermark'] = $config['allow_watermark'];
				}
				
				if( isset( $_POST['make_thumb'] ) ) {
					
					$thumb = new thumbnail( $config_path_image_upload . $file_prefix . $image_name );
					
					if( $thumb->size_auto( $config['max_image'], $_POST['t_seite'] ) ) {
						
						$thumb->jpeg_quality( $config['jpeg_quality'] );
						
						if( $config['allow_watermark'] == "yes" and $_POST['make_watermark'] == "yes" ) $thumb->insert_watermark( $config['max_watermark'] );
						
						$thumb->save( $config_path_image_upload . "thumbs/" . $file_prefix . $image_name );
					}
					
					if( @file_exists( $config_path_image_upload . "thumbs/" . $file_prefix . $image_name ) ) $img_result_th .= "<div><font color=blue>$image_name -> $lang[images_thok]</font></div>";
					
					@chmod( $config_path_image_upload . "thumbs/" . $file_prefix . $image_name, 0666 );
				}
				
			
				if( ($config['allow_watermark'] == "yes" and $_POST['make_watermark'] == "yes") or $config['max_up_side'] ) {
					$thumb = new thumbnail( $config_path_image_upload . $file_prefix . $image_name );
					$thumb->jpeg_quality( $config['jpeg_quality'] );
					
					if( $config['max_up_side'] ) $thumb->size_auto( $config['max_up_side'] );
					
					if( $config['allow_watermark'] == "yes" and $_POST['make_watermark'] == "yes" ) $thumb->insert_watermark( $config['max_watermark'] );
					
					$thumb->save( $config_path_image_upload . $file_prefix . $image_name );
				}
			
			} //if file is uploaded succesfully
			

			if( $urlcopy == "yes" or $serverfile != '' ) break;
		
		}
	}
}

echo "<script language=\"javascript\" type=\"text/javascript\">
	function ShowOrHideEx(id, show) {
        var item = null;
        if (document.getElementById) {
          item = document.getElementById(id);
        } else if (document.all) {
          item = document.all[id];
        } else if (document.layers){
          item = document.layers[id];
        }
        if (item && item.style) {
          item.style.display = show ? \"\" : \"none\";
        }
	}

	var total_allow_rows = {$config['max_file_count']};

	function AddImages() {
     var tbl = document.getElementById('tblSample');
     var lastRow = tbl.rows.length;

	 if (total_allow_rows &&  lastRow == total_allow_rows ) return;

     // if there's no header row in the table, then iteration = lastRow + 1
     var iteration = lastRow+1;
     var row = tbl.insertRow(lastRow);

     var cellRight = row.insertCell(0);
     var el = document.createElement('input');
     el.setAttribute('type', 'file');
     el.setAttribute('name', 'file_' + iteration);
     el.setAttribute('size', '41');
     el.setAttribute('value', iteration);
     cellRight.appendChild(el);

     document.getElementById('images_number').value = iteration;
	}

	function RemoveImages() {
     var tbl = document.getElementById('tblSample');
     var lastRow = tbl.rows.length;
     if (lastRow > 1){
              tbl.deleteRow(lastRow - 1);
               document.getElementById('images_number').value =  document.getElementById('images_number').value - 1;
     }
	}
    </script>";
	
	if( $action != "quick" ) {
	$colorScript = "
	<table style='margin-left:150px; margin-top:0px;'>
	<form action='{$_SERVER['PHP_SELF']}?mod=files' method='post'></form>
	<tr>
		<form action='{$_SERVER['PHP_SELF']}?mod=files' method='post'>
			<td align='right'><span>Добавить колеровку цветов: </span></td>
			<td>
			<input type='text' style='font-size:11px; height:18px; width:200px;' name='cat_name' value=''>
			<input type='submit' class='edit' name='addKol' value='Добавить'></td>
		</form>
	</tr>
	<tr>
		<form action='{$_SERVER['PHP_SELF']}?mod=files' method='post'>
			<td align='right'><span>Колеровка цветов: </span></td>
			<td style='padding-top:3px;'>
			<select name='colors'>
			<option value=''>---</option>
			";
				$selectColors = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE type='1'");
				if($db->num_rows($selectColors) != 0) {
					$myrowColors  = $db->get_array($selectColors);
					do {
						$colorScript .= "<option value='{$myrowColors['translit']}'>{$myrowColors['name']}</option>";
					} while($myrowColors  = $db->get_array($selectColors));
				}
			$colorScript .= "
			</select>
			
			<input type='submit' class='edit' name='changeColor' value='Перейти'></td>
		</form>
	</tr>
	
	<tr>
		<form action='{$_SERVER['PHP_SELF']}?mod=files' method='post'>
			<td align='right'><span>Удалить колеровку цветов: </span></td>
			<td style='padding-top:3px;'>
			<select name='colorsDel'>
			<option value=''>---</option>
			";
				$selectColors = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE type='1'");
				if($db->num_rows($selectColors) != 0) {
					$myrowColors  = $db->get_array($selectColors);
					do {
						$colorScript .= "<option value='{$myrowColors['translit']}'>{$myrowColors['name']}</option>";
					} while($myrowColors  = $db->get_array($selectColors));
				}
			$colorScript .= "
			</select>
			<input type='submit' class='edit' name='delKol' value='Удалить'>
			</td>
		</form>
	</tr>
	</table>
		";
	}
	if(isset($_REQUEST['colors']) && $_REQUEST['colors'] != "") {
		$name = "fileColors";
		$form = "uploading";
	} else {
		$name = "file_1";
		$form = "form";
	}
	
if(isset($_REQUEST['colors']) && $_REQUEST['colors'] != "" && !isset($_REQUEST['this'])){
	require_once (ROOT_DIR . '/engine/classes/thumb.class.php');
	if(isset($_REQUEST['addImg'])) {
		if (!empty($_FILES["fileColors"])){
			if ($_FILES['fileColors']['type'] == 'image/jpeg' || $_FILES['fileColors']['type'] == 'image/gif' || $_FILES['fileColors']['type'] == 'image/png'){
				$resultId = $db->query("SELECT * FROM " . PREFIX . "_colors ORDER BY id DESC");
				if($db->num_rows($resultId) != 0) {
					$myrowId = $db->get_array($resultId);
					$thisIdColor = $myrowId['id']+1;
				} else {
					$thisIdColor = 1;
				}
				
				$ourNumber    = $_REQUEST['ourNumber'];
				$nativeNumber = $_REQUEST['nativeNumber'];
				$comment 	  = $_REQUEST['comment'];
				$opic		  = $_REQUEST['opic'];
				$thubmFirst   = $_REQUEST['thumbSizeFirst'];
				$thumbSecond  = $_REQUEST['thumbSizeSecond'];
				$bigFirst     = $_REQUEST['bigSizeFirst'];
				$bigSecond    = $_REQUEST['bigSizeSecond'];
				$thumbSize    = $thubmFirst . "x" . $thumbSecond;
				$bigSize      = $bigFirst . "x" . $bigSecond;
				//$picExp = explode(".", $_FILES["fileColors"]["name"]);
//				$picImpl = $picExp[0] . ".jpg";
				$currName 	  = $_REQUEST['colors'] . "_x_" . $thisIdColor . "_x_" . $_FILES["fileColors"]["name"];
				move_uploaded_file($_FILES["fileColors"]["tmp_name"], ROOT_DIR . "/uploads/color/" . $currName);
				if($config['allow_watermark'] == "yes" and $_POST['make_watermark'] == "yes") {
					$thumb = new thumbnail(ROOT_DIR . "/uploads/color/" . $currName);
					$thumb->jpeg_quality( $config['jpeg_quality'] );
					
					if( $config['max_up_side'] ) $thumb->size_auto( $config['max_up_side'] );
					
					if( $config['allow_watermark'] == "yes" and $_POST['make_watermark'] == "yes" ) $thumb->insert_watermark( $config['max_watermark'] );
					
					$thumb->save( ROOT_DIR . "/uploads/color/" . $currName );
					
				}
				$check = $db->query("INSERT INTO " . PREFIX . "_colors (id,name,ourNumber,nativeNumber,comments,commentsAct,opic,opicAct,thumbSize,bigSize) VALUES (NULL,'$currName','$ourNumber ','$nativeNumber','$comment','1','$opic','1','$thumbSize','$bigSize')");
				if($check) {$reporting = "Файл успешно добавлен.";}
			} else {
				
			}
		} else {
			$reporting = "Укажите картинку для загрузки!!!";
		}
		
	}
	
	if(isset($_REQUEST['addOpis'])) {
		$err = 0;
		$cats = array();
		if($_REQUEST['wheretoshow'] != "") {
		$selectCats = $db->query("SELECT * fROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['colors']}'");
		$myrowCats  = $db->get_array($selectCats);
		$explCats = explode(",", $myrowCats['catId']);
			foreach($_REQUEST['wheretoshow'] as $value) {
				if(!in_array($value, $explCats)) {
					$resultDubl = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE catId LIKE '%{$value}%'");
					if($db->num_rows($resultDubl) != 0) {
						$sel = $db->query("SELECT * FROM " . PREFIX . "_category WHERE id='{$value}'");
						$myr = $db->get_array($sel);
						$report .= "Категория <b>{$myr['name']}</b> уже используется, выберите другую.<br />";
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
		$db->query("UPDATE " . PREFIX . "_directories SET text='{$_REQUEST['colorOpis']}',stolb='{$_REQUEST['stolb']}',catId='{$implode}', stroki='{$_REQUEST['stroki']}' WHERE translit='{$_REQUEST['colors']}'");
		}
		
		if($_REQUEST['wheretoshow'] != "") {
			$nameRus = $db->super_query("SELECT * FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['colors']}'");
		}
	}
	$colorText = $db->super_query("SELECT text,stolb,catId,stroki FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['colors']}'");
	$catId = $db->super_query("SELECT * FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['colors']}'");
	$explodeCatId = explode(",", $catId['catId']);
	$option = CategoryNewsSelection($explodeCatId,0);
	echo "<form action='{$_SERVER['PHP_SELF']}?mod=files' method='post'>";
	echo "<p style='color:red; width:100%;'>{$report}</p>";
	echo "<div class=\"navigation\">Выберите директорию для отоброжения страницы:</div>";
	echo "<select name='wheretoshow[]' multiple size='7'>";
		
	
		echo $option;
		
	echo "</select>";
	echo "<div class=\"navigation\">Количество столбцов в фотогалерее: <input type='text' name='stolb' value='{$colorText['stolb']}' style='padding:0px; border:1px #ccc solid; width:25px;'></div>";
	echo "<div class=\"navigation\">Количество строк на странице: <input type='text' name='stroki' value='{$colorText['stroki']}' style='padding:0px; border:1px #ccc solid; width:25px;'></div>";
	echo "<div class=\"navigation\">Описание раздела:</div><br>";
	include (ENGINE_DIR . '/editor/color.php');
	echo "<input type='hidden' name='colors' value='{$_REQUEST['colors']}'>";
	echo "<input type='submit' class='edit' name='addOpis' value='Отправить' style='margin-top:3px; margin-bottom:3px;'>";
	echo "</form>";
}

echo <<<HTML
<form action='{$_SERVER['PHP_SELF']}?mod=files' method='post' enctype="multipart/form-data" name="{$form}" id="form">
<input type="hidden" name="subaction" value="upload">
<input type="hidden" name="area" value='{$area}'>
<input type="hidden" name="action" value='{$action}'>
<input type="hidden" name="images_number" id="images_number" value="1">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" width='200' style="padding-left:10px;"><div class="navigation">Загрузка файлов на сервер</div></td>
    </tr>
</table>
<div class="unterline"></div>
HTML;

if( $action == "quick" ) {
	
	echo <<<HTML
<div id="dle_tabView1">

<div class="dle_aTab" style="display:none;">
HTML;

}
$remoreUpload = "";
if(!isset($_REQUEST['colors']) || $_REQUEST['colors'] == ""){
	$remoreUpload = "<div class=\"hr_line\"></div>
<div>{$lang['images_upurl']}&nbsp;<input class=\"edit\" type=\"text\" name=\"imageurl\" size=42></div>
<div class=\"hr_line\"></div>";
if(isset($_REQUEST['userdir'])) {
	echo "<input type='hidden' name='userdir' value='{$_REQUEST['userdir']}'>";
}
$br = "<br>";
$tds = "";
$right = "";
}
if($_REQUEST['colors'] != "") {
$tds = "</td><td>";
$br = "";
$right = " align='right'";

if(isset($_REQUEST['currRed'])) {
	$thumbSize = $_REQUEST['thumbSizeFirst'] . "x" . $_REQUEST['thumbSizeSecond'];
	$bigSize   = $_REQUEST['bigSizeFirst'] . "x" . $_REQUEST['bigSizeSecond'];
	$ourNumber = $_REQUEST['ourNumber'];
	$nativeNumber = $_REQUEST['nativeNumber'];
	$comment = $_REQUEST['comment'];
	$opic = $_REQUEST['opic'];
	
	if($_FILES["fileColors"]["name"] != "") {
		$pic = $db->super_query("SELECT id,name FROM " . PREFIX . "_colors WHERE id='{$_REQUEST['colorid']}'");
		unlink(ROOT_DIR . "/uploads/color/" . $pic['name']);
		$currName = $_REQUEST['colors'] . "_x_" . $pic['id'] . "_x_" . $_FILES["fileColors"]["name"];
		move_uploaded_file($_FILES["fileColors"]["tmp_name"], ROOT_DIR . "/uploads/color/" . $currName);
		
		$updPic = "name='{$currName}',";
	} else {$updPic = "";}
	
	if($_REQUEST['coma'] == "on") {$coma = "commentsAct='1',";} else {$coma = "commentsAct='0',";}
	if($_REQUEST['coma2'] == "on") {$coma2 = "opicAct='1',";} else {$coma2 = "opicAct='0',";}
	
	
	$db->query("UPDATE " . PREFIX . "_colors SET {$updPic} {$coma} {$coma2} ourNumber='$ourNumber', nativeNumber='$nativeNumber', comments='$comment', opic='$opic', thumbSize='$thumbSize', bigSize='$bigSize' WHERE id='{$_REQUEST['colorid']}'");
}

if($_REQUEST['this'] == "red") {

	$rowc = $db->super_query("SELECT * FROM " . PREFIX . "_colors WHERE id='{$_REQUEST['colorid']}'");
	$thumbSize = explode("x", $rowc['thumbSize']);
	$bigSize = explode("x", $rowc['bigSize']);
	$thumbSizeFirst = $thumbSize[0];
	$thumbSizeSecond = $thumbSize[1];
	$bigSizeFirst = $bigSize[0];
	$bigSizeSecond = $bigSize[1];
	$ourNumber = $rowc['ourNumber'];
	$nativeNumber = $rowc['nativeNumber'];
	$comment = $rowc['comments'];
	$opic = $rowc['opic'];
	if($rowc['commentsAct'] == 1) {
		$checkComment = "<input type='checkbox' name='coma' checked>";
	} else {
		$checkComment = "<input type='checkbox' name='coma'>";
	}
	
	if($rowc['opicAct'] == 1) {
		$checkOpic = "<input type='checkbox' name='coma2' checked>";
	} else {
		$checkOpic = "<input type='checkbox' name='coma2'>";
	}
	
	$hidRed = "<input type='hidden' name='currRed' value='{$_REQUEST['colorid']}'><input type='hidden' name='this' value='red'><input type='hidden' name='colorid' value='{$_REQUEST['colorid']}'>";
} else {
    $thumbSizeFirst = "100";
	$thumbSizeSecond = "100";
	$bigSizeFirst = "380";
	$bigSizeSecond = "380";
	$hidRed = "";
	$ourNumber = "";
	$nativeNumber = "";
	$comment = "";
	$opic = "";
	$checkComment = "";
	$checkOpic = "";
}

	$endingTable = "
		 <tr id=\"row\">
		  <td align='right'>Размер картинки:</td>
		  <td>
		  	<spna style='padding-left:5px;'>Ширина</span> - <input type=\"text\" name=\"thumbSizeFirst\" style='margin-top:2px; width:53px;' value='{$thumbSizeFirst}' onFocus=\"this.value=''\">(px) x 
			<spna>Высота</span> - <input type=\"text\" name=\"thumbSizeSecond\" style='margin-top:2px; width:53px;'  value='{$thumbSizeSecond}' onFocus=\"this.value=''\">(px)
		  </td>
		 </tr>
		 <tr id=\"row\">
		  <td align='right'>Размер при открытии:</td>
		  <td>
		  	<spna style='padding-left:5px;'>Ширина</span> - <input type=\"text\" name=\"bigSizeFirst\" style='margin-top:2px; width:53px;' value='{$bigSizeFirst}' onFocus=\"this.value=''\">(px) x 
			<spna>Высота</span> - <input type=\"text\" name=\"bigSizeSecond\" style='margin-top:2px; width:53px;'  value='{$bigSizeSecond}' onFocus=\"this.value=''\">(px)
		  </td>
		 </tr> 
		 <tr id=\"row\">
		  <td align='right'>Наш номер цвета:</td>
		  <td><input type=\"text\" name=\"ourNumber\" value='{$ourNumber}' style='margin-top:2px; width:290px;'></td>
		 </tr>
		 <tr id=\"row\">
		  <td align='right'>Родной номер цвета:</td>
		  <td><input type=\"text\" name=\"nativeNumber\" value='{$nativeNumber}' style='margin-top:2px; width:290px;'></td>
		 </tr>
		 <tr id=\"row\">
		  <td align='right'>Коментарии:</td>
		  <td><input type=\"text\" name=\"comment\" value='{$comment}' style='margin-top:2px; width:290px;'>{$checkComment}</td>
		 </tr>
		 <tr id=\"row\">
		  <td align='right' valign='top'>Описание:</td>
		  <td valign='top'>
			  <table>
				  <tr>
					  <td>
					  	<textarea name=\"opic\" rows='5' style='margin-top:2px; width:290px;'>{$opic}</textarea>
					  </td>
					  <td>
					  	{$checkOpic}
					  </td>
				  </tr>
			  </table>
		  </td>
		 </tr>
		 {$hidRed}
	";
}

echo <<<HTML

<!-- TinyMCE js/tiny_mce/tiny_mce.js-->
<script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		formats : {
			alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
			aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
			alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
			alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},
			bold : {inline : 'span', 'classes' : 'bold'},
			italic : {inline : 'span', 'classes' : 'italic'},
			underline : {inline : 'span', 'classes' : 'underline', exact : true},
			strikethrough : {inline : 'del'}
		},

	});
</script>
<!-- /TinyMCE -->


<table id="tblSample" class="upload">
 <tr id="row">
  <td colspan='2' align='center'><p style='color:red; width:416px;'>{$reporting}</p></td>
 </tr>
 <tr id="row">
  <td{$right}>Путь к изображению: {$br}{$tds}<input type="file" size="41" name="{$name}"></td><td><p style='margin-top:5px; margin-left:450px;'><a href='/admin.php?mod=pcat'>Загрузка цветов c других сайтов</a></p></td>
 </tr>
	{$endingTable}
</table>
{$remoreUpload}
HTML;

if( $member_id['user_group'] == 1 && (!isset($_REQUEST['colors']) || $_REQUEST['colors'] == "")) {
	
	echo <<<HTML
<div><b>uploads/files/</b>&nbsp;&nbsp;&nbsp;<input class="edit" type="text" name="serverfile" size=42></div>
<div class="hr_line"></div>
HTML;

}

if(!isset($_REQUEST['colors']) || $_REQUEST['colors'] == ""){
	$addUpl = "
		<input type=button class=buttons value=' - ' style=\"width:30px;\" title='{$lang['images_rem_tl']}' onClick=\"RemoveImages();return false;\">
	<input type=button class=buttons value=' + ' style=\"width:30px;\" title='{$lang['images_add_tl']}' onClick=\"AddImages();return false;\"> &nbsp;
	&nbsp;&nbsp;
	";
}
$subName  = "";
$openDiv  = "";
$closeDiv = "";
if($_REQUEST['colors'] != "") {
	$addUpl   = "<input type='hidden' name='colors' value='{$_REQUEST['colors']}'>";
	$subName  = "name=\"addImg\"";
	$openDiv  = "<div style='width:416px; text-align:center;'>";
	$closeDiv = "</div>";
}
echo <<<HTML
<div>
{$addUpl}
{$openDiv}<input type="submit" {$subName} class="buttons" value="  {$lang['db_load_a']}  ">{$closeDiv}
</div>
HTML;
if( $action == "quick" ) {
	
	if( $user_group[$member_id['user_group']]['allow_file_upload'] ) {
		
		if( $config['max_file_size'] ) {
			
			$lang['files_max_info'] = $lang['files_max_info'] . " " . formatsize( $config['max_file_size'] * 1024 );
		
		} else {
			
			$lang['files_max_info'] = $lang['files_max_info_2'];
		
		}
		
		$lang['files_max_info_1'] = $lang['files_max_info'] . "<br />" . $lang['files_max_info_1'] . " " . formatsize( $config['max_up_size'] * 1024 );
	
	} else {
		
		$lang['files_max_info_1'] = $lang['files_max_info_1'] . " " . formatsize( $config['max_up_size'] * 1024 );
	
	}
	
	echo <<<HTML
</div>

<div class="dle_aTab" style="display:none;">
<br />
<fieldset class="flash" id="fsUploadProgress">
			<legend>{$lang['upload_queue']}</legend>
			</fieldset>
		<div id="divStatus">{$lang['upload_mass_info']}<br /><br />{$lang['files_max_info_1']}</div>
<br />
<div style="position: relative">
					<input id="btnBrowse" type="button" value="{$lang['upload_waehlen']}" style="width:130px;" class="edit" />&nbsp;&nbsp;<input id="btnCancel" type="button" value="  {$lang['upload_cancel']}  " onclick="swfu.cancelQueue();" disabled="disabled" class="edit" />
				  <div id="flash_container" style="width:130px; height: 20px;position:absolute;top:0;left:0px;">Flash Container</div>
				</div>
</div>
<script type="text/javascript">
initTabs('dle_tabView1',Array('{$lang['upload_standart']}', '{$lang['upload_mass']}'),0, '100%');
</script>
HTML;

}

if( $member_id['user_group'] < 4 ) {
	
	$_POST['t_seite'] = intval( $_POST['t_seite'] );
	$t_seite_selected[$_POST['t_seite']] = "selected";
	
    if(!isset($_REQUEST['colors']) || $_REQUEST['colors'] == "") {
    	$ymUp = "
        	<div class=\"hr_line\"></div>
<div>{$lang['upload_t_size']}&nbsp;<input class=\"edit\" type=\"text\" name=\"t_size\" id=\"t_size\" size=9 value=\"{$config['max_image']}\">&nbsp;px&nbsp;<select name=\"t_seite\" id=\"t_seite\"><option value=\"0\" {$t_seite_selected[0]}>{$lang['upload_t_seite_1']}</option><option value=\"1\" {$t_seite_selected[1]}>{$lang['upload_t_seite_2']}</option><option value=\"2\" {$t_seite_selected[2]}>{$lang['upload_t_seite_3']}</option></select></div>
<div class=\"hr_line\"></div>
        ";
    }
    
	echo <<<HTML
{$ymUp}
<table>
<tr>
<td>
HTML;
}

if( $action != "quick" ) echo "<input type=checkbox name=overwrite value=1 id=ex> <label for=ex>$lang[images_aren]</label><br />";

if( $member_id['user_group'] < 4 ) {
	
	if( ! extension_loaded( "gd" ) ) echo "<font color=\"red\"><b>$lang[images_nogd]</b></font>";
	else echo "<input type=\"checkbox\" name=\"make_thumb\" value=\"make_thumb\" id=\"make_thumb\" checked> <label for=make_thumb>$lang[images_ath]</label></b>";
	if( $config['allow_watermark'] == "yes" ) echo "<br /><input type=\"checkbox\" name=\"make_watermark\" value=\"yes\" id=\"make_watermark\" checked> <label for=make_watermark>$lang[images_water]</label></b><div class=\"hr_line\"></div>";

}

echo <<<HTML
</td>
<td>
	{$colorScript}
</td>
</tr>
</table>
<div style="padding:4px;">{$img_result}{$img_result_th}</div>
</form>
HTML;

if( $action == "quick" ) {
	
	$image_align = array ();
	$image_align[$config['image_align']] = "selected";
	
	echo <<<HTML
<form name="properties">
<div style="padding:4px;">
{$lang['images_align']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name='imageAlign'>
          <option value="none" {$image_align[0]}>{$lang['opt_sys_no']}</option>
          <option value="left" {$image_align['left']}>{$lang['images_left']}</option>
          <option value="right" {$image_align['right']}>{$lang['images_right']}</option>
          <option value="center" {$image_align['center']}>{$lang['images_center']}</option>
        </select>
</div>
</form>
HTML;
}

if( $action == "quick" ) {
	
	echo <<<HTML
<form action='{$_SERVER['REQUEST_URI']}' method='post' name="delimages" id="delimages">
<input type="hidden" name="subaction" value="deluploads">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<input type="hidden" name="area" value='{$area}'>
<input type="hidden" name="action" value='{$action}'>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['images_iln']}</div></td>
    	<td bgcolor="#EFEFEF" width="10" align="right"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()">

    </tr>
</table>

<div class="unterline"></div><table width="100%">
HTML;
	
	$config_path_image_upload = ROOT_DIR . "/uploads/";
	
	if( $area != "template" ) {
		
		$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images where author = '$author' AND news_id = '$news_id'" );
		
		$listimages = explode( "|||", $row['images'] );
		$i = 0;
		$this_size_2 = 0;
		$total_size = 0;
		
		if( $row['images'] != "" ) foreach ( $listimages as $dataimages ) {
			$i ++;
			
			$url_image = explode( "/", $dataimages );
			
			if( count( $url_image ) == 2 ) {
				
				$folder_prefix = $url_image[0] . "/";
				$dataimages = $url_image[1];
			
			} else {
				
				$folder_prefix = "";
				$dataimages = $url_image[0];
			
			}
			
			if( file_exists( $config_path_image_upload . "posts/" . $folder_prefix . $dataimages ) ) {
				
				$this_size = @filesize( $config_path_image_upload . "posts/" . $folder_prefix . $dataimages );
				$img_info = @getimagesize( $config_path_image_upload . "posts/" . $folder_prefix . $dataimages );
				$total_size += $this_size + $this_size_2;
				
				if( file_exists( $config_path_image_upload . "posts/" . $folder_prefix . "thumbs/" . $dataimages ) ) {
					
					$this_size_2 = @filesize( $config_path_image_upload . "posts/" . $folder_prefix . "thumbs/" . $dataimages );
					$img_info_th = @getimagesize( $config_path_image_upload . "posts/" . $folder_prefix . "thumbs/" . $dataimages );
					
					$thumb_link = "<a class=maintitle href=\"javascript:insertthumb('" . $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages . "','')\">$dataimages</a>";
					
					$original_link = "[ <a class=maintitle href=\"javascript:insertimage('" . $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages . "')\">{$lang['img_orig_ins']}</a> ] ";
					$link_id = "thumbimage";
				
				} else {
					
					$thumb_link = "<a class=maintitle href=\"javascript:insertimage('" . $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages . "')\">$dataimages</a>";
					
					$original_link = "";
					$link_id = "fullimage";
				
				}
				
				echo "<tr>
			  <td style=\"padding:2px;\">&nbsp;$thumb_link</td>
			  <td width=150 nowrap>{$original_link}[ <a class=maintitle href=\"javascript:ShowBild('" . $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages . "')\">" . $lang['images_view'] . "</a> ]</td>
			  <td align=\"right\" width=\"60\">$img_info[0]x$img_info[1]</td>
			  <td align=\"right\" width=\"10\"><input type=\"checkbox\" id=\"" . $link_id . "\" name=\"images[" . $folder_prefix . $dataimages . "]\" value=\"" . $folder_prefix . $dataimages . "\"></td>
			  </tr><tr><td background=\"{$config['http_home_url']}engine/skins/images/mline.gif\" height=1 colspan=4></td></tr>";
			}
		}
		
		$db->query( "SELECT id, name, onserver  FROM " . PREFIX . "_files where author = '$author' AND news_id = '$news_id'" );
		
		while ( $row = $db->get_row() ) {
			
			$this_size = formatsize( @filesize( ROOT_DIR . "/uploads/files/" . $row['onserver'] ) );
			$file_link = "<a class=maintitle href=\"javascript:insertfile('[attachment={$row['id']}]')\">{$row['name']}</a>";
			$file_type = explode( ".", $row['name'] );
			$file_type = totranslit( end( $file_type ) );
			
			if( in_array( $file_type, $allowed_video ) ) {
				
				if( $file_type == "mp3" ) {
					
					$video_link = "<a class=maintitle href=\"javascript:insertfile('[audio={$config['http_home_url']}uploads/files/{$row['onserver']}]')\">{$lang['inset_audio_link']}</a>";
				
				} elseif ($file_type == "swf") {

					$video_link = "<a class=maintitle href=\"javascript:insertfile('[flash=425,264]{$config['http_home_url']}uploads/files/{$row['onserver']}[/flash]')\">{$lang['inset_flash_link']}</a>";

				} else {
					
					$video_link = "<a class=maintitle href=\"javascript:insertfile('[video={$config['http_home_url']}uploads/files/{$row['onserver']}]')\">{$lang['inset_video_link']}</a>";
				}
			
			} else {
				$video_link = "";
			}
			
			echo "<tr>
	  <td style=\"padding:2px;\" width=\"100%\">&nbsp;$file_link</td>
	  <td width=\"150\" nowrap>$video_link&nbsp;</td>
	  <td align=\"right\" width=\"60\" nowrap>{$this_size}</td>
	  <td align=\"right\" width=\"10\"><input type=checkbox id=file name=files[] value=\"{$row['id']}\"></td>
	  </tr><tr><td background=\"{$config['http_home_url']}engine/skins/images/mline.gif\" height=1 colspan=4></td></tr>";
		
		}
		
		$db->free();
	}
	
	if( $area == "template" ) {
		
		$db->query( "SELECT id, name FROM " . PREFIX . "_static_files WHERE static_id = '$news_id' AND onserver = ''" );
		
		while ( $row = $db->get_row() ) {
			
			$url_image = explode( "/", $row['name'] );
			
			if( count( $url_image ) == 2 ) {
				
				$folder_prefix = $url_image[0] . "/";
				$dataimages = $url_image[1];
			
			} else {
				
				$folder_prefix = "";
				$dataimages = $url_image[0];
			
			}
			
			if( file_exists( $config_path_image_upload . "posts/" . $folder_prefix . $dataimages ) ) {
				
				$this_size = @filesize( $config_path_image_upload . "posts/" . $folder_prefix . $dataimages );
				$img_info = @getimagesize( $config_path_image_upload . "posts/" . $folder_prefix . $dataimages );
				$total_size += $this_size + $this_size_2;
				
				if( file_exists( $config_path_image_upload . "posts/" . $folder_prefix . "thumbs/" . $dataimages ) ) {
					
					$this_size_2 = @filesize( $config_path_image_upload . "posts/" . $folder_prefix . "thumbs/" . $dataimages );
					$img_info_th = @getimagesize( $config_path_image_upload . "posts/" . $folder_prefix . "thumbs/" . $dataimages );
					
					$thumb_link = "<a class=maintitle href=\"javascript:insertthumb('" . $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages . "','')\">$dataimages</a>";
					$original_link = "[ <a class=maintitle href=\"javascript:insertimage('" . $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages . "')\">{$lang['img_orig_ins']}</a> ] ";
					$link_id = "thumbstatic";
				
				} else {
					
					$thumb_link = "<a class=maintitle href=\"javascript:insertimage('" . $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages . "')\">$dataimages</a>";
					$link_id = "fullstatic";
					$original_link = "";
				
				}
				
				echo "<tr>
			  <td style=\"padding:2px;\">&nbsp;$thumb_link</td>
			  <td width=150>{$original_link}[ <a class=maintitle href=\"javascript:ShowBild('" . $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages . "')\">" . $lang['images_view'] . "</a> ]</td>
			  <td align=right width=60>$img_info[0]x$img_info[1]</td>
			  <td align=right width=10><input type=checkbox id=\"" . $link_id . "\" name=static_files[] alt=\"" . $folder_prefix . $dataimages . "\" value=\"" . $row['id'] . "\"></td>
			  </tr><tr><td background=\"{$config['http_home_url']}engine/skins/images/mline.gif\" height=1 colspan=4></td></tr>";
			}
		}
		
		$db->query( "SELECT id, name, onserver  FROM " . PREFIX . "_static_files where author = '$author' AND static_id = '$news_id' AND onserver != ''" );
		
		while ( $row = $db->get_row() ) {
			
			$this_size = formatsize( @filesize( ROOT_DIR . "/uploads/files/" . $row['onserver'] ) );
			$file_link = "<a class=maintitle href=\"javascript:insertfile('[attachment={$row['id']}]')\">{$row['name']}</a>";
			$file_type = explode( ".", $row['name'] );
			$file_type = totranslit( end( $file_type ) );
			
			if( in_array( $file_type, $allowed_video ) ) {
				
				if( $file_type == "mp3" ) {
					
					$video_link = "<a class=maintitle href=\"javascript:insertfile('[audio={$config['http_home_url']}uploads/files/{$row['onserver']}]')\">{$lang['inset_audio_link']}</a>";
				
				} else {
					
					$video_link = "<a class=maintitle href=\"javascript:insertfile('[video={$config['http_home_url']}uploads/files/{$row['onserver']}]')\">{$lang['inset_video_link']}</a>";
				}
			
			} else {
				$video_link = "";
			}
			
			echo "<tr>
	  <td style=\"padding:2px;\" width=\"100%\">&nbsp;$file_link</td>
	  <td width=\"150\" nowrap>$video_link&nbsp;</td>
	  <td align=\"right\" width=\"60\" nowrap>{$this_size}</td>
	  <td align=\"right\" width=\"10\"><input type=checkbox id=file name=static_files[] value=\"{$row['id']}\"></td>
	  </tr><tr><td background=\"{$config['http_home_url']}engine/skins/images/mline.gif\" height=1 colspan=4></td></tr>";
		
		}
		
		$db->free();
	}
	
	echo "<tr>
    <td colspan=4><div class=\"hr_line\"></div></td>
    </tr>
	<tr>
    <td colspan=4 align=\"right\"><input class=\"edit\" type=\"button\" onClick=\"insert_all();\" value=' $lang[images_all_insert] '> <input class=edit type=submit value=' $lang[images_del] '>
    </tr></table></form><br>";
}

if(isset($_REQUEST['colors'])) {
	$folderSelect = $db->super_query("SELECT * FROM " . PREFIX . "_directories WHERE translit='{$_REQUEST['colors']}'");
	$folder = $folderSelect['name'];
} else {$folder = $lang['images_lgem'];}

echo "<table width=\"100%\">
    <tr>
        <td bgcolor=\"#EFEFEF\" height=\"29\" style=\"padding-left:10px;\"><div class=\"navigation\">{$folder}</div></td>
    </tr>
</table>
<div class=\"unterline\"></div>
<table width=100%>
    <form action='{$_SERVER['REQUEST_URI']}' METHOD='POST'>";

$img_dir = opendir( $config_path_image_upload );
$i = 0;
$total_size = 0;
$this_size_2 = 0;

while ( $file = readdir( $img_dir ) ) {
	$images_in_dir[] = $file;
}

natcasesort( $images_in_dir );
reset( $images_in_dir );

foreach ( $images_in_dir as $file ) {
	
	$img_type = explode( ".", $file );
	$img_type = end( $img_type );
	
	if( (in_array( $img_type, $allowed_extensions ) or in_array( strtolower( $img_type ), $allowed_extensions )) and $file != ".." and $file != "." and is_file( $config_path_image_upload . $file ) ) {
		
		$i ++;
		$this_size = @filesize( $config_path_image_upload . $file );
		$img_info = @getimagesize( $config_path_image_upload . $file );
		$total_size += $this_size + $this_size_2;
		
		if( file_exists( $config_path_image_upload . "thumbs/" . $file ) ) {
			
			$this_size_2 = @filesize( $config_path_image_upload . "thumbs/" . $file );
			$img_info_th = @getimagesize( $config_path_image_upload . "thumbs/" . $file );
			$preview = "<a class=maintitle target=_blank href=\"" . $config['http_home_url'] . "uploads/" . $userdir . $sub_dir . "thumbs/$file\" title=\"$img_info_th[0]x$img_info_th[1]\">" . $lang['images_thn'] . "</a>";
			
			$thumb_link = "<a class=maintitle href=\"javascript:insertthumb('" . $config['http_home_url'] . "uploads/$userdir" . $file . "','')\">$file</a>";
		
		} else {
			
			$preview = "";
			$thumb_link = "<a class=maintitle href=\"javascript:insertimage('" . $config['http_home_url'] . "uploads/$userdir" . $file . "')\">$file</a>";
		}
		
		if( $action == "quick" ) {
			
			echo "<tr>
	  <td style=\"padding:2px;\">&nbsp;$thumb_link</td>
	  <td align=center width=60><a class=maintitle href=\"javascript:ShowBild('" . $config['http_home_url'] . "uploads/$file')\">" . $lang['images_view'] . "</a></td>
	  <td align=right width=60>$img_info[0]x$img_info[1]</td>
	  <td align=right width=60><nobr>" . formatsize( $this_size ) . "</nobr></td>
	  </tr><tr><td background=\"{$config['http_home_url']}engine/skins/images/mline.gif\" height=1 colspan=4></td></tr>";
		
		} else {
			if(!isset($_REQUEST['colors']) || $_REQUEST['colors'] == "") {
			echo "
	  <tr>
	  <td style=\"padding:2px;\">&nbsp;<a class=maintitle target=_blank href=\"" . $config['http_home_url'] . "uploads/" . $userdir . $sub_dir . "$file\">$file</a></td>
	  <td align=center width=180>$preview&nbsp;</td>
	  <td align=right width=60>$img_info[0]x$img_info[1]</td>
	  <td align=right width=60><nobr>" . formatsize( $this_size ) . "</nobr></td>
	  <td align=center width=10>
          <input type=checkbox name=images[$file] value=\"$file\" style=\"border: 0; background: transparent;\">
	  </tr><tr><td background=\"{$config['http_home_url']}engine/skins/images/mline.gif\" height=1 colspan=6></td></tr>";
      		} else {
            	
            }
		}
	}
}
if($action != quick && $_REQUEST['colors'] != "") {

	if(isset($_REQUEST['colorid']) && $_REQUEST['this'] == "del") {
    	$rowPicResult = $db->query("SELECT * FROM " . PREFIX . "_colors WHERE id='{$_REQUEST['colorid']}'");
        $rowPic = $db->get_array($rowPicResult);
        unlink(ROOT_DIR . "/uploads/color/" . $rowPic['name']);
        $db->query("DELETE FROM " . PREFIX . "_colors WHERE id='{$_REQUEST['colorid']}'"); 
    }
	 $resultColors = $db->query("SELECT * FROM " . PREFIX . "_colors WHERE name LIKE '{$_REQUEST['colors']}%' ORDER BY id ASC");
    $colors = $db->get_array($resultColors);
    
	
   	$resultColors = $db->query("SELECT * FROM " . PREFIX . "_colors WHERE name LIKE '{$_REQUEST['colors']}%' ORDER BY id ASC");
    
    if($db->num_rows($resultColors) != 0) {
    echo "
        <style>
            #table {border:1px #000000 solid;}
            #firsttr p {color:#06538f;}
			#redd p {margin:1px; padding:1px;}
        </style>
        <table cellpadding='0' cellspacing='0' border='1' width='100%' id='table'>
            <tr id='firsttr'>
                <td align='center' width='22%'><p>Загруженный файл</p></td>
                <td align='center' width='6%'><p>Размер картинки</p></td>
                <td align='center' width='6%'><p>Размер при открытии</p></td>
                <td align='center' width='12%'><p>Наш номер цвета</p></td>
                <td align='center' width='12%'><p>Родной номер цвета</p></td>
                <td align='center' width='16%'><p>Коментарии</p></td>
                <td align='center' width='16%'><p>Описание</p></td>
                <td align='center' width='10%'><p>Действие</p></td>
            </tr>
        
    ";
    
     $colors = $db->get_array($resultColors);
     
     do {
     $colorName = explode("_", $colors['name']);
     	echo "
        	<tr>
                <td align='left' width='22%' valign='top'><p style='padding:3px;'>{$colors['name']}</p></td>
                <td align='center' width='6%' valign='top'><p style='padding:3px;'>{$colors['thumbSize']}</p></td>
                <td align='center' width='6%' valign='top'><p style='padding:3px;'>{$colors['bigSize']}</p></td>
                <td align='left' width='12%' valign='top'><p style='padding:3px;'>{$colors['ourNumber']}</p></td>
                <td align='left' width='12%' valign='top'><p style='padding:3px;'>{$colors['nativeNumber']}</p></td>
                <td align='left' width='16%' valign='top'><p style='padding:3px;'>{$colors['comments']}</p></td>
                <td align='left' width='16%' valign='top'><p style='padding:3px;'>{$colors['opic']}</p></td>
                <td align='center' width='10%' id='redd'>
                    <p><a href='admin.php?mod=files&this=del&colors={$colorName[0]}&colorid={$colors['id']}' style='color:red;'>Удалить</a></p>
                    <p><a href='admin.php?mod=files&this=red&colors={$colorName[0]}&colorid={$colors['id']}' style='color:red;'>Редактировать</a></p>
                </td>
            </tr>
        ";
     } while($colors = $db->get_array($resultColors));
     echo "</table>";
    }
}
if( $i > 0 ) {
	
	echo "<tr><td height=16>";
	
	if( $action != "quick" and $member_id['user_group'] == 1 ) {
		if(!isset($_REQUEST['colors']) || $_REQUEST['colors'] == ""){
			echo "<td colspan=5 align=right><br><input class=edit type=submit value=' $lang[images_del] '></tr>";
		}
	}
	if(!isset($_REQUEST['colors']) || $_REQUEST['colors'] == "") {
	echo "<tr height=1><td colspan=6><b>$lang[images_size]</b> " . formatsize( $total_size ) . '<tr>';
	}
}

if( $member_id['user_group'] == 1 and $action != "quick" ) {
	
	echo "<input type=hidden name=action value=doimagedelete><input type=hidden name=userdir value=$userdir><input type=\"hidden\" name=\"user_hash\" value=\"$dle_login_hash\" />

    
<tr height=1>
<td colspan=6><b>$lang[images_listdir]</b> 
<form action='{$_SERVER['REQUEST_URI']}' method='post'>
<select onchange=\"window.open(this.options[this.selectedIndex].value,'_top')\"><option value=$PHP_SELF?mod=files>--</option>";
	
	$current_dir = opendir( ROOT_DIR . "/uploads" );
	
	while ( $entryname = readdir( $current_dir ) ) {
		
		if( is_dir( ROOT_DIR . "/uploads/$entryname" ) and ($entryname != "." and $entryname != ".." and $entryname != "files") ) {
			
			if( $userdir == $entryname . "/" ) $sel_dir = "selected";
			else $sel_dir = "";
			
			if( $entryname == "fotos" ) $listname = $lang['images_foto'];
			elseif( $entryname == "thumbs" ) $listname = $lang['images_thumb'];
			elseif( $entryname == "posts" ) $listname = $lang['images_news'];
			else $listname = $entryname;
			$directoryResult = $db->query("SELECT * FROM " . PREFIX . "_directories WHERE translit='{$listname}'");
            if($db->num_rows($directoryResult) != 0) {
            	$directoryRow = $db->get_array($directoryResult);
                $listname = $directoryRow['name'];
            }
            if($listname != "color") {
			echo "<option value=\"$PHP_SELF?mod=files&userdir=" . str_replace( ' ', '%20', ${entryname} ) . "\" $sel_dir>$listname";
			echo "</option>";
            }
		}
	}
	$current_dir = opendir( ROOT_DIR . "/uploads/posts" );
	
	while ( $entryname = readdir( $current_dir ) ) {
		
		if( is_dir( ROOT_DIR . "/uploads/posts/$entryname" ) and ($entryname != "." and $entryname != ".." and $entryname != "thumbs") ) {
			
			if( $sub_dir == $entryname . "/" ) $sel_dir = "selected";
			else $sel_dir = "";
			
			echo "<option value=\"$PHP_SELF?mod=files&userdir=posts&sub_dir=" . str_replace( ' ', '%20', $entryname ) . "\" $sel_dir>{$lang['images_news']} / $entryname";
			echo "</option>";
		}
	}
	echo "</select></form>";
    
    echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
    echo "<br><b>Добавить директорию:</b>";
    
    echo "<input type='text' name='mk_dir' value='' style=\"margin-left:5px; margin-top:1px; font-size:10px; width:167px;\">";
    echo "<input type='hidden' name='mod' value='files'>";
    echo "<input class=edit  type='submit' name='add_dir' value='Добавить директорию' style='margin-left:3px;'>";
    echo "</form>";
    
    
    echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
    echo "<br><b>Удалить директорию:</b>";
    echo "<select name='dirrrr' style=\"margin-left:13px; margin-top:1px; font-size:10px; \">"; 
    echo "<option value=''>--</option>";
    $open = opendir("uploads");
    readdir($open);
    while (($file = readdir($open)) !== false)
      {
      	if($file != "." && $file != ".."){
          if(is_dir("uploads/" . $file)){ 
          	if($file != "files" && $file != "fotos" && $file != "posts" && $file != "thumbs" && $file != "color"){
            $directory = $db->super_query("SELECT * FROM " . PREFIX . "_directories WHERE translit='{$file}'");
            echo "<option value='$file'>{$directory['name']}</option>";
 			}
          }
        }
      }
    echo "</select>"; 
   
    echo "<input type='hidden' name='mod' value='files'>";
    echo "<input class=edit  type='submit' name='del_dir' value='Удалить директорию' style='margin-left:3px;'>";
    echo "</form>";
    
    
	echo "</tr>";
}



echo '</table></form>';




if( $action != "quick" ) {
	
	echo <<<HTML
</td>
        <td background="{$config['http_home_url']}engine/skins/images/tl_rb.gif"><img src="{$config['http_home_url']}engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="{$config['http_home_url']}engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="{$config['http_home_url']}engine/skins/images/tl_ub.gif"><img src="{$config['http_home_url']}engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="{$config['http_home_url']}engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
HTML;
	
	echofooter();

} else {
	echo <<<HTML
		</td>
		<td width="4" background="{$config['http_home_url']}engine/skins/images/tb_rt.gif"><img src="{$config['http_home_url']}engine/skins/images/tb_rt.gif" width="4" height="1" border="0" /></td>
    </tr>
	<tr>
        <td height="16" background="{$config['http_home_url']}engine/skins/images/tb_lb.gif"></td>
		<td background="{$config['http_home_url']}engine/skins/images/tb_tb.gif"></td>
		<td background="{$config['http_home_url']}engine/skins/images/tb_rb.gif"></td>
    </tr>
</table>
</body>

</html>
HTML;
}
?>