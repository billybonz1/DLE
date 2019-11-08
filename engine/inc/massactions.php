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
 Файл: massaction.php
-----------------------------------------------------
 Назначение: массовые действие
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	die( "Hacking attempt!" );
}

if($member_id['user_group'] == 7) {
	$member_id['user_group'] = 1;
}

$db->query("SET NAMES cp1251");

if( ! $_SESSION['admin_referrer'] ) {
	
	$_SESSION['admin_referrer'] = "?mod=editnews&amp;action=list";

}

if( !$user_group[$member_id['user_group']]['admin_editnews'] OR !$user_group[$member_id['user_group']]['allow_all_edit'] ) {
	msg( "error", $lang['mass_error'], $lang['mass_ddenied'], $_SESSION['admin_referrer'] );
}

$selected_news = $_REQUEST['selected_news'];



if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
	
	die( "Hacking attempt! User not found" );

}

$action = htmlspecialchars( strip_tags( stripslashes( $_REQUEST['action'] ) ) );

$k_mass = false;
$field = false;

if( $action == "mass_approve" ) {
	$field = "approve";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_app_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_app_fr1'];
} elseif( $action == "mass_date" ) {
	$field = "date";
	$value = date( "Y-m-d H:i:s", time() + ($config['date_adjust'] * 60) );
	$k_mass = true;
	$title = $lang['mass_edit_date_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_date_fr1'];
} elseif( $action == "mass_not_approve" ) {
	$field = "approve";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_app_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_app_fr2'];
} elseif( $action == "mass_fixed" ) {
	$field = "fixed";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_fix_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_fix_fr1'];
} elseif( $action == "mass_not_fixed" ) {
	$field = "fixed";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_fix_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_fix_fr2'];
} elseif( $action == "mass_comments" ) {
	$field = "allow_comm";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_com_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_comm_fr1'];
	$lang[mass_confirm_1] = $lang[mass_confirm_2];
} elseif( $action == "mass_not_comments" ) {
	$field = "allow_comm";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_com_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_comm_fr2'];
	$lang[mass_confirm_1] = $lang[mass_confirm_2];
} elseif( $action == "mass_rating" ) {
	$field = "allow_rate";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_rate_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_rate_fr1'];
	$lang[mass_confirm_1] = $lang[mass_confirm_2];
} elseif( $action == "mass_not_rating" ) {
	$field = "allow_rate";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_rate_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_rate_fr2'];
	$lang[mass_confirm_1] = $lang[mass_confirm_2];
} elseif( $action == "mass_main" ) {
	$field = "allow_main";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_main_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_main_fr1'];
} elseif( $action == "mass_not_main" ) {
	$field = "allow_main";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_main_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_main_fr2'];

} elseif( $action == "mass_clear_count" ) {
	$field = "news_read";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_clear_count_2'];
	$lang['mass_confirm'] = $lang['mass_clear_count_1'];
	$lang[mass_confirm_1] = $lang[mass_confirm_2];

} elseif( $action == "mass_clear_rating" ) {
	$field = "rating";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_clear_rating_2'];
	$lang['mass_confirm'] = $lang['mass_clear_rating_1'];
	$lang[mass_confirm_1] = $lang[mass_confirm_2];
}

if( $_POST['doaction'] == "mass_update" and $field ) {
	
	foreach ( $selected_news as $id ) {
		$id = intval( $id );
		$db->query( "UPDATE " . PREFIX . "_post SET {$field}='{$value}' WHERE id='{$id}'" );
		
		if( $field == "approve" ) {
			
			if( $value ) {
				
				$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$id}'" );
				$row = $db->super_query( "SELECT tags FROM " . PREFIX . "_post where id = '{$id}'" );
				
				if( $row['tags'] ) {
					
					$tags = array ();
					
					$row['tags'] = explode( ",", $row['tags'] );
					
					foreach ( $row['tags'] as $tags_value ) {
						
						$tags[] = "('" . $id . "', '" . trim( $tags_value ) . "')";
					}
					
					$tags = implode( ", ", $tags );
					$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
				
				}
			
			} else {
				
				$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$id}'" );
			
			}
		
		}

		if ( $field == "news_read" ) {

			$db->query( "DELETE FROM " . PREFIX . "_views WHERE news_id = '{$id}'" );

		}

		if ( $field == "rating" ) {

			$db->query( "UPDATE " . PREFIX . "_post SET vote_num='0' WHERE id='{$id}'" );
			$db->query( "DELETE FROM " . PREFIX . "_logs WHERE news_id = '{$id}'" );

		}
	
	}
	
	clear_cache();
	
	msg( "info", $lang['db_ok'], $lang['db_ok_1'], $_SESSION['admin_referrer'] );
}

if( $k_mass ) {
	if( ! $selected_news ) {
	msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
}
	echoheader( "options", $lang['mass_head'] );
	
	echo <<<HTML
<form action="{$PHP_SELF}" method="post">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$title}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100" align="center">{$lang['mass_confirm']}
HTML;
	
	echo " (<b>" . count( $selected_news ) . "</b>) $lang[mass_confirm_1]<br><br>
<input class=bbcodes type=submit value=\"   $lang[mass_yes]   \"> &nbsp; <input type=button class=bbcodes value=\"  $lang[mass_no]  \" onclick=\"javascript:document.location='$PHP_SELF?mod=editnews&action=list'\">
<input type=hidden name=action value=\"{$action}\">
<input type=hidden name=user_hash value=\"{$dle_login_hash}\">
<input type=hidden name=doaction value=\"mass_update\">
<input type=hidden name=mod value=\"massactions\">";
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">\n";
	}
	
	echo <<<HTML
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
HTML;
	
	echofooter();
	exit();

}
/* -----------------------------------------------------~~~~~~~~~~
  Подтвреждение удаления
 -----------------------------------------------------~~~~~~~~~~ */
if( $action == "mass_delete" ) {
	if( ! $selected_news ) {
	msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
}
	echoheader( "options", $lang['mass_head'] );
	
	echo <<<HTML
<form action="{$PHP_SELF}" method="post">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['mass_head']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100" align="center">{$lang['mass_confirm']}
HTML;
	
	echo "(<b>" . count( $selected_news ) . "</b>) $lang[mass_confirm_1]<br><br>
<input class=bbcodes type=submit value=\"   $lang[mass_yes]   \"> &nbsp; <input type=button class=bbcodes value=\"  $lang[mass_no]  \" onclick=\"javascript:document.location='$PHP_SELF?mod=editnews&action=list'\">
<input type=hidden name=action value=\"do_mass_delete\">
<input type=hidden name=user_hash value=\"{$dle_login_hash}\">
<input type=hidden name=mod value=\"massactions\">";
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">\n";
	}
	
	echo <<<HTML
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
HTML;
	
	echofooter();
	exit();

} 
/* -----------------------------------------------------~~~~~~~~~~
  Удаление новостей
 -----------------------------------------------------~~~~~~~~~~ */
elseif( $action == "do_mass_delete" ) {
	if( ! $selected_news ) {
	msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
}
	$deleted_articles = 0;
	
	foreach ( $selected_news as $id ) {
		
		$id = intval( $id );
		$row = $db->super_query( "SELECT autor FROM " . PREFIX . "_post where id = '$id'" );
		
		$db->query( "UPDATE " . USERPREFIX . "_users set news_num=news_num-1 where name='{$row['autor']}'" );
		
		$deleted_articles ++;
		
		$db->query( "DELETE FROM " . PREFIX . "_post WHERE id='$id'" );
		
		$db->query( "DELETE FROM " . PREFIX . "_comments WHERE post_id='$id'" );
		
		$db->query( "SELECT onserver FROM " . PREFIX . "_files WHERE news_id = '$id'" );
		while ( $row = $db->get_row() ) {
			@unlink( ROOT_DIR . "/uploads/files/" . $row['onserver'] );
		}
		$db->free();
		
		$db->query( "DELETE FROM " . PREFIX . "_files WHERE news_id = '$id'" );
		$db->query( "DELETE FROM " . PREFIX . "_poll WHERE news_id = '$id'" );
		$db->query( "DELETE FROM " . PREFIX . "_poll_log WHERE news_id = '$id'" );
		$db->query( "DELETE FROM " . PREFIX . "_post_log WHERE news_id = '$id'" );
		$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '$id'" );
		
		$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images where news_id = '$id'" );
		
		$listimages = explode( "|||", $row['images'] );
		
		if( $row['images'] != "" ) foreach ( $listimages as $dataimages ) {
			$url_image = explode( "/", $dataimages );
			
			if( count( $url_image ) == 2 ) {
				
				$folder_prefix = $url_image[0] . "/";
				$dataimages = $url_image[1];
			
			} else {
				
				$folder_prefix = "";
				$dataimages = $url_image[0];
			
			}
			
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages );
		}
		
		$db->query( "DELETE FROM " . PREFIX . "_images WHERE news_id = '$id'" );
	}
	
	clear_cache();
	
	if( count( $selected_news ) == $deleted_articles ) {
		msg( "info", $lang['mass_head'], $lang['mass_delok'], $_SESSION['admin_referrer'] );
	} else {
		msg( "error", $lang['mass_notok'], "$deleted_articles $lang[mass_i] " . count( $selected_news ) . " $lang[mass_notok_1]", $_SESSION['admin_referrer'] );
	}
} 
/* -----------------------------------------------------~~~~~~~~~~
  Подтвеждение смены категорий
 -----------------------------------------------------~~~~~~~~~~ */
elseif( $action == "mass_move_to_cat" ) {
	if( ! $selected_news ) {
	msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
}
	echoheader( "options", $lang['mass_cat'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"cat_select\" multiple";
	else $category_multiple = "";
	
	echo <<<HTML
<form action="{$PHP_SELF}" method="post">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['mass_cat_1']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100">{$lang['mass_cat_2']} (<b>{$count}</b>) {$lang['mass_cat_3']}
<select name="move_to_category[]" align="absmiddle" {$category_multiple}>
HTML;
	
	echo CategoryNewsSelection( 0, 0 );
	echo "</select>";
	
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">";
	}
	
	echo <<<HTML
<input type=hidden name=user_hash value="{$dle_login_hash}"><input type="hidden" name="action" value="do_mass_move_to_cat"><input type="hidden" name="mod" value="massactions">&nbsp;<input type="submit" value="{$lang['b_start']}" class="buttons"></td>
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
HTML;
	
	echofooter();
	exit();
} 

elseif ( $action == "mass_edit_price" ) {

			
		 $ddb = mysql_connect ("localhost", "lakikras_laki", "Hli1wtwtobz");
		 mysql_select_db("lakikras_laki", $ddb);
		mysql_query("SET NAMES cp1251");
		if(isset($_REQUEST['ac'])) {
			mysql_query("DELETE FROM " . PREFIX . "_price WHERE id='" . $_REQUEST['itid'] . "'");
			
			$checkItId = mysql_query("SELECT * FROM " . PREFIX . "_price WHERE thisid='" . $_REQUEST['bid'] . "'");
			if(mysql_num_rows($checkItId) == 0) {
				mysql_query("DELETE FROM " . PREFIX . "_price_g WHERE cat='" . $_REQUEST['bid'] . "'");
			}
		}
		if(isset($_REQUEST['ac_k'])) {
			//mysql_query("DELETE FROM " . PREFIX . "_price WHERE id='" . $_REQUEST['itid'] . "'");
			
			$ac_k = mysql_query("select my_table.id id, sum(my_table.st_upak) as s, sum(my_table.st_upak)/IF(INSTR(my_table.pack, ' ') = 0,1,LEFT(my_table.pack, INSTR(my_table.pack, ' '))) as l from
			(
			select p.id, p.pack, (REPLACE(k.Price_litr, ',', '.') * p.kod1k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod1 
			and p.id in (select id from `dle_price` where k = 1)
			union all
			select p.id, p.pack, (REPLACE(k.Price_litr, ',', '.') * p.kod2k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod2 
			and p.id in (select id from `dle_price` where k = 1)
			union all
			select p.id, p.pack, (REPLACE(k.Price_litr, ',', '.') * p.kod3k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod3 
			and p.id in (select id from `dle_price` where k = 1)
			union all
			select p.id, p.pack, (REPLACE(k.Price_litr, ',', '.') * p.kod4k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod4 
			and p.id in (select id from `dle_price` where k = 1)
			union all
			select p.id, p.pack, (REPLACE(k.Price_litr, ',', '.') * p.kod5k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod5 
			and p.id in (select id from `dle_price` where k = 1)
			union all
			select p.id, p.pack, (REPLACE(k.Price_litr, ',', '.') * p.kod6k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod6 
			and p.id in (select id from `dle_price` where k = 1)
			union all
			select p.id, p.pack, (REPLACE(k.Price_litr, ',', '.') * p.kod7k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod7 
			and p.id in (select id from `dle_price` where k = 1)
			union all
			select p.id, p.pack, (REPLACE(k.Price_litr, ',', '.') * p.kod8k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod8 
			and p.id in (select id from `dle_price` where k = 1)
			) as my_table
			group by my_table.id;");
			
			while($myrow_ac = mysql_fetch_array($ac_k)) {
			 //echo $myrow_ac['id']. ' - ' . $myrow_ac['s']. ' - ' . round($myrow_ac['l'],2).' | ';
				mysql_query("UPDATE dle_price SET Price_litr='".round($myrow_ac['l'],2)."', price_yp='".round($myrow_ac['s'],2)."' WHERE  id=".$myrow_ac['id']);
			}
			//if(mysql_num_rows($checkItId) == 0) {
			//	mysql_query("DELETE FROM " . PREFIX . "_price_g WHERE cat='" . $_REQUEST['bid'] . "'");
			//}
		}
		
		if(isset($_REQUEST['save_changes'])){
			$chidNum = count($_REQUEST['chid']);
			for($i=0; $i<$chidNum; $i++){
				$currDate = date("d.m.Y");
				$currId = $_REQUEST['chid'][$i];
				if($_REQUEST['komplekt'][$currId]=="true") $komplekt="true"; else $komplekt = "false";
				$currArtikyl = $_REQUEST['artikyl'][$i];
				$currPack = $_REQUEST['pack'][$i];
				$currCvet = $_REQUEST['cvet'][$i];
				$currBlesk = $_REQUEST['blesk'][$i];
				$currPriceYp = $_REQUEST['price_yp'][$i];
				$currPriceLitr = $_REQUEST['price_litr'][$i];
				$currPack2 = explode(" ", $currPack);

				$result_c = mysql_query("select my_table.id id, sum(my_table.st_upak) as s from
				(
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod1k) as st_upak  from `dle_price` p, `dle_price` k where k.id = p.kod1 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod2k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod2 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod3k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod3 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod4k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod4 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod5k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod5 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod6k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod6 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod7k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod7 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod8k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod8 and p.id = '$currId' and p.k = 1
				) as my_table
				group by my_table.id;");

				
				if($myrow_c = mysql_fetch_array($result_c)) {
					$currPriceYp = round($myrow_c[s],2);
				} 

				$prLit = $currPriceYp/$currPack2[0];
				$prLitr = round($prLit, 2);
				if($currArtikyl == "") {$currArtikyl = "---//---";//$kod_p="-/ /-";
				}//else {$kod_p=substr(md5($currArtikyl),0,5);}
				if($currPack == "") {$currPack = "---//---";}
				if($currCvet == "") {$currCvet = "---//---";}
				if($currBlesk == "") {$currBlesk = "---//---";}
				if($currPriceYp == "") {$currPriceYp = "---//---"; $currPriceLitr = "---//---";}
				
				mysql_query("UPDATE dle_price SET date='$currDate', artikyl='$currArtikyl', pack='$currPack', cvet='$currCvet', blesk='$currBlesk', price_yp='$currPriceYp', price_litr='$prLitr', komplekt='$komplekt' WHERE id='$currId'");

			}
			
			for($i=0; $i<$chidNum; $i++){
				$currId = $_REQUEST['chid'][$i];
				$result_curr_id = mysql_query("SELECT thisid FROM " . PREFIX . "_price WHERE id='$currId'");
				$myrow_curr_id = mysql_fetch_array($result_curr_id);
				
				$current = array();
				$current = $_REQUEST['date_g'];
				
				$currentName = array();
				$currentName = $_REQUEST['name_g'];
				
				$currentProiz = array();
				$currentProiz = $_REQUEST['proiz_g'];
				
				$currentArtikyl = array();
				$currentArtikyl = $_REQUEST['artikyl_g'];
				
				$currentCvet = array();
				$currentCvet = $_REQUEST['cvet_g'];
				
				$currentPack = array();
				$currentPack = $_REQUEST['pack_g'];
				
				$currentBlesk = array();
				$currentBlesk = $_REQUEST['blesk_g'];
				
				$currentPriceYp = array();
				$currentPriceYp = $_REQUEST['price_yp_g'];
				
				$currentPriceLitr = array();
				$currentPriceLitr = $_REQUEST['price_litr_g'];
				
				
				$result_cat = mysql_query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$myrow_curr_id[thisid]}'");
				$myrow_cat = mysql_fetch_array($result_cat);
				do {
					if(isset($current[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET date_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET date_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentName[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET name_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET name_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentProiz[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET proiz_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET proiz_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentArtikyl[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET artikyl_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET artikyl_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentCvet[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET cvet_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET cvet_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPack[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET pack_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET pack_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentBlesk[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET blesk_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET blesk_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPriceYp[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_yp_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_yp_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPriceLitr[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_litr_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_litr_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
				} while($myrow_cat = mysql_fetch_array($result_cat));
				
			}
			
		}
		
		if(isset($_REQUEST['su'])) {
			$iw="";
			$iw2="";
			$date = date("d.m.Y");
			if(isset($_REQUEST['artikyl'])) {$artikyl = $_REQUEST['artikyl'];// $kodph = substr(md5($artikyl),0,5);
			} else {$artikyl = "---//---";//$kodph = "-/ /-";
			}
			if(isset($_REQUEST['cvet'])) {$cvet = $_REQUEST['cvet'];} else {$cvet = "---//---";}
			if(isset($_REQUEST['pack'])) {$pack = $_REQUEST['pack'];} else {$pack = "---//---";}
			if(isset($_REQUEST['blesk'])) {$blesk = $_REQUEST['blesk'];} else {$blesk = "---//---";}
			if(isset($_REQUEST['priceyp'])) {$priceyp = $_REQUEST['priceyp'];} else {$priceyp = "---//---";}
			
			if(isset($_REQUEST['thisid'])) {$thisid = $_REQUEST['thisid'];} else {$thisid = "---//---";}
			if(isset($_REQUEST['k'])) {
				$tm = time();
				$iw = ",k,kod1k,kod2k,kod3k,kod4k,kod5k,kod6k,kod7k,kod8k,kod1,kod2,kod3,kod4,kod5,kod6,kod7,kod8,tm,tara1,tara2,tara3,tara4,tara5,tara6,tara7,tara8";
				$k = $_REQUEST['k'];
				if(isset($_REQUEST['kolsmes'])) $kolsmes = $_REQUEST['kolsmes']; 
				if(isset($_REQUEST['kodpr'])) $kodpr = $_REQUEST['kodpr']; 
				if(isset($_REQUEST['tara'])) $tara = $_REQUEST['tara']; 
				for ($i=0;$i<8;$i++) 
				{
					if (isset($tara[$i])) $tara[$i] = "true"; else $tara[$i] = "false";
					if ($kodpr[$i]==NULL or $kodpr[$i]=="") $kodpr[$i]=0; else $kodpr[$i] = substr($kodpr[$i],0,5);
					settype($kodpr[$i], "integer");
				}
				$iw2 = ",{$k},'{$kolsmes[0]}','{$kolsmes[1]}','{$kolsmes[2]}','{$kolsmes[3]}','{$kolsmes[4]}','{$kolsmes[5]}','{$kolsmes[6]}','{$kolsmes[7]}',{$kodpr[0]},{$kodpr[1]},{$kodpr[2]},{$kodpr[3]},{$kodpr[4]},{$kodpr[5]},{$kodpr[6]},{$kodpr[7]},{$tm},'{$tara[0]}','{$tara[1]}','{$tara[2]}','{$tara[3]}','{$tara[4]}','{$tara[5]}','{$tara[6]}','{$tara[7]}'";
			} 
	
			if($artikyl == "") {$artikyl = "---//---";}
			if($cvet == "") {$cvet = "---//---";}
			if($pack == "") {$pack = "---//---";}
			if($blesk == "") {$blesk = "---//---";}
			$pack2 = explode(" ", $pack);
			$pricelitr = $priceyp/$pack2[0];
			$pricelitr = round($pricelitr, 2);
			
			if($priceyp == "") {$priceyp = "---//---"; $pricelitr = "---//---";}
			mysql_query("SET NAMES cp1251");
			mysql_query("INSERT INTO " . PREFIX . "_price (id,date,artikyl,cvet,pack,blesk,price_yp,price_litr,thisid$iw) VALUES(null,'{$date}','{$artikyl}','{$cvet}','{$pack}','{$blesk}','{$priceyp}','{$pricelitr}','{$thisid}'$iw2)");
			$checkk = mysql_query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$thisid}'");
			if(mysql_num_rows($checkk) == 0) {
			mysql_query("INSERT INTO " . PREFIX . "_price_g (id,date_g,name_g,proiz_g,artikyl_g,cvet_g,pack_g,blesk_g,price_yp_g,price_litr_g,cat) VALUES (null,'false','false','false','false','true','true','true','true','true','{$thisid}')");
			}
			mysql_query("SET NAMES cp1251");
		}
		
		if(isset($_REQUEST['suedit'])) {
			$iw="";
			$iw2="";
			$date = date("d.m.Y");
			if(isset($_REQUEST['artikyl'])) {$artikyl = $_REQUEST['artikyl'];// $kodph = substr(md5($artikyl),0,5);
			} else {$artikyl = "---//---";//$kodph = "-/ /-";
			}
			if(isset($_REQUEST['cvet'])) {$cvet = $_REQUEST['cvet'];} else {$cvet = "---//---";}
			if(isset($_REQUEST['pack'])) {$pack = $_REQUEST['pack'];} else {$pack = "---//---";}
			if(isset($_REQUEST['blesk'])) {$blesk = $_REQUEST['blesk'];} else {$blesk = "---//---";}
			if(isset($_REQUEST['priceyp'])) {$priceyp = $_REQUEST['priceyp'];} else {$priceyp = "---//---";}
			
			if(isset($_REQUEST['thisid'])) {$thisid = $_REQUEST['thisid'];} else {$thisid = "---//---";}
			
				$iw = ",k,kod1k,kod2k,kod3k,kod4k,kod5k,kod6k,kod7k,kod8k,kod1,kod2,kod3,kod4,kod5,kod6,kod7,kod8,tara1,tara2,tara3,tara4,tara5,tara6,tara7,tara8";
				$k = $_REQUEST['k'];
				if(isset($_REQUEST['kolsmes'])) $kolsmes = $_REQUEST['kolsmes']; 
				if(isset($_REQUEST['kodpr'])) $kodpr = $_REQUEST['kodpr']; 
				if(isset($_REQUEST['itid'])) $itid = $_REQUEST['itid']; 
				if(isset($_REQUEST['tara'])) $tara = $_REQUEST['tara']; 
				for ($i=0;$i<8;$i++) 
				{
				//	if ($tara[$i] == "true") $tara[$i] = "true"; else $tara[$i] = "false";
					if ($kodpr[$i]==NULL or $kodpr[$i]=="") $kodpr[$i]=0; else $kodpr[$i] = substr($kodpr[$i],0,5);
					settype($kodpr[$i], "integer");
				}
				//var_dump($tara);
				$iw2 = ",{$k},'{$kolsmes[0]}','{$kolsmes[1]}','{$kolsmes[2]}','{$kolsmes[3]}','{$kolsmes[4]}','{$kolsmes[5]}','{$kolsmes[6]}','{$kolsmes[7]}',{$kodpr[0]},{$kodpr[1]},{$kodpr[2]},{$kodpr[3]},{$kodpr[4]},{$kodpr[5]},{$kodpr[6]},{$kodpr[7]},'{$tara[0]}','{$tara[1]}','{$tara[2]}','{$tara[3]}','{$tara[4]}','{$tara[5]}','{$tara[6]}','{$tara[7]}'";
			 
	
			if($artikyl == "") {$artikyl = "---//---";}
			if($cvet == "") {$cvet = "---//---";}
			if($pack == "") {$pack = "---//---";}
			if($blesk == "") {$blesk = "---//---";}
			$pack2 = explode(" ", $pack);
			$pricelitr = $priceyp/$pack2[0];
			$pricelitr = round($pricelitr, 2);
			
			if($priceyp == "") {$priceyp = "---//---"; $pricelitr = "---//---";}
			mysql_query("SET NAMES cp1251");
			mysql_query("UPDATE dle_price SET date='$date', artikyl='$artikyl', pack='$pack', cvet='$cvet', blesk='$blesk', price_yp='$priceyp', price_litr='$pricelitr', kod1k='{$kolsmes[0]}', kod2k='{$kolsmes[1]}', kod3k='{$kolsmes[2]}', kod4k='{$kolsmes[3]}', kod5k='{$kolsmes[4]}', kod6k='{$kolsmes[5]}', kod7k='{$kolsmes[6]}', kod8k='{$kolsmes[7]}', kod1={$kodpr[0]}, kod2={$kodpr[1]}, kod3={$kodpr[2]}, kod4={$kodpr[3]}, kod5={$kodpr[4]}, kod6={$kodpr[5]}, kod7={$kodpr[6]}, kod8={$kodpr[7]}, tara1='{$tara[0]}', tara2='{$tara[1]}', tara3='{$tara[2]}', tara4='{$tara[3]}', tara5='{$tara[4]}', tara6='{$tara[5]}', tara7='{$tara[6]}', tara8='{$tara[7]}' WHERE id='$itid'");
			

		}
		
		
		
		$rrrr = mysql_query("SELECT * FROM dle_post");
		$mmmm = mysql_fetch_array($rrrr);
		
		if(!isset($_COOKIE['news_array'])) {
			if( ! $selected_news ) {
				msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
				exit;
			}
			$selected_news_array = $_REQUEST["selected_news"];
			$sd = implode(",", $selected_news_array);
			setcookie("news_array", $sd);
		} else {
			$sd = explode(",", $_COOKIE['news_array']);
			$selected_news_array = $sd;
			
		}
		
		$selected_news_array_length = count($selected_news_array);
			
			
			
	echoheader( "options", $lang['mass_cat'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"cat_select\" multiple";
	else $category_multiple = "";
	
	echo <<<HTML
<form action="{$PHP_SELF}" method="post">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['mass_cat_1']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100">
HTML;
		
		
		
		
		?>
        <style>
			.ht {background-color:#e4e4e4; padding:4px;}
			.ht3 {color:#0b5e92;}
			.to4ki {font-size:7px; color:#999999;}
			#price_actions p {line-height:0.3;}
		</style>
        <table width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
        	<tr>
            	<td valign="middle" align="center" class="ht" width="65">Дата</td>
                <td valign="middle" align="center" class="ht">Продукция</td>
                <td valign="middle" align="center" class="ht">Код-продукта</td>
                <td valign="middle" align="center" class="ht">Комплект</td>
                <td valign="middle" align="center" class="ht" width="88">Производитель</td>
                <td valign="middle" align="center" class="ht" width="100">Артикул</td>
                <td valign="middle" align="center" class="ht" width="56">Упаковка</td>
                <td valign="top" align="center" class="ht" width="68">База и цвет</td>
                <td valign="top" align="center" class="ht" width="60">Степень блеска</td>
                <td valign="top" align="center" class="ht" width="58">Стоимость уп</td>
                <td valign="top" align="center" class="ht" width="58">Стоимость за 1л</td>
                <td valign="middle" align="center" class="ht" width="50">Действие</td>
            </tr>
        
        
        <?php
		echo "<form action='' method='post'>";
		for($i=0; $i<$selected_news_array_length; $i++){
		
			$podcatit = "";
			$curr = $selected_news_array[$i];
			$selecter_result = mysql_query("SELECT * FROM dle_post WHERE id='" . $curr . "'");
			$selected_myrow  = mysql_fetch_array($selecter_result);
			
			$expl = explode(",", $selected_myrow['category']);
			
			$select_category = mysql_query("SELECT * FROM dle_category WHERE id='$expl[0]'");
			$myrow_category = mysql_fetch_array($select_category);
			
			$result_catprice = mysql_query("SELECT * FROM dle_price WHERE thisid='" . $curr . "' ORDER BY id");
			$myrow_catproce = mysql_fetch_array($result_catprice);
			
			$result_catprice2 = mysql_query("SELECT * FROM dle_price_g WHERE cat='" . $curr . "'");
			$myrow_catproce2 = mysql_fetch_array($result_catprice2);
			
			
			if(mysql_num_rows($result_catprice) != 0) {
				
				if($myrow_catproce2["date_g"] == "true"){$date_g = "<input type='checkbox' name='date_g[$curr]' id='asd' value='true' checked>";} else {$date_g = "<input type='checkbox'  id='asd' name='date_g[$curr]' value='true'>";}
				if($myrow_catproce2["name_g"] == "true"){$name_g = "<input type='checkbox' name='name_g[$curr]' value='true' checked>";} else {$name_g = "<input type='checkbox' name='name_g[$curr]' value='true'>";}

				//if($myrow_catproce2["kodp_g"] == "true"){$kodp_g = "<input type='checkbox' name='kodp_g[$curr]' value='true' checked>";} else {$kodp_g = "<input type='checkbox' name='kodp_g[$curr]' value='true'>";}
				
				//$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true'>";
				//var_dump($myrow_catproce['komplekt']);
				//if($myrow_catproce['komplekt'] == "true"){$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true' checked>";} else {$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true'>";}
				
				if($myrow_catproce2["proiz_g"] == "true"){$proiz_g = "<input type='checkbox' name='proiz_g[$curr]' value='true' checked>";} else {$proiz_g = "<input type='checkbox' name='proiz_g[$curr]' value='true'>";}
				if($myrow_catproce2["artikyl_g"] == "true"){$artikyl_g = "<input type='checkbox' name='artikyl_g[$curr]' value='true' checked>";} else {$artikyl_g = "<input type='checkbox' name='artikyl_g[$curr]' value='true'>";}
				if($myrow_catproce2["cvet_g"] == "true"){$cvet_g = "<input type='checkbox' name='cvet_g[$curr]' value='true' checked>";} else {$cvet_g = "<input type='checkbox' name='cvet_g[$curr]' value='true'>";}
				if($myrow_catproce2["pack_g"] == "true"){$pack_g = "<input type='checkbox' name='pack_g[$curr]' value='true' checked>";} else {$pack_g = "<input type='checkbox' name='pack_g[$curr]' value='true'>";}
				if($myrow_catproce2["blesk_g"] == "true"){$blesk_g = "<input type='checkbox' name='blesk_g[$curr]' value='true' checked>";} else {$blesk_g = "<input type='checkbox' name='blesk_g[$curr]' value='true'>";}
				if($myrow_catproce2["price_yp_g"] == "true"){$price_yp_g = "<input type='checkbox' name='price_yp_g[$curr]' value='true' checked>";} else {$price_yp_g = "<input type='checkbox' name='price_yp_g[$curr]' value='true'>";}
				if($myrow_catproce2["price_litr_g"] == "true"){$price_litr_g = "<input type='checkbox' name='price_litr_g[$curr]' value='true' checked>";} else {$price_litr_g = "<input type='checkbox' name='price_litr_g[$curr]' value='true'>";}
					
					$podcatit = "
					<tr>
							<td valign='middle' align='center' class='ht2' width='65'>$date_g</td>
							<td valign='middle' align='center' class='ht3'>$name_g</td>
							<td valign='middle' align='center' class='ht3'></td>
							<td valign='middle' align='center' class='ht3'></td>
							<td valign='middle' align='center' class='ht2' width='88'>$proiz_g</td>
							<td valign='middle' align='center' class='ht2' width='100'>$artikyl_g</td>
							<td valign='middle' align='center' class='ht2' width='56'>$cvet_g</td>
							<td valign='middle' align='center' class='ht2' width='68'>$pack_g</td>
							<td valign='middle' align='center' class='ht2' width='60'>$blesk_g</td>
							<td valign='middle' align='center' class='ht2' width='58'>$price_yp_g</td>
							<td valign='middle' align='center' class='ht2' width='58'>$price_litr_g</td>
							<td valign='middle' align='center' class='ht2' width='50'>
								<div id='price_actions'>
									<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=add_this_komplekt&prid={$selected_myrow['id']}'>Комплект</a></p>
									<p><a href='admin.php?mod=massactions&user_hash={$dle_login_hash}&action=mass_edit_price&ac_k=komp' >Пересчитать</a></p>
								</div>
							</td>
							
						</tr>
						";
					do {
					    if($myrow_catproce['k'] == 1) $izmen = "<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=edit_this_komplekt&prid={$selected_myrow['id']}&itid={$myrow_catproce['id']}'>Изменить</a></p>";
						else $izmen = "";
						if($myrow_catproce['komplekt'] == "true"){$komplekt = "<input type='checkbox' name='komplekt[{$myrow_catproce[id]}]' value='true' checked>";} else {$komplekt = "<input type='checkbox' name='komplekt[{$myrow_catproce[id]}]' value='true'>";}
						$kod_p = $myrow_catproce[id];
						if(strlen($myrow_catproce[id]) < 5){
							if(strlen($myrow_catproce[id])==1) $kod_p = "0000".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==2) $kod_p = "000".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==3) $kod_p = "00".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==4) $kod_p = "0".$myrow_catproce[id];
						} 
					$podcatit .= "
						
						<tr>
							<td colspan='10' valign='middle' class='to4ki' align='center'>..........................................................................................................................................................................................................................</td>
						</tr>
						<tr>
							<td valign='middle' align='center' class='ht2' width='65'>{$myrow_catproce['date']}- </td>
							<td valign='middle' align='left' class='ht3'> {$selected_myrow['title']}</td>
							<td valign='middle' align='left' class='ht3'>{$kod_p}</td>
							<td valign='middle' align='left' class='ht3'>{$komplekt}</td>
							<td valign='middle' align='center' class='ht2' width='88'>{$myrow_category['name']}</td>
							<td valign='middle' align='center' class='ht2' width='47'>
								<input type='hidden' name='chid[]' value='{$myrow_catproce[id]}'>
								<input type='text' name='artikyl[]' style='width:100px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[artikyl]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='56'>
								<input type='text' name='pack[]' style='width:56px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[pack]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='68'>
								<input type='text' name='cvet[]' style='width:68px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[cvet]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='60'>
								<input type='text' name='blesk[]' style='width:60px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[blesk]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='58'>
								<input type='text' name='price_yp[]' style='width:58px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[price_yp]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='58'>
								{$myrow_catproce[price_litr]}
							</td>
							<td valign='middle' align='center' class='ht2' width='50'>
								<div id='price_actions'>
									<p><a href='admin.php?mod=massactions&user_hash={$dle_login_hash}&action=mass_edit_price&ac=del&itid={$myrow_catproce['id']}&bid={$selected_myrow['id']}'>Удалить</a></p>$izmen
								</div>
							</td>
							
						</tr>
						
					";
				} while($myrow_catproce = mysql_fetch_array($result_catprice));
			}
			
			printf("
			
			<tr>
            	
                <td valign='middle' align='left' class='ht3' colspan='11'><div align='center' style='font-weight:bold;'>%s</div></td>
				
			
                <td valign='middle' align='center' class='ht2' width='50'>
					<div id='price_actions'>
						<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=add_this_price&prid=%s'>Добавить</a></p>
					</div>
				</td>
				
            </tr>
			
			
			%s
			<tr>
				<td colspan='10' valign='middle' class='to4ki'>.........................................................................................................................................................................................................................................................................................................................</td>
			</tr>", $selected_myrow['title'], $selected_myrow['id'], $podcatit);
			
			
		}
		
		
		?>
        	</table>
            
            
        <?php	
	
	echo <<<HTML
		</td>
    </tr>
</table>
<input type=hidden name=user_hash value='{$dle_login_hash}'><input type='hidden' name='action' value='mass_edit_price'>
<input type='hidden' name='mod' value='massactions'><br><br>
<center><input type='submit' name='save_changes' class='edit' value='Сохранить' /></center><br>

</form>	
		
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
	exit();

}
elseif ( $action == "mass_edit_price_k" ) {

			
		 $ddb = mysql_connect ("localhost", "lakikras_laki", "Hli1wtwtobz");
		 mysql_select_db("lakikras_laki", $ddb);
		mysql_query("SET NAMES cp1251");
		if(isset($_REQUEST['ac'])) {
			mysql_query("DELETE FROM " . PREFIX . "_price WHERE id='" . $_REQUEST['itid'] . "'");
			
			$checkItId = mysql_query("SELECT * FROM " . PREFIX . "_price WHERE thisid='" . $_REQUEST['bid'] . "'");
			if(mysql_num_rows($checkItId) == 0) {
				mysql_query("DELETE FROM " . PREFIX . "_price_g WHERE cat='" . $_REQUEST['bid'] . "'");
			}
		}
		
		if(isset($_REQUEST['save_changes'])){
			$chidNum = count($_REQUEST['chid']);
			for($i=0; $i<$chidNum; $i++){
				$currDate = date("d.m.Y");
				$currId = $_REQUEST['chid'][$i];
				if($_REQUEST['komplekt'][$currId]=="true") $komplekt="true"; else $komplekt = "false";
				$currArtikyl = $_REQUEST['artikyl'][$i];
				$currPack = $_REQUEST['pack'][$i];
				$currCvet = $_REQUEST['cvet'][$i];
				$currBlesk = $_REQUEST['blesk'][$i];
				$currPriceYp = $_REQUEST['price_yp'][$i];
				$currPriceLitr = $_REQUEST['price_litr'][$i];
				$currPack2 = explode(" ", $currPack);
				
				$result_c = mysql_query("select my_table.id id, sum(my_table.st_upak) as s from
				(
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod1k) as st_upak  from `dle_price` p, `dle_price` k where k.id = p.kod1 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod2k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod2 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod3k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod3 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod4k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod4 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod5k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod5 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod6k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod6 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod7k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod7 and p.id = '$currId' and p.k = 1
				union all
				select p.id,(REPLACE(k.Price_litr, ',', '.') * p.kod8k) as st_upak from `dle_price` p, `dle_price` k where k.id = p.kod8 and p.id = '$currId' and p.k = 1
				) as my_table
				group by my_table.id;");

				
				if($myrow_c = mysql_fetch_array($result_c)) {
					$currPriceYp = round($myrow_c[s],2);
				} 
				
				$prLit = $currPriceYp/$currPack2[0];
				$prLitr = round($prLit, 2);
				if($currArtikyl == "") {$currArtikyl = "---//---";//$kod_p="-/ /-";
				}//else {$kod_p=substr(md5($currArtikyl),0,5);}
				if($currPack == "") {$currPack = "---//---";}
				if($currCvet == "") {$currCvet = "---//---";}
				if($currBlesk == "") {$currBlesk = "---//---";}
				if($currPriceYp == "") {$currPriceYp = "---//---"; $currPriceLitr = "---//---";}
				mysql_query("UPDATE dle_price SET date='$currDate', artikyl='$currArtikyl', pack='$currPack', cvet='$currCvet', blesk='$currBlesk', price_yp='$currPriceYp', price_litr='$prLitr', komplekt='$komplekt' WHERE id='$currId'");

			}
			
			for($i=0; $i<$chidNum; $i++){
				$currId = $_REQUEST['chid'][$i];
				$result_curr_id = mysql_query("SELECT thisid FROM " . PREFIX . "_price WHERE id='$currId'");
				$myrow_curr_id = mysql_fetch_array($result_curr_id);
				
				$current = array();
				$current = $_REQUEST['date_g'];
				
				$currentName = array();
				$currentName = $_REQUEST['name_g'];
				
				$currentProiz = array();
				$currentProiz = $_REQUEST['proiz_g'];
				
				$currentArtikyl = array();
				$currentArtikyl = $_REQUEST['artikyl_g'];
				
				$currentCvet = array();
				$currentCvet = $_REQUEST['cvet_g'];
				
				$currentPack = array();
				$currentPack = $_REQUEST['pack_g'];
				
				$currentBlesk = array();
				$currentBlesk = $_REQUEST['blesk_g'];
				
				$currentPriceYp = array();
				$currentPriceYp = $_REQUEST['price_yp_g'];
				
				$currentPriceLitr = array();
				$currentPriceLitr = $_REQUEST['price_litr_g'];
				
				
				$result_cat = mysql_query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$myrow_curr_id[thisid]}'");
				$myrow_cat = mysql_fetch_array($result_cat);
				do {
					if(isset($current[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET date_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET date_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentName[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET name_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET name_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentProiz[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET proiz_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET proiz_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentArtikyl[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET artikyl_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET artikyl_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentCvet[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET cvet_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET cvet_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPack[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET pack_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET pack_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentBlesk[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET blesk_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET blesk_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPriceYp[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_yp_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_yp_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPriceLitr[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_litr_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_litr_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
				} while($myrow_cat = mysql_fetch_array($result_cat));
				
			}
			
		}
		
		if(isset($_REQUEST['su'])) {
			$iw="";
			$iw2="";
			$date = date("d.m.Y");
			if(isset($_REQUEST['artikyl'])) {$artikyl = $_REQUEST['artikyl'];// $kodph = substr(md5($artikyl),0,5);
			} else {$artikyl = "---//---";//$kodph = "-/ /-";
			}
			if(isset($_REQUEST['cvet'])) {$cvet = $_REQUEST['cvet'];} else {$cvet = "---//---";}
			if(isset($_REQUEST['pack'])) {$pack = $_REQUEST['pack'];} else {$pack = "---//---";}
			if(isset($_REQUEST['blesk'])) {$blesk = $_REQUEST['blesk'];} else {$blesk = "---//---";}
			if(isset($_REQUEST['priceyp'])) {$priceyp = $_REQUEST['priceyp'];} else {$priceyp = "---//---";}
			
			if(isset($_REQUEST['thisid'])) {$thisid = $_REQUEST['thisid'];} else {$thisid = "---//---";}
			if(isset($_REQUEST['k'])) {
				$tm = time();
				$iw = ",k,kod1k,kod2k,kod3k,kod4k,kod5k,kod6k,kod7k,kod8k,kod1,kod2,kod3,kod4,kod5,kod6,kod7,kod8,tm,tara1,tara2,tara3,tara4,tara5,tara6,tara7,tara8";
				$k = $_REQUEST['k'];
				if(isset($_REQUEST['kolsmes'])) $kolsmes = $_REQUEST['kolsmes']; 
				if(isset($_REQUEST['kodpr'])) $kodpr = $_REQUEST['kodpr']; 
				if(isset($_REQUEST['tara'])) $tara = $_REQUEST['tara']; 
				for ($i=0;$i<8;$i++) 
				{
					if (isset($tara[$i])) $tara[$i] = "true"; else $tara[$i] = "false";
					if ($kodpr[$i]==NULL or $kodpr[$i]=="") $kodpr[$i]=0; else $kodpr[$i] = substr($kodpr[$i],0,5);
					settype($kodpr[$i], "integer");
				}
				$iw2 = ",{$k},'{$kolsmes[0]}','{$kolsmes[1]}','{$kolsmes[2]}','{$kolsmes[3]}','{$kolsmes[4]}','{$kolsmes[5]}','{$kolsmes[6]}','{$kolsmes[7]}',{$kodpr[0]},{$kodpr[1]},{$kodpr[2]},{$kodpr[3]},{$kodpr[4]},{$kodpr[5]},{$kodpr[6]},{$kodpr[7]},{$tm},'{$tara[0]}','{$tara[1]}','{$tara[2]}','{$tara[3]}','{$tara[4]}','{$tara[5]}','{$tara[6]}','{$tara[7]}'";
			} 
	
			if($artikyl == "") {$artikyl = "---//---";}
			if($cvet == "") {$cvet = "---//---";}
			if($pack == "") {$pack = "---//---";}
			if($blesk == "") {$blesk = "---//---";}
			$pack2 = explode(" ", $pack);
			$pricelitr = $priceyp/$pack2[0];
			$pricelitr = round($pricelitr, 2);
			
			if($priceyp == "") {$priceyp = "---//---"; $pricelitr = "---//---";}
			mysql_query("SET NAMES cp1251");
			mysql_query("INSERT INTO " . PREFIX . "_price (id,date,artikyl,cvet,pack,blesk,price_yp,price_litr,thisid$iw) VALUES(null,'{$date}','{$artikyl}','{$cvet}','{$pack}','{$blesk}','{$priceyp}','{$pricelitr}','{$thisid}'$iw2)");
			$checkk = mysql_query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$thisid}'");
			if(mysql_num_rows($checkk) == 0) {
			mysql_query("INSERT INTO " . PREFIX . "_price_g (id,date_g,name_g,proiz_g,artikyl_g,cvet_g,pack_g,blesk_g,price_yp_g,price_litr_g,cat) VALUES (null,'false','false','false','false','true','true','true','true','true','{$thisid}')");
			}
			mysql_query("SET NAMES cp1251");
		}
		
		if(isset($_REQUEST['suedit'])) {
			$iw="";
			$iw2="";
			$date = date("d.m.Y");
			if(isset($_REQUEST['artikyl'])) {$artikyl = $_REQUEST['artikyl'];// $kodph = substr(md5($artikyl),0,5);
			} else {$artikyl = "---//---";//$kodph = "-/ /-";
			}
			if(isset($_REQUEST['cvet'])) {$cvet = $_REQUEST['cvet'];} else {$cvet = "---//---";}
			if(isset($_REQUEST['pack'])) {$pack = $_REQUEST['pack'];} else {$pack = "---//---";}
			if(isset($_REQUEST['blesk'])) {$blesk = $_REQUEST['blesk'];} else {$blesk = "---//---";}
			if(isset($_REQUEST['priceyp'])) {$priceyp = $_REQUEST['priceyp'];} else {$priceyp = "---//---";}
			
			if(isset($_REQUEST['thisid'])) {$thisid = $_REQUEST['thisid'];} else {$thisid = "---//---";}
			
				$iw = ",k,kod1k,kod2k,kod3k,kod4k,kod5k,kod6k,kod7k,kod8k,kod1,kod2,kod3,kod4,kod5,kod6,kod7,kod8,tara1,tara2,tara3,tara4,tara5,tara6,tara7,tara8";
				$k = $_REQUEST['k'];
				if(isset($_REQUEST['kolsmes'])) $kolsmes = $_REQUEST['kolsmes']; 
				if(isset($_REQUEST['kodpr'])) $kodpr = $_REQUEST['kodpr']; 
				if(isset($_REQUEST['itid'])) $itid = $_REQUEST['itid']; 
				if(isset($_REQUEST['tara'])) $tara = $_REQUEST['tara']; 
				for ($i=0;$i<8;$i++) 
				{
				//	if ($tara[$i] == "true") $tara[$i] = "true"; else $tara[$i] = "false";
					if ($kodpr[$i]==NULL or $kodpr[$i]=="") $kodpr[$i]=0; else $kodpr[$i] = substr($kodpr[$i],0,5);
					settype($kodpr[$i], "integer");
				}
				//var_dump($tara);
				$iw2 = ",{$k},'{$kolsmes[0]}','{$kolsmes[1]}','{$kolsmes[2]}','{$kolsmes[3]}','{$kolsmes[4]}','{$kolsmes[5]}','{$kolsmes[6]}','{$kolsmes[7]}',{$kodpr[0]},{$kodpr[1]},{$kodpr[2]},{$kodpr[3]},{$kodpr[4]},{$kodpr[5]},{$kodpr[6]},{$kodpr[7]},'{$tara[0]}','{$tara[1]}','{$tara[2]}','{$tara[3]}','{$tara[4]}','{$tara[5]}','{$tara[6]}','{$tara[7]}'";
			 
	
			if($artikyl == "") {$artikyl = "---//---";}
			if($cvet == "") {$cvet = "---//---";}
			if($pack == "") {$pack = "---//---";}
			if($blesk == "") {$blesk = "---//---";}
			$pack2 = explode(" ", $pack);
			$pricelitr = $priceyp/$pack2[0];
			$pricelitr = round($pricelitr, 2);
			
			if($priceyp == "") {$priceyp = "---//---"; $pricelitr = "---//---";}
			mysql_query("SET NAMES cp1251");
			mysql_query("UPDATE dle_price SET date='$date', artikyl='$artikyl', pack='$pack', cvet='$cvet', blesk='$blesk', price_yp='$priceyp', price_litr='$pricelitr', kod1k='{$kolsmes[0]}', kod2k='{$kolsmes[1]}', kod3k='{$kolsmes[2]}', kod4k='{$kolsmes[3]}', kod5k='{$kolsmes[4]}', kod6k='{$kolsmes[5]}', kod7k='{$kolsmes[6]}', kod8k='{$kolsmes[7]}', kod1={$kodpr[0]}, kod2={$kodpr[1]}, kod3={$kodpr[2]}, kod4={$kodpr[3]}, kod5={$kodpr[4]}, kod6={$kodpr[5]}, kod7={$kodpr[6]}, kod8={$kodpr[7]}, tara1='{$tara[0]}', tara2='{$tara[1]}', tara3='{$tara[2]}', tara4='{$tara[3]}', tara5='{$tara[4]}', tara6='{$tara[5]}', tara7='{$tara[6]}', tara8='{$tara[7]}' WHERE id='$itid'");
			

		}
		
		
		
		$rrrr = mysql_query("SELECT * FROM dle_post");
		$mmmm = mysql_fetch_array($rrrr);
		
		if(!isset($_COOKIE['news_array'])) {
			if( ! $selected_news ) {
				msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
				exit;
			}
			$selected_news_array = $_REQUEST["selected_news"];
			$sd = implode(",", $selected_news_array);
			setcookie("news_array", $sd);
		} else {
			$sd = explode(",", $_COOKIE['news_array']);
			$selected_news_array = $sd;
			
		}
		
		$selected_news_array_length = count($selected_news_array);
			
			
			
	echoheader( "options", $lang['mass_cat'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"cat_select\" multiple";
	else $category_multiple = "";
	
	echo <<<HTML
<form action="{$PHP_SELF}" method="post">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['mass_cat_1']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100">
HTML;
		
		
		
		
		?>
        <style>
			.ht {background-color:#e4e4e4; padding:4px;}
			.ht3 {color:#0b5e92;}
			.to4ki {font-size:7px; color:#999999;}
			#price_actions p {line-height:0.3;}
		</style>
        <table width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
        	<tr>
            	<td valign="middle" align="center" class="ht" width="65">Дата</td>
                <td valign="middle" align="center" class="ht">Продукция</td>
                <td valign="middle" align="center" class="ht">Код-продукта</td>
                <td valign="middle" align="center" class="ht">Комплект</td>
                <td valign="middle" align="center" class="ht" width="88">Производитель</td>
                <td valign="middle" align="center" class="ht" width="100">Артикул</td>
                <td valign="middle" align="center" class="ht" width="56">Упаковка</td>
                <td valign="top" align="center" class="ht" width="68">База и цвет</td>
                <td valign="top" align="center" class="ht" width="60">Степень блеска</td>
                <td valign="top" align="center" class="ht" width="58">Стоимость уп</td>
                <td valign="top" align="center" class="ht" width="58">Стоимость за 1л</td>
                <td valign="middle" align="center" class="ht" width="50">Действие</td>
            </tr>
        
        
        <?php
		echo "<form action='' method='post'>";
		for($i=0; $i<$selected_news_array_length; $i++){
		
			$podcatit = "";
			$curr = $selected_news_array[$i];
			$selecter_result = mysql_query("SELECT * FROM dle_post WHERE id='" . $curr . "'");
			$selected_myrow  = mysql_fetch_array($selecter_result);
			
			$expl = explode(",", $selected_myrow['category']);
			
			$select_category = mysql_query("SELECT * FROM dle_category WHERE id='$expl[0]'");
			$myrow_category = mysql_fetch_array($select_category);
			
			$result_catprice = mysql_query("SELECT * FROM dle_price WHERE k = '1' and thisid='" . $curr . "' ORDER BY id ");
			$myrow_catproce = mysql_fetch_array($result_catprice);
			
			$result_catprice2 = mysql_query("SELECT * FROM dle_price_g WHERE cat='" . $curr . "'");
			$myrow_catproce2 = mysql_fetch_array($result_catprice2);
			
			
			if(mysql_num_rows($result_catprice) != 0) {
				
				if($myrow_catproce2["date_g"] == "true"){$date_g = "<input type='checkbox' name='date_g[$curr]' id='asd' value='true' checked>";} else {$date_g = "<input type='checkbox'  id='asd' name='date_g[$curr]' value='true'>";}
				if($myrow_catproce2["name_g"] == "true"){$name_g = "<input type='checkbox' name='name_g[$curr]' value='true' checked>";} else {$name_g = "<input type='checkbox' name='name_g[$curr]' value='true'>";}

				//if($myrow_catproce2["kodp_g"] == "true"){$kodp_g = "<input type='checkbox' name='kodp_g[$curr]' value='true' checked>";} else {$kodp_g = "<input type='checkbox' name='kodp_g[$curr]' value='true'>";}
				
				//$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true'>";
				//var_dump($myrow_catproce['komplekt']);
				//if($myrow_catproce['komplekt'] == "true"){$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true' checked>";} else {$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true'>";}
				
				if($myrow_catproce2["proiz_g"] == "true"){$proiz_g = "<input type='checkbox' name='proiz_g[$curr]' value='true' checked>";} else {$proiz_g = "<input type='checkbox' name='proiz_g[$curr]' value='true'>";}
				if($myrow_catproce2["artikyl_g"] == "true"){$artikyl_g = "<input type='checkbox' name='artikyl_g[$curr]' value='true' checked>";} else {$artikyl_g = "<input type='checkbox' name='artikyl_g[$curr]' value='true'>";}
				if($myrow_catproce2["cvet_g"] == "true"){$cvet_g = "<input type='checkbox' name='cvet_g[$curr]' value='true' checked>";} else {$cvet_g = "<input type='checkbox' name='cvet_g[$curr]' value='true'>";}
				if($myrow_catproce2["pack_g"] == "true"){$pack_g = "<input type='checkbox' name='pack_g[$curr]' value='true' checked>";} else {$pack_g = "<input type='checkbox' name='pack_g[$curr]' value='true'>";}
				if($myrow_catproce2["blesk_g"] == "true"){$blesk_g = "<input type='checkbox' name='blesk_g[$curr]' value='true' checked>";} else {$blesk_g = "<input type='checkbox' name='blesk_g[$curr]' value='true'>";}
				if($myrow_catproce2["price_yp_g"] == "true"){$price_yp_g = "<input type='checkbox' name='price_yp_g[$curr]' value='true' checked>";} else {$price_yp_g = "<input type='checkbox' name='price_yp_g[$curr]' value='true'>";}
				if($myrow_catproce2["price_litr_g"] == "true"){$price_litr_g = "<input type='checkbox' name='price_litr_g[$curr]' value='true' checked>";} else {$price_litr_g = "<input type='checkbox' name='price_litr_g[$curr]' value='true'>";}
					
					$podcatit = "
					<tr>
							<td valign='middle' align='center' class='ht2' width='65'>$date_g</td>
							<td valign='middle' align='center' class='ht3'>$name_g</td>
							<td valign='middle' align='center' class='ht3'></td>
							<td valign='middle' align='center' class='ht3'></td>
							<td valign='middle' align='center' class='ht2' width='88'>$proiz_g</td>
							<td valign='middle' align='center' class='ht2' width='100'>$artikyl_g</td>
							<td valign='middle' align='center' class='ht2' width='56'>$cvet_g</td>
							<td valign='middle' align='center' class='ht2' width='68'>$pack_g</td>
							<td valign='middle' align='center' class='ht2' width='60'>$blesk_g</td>
							<td valign='middle' align='center' class='ht2' width='58'>$price_yp_g</td>
							<td valign='middle' align='center' class='ht2' width='58'>$price_litr_g</td>
							<td valign='middle' align='center' class='ht2' width='50'>
								<div id='price_actions'>
									<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=add_this_komplekt&prid={$selected_myrow['id']}'>Комплект</a></p>
									<p><a href='admin.php?mod=massactions&user_hash={$dle_login_hash}&action=mass_edit_price&ac_k=komp' >Пересчитать</a></p>
								</div>
							</td>
							
						</tr>
						";
					do {
					    if($myrow_catproce['k'] == 1) $izmen = "<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=edit_this_komplekt&prid={$selected_myrow['id']}&itid={$myrow_catproce['id']}'>Изменить</a></p>";
						else $izmen = "";
						if($myrow_catproce['komplekt'] == "true"){$komplekt = "<input type='checkbox' name='komplekt[{$myrow_catproce[id]}]' value='true' checked>";} else {$komplekt = "<input type='checkbox' name='komplekt[{$myrow_catproce[id]}]' value='true'>";}
						$kod_p = $myrow_catproce[id];
						if(strlen($myrow_catproce[id]) < 5){
							if(strlen($myrow_catproce[id])==1) $kod_p = "0000".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==2) $kod_p = "000".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==3) $kod_p = "00".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==4) $kod_p = "0".$myrow_catproce[id];
						} 
					$podcatit .= "
						
						<tr>
							<td colspan='10' valign='middle' class='to4ki' align='center'>..........................................................................................................................................................................................................................</td>
						</tr>
						<tr>
							<td valign='middle' align='center' class='ht2' width='65'>{$myrow_catproce['date']}- </td>
							<td valign='middle' align='left' class='ht3'> {$selected_myrow['title']}</td>
							<td valign='middle' align='left' class='ht3'>{$kod_p}</td>
							<td valign='middle' align='left' class='ht3'>{$komplekt}</td>
							<td valign='middle' align='center' class='ht2' width='88'>{$myrow_category['name']}</td>
							<td valign='middle' align='center' class='ht2' width='47'>
								<input type='hidden' name='chid[]' value='{$myrow_catproce[id]}'>
								<input type='text' name='artikyl[]' style='width:100px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[artikyl]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='56'>
								<input type='text' name='pack[]' style='width:56px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[pack]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='68'>
								<input type='text' name='cvet[]' style='width:68px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[cvet]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='60'>
								<input type='text' name='blesk[]' style='width:60px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[blesk]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='58'>
								<input type='text' name='price_yp[]' style='width:58px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[price_yp]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='58'>
								{$myrow_catproce[price_litr]}
							</td>
							<td valign='middle' align='center' class='ht2' width='50'>
								<div id='price_actions'>
									<p><a href='admin.php?mod=massactions&user_hash={$dle_login_hash}&action=mass_edit_price&ac=del&itid={$myrow_catproce['id']}&bid={$selected_myrow['id']}'>Удалить</a></p>$izmen
								</div>
							</td>
							
						</tr>
						
					";
				} while($myrow_catproce = mysql_fetch_array($result_catprice));
			}
			
			printf("
			
			<tr>
            	
                <td valign='middle' align='left' class='ht3' colspan='11'><div align='center' style='font-weight:bold;'>%s</div></td>
				
			
                <td valign='middle' align='center' class='ht2' width='50'>
					<div id='price_actions'>
						<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=add_this_price&prid=%s'>Добавить</a></p>
					</div>
				</td>
				
            </tr>
			
			
			%s
			<tr>
				<td colspan='10' valign='middle' class='to4ki'>.........................................................................................................................................................................................................................................................................................................................</td>
			</tr>", $selected_myrow['title'], $selected_myrow['id'], $podcatit);
			
			
		}
		
		
		?>
        	</table>
            
            
        <?php	
	
	echo <<<HTML
		</td>
    </tr>
</table>
<input type=hidden name=user_hash value='{$dle_login_hash}'><input type='hidden' name='action' value='mass_edit_price_k'>
<input type='hidden' name='mod' value='massactions'><br><br>
<center><input type='submit' name='save_changes' class='edit' value='Сохранить' /></center><br>
</form>	
		
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
	exit();

}
elseif ( $action == "mass_edit_price_p" ) {

			
		 $ddb = mysql_connect ("localhost", "lakikras_laki", "Hli1wtwtobz");
		 mysql_select_db("lakikras_laki", $ddb);
		mysql_query("SET NAMES cp1251");
		if(isset($_REQUEST['ac'])) {
			mysql_query("DELETE FROM " . PREFIX . "_price WHERE id='" . $_REQUEST['itid'] . "'");
			
			$checkItId = mysql_query("SELECT * FROM " . PREFIX . "_price WHERE thisid='" . $_REQUEST['bid'] . "'");
			if(mysql_num_rows($checkItId) == 0) {
				mysql_query("DELETE FROM " . PREFIX . "_price_g WHERE cat='" . $_REQUEST['bid'] . "'");
			}
		}
		
		if(isset($_REQUEST['save_changes'])){
			$chidNum = count($_REQUEST['chid']);
			for($i=0; $i<$chidNum; $i++){
				$currDate = date("d.m.Y");
				$currId = $_REQUEST['chid'][$i];
				if($_REQUEST['komplekt'][$currId]=="true") $komplekt="true"; else $komplekt = "false";
				$currArtikyl = $_REQUEST['artikyl'][$i];
				$currPack = $_REQUEST['pack'][$i];
				$currCvet = $_REQUEST['cvet'][$i];
				$currBlesk = $_REQUEST['blesk'][$i];
				$currPriceYp = $_REQUEST['price_yp'][$i];
				$currPriceLitr = $_REQUEST['price_litr'][$i];
				$currPack2 = explode(" ", $currPack);
				$prLit = $currPriceYp/$currPack2[0];
				$prLitr = round($prLit, 2);
				if($currArtikyl == "") {$currArtikyl = "---//---";//$kod_p="-/ /-";
				}//else {$kod_p=substr(md5($currArtikyl),0,5);}
				if($currPack == "") {$currPack = "---//---";}
				if($currCvet == "") {$currCvet = "---//---";}
				if($currBlesk == "") {$currBlesk = "---//---";}
				if($currPriceYp == "") {$currPriceYp = "---//---"; $currPriceLitr = "---//---";}
				mysql_query("UPDATE dle_price SET date='$currDate', artikyl='$currArtikyl', pack='$currPack', cvet='$currCvet', blesk='$currBlesk', price_yp='$currPriceYp', price_litr='$prLitr', komplekt='$komplekt' WHERE id='$currId'");

			}
			
			for($i=0; $i<$chidNum; $i++){
				$currId = $_REQUEST['chid'][$i];
				$result_curr_id = mysql_query("SELECT thisid FROM " . PREFIX . "_price WHERE id='$currId'");
				$myrow_curr_id = mysql_fetch_array($result_curr_id);
				
				$current = array();
				$current = $_REQUEST['date_g'];
				
				$currentName = array();
				$currentName = $_REQUEST['name_g'];
				
				$currentProiz = array();
				$currentProiz = $_REQUEST['proiz_g'];
				
				$currentArtikyl = array();
				$currentArtikyl = $_REQUEST['artikyl_g'];
				
				$currentCvet = array();
				$currentCvet = $_REQUEST['cvet_g'];
				
				$currentPack = array();
				$currentPack = $_REQUEST['pack_g'];
				
				$currentBlesk = array();
				$currentBlesk = $_REQUEST['blesk_g'];
				
				$currentPriceYp = array();
				$currentPriceYp = $_REQUEST['price_yp_g'];
				
				$currentPriceLitr = array();
				$currentPriceLitr = $_REQUEST['price_litr_g'];
				
				
				$result_cat = mysql_query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$myrow_curr_id[thisid]}'");
				$myrow_cat = mysql_fetch_array($result_cat);
				do {
					if(isset($current[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET date_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET date_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentName[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET name_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET name_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentProiz[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET proiz_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET proiz_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentArtikyl[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET artikyl_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET artikyl_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentCvet[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET cvet_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET cvet_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPack[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET pack_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET pack_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentBlesk[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET blesk_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET blesk_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPriceYp[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_yp_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_yp_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
					
					if(isset($currentPriceLitr[$myrow_cat['cat']])) {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_litr_g='true' WHERE cat='{$myrow_cat[cat]}'");
					} else {
						mysql_query("UPDATE " . PREFIX . "_price_g SET price_litr_g='false' WHERE cat='{$myrow_cat[cat]}'");
					}
				} while($myrow_cat = mysql_fetch_array($result_cat));
				
			}
			
		}
		
		if(isset($_REQUEST['su'])) {
			$iw="";
			$iw2="";
			$date = date("d.m.Y");
			if(isset($_REQUEST['artikyl'])) {$artikyl = $_REQUEST['artikyl'];// $kodph = substr(md5($artikyl),0,5);
			} else {$artikyl = "---//---";//$kodph = "-/ /-";
			}
			if(isset($_REQUEST['cvet'])) {$cvet = $_REQUEST['cvet'];} else {$cvet = "---//---";}
			if(isset($_REQUEST['pack'])) {$pack = $_REQUEST['pack'];} else {$pack = "---//---";}
			if(isset($_REQUEST['blesk'])) {$blesk = $_REQUEST['blesk'];} else {$blesk = "---//---";}
			if(isset($_REQUEST['priceyp'])) {$priceyp = $_REQUEST['priceyp'];} else {$priceyp = "---//---";}
			
			if(isset($_REQUEST['thisid'])) {$thisid = $_REQUEST['thisid'];} else {$thisid = "---//---";}
			if(isset($_REQUEST['k'])) {
				$tm = time();
				$iw = ",k,kod1k,kod2k,kod3k,kod4k,kod5k,kod6k,kod7k,kod8k,kod1,kod2,kod3,kod4,kod5,kod6,kod7,kod8,tm,tara1,tara2,tara3,tara4,tara5,tara6,tara7,tara8";
				$k = $_REQUEST['k'];
				if(isset($_REQUEST['kolsmes'])) $kolsmes = $_REQUEST['kolsmes']; 
				if(isset($_REQUEST['kodpr'])) $kodpr = $_REQUEST['kodpr']; 
				if(isset($_REQUEST['tara'])) $tara = $_REQUEST['tara']; 
				for ($i=0;$i<8;$i++) 
				{
					if (isset($tara[$i])) $tara[$i] = "true"; else $tara[$i] = "false";
					if ($kodpr[$i]==NULL or $kodpr[$i]=="") $kodpr[$i]=0; else $kodpr[$i] = substr($kodpr[$i],0,5);
					settype($kodpr[$i], "integer");
				}
				$iw2 = ",{$k},'{$kolsmes[0]}','{$kolsmes[1]}','{$kolsmes[2]}','{$kolsmes[3]}','{$kolsmes[4]}','{$kolsmes[5]}','{$kolsmes[6]}','{$kolsmes[7]}',{$kodpr[0]},{$kodpr[1]},{$kodpr[2]},{$kodpr[3]},{$kodpr[4]},{$kodpr[5]},{$kodpr[6]},{$kodpr[7]},{$tm},'{$tara[0]}','{$tara[1]}','{$tara[2]}','{$tara[3]}','{$tara[4]}','{$tara[5]}','{$tara[6]}','{$tara[7]}'";
			} 
	
			if($artikyl == "") {$artikyl = "---//---";}
			if($cvet == "") {$cvet = "---//---";}
			if($pack == "") {$pack = "---//---";}
			if($blesk == "") {$blesk = "---//---";}
			$pack2 = explode(" ", $pack);
			$pricelitr = $priceyp/$pack2[0];
			$pricelitr = round($pricelitr, 2);
			
			if($priceyp == "") {$priceyp = "---//---"; $pricelitr = "---//---";}
			mysql_query("SET NAMES cp1251");
			mysql_query("INSERT INTO " . PREFIX . "_price (id,date,artikyl,cvet,pack,blesk,price_yp,price_litr,thisid$iw) VALUES(null,'{$date}','{$artikyl}','{$cvet}','{$pack}','{$blesk}','{$priceyp}','{$pricelitr}','{$thisid}'$iw2)");
			$checkk = mysql_query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$thisid}'");
			if(mysql_num_rows($checkk) == 0) {
			mysql_query("INSERT INTO " . PREFIX . "_price_g (id,date_g,name_g,proiz_g,artikyl_g,cvet_g,pack_g,blesk_g,price_yp_g,price_litr_g,cat) VALUES (null,'false','false','false','false','true','true','true','true','true','{$thisid}')");
			}
			mysql_query("SET NAMES cp1251");
		}
		
		if(isset($_REQUEST['suedit'])) {
			$iw="";
			$iw2="";
			$date = date("d.m.Y");
			if(isset($_REQUEST['artikyl'])) {$artikyl = $_REQUEST['artikyl'];// $kodph = substr(md5($artikyl),0,5);
			} else {$artikyl = "---//---";//$kodph = "-/ /-";
			}
			if(isset($_REQUEST['cvet'])) {$cvet = $_REQUEST['cvet'];} else {$cvet = "---//---";}
			if(isset($_REQUEST['pack'])) {$pack = $_REQUEST['pack'];} else {$pack = "---//---";}
			if(isset($_REQUEST['blesk'])) {$blesk = $_REQUEST['blesk'];} else {$blesk = "---//---";}
			if(isset($_REQUEST['priceyp'])) {$priceyp = $_REQUEST['priceyp'];} else {$priceyp = "---//---";}
			
			if(isset($_REQUEST['thisid'])) {$thisid = $_REQUEST['thisid'];} else {$thisid = "---//---";}
			
				$iw = ",k,kod1k,kod2k,kod3k,kod4k,kod5k,kod6k,kod7k,kod8k,kod1,kod2,kod3,kod4,kod5,kod6,kod7,kod8,tara1,tara2,tara3,tara4,tara5,tara6,tara7,tara8";
				$k = $_REQUEST['k'];
				if(isset($_REQUEST['kolsmes'])) $kolsmes = $_REQUEST['kolsmes']; 
				if(isset($_REQUEST['kodpr'])) $kodpr = $_REQUEST['kodpr']; 
				if(isset($_REQUEST['itid'])) $itid = $_REQUEST['itid']; 
				if(isset($_REQUEST['tara'])) $tara = $_REQUEST['tara']; 
				for ($i=0;$i<8;$i++) 
				{
				//	if ($tara[$i] == "true") $tara[$i] = "true"; else $tara[$i] = "false";
					if ($kodpr[$i]==NULL or $kodpr[$i]=="") $kodpr[$i]=0; else $kodpr[$i] = substr($kodpr[$i],0,5);
					settype($kodpr[$i], "integer");
				}
				//var_dump($tara);
				$iw2 = ",{$k},'{$kolsmes[0]}','{$kolsmes[1]}','{$kolsmes[2]}','{$kolsmes[3]}','{$kolsmes[4]}','{$kolsmes[5]}','{$kolsmes[6]}','{$kolsmes[7]}',{$kodpr[0]},{$kodpr[1]},{$kodpr[2]},{$kodpr[3]},{$kodpr[4]},{$kodpr[5]},{$kodpr[6]},{$kodpr[7]},'{$tara[0]}','{$tara[1]}','{$tara[2]}','{$tara[3]}','{$tara[4]}','{$tara[5]}','{$tara[6]}','{$tara[7]}'";
			 
	
			if($artikyl == "") {$artikyl = "---//---";}
			if($cvet == "") {$cvet = "---//---";}
			if($pack == "") {$pack = "---//---";}
			if($blesk == "") {$blesk = "---//---";}
			$pack2 = explode(" ", $pack);
			$pricelitr = $priceyp/$pack2[0];
			$pricelitr = round($pricelitr, 2);
			
			if($priceyp == "") {$priceyp = "---//---"; $pricelitr = "---//---";}
			mysql_query("SET NAMES cp1251");
			mysql_query("UPDATE dle_price SET date='$date', artikyl='$artikyl', pack='$pack', cvet='$cvet', blesk='$blesk', price_yp='$priceyp', price_litr='$pricelitr', kod1k='{$kolsmes[0]}', kod2k='{$kolsmes[1]}', kod3k='{$kolsmes[2]}', kod4k='{$kolsmes[3]}', kod5k='{$kolsmes[4]}', kod6k='{$kolsmes[5]}', kod7k='{$kolsmes[6]}', kod8k='{$kolsmes[7]}', kod1={$kodpr[0]}, kod2={$kodpr[1]}, kod3={$kodpr[2]}, kod4={$kodpr[3]}, kod5={$kodpr[4]}, kod6={$kodpr[5]}, kod7={$kodpr[6]}, kod8={$kodpr[7]}, tara1='{$tara[0]}', tara2='{$tara[1]}', tara3='{$tara[2]}', tara4='{$tara[3]}', tara5='{$tara[4]}', tara6='{$tara[5]}', tara7='{$tara[6]}', tara8='{$tara[7]}' WHERE id='$itid'");
			

		}
		
		
		
		$rrrr = mysql_query("SELECT * FROM dle_post");
		$mmmm = mysql_fetch_array($rrrr);
		
		if(!isset($_COOKIE['news_array'])) {
			if( ! $selected_news ) {
				msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
				exit;
			}
			$selected_news_array = $_REQUEST["selected_news"];
			$sd = implode(",", $selected_news_array);
			setcookie("news_array", $sd);
		} else {
			$sd = explode(",", $_COOKIE['news_array']);
			$selected_news_array = $sd;
			
		}
		
		$selected_news_array_length = count($selected_news_array);
			
			
			
	echoheader( "options", $lang['mass_cat'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"cat_select\" multiple";
	else $category_multiple = "";
	
	echo <<<HTML
<form action="{$PHP_SELF}" method="post">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['mass_cat_1']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100">
HTML;
		
		
		
		
		?>
        <style>
			.ht {background-color:#e4e4e4; padding:4px;}
			.ht3 {color:#0b5e92;}
			.to4ki {font-size:7px; color:#999999;}
			#price_actions p {line-height:0.3;}
		</style>
        <table width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
        	<tr>
            	<td valign="middle" align="center" class="ht" width="65">Дата</td>
                <td valign="middle" align="center" class="ht">Продукция</td>
                <td valign="middle" align="center" class="ht">Код-продукта</td>
                <td valign="middle" align="center" class="ht">Комплект</td>
                <td valign="middle" align="center" class="ht" width="88">Производитель</td>
                <td valign="middle" align="center" class="ht" width="100">Артикул</td>
                <td valign="middle" align="center" class="ht" width="56">Упаковка</td>
                <td valign="top" align="center" class="ht" width="68">База и цвет</td>
                <td valign="top" align="center" class="ht" width="60">Степень блеска</td>
                <td valign="top" align="center" class="ht" width="58">Стоимость уп</td>
                <td valign="top" align="center" class="ht" width="58">Стоимость за 1л</td>
                <td valign="middle" align="center" class="ht" width="50">Действие</td>
            </tr>
        
        
        <?php
		echo "<form action='' method='post'>";
		for($i=0; $i<$selected_news_array_length; $i++){
		
			$podcatit = "";
			$curr = $selected_news_array[$i];
			$selecter_result = mysql_query("SELECT * FROM dle_post WHERE id='" . $curr . "'");
			$selected_myrow  = mysql_fetch_array($selecter_result);
			
			$expl = explode(",", $selected_myrow['category']);
			
			$select_category = mysql_query("SELECT * FROM dle_category WHERE id='$expl[0]'");
			$myrow_category = mysql_fetch_array($select_category);
			
			$result_catprice = mysql_query("SELECT * FROM dle_price WHERE k is null and thisid='" . $curr . "' ORDER BY id ");
			$myrow_catproce = mysql_fetch_array($result_catprice);
			
			$result_catprice2 = mysql_query("SELECT * FROM dle_price_g WHERE cat='" . $curr . "'");
			$myrow_catproce2 = mysql_fetch_array($result_catprice2);
			
			
			if(mysql_num_rows($result_catprice) != 0) {
				
				if($myrow_catproce2["date_g"] == "true"){$date_g = "<input type='checkbox' name='date_g[$curr]' id='asd' value='true' checked>";} else {$date_g = "<input type='checkbox'  id='asd' name='date_g[$curr]' value='true'>";}
				if($myrow_catproce2["name_g"] == "true"){$name_g = "<input type='checkbox' name='name_g[$curr]' value='true' checked>";} else {$name_g = "<input type='checkbox' name='name_g[$curr]' value='true'>";}

				//if($myrow_catproce2["kodp_g"] == "true"){$kodp_g = "<input type='checkbox' name='kodp_g[$curr]' value='true' checked>";} else {$kodp_g = "<input type='checkbox' name='kodp_g[$curr]' value='true'>";}
				
				//$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true'>";
				//var_dump($myrow_catproce['komplekt']);
				//if($myrow_catproce['komplekt'] == "true"){$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true' checked>";} else {$komplekt = "<input type='checkbox' name='komplekt[$curr]' value='true'>";}
				
				if($myrow_catproce2["proiz_g"] == "true"){$proiz_g = "<input type='checkbox' name='proiz_g[$curr]' value='true' checked>";} else {$proiz_g = "<input type='checkbox' name='proiz_g[$curr]' value='true'>";}
				if($myrow_catproce2["artikyl_g"] == "true"){$artikyl_g = "<input type='checkbox' name='artikyl_g[$curr]' value='true' checked>";} else {$artikyl_g = "<input type='checkbox' name='artikyl_g[$curr]' value='true'>";}
				if($myrow_catproce2["cvet_g"] == "true"){$cvet_g = "<input type='checkbox' name='cvet_g[$curr]' value='true' checked>";} else {$cvet_g = "<input type='checkbox' name='cvet_g[$curr]' value='true'>";}
				if($myrow_catproce2["pack_g"] == "true"){$pack_g = "<input type='checkbox' name='pack_g[$curr]' value='true' checked>";} else {$pack_g = "<input type='checkbox' name='pack_g[$curr]' value='true'>";}
				if($myrow_catproce2["blesk_g"] == "true"){$blesk_g = "<input type='checkbox' name='blesk_g[$curr]' value='true' checked>";} else {$blesk_g = "<input type='checkbox' name='blesk_g[$curr]' value='true'>";}
				if($myrow_catproce2["price_yp_g"] == "true"){$price_yp_g = "<input type='checkbox' name='price_yp_g[$curr]' value='true' checked>";} else {$price_yp_g = "<input type='checkbox' name='price_yp_g[$curr]' value='true'>";}
				if($myrow_catproce2["price_litr_g"] == "true"){$price_litr_g = "<input type='checkbox' name='price_litr_g[$curr]' value='true' checked>";} else {$price_litr_g = "<input type='checkbox' name='price_litr_g[$curr]' value='true'>";}
					
					$podcatit = "
					<tr>
							<td valign='middle' align='center' class='ht2' width='65'>$date_g</td>
							<td valign='middle' align='center' class='ht3'>$name_g</td>
							<td valign='middle' align='center' class='ht3'></td>
							<td valign='middle' align='center' class='ht3'></td>
							<td valign='middle' align='center' class='ht2' width='88'>$proiz_g</td>
							<td valign='middle' align='center' class='ht2' width='100'>$artikyl_g</td>
							<td valign='middle' align='center' class='ht2' width='56'>$cvet_g</td>
							<td valign='middle' align='center' class='ht2' width='68'>$pack_g</td>
							<td valign='middle' align='center' class='ht2' width='60'>$blesk_g</td>
							<td valign='middle' align='center' class='ht2' width='58'>$price_yp_g</td>
							<td valign='middle' align='center' class='ht2' width='58'>$price_litr_g</td>
							<td valign='middle' align='center' class='ht2' width='50'>
								<div id='price_actions'>
									<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=add_this_komplekt&prid={$selected_myrow['id']}'>Комплект</a></p>
									<p><a href='admin.php?mod=massactions&user_hash={$dle_login_hash}&action=mass_edit_price&ac_k=komp' >Пересчитать</a></p>
								</div>
							</td>
							
						</tr>
						";
					do {
					    if($myrow_catproce['k'] == 1) $izmen = "<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=edit_this_komplekt&prid={$selected_myrow['id']}&itid={$myrow_catproce['id']}'>Изменить</a></p>";
						else $izmen = "";
						if($myrow_catproce['komplekt'] == "true"){$komplekt = "<input type='checkbox' name='komplekt[{$myrow_catproce[id]}]' value='true' checked>";} else {$komplekt = "<input type='checkbox' name='komplekt[{$myrow_catproce[id]}]' value='true'>";}
						$kod_p = $myrow_catproce[id];
						if(strlen($myrow_catproce[id]) < 5){
							if(strlen($myrow_catproce[id])==1) $kod_p = "0000".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==2) $kod_p = "000".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==3) $kod_p = "00".$myrow_catproce[id];
							if(strlen($myrow_catproce[id])==4) $kod_p = "0".$myrow_catproce[id];
						} 
					$podcatit .= "
						
						<tr>
							<td colspan='10' valign='middle' class='to4ki' align='center'>..........................................................................................................................................................................................................................</td>
						</tr>
						<tr>
							<td valign='middle' align='center' class='ht2' width='65'>{$myrow_catproce['date']}- </td>
							<td valign='middle' align='left' class='ht3'> {$selected_myrow['title']}</td>
							<td valign='middle' align='left' class='ht3'>{$kod_p}</td>
							<td valign='middle' align='left' class='ht3'>{$komplekt}</td>
							<td valign='middle' align='center' class='ht2' width='88'>{$myrow_category['name']}</td>
							<td valign='middle' align='center' class='ht2' width='47'>
								<input type='hidden' name='chid[]' value='{$myrow_catproce[id]}'>
								<input type='text' name='artikyl[]' style='width:100px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[artikyl]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='56'>
								<input type='text' name='pack[]' style='width:56px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[pack]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='68'>
								<input type='text' name='cvet[]' style='width:68px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[cvet]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='60'>
								<input type='text' name='blesk[]' style='width:60px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[blesk]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='58'>
								<input type='text' name='price_yp[]' style='width:58px; border:1px #45789e solid; font-size:12px; padding:0px; padding-bottom:1px;' value='{$myrow_catproce[price_yp]}'>
							</td>
							<td valign='middle' align='center' class='ht2' width='58'>
								{$myrow_catproce[price_litr]}
							</td>
							<td valign='middle' align='center' class='ht2' width='50'>
								<div id='price_actions'>
									<p><a href='admin.php?mod=massactions&user_hash={$dle_login_hash}&action=mass_edit_price&ac=del&itid={$myrow_catproce['id']}&bid={$selected_myrow['id']}'>Удалить</a></p>$izmen
								</div>
							</td>
							
						</tr>
						
					";
				} while($myrow_catproce = mysql_fetch_array($result_catprice));
			}
			
			printf("
			
			<tr>
            	
                <td valign='middle' align='left' class='ht3' colspan='11'><div align='center' style='font-weight:bold;'>%s</div></td>
				
			
                <td valign='middle' align='center' class='ht2' width='50'>
					<div id='price_actions'>
						<p><a href='admin.php?mod=editnews&user_hash={$dle_login_hash}&action=add_this_price&prid=%s'>Добавить</a></p>
					</div>
				</td>
				
            </tr>
			
			
			%s
			<tr>
				<td colspan='10' valign='middle' class='to4ki'>.........................................................................................................................................................................................................................................................................................................................</td>
			</tr>", $selected_myrow['title'], $selected_myrow['id'], $podcatit);
			
			
		}
		
		
		?>
        	</table>
            
            
        <?php	
	
	echo <<<HTML
		</td>
    </tr>
</table>
<input type=hidden name=user_hash value='{$dle_login_hash}'><input type='hidden' name='action' value='mass_edit_price_p'>
<input type='hidden' name='mod' value='massactions'><br><br>
<center><input type='submit' name='save_changes' class='edit' value='Сохранить' /></center><br>
</form>	
		
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
	exit();

}
/* -----------------------------------------------------~~~~~~~~~~
  Выбор символьного кода
 -----------------------------------------------------~~~~~~~~~~ */
elseif( $action == "mass_edit_symbol" ) {
	if( ! $selected_news ) {
	msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
}
	echoheader( "options", $lang['mass_cat'] );
	
	$count = count( $selected_news );
	
	echo <<<HTML
<form action="{$PHP_SELF}" method="post">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['catalog_url']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100" align="center">{$lang['catalog_url']} <input type="text" name="catalog_url" size="15"  class="edit" value="{$row['symbol']}">
HTML;
	
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">";
	}
	
	echo <<<HTML
<input type=hidden name=user_hash value="{$dle_login_hash}"><input type="hidden" name="action" value="do_mass_edit_symbol"><input type="hidden" name="mod" value="massactions">&nbsp;<input type="submit" value="{$lang['b_start']}" class="edit"></td>
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
HTML;
	
	echofooter();
	exit();
} 
/* -----------------------------------------------------~~~~~~~~~~
  смена категории
 -----------------------------------------------------~~~~~~~~~~ */
elseif( $action == "do_mass_move_to_cat" ) {
	if( ! $selected_news ) {
	msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
}
	$moved_articles = 0;
	
	$move_to_category = $db->safesql( implode( ',', $_REQUEST['move_to_category'] ) );
	
	foreach ( $selected_news as $id ) {
		$moved_articles ++;
		$id = intval( $id );
		
		$db->query( "UPDATE " . PREFIX . "_post set category='$move_to_category' WHERE id='$id'" );
	}
	
	clear_cache();
	
	if( count( $selected_news ) == $moved_articles ) {
		msg( "info", $lang['mass_cat_ok'], "$lang[mass_cat_ok] ($moved_articles)", $_SESSION['admin_referrer'] );
	} else {
		msg( "error", $lang['mass_cat_notok'], $lang['mass_cat_notok_1'], $_SESSION['admin_referrer'] );
	}
} 
/* -----------------------------------------------------~~~~~~~~~~
  смена символьного кода
 -----------------------------------------------------~~~~~~~~~~ */
elseif( $action == "do_mass_edit_symbol" ) {
	if( ! $selected_news ) {
	msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
}
	$edit_articles = 0;
	
	$catalog_url = $db->safesql( substr( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['catalog_url'] ) ) ) ), 0, 3 ) );
	
	foreach ( $selected_news as $id ) {
		$edit_articles ++;
		$id = intval( $id );
		
		$db->query( "UPDATE " . PREFIX . "_post SET symbol='$catalog_url' WHERE id='$id'" );
	}
	
	clear_cache();
	
	msg( "info", $lang['mass_symbol_ok'], $lang['mass_symbol_ok'] . " ($edit_articles)", $_SESSION['admin_referrer'] );
} 
/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  смена ключевых слов для облака тегов
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
elseif( $action == "do_mass_edit_cloud" ) {
	
	$edit_articles = 0;
	
	if( @preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $_POST['tags'] ) ) $_POST['tags'] = "";
	else $_POST['tags'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['tags'] ) ) ), ENT_QUOTES ) );

	if ( $_POST['tags'] ) {

		$temp_array = array();
		$tags_array = array();
		$temp_array = explode (",", $_POST['tags']);

		if (count($temp_array)) {

			foreach ( $temp_array as $value ) {
				if( trim($value) ) $tags_array[] = trim( $value );
			}

		}

		if ( count($tags_array) ) $_POST['tags'] = implode(", ", $tags_array); else $_POST['tags'] = "";

	}

	if ( $_POST['tags'] ) {
		foreach ( $selected_news as $id ) {
			$edit_articles ++;
			$id = intval( $id );

			$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '$id'" );
			$db->query( "UPDATE " . PREFIX . "_post SET tags='{$_POST['tags']}' WHERE id='$id'" );

			$tags = array ();
						
			$tags_array = explode( ",", $_POST['tags'] );
						
			foreach ( $tags_array as $value ) {
							
							$tags[] = "('" . $id . "', '" . trim( $value ) . "')";
			}
						
			$tags = implode( ", ", $tags );
			$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
		}
	}	
	clear_cache();
	
	msg( "info", $lang['mass_cloud_ok'], $lang['mass_cloud_ok'] . " ($edit_articles)", $_SESSION['admin_referrer'] );
} 
/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  Ничего не выбрано
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
else {
	
	msg( "info", $lang['mass_noact'], $lang['mass_noact_1'], $_SESSION['admin_referrer'] );

}
?>