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
 Файл: editnews.php
-----------------------------------------------------
 Назначение: редактирование новостей
=====================================================
*/
//if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
//	die( "Hacking attempt!" );
//}
 
if( ! $user_group[$member_id['user_group']]['admin_editnews'] ) {
	msg( "error", $lang['addnews_denied'], $lang['edit_denied'] );
}

if( isset( $_REQUEST['author'] ) ) $author = $db->safesql( trim( htmlspecialchars( $_REQUEST['author'] ) ) ); else $author = "";
if( isset( $_REQUEST['ifdelete'] ) ) $ifdelete = $_REQUEST['ifdelete']; else $ifdelete = "";
if( isset( $_REQUEST['news_fixed'] ) ) $news_fixed = $_REQUEST['news_fixed']; else $news_fixed = "";
if( isset( $_REQUEST['search_cat'] ) ) $search_cat = intval( $_REQUEST['search_cat'] ); else $search_cat = "";

include_once ENGINE_DIR . '/classes/parse.class.php';

$parse = new ParseFilter( Array (), Array (), 1, 1 );

$asd = new db;
$rrr = $asd->query("SELECT * FROM dle_razdeli ORDER BY id ASC");
$mmm = $asd->get_array($rrr);
$sss = $asd->num_rows($rrr);
$asd->query("SET NAMES cp1251");

$lm = new db;

$result_id = $lm->query("SELECT * FROM dle_post WHERE id='" . $_REQUEST['prid'] . "'");
$myrow_id = $lm->get_array($result_id);

$lmcat = new db;

$result_lm = $lmcat->query("SELECT * FROM dle_category WHERE id='" . $myrow_id['category'] . "'");
$myrow_lm = $lmcat->get_array($result_lm);



if( $action == "list" ) {
	if(!isset($_REQUEST['number']) && !isset($_REQUEST['tdo'])) {
	setcookie("news_array", "");
	$_SESSION['admin_referrer'] = $_SERVER['REQUEST_URI'];
	echoheader( "editnews", $lang['edit_head'] );
	
	$search_field = $db->safesql( trim( htmlspecialchars( stripslashes( urldecode( $_REQUEST['search_field'] ) ), ENT_QUOTES ) ) );
	$search_author = $db->safesql( trim( htmlspecialchars( stripslashes( urldecode( $_REQUEST['search_author'] ) ), ENT_QUOTES ) ) );
	$fromnewsdate = $db->safesql( trim( htmlspecialchars( stripslashes( $_REQUEST['fromnewsdate'] ), ENT_QUOTES ) ) );
	$tonewsdate = $db->safesql( trim( htmlspecialchars( stripslashes( $_REQUEST['tonewsdate'] ), ENT_QUOTES ) ) );
	
	$start_from = intval( $_REQUEST['start_from'] );
	$news_per_page = intval( $_REQUEST['news_per_page'] );
	$gopage = intval( $_REQUEST['gopage'] );
	
	$_REQUEST['news_status'] = intval( $_REQUEST['news_status'] );
	$news_status_sel = array ('0' => '', '1' => '', '2' => '' );
	$news_status_sel[$_REQUEST['news_status']] = 'selected="selected"';
	
	if( ! $news_per_page or $news_per_page < 1 ) {
		$news_per_page = 50;
	}
	if( $gopage ) $start_from = ($gopage - 1) * $news_per_page;
	
	if( $start_from < 0 ) $start_from = 0;
	
	$where = array ();
	
	if( ! $user_group[$member_id['user_group']]['allow_all_edit'] and $member_id['user_group'] != 1 ) {
		
		$where[] = "autor = '{$member_id['name']}'";
	
	}
	
	if( $search_field != "" ) {
		
		$where[] = "(short_story like '%$search_field%' OR title like '%$search_field%' OR full_story like '%$search_field%' OR xfields like '%$search_field%')";
	
	}
	
	if( $search_author != "" ) {
		
		$where[] = "autor like '$search_author%'";
	
	}
	
	if( $search_cat != "" ) {
		
		if ($search_cat == -1) $where[] = "category = '' OR category = '0'";
		else $where[] = "category regexp '[[:<:]]($search_cat)[[:>:]]'";
	
	}
	
	if( $fromnewsdate != "" ) {
		
		$where[] = "date >= '$fromnewsdate'";
	
	}
	
	if( $tonewsdate != "" ) {
		
		$where[] = "date <= '$tonewsdate'";
	
	}
	
	if( $_REQUEST['news_status'] == 1 ) $where[] = "approve = '1'";
	elseif( $_REQUEST['news_status'] == 2 ) $where[] = "approve = '0'";
	
	if( count( $where ) ) {
		
		$where = implode( " AND ", $where );
		$where = " WHERE " . $where;
	
	} else {
		$where = "";
	}
	
	$order_by = array ();
	
	if( $_REQUEST['search_order_f'] == "asc" or $_REQUEST['search_order_f'] == "desc" ) $search_order_f = $_REQUEST['search_order_f'];
	else $search_order_f = "";
	if( $_REQUEST['search_order_m'] == "asc" or $_REQUEST['search_order_m'] == "desc" ) $search_order_m = $_REQUEST['search_order_m'];
	else $search_order_m = "";
	if( $_REQUEST['search_order_d'] == "asc" or $_REQUEST['search_order_d'] == "desc" ) $search_order_d = $_REQUEST['search_order_d'];
	else $search_order_d = "";
	if( $_REQUEST['search_order_t'] == "asc" or $_REQUEST['search_order_t'] == "desc" ) $search_order_t = $_REQUEST['search_order_t'];
	else $search_order_t = "";
	
	if( ! empty( $search_order_f ) ) {
		$order_by[] = "fixed $search_order_f";
	}
	if( ! empty( $search_order_m ) ) {
		$order_by[] = "approve $search_order_m";
	}
	if( ! empty( $search_order_d ) ) {
		$order_by[] = "date $search_order_d";
	}
	if( ! empty( $search_order_t ) ) {
		$order_by[] = "title $search_order_t";
	}
	
	$order_by = implode( ", ", $order_by );
	if( ! $order_by ) $order_by = "fixed desc, approve asc, date desc";
	
	$search_order_fixed = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( isset( $_REQUEST['search_order_f'] ) ) {
		$search_order_fixed[$search_order_f] = 'selected';
	} else {
		$search_order_fixed['desc'] = 'selected';
	}
	$search_order_mod = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( isset( $_REQUEST['search_order_m'] ) ) {
		$search_order_mod[$search_order_m] = 'selected';
	} else {
		$search_order_mod['asc'] = 'selected';
	}
	$search_order_date = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( isset( $_REQUEST['search_order_d'] ) ) {
		$search_order_date[$search_order_d] = 'selected';
	} else {
		$search_order_date['desc'] = 'selected';
	}
	$search_order_title = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( ! empty( $search_order_t ) ) {
		$search_order_title[$search_order_t] = 'selected';
	} else {
		$search_order_title['----'] = 'selected';
	}
	
	$db->query( "SELECT id, date, title, category, autor, alt_name, comm_num, approve, fixed, news_read, flag FROM " . PREFIX . "_post" . $where . " ORDER BY " . $order_by . " LIMIT $start_from,$news_per_page" );
	
	// Prelist Entries
	$flag = 1;
	if( $start_from == "0" ) {
		$start_from = "";
	}
	$i = $start_from;
	$entries_showed = 0;
	
	$entries = "";
	
	while ( $row = $db->get_array() ) {
		
		$i ++;
		
		$itemdate = date( "d.m.Y", strtotime( $row['date'] ) );
		
		if( strlen( $row['title'] ) > 65 ) $title = substr( $row['title'], 0, 65 ) . " ...";
		else $title = $row['title'];
		
		$title = htmlspecialchars( stripslashes( $title ), ENT_QUOTES );
		$title = str_replace("&amp;","&", $title );
		
		$entries .= "<tr>

        <td class=\"list\" style=\"padding:4px;\">
        $itemdate - ";
		
		if( $row['fixed'] == '1' ) $entries .= "<font color=\"red\">$lang[edit_fix] </font> ";
		
		if( $row['comm_num'] > 0 ) {
			
			if( $config['allow_alt_url'] == "yes" ) {
				
				if( $row['flag'] and $config['seo_type'] ) {
					
					if( intval( $row['category'] ) and $config['seo_type'] == 2 ) {
						
						$full_link = $config['http_home_url'] . get_url( intval( $row['category'] ) ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
					
					} else {
						
						$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
					
					}
				
				} else {
					
					$full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime( $row['date'] ) ) . $row['alt_name'] . ".html";
				}
			
			} else {
				
				$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
			
			}
			
			$comm_link = "<a class=\"list\" onClick=\"return dropdownmenu(this, event, MenuBuild('" . $row['id'] . "', '{$full_link}'), '150px')\"href=\"{$full_link}\" target=\"_blank\">{$row['comm_num']}</a>";
		
		} else {
			$comm_link = $row['comm_num'];
		}
		
		$entries .= "<a title='$lang[edit_act]' class=\"list\" href=\"$PHP_SELF?mod=editnews&action=editnews&id=$row[0]\">$title</a>
        <td align=center>{$row['news_read']}</td><td align=center>" . $comm_link;
		
		$entries .= "</td><td style=\"text-align: center\">";
		
		if( $row['approve'] ) $erlaub = "$lang[edit_yes]";
		else $erlaub = "<font color=\"red\">$lang[edit_no]</font>";
		$entries .= $erlaub;
		
		$entries .= "<td align=\"center\">";
		
		if( ! $row['category'] ) $my_cat = "---";
		else {
			
			$my_cat = array ();
			$cat_list = explode( ',', $row['category'] );
			
			foreach ( $cat_list as $element ) {
				if( $element ) $my_cat[] = $cat[$element];
			}
			$my_cat = implode( ',<br />', $my_cat );
		}
		
		$entries .= "$my_cat<td class=\"list\"><a class=list href=\"?mod=editusers&action=list&search=yes&search_name=" . $row['autor'] . "\">" . $row['autor'] . "</a>

               <td align=center><input name=\"selected_news[]\" value=\"{$row['id']}\" type='checkbox'>

             </tr>
			<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=7></td></tr>
            ";
		$entries_showed ++;
		
		if( $i >= $news_per_page + $start_from ) {
			break;
		}
	}
	

	// End prelisting
	$result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post" . $where );
	
	$all_count_news = $result_count['count'];
	
	///////////////////////////////////////////
	// Options Bar
	$category_list = CategoryNewsSelection( $search_cat, 0, false );
	
	
	echo <<<HTML
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="engine/skins/calendar-blue.css" title="win2k-cold-1" />
<script type="text/javascript" src="engine/skins/calendar.js"></script>
<script type="text/javascript" src="engine/skins/calendar-en.js"></script>
<script type="text/javascript" src="engine/skins/calendar-setup.js"></script>
<script language="javascript">
    function search_submit(prm){
      document.optionsbar.start_from.value=prm;
      document.optionsbar.submit();
      return false;
    }
    function gopage_submit(prm){
      document.optionsbar.start_from.value= (prm - 1) * {$news_per_page};
      document.optionsbar.submit();
      return false;
    }
    </script>
<form action="?mod=editnews&amp;action=list" method="GET" name="optionsbar" id="optionsbar">
<input type="hidden" name="mod" value="editnews">
<input type="hidden" name="action" value="list">
<div style="padding-top:5px;padding-bottom:2px;display:none" name="advancedsearch" id="advancedsearch">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['edit_stat']} <b>{$entries_showed}</b> {$lang['edit_stat_1']} <b>{$all_count_news}</b></div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
     <tr>
		<td style="padding:5px;">{$lang['edit_search_news']}</td>
		<td style="padding-left:5px;"><input class="edit" name="search_field" value="{$search_field}" type="text" size="35"></td>
		<td style="padding-left:5px;">{$lang['search_by_author']}</td>
		<td style="padding-left:22px;"><input class="edit" name="search_author" value="{$search_author}" type="text" size="36"></td>

    </tr>
     <tr>
		<td style="padding:5px;">{$lang['edit_cat']}</td>
		<td style="padding-left:5px;"><select name="search_cat" ><option selected value="">$lang[edit_all]</option><option value="-1">$lang[cat_in_none]</option>{$category_list}</select></td>
		<td style="padding-left:5px;">{$lang['search_by_date']}</td>
		<td style="padding-left:5px;">{$lang['edit_fdate']} <input type="text" name="fromnewsdate" id="fromnewsdate" size="11" maxlength="16" class="edit" value="{$fromnewsdate}">
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_dnews" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>
<script type="text/javascript">
    Calendar.setup({
      inputField     :    "fromnewsdate",     // id of the input field
      ifFormat       :    "%Y-%m-%d",      // format of the input field
      button         :    "f_trigger_dnews",  // trigger for the calendar (button ID)
      align          :    "Br",           // alignment 
		  timeFormat     :    "24",
		  showsTime      :    false,
      singleClick    :    true
    });
</script> {$lang['edit_tdate']} <input type="text" name="tonewsdate" id="tonewsdate" size="11" maxlength="16" class="edit" value="{$tonewsdate}">
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_tnews" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>
<script type="text/javascript">
    Calendar.setup({
      inputField     :    "tonewsdate",     // id of the input field
      ifFormat       :    "%Y-%m-%d",      // format of the input field
      button         :    "f_trigger_tnews",  // trigger for the calendar (button ID)
      align          :    "Br",           // alignment 
		  timeFormat     :    "24",
		  showsTime      :    false,
      singleClick    :    true
    });
</script></td>

    </tr>
     <tr>
		<td style="padding:5px;">{$lang['search_by_status']}</td>
		<td style="padding-left:5px;"><select name="news_status" id="news_status">
								<option {$news_status_sel['0']} value="0">{$lang['news_status_all']}</option>
								<option {$news_status_sel['1']} value="1">{$lang['news_status_approve']}</option>
                                <option {$news_status_sel['2']} value="2">{$lang['news_status_mod']}</option>
							</select></td>
		<td style="padding-left:5px;">{$lang['edit_page']}</td>
		<td style="padding-left:22px;"><input class="edit" style="text-align: center" name="news_per_page" value="{$news_per_page}" type="text" size="36"></td>

    </tr>
    <tr>
        <td colspan="4"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td colspan="4">{$lang['news_order']}</td>
    </tr>
    <tr>
        <td style="padding:5px;">{$lang['news_order_fixed']}</td>
        <td style="padding:5px;">{$lang['edit_approve']}</td>
        <td style="padding:5px;">{$lang['search_by_date']}</td>
        <td style="padding:5px;">{$lang['edit_et']}</td>
    </tr>
    <tr>
        <td style="padding-left:2px;"><select name="search_order_f" id="search_order_f">
           <option {$search_order_fixed['----']} value="">{$lang['user_order_no']}</option>
           <option {$search_order_fixed['asc']} value="asc">{$lang['user_order_plus']}</option>
           <option {$search_order_fixed['desc']} value="desc">{$lang['user_order_minus']}</option>
            </select>
        </td>
        <td style="padding-left:2px;"><select name="search_order_m" id="search_order_m">
           <option {$search_order_mod['----']} value="">{$lang['user_order_no']}</option>
           <option {$search_order_mod['asc']} value="asc">{$lang['user_order_plus']}</option>
           <option {$search_order_mod['desc']} value="desc">{$lang['user_order_minus']}</option>
            </select>
        </td>
        <td style="padding-left:2px;"><select name="search_order_d" id="search_order_d">
           <option {$search_order_date['----']} value="">{$lang['user_order_no']}</option>
           <option {$search_order_date['asc']} value="asc">{$lang['user_order_plus']}</option>
           <option {$search_order_date['desc']} value="desc">{$lang['user_order_minus']}</option>
            </select>
        </td>
        <td style="padding-left:2px;" colspan="2"><select name="search_order_t" id="search_order_t">
           <option {$search_order_title['----']} value="">{$lang['user_order_no']}</option>
           <option {$search_order_title['asc']} value="asc">{$lang['user_order_plus']}</option>
           <option {$search_order_title['desc']} value="desc">{$lang['user_order_minus']}</option>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="4"><div class="hr_line"></div></td>
    </tr>
    <tr>
		<td style="padding:5px;">&nbsp;</td>
		<td colspan="3">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
<input onClick="javascript:search_submit(0); return(false);" class="edit" type="submit" value="{$lang['edit_act_1']}"></td>

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
</div>
</form>
HTML;
	// End Options Bar
	

	echo <<<JSCRIPT
<script language='JavaScript' type="text/javascript">
<!--
function ckeck_uncheck_all() {
    var frm = document.editnews;
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
-->
</script>
JSCRIPT;
	
	if( $entries_showed == 0 ) {
		
		echo <<<HTML
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['news_list']}</div></td>
        <td bgcolor="#EFEFEF" height="29" style="padding:5px;" align="right"><a href="javascript:ShowOrHide('advancedsearch');">{$lang['news_advanced_search']}</a></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td align="center" style="height:50px;">{$lang['edit_nonews']}</td>
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
</div>

HTML;
	
	} else {
		
		echo <<<HTML
<script type="text/javascript" src="engine/ajax/menu.js"></script>
<script language="javascript" type="text/javascript">
<!--
function cdelete(id){
    var agree=confirm("{$lang['db_confirmclear']}");
    if (agree)
    document.location='?mod=comments&user_hash={$dle_login_hash}&action=dodelete&id=' + id + '';
}
function MenuBuild( m_id, m_link ){

var menu=new Array()

menu[0]='<a href="' + m_link + '" target="_blank">{$lang['comm_view']}</a>';
menu[1]='<a href="?mod=comments&action=edit&id=' + m_id + '">{$lang['vote_edit']}</a>';
menu[2]='<a onClick="javascript:cdelete(' + m_id + '); return(false)" href="?mod=comments&user_hash={$dle_login_hash}&action=dodelete&id=' + m_id + '" >{$lang['comm_del']}</a>';

return menu;
}
//-->
</script>
<div style="margin-left:4px; margin-top:3px;" align="center">
<form action="">
<select name="thiscat">
<option value="">Выберите производителя</option>
HTML;

do {
echo <<<HTML

<option value="{$mmm[id]}">
	{$mmm[name]}
</option>

HTML;
} while($mmm = $asd->get_array($rrr));
echo <<<HTML
</select>
<input type=hidden name=user_hash value="{$dle_login_hash}">
<input type="hidden" name="action" value="display_this_cat">
<input type="hidden" name="mod" value="editnews">
<input type="submit" value="Выполнить" class="edit">
<a href="admin.php?mod=editnews&action=list&tdo=razdel" style="margin-left:25px;">Управление разделами</a>
</form>

<form action="" method="post" name="editnews">
</div>
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['news_list']}</div></td>
        <td bgcolor="#EFEFEF" height="29" style="padding:5px;" align="right"><a href="javascript:ShowOrHide('advancedsearch');">{$lang['news_advanced_search']}</a></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td>
	<table width=100%>
	<tr>
    <td>&nbsp;&nbsp;{$lang['edit_title']}
	<td width=80>&nbsp;{$lang['st_views']}&nbsp;
	<td width=80>&nbsp;{$lang['edit_com']}&nbsp;
    <td width=80 align="center">{$lang['edit_approve']}
    <td width=120 align="center">{$lang['edit_cl']}
    <td width=70 >{$lang['edit_autor']}
    <td width=10 align="center"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()">
	</tr>
	<tr><td colspan="7"><div class="hr_line"></div></td></tr>
	{$entries}
	<tr><td colspan="7"><div class="hr_line"></div></td></tr>
HTML;
		
		// pagination

		$npp_nav = "<div class=\"news_navigation\" style=\"margin-bottom:5px; margin-top:5px;\">";
		
		if( $start_from > 0 ) {
			$previous = $start_from - $news_per_page;
			$npp_nav .= "<a onClick=\"javascript:search_submit($previous); return(false);\" href=\"#\" title=\"{$lang['edit_prev']}\">&lt;&lt;</a> ";
		}
		
		if( $all_count_news > $news_per_page ) {
			
			$enpages_count = @ceil( $all_count_news / $news_per_page );
			$enpages_start_from = 0;
			$enpages = "";
			
			if( $enpages_count <= 10 ) {
				
				for($j = 1; $j <= $enpages_count; $j ++) {
					
					if( $enpages_start_from != $start_from ) {
						
						$enpages .= "<a onClick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$j</a> ";
					
					} else {
						
						$enpages .= "<span>$j</span> ";
					}
					
					$enpages_start_from += $news_per_page;
				}
				
				$npp_nav .= $enpages;
			
			} else {
				
				$start = 1;
				$end = 10;
				
				if( $start_from > 0 ) {
					
					if( ($start_from / $news_per_page) > 4 ) {
						
						$start = @ceil( $start_from / $news_per_page ) - 3;
						$end = $start + 9;
						
						if( $end > $enpages_count ) {
							$start = $enpages_count - 10;
							$end = $enpages_count - 1;
						}
						
						$enpages_start_from = ($start - 1) * $news_per_page;
					
					}
				
				}
				
				if( $start > 2 ) {
					
					$enpages .= "<a onClick=\"javascript:search_submit(0); return(false);\" href=\"#\">1</a> ... ";
				
				}
				
				for($j = $start; $j <= $end; $j ++) {
					
					if( $enpages_start_from != $start_from ) {
						
						$enpages .= "<a onClick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$j</a> ";
					
					} else {
						
						$enpages .= "<span>$j</span> ";
					}
					
					$enpages_start_from += $news_per_page;
				}
				
				$enpages_start_from = ($enpages_count - 1) * $news_per_page;
				$enpages .= "... <a onClick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$enpages_count</a> ";
				
				$npp_nav .= $enpages;
			
			}
		
		}
		
		if( $all_count_news > $i ) {
			$how_next = $all_count_news - $i;
			if( $how_next > $news_per_page ) {
				$how_next = $news_per_page;
			}
			$npp_nav .= "<a onClick=\"javascript:search_submit($i); return(false);\" href=\"#\" title=\"{$lang['edit_next']}\">&gt;&gt;</a>";
		}
		
		$npp_nav .= "</div>";
		
		// pagination
		

		if( $entries_showed != 0 ) {
			echo <<<HTML
<tr><td>{$npp_nav}</td>
<td colspan=5 align="right" valign="top"><div style="margin-bottom:5px; margin-top:5px;">
<select name=action>
<option value="">{$lang['edit_selact']}</option>
<option value="mass_edit_price">Изменить цену</option>
<option value="mass_edit_price_k">Изменить цену комплекта</option>
<option value="mass_edit_price_p">Изменить цену продукции</option>
<option value="mass_move_to_cat">{$lang['edit_selcat']}</option>
<option value="mass_edit_symbol">{$lang['edit_selsymbol']}</option>
<option value="mass_edit_cloud">{$lang['edit_cloud']}</option>
<option value="mass_date">{$lang['mass_edit_date']}</option>
<option value="mass_approve">{$lang['mass_edit_app']}</option>
<option value="mass_not_approve">{$lang['mass_edit_notapp']}</option>
<option value="mass_fixed">{$lang['mass_edit_fix']}</option>
<option value="mass_not_fixed">{$lang['mass_edit_notfix']}</option>
<option value="mass_comments">{$lang['mass_edit_comm']}</option>
<option value="mass_not_comments">{$lang['mass_edit_notcomm']}</option>
<option value="mass_rating">{$lang['mass_edit_rate']}</option>
<option value="mass_not_rating">{$lang['mass_edit_notrate']}</option>
<option value="mass_main">{$lang['mass_edit_main']}</option>
<option value="mass_not_main">{$lang['mass_edit_notmain']}</option>
<option value="mass_clear_count">{$lang['mass_clear_count']}</option>
<option value="mass_clear_rating">{$lang['mass_clear_rating']}</option>
<option value="mass_clear_cloud">{$lang['mass_clear_cloud']}</option>
<option value="mass_delete">{$lang['edit_seldel']}</option>
</select>
<input type=hidden name=mod value="massactions">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<input class="edit" type="submit" value="{$lang['b_start']}">
</div></th></tr>
HTML;
			
			if( $all_count_news > $news_per_page ) {
				
				echo <<<HTML
<tr><td colspan="6">
{$lang['edit_go_page']} <input class="edit" style="text-align: center" name="gopage" id="gopage" value="" type="text" size="3"> <input onClick="javascript:gopage_submit(document.getElementById('gopage').value); return(false);" class="edit" type="button" value=" ok ">
</td></tr>
HTML;
			
			}
		
		}
		
		echo <<<HTML
	</table>
</td>
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
</div>
HTML;
	
	}
	
	echofooter();
    } else if(isset($_REQUEST['tdo']) && $_REQUEST['tdo'] == 'razdel'){
		setcookie("news_array", "");
        $_SESSION['admin_referrer'] = $_SERVER['REQUEST_URI'];
        echoheader( "editnews", $lang['edit_head'] );
        	$db->query("DELETE FROM " . PREFIX . "_catt");
        	$resultCat = $db->query("SELECT category FROM " . PREFIX . "_post ORDER BY id ASC");
            $myrowCat  = $db->get_array($resultCat);
            $null = 1;
            
            do {
            	
                $explodeCat = explode(",", $myrowCat['category']);
                
                $resultNameCat = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='$explodeCat[0]'");
                $myrowNameCat  = $db->get_array($resultNameCat);
                
                $firstWord = explode(" ", $myrowNameCat['name']);
                $ifZap = explode(",",$firstWord[0]);
                if($ifZap[0] == "TIKKURILA") {
                	$current = $firstWord[0] . " " . $firstWord[1];
                } else if($ifZap[0] == "Teknos"){
                	$current = $firstWord[0] . " " . $firstWord[1] . " " . $firstWord[2];
                } else { 
	                $current = $ifZap[0];
                }
                
                $checkCat = $db->query("SELECT * FROM " . PREFIX . "_catt WHERE name LIKE '$current%'");
                $myrowCheckCat = $db->get_array($checkCat);
                
                $countCheckCat = $db->num_rows($checkCat);
                if($countCheckCat == 0){
                	$db->query("INSERT INTO " . PREFIX . "_catt(id,name) VALUES ('$null','$myrowNameCat[name]')");
                    $null++;
                }
                
                
            } while($myrowCat  = $db->get_array($resultCat));
           
            ?>
            	<style>
                	.frmtext {color:#0a3a6a; font-size:11px; font-weight:bold; margin-right:3px;}
					.frmtext2 {color:#0a3a6a; font-size:11px; margin-left:7px;}
					.frminput {width:300px; margin-left:7px; margin-top:7px; padding:0px;}
					.frminput2 {margin-left:7px; margin-top:7px;}
					.btn {border:1px #0a3a6a solid; background-color:#FFFFFF; font-size:12px;}
					.formnac input, .formnac select {margin-top:3px;}
					.formnac input {width:220px;}
                </style>
            <?php
			if(isset($_REQUEST['make']) && $_REQUEST['make'] == "add") {
				$nazvanie	  = $_REQUEST['nazvanie'];
				$proizvoditel = $_REQUEST['proizvoditel'];
				$perevod 	  = $_REQUEST['perevod'];
				$nacenka	  = $_REQUEST['nacenka'];
				
				if($nazvanie != "" && $proizvoditel != "" && $perevod != "" && $nacenka != ""){
					$check = $db->query("INSERT INTO " . PREFIX . "_razdeli(id,name,proizvoditel,togrn,nacenka) VALUES (null,'$nazvanie','$proizvoditel','$perevod','$nacenka')");
					if($check) {
						$reporting = "Операция выполнена успешно.";
					}
				} else {
					$reporting = "Не все поля заполнены.";
				}
			}
			if(isset($_REQUEST['make']) && $_REQUEST['make'] == "save") {
				$kyrs = array();
				$proc = array();
				
				$kyrs = $_REQUEST['togrn'];
				$proc = $_REQUEST['nac'];
				
				foreach($kyrs as $key=>$value) {
					$db->query("UPDATE " . PREFIX . "_razdeli SET togrn='{$value}' WHERE id='{$key}'");
				}
				
				foreach($proc as $key=>$value) {
					$db->query("UPDATE " . PREFIX . "_razdeli SET nacenka='{$value}' WHERE id='{$key}'");
				}
				
			}
			
			if(isset($_REQUEST['make']) && $_REQUEST['make'] == 'del') {
				$id = $_REQUEST['id'];
				$check = $db->query("DELETE FROM " . PREFIX . "_razdeli WHERE id='{$id}'");
				if($check) {
					$reporting = "Операция выполнена успешно.";
				}
			}
			$resultPr = $db->query("SELECT * FROM " . PREFIX . "_catt ORDER BY id ASC");
			$myrowPr = $db->get_array($resultPr);
            $forma= "<div>
						<p align='center' style='font-size:14px; font-weight:bold;'>Создание раздела новостей</p>
						<br>
							<p align='center' style='color:#FF0000'>$reporting</p>
						<br>
						<form action='' method='post' class='formnac'>
							<table align='center'>
								<tr>
									<td align='right'><p class='frmtext'>Название раздела:</p></td>
									<td><input type='text' name='nazvanie' value=''></td>
								</tr>
								<tr>
									<td align='right'><p class='frmtext'>Производитель:</p></td>
									<td>
										<select name='proizvoditel' class='frmtext2' style='margin-left:0px;'>
										<option value=''>Выберите производителя</option>
										";
										do {
											$resultCheck = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel='{$myrowPr[name]}'");
											$countCheck = $db->num_rows($resultCheck);
											if($countCheck == 0) {
												$forma .= "<option value='$myrowPr[name]' class='frmtext2'>$myrowPr[name]</option>";
											}
										} while($myrowPr = $db->get_array($resultPr));
										$forma .= "
										</select>
									</td>
								</tr>
								<tr>
									<td align='right'><p class='frmtext'>Перевод в грн:</p></td>
									<td><input type='text' name='perevod' value=''><span style='color:#999999;'>(Н-р: 8.04)</span></td>
								</tr>
								<tr>
									<td align='right'><p class='frmtext'>Торговая наценка:</p></td>
									<td><input type='text' name='nacenka' value=''><span style='color:#999999;'>(Число в %)</span></td>
								</tr>
								<tr>
									<td colspan='2' align='center'>
										<input type=hidden name=mod value='editnews'>
										<input type=hidden name=action value='list'>
										<input type=hidden name=tdo value='razdel'>
										<input type=hidden name=make value='add'>
										
										<input type='hidden' name='user_hash' value='$dle_login_hash' />
										<input class='edit' type='submit' value='Создать' style='width:100px;'>
									</td>
								</tr>
							</table>
						
						</form>
					  </div>";
           
            echo $forma;
            
			$razdIsn = $db->query("SELECT * FROM " . PREFIX . "_razdeli ORDER BY id ASC");
			$myrowIsn = $db->get_array($razdIsn);
			$countIsn = $db->num_rows($razdIsn);
			if($countIsn != 0) {
			
				$table = "<br><br><br>
					<style>
						.ht {background-color:#e4e4e4; padding:4px;}
						.ht3 {color:#0b5e92;}
						.to4ki {font-size:7px; color:#999999;}
						#price_actions p {line-height:0.3;}
					</style>
					<form action='' method='post'>
					<table width='100%' align='left' cellpadding='0' cellspacing='0' border='0'>
						<tr>
							<td valign='middle' align='center' class='ht'>Название</td>
							<td valign='middle' align='center' class='ht' width='300'>Производитель</td>
							<td valign='middle' align='center' class='ht' width='100'>Перевод в грн</td>
							<td valign='middle' align='center' class='ht' width='100'>Наценка</td>
							<td valign='middle' align='center' class='ht' width='100'>Действие</td>
							
						</tr>";
						
				do {
				$table .= "
							<tr>
								<td valign='middle' align='left' class='ht2'><p style='margin-left:10px;'>$myrowIsn[name]</p></td>
								<td valign='middle' align='left' class='ht3' width='300'>$myrowIsn[proizvoditel]</td>
								<td valign='middle' align='center' class='ht2' width='100'>
									<input type='text' name='togrn[{$myrowIsn[id]}]' value='$myrowIsn[togrn]' size='8'>
								</td>
								<td valign='middle' align='center' class='ht2' width='100'>
									<input type='text' name='nac[{$myrowIsn[id]}]' value='$myrowIsn[nacenka]' size='8'>
								</td>
								<td valign='middle' align='center' class='ht2' width='100'>
									<a href='admin.php?mod=editnews&action=list&tdo=razdel&make=del&user_hash=$dle_login_hash&id=$myrowIsn[id]'>Удалить</a>
								</td>
								
							</tr>
							
				";
				} while($myrowIsn = $db->get_array($razdIsn));
				$table .= "
				
					<tr>
						<td colspan='5' align='center'>
						<br><br>
							<input type=hidden name=mod value='editnews'>
							<input type=hidden name=action value='list'>
							<input type=hidden name=tdo value='razdel'>
							<input type=hidden name=make value='save'>
							
							<input type='hidden' name='user_hash' value='$dle_login_hash' />
							<input class='edit' type='submit' value='Сохранить' style='width:100px; margin-bottom:20px;'>
						</td>
					</tr>
					</form>
					</table>
				

				";
				
				echo $table;
			}
			
			if(isset($_REQUEST['addHiddenBrand'])) {
				$add = $_REQUEST['addHiddenBrand'];
				$db->query("INSERT INTO dle_catt_hidden SET catID='" . $_REQUEST['brand'] . "'");
			}
			
			if(isset($_REQUEST['hidid'])) {
				$db->query("DELETE FROM dle_catt_hidden WHERE id='" . $_REQUEST['hidid'] . "'");
			}
			echo "
				
				<p align='center' style='font-size:14px; font-weight:bold;'>Скрытие раздела</p>
				<br>
				<form action='' method='post'>
					<table width='300px' align='center' cellpadding='0' cellspacing='0' border='0'>
						<tr>
							<td><p class='frmtext'>Производитель:</p></td>
							<td>
								<select name='brand' class='frmtext2' style='margin-left:0px;'>
										<option value=''>Выберите производителя</option>
										";
										$res = $db->query("SELECT * FROM dle_catt WHERE NOT id IN(SELECT catID FROM dle_catt_hidden) ORDER BY id"); 
										while($myr = $db->get_array($res)) {
											echo "<option value='$myr[id]' class='frmtext2'>$myr[name]</option>";
										}
						  echo "
								</select>
							</td>
						</tr>
						<tr>
							<td colspan='2' align='center'>
								<input type='submit' name='addHiddenBrand' class='edit' style='margin-top:3px; margin-bottom:15px;' value='Добавить'>
							</td>
						</tr>
					</table>
				</form>
				";
				$res = $db->query("SELECT * FROM dle_catt_hidden ORDER BY id");
				if($db->num_rows($res) != 0) {
				echo "
				<form action='' method='post'>
				<table width='100%' align='left' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td valign='middle' align='center' class='ht' width='300'>Производитель</td>
						<td valign='middle' align='center' class='ht' width='100'>Исключение</td>
						<td valign='middle' align='center' class='ht' width='100'>Пользователь</td>
						<td valign='middle' align='center' class='ht' width='100'>Действие</td>
					</tr>
					";
					if(isset($_REQUEST['deluser']) && isset($_REQUEST['catID']) && isset($_REQUEST['user'])) {
						$catID = $_REQUEST['catID'];
						$user = $_REQUEST['user'];
						$reshid = $db->query("SELECT * FROM dle_catt_hidden WHERE catID='$catID'");
						$myrhid = $db->get_array($reshid);
						$exp = explode(",",$myrhid['users']);
						$temp = array();
						foreach($exp as $k=>$v) {
							if($v != $user) {
								$temp[] = $v;
							}
						}
						$count = count($temp);
						$cc = "";
						$c = 1;
						foreach($temp as $k => $v) {
							$cc .= "$v";
							if($c<$count) {
								$cc .= ",";
							}
							$c++;
						}
						
						$db->query("UPDATE dle_catt_hidden SET users='$cc' WHERE catID='$catID'");
					}
					if(isset($_REQUEST['saveBrands'])) {
						$acception = $_REQUEST['acception'];
						foreach($acception as $key => $value){
							if($value != "") {
								$check = $db->query("SELECT users FROM dle_catt_hidden WHERE catID='$key'");
								$ins = $db->get_array($check);
								if($ins['users'] != "") {
									$db->query("UPDATE dle_catt_hidden SET users='{$ins['users']},$value' WHERE catID='$key'");
								} else {
									$db->query("UPDATE dle_catt_hidden SET users='$value' WHERE catID='$key'");
								}
							}
						}
					}
					
					while ($myr = $db->get_array($res)){
					$result = $db->query("SELECT * FROM dle_catt WHERE id='" . $myr['catID'] . "'");
					$myrow = $db->get_array($result);
					$us = $db->query("SELECT users FROM dle_catt_hidden WHERE catID={$myr['catID']}");
					$mus = $db->get_array($us);
					if($mus['users'] != "") {
						$explode = explode(",", $mus['users']);
						$count = count($explode);
						$cc = "";
						$c = 1;
						foreach($explode as $k => $v) {
							$cc .= "'$v'";
							if($c<$count) {
								$cc .= ",";
							}
							$c++;
						}
						
						$cu = "WHERE NOT name IN($cc)";
					} else {
						$cu = "";
					}
					
					
					$resUsers = $db->Query("SELECT * FROM dle_users $cu ORDER BY user_id ASC");
					echo "
						<tr>
							<td align='left' align='left' class='ht3'>{$myrow['name']}</td>
							<td valign='middle' align='left' class='ht2'>
							";
							if($mus['users'] != "") {
								$c = 1;
								foreach($explode as $k => $v) {
									echo "<a href='/admin.php?mod=editnews&action=list&tdo=razdel&deluser&catID={$myr['catID']}&user=$v' onClick=\"asd=confirm('Вы действительно хотите убрать этого пользователя');if(asd){return true;}else{return false;}\">$v</a>";
									if($c<$count) {
										echo ", ";
									}
									$c++;
								}
							} else {
								echo "---//---";
							}
					   echo "
							</td>
							<td align='center' align='left' class='ht2'>
							<select name='acception[{$myr['catID']}]' style='margin-top:2px; margin-bottom:2px;'>
							<option value=''>Выберите пользователя</option>
							";
							while($users = $db->get_array($resUsers)) {
								echo "
									<option value='{$users['name']}'>{$users['name']}</option>
								";
							}
							echo "
							</select>
							</td>
							<td align='center' align='left' class='ht2'>
							<a href='/admin.php?mod=editnews&action=list&tdo=razdel&hidid={$myr['id']}'>Удалить</a>
							</td>
						</tr>
					";
					}
			echo "
				<tr>
					<td align='center' colspan='4'>
						<input type='submit' name='saveBrands' class='edit' value='Сохранить'>
						<br><br><br>
					</td>
				</tr>
				</table>
				</form>
			";
			
			$repc = "";
			if(isset($_REQUEST['savePereocenka'])) {
				if (!empty($_FILES["cena"])){
					if ($_FILES['cena']['type'] == 'application/vnd.ms-excel'){
						move_uploaded_file($_FILES["cena"]["tmp_name"], "files/excelprices.xls");
						require_once "engine/classes/PHPExcel/Reader/Excel5.php";

						$excelFileName = "files/excelprices.xls";
						
						$objReader = new PHPExcel_Reader_Excel5();
						$objPHPExcel = $objReader->load( $excelFileName );
						$objWorksheet = $objPHPExcel->getActiveSheet();
						
						foreach ($objWorksheet->getRowIterator() as $row) {
						
						  $cellIterator = $row->getCellIterator();
						  $cellIterator->setIterateOnlyExistingCells(false);
						  $n = 1;
						  foreach ($cellIterator as $cell) {
						  	if($n == 1) {
								if($cell->getValue() != "") {
									$col1 = trim($cell->getValue());
								}
								$n++;
							} else {
								if($cell->getValue() != "") {
									$col2 = trim($cell->getValue());
								}
							}
						  }
						  
						  $col2 = round($col2, 2);
						  $col2 = number_format($col2, 2);
						  $col2 = str_replace(",","",$col2);
						  $res = $db->query("SELECT * FROM " . PREFIX . "_price WHERE artikyl='$col1'");
						  while($myr = $db->get_array($res)) {
						  	$ex = explode(" ", $myr['pack']);
							$prl = round($col2/$ex[0], 2);
							$prl = number_format($prl, 2);
							$db->query("UPDATE " . PREFIX . "_price SET price_yp='$col2', price_litr='$prl' WHERE id='{$myr['id']}'");
						  }
						
						}
						$repc = "Переоценка выполнена";
					} else {
						$repc = "Вы пытаетесь добавить файл не соответствующего типа.";
					}
					
				}
			}
			
			echo "
				<div style='text-align:center;'>
					<p style='color:#FF0000;'>$repc</p>
					<form action='' method='post' enctype='multipart/form-data'>
						<span style='font-size:12px; font-weight:bold;'>Переоценка:</span>
						<input type='file' name='cena'> 
						<input type='submit' name='savePereocenka' class='edit' value='Сохранить'>
					</form>
				</div>
			";
			}
           
        echofooter();
    }

} 

// ********************************************************************************
// Показ новости и редактирование
// ********************************************************************************
elseif( $action == "editnews" ) {
	
	$id = intval( $_GET['id'] );
	$row = $db->super_query( "SELECT * FROM " . PREFIX . "_post where id = '$id'" );
	
	$found = FALSE;
	
	if( $id == $row['id'] ) $found = TRUE;
	if( ! $found ) {
		msg( "error", $lang['cat_error'], $lang['edit_nonews'] );
	}
	
	$cat_list = explode( ',', $row['category'] );
	
	$have_perm = 0;
	
	if( $user_group[$member_id['user_group']]['allow_all_edit'] ) {
		$have_perm = 1;
		
		$allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_add'] );
		
		foreach ( $cat_list as $selected ) {
			if( $allow_list[0] != "all" and ! in_array( $selected, $allow_list ) ) $have_perm = 0;
		}
	}
	
	if( $user_group[$member_id['user_group']]['allow_edit'] and $row['autor'] == $member_id['name'] ) {
		$have_perm = 1;
	}
	
	if( ($member_id['user_group'] == 1) ) {
		$have_perm = 1;
	}
	
	if( ! $have_perm ) {
		msg( "error", $lang['addnews_denied'], $lang['edit_denied'], "$PHP_SELF?mod=editnews&action=list" );
	}
	
	$row['title'] = $parse->decodeBBCodes( $row['title'], false );
	$row['title'] = str_replace("&amp;","&", $row['title'] );
	$row['descr'] = $parse->decodeBBCodes( $row['descr'], false );
	$row['keywords'] = $parse->decodeBBCodes( $row['keywords'], false );
	$row['expires'] = ($row['expires'] == "0000-00-00") ? "" : $row['expires'];
	$row['metatitle'] = stripslashes( $row['metatitle'] );
	
	if( $row['allow_br'] != '1' or $config['allow_admin_wysiwyg'] == "yes" ) {
		$row['short_story'] = $parse->decodeBBCodes( $row['short_story'], true, $config['allow_admin_wysiwyg'] );
		$row['full_story'] = $parse->decodeBBCodes( $row['full_story'], true, $config['allow_admin_wysiwyg'] );
	} else {
		$row['short_story'] = $parse->decodeBBCodes( $row['short_story'], false );
		$row['full_story'] = $parse->decodeBBCodes( $row['full_story'], false );
	}
	
	$access = permload( $row['access'] );
	
	if( $row['votes'] ) {
		$poll = $db->super_query( "SELECT * FROM " . PREFIX . "_poll where news_id = '{$row['id']}'" );
		$poll['title'] = $parse->decodeBBCodes( $poll['title'], false );
		$poll['frage'] = $parse->decodeBBCodes( $poll['frage'], false );
		$poll['body'] = $parse->decodeBBCodes( $poll['body'], false );
		$poll['multiple'] = $poll['multiple'] ? "checked" : "";
	}

	$expires = $db->super_query( "SELECT * FROM " . PREFIX . "_post_log where news_id = '{$row['id']}'" );

	if ( $expires['expires'] ) $expires['expires'] = date("Y-m-d", $expires['expires']);

	$js_array[] = "engine/skins/calendar.js";
	$js_array[] = "engine/skins/tabs.js";
	$js_array[] = "engine/skins/autocomplete.js";

	echoheader( "editnews", $lang['edit_head'] );

	if ( !$user_group[$member_id['user_group']]['allow_html'] ) $config['allow_admin_wysiwyg'] = "no";
	
	// Доп. поля
	$xfieldsaction = "categoryfilter";
	include (ENGINE_DIR . '/inc/xfields.php');
	echo $categoryfilter;
	

	echo "
    <SCRIPT LANGUAGE=\"JavaScript\">
    function preview(){";
	
	if( $config['allow_admin_wysiwyg'] == "yes" ) {
		echo "document.getElementById('short_story').value = $('#short_story').html();
	document.getElementById('full_story').value = $('#full_story').html();";
	}
	
	echo "if(document.addnews.short_story.value == '' || document.addnews.title.value == ''){ DLEalert('$lang[addnews_alert]', '$lang[p_info]'); }
    else{
        dd=window.open('','prv','height=400,width=750,left=0,top=0,resizable=1,scrollbars=1')
        document.addnews.mod.value='preview';document.addnews.target='prv'
        document.addnews.submit();dd.focus()
        setTimeout(\"document.addnews.mod.value='editnews';document.addnews.target='_self'\",500)
    }
    }
    function sendNotice( id ){
		var b = {};

		b[dle_act_lang[3]] = function() { 
			$(this).dialog('close');						
		};

		b['{$lang['p_send']}'] = function() { 
			if ( $('#dle-promt-text').val().length < 1) {
				$('#dle-promt-text').addClass('ui-state-error');
			} else {
				var response = $('#dle-promt-text').val()
				$(this).dialog('close');
				$('#dlepopup').remove();
				$.post('engine/ajax/message.php', { id: id,  text: response, allowdelete: \"no\" },
					function(data){
						if (data == 'ok') { DLEalert('{$lang['p_send_ok']}', '{$lang['p_info']}'); }
					});
	
			}				
		};

		$('#dlepopup').remove();
					
		$('body').append(\"<div id='dlepopup' title='{$lang['p_title']}' style='display:none'><br />{$lang['p_text']}<br /><br /><textarea name='dle-promt-text' id='dle-promt-text' class='ui-widget-content ui-corner-all' style='width:97%;height:100px; padding: .4em;'></textarea></div>\");
					
		$('#dlepopup').dialog({
			autoOpen: true,
			width: 500,
			buttons: b
		});

	}

    function confirmDelete(url, id){

		var b = {};
	
		b[dle_act_lang[1]] = function() { 
						$(this).dialog(\"close\");						
				    };

		b['{$lang['p_message']}'] = function() { 
						$(this).dialog(\"close\");

						var bt = {};
					
						bt[dle_act_lang[3]] = function() { 
										$(this).dialog('close');						
								    };
					
						bt['{$lang['p_send']}'] = function() { 
										if ( $('#dle-promt-text').val().length < 1) {
											 $('#dle-promt-text').addClass('ui-state-error');
										} else {
											var response = $('#dle-promt-text').val()
											$(this).dialog('close');
											$('#dlepopup').remove();
											$.post('engine/ajax/message.php', { id: id,  text: response },
											  function(data){
											    if (data == 'ok') { document.location=url; } else { DLEalert('{$lang['p_not_send']}', '{$lang['p_info']}'); }
										  });
	
										}				
									};
					
						$('#dlepopup').remove();
					
						$('body').append(\"<div id='dlepopup' title='{$lang['p_title']}' style='display:none'><br />{$lang['p_text']}<br /><br /><textarea name='dle-promt-text' id='dle-promt-text' class='ui-widget-content ui-corner-all' style='width:97%;height:100px; padding: .4em;'></textarea></div>\");
					
						$('#dlepopup').dialog({
							autoOpen: true,
							width: 500,
							buttons: bt
						});
					
				    };
	
		b[dle_act_lang[0]] = function() { 
						$(this).dialog(\"close\");
						document.location=url;					
					};
	
		$(\"#dlepopup\").remove();
	
		$(\"body\").append(\"<div id='dlepopup' title='{$lang['p_confirm']}' style='display:none'><br /><div id='dlepopupmessage'>{$lang['edit_cdel']}</div></div>\");
	
		$('#dlepopup').dialog({
			autoOpen: true,
			width: 500,
			buttons: b
		});


    }

    function CheckStatus(Form){
		if(Form.allow_date.checked) {
		Form.allow_now.disabled = true;
		Form.allow_now.checked = false;
		} else {
		Form.allow_now.disabled = false;
		}
    }

	function auto_keywords ( key )
	{
		var wysiwyg = '{$config['allow_admin_wysiwyg']}';

		if (wysiwyg == \"yes\") {
			var short_txt = $('#short_story').html();
			var full_txt = $('#full_story').html();
		} else {
			var short_txt = document.getElementById('short_story').value;
			var full_txt = document.getElementById('full_story').value;
		}

		ShowLoading('');

		$.post(\"engine/ajax/keywords.php\", { short_txt: short_txt, full_txt: full_txt, key: key }, function(data){
	
			HideLoading('');
	
			if (key == 1) { $('#autodescr').val(data); }
			else { $('#keywords').val(data); }
	
		});

		return false;
	}

	function find_relates ( )
	{
		var title = document.getElementById('title').value;

		ShowLoading('');

		$.post('engine/ajax/find_relates.php', { title: title, id: '{$row['id']}' }, function(data){
	
			HideLoading('');
	
			$('#related_news').html(data);
	
		});

		return false;

	};

	$(function(){
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}
 
		$( '#tags' ).autocomplete({
			source: function( request, response ) {
				$.getJSON( 'engine/ajax/find_tags.php', {
					term: extractLast( request.term )
				}, response );
			},
			search: function() {
				var term = extractLast( this.value );
				if ( term.length < 3 ) {
					return false;
				}
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				terms.pop();
				terms.push( ui.item.value );
				terms.push( '' );
				this.value = terms.join( ', ' );
				return false;
			}
		});

	});
    </SCRIPT>";
	
	if( $config['allow_admin_wysiwyg'] == "yes" ) echo "<form method=post name=\"addnews\" id=\"addnews\" onsubmit=\"document.getElementById('short_story').value = $('#short_story').html(); document.getElementById('full_story').value = $('#full_story').html(); if(document.addnews.title.value == '' || document.addnews.short_story.value == ''){DLEalert('$lang[addnews_alert]', '$lang[p_info]');return false}\" action=\"\">";
	else echo "<form method=post name=\"addnews\" id=\"addnews\" onsubmit=\"if(document.addnews.title.value == '' || document.addnews.short_story.value == ''){DLEalert('$lang[addnews_alert]', '$lang[p_info]');return false}\" action=\"\">";
	
	$categories_list = CategoryNewsSelection( $cat_list, 0 );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"cat_select\" multiple";
	else $category_multiple = "";
	
	if( $member_id['user_group'] == 1 ) {
		
		$author_info = "<input type=\"text\" name=\"new_author\" size=\"20\"  class=\"edit bk\" style=\"vertical-align: middle;\" value=\"{$row['autor']}\"><input type=\"hidden\" name=\"old_author\" value=\"{$row['autor']}\" />";
	
	} else {
		
		$author_info = "<b>{$row['autor']}</b>";
	
	}


	if ( $user_group[$member_id['user_group']]['admin_editusers'] ) {

		$author_info .= "&nbsp;<a onclick=\"javascript:window.open('?mod=editusers&action=edituser&user=".urlencode($row['autor'])."','User','toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=540,height=500'); return(false)\" href=\"#\"><img src=\"engine/skins/images/user_edit.png\" style=\"vertical-align: middle;border: none;\" /></a>";

	}

	
	echo <<<HTML
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="engine/skins/calendar-blue.css" title="win2k-cold-1" />
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['edit_etitle']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<div id="dle_tabView1">
<div class="dle_aTab" style="display:none;">
<table width="100%">
    <tr>
        <td width="140" style="padding-left:5px;">{$lang['edit_info']}</td>
        <td>ID=<b>{$row['id']}</b>, {$lang['edit_eau']} {$author_info}</td>
    </tr>
    <tr>
        <td width="140" height="29" style="padding-left:5px;">{$lang['edit_et']}</td>
        <td><input class="edit bk" type="text" size="55" name="title" id="title" value="{$row['title']}"> <input class="edit" type="button" onClick="find_relates(); return false;" style="width:160px;" value="{$lang['b_find_related']}"> <a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_title]}', this, event, '220px')">[?]</a><span id="related_news"></span></td>
    </tr>
    <tr>
        <td height="29" style="padding-left:5px;">{$lang['edit_edate']}</td>
        <td><input type="text" name="newdate" id="f_date_c" size="20"  class="edit bk" value="{$row['date']}">
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_c" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>&nbsp;<input type="checkbox" name="allow_date" id="allow_date" value="yes" onclick="CheckStatus(addnews)" checked>&nbsp;{$lang['edit_ndate']}&nbsp;<input type="checkbox" name="allow_now" id="allow_now" value="yes" disabled>&nbsp;{$lang['edit_jdate']}
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_c",     // id of the input field
        ifFormat       :    "%Y-%m-%d %H:%M",      // format of the input field
        button         :    "f_trigger_c",  // trigger for the calendar (button ID)
        align          :    "Br",           // alignment 
		timeFormat     :    "24",
		showsTime      :    true,
        singleClick    :    true
    });
</script></td>
    </tr>
    <tr>
        <td height="29" style="padding-left:5px;">{$lang['addnews_cat']}</td>
        <td>
        <script src="engine/classes/js/alex_expand_category_select.js" type="text/javascript"></script>
        <div><button id="alex_expand_category_select" type="button">Развернуть</button></div>        
        <select id="category_select" name="category[]" id="category" onchange="onCategoryChange(this.value)" $category_multiple>
        {$categories_list}
        </select>
        </td>
    </tr>
</table>
<div class="hr_line"></div>
<table width="100%">
HTML;
	
	if( $config['allow_admin_wysiwyg'] == "yes" ) {
		
		include (ENGINE_DIR . '/editor/shortnews.php');
	
	} else {
		$bb_editor = true;
		include (ENGINE_DIR . '/inc/include/inserttag.php');
		
		echo <<<HTML
    <tr>
        <td width="140" height="29" style="padding-left:5px;">{$lang['addnews_short']}<br /><input class=bbcodes style="width: 30px;" onclick="document.addnews.short_story.rows += 5;" type=button value=" + ">&nbsp;&nbsp;<input class=bbcodes style="width: 30px;" onclick="document.addnews.short_story.rows -= 5;" type=button value=" - "></td>
        <td>{$bb_code}
	<textarea rows="16" style="width:98%;" onclick="setFieldName(this.name)" name="short_story" id="short_story" class="bk">{$row['short_story']}</textarea>
	</td></tr>
HTML;
	}
	
	if( $config['allow_admin_wysiwyg'] == "yes" ) {
		
		include (ENGINE_DIR . '/editor/fullnews.php');
	
	} else {
		
		echo <<<HTML
    <tr>
    <td height="29" style="padding-left:5px;">{$lang['addnews_full']}<br /><span class="navigation">({$lang['addnews_alt']})</span><br /><input class=bbcodes style="width: 30px;" onclick="document.addnews.full_story.rows += 5;" type=button value=" + ">&nbsp;&nbsp;<input class=bbcodes style="width: 30px;" onclick="document.addnews.full_story.rows -= 5;" type=button value=" - "></td>
    <td>{$bb_panel}<textarea rows="19" onclick="setFieldName(this.name)" name="full_story" id="full_story" style="width:98%;" class="bk">{$row['full_story']}</textarea>
	</td></tr>
HTML;
	}
	
	// Доп. поля
	$xfieldsaction = "list";
	$xfieldsid = $row['xfields'];
	$xfieldscat = $row['category'];
	include (ENGINE_DIR . '/inc/xfields.php');

	if( $config['allow_admin_wysiwyg'] != "yes" ) $output = str_replace("<!--panel-->", $bb_panel, $output);
	echo $output;
	
	if( $row['allow_comm'] ) $ifch = "checked";	else $ifch = "";
	if( $row['allow_main'] ) $ifmain = "checked"; else $ifmain = "";
	if( $row['approve'] ) $ifapp = "checked"; else $ifapp = "";
	if( $row['fixed'] ) $iffix = "checked";	else $iffix = "";
	if( $row['allow_rate'] ) $ifrat = "checked"; else $ifrat = "";
	
	if( $user_group[$member_id['user_group']]['allow_fixed'] and $config['allow_fixed'] ) $fix_input = "<input type=\"checkbox\" name=\"news_fixed\" value=\"1\" $iffix> $lang[addnews_fix]";
	if( $user_group[$member_id['user_group']]['allow_main'] ) $main_input = "<input type=\"checkbox\" name=\"allow_main\" value=\"1\" {$ifmain}> {$lang['addnews_main']}";
	
	if( $row['allow_br'] == '1' ) $fix_br_cheked = "checked";
	else $fix_br_cheked = "";
	
	if( $config['allow_admin_wysiwyg'] != "yes" ) $fix_br = "<input type=\"checkbox\" name=\"allow_br\" value=\"1\" {$fix_br_cheked}> {$lang['allow_br']}";
	else $fix_br = "";
	
	if( $row['editdate'] ) {
		$row['editdate'] = date( "d.m.Y H:i:s", $row['editdate'] );
		$lang['news_edit_date'] = $lang['news_edit_date'] . " " . $row['editor'] . " - " . $row['editdate'];
	} else
		$lang['news_edit_date'] = "";
	if( $row['view_edit'] == '1' ) $view_edit_cheked = "checked";
	else $view_edit_cheked = "";

	$exp_action = array();
	$exp_action[$expires['action']] = "selected=\"selected\"";


	echo <<<HTML
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td width="140" height="29" style="padding-left:5px;"><br /><br /><br />{$lang['news_edit_reason']}</td>
        <td><input type="checkbox" name="view_edit" value="1" {$view_edit_cheked}>{$lang['allow_view_edit']}<br /><br /><input class="edit bk" type="text" size="55" name="editreason" id="editreason" value="{$row['reason']}"> {$lang['news_edit_date']}</td>
    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td height="29" style="padding-left:5px;">{$lang['addnews_option']}</td>
        <td><input type="checkbox" name="approve" value="1" {$ifapp}> {$lang['addnews_mod']}<br /><br />

		{$main_input}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="allow_comm" value="1" {$ifch}> {$lang['addnews_comm']}<br />
		<input type="checkbox" name="allow_rating" value="1" {$ifrat}> {$lang['addnews_allow_rate']}&nbsp;&nbsp;&nbsp;{$fix_input}<br /><br />
		{$fix_br}
</td>
	</tr>
</table></div>
HTML;
	
	echo <<<HTML
	<div class="dle_aTab" style="display:none;">
<table width="100%">
    <tr>
        <td width="140" style="padding:4px;">{$lang['v_ftitle']}</td>
        <td ><input type="text" class="edit bk" name="vote_title" style="width:350px" value="{$poll['title']}"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_ftitle]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$lang['vote_title']}</td>
        <td><input type="text" class="edit bk" name="frage" style="width:350px" value="{$poll['frage']}"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_vtitle]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:4px;">$lang[vote_body]<br /><span class="navigation">$lang[vote_str_1]</span></td>
        <td><textarea rows="10" style="width:350px;" name="vote_body" class="bk">{$poll['body']}</textarea>
    </td>
    </tr>
    <tr>
        <td style="padding:4px;">&nbsp;</td>
        <td><input type="checkbox" name="allow_m_vote" value="1" {$poll['multiple']}> {$lang['v_multi']}</td>
    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
</table>
<div class="navigation">{$lang['v_info']}</div>
	</div>
	<div class="dle_aTab" style="display:none;">
	<table width="100%">
    <tr>
        <td width="140" height="29" style="padding-left:5px;">{$lang['catalog_url']}</td>
        <td><input type="text" name="catalog_url" size="5"  class="edit bk" value="{$row['symbol']}"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[catalog_hint_url]}', this, event, '300px')">[?]</a></td>
    </tr>
    <tr>
        <td width="140" height="29" style="padding-left:5px;">{$lang['addnews_url']}</td>
        <td><input type="text" name="alt_name" size="55"  class="edit bk" value="{$row['alt_name']}"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_url]}', this, event, '300px')">[?]</a></td>
    </tr>
    <tr>
        <td width="140" height="29" style="padding-left:5px;">{$lang['addnews_tags']}</td>
        <td><input type="text" id="tags" name="tags" size="55"  class="edit bk" value="{$row['tags']}" autocomplete="off" /><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_tags]}', this, event, '300px')">[?]</a></td>
    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td height="29" style="padding-left:5px;">{$lang['date_expires']}</td>
        <td><input type="text" name="expires" id="e_date_c" size="20"  class="edit bk" value="{$expires['expires']}">
<img src="engine/skins/images/img.gif"  align="absmiddle" id="e_trigger_c" style="cursor: pointer; border: 0" /> {$lang['cat_action']} <select name="expires_action"><option value="0" {$exp_action[0]}>{$lang['edit_dnews']}</option><option value="1" {$exp_action[1]}>{$lang['mass_edit_notapp']}</option><option value="2" {$exp_action[2]}>{$lang['mass_edit_notmain']}</option><option value="3" {$exp_action[3]}>{$lang['mass_edit_notfix']}</option></select><a href="#" class="hintanchor" onMouseover="showhint('{$lang['hint_expires']}', this, event, '320px')">[?]</a>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "e_date_c",     // id of the input field
        ifFormat       :    "%Y-%m-%d",      // format of the input field
        button         :    "e_trigger_c",  // trigger for the calendar (button ID)
        align          :    "Br",           // alignment 
        singleClick    :    true
    });
</script></td>
    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	    <tr>
	        <td>&nbsp;</td>
	        <td>{$lang['add_metatags']}<a href="#" class="hintanchor" onMouseover="showhint('{$lang['hint_metas']}', this, event, '220px')">[?]</a></td>
	    </tr>
	    <tr>
	        <td height="29" style="padding-left:5px;">{$lang['meta_title']}</td>
	        <td><input type="text" name="meta_title" style="width:388px;" class="edit bk" value="{$row['metatitle']}"></td>
	    </tr>
	    <tr>
	        <td height="29" style="padding-left:5px;">{$lang['meta_descr']}</td>
	        <td><input type="text" name="descr" id="autodescr" style="width:388px;" class="edit bk" value="{$row['descr']}"> ({$lang['meta_descr_max']})</td>
	    </tr>
	    <tr>
	        <td height="29" style="padding-left:5px;">{$lang['meta_keys']}</td>
	        <td><textarea name="keywords" id='keywords' style="width:388px;height:70px;" class="bk">{$row['keywords']}</textarea><br />
			<input onClick="auto_keywords(1)" type="button" class="buttons" value="{$lang['btn_descr']}" style="width:170px;">&nbsp;
			<input onClick="auto_keywords(2)" type="button" class="buttons" value="{$lang['btn_keyword']}" style="width:210px;">
			</td>
	    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	</table>
	</div>
	<div class="dle_aTab" style="display:none;">

<table width="100%">
HTML;
	
	if( $member_id['user_group'] < 3 ) {
		foreach ( $user_group as $group ) {
			if( $group['id'] > 1 ) {
				echo <<<HTML
    <tr>
        <td width="150" style="padding:4px;">{$group['group_name']}</td>
        <td><select name="group_extra[{$group['id']}]">
		<option value="0">{$lang['ng_group']}</option>
		<option value="1" {$access[$group['id']][1]}>{$lang['ng_read']}</option>
		<option value="2" {$access[$group['id']][2]}>{$lang['ng_all']}</option>
		<option value="3" {$access[$group['id']][3]}>{$lang['ng_denied']}</option>
		</select></td>
    </tr>
HTML;
			}
		}
	} else {
		
		echo <<<HTML
    <tr>
        <td style="padding:4px;"><br />{$lang['tabs_not']}</br /><br /></td>
    </tr>
HTML;
	
	}

	if ($row['autor'] != $member_id['name']) $notice_btn = "<input onClick=\"sendNotice('{$id}')\" type=\"button\" class=\"buttons\" value=\"{$lang['btn_notice']}\" style=\"width:130px;\">&nbsp;"; else $notice_btn = "";
	
	echo <<<HTML
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
</table>
<div class="navigation">{$lang['tabs_g_info']}</div>
	</div>
</div>
	<script type="text/javascript">
initTabs('dle_tabView1',Array('{$lang['tabs_news']}','{$lang['tabs_vote']}','{$lang['tabs_extra']}','{$lang['tabs_perm']}'),0, '100%',0);
	</script>
<div style="padding-left:150px;padding-top:5px;padding-bottom:5px;">
	<input type="submit" class="buttons" value="{$lang['btn_send']}" style="width:100px;">&nbsp;
	<input onClick="preview()" type="button" class="buttons" value="{$lang['btn_preview']}" style="width:100px;">&nbsp;
	{$notice_btn}
	<input onClick="confirmDelete('$PHP_SELF?mod=editnews&action=doeditnews&ifdelete=yes&id=$id&user_hash=$dle_login_hash', '{$id}')" type="button" class="buttons" value="{$lang['edit_dnews']}" style="width:100px;">
    <input type="hidden" name="id" value="$id" />
    <input type="hidden" name="expires_alt" value="{$expires['expires']}{$expires['action']}" />
    <input type="hidden" name="user_hash" value="$dle_login_hash" />
    <input type="hidden" name="action" value="doeditnews" />
    <input type="hidden" name="mod" value="editnews" />
</div>
HTML;
	
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
} 
// ********************************************************************************
// Сохранение или удаление новости
// ********************************************************************************
elseif( $action == "doeditnews" ) {

	$id = intval( $_GET['id'] );
	
	$allow_comm = isset( $_POST['allow_comm'] ) ? intval( $_POST['allow_comm'] ) : 0;
	$allow_main = isset( $_POST['allow_main'] ) ? intval( $_POST['allow_main'] ) : 0;
	$approve = isset( $_POST['approve'] ) ? intval( $_POST['approve'] ) : 0;
	$allow_rating = isset( $_POST['allow_rating'] ) ? intval( $_POST['allow_rating'] ) : 0;
	$news_fixed = isset( $_POST['news_fixed'] ) ? intval( $_POST['news_fixed'] ) : 0;
	$allow_br = isset( $_POST['allow_br'] ) ? intval( $_POST['allow_br'] ) : 0;
	$view_edit = isset( $_POST['view_edit'] ) ? intval( $_POST['view_edit'] ) : 0;
	$category = $_POST['category'];

	if( ! count( $category ) ) {
		$category = array ();
		$category[] = '0';
	}

	$category_list = array();

	foreach ( $category as $value ) {
		$category_list[] = intval($value);
	}

	$category_list = $db->safesql( implode( ',', $category_list ) );
	
	$allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_add'] );
	
	foreach ( $category as $selected ) {
		if( $allow_list[0] != "all" and ! in_array( $selected, $allow_list ) and $member_id['user_group'] != 1 ) $approve = 0;
	}

	$allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_allow_addnews'] );
	
	foreach ( $category as $selected ) {
		if( $allow_list[0] != "all" AND ! in_array( $selected, $allow_list ) AND $ifdelete != "yes") msg( "error", $lang['addnews_error'], $lang['news_err_41'], "javascript:history.go(-1)" );
	}

	$title = $parse->process( trim( strip_tags ($_POST['title']) ) );

	if ( !$user_group[$member_id['user_group']]['allow_html'] ) {

		$_POST['short_story'] = strip_tags ($_POST['short_story']);
		$_POST['full_story'] = strip_tags ($_POST['full_story']);

	}

	if ( $config['allow_admin_wysiwyg'] == "yes" ) $parse->allow_code = false;
	
	$full_story = $parse->process( $_POST['full_story'] );
	$short_story = $parse->process( $_POST['short_story'] );
	
	if( $config['allow_admin_wysiwyg'] == "yes" or $allow_br != '1' ) {
		
		$full_story = $db->safesql( $parse->BB_Parse( $full_story ) );
		$short_story = $db->safesql( $parse->BB_Parse( $short_story ) );
	
	} else {
		
		$full_story = $db->safesql( $parse->BB_Parse( $full_story, false ) );
		$short_story = $db->safesql( $parse->BB_Parse( $short_story, false ) );
	
	}

	if( $parse->not_allowed_text ) {
		msg( "error", $lang['addnews_error'], $lang['news_err_39'], "javascript:history.go(-1)" );
	}
	
	if( trim( $title ) == "" and $ifdelete != "yes" ) msg( "error", $lang['cat_error'], $lang['addnews_ertitle'], "javascript:history.go(-1)" );
	if( $short_story == "" and $ifdelete != "yes" ) msg( "error", $lang['cat_error'], $lang['addnews_erstory'], "javascript:history.go(-1)" );
	if( dle_strlen( $title, $config['charset'] ) > 255 ) {
		msg( "error", $lang['cat_error'], $lang['addnews_ermax'], "javascript:history.go(-1)" );
	}
	
	if( trim( $_POST['alt_name'] ) == "" or ! $_POST['alt_name'] ) $alt_name = totranslit( stripslashes( $title ) );
	else $alt_name = totranslit( stripslashes( $_POST['alt_name'] ) );
	
	$title = $db->safesql( $title );
	$metatags = create_metatags( $short_story . $full_story );
	
	$catalog_url = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['catalog_url'] ) ) ) ), 0, 3, $config['charset'] ) );
	$editreason = $db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['editreason'] ) ) ), ENT_QUOTES ) );
	
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
	
	// обработка опроса
	if( trim( $_POST['vote_title'] != "" ) ) {
		
		$add_vote = 1;
		$vote_title = trim( $db->safesql( $parse->process( $_POST['vote_title'] ) ) );
		$frage = trim( $db->safesql( $parse->process( $_POST['frage'] ) ) );
		$vote_body = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['vote_body'] ), false ) );
		$allow_m_vote = intval( $_POST['allow_m_vote'] );
	
	} else
		$add_vote = 0;
		
	// обработка доступа
	if( $member_id['user_group'] < 3 and $ifdelete != "yes" ) {
		
		$group_regel = array ();
		
		foreach ( $_POST['group_extra'] as $key => $value ) {
			if( $value ) $group_regel[] = intval( $key ) . ':' . intval( $value );
		}
		
		if( count( $group_regel ) ) $group_regel = implode( "||", $group_regel );
		else $group_regel = "";
	
	} else
		$group_regel = '';

	if ( ($_POST['expires'].$_POST['expires_action']) != $_POST['expires_alt'] ) {	
		if( trim( $_POST['expires'] ) != "" ) {
			if( (($expires = strtotime( $_POST['expires'] )) === - 1) OR !$expires) {
				msg( "error", $lang['addnews_error'], $lang['addnews_erdate'], "javascript:history.go(-1)" );
			} 
		} else $expires = '';

		$expires_change = true;

	} else $expires_change = false;
	
	$no_permission = FALSE;
	$okdeleted = FALSE;
	$okchanges = FALSE;
	
	$db->query( "SELECT id, autor, approve, tags FROM " . PREFIX . "_post where id = '$id'" );
	
	while ( $row = $db->get_row() ) {
		$item_db[0] = $row['id'];
		$item_db[1] = $row['autor'];
		$item_db[2] = $row['tags'];
		$item_db[3] = $row['approve'];
	}
	
	$db->free();

	if( $ifdelete != "yes" ) {

		if( $config['safe_xfield'] ) {
			$parse->ParseFilter();
			$parse->safe_mode = true;
		}

		$xfieldsaction = "init";
		$xfieldsid = $item_db[0];
		include (ENGINE_DIR . '/inc/xfields.php');
	}
	

	if( $item_db[0] ) {
		
		$have_perm = 0;
		
		if( $user_group[$member_id['user_group']]['allow_all_edit'] ) $have_perm = 1;
		
		if( $user_group[$member_id['user_group']]['allow_edit'] and $item_db[1] == $member_id['name'] ) {
			$have_perm = 1;
		}
		
		if( ($member_id['user_group'] == 1) ) {
			$have_perm = 1;
		}
		
		if( $have_perm ) {
			
			if( $ifdelete != "yes" ) {
				$okchanges = TRUE;
				
				// Обработка даты и времени
				$added_time = time() + ($config['date_adjust'] * 60);
				$newdate = $_POST['newdate'];
				
				if( $_POST['allow_date'] != "yes" ) {
					
					if( $_POST['allow_now'] == "yes" ) $thistime = date( "Y-m-d H:i:s", $added_time );
					elseif( (($newsdate = strtotime( $newdate )) === - 1) OR !$newsdate ) {
						msg( "error", $lang['cat_error'], $lang['addnews_erdate'], "javascript:history.go(-1)" );
					} else {
						
						$thistime = date( "Y-m-d H:i:s", $newsdate );
						
						if( ! intval( $config['no_date'] ) and $newsdate > $added_time ) {
							$thistime = date( "Y-m-d H:i:s", $added_time );
						}
					
					}
					
					$result = $db->query( "UPDATE " . PREFIX . "_post set title='$title', date='$thistime', short_story='$short_story', full_story='$full_story', xfields='$filecontents', descr='{$metatags['description']}', keywords='{$metatags['keywords']}', category='$category_list', alt_name='$alt_name', allow_comm='$allow_comm', approve='$approve', allow_main='$allow_main', allow_rate='$allow_rating', fixed='$news_fixed', allow_br='$allow_br', votes='$add_vote', access='$group_regel', symbol='$catalog_url', flag='1', editdate='$added_time', editor='{$member_id['name']}', reason='$editreason', view_edit='$view_edit', tags='{$_POST['tags']}', metatitle='{$metatags['title']}' WHERE id='$item_db[0]'" );
				
				} else {
					
					$result = $db->query( "UPDATE " . PREFIX . "_post set title='$title', short_story='$short_story', full_story='$full_story', xfields='$filecontents', descr='{$metatags['description']}', keywords='{$metatags['keywords']}', category='$category_list', alt_name='$alt_name', allow_comm='$allow_comm', approve='$approve', allow_main='$allow_main', allow_rate='$allow_rating', fixed='$news_fixed', allow_br='$allow_br', votes='$add_vote', access='$group_regel', symbol='$catalog_url', editdate='$added_time', editor='{$member_id['name']}', reason='$editreason', view_edit='$view_edit', tags='{$_POST['tags']}', metatitle='{$metatags['title']}' WHERE id='$item_db[0]'" );
				}
				
				if( $add_vote ) {
					
					$count = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_poll WHERE news_id = '$item_db[0]'" );
					
					if( $count['count'] ) $db->query( "UPDATE  " . PREFIX . "_poll set title='$vote_title', frage='$frage', body='$vote_body', multiple='$allow_m_vote' WHERE news_id = '$item_db[0]'" );
					else $db->query( "INSERT INTO " . PREFIX . "_poll (news_id, title, frage, body, votes, multiple, answer) VALUES('$item_db[0]', '$vote_title', '$frage', '$vote_body', 0, '$allow_m_vote', '')" );
				
				} else {
					$db->query( "DELETE FROM " . PREFIX . "_poll WHERE news_id='$item_db[0]'" );
					$db->query( "DELETE FROM " . PREFIX . "_poll_log WHERE news_id='$item_db[0]'" );
				}

				if ( $expires_change ) {
					if( $expires ) {
						$expires_action = intval($_POST['expires_action']);
						$db->query( "DELETE FROM " . PREFIX . "_post_log WHERE news_id='$item_db[0]'" );
						$db->query( "INSERT INTO " . PREFIX . "_post_log (news_id, expires, action) VALUES('$item_db[0]', '$expires', '$expires_action')" );
					} else {
	
						$db->query( "DELETE FROM " . PREFIX . "_post_log WHERE news_id='$item_db[0]'" );
	
					}
				}
				
				// Смена автора публикации
				if( $member_id['user_group'] == 1 and $_POST['new_author'] != $_POST['old_author'] ) {
					
					$_POST['new_author'] = $db->safesql( $_POST['new_author'] );
					
					$row = $db->super_query( "SELECT user_id  FROM " . USERPREFIX . "_users WHERE name = '{$_POST['new_author']}'" );
					
					if( $row['user_id'] ) {
						
						$db->query( "UPDATE " . PREFIX . "_post SET autor='{$_POST['new_author']}' WHERE id='$item_db[0]'" );
						$db->query( "UPDATE " . PREFIX . "_images SET author='{$_POST['new_author']}' WHERE news_id='$item_db[0]'" );
						$db->query( "UPDATE " . PREFIX . "_files SET author='{$_POST['new_author']}' WHERE news_id='$item_db[0]'" );
						
						$db->query( "UPDATE " . USERPREFIX . "_users SET news_num=news_num+1 where user_id='{$row['user_id']}'" );
						$db->query( "UPDATE " . USERPREFIX . "_users SET news_num=news_num-1 where name='$item_db[1]'" );
					
					} else {
						
						msg( "error", $lang['addnews_error'], $lang['edit_no_author'], "javascript:history.go(-1)" );
					
					}
				
				}
				
				// Облако тегов
				if( $_POST['tags'] != $item_db[2] or $approve != $item_db[3] ) {
					$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '$item_db[0]'" );
					
					if( $_POST['tags'] != "" and $approve ) {
						
						$tags = array ();
						
						$_POST['tags'] = explode( ",", $_POST['tags'] );
						
						foreach ( $_POST['tags'] as $value ) {
							
							$tags[] = "('" . $item_db[0] . "', '" . trim( $value ) . "')";
						}
						
						$tags = implode( ", ", $tags );
						$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
					
					}
				}
			
			} else {
				
				$db->query( "DELETE FROM " . PREFIX . "_post WHERE id='$item_db[0]'" );
				$db->query( "DELETE FROM " . PREFIX . "_comments WHERE post_id='$item_db[0]'" );
				$db->query( "DELETE FROM " . PREFIX . "_poll WHERE news_id='$item_db[0]'" );
				$db->query( "DELETE FROM " . PREFIX . "_poll_log WHERE news_id='$item_db[0]'" );
				$db->query( "DELETE FROM " . PREFIX . "_post_log WHERE news_id='$item_db[0]'" );
				$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '$item_db[0]'" );
				$db->query( "DELETE FROM " . PREFIX . "_logs WHERE news_id = '$item_db[0]'" );

				$db->query( "UPDATE " . USERPREFIX . "_users set news_num=news_num-1 where name='$item_db[1]'" );
				
				$okdeleted = TRUE;
				
				$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images where news_id = '$item_db[0]'" );
				
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
				
				$db->query( "DELETE FROM " . PREFIX . "_images WHERE news_id = '$item_db[0]'" );
				
				$db->query( "SELECT id, onserver FROM " . PREFIX . "_files WHERE news_id = '$item_db[0]'" );
				
				while ( $row = $db->get_row() ) {
					
					@unlink( ROOT_DIR . "/uploads/files/" . $row['onserver'] );
				
				}
				
				$db->query( "DELETE FROM " . PREFIX . "_files WHERE news_id = '$item_db[0]'" );
			
			}
		} else
			$no_permission = TRUE;
	
	}
	
	clear_cache();
	
	if( ! $_SESSION['admin_referrer'] ) {
		
		$_SESSION['admin_referrer'] = "?mod=editnews&amp;action=list";
	
	}
	
	if( $no_permission ) {
		msg( "error", $lang['addnews_error'], $lang['edit_denied'], $_SESSION['admin_referrer'] );
	} elseif( $okdeleted ) {
		msg( "info", $lang['edit_delok'], $lang['edit_delok_1'], $_SESSION['admin_referrer'] );
	} elseif( $okchanges ) {
		msg( "info", $lang['edit_alleok'], $lang['edit_alleok_1'], $_SESSION['admin_referrer'] );
	} else {
		msg( "error", $lang['addnews_error'], $lang['edit_allerr'], $_SESSION['admin_referrer'] );
	}
}


if($action == "display_this_cat") {
		
    $id = $_REQUEST['thiscat'];
    $resultCurrCat = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE id='{$id}'");
    $myrowCurrCat = $db->get_array($resultCurrCat);
    
    $explodeCurrCat1 = explode(" ", $myrowCurrCat['proizvoditel']);
    $explodeCurrCat = explode(",",$explodeCurrCat1[0]);
    
				if($explodeCurrCat[0] == "TIKKURILA") {
                	$current = $explodeCurrCat1[0] . " " . $explodeCurrCat1[1];
                } else if($explodeCurrCat[0] == "Tikkurila"){
                	$current = $explodeCurrCat1[0] . " " . $explodeCurrCat1[1];
                }else if($explodeCurrCat[0] == "Teknos"){
                	$current = $explodeCurrCat1[0] . " " . $explodeCurrCat1[1] . " " . $explodeCurrCat1[2];
                } else { 
                	$current = $explodeCurrCat[0];
                }
    
    $resultSelect = $db->query("SELECT id FROM " . PREFIX . "_category WHERE name LIKE '$current%'");
    $myrowSelect = $db->get_array($resultSelect);
    
    $categ = array();
    $selectId = array();
    
    do {
    	$selectId[$myrowSelect['id']] = $myrowSelect['id'];
    } while($myrowSelect = $db->get_array($resultSelect));
    
    $resultPost = $db->query("SELECT id, category FROM " . PREFIX . "_post ORDER BY id ASC");
    $myrowPost = $db->get_array($resultPost);
    
    
    
    
    do {
       
        $mp = explode(",", $myrowPost['category']);
            
        foreach($selectId as $key=>$value){
            if(in_array($value, $mp)) {
            	if(!in_array($myrowPost['id'], $categ)) {
                	$categ[] = "id=" . $myrowPost['id'];
                }
            }
            
        }
    	
    } while($myrowPost = $db->get_array($resultPost));
    
    $where = implode(" OR ", $categ);
    $where = " WHERE " . $where;
    
    
    
    
	
	if(!isset($_REQUEST['number']) && !isset($_REQUEST['tdo'])) {
	setcookie("news_array", "");
	$_SESSION['admin_referrer'] = $_SERVER['REQUEST_URI'];
	echoheader( "editnews", $lang['edit_head'] );
	
	$search_field = $db->safesql( trim( htmlspecialchars( stripslashes( urldecode( $_REQUEST['search_field'] ) ), ENT_QUOTES ) ) );
	$search_author = $db->safesql( trim( htmlspecialchars( stripslashes( urldecode( $_REQUEST['search_author'] ) ), ENT_QUOTES ) ) );
	$fromnewsdate = $db->safesql( trim( htmlspecialchars( stripslashes( $_REQUEST['fromnewsdate'] ), ENT_QUOTES ) ) );
	$tonewsdate = $db->safesql( trim( htmlspecialchars( stripslashes( $_REQUEST['tonewsdate'] ), ENT_QUOTES ) ) );
	
	$start_from = intval( $_REQUEST['start_from'] );
	$news_per_page = intval( $_REQUEST['news_per_page'] );
	$gopage = intval( $_REQUEST['gopage'] );
	
	$_REQUEST['news_status'] = intval( $_REQUEST['news_status'] );
	$news_status_sel = array ('0' => '', '1' => '', '2' => '' );
	$news_status_sel[$_REQUEST['news_status']] = 'selected="selected"';
	
	if( ! $news_per_page or $news_per_page < 1 ) {
		$news_per_page = 10000;
	}
	if( $gopage ) $start_from = ($gopage - 1) * $news_per_page;
	
	if( $start_from < 0 ) $start_from = 0;
	
	
	
	

	
	$order_by = array ();
	
	if( $_REQUEST['search_order_f'] == "asc" or $_REQUEST['search_order_f'] == "desc" ) $search_order_f = $_REQUEST['search_order_f'];
	else $search_order_f = "";
	if( $_REQUEST['search_order_m'] == "asc" or $_REQUEST['search_order_m'] == "desc" ) $search_order_m = $_REQUEST['search_order_m'];
	else $search_order_m = "";
	if( $_REQUEST['search_order_d'] == "asc" or $_REQUEST['search_order_d'] == "desc" ) $search_order_d = $_REQUEST['search_order_d'];
	else $search_order_d = "";
	if( $_REQUEST['search_order_t'] == "asc" or $_REQUEST['search_order_t'] == "desc" ) $search_order_t = $_REQUEST['search_order_t'];
	else $search_order_t = "";
	
	if( ! empty( $search_order_f ) ) {
		$order_by[] = "fixed $search_order_f";
	}
	if( ! empty( $search_order_m ) ) {
		$order_by[] = "approve $search_order_m";
	}
	if( ! empty( $search_order_d ) ) {
		$order_by[] = "date $search_order_d";
	}
	if( ! empty( $search_order_t ) ) {
		$order_by[] = "title $search_order_t";
	}
	
	$order_by = implode( ", ", $order_by );
	if( ! $order_by ) $order_by = "fixed desc, approve asc, date desc";
	
	$search_order_fixed = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( isset( $_REQUEST['search_order_f'] ) ) {
		$search_order_fixed[$search_order_f] = 'selected';
	} else {
		$search_order_fixed['desc'] = 'selected';
	}
	$search_order_mod = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( isset( $_REQUEST['search_order_m'] ) ) {
		$search_order_mod[$search_order_m] = 'selected';
	} else {
		$search_order_mod['asc'] = 'selected';
	}
	$search_order_date = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( isset( $_REQUEST['search_order_d'] ) ) {
		$search_order_date[$search_order_d] = 'selected';
	} else {
		$search_order_date['desc'] = 'selected';
	}
	$search_order_title = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( ! empty( $search_order_t ) ) {
		$search_order_title[$search_order_t] = 'selected';
	} else {
		$search_order_title['----'] = 'selected';
	}
    
	$db->query( "SELECT id, date, title, category, autor, alt_name, comm_num, approve, fixed, news_read, flag FROM " . PREFIX . "_post " . $where . " ORDER BY title ASC");
	
	// Prelist Entries
	$flag = 1;
	if( $start_from == "0" ) {
		$start_from = "";
	}
	$i = $start_from;
	$entries_showed = 0;
	
	$entries = "";
	
	while ( $row = $db->get_array() ) {
		
		$i ++;
		
		$itemdate = date( "d.m.Y", strtotime( $row['date'] ) );
		
		if( strlen( $row['title'] ) > 65 ) $title = substr( $row['title'], 0, 65 ) . " ...";
		else $title = $row['title'];
		
		$title = htmlspecialchars( stripslashes( $title ), ENT_QUOTES );
		$title = str_replace("&amp;","&", $title );
		
		$entries .= "<tr>

        <td class=\"list\" style=\"padding:4px;\">
        $itemdate - ";
		
		if( $row['fixed'] == '1' ) $entries .= "<font color=\"red\">$lang[edit_fix] </font> ";
		
		if( $row['comm_num'] > 0 ) {
			
			if( $config['allow_alt_url'] == "yes" ) {
				
				if( $row['flag'] and $config['seo_type'] ) {
					
					if( intval( $row['category'] ) and $config['seo_type'] == 2 ) {
						
						$full_link = $config['http_home_url'] . get_url( intval( $row['category'] ) ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
					
					} else {
						
						$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
					
					}
				
				} else {
					
					$full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime( $row['date'] ) ) . $row['alt_name'] . ".html";
				}
			
			} else {
				
				$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
			
			}
			
			$comm_link = "<a class=\"list\" onClick=\"return dropdownmenu(this, event, MenuBuild('" . $row['id'] . "', '{$full_link}'), '150px')\"href=\"{$full_link}\" target=\"_blank\">{$row['comm_num']}</a>";
		
		} else {
			$comm_link = $row['comm_num'];
		}
		
		$entries .= "<a title='$lang[edit_act]' class=\"list\" href=\"$PHP_SELF?mod=editnews&action=editnews&id=$row[0]\">$title</a>
        <td align=center>{$row['news_read']}</td><td align=center>" . $comm_link;
		
		$entries .= "</td><td style=\"text-align: center\">";
		
		if( $row['approve'] ) $erlaub = "$lang[edit_yes]";
		else $erlaub = "<font color=\"red\">$lang[edit_no]</font>";
		$entries .= $erlaub;
		
		$entries .= "<td align=\"center\">";
		
		if( ! $row['category'] ) $my_cat = "---";
		else {
			
			$my_cat = array ();
			$cat_list = explode( ',', $row['category'] );
			
			foreach ( $cat_list as $element ) {
				if( $element ) $my_cat[] = $cat[$element];
			}
			$my_cat = implode( ',<br />', $my_cat );
		}
		
		$entries .= "$my_cat<td class=\"list\"><a class=list href=\"?mod=editusers&action=list&search=yes&search_name=" . $row['autor'] . "\">" . $row['autor'] . "</a>

               <td align=center><input name=\"selected_news[]\" value=\"{$row['id']}\" type='checkbox'>

             </tr>
			<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=7></td></tr>
            ";
		$entries_showed ++;
		
		if( $i >= $news_per_page + $start_from ) {
			break;
		}
	}
	

	// End prelisting
	$result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post" . $where );
	
	$all_count_news = $result_count['count'];
	
	///////////////////////////////////////////
	// Options Bar
	$category_list = CategoryNewsSelection( $search_cat, 0, false );
	
	
	echo <<<HTML
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="engine/skins/calendar-blue.css" title="win2k-cold-1" />
<script type="text/javascript" src="engine/skins/calendar.js"></script>
<script type="text/javascript" src="engine/skins/calendar-en.js"></script>
<script type="text/javascript" src="engine/skins/calendar-setup.js"></script>
<script language="javascript">
    function search_submit(prm){
      document.optionsbar.start_from.value=prm;
      document.optionsbar.submit();
      return false;
    }
    function gopage_submit(prm){
      document.optionsbar.start_from.value= (prm - 1) * {$news_per_page};
      document.optionsbar.submit();
      return false;
    }
    </script>
<form action="?mod=editnews&amp;action=list" method="GET" name="optionsbar" id="optionsbar">
<input type="hidden" name="mod" value="editnews">
<input type="hidden" name="action" value="list">
<div style="padding-top:5px;padding-bottom:2px;display:none" name="advancedsearch" id="advancedsearch">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['edit_stat']} <b>{$entries_showed}</b> {$lang['edit_stat_1']} <b>{$all_count_news}</b></div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
     <tr>
		<td style="padding:5px;">{$lang['edit_search_news']}</td>
		<td style="padding-left:5px;"><input class="edit" name="search_field" value="{$search_field}" type="text" size="35"></td>
		<td style="padding-left:5px;">{$lang['search_by_author']}</td>
		<td style="padding-left:22px;"><input class="edit" name="search_author" value="{$search_author}" type="text" size="36"></td>

    </tr>
     <tr>
		<td style="padding:5px;">{$lang['edit_cat']}</td>
		<td style="padding-left:5px;"><select name="search_cat" ><option selected value="">$lang[edit_all]</option><option value="-1">$lang[cat_in_none]</option>{$category_list}</select></td>
		<td style="padding-left:5px;">{$lang['search_by_date']}</td>
		<td style="padding-left:5px;">{$lang['edit_fdate']} <input type="text" name="fromnewsdate" id="fromnewsdate" size="11" maxlength="16" class="edit" value="{$fromnewsdate}">
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_dnews" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>
<script type="text/javascript">
    Calendar.setup({
      inputField     :    "fromnewsdate",     // id of the input field
      ifFormat       :    "%Y-%m-%d",      // format of the input field
      button         :    "f_trigger_dnews",  // trigger for the calendar (button ID)
      align          :    "Br",           // alignment 
		  timeFormat     :    "24",
		  showsTime      :    false,
      singleClick    :    true
    });
</script> {$lang['edit_tdate']} <input type="text" name="tonewsdate" id="tonewsdate" size="11" maxlength="16" class="edit" value="{$tonewsdate}">
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_tnews" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>
<script type="text/javascript">
    Calendar.setup({
      inputField     :    "tonewsdate",     // id of the input field
      ifFormat       :    "%Y-%m-%d",      // format of the input field
      button         :    "f_trigger_tnews",  // trigger for the calendar (button ID)
      align          :    "Br",           // alignment 
		  timeFormat     :    "24",
		  showsTime      :    false,
      singleClick    :    true
    });
</script></td>

    </tr>
     <tr>
		<td style="padding:5px;">{$lang['search_by_status']}</td>
		<td style="padding-left:5px;"><select name="news_status" id="news_status">
								<option {$news_status_sel['0']} value="0">{$lang['news_status_all']}</option>
								<option {$news_status_sel['1']} value="1">{$lang['news_status_approve']}</option>
                                <option {$news_status_sel['2']} value="2">{$lang['news_status_mod']}</option>
							</select></td>
		<td style="padding-left:5px;">{$lang['edit_page']}</td>
		<td style="padding-left:22px;"><input class="edit" style="text-align: center" name="news_per_page" value="{$news_per_page}" type="text" size="36"></td>

    </tr>
    <tr>
        <td colspan="4"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td colspan="4">{$lang['news_order']}</td>
    </tr>
    <tr>
        <td style="padding:5px;">{$lang['news_order_fixed']}</td>
        <td style="padding:5px;">{$lang['edit_approve']}</td>
        <td style="padding:5px;">{$lang['search_by_date']}</td>
        <td style="padding:5px;">{$lang['edit_et']}</td>
    </tr>
    <tr>
        <td style="padding-left:2px;"><select name="search_order_f" id="search_order_f">
           <option {$search_order_fixed['----']} value="">{$lang['user_order_no']}</option>
           <option {$search_order_fixed['asc']} value="asc">{$lang['user_order_plus']}</option>
           <option {$search_order_fixed['desc']} value="desc">{$lang['user_order_minus']}</option>
            </select>
        </td>
        <td style="padding-left:2px;"><select name="search_order_m" id="search_order_m">
           <option {$search_order_mod['----']} value="">{$lang['user_order_no']}</option>
           <option {$search_order_mod['asc']} value="asc">{$lang['user_order_plus']}</option>
           <option {$search_order_mod['desc']} value="desc">{$lang['user_order_minus']}</option>
            </select>
        </td>
        <td style="padding-left:2px;"><select name="search_order_d" id="search_order_d">
           <option {$search_order_date['----']} value="">{$lang['user_order_no']}</option>
           <option {$search_order_date['asc']} value="asc">{$lang['user_order_plus']}</option>
           <option {$search_order_date['desc']} value="desc">{$lang['user_order_minus']}</option>
            </select>
        </td>
        <td style="padding-left:2px;" colspan="2"><select name="search_order_t" id="search_order_t">
           <option {$search_order_title['----']} value="">{$lang['user_order_no']}</option>
           <option {$search_order_title['asc']} value="asc">{$lang['user_order_plus']}</option>
           <option {$search_order_title['desc']} value="desc">{$lang['user_order_minus']}</option>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="4"><div class="hr_line"></div></td>
    </tr>
    <tr>
		<td style="padding:5px;">&nbsp;</td>
		<td colspan="3">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
<input onClick="javascript:search_submit(0); return(false);" class="edit" type="submit" value="{$lang['edit_act_1']}"></td>

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
</div>
</form>
HTML;
	// End Options Bar
	

	echo <<<JSCRIPT
<script language='JavaScript' type="text/javascript">
<!--
function ckeck_uncheck_all() {
    var frm = document.editnews;
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
-->
</script>
JSCRIPT;
	
	if( $entries_showed == 0 ) {
		
		echo <<<HTML
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['news_list']}</div></td>
        <td bgcolor="#EFEFEF" height="29" style="padding:5px;" align="right"><a href="javascript:ShowOrHide('advancedsearch');">{$lang['news_advanced_search']}</a></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td align="center" style="height:50px;">{$lang['edit_nonews']}</td>
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
</div>

HTML;
	
	} else {
		
		echo <<<HTML
<script type="text/javascript" src="engine/ajax/menu.js"></script>
<script language="javascript" type="text/javascript">
<!--
function cdelete(id){
    var agree=confirm("{$lang['db_confirmclear']}");
    if (agree)
    document.location='?mod=comments&user_hash={$dle_login_hash}&action=dodelete&id=' + id + '';
}
function MenuBuild( m_id, m_link ){

var menu=new Array()

menu[0]='<a href="' + m_link + '" target="_blank">{$lang['comm_view']}</a>';
menu[1]='<a href="?mod=comments&action=edit&id=' + m_id + '">{$lang['vote_edit']}</a>';
menu[2]='<a onClick="javascript:cdelete(' + m_id + '); return(false)" href="?mod=comments&user_hash={$dle_login_hash}&action=dodelete&id=' + m_id + '" >{$lang['comm_del']}</a>';

return menu;
}
//-->
</script>
<div style="margin-left:4px; margin-top:3px;" align="center">
<form action="">
<select name="thiscat">
<option value="">Выберите производителя</option>
HTML;

do {
echo <<<HTML

<option value="{$mmm[id]}">
	{$mmm[name]}
</option>

HTML;
} while($mmm = $asd->get_array($rrr));
echo <<<HTML
</select>
<input type=hidden name=user_hash value="{$dle_login_hash}">
<input type="hidden" name="action" value="display_this_cat">
<input type="hidden" name="mod" value="editnews">
<input type="submit" value="Выполнить" class="edit">
<a href="admin.php?mod=editnews&action=list&tdo=razdel" style="margin-left:25px;">Управление разделами</a>
</form>

<form action="" method="post" name="editnews">
</div>
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['news_list']}</div></td>
        <td bgcolor="#EFEFEF" height="29" style="padding:5px;" align="right"><a href="javascript:ShowOrHide('advancedsearch');">{$lang['news_advanced_search']}</a></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td>
	<table width=100%>
	<tr>
    <td>&nbsp;&nbsp;{$lang['edit_title']}
	<td width=80>&nbsp;{$lang['st_views']}&nbsp;
	<td width=80>&nbsp;{$lang['edit_com']}&nbsp;
    <td width=80 align="center">{$lang['edit_approve']}
    <td width=120 align="center">{$lang['edit_cl']}
    <td width=70 >{$lang['edit_autor']}
    <td width=10 align="center"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()">
	</tr>
	<tr><td colspan="7"><div class="hr_line"></div></td></tr>
	{$entries}
	<tr><td colspan="7"><div class="hr_line"></div></td></tr>
HTML;
		
		// pagination

		$npp_nav = "<div class=\"news_navigation\" style=\"margin-bottom:5px; margin-top:5px;\">";
		
		if( $start_from > 0 ) {
			$previous = $start_from - $news_per_page;
			$npp_nav .= "<a onClick=\"javascript:search_submit($previous); return(false);\" href=\"#\" title=\"{$lang['edit_prev']}\">&lt;&lt;</a> ";
		}
		
		if( $all_count_news > $news_per_page ) {
			
			$enpages_count = @ceil( $all_count_news / $news_per_page );
			$enpages_start_from = 0;
			$enpages = "";
			
			if( $enpages_count <= 10 ) {
				
				for($j = 1; $j <= $enpages_count; $j ++) {
					
					if( $enpages_start_from != $start_from ) {
						
						$enpages .= "<a onClick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$j</a> ";
					
					} else {
						
						$enpages .= "<span>$j</span> ";
					}
					
					$enpages_start_from += $news_per_page;
				}
				
				$npp_nav .= $enpages;
			
			} else {
				
				$start = 1;
				$end = 10;
				
				if( $start_from > 0 ) {
					
					if( ($start_from / $news_per_page) > 4 ) {
						
						$start = @ceil( $start_from / $news_per_page ) - 3;
						$end = $start + 9;
						
						if( $end > $enpages_count ) {
							$start = $enpages_count - 10;
							$end = $enpages_count - 1;
						}
						
						$enpages_start_from = ($start - 1) * $news_per_page;
					
					}
				
				}
				
				if( $start > 2 ) {
					
					$enpages .= "<a onClick=\"javascript:search_submit(0); return(false);\" href=\"#\">1</a> ... ";
				
				}
				
				for($j = $start; $j <= $end; $j ++) {
					
					if( $enpages_start_from != $start_from ) {
						
						$enpages .= "<a onClick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$j</a> ";
					
					} else {
						
						$enpages .= "<span>$j</span> ";
					}
					
					$enpages_start_from += $news_per_page;
				}
				
				$enpages_start_from = ($enpages_count - 1) * $news_per_page;
				$enpages .= "... <a onClick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$enpages_count</a> ";
				
				$npp_nav .= $enpages;
			
			}
		
		}
		
		if( $all_count_news > $i ) {
			$how_next = $all_count_news - $i;
			if( $how_next > $news_per_page ) {
				$how_next = $news_per_page;
			}
			$npp_nav .= "<a onClick=\"javascript:search_submit($i); return(false);\" href=\"#\" title=\"{$lang['edit_next']}\">&gt;&gt;</a>";
		}
		
		$npp_nav .= "</div>";
		
		// pagination
		

		if( $entries_showed != 0 ) {
			echo <<<HTML
<tr><td>{$npp_nav}</td>
<td colspan=5 align="right" valign="top"><div style="margin-bottom:5px; margin-top:5px;">
<select name=action>
<option value="">{$lang['edit_selact']}</option>
<option value="mass_edit_price">Изменить цену</option>
<option value="mass_edit_price_k">Изменить цену комплекта</option>
<option value="mass_edit_price_p">Изменить цену продукции</option>
<option value="mass_move_to_cat">{$lang['edit_selcat']}</option>
<option value="mass_edit_symbol">{$lang['edit_selsymbol']}</option>
<option value="mass_edit_cloud">{$lang['edit_cloud']}</option>
<option value="mass_date">{$lang['mass_edit_date']}</option>
<option value="mass_approve">{$lang['mass_edit_app']}</option>
<option value="mass_not_approve">{$lang['mass_edit_notapp']}</option>
<option value="mass_fixed">{$lang['mass_edit_fix']}</option>
<option value="mass_not_fixed">{$lang['mass_edit_notfix']}</option>
<option value="mass_comments">{$lang['mass_edit_comm']}</option>
<option value="mass_not_comments">{$lang['mass_edit_notcomm']}</option>
<option value="mass_rating">{$lang['mass_edit_rate']}</option>
<option value="mass_not_rating">{$lang['mass_edit_notrate']}</option>
<option value="mass_main">{$lang['mass_edit_main']}</option>
<option value="mass_not_main">{$lang['mass_edit_notmain']}</option>
<option value="mass_clear_count">{$lang['mass_clear_count']}</option>
<option value="mass_clear_rating">{$lang['mass_clear_rating']}</option>
<option value="mass_clear_cloud">{$lang['mass_clear_cloud']}</option>
<option value="mass_delete">{$lang['edit_seldel']}</option>
</select>
<input type=hidden name=mod value="massactions">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<input class="edit" type="submit" value="{$lang['b_start']}">
</div></th></tr>
HTML;
			
			if( $all_count_news > $news_per_page ) {
				
				echo <<<HTML
<tr><td colspan="6">
{$lang['edit_go_page']} <input class="edit" style="text-align: center" name="gopage" id="gopage" value="" type="text" size="3"> <input onClick="javascript:gopage_submit(document.getElementById('gopage').value); return(false);" class="edit" type="button" value=" ok ">
</td></tr>
HTML;
			
			}
		
		}
		
		echo <<<HTML
	</table>
</td>
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
</div>
HTML;
	
	}
	
	echofooter();
}
}

if($action == "add_this_price") {
		
	echoheader( "options", $lang['mass_cat'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"cat_select\" multiple";
	else $category_multiple = "";
	
	echo <<<HTML

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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Добавление позиции в прайс лист</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100">
        
			<style>
                #prs {margin-left:100px; margin-top:20px; margin-bottom:20px;}
                #prs p {margin-right:10px;}
                #textpol {border:1px #5b8aa8 solid; margin-top:2px;}
				.ht3 {color:#0b5e92; padding-bottom:5px; margin-top:5px;}
            </style>
            
            <div id="prs">
        	
HTML;
	
			printf("
            	<form action='{$PHP_SELF}' method='post'>
                    <input type='hidden' name='nameh' value='%s'>
                    <input type='hidden' name='proizh' value='%s'>
                    <input type='hidden' name='thisid' value='%s'>
                    <table cellpadding='0' cellspacing='0' border='0'>
                        <tr>
                            <td align='right'><p>Продукция:</p></td><td><p class='ht3'>%s</p></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Производитель:</p></td><td><p class='ht3'>%s</p></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Артикул:</p></td><td><input type='text' name='artikyl' value='' id='textpol'></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>База и цвет:</p></td><td><input type='text' name='cvet' value='' id='textpol'></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Упаковка:</p></td><td><input type='text' name='pack' value='' id='textpol'></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Степень блеска:</p></td><td><input type='text' name='blesk' value='' id='textpol'></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Стоимость упаковки:</p></td><td><input type='text' name='priceyp' value='' id='textpol'></td>
                            <td></td>
                        </tr>
                            
						<tr>
                        	<td></td><td><br>
                            	<input type=hidden name=user_hash value='{$dle_login_hash}'><input type='hidden' name='action' value='mass_edit_price'>
                                <input type='hidden' name='mod' value='massactions'>
                            	<input type='submit' name='su' class='edit' value='Выполнить' />   
                            </td><td></td>
                        </tr>
                    </table>
                </form>
            ", $myrow_id['title'], $myrow_lm['name'], $_REQUEST['prid'], $myrow_id['title'], $myrow_lm['name']);
	echo <<<HTML
    		
			
			</div>
		</td>
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
</div>
HTML;
	
	echofooter();
	exit();
}

if($action == "add_this_komplekt") {
		
	echoheader( "options", $lang['mass_cat'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"cat_select\" multiple";
	else $category_multiple = "";
	

	$prid = $_REQUEST['prid'];
	$result_catpricek = $db->query("SELECT pack, id, blesk, price_litr, price_yp FROM " . PREFIX . "_price WHERE thisid='" . $prid . "' and komplekt = 'true' ORDER BY id");
	$j=0;
	while ( $myrowCatk = $db->get_array($result_catpricek) ) {
		
		if ($j==8) Break;
		$kod = $myrowCatk['id'];
			if(strlen($myrowCatk['id']) < 5){
				if(strlen($myrowCatk['id'])==1) $kod = "0000".$myrowCatk['id'];
				if(strlen($myrowCatk['id'])==2) $kod = "000".$myrowCatk['id'];
				if(strlen($myrowCatk['id'])==3) $kod = "00".$myrowCatk['id'];
				if(strlen($myrowCatk['id'])==4) $kod = "0".$myrowCatk['id'];
			} 
			
		$packint = $myrowCatk['pack'];
		
		$pos = strpos($packint, 'л');
		$pos2 = strpos($packint, 'кг');
		if ($pos !== false) $izm = ' л';
		if ($pos2 !== false) $izm = ' кг';
			
		//$packint = (int)$packint;
		$packint =str_replace(",","." ,$packint);
		settype($packint, "float");
		
		$s += $packint;
		$si = $s . $izm;
		$blesk=$myrowCatk['blesk'];
		$pack1=$myrowCatk['pack'];
		$price_yp=$myrowCatk['price_yp'];
		$price_litr = $myrowCatk['price_litr'];
		if ($pos !== false && $pos2 !== false) $price_litr =str_replace(",","" ,$price_litr); 
		else $price_litr =str_replace(",","." ,$price_litr);
		$price_litr1 = $price_litr;
		settype($price_litr, "float");
		$skomp += $packint * $price_litr;
		
		
		$packk0 .= "<tr class='pkol'>
                            <td align='right'><p>Тара: <input type='checkbox' name='tara[{$j}]' class='tara' value='true'  onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$j}]' value='$packint' class='kolsmes' onchange='komplekt();'>Код продукции($blesk): $kod</p></td><td><p>Стоимость за 1л: <span class='st1lit'>$price_litr1</span> Тара: $pack1 Стоимость уп: $price_yp </p><input type='hidden' name='kodpr[{$j}]' value='{$kod}'></td>
							<td></td>
                 </tr>";
	$j++;	
	}

		/*$pack .= "<tr class='pkol'>
                            <td align='right'><p  >Количество смеси: <input type='text' size='1' name='kolsmes[5]' value='0' id='dkolsmes' onchange='komplekt();'>Код продукции: <input type='text' size='1' name='kodpr[5]' value='' id='drkod' onchange='komplekt();'></p></td><td></td>
							<td></td>
                 </tr>";
			*/	
		$jj = $j;
		$packk1 .= "<tr class='pkol'>
                            <td align='right'><p>Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack2 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p>Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack3 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p>Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack4 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p>Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack5 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p>Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0' class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack6 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p>Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt();'><option value='0'>код пр|| за 1л</option>";
							
		
           $result1 = $db->query("SELECT pack, id, blesk, price_litr, price_yp FROM " . PREFIX . "_price ORDER BY id");
			
			while ( $myrowC = $db->get_array($result1) ) {
				$kod = $myrowC['id'];
				if(strlen($myrowC['id']) < 5){
					if(strlen($myrowC['id'])==1) $kod = "0000".$myrowC['id'];
					if(strlen($myrowC['id'])==2) $kod = "000".$myrowC['id'];
					if(strlen($myrowC['id'])==3) $kod = "00".$myrowC['id'];
					if(strlen($myrowC['id'])==4) $kod = "0".$myrowC['id'];
				} 
				$pack_ .= "<option value='".$kod.$myrowC['price_litr']."'>".$kod." || " .$myrowC['price_litr']. "</option>";
			}
		   $packq .= "</select></p></td><td></td></tr>";
		   $packk1 .= $pack_ . $packq;
		   $pack2 .= $pack_ . $packq;
		   $pack3 .= $pack_ . $packq;
		   $pack4 .= $pack_ . $packq;
		   $pack5 .= $pack_ . $packq;
		   $pack6 .= $pack_ . $packq;

		   if ($j<=2)  $pack .= $packk0 . $packk1 . $pack2 . $pack3 . $pack4 . $pack5. $pack6;
		   if ($j==3)  $pack .= $packk0 . $packk1 . $pack2 . $pack3 . $pack4 . $pack5;
		   if ($j==4)  $pack .= $packk0 . $packk1 . $pack2 . $pack3 . $pack4;
		   if ($j==5)  $pack .= $packk0 . $packk1 . $pack2 . $pack3;
		   if ($j==6)  $pack .= $packk0 . $packk1 . $pack2;
		   if ($j==7)  $pack .= $packk0 . $packk1;
		   if ($j>7)  $pack .= $packk0;
		   
		   
		   

		   		   

	echo <<<HTML
	
	<script type="text/javascript">
		function komplekt(e) {
		var c = e + 1;
		var d = "#pkol" + c;
		if (c > 0) $(d).css("display","table-row");

			var skom = 0;
			var allkols = 0;
			var kols = $(".pkol .kolsmes").get();
			var kols2 = $(".pkol .kolsmes2").get();
			var st1 = $(".pkol .st1lit").get();
			var st2 = $(".pkol .st1litr").get();
			var tar = $(".pkol .tara").get();
			var tar2 = $(".pkol .tara2").get();
			
			for (i=0; i<$(st2).size();i++) 
			{
				var ks = $(kols2[i]).val();
				if(ks.indexOf(',') + 1) {
				 ks=ks.replace(",",".");
				}
				var allkol = parseFloat(ks);
				
				var slitrv = $(st2[i]).val();
				slitrv = slitrv.substr(5,100);
				if(slitrv.indexOf(',') + 1) {
					if(slitrv.indexOf('.') + 1) {
						slitrv=slitrv.replace(",","");
					}else 
					{
						slitrv=slitrv.replace(",",".");
					}
				}
					
				slitrv = slitrv * 1;
				slitrv = parseFloat(slitrv);
				skom += allkol * slitrv;
				
				if (!$(tar2[i]).is(":checked")) 
				{
					allkols += allkol;
				}
				
			}
			
			for (i=0; i<$(kols).size();i++)
			{
				var ks = $(kols[i]).val();
				if(ks.indexOf(',') + 1) {
				 ks=ks.replace(",",".");
				}
				var allkol = parseFloat(ks);
				var slitrt = $(st1[i]).text();
				if(slitrt.indexOf(',') + 1) {
					if(slitrt.indexOf('.') + 1) {
						slitrt=slitrt.replace(",","");
					}else 
					{
						slitrv=slitrv.replace(",",".");
					}
				}				
				slitrt = slitrt * 1;
				slitrt = parseFloat(slitrt);
				skom += allkol * slitrt;
				
				if (!$(tar[i]).is(":checked")) 
				{
					allkols += allkol;
				}
				
			}
			allkols = Math.round(allkols * 100)/100;
			skom = Math.round(skom * 100)/100;
			$("#skomp").val(skom);
			var izm = $("#allkol").val();
			var izm1 = "";
			if(izm.indexOf('л') + 1) {
			izm1 = " л";
			}
			if(izm.indexOf('кг') + 1) {
			izm1 = " кг";
			}
			
			$("#allkol").val(allkols + izm1);
		}
	</script>

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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Добавление позиции в прайс лист</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100">
        
			<style>
                #prs {margin-left:100px; margin-top:20px; margin-bottom:20px;}
                #prs p {margin-right:10px;}
                #textpol {border:1px #5b8aa8 solid; margin-top:2px;}
				.ht3 {color:#0b5e92; padding-bottom:5px; margin-top:5px;}
            </style>
            
            <div id="prs">
        	
HTML;
	
			printf("
            	<form action='{$PHP_SELF}' method='post'>
                    <input type='hidden' name='nameh' value='%s'>
                    <input type='hidden' name='proizh' value='%s'>
                    <input type='hidden' name='thisid' value='%s'>
                    <table cellpadding='0' cellspacing='0' border='0'>
                        <tr>
                            <td align='right'><p>Продукция:</p></td><td><p class='ht3'>%s</p></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Производитель:</p></td><td><p class='ht3'>%s</p></td>
                            <td></td>
                        </tr>
                       $pack
                       <tr>
                            <td align='right'><p>Общее кол-во смеси:</p></td><td><input type='text' name='pack' value='$si' id='allkol'></td>
                            <td></td>
                        </tr>
                        <tr>
                       <tr>
                            <td align='right'><p>Стоимость комплекта:</p></td><td><input type='text' name='priceyp' value='$skomp' id='skomp'></td>
                            <td></td>
                        </tr>
                        <tr>
                       <tr>
                            <td align='right'><p>Артикул:</p></td><td><input type='text' name='artikyl' value='' id='textpol'></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>База и цвет:</p></td><td><input type='text' name='cvet' value='' id='textpol'></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Степень блеска:</p></td><td><input type='text' name='blesk' value='' id='textpol'></td>
                            <td></td>
                        </tr>
						<tr>
                        	<td></td><td><br>
                            	<input type=hidden name=user_hash value='{$dle_login_hash}'><input type='hidden' name='action' value='mass_edit_price'>
                                <input type='hidden' name='mod' value='massactions'>
                                <input type='hidden' name='k' value='1'>
                            	<input type='submit' name='su' class='edit' value='Выполнить' />   
                            </td><td></td>
                        </tr>
                    </table>
                </form>
            ", $myrow_id['title'], $myrow_lm['name'], $_REQUEST['prid'], $myrow_id['title'], $myrow_lm['name']);
	echo <<<HTML
    		
			
			</div>
		</td>
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
</div>
HTML;
	
	echofooter();
	exit();
}
if($action == "edit_this_komplekt") {
		
	echoheader( "options", $lang['mass_cat'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"cat_select\" multiple";
	else $category_multiple = "";
	

	$prid = $_REQUEST['prid'];
	$itid = $_REQUEST['itid'];
	$result_catpricek = $db->query("SELECT pack, id, blesk, price_litr, price_yp, kod1k, kod2k, kod3k, kod4k, kod5k, kod6k, kod7k , kod8k FROM " . PREFIX . "_price WHERE id in (SELECT kod1 FROM " . PREFIX . "_price WHERE id='" . $itid . "') or id in (SELECT kod2 FROM " . PREFIX . "_price WHERE id='" . $itid . "') or id in (SELECT kod3 FROM " . PREFIX . "_price WHERE id='" . $itid . "') or id in (SELECT kod4 FROM " . PREFIX . "_price WHERE id='" . $itid . "') or id in (SELECT kod5 FROM " . PREFIX . "_price WHERE id='" . $itid . "') or id in (SELECT kod6 FROM " . PREFIX . "_price WHERE id='" . $itid . "') or id in (SELECT kod7 FROM " . PREFIX . "_price WHERE id='" . $itid . "') or id in (SELECT kod8 FROM " . PREFIX . "_price WHERE id='" . $itid . "')");
	$result_catpricek2 = $db->query("SELECT artikyl, cvet, blesk, kod1, kod2, kod3, kod4, kod5, kod6, kod7, kod1k, kod2k, kod3k, kod4k, kod5k, kod6k, kod7k, kod8k, tara1, tara2, tara3, tara4, tara5, tara6, tara7, tara8 FROM " . PREFIX . "_price WHERE id='" . $itid . "'");
	$myrowCatk2 = $db->get_array($result_catpricek2);
	$artikyl = $myrowCatk2['artikyl'];
	$cvet = $myrowCatk2['cvet'];
	$blesk = $myrowCatk2['blesk'];
	$j=0;
	while ( $myrowCatk = $db->get_array($result_catpricek) ) {
		if ($j==8) Break;
		$kod = $myrowCatk['id'];
			if(strlen($myrowCatk['id']) < 5){
				if(strlen($myrowCatk['id'])==1) $kod = "0000".$myrowCatk['id'];
				if(strlen($myrowCatk['id'])==2) $kod = "000".$myrowCatk['id'];
				if(strlen($myrowCatk['id'])==3) $kod = "00".$myrowCatk['id'];
				if(strlen($myrowCatk['id'])==4) $kod = "0".$myrowCatk['id'];
			} 
			
		if($myrowCatk['id'] == $myrowCatk2['kod1'])  {$packint = $myrowCatk2['kod1k']; if($myrowCatk2['tara1'] == "true") { $b = "checked"; } else {$b="";} }
		if($myrowCatk['id'] == $myrowCatk2['kod2'])  {$packint = $myrowCatk2['kod2k']; if($myrowCatk2['tara2'] == "true") { $b = "checked"; } else {$b="";} }
		if($myrowCatk['id'] == $myrowCatk2['kod3'])  {$packint = $myrowCatk2['kod3k']; if($myrowCatk2['tara3'] == "true") { $b = "checked"; } else {$b="";} }
		if($myrowCatk['id'] == $myrowCatk2['kod4'])  {$packint = $myrowCatk2['kod4k']; if($myrowCatk2['tara4'] == "true") { $b = "checked"; } else {$b="";} }
		if($myrowCatk['id'] == $myrowCatk2['kod5'])  {$packint = $myrowCatk2['kod5k']; if($myrowCatk2['tara5'] == "true") { $b = "checked"; } else {$b="";} }
		if($myrowCatk['id'] == $myrowCatk2['kod6'])  {$packint = $myrowCatk2['kod6k']; if($myrowCatk2['tara6'] == "true") { $b = "checked"; } else {$b="";} }
		if($myrowCatk['id'] == $myrowCatk2['kod7'])  {$packint = $myrowCatk2['kod7k'];if($myrowCatk2['tara7'] == "true") { $b = "checked"; } else {$b="";} }
		if($myrowCatk['id'] == $myrowCatk2['kod8'])  {$packint = $myrowCatk2['kod8k']; if($myrowCatk2['tara8'] == "true") { $b = "checked"; } else {$b="";} }
			
		
		$pos = strpos($packint, 'л');
		$pos2 = strpos($packint, 'кг');
		if ($pos !== false) $izm = ' л';
		if ($pos2 !== false) $izm = ' кг';
			
		//$packint = (int)$packint;
		$packint =str_replace(",","." ,$packint);
		settype($packint, "float");
		if ($b !== "checked") $s += $packint;
		$si = $s . $izm;
		$blesk=$myrowCatk['blesk'];
		$pack1=$myrowCatk['pack'];
		$price_yp=$myrowCatk['price_yp'];
		$price_litr = $myrowCatk['price_litr'];
		$pos = strpos($price_litr, ".");
		$pos2 = strpos($price_litr, ",");
			if ($pos !== false && $pos2 !== false) $price_litr =str_replace(",","" ,$price_litr); 
			else $price_litr =str_replace(",","." ,$price_litr);
		$price_litr1 = $price_litr;
		settype($price_litr, "float");
		$skomp += $packint * $price_litr;
		
		
		$packk0 .= "<tr class='pkol'>
                            <td align='right'><p  >Тара: <input type='checkbox' name='tara[{$j}]' class='tara' value='true'  onchange='komplekt();' $b> Количество смеси: <input type='text' size='1' name='kolsmes[{$j}]' value='$packint' class='kolsmes' onchange='komplekt();'>Код продукции($blesk): $kod</p></td><td><p>Стоимость за 1л: <span class='st1lit'>$price_litr1</span> Тара: $pack1 Стоимость уп: $price_yp </p><input type='hidden' name='kodpr[{$j}]' value='{$kod}'></td>
							<td></td>
                 </tr>";
	$j++;	
	}

		/*$pack .= "<tr class='pkol'>
                            <td align='right'><p  >Количество смеси: <input type='text' size='1' name='kolsmes[5]' value='0' id='dkolsmes' onchange='komplekt();'>Код продукции: <input type='text' size='1' name='kodpr[5]' value='' id='drkod' onchange='komplekt();'></p></td><td></td>
							<td></td>
                 </tr>";
			*/	
		$jj = $j;
		$packk1 .= "<tr class='pkol'>
                            <td align='right'><p>Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack2 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p  >Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack3 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p  >Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack4 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p  >Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack5 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p  >Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0' class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt({$jj});'><option value='0'>код пр|| за 1л</option>";
							$jj++;
		$pack6 = "<tr class='pkol' style='display:none;' id='pkol{$jj}'>
                            <td align='right'><p  >Тара: <input type='checkbox' name='tara[{$jj}]' class='tara2' value='true' onchange='komplekt();'> Количество смеси: <input type='text' size='1' name='kolsmes[{$jj}]' value='0'  class='kolsmes2' onchange='komplekt();'>Код продукции: </p></td><td><p><select name='kodpr[{$jj}]' class='st1litr' onchange='komplekt();'><option value='0'>код пр|| за 1л</option>";
							
		
           $result1 = $db->query("SELECT pack, id, blesk, price_litr, price_yp FROM " . PREFIX . "_price ORDER BY id");
			
			while ( $myrowC = $db->get_array($result1) ) {
				$kod = $myrowC['id'];
				if(strlen($myrowC['id']) < 5){
					if(strlen($myrowC['id'])==1) $kod = "0000".$myrowC['id'];
					if(strlen($myrowC['id'])==2) $kod = "000".$myrowC['id'];
					if(strlen($myrowC['id'])==3) $kod = "00".$myrowC['id'];
					if(strlen($myrowC['id'])==4) $kod = "0".$myrowC['id'];
				} 
				$pack_ .= "<option value='".$kod.$myrowC['price_litr']."'>".$kod." || " .$myrowC['price_litr']. "</option>";
			}
		   $packq .= "</select></p></td><td></td></tr>";
		   $packk1 .= $pack_ . $packq;
		   $pack2 .= $pack_ . $packq;
		   $pack3 .= $pack_ . $packq;
		   $pack4 .= $pack_ . $packq;
		   $pack5 .= $pack_ . $packq;
		   $pack6 .= $pack_ . $packq;

		   if ($j<=2)  $pack .= $packk0 . $packk1 . $pack2 . $pack3 . $pack4 . $pack5. $pack6;
		   if ($j==3)  $pack .= $packk0 . $packk1 . $pack2 . $pack3 . $pack4 . $pack5;
		   if ($j==4)  $pack .= $packk0 . $packk1 . $pack2 . $pack3 . $pack4;
		   if ($j==5)  $pack .= $packk0 . $packk1 . $pack2 . $pack3;
		   if ($j==6)  $pack .= $packk0 . $packk1 . $pack2;
		   if ($j==7)  $pack .= $packk0 . $packk1;
		   if ($j>7)  $pack .= $packk0;
		   
		   
		   

		   		   

	echo <<<HTML
	
	<script type="text/javascript">
		function komplekt(e) {
		var c = e + 1;
		var d = "#pkol" + c;
		if (c > 0) $(d).css("display","table-row");
			var skom = 0;
			var allkols = 0;
			var kols = $(".pkol .kolsmes").get();
			var kols2 = $(".pkol .kolsmes2").get();
			var st1 = $(".pkol .st1lit").get();
			var st2 = $(".pkol .st1litr").get();
			var tar = $(".pkol .tara").get();
			var tar2 = $(".pkol .tara2").get();
			
			for (i=0; i<$(st2).size();i++) 
			{
				var ks = $(kols2[i]).val();
				if(ks.indexOf(',') + 1) {
				 ks=ks.replace(",",".");
				}
				var allkol = parseFloat(ks);
				
				var slitrv = $(st2[i]).val();
				slitrv = slitrv.substr(5,100);
				if(slitrv.indexOf(',') + 1) {
					if(slitrv.indexOf('.') + 1) {
						slitrv=slitrv.replace(",","");
					}else 
					{
						slitrv=slitrv.replace(",",".");
					}
				}
					
				slitrv = slitrv * 1;
				slitrv = parseFloat(slitrv);
				skom += allkol * slitrv;
				if (!$(tar2[i]).is(":checked")) 
				{
					allkols += allkol;
				}
			}
			
			for (i=0; i<$(kols).size();i++)
			{
				var ks = $(kols[i]).val();
				if(ks.indexOf(',') + 1) {
				 ks=ks.replace(",",".");
				}
				var allkol = parseFloat(ks);
				var slitrt = $(st1[i]).text();
				if(slitrt.indexOf(',') + 1) {
					if(slitrt.indexOf('.') + 1) {
						slitrt=slitrt.replace(",","");
					}else 
					{
						slitrv=slitrv.replace(",",".");
					}
				}				
				slitrt = slitrt * 1;
				slitrt = parseFloat(slitrt);
				skom += allkol * slitrt;
				if (!$(tar[i]).is(":checked")) 
				{
					allkols += allkol;
				}
			}
			allkols = Math.round(allkols * 100)/100;
			skom = Math.round(skom * 100)/100;
			$("#skomp").val(skom);
			var izm = $("#allkol").val();
			var izm1 = "";
			if(izm.indexOf('л') + 1) {
			izm1 = " л";
			}
			if(izm.indexOf('кг') + 1) {
			izm1 = " кг";
			}
			
			$("#allkol").val(allkols + izm1);
		}
	</script>

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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Добавление позиции в прайс лист</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" height="100">
        
			<style>
                #prs {margin-left:100px; margin-top:20px; margin-bottom:20px;}
                #prs p {margin-right:10px;}
                #textpol {border:1px #5b8aa8 solid; margin-top:2px;}
				.ht3 {color:#0b5e92; padding-bottom:5px; margin-top:5px;}
            </style>
            
            <div id="prs">
        	
HTML;
	
			printf("
            	<form action='{$PHP_SELF}' method='post'>
                    <input type='hidden' name='nameh' value='%s'>
                    <input type='hidden' name='proizh' value='%s'>
                    <input type='hidden' name='thisid' value='%s'>
                    <table cellpadding='0' cellspacing='0' border='0'>
                        <tr>
                            <td align='right'><p>Продукция:</p></td><td><p class='ht3'>%s</p></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Производитель:</p></td><td><p class='ht3'>%s</p></td>
                            <td></td>
                        </tr>
                       $pack
                       <tr>
                            <td align='right'><p>Общее кол-во смеси:</p></td><td><input type='text' name='pack' value='$si' id='allkol'></td>
                            <td></td>
                        </tr>
                        <tr>
                       <tr>
                            <td align='right'><p>Стоимость комплекта:</p></td><td><input type='text' name='priceyp' value='$skomp' id='skomp'></td>
                            <td></td>
                        </tr>
                        <tr>
                       <tr>
                            <td align='right'><p>Артикул:</p></td><td><input type='text' name='artikyl' value='$artikyl' id='textpol'></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>База и цвет:</p></td><td><input type='text' name='cvet' value='$cvet' id='textpol'></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align='right'><p>Степень блеска:</p></td><td><input type='text' name='blesk' value='$blesk' id='textpol'></td>
                            <td></td>
                        </tr>
						<tr>
                        	<td></td><td><br>
                            	<input type=hidden name=user_hash value='{$dle_login_hash}'><input type='hidden' name='action' value='mass_edit_price'>
                                <input type='hidden' name='mod' value='massactions'>
                                <input type='hidden' name='itid' value='{$itid}'>
                            	<input type='submit' name='suedit' class='edit' value='Выполнить' />   
                            </td><td></td>
                        </tr>
                    </table>
                </form>
            ", $myrow_id['title'], $myrow_lm['name'], $_REQUEST['prid'], $myrow_id['title'], $myrow_lm['name']);
	echo <<<HTML
    		
			
			</div>
		</td>
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
</div>
HTML;
	
	echofooter();
	exit();
}




?>