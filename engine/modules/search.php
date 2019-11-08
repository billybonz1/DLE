<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004,2011 SoftNews Media Group
=====================================================
 ������ ��� ������� ���������� �������
=====================================================
 ����: search.php
-----------------------------------------------------
 ����������: ����� �� �����
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['allow_search'] ) {
	
	$lang['search_denied'] = str_replace( '{group}', $user_group[$member_id['user_group']]['group_name'], $lang['search_denied'] );
	msgbox( $lang['all_info'], $lang['search_denied'] );

} else {
	
	function strip_data($text) {
		$quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "'", ",", "/", ";", ":", "@", "[", "]", "{", "}", "=", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"' );
		$goodquotes = array ("-", "+", "#" );
		$repquotes = array ("\-", "\+", "\#" );
		$text = stripslashes( $text );
		$text = trim( strip_tags( $text ) );
		$text = str_replace( $quotes, '', $text );
		$text = str_replace( $goodquotes, $repquotes, $text );
		return $text;
	}
	
	$count_result = 0;
	$sql_count = "";
	$sql_find = "";

	$tpl->load_template( 'search.tpl' );
	
	// ����������� ���������� ������ �� ��������
	$config['result_num_def'] = $config['news_number'] * 2;
	// ������������ ���������� ������ �� ��������
	$config['result_num_max'] = $config['result_num_def'] * 10;
	// ����������� ���������� �������� � ����� ������
	$config['search_length_min'] = 3;
	// �������� �����, ����������� ���������� ������ �� �������� � ������ �� 3-� ������ - ���������, ��������, ��������
	// �������� �����, ����������� ���������� ������ �� �������� � ������
	$config['pages_per_section'] = 7;
	
	$this_date = date( "Y-m-d H:i:s", $_TIME );
	if( intval( $config['no_date'] ) ) $this_date = " AND " . PREFIX . "_post.date < '" . $this_date . "'"; else $this_date = "";
	
	if( isset( $_REQUEST['story'] ) ) $story = dle_substr( strip_data( $_REQUEST['story'] ), 0, 90, $config['charset'] ); else $story = "";
	if( isset( $_REQUEST['search_start'] ) ) $search_start = intval( $_REQUEST['search_start'] ); else $search_start = 0;
	if( isset( $_REQUEST['titleonly'] ) ) $titleonly = intval( $_REQUEST['titleonly'] ); else $titleonly = 0;
	if( isset( $_REQUEST['searchuser'] ) ) $searchuser = dle_substr( strip_data( $_REQUEST['searchuser'] ), 0, 40, $config['charset'] ); else $searchuser = "";
	if( isset( $_REQUEST['exactname'] ) ) $exactname = $_REQUEST['exactname']; else $exactname = "";
	if( isset( $_REQUEST['postonly'] ) ) $postonly = intval( $_REQUEST['postonly'] ); else $postonly = 0;
	if( isset( $_REQUEST['replyless'] ) ) $replyless = intval( $_REQUEST['replyless'] ); else $replyless = 0;
	if( isset( $_REQUEST['replylimit'] ) ) $replylimit = intval( $_REQUEST['replylimit'] ); else $replylimit = 0;
	if( isset( $_REQUEST['searchdate'] ) ) $searchdate = intval( $_REQUEST['searchdate'] ); else $searchdate = 0;
	if( isset( $_REQUEST['beforeafter'] ) ) $beforeafter = strip_data( $_REQUEST['beforeafter'] ); else $beforeafter = "after";
	if( isset( $_REQUEST['sortby'] ) ) $sortby = strip_data( $_REQUEST['sortby'] ); else $sortby = "date";
	if( isset( $_REQUEST['resorder'] ) ) $resorder = strip_data( $_REQUEST['resorder'] ); else $resorder = "desc";
	if( isset( $_REQUEST['showposts'] ) ) $showposts = intval( $_REQUEST['showposts'] ); else $showposts = 0;
	if( isset( $_REQUEST['result_num'] ) ) $result_num = intval( $_REQUEST['result_num'] ); else $result_num = $config['result_num_def'];
	if( isset( $_REQUEST['result_from'] ) ) $result_from = intval( $_REQUEST['result_from'] ); else $result_from = 1; // �������� �������� � ����������� � ���
	if( isset( $_REQUEST['catlist'] ) ) $category_list = $db->safesql( @implode( ',', $_REQUEST['catlist'] ) ); else $category_list = "";
	$full_search = intval( $_REQUEST['full_search'] );
	
	$story = preg_replace( "#^(\s*OR\s+)*#i", '', $story );
	$story = preg_replace( "#(\s+OR\s*)*$#i", '', $story );

	$findstory = stripslashes( $story ); // ��� ������ � ���� ������
	
	$story = $db->safesql($story);

	if( empty( $story ) and ! empty( $searchuser ) ) $story = "___SEARCH___ALL___"; // ��� ������ ���� ������
	if( $search_start < 0 ) $search_start = 0; // ��������� �������� ������
	if( $titleonly < 0 or $titleonly > 6 ) $titleonly = 0; // ������ � ����������, �������, �������������� �����, ������������ ��� �����
	if( $postonly < 0 or $postonly > 2 ) $postonly = 0; // ����� ������, ��������� �������������
	if( $replyless < 0 or $replyless > 1 ) $replyless = 0; // ������ ������ ��� ������ �������
	if( $replylimit < 0 ) $replylimit = 0; // ����� �������
	if( $showposts < 0 or $showposts > 1 ) $showposts = 0; // ������ � ������� ��� ������������ �����
	if( $result_num < 1 ) $result_num = $config['result_num_def']; // ���������� ����������� �� ��������
	if( $result_num > $config['result_num_max'] ) $result_num = $config['result_num_max'];
	$config_search_numbers = $result_num;
	
	$listdate = array (0, - 1, 1, 7, 14, 30, 90, 180, 365 ); // ������ �� ������ ��� ����
	if( ! (in_array( $searchdate, $listdate )) ) $searchdate = 0;
	if( $beforeafter != "after" and $beforeafter != "before" ) $beforeafter = "after"; // ������ �� ��� ����� ������� ����
	$listsortby = array ("date", "title", "comm_num", "news_read", "autor", "category", "rating" );
	if( ! (in_array( $sortby, $listsortby )) ) $sortby = "date"; // ����������� �� �����
	$listresorder = array ("desc", "asc" );
	if( ! (in_array( $resorder, $listresorder )) ) $resorder = "desc"; // ����������� �� ������������ ��� ���������
	

	// ����������� ��������� ����� �����, ���������� � �����
	$titleonly_sel = array ('0' => '', '1' => '', '2' => '', '3' => '', '4' => '', '5' => '' );
	$titleonly_sel[$titleonly] = 'selected="selected"';
	$postonly_sel = array ('0' => '', '1' => '', '2' => '' );
	$postonly_sel[$postonly] = 'selected="selected"';
	$replyless_sel = array ('0' => '', '1' => '' );
	$replyless_sel[$replyless] = 'selected="selected"';
	$searchdate_sel = array ('0' => '', '-1' => '', '1' => '', '7' => '', '14' => '', '30' => '', '90' => '', '180' => '', '365' => '' );
	$searchdate_sel[$searchdate] = 'selected="selected"';
	$beforeafter_sel = array ('after' => '', 'before' => '' );
	$beforeafter_sel[$beforeafter] = 'selected="selected"';
	$sortby_sel = array ('date' => '', 'title' => '', 'comm_num' => '', 'news_read' => '', 'autor' => '', 'category' => '', 'rating' => '' );
	$sortby_sel[$sortby] = 'selected="selected"';
	$resorder_sel = array ('desc' => '', 'asc' => '' );
	$resorder_sel[$resorder] = 'selected="selected"';
	$showposts_sel = array ('0' => '', '1' => '' );
	$showposts_sel[$showposts] = 'checked="checked"';
	if( $exactname == "yes" ) $exactname_sel = 'checked="checked"';
	else $exactname_sel = '';
	
	// ����� ����� ������
	if( $category_list == "" or $category_list == "0" ) {
		$catselall = "selected=\"selected\"";
	} else {
		$catselall = "";
		$category_list = preg_replace( "/^0\,/", '', $category_list );
	}
	
	// ����������� � ����� ��������� ���������
	$cats = "<select class=\"rating\" style=\"width:95%;height:200px;\" name=\"catlist[]\" size=\"13\" multiple=\"multiple\">";
	$cats .= "<option " . $catselall . " value=\"0\">" . $lang['s_allcat'] . "</option>";
	$cats .= CategoryNewsSelection( explode( ',', $category_list ), 0, false );
	$cats .= "</select>";
	
	$tpl->copy_template .= <<<HTML
<script type="text/javascript" language="javascript">
<!-- begin
function clearform(frmname){
  var frm = document.getElementById(frmname);
  for (var i=0;i<frm.length;i++) {
    var el=frm.elements[i];
    if (el.type=="checkbox" || el.type=="radio") {
    	if (el.name=='showposts') {document.getElementById('rb_showposts_0').checked=1; } else {el.checked=0; }
    }
    if ((el.type=="text") || (el.type=="textarea") || (el.type == "password")) { el.value=""; continue; }
    if ((el.type=="select-one") || (el.type=="select-multiple")) { el.selectedIndex=0; }
  }
  document.getElementById('result_num').value = "{$config['result_num_def']}";
  document.getElementById('replylimit').value = 0;
  document.getElementById('search_start').value = 0;
  document.getElementById('result_from').value = 1;
}
function list_submit(prm){
  var frm = document.getElementById('fullsearch');
	if (prm == -1) {
		prm=Math.ceil(frm.result_from.value / frm.result_num.value);
	} else {
		frm.result_from.value=(prm-1) * frm.result_num.value + 1;
	}
	frm.search_start.value=prm;

  frm.submit();
  return false;
}
function full_submit(prm){
    document.getElementById('fullsearch').full_search.value=prm;
    list_submit(-1);
}
function reg_keys(key) {
	var code;
	if (!key) var key = window.event;
	if (key.keyCode) code = key.keyCode;
	else if (key.which) code = key.which;

	if (code == 13) {
		list_submit(-1);
	}
};

document.onkeydown = reg_keys;
// end -->
</script>
HTML;
	
	$searchtable = <<<HTML
<form name="fullsearch" id="fullsearch" action="{$config['http_home_url']}index.php?do=search" method="post">
<input type="hidden" name="do" id="do" value="search" />
<input type="hidden" name="subaction" id="subaction" value="search" />
<input type="hidden" name="search_start" id="search_start" value="$search_start" />
<input type="hidden" name="full_search" id="full_search" value="$full_search" />
HTML;
	
	if( $full_search ) {
		
		$searchtable .= <<<HTML
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td class="search">
      <div align="center">
        <table cellpadding="0" cellspacing="2" width="100%">

        <tr style="vertical-align: top;">
				<td class="search">
					<fieldset style="margin:0px">
						<legend>{$lang['s_con']}</legend>
						<table cellpadding="0" cellspacing="3" border="0">
						<tr>
						<td class="search">
							<div>{$lang['s_word']}</div>
							<div><input type="text" name="story" size="35" id="searchinput" value="$findstory" class="textin" style="width:250px" /></div>
						</td>
						</tr>
						<tr>
						<td class="search">
							<select class="textin" name="titleonly" id="titleonly">
								<option {$titleonly_sel['0']} value="0">{$lang['s_ncom']}</option>
								<option {$titleonly_sel['1']} value="1">{$lang['s_ncom1']}</option>
                                <option {$titleonly_sel['6']} value="6">{$lang['s_static']}</option>
								<option {$titleonly_sel['3']} value="3">{$lang['s_tnews']}</option>
							</select>
						</td>
						</tr>
						</table>
					</fieldset>
				</td>

				<td class="search" valign="top">					
					<fieldset style="margin:0px">
						<legend>{$lang['s_mname']}</legend>
						<table cellpadding="0" cellspacing="3" border="0">
						<tr>
						<td class="search">
							<div>{$lang['s_fname']}</div>
							<div id="userfield"><input type="text" name="searchuser" id="searchuser" size="35"  value="$searchuser" class="textin" style="width:250px" /><br /><label for="exactname"><input type="checkbox" name="exactname" value="yes" id="exactname" {$exactname_sel} />{$lang['s_fgname']}</label>
							</div>
						</td>
						</tr>
						</table>
					</fieldset>
				</td>
				</tr>

				<tr style="vertical-align: top;">

				<td width="50%" class="search">
					<fieldset style="margin:0px">
						<legend>{$lang['s_fart']}</legend>
						<div style="padding:3px">
							<select class="textin" name="replyless" id="replyless" style="width:200px">
								<option {$replyless_sel['0']} value="0">{$lang['s_fmin']}</option>
								<option {$replyless_sel['1']} value="1">{$lang['s_fmax']}</option>
							</select>
							<input type="text" name="replylimit" id="replylimit" size="5" value="$replylimit" class="textin" /> {$lang['s_wcomm']}
						</div>
					</fieldset>

					<fieldset style="padding-top:10px">
						<legend>{$lang['s_fdaten']}</legend>

						<div style="padding:3px">					
							<select name="searchdate" id="searchdate" class="textin" style="width:200px">
								<option {$searchdate_sel['0']} value="0">{$lang['s_tall']}</option>
								<option {$searchdate_sel['-1']} value="-1">{$lang['s_tlast']}</option>
								<option {$searchdate_sel['1']} value="1">{$lang['s_tday']}</option>
								<option {$searchdate_sel['7']} value="7">{$lang['s_tweek']}</option>
								<option {$searchdate_sel['14']} value="14">{$lang['s_ttweek']}</option>
								<option {$searchdate_sel['30']} value="30">{$lang['s_tmoth']}</option>
								<option {$searchdate_sel['90']} value="90">{$lang['s_tfmoth']}</option>
								<option {$searchdate_sel['180']} value="180">{$lang['s_tsmoth']}</option>
								<option {$searchdate_sel['365']} value="365">{$lang['s_tyear']}</option>
							</select>
							<select name="beforeafter" id="beforeafter" class="textin">
								<option {$beforeafter_sel['after']} value="after">{$lang['s_fnew']}</option>
								<option {$beforeafter_sel['before']} value="before">{$lang['s_falt']}</option>
							</select>
						</div>
					</fieldset>

					<fieldset style="padding-top:10px">
						<legend>{$lang['s_fsoft']}</legend>
							<div style="padding:3px">
								<select name="sortby" id="sortby" class="textin" style="width:200px">
									<option {$sortby_sel['date']} value="date" selected="selected">{$lang['s_fsdate']}</option>
									<option {$sortby_sel['title']} value="title" >{$lang['s_fstitle']}</option>
									<option {$sortby_sel['comm_num']} value="comm_num" >{$lang['s_fscnum']}</option>
									<option {$sortby_sel['news_read']} value="news_read" >{$lang['s_fsnnum']}</option>
									<option {$sortby_sel['autor']} value="autor" >{$lang['s_fsaut']}</option>
									<option {$sortby_sel['category']} value="category" >{$lang['s_fscat']}</option>
									<option {$sortby_sel['rating']} value="rating" >{$lang['s_fsrate']}</option>
								</select>
								<select name="resorder" id="resorder" class="textin">
									<option {$resorder_sel['desc']} value="desc">{$lang['s_fsdesc']}</option>
									<option {$resorder_sel['asc']} value="asc">{$lang['s_fsasc']}</option>
								</select>
							</div>
					</fieldset>

					<fieldset style="padding-top:10px">
						<legend>{$lang['s_vlegend']}</legend>

						<table cellpadding="0" cellspacing="3" border="0">
						<tr align="left" valign="middle">
						<td class="search">
							<span>{$lang['s_vnum']} </span><input type="text" name="result_num" id="result_num" size="3" value="$result_num" class="textin" />
						</td>
						<td align="right" class="search">{$lang['s_vwie']}</td>
						</tr>
						<tr align="left" valign="middle">
						<td class="search">
							<span>{$lang['s_vjump']} </span><input type="text" name="result_from" id="result_from" size="6" value="$result_from" class="textin" />
						</td>
						<td align="right" class="search">
							<label for="rb_showposts_0"><input type="radio" name="showposts" value="0" id="rb_showposts_0" {$showposts_sel['0']} />{$lang['s_vnews']}</label>
							<label for="rb_showposts_1"><input type="radio" name="showposts" value="1" id="rb_showposts_1" {$showposts_sel['1']} />{$lang['s_vtitle']}</label>
						</td>
						</tr>

						</table>
					</fieldset>
				</td>

				<td width="50%" class="search" valign="top">
					<fieldset style="margin:0px">
						<legend>{$lang['s_fcats']}</legend>
							<div style="padding:3px">
								<div>$cats</div>
							</div>

					</fieldset>
				</td>
				</tr>

        <tr>
                <td class="search" colspan="2">
                    <div style="margin-top:6px">
                        <input type="button" class="bbcodes" style="margin:0px 20px 0 0px;" name="dosearch" id="dosearch" value="{$lang['s_fstart']}" onclick="javascript:list_submit(-1); return false;" />
                        <input type="button" class="bbcodes" style="margin:0px 20px 0 20px;" name="doclear" id="doclear" value="{$lang['s_fstop']}" onclick="javascript:clearform('fullsearch'); return false;" />
                        <input type="reset" class="bbcodes" style="margin:0px 20px 0 20px;" name="doreset" id="doreset" value="{$lang['s_freset']}" />
                    </div>

                </td>
                </tr>

        </table>
      </div>
    </td>
  </tr>
</table>
HTML;
	
	} else {

	if ( $smartphone_detected ) {

		$link_full_search = "";

	} else {

		$link_full_search = "<input type=\"button\" class=\"bbcodes\" name=\"dofullsearch\" id=\"dofullsearch\" value=\"{$lang['s_ffullstart']}\" onclick=\"javascript:full_submit(1); return false;\" />";

	}
		
		$searchtable .= <<<HTML
<input type="hidden" name="result_from" id="result_from" value="$result_from" />
<input type="hidden" name="result_num" id="result_num" value="$result_num" />

<table cellpadding="4" cellspacing="0" width="100%">
  <tr>
    <td class="search">
      <div style="margin:10px;">
                <input type="text" name="story" id="searchinput" value="$findstory" class="textin" style="width:250px" /><br /><br />
                <input type="button" class="bbcodes" name="dosearch" id="dosearch" value="{$lang['s_fstart']}" onclick="javascript:list_submit(-1); return false;" />
                {$link_full_search}
            </div>

        </td>
    </tr>
</table>
HTML;
	
	}
	
	$searchtable .= <<<HTML

</form>
HTML;
	
	$tpl->set( '{searchtable}', $searchtable );
	// �� ���������, ��������� ������ ����� ������
	if( $subaction != "search" ) {
		$tpl->set_block( "'\[searchmsg\](.*?)\[/searchmsg\]'si", "" );
		$tpl->compile( 'content' );
	}
	// ����� ������ ����� ������
	

	if( $subaction == "search" ) {
		// ����� ����������� ������
		

		$story = preg_replace( "#\s+OR\s+#i", '__OR__', trim( $story ) );
		$storywords = explode( "__OR__", $story );
		
		$story = preg_replace( "#(\s+|__OR__)#i", '%', $story );
		
		$arr = explode( '%', $story );
		$story_maxlen = 0;
		foreach ( $arr as $word ) {
			$wordlen = strlen( trim( $word ) );
			if( $wordlen > $story_maxlen ) {
				$story_maxlen = $wordlen;
			}
		}
		
		if( (empty( $story ) or ($story_maxlen < $config['search_length_min'])) and (empty( $searchuser ) or (strlen( $searchuser ) < $config['search_length_min'])) ) {
			
			msgbox( $lang['all_info'], $lang['search_err_3'] );
			
			$tpl->set( '{searchmsg}', '' );
			$tpl->set_block( "'\[searchmsg\](.*?)\[/searchmsg\]'si", "" );
			$tpl->compile( 'content' );
		
		} else {
			// ������ ���������� ������
			if( $search_start ) {
				$search_start = $search_start - 1;
				$search_start = $search_start * $config_search_numbers;
			}
			
			// �������� ����������� ��������� �� ������ ��������� ���������
			$allow_cats = $user_group[$member_id['user_group']]['allow_cats'];
			$allow_list = explode( ',', $allow_cats );
			$stop_list = "";
			if( $allow_list[0] == "all" ) {
				// ��� ��������� �������� ��� ������
				if( $category_list == "" or $category_list == "0" ) {
					// ������ ����� �� ���� ����������
					;
				} else {
					// ������ ����� �� ��������� ����������
					$stop_list = str_replace( ',', '|', $category_list );
				}
			} else {
				// �� ��� ��������� �������� ��� ������
				if( $category_list == "" or $category_list == "0" ) {
					// ������ ����� �� ���� ����������
					$stop_list = str_replace( ',', '|', $allow_cats );
				} else {
					// ������ ����� �� ��������� ����������
					$cats_list = explode( ',', $category_list );
					foreach ( $cats_list as $id ) {
						if( in_array( $id, $allow_list ) ) $stop_list .= $id . '|';
					}
					$stop_list = substr( $stop_list, 0, strlen( $stop_list ) - 1 );
				}
			}
			// ����������� �� ����������
			$where_category = "";
			if( ! empty( $stop_list ) ) {
				
				if( $config['allow_multi_category'] ) {
					
					$where_category = "category regexp '[[:<:]](" . $stop_list . ")[[:>:]]'";
				
				} else {
					
					$stop_list = str_replace( "|", "','", $stop_list );
					$where_category = "category IN ('" . $stop_list . "')";
				
				}
			}
			
			if( $story == "___SEARCH___ALL___" ) $story = '';
			$thistime = date( "Y-m-d H:i:s", (time() + $config['date_adjust'] * 60) );
			
			if( $exactname == 'yes' ) $likename = '';
			else $likename = '%';
			if( $searchdate != '0' ) {
				if( $searchdate != '-1' ) {
					$qdate = date( "Y-m-d H:i:s", (time() + $config['date_adjust'] * 60 - $searchdate * 86400) );
				} else {
					if( $is_logged and isset( $_SESSION['member_lasttime'] ) ) $qdate = date( "Y-m-d H:i:s", $_SESSION['member_lasttime'] );
					else $qdate = $thistime;
				}
			}
			
			// ����� �� ������ ������ ��� �����������
			$autor_posts = '';
			$autor_comms = '';
			if( ! empty( $searchuser ) ) {
				switch ($titleonly) {
					case 2 :
						// ������ � ������� � ������������
						$autor_posts = PREFIX . "_post.autor like '$searchuser$likename'";
						$autor_comms = PREFIX . "_comments.autor like '$searchuser$likename'";
						break;
					case 0 :
						// ������ ������ � �������
						$autor_posts = PREFIX . "_post.autor like '$searchuser$likename'";
						break;
					case 1 :
						// ������ ������ � ������������
						$autor_comms = PREFIX . "_comments.autor like '$searchuser$likename'";
						break;
				}
			}
			
			$where_reply = "";
			if( ! empty( $replylimit ) ) {
				if( $replyless == 0 ) $where_reply = PREFIX . "_post.comm_num >= '" . $replylimit . "'";
				else $where_reply = PREFIX . "_post.comm_num <= '" . $replylimit . "'";
			}
			
			// ����� �� �������� ������
			if( ! empty( $story ) ) {
				$titleonly_where = array ('0' => "short_story LIKE '%{story}%' OR full_story LIKE '%{story}%' OR " . PREFIX . "_post.xfields LIKE '%{story}%' OR title LIKE '%{story}%'", // ������ ������ � �������
										  '1' => "text LIKE '%{story}%'", // ������ ������ � ������������
										  '2' => "short_story LIKE '%{story}%' OR full_story LIKE '%{story}%' OR " . PREFIX . "_post.xfields LIKE '%{story}%' OR title LIKE '%{story}%'", // ������ � ������� � ������������
										  '3' => "title LIKE '%{story}%'", // ������ ������ � ���������� ������
										  '5' => "id LIKE '{story}%'", // ������ �� ������ ������
										  '6' => PREFIX . "_static.template LIKE '%{story}%'" ); // ������ ������ � ����������� ���������
				
				foreach ( $titleonly_where as $name => $value ) {
					$value2 = '';
					foreach ( $storywords as $words ) {
						$words = preg_replace( "#\s+#i", '%', $words );
						$value2 .= str_replace( "{story}", $words, $value );
						$value2 .= " OR ";
					}
					$value2 = preg_replace( "# OR $#i", '', $value2 );
					$titleonly_where[$name] = $value2;
				}
			}
			
			// ����� �� �������
			if( in_array( $titleonly, array (0, 2, 3 ) ) ) {
				$where_posts = "WHERE " . PREFIX . "_post.approve" . $this_date;
				if( ! empty( $where_category ) ) $where_posts .= " AND " . $where_category;
				if( ! empty( $story ) ) $where_posts .= " AND (" . $titleonly_where[$titleonly] . ")";
				if( ! empty( $autor_posts ) ) $where_posts .= " AND " . $autor_posts;
				$sdate = PREFIX . "_post.date";
				if( $searchdate != '0' ) {
					if( $beforeafter == 'before' ) $where_date = $sdate . " < '" . $qdate . "'";
					else $where_date = $sdate . " between '" . $qdate . "' and '" . $thistime . "'";
					$where_posts .= " AND " . $where_date;
				}
				if( ! empty( $where_reply ) ) $where_posts .= " AND " . $where_reply;
				$where = $where_posts;
				$posts_fields = "SELECT SQL_NO_CACHE id, autor, " . PREFIX . "_post.date AS newsdate, " . PREFIX . "_post.date AS date, short_story AS story, " . PREFIX . "_post.xfields AS xfields, title, descr, keywords, category, alt_name, comm_num AS comm_in_news, allow_comm, rating, news_read, flag, editdate, editor, reason, view_edit, tags, '' AS output_comms";
				$posts_from = "FROM " . PREFIX . "_post";
				$sql_fields = $posts_fields;
				$sql_find = "$sql_fields $posts_from $where";
				$posts_count = "SELECT SQL_NO_CACHE COUNT(*) AS count $posts_from $where";
				$sql_count = $posts_count;
			}
			// ����� �� ������������
			if( $titleonly == 1 or $titleonly == 2 ) {
				$where_comms = "WHERE " . PREFIX . "_post.approve" . $this_date;
				if( ! empty( $where_category ) ) $where_comms .= " AND " . $where_category;
				if( ! empty( $story ) ) $where_comms .= " AND (" . $titleonly_where['1'] . ")";
				if( ! empty( $autor_comms ) ) $where_comms .= " AND " . $autor_comms;
				$sdate = PREFIX . "_comments.date";
				if( $searchdate != '0' ) {
					if( $beforeafter == 'before' ) $where_date = $sdate . " < '" . $qdate . "'";
					else $where_date = $sdate . " between '" . $qdate . "' and '" . $thistime . "'";
					$where_comms .= " AND " . $where_date;
				}
				if( ! empty( $where_reply ) ) $where_comms .= " AND " . $where_reply;
				$where = $where_comms;
				$comms_fields = "SELECT SQL_NO_CACHE " . PREFIX . "_comments.id AS coms_id, post_id AS id, " . PREFIX . "_comments.date, " . PREFIX . "_comments.autor AS autor, " . PREFIX . "_comments.email AS gast_email, " . PREFIX . "_comments.text AS story, ip, is_register, name, " . USERPREFIX . "_users.email, news_num, " . USERPREFIX . "_users.comm_num, reg_date, banned, signature, foto, fullname, land, icq, " . PREFIX . "_post.date AS newsdate, " . PREFIX . "_post.title, " . PREFIX . "_post.category, " . PREFIX . "_post.alt_name, " . PREFIX . "_post.comm_num AS comm_in_news, " . PREFIX . "_post.allow_comm, " . PREFIX . "_post.rating, " . PREFIX . "_post.news_read, '1' AS output_comms, " . PREFIX . "_post.flag";
				$comms_from = "FROM " . PREFIX . "_comments LEFT JOIN " . PREFIX . "_post ON " . PREFIX . "_comments.post_id=" . PREFIX . "_post.id LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id";
				$sql_fields = $comms_fields;
				$sql_find = "$sql_fields $comms_from $where";
				$comms_count = "SELECT SQL_NO_CACHE COUNT(*) AS count $comms_from $where";
				$sql_count = $comms_count;
			}
			
			$order_by = $sortby . " " . $resorder;
			
			// ����� � ����������� ���������
			if( $titleonly == 6 ) {
				$sql_from = "FROM " . PREFIX . "_static";
				$sql_fields = "SELECT SQL_NO_CACHE id, name AS static_name, descr AS title, template AS story, allow_template, grouplevel, date, views";
				if ( $titleonly_where[$titleonly] )	$where = "WHERE " . $titleonly_where[$titleonly];
				else $where = "";
				$sql_find = "$sql_fields $sql_from $where";
				$sql_count = "SELECT SQL_NO_CACHE COUNT(*) AS count $sql_from $where";
				$order_by = "id";
			}
			
			// ------ ������ � ����
	
			if ( $sql_count ) {

				$result_count = $db->super_query( $sql_count, true );
				$count_result = $result_count[0]['count'] + $result_count[1]['count'];
				if( $count_result > ($config_search_numbers * 5) ) $count_result = ($config_search_numbers * 5);

			} else die("URL Not correct");

			
			$min_search = (@ceil( $count_result / $config_search_numbers ) - 1) * $config_search_numbers;
			
			if( $min_search < 0 ) $min_search = 0;
			if( $search_start > $min_search ) {
				$search_start = $min_search;
			}
			$from_num = $search_start + 1;
			
			$sql_request = "$sql_find ORDER BY $order_by LIMIT $search_start,$config_search_numbers";
			
			$sql_result = $db->query( $sql_request );
			$found_result = $db->num_rows( $sql_result );
			
			// �� �������
			if( ! $found_result ) {
				msgbox( $lang['all_info'], $lang[search_err_2] );
				$tpl->set( '{searchmsg}', '' );
				$tpl->set_block( "'\[searchmsg\](.*?)\[/searchmsg\]'si", "" );
				$tpl->compile( 'content' );
			} else {
				$to_num = $search_start + $found_result;
				
				// ����� ���������� � ���������� ��������� �����������
				$searchmsg = "$lang[search_ok] " . $count_result . " $lang[search_ok_1] ($lang[search_ok_2] " . $from_num . " - " . $to_num . ") :";
				$tpl->set( '{searchmsg}', $searchmsg );
				$tpl->set( '[searchmsg]', "" );
				$tpl->set( '[/searchmsg]', "" );
				$tpl->compile( 'content' );
				
				$tpl->load_template( 'searchresult.tpl' );
				$xfields = xfieldsload();
				
				function hilites($search, $txt) {
					
					$r = preg_split( '((>)|(<))', $txt, - 1, PREG_SPLIT_DELIM_CAPTURE );
					
					for($i = 0; $i < count( $r ); $i ++) {
						if( $r[$i] == "<" ) {
							$i ++;
							continue;
						}
						$r[$i] = preg_replace( "#($search)#i", "<span style='background-color:yellow;'><font color='red'>\\1</font></span>", $r[$i] );
					}
					return join( "", $r );
				}
				
				// ����� ������ ������ ��� ����������� �� ����������� ��������� ��� ������ ������ ����������
				function create_description($txt) {
					$fastquotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r" );
					$quotes = array ('"', "'" );
					$maxchr = 80;
					$txt = preg_replace( "/\[hide\](.*?)\[\/hide\]/ims", "", $txt );
					$txt = stripslashes( $txt );
					$txt = trim( strip_tags( $txt ) );
					$txt = str_replace( $fastquotes, ' ', $txt );
					$txt = str_replace( $quotes, '', $txt );
					$txt = preg_replace( "#\s+#i", ' ', $txt );
					$txt = substr( $txt, 0, 300 );
					$txt = wordwrap( $txt, $maxchr, "  " );
					return $txt;
				}
				
				// ����� ����������� ������
				$search_id = $search_start;
				while ( $row = $db->get_row( $sql_result ) ) {
					
					// ���������� ����� ���������� ������
					$search_id ++;
					
					$attachments[] = $row['id'];
					if( $titleonly != 6 ) {
						$row['newsdate'] = strtotime( $row['newsdate'] );
						$row['date'] = strtotime( $row['date'] );
					}

					$row['story'] = stripslashes( $row['story'] );

					if( $user_group[$member_id['user_group']]['allow_hide'] ) $row['story'] = preg_replace( "'\[hide\](.*?)\[/hide\]'si", "\\1", $row['story']);
					else $row['story'] = preg_replace ( "'\[hide\](.*?)\[/hide\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $row['story'] );
					
					$arr = explode( "%", $story );
					
					foreach ( $arr as $word ) {
						if( strlen( trim( $word ) ) >= $config['search_length_min'] ) {
							$row['story'] = hilites( $word, $row['story'] );
						}
						;
					}
					
					if( $titleonly == 6 ) {
						// ���������� ������ � ����������� ���������
						$row['grouplevel'] = explode( ',', $row['grouplevel'] );
						if( $row['grouplevel'][0] != "all" and ! in_array( $member_id['user_group'], $row['grouplevel'] ) ) {
							$tpl->result['content'] .= $lang['static_denied'];
						} else {
							
							$row['story'] = stripslashes( $row['story'] );

							$news_seiten = explode( "{PAGEBREAK}", $row['story'] );
							$anzahl_seiten = count( $news_seiten );

							$row['story'] = $news_seiten[0];

							$news_seiten = "";
							unset( $news_seiten );

							if( $anzahl_seiten > 1 ) {

								if( $config['allow_alt_url'] == "yes" ) {
									$replacepage = "<a href=\"" . $config['http_home_url'] . "page," . "\\1" . "," . $row['static_name'] . ".html\">\\2</a>";
								} else {
									$replacepage = "<a href=\"$PHP_SELF?do=static&page=" . $row['static_name'] . "&news_page=\\1\">\\2</a>";
								}

								$row['story'] = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", $replacepage, $row['story'] );

							} else {
								
								$row['story'] = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "", $row['story'] );
							
							}
	
							$title = stripslashes( strip_tags( $row['title'] ) );
							
							if( $row['allow_template'] ) {
								$tpl->load_template( 'static.tpl' );
								if( $config['allow_alt_url'] == "yes" ) $static_descr = "<a title=\"" . $title . "\" href=\"" . $config['http_home_url'] . $row['static_name'] . ".html\" >" . $title . "</a>";
								else $static_descr = "<a title=\"" . $title . "\" href=\"$PHP_SELF?do=static&page=" . $row['static_name'] . "\" >" . $title . "</a>";
								$tpl->set( '{description}', $static_descr );

								if (dle_strlen( $row['story'], $config['charset'] ) > 2000) {

									$row['story'] = dle_substr( strip_tags ($row['story']), 0, 2000, $config['charset'])." .... ";
									if( $config['allow_alt_url'] == "yes" ) $row['story'] .= "( <a href=\"" . $config['http_home_url'] . $row['static_name'] . ".html\" >" . $lang['search_s_go'] . "</a> )";
									else $row['story'] .= "( <a href=\"$PHP_SELF?do=static&page=" . $row['static_name'] . "\" >" . $lang['search_s_go'] . "</a> )";

								}

								$tpl->set( '{static}', $row['story'] );
								$tpl->set( '{pages}', '' );

								if( @date( "Ymd", $row['date'] ) == date( "Ymd", $_TIME ) ) {
									
									$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $row['date'] ) );
								
								} elseif( @date( "Ymd", $row['date'] ) == date( "Ymd", ($_TIME - 86400) ) ) {
									
									$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $row['date'] ) );
								
								} else {
									
									$tpl->set( '{date}', langdate( $config['timestamp_active'], $row['date'] ) );
								
								}
						
								$tpl->copy_template = preg_replace ( "#\{date=(.+?)\}#ie", "langdate('\\1', '{$row['date']}')", $tpl->copy_template );

								$tpl->set( '{views}', $row['views'] );
			
								if( $config['allow_alt_url'] == "yes" ) $print_link = $config['http_home_url'] . "print:" . $row['static_name'] . ".html";
								else $print_link = $config['http_home_url'] . "engine/print.php?do=static&amp;page=" . $row['static_name'];
								
								$tpl->set( '[print-link]', "<a href=\"" . $print_link . "\">" );
								$tpl->set( '[/print-link]', "</a>" );
								
								$tpl->compile( 'content' );
								$tpl->clear();
							} else
								$tpl->result['content'] .= $row['story'];
							
							if( $config['files_allow'] == "yes" ) {
								if( strpos( $tpl->result['content'], "[attachment=" ) !== false ) {
									$tpl->result['content'] = show_attach( $tpl->result['content'], $attachments, true );
								}
							}
						
						}
					} else {
						// ���������� ������ � ������� � ������������
						

						$tpl->set( '{result-date}', langdate( $config['timestamp_active'], $row['date'] ) );
						
						$row_title = stripslashes( $row['title'] );
						$tpl->set( '{result-title}', $row_title );

						$go_page = $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/";
						$go_page = "onclick=\"ShowProfile('" . urlencode( $row['autor'] ) . "', '" . htmlspecialchars( $go_page ) . "'); return false;\"";
						
						if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{result-author}', "<a {$go_page} href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/\">" . $row['autor'] . "</a>" );
						else $tpl->set( '{result-author}', "<a {$go_page} href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['autor'] ) . "\">" . $row['autor'] . "</a>" );
												
						$tpl->set( '{result-comments}', $row['comm_in_news'] );
						$my_news_id = "<a title=\"" . $row_title . "\" href=\"$PHP_SELF?newsid=" . $row['id'] . "\">� " . $row['id'] . "</a>";
						$tpl->set( '{news-id}', $my_news_id );
						
						if( ! $row['category'] ) {
							$my_cat = "---";
							$my_cat_link = "---";
						} else {
							
							$my_cat = array ();
							$my_cat_link = array ();
							$cat_list = explode( ',', $row['category'] );
							
							if( count( $cat_list ) == 1 ) {
								
								$my_cat[] = $cat_info[$cat_list[0]]['name'];
								
								$my_cat_link = get_categories( $cat_list[0] );
							
							} else {
								
								foreach ( $cat_list as $element ) {
									if( $element ) {
										$my_cat[] = $cat_info[$element]['name'];
										if( $config['allow_alt_url'] == "yes" ) $my_cat_link[] = "<a href=\"" . $config['http_home_url'] . get_url( $element ) . "/\">{$cat_info[$element]['name']}</a>";
										else $my_cat_link[] = "<a href=\"$PHP_SELF?do=cat&category={$cat_info[$element]['alt_name']}\">{$cat_info[$element]['name']}</a>";
									}
								}
								
								$my_cat_link = stripslashes( implode( ', ', $my_cat_link ) );
							}
							
							$my_cat = stripslashes( implode( ', ', $my_cat ) );
						}
						
						$row['category'] = intval( $row['category'] );
						
						if( $row['view_edit'] and $row['editdate'] ) {
							
							if( date( Ymd, $row['editdate'] ) == date( Ymd, $_TIME ) ) {
								
								$tpl->set( '{edit-date}', $lang['time_heute'] . langdate( ", H:i", $row['editdate'] ) );
							
							} elseif( date( Ymd, $row['editdate'] ) == date( Ymd, ($_TIME - 86400) ) ) {
								
								$tpl->set( '{edit-date}', $lang['time_gestern'] . langdate( ", H:i", $row['editdate'] ) );
							
							} else {
								
								$tpl->set( '{edit-date}', langdate( $config['timestamp_active'], $row['editdate'] ) );
							
							}
							
							$tpl->set( '{editor}', $row['editor'] );
							$tpl->set( '{edit-reason}', $row['reason'] );
							
							if( $row['reason'] ) {
								
								$tpl->set( '[edit-reason]', "" );
								$tpl->set( '[/edit-reason]', "" );
							
							} else
								$tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );
							
							$tpl->set( '[edit-date]', "" );
							$tpl->set( '[/edit-date]', "" );
						
						} else {
							
							$tpl->set( '{edit-date}', "" );
							$tpl->set( '{editor}', "" );
							$tpl->set( '{edit-reason}', "" );
							$tpl->set_block( "'\\[edit-date\\](.*?)\\[/edit-date\\]'si", "" );
							$tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );
						}
						
						if( $config['allow_tags'] and $row['tags'] ) {
							
							$tpl->set( '[tags]', "" );
							$tpl->set( '[/tags]', "" );
							
							$tags = array ();
							
							$row['tags'] = explode( ",", $row['tags'] );
							
							foreach ( $row['tags'] as $value ) {
								
								$value = trim( $value );
								
								if( $config['allow_alt_url'] == "yes" ) $tags[] = "<a href=\"" . $config['http_home_url'] . "tags/" . urlencode( $value ) . "/\">" . $value . "</a>";
								else $tags[] = "<a href=\"$PHP_SELF?do=tags&amp;tag=" . urlencode( $value ) . "\">" . $value . "</a>";
							
							}
							
							$tpl->set( '{tags}', implode( ", ", $tags ) );
						
						} else {
							
							$tpl->set_block( "'\\[tags\\](.*?)\\[/tags\\]'si", "" );
							$tpl->set( '{tags}', "" );
						
						}
						
						$tpl->set( '{link-category}', $my_cat_link );
						$tpl->set( '{views}', $row['news_read'] );
						
						if( $row['output_comms'] == '1' ) {
							
							// ��������� � ����� ������������
							

							if( ! $row['is_register'] ) {

								if( $row['gast_email'] != "" ) {
									$tpl->set( '{result-author}', "<a href=\"mailto:".htmlspecialchars($row['gast_email'], ENT_QUOTES)."\">" . stripslashes( $row['autor'] ) . "</a>" );
								} else {
									$tpl->set( '{result-author}', stripslashes( $row['autor'] ) );
								}

							} else {

								$go_page = $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/";
								$go_page = "onclick=\"ShowProfile('" . urlencode( $row['autor'] ) . "', '" . htmlspecialchars( $go_page ) . "'); return false;\"";
								
								if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{result-author}', "<a {$go_page} href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/\">" . $row['autor'] . "</a>" );
								else $tpl->set( '{result-author}', "<a {$go_page} href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['autor'] ) . "\">" . $row['autor'] . "</a>" );
							}
							
							if( $is_logged and $member_id['user_group'] == '1' ) $tpl->set( '{ip}', "IP: <a onclick=\"return dropdownmenu(this, event, IPMenu('" . $row['ip'] . "', '" . $lang['ip_info'] . "', '" . $lang['ip_tools'] . "', '" . $lang['ip_ban'] . "'), '190px')\" href=\"https://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>" );
							else $tpl->set( '{ip}', '' );

							$edit_limit = false;
							if (!$user_group[$member_id['user_group']]['edit_limit']) $edit_limit = true;
							elseif ( ($row['date'] + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) > $_TIME ) {
								$edit_limit = true;
							}

							if( $is_logged AND $edit_limit AND (($member_id['name'] == $row['name'] AND $row['is_register'] AND $user_group[$member_id['user_group']]['allow_editc']) OR $user_group[$member_id['user_group']]['edit_allc']) ) {
								$tpl->set( '[com-edit]', "<a onclick=\"return dropdownmenu(this, event, MenuCommBuild('" . $row['coms_id'] . "', 'news'), '170px')\" href=\"" . $config['http_home_url'] . "?do=comments&action=comm_edit&id=" . $row['coms_id'] . "\">" );
								$tpl->set( '[/com-edit]', "</a>" );
								$allow_comments_ajax = true;
							} else
								$tpl->set_block( "'\\[com-edit\\](.*?)\\[/com-edit\\]'si", "" );
							
							if( $is_logged AND $edit_limit AND (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_delc']) or $member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['del_allc']) ) {
								$tpl->set( '[com-del]', "<a href=\"javascript:DeleteComments('{$row['coms_id']}', '{$dle_login_hash}')\">" );
								$tpl->set( '[/com-del]', "</a>" );
							} else
								$tpl->set_block( "'\\[com-del\\](.*?)\\[/com-del\\]'si", "" );
							
							$tpl->set_block( "'\\[fast\\](.*?)\\[/fast\\]'si", "" );
							
							$tpl->set( '{mail}', $row['email'] );
							$tpl->set( '{comment-id}', '--' );
							
							if( $row['banned'] == 'yes' or $row['name'] == '' or ! $row['is_register'] ) {
								$tpl->set( '{foto}', "{THEME}/images/noavatar.png" );
							} else {
								if( $row['foto'] ) $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
								else $tpl->set( '{foto}', "{THEME}/images/noavatar.png" );
							}
							
							if( $row['is_register'] and $row['icq'] ) $tpl->set( '{icq}', stripslashes( $row['icq'] ) );
							else $tpl->set( '{icq}', '--' );
							
							if( $row['is_register'] ) $tpl->set( '{registration}', langdate( "d.m.Y", $row['reg_date'] ) );
							else $tpl->set( '{registration}', '--' );
							
							if( $row['is_register'] and $row['news_num'] ) $tpl->set( '{news_num}', $row['news_num'] );
							else $tpl->set( '{news_num}', '0' );
							
							if( $row['is_register'] and $row['comm_num'] ) $tpl->set( '{comm_num}', $row['comm_num'] );
							else $tpl->set( '{comm_num}', '0' );
							
							$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "" );
							$tpl->set( '{result-text}', "<div id='comm-id-" . $row['coms_id'] . "'>" . $row['story'] . "</div>" );
						
						} else {
                            // ��������� �������������� �����
                            $xfieldsdata = xfieldsdataload( $row['xfields'] );
                            
                            foreach ( $xfields as $value ) {
                                $preg_safe_name = preg_quote( $value[0], "'" );
                                
                                //          if ($value[5] != 0) {
                                if( empty( $xfieldsdata[$value[0]] ) ) {
                                    $tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
                                } else {
                                    $tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "\\1", $tpl->copy_template );
                                }
                                //          }
                                $xfields_val = stripslashes($xfieldsdata[$value[0]]);
                                $tpl->copy_template = preg_replace( "'\\[xfvalue_{$preg_safe_name}\\]'i", $xfields_val, $tpl->copy_template );
                            }
                            // ��������� �������������� �����
							

							if( $is_logged and (($member_id['name'] == $row['autor'] and $user_group[$member_id['user_group']]['allow_edit']) or $user_group[$member_id['user_group']]['allow_all_edit']) ) {
								$tpl->set( '[edit]', "<a onclick=\"return dropdownmenu(this, event, MenuNewsBuild('" . $row['id'] . "', 'short'), '170px')\" href=\"#\">" );
								$tpl->set( '[/edit]', "</a>" );
								$allow_comments_ajax = true;
							} else {
								$tpl->set_block( "'\\[edit\\](.*?)\\[/edit\\]'si", "" );
							}


							if ($smartphone_detected) {

								if (!$config['allow_smart_format']) {
				
										$row['story'] = strip_tags( $row['story'], '<p><br><a>' );
				
								} else {
				
									if ( !$config['allow_smart_images'] ) {
					
										$row['story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['story'] );
										$row['story'] = preg_replace( "#<img(.+?)>#is", "", $row['story'] );
					
									}
					
									if ( !$config['allow_smart_video'] ) {
					
										$row['story'] = preg_replace( "#<!--dle_video_begin(.+?)<!--dle_video_end-->#is", "", $row['story'] );
										$row['story'] = preg_replace( "#<!--dle_audio_begin(.+?)<!--dle_audio_end-->#is", "", $row['story'] );
					
									}
								}
				
							}

                            if ($is_logged){

                                $fav_arr = explode (',', $member_id['favorites']);

                                if (!in_array ($row['id'], $fav_arr))
                                    $tpl->set('{favorites}',"<a id=\"fav-id-".$row['id']."\" href=\"$PHP_SELF?do=favorites&amp;doaction=add&amp;id=".$row['id']."\"><img src=\"".$config['http_home_url']."templates/{$config['skin']}/dleimages/plus_fav.gif\" onclick=\"doFavorites('".$row['id']."', 'plus'); return false;\" alt=\"".$lang['news_addfav']."\" align=\"middle\" border=\"0\" /></a>");
                                else
                            		$tpl->set('{favorites}',"<a id=\"fav-id-".$row['id']."\" href=\"$PHP_SELF?do=favorites&amp;doaction=del&amp;id=".$row['id']."\"><img src=\"".$config['http_home_url']."templates/{$config['skin']}/dleimages/minus_fav.gif\" onclick=\"doFavorites('".$row['id']."', 'minus'); return false;\" alt=\"".$lang['news_minfav']."\" align=\"middle\" border=\"0\" /></a>");

                            } else $tpl->set('{favorites}',"");
							
                            
                            
                            $countPrice = 0;

			$resultPrice = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}' ORDER BY k DESC, tm desc");
			$myrowPrice = $db->get_array($resultPrice);
			$countPrice = $db->num_rows($resultPrice);
			
			$price = "";
			if($countPrice != 0){
			
				$hidkod = "";
				$hidPack = "";
				$hidCvet = "";
				$hidBlesk = "";
				$hidPriceYp = "";
				$hidPriceLitr = "";
				$numsCvet = 0;
				$numsBlesk = 0;
				$numsPriceYp = 0;
				$numsPriceLitr = 0;
				
				
				$resultWidth = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}'");
				$myrowWidth = $db->get_array($resultWidth);
				
				if($db->num_rows($resultWidth) != 0){
					do {
						if(strlen($myrowWidth['cvet']) > $numsCvet) {$numsCvet = strlen($myrowWidth['cvet']);}
						if(strlen($myrowWidth['blesk']) > $numsBlesk) {$numsBlesk = strlen($myrowWidth['blesk']);}
						if(strlen($myrowWidth['price_yp']) > $numsPriceYp) {$numsPriceYp = strlen($myrowWidth['price_yp']);}
						if(strlen($myrowWidth['price_litr']) > $numsPriceLitr) {$numsPriceLitr = strlen($myrowWidth['price_litr']);}
						
					}while($myrowWidth = $db->get_array($resultWidth));
					$numsPriceYp = $numsPriceYp+4;
					$numsPriceLitr = $numsPriceLitr+4;
					$widthCvet = $numsCvet*8;
					$widthBlesk = $numsBlesk*8;
					$widthPriceYp = $numsPriceYp*8;
					$widthPriceLitr = $numsPriceLitr*9;
					
					if($widthCvet < 77) {$widthCvet = 77;}
					if($widthCvet > 230) {$widthCvet = 230;}
					if($widthBlesk < 100) {$widthBlesk = 100;}
					if($widthPriceYp < 102) {$widthPriceYp = 102;}
					if($widthPriceLitr < 70) {$widthPriceLitr = 70;}
					
				}
				
				
				
				$rowCa = explode(",", $row['category'] );

				$resultGetCat = $db->query("SELECT name FROM " . PREFIX . "_category WHERE id='{$rowCa[0]}'");
				$myrowGetCat = $db->get_array($resultGetCat);
				
				$fex = explode(" ", $myrowGetCat['name']);
				$sex = explode(",", $fex[0]);
				
				 if($sex[0] == "TIKKURILA") {
					$current = $fex[0] . " " . $fex[1];
				} else if ($sex[0] == "Tikkurila") {
					$current = $fex[0] . " " . $fex[1];
				} else if ($sex[0] == "Teknos") {
					$current = $fex[0] . " " . $fex[1] . " " . $fex[2];
				} else {
					$current = $sex[0];
				}
				
				 $resultRazdeli = $db->query("SELECT * FROM " . PREFIX . "_razdeli WHERE proizvoditel LIKE '{$current}%'");
				 $myrowRazdeli  = $db->get_array($resultRazdeli);
				 
				 if(!isset($_COOKIE['dle_user_id'])) {

				 	if($myrowRazdeli['nacenka'] != 0) {
					}
					if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
						$t = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
						$pr = $t+(($t*$myrowRazdeli['nacenka'])/100);
						
					} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
						$pr = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
						$pr = $myrowPrice['price_yp']+(($myrowPrice['price_yp']*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
						$pr = $myrowPrice['price_yp'];
					}
					$pr = round($pr, 2);
					
					
					
				 } else {
					if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
						$t = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
						$pr = $t+(($t*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
						$pr = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
						$pr = $myrowPrice['price_yp']+(($myrowPrice['price_yp']*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
						$pr = $myrowPrice['price_yp'];
					}
					$pr = round($pr, 2);
					
					$resultUser = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE '{$current}%'");
					$myrowUser  = $db->get_array($resultUser);
					$countUser  = $db->num_rows($resultUser);
					if($countUser != 0) {
						$disc = ($pr*$myrowUser['discount'])/100;
						$disc = round($disc, 2);
						$pr   = $pr-$disc;
					}
					
					
					
				 }
				 $mp = explode(" ", $myrowPrice['pack']);
				 if($mp[0] != 0) {
					 $prl = $pr/$mp[0];
					 $prl = round($prl, 2);
				 } else {
				 	$prl = 0;
				 }
				 
				$numIt = 0;
				
				do {
				
				
				if(!isset($_COOKIE['dle_user_id'])) {
					if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
						$t = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
						$pr2 = $t+(($t*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
						$pr2 = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
						$pr2 = $myrowPrice['price_yp']+(($myrowPrice['price_yp']*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
						$pr2 = $myrowPrice['price_yp'];
					}
					$pr2 = round($pr2, 2);
					
					
					
				 } else {
					if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] != 0) {
						$t = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
						$pr2 = $t+(($t*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] != 0 && $myrowRazdeli['nacenka'] == 0){
						$pr2 = $myrowPrice['price_yp']*$myrowRazdeli['togrn'];
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] != 0){
						$pr2 = $myrowPrice['price_yp']+(($myrowPrice['price_yp']*$myrowRazdeli['nacenka'])/100);
					} else if($myrowRazdeli['togrn'] == 0 && $myrowRazdeli['nacenka'] == 0){
						$pr2 = $myrowPrice['price_yp'];
					}
					$pr2 = round($pr2, 2);
					
					$resultUser = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='{$_COOKIE['dle_user_id']}' AND proizv LIKE '{$current}%'");
					$myrowUser  = $db->get_array($resultUser);
					$countUser  = $db->num_rows($resultUser);
					
					if($countUser != 0) {
						$disc2 = ($pr2*$myrowUser['discount'])/100;
						$disc2 = round($disc2, 2);
						$pr2   = $pr2-$disc2;
					}
					
				 }
				 $mp2 = explode(" ", $myrowPrice['pack']);
				 if($mp2[0] != 0) {
					 $prl2 = $pr2/$mp2[0];
					 $prl2 = round($prl2, 2);
				 } else {
				 	$prl2 = 0;
				 }
				
				$kod = $myrowPrice['id'];
				$lenkod = strlen($myrowPrice['id']);
				if($lenkod < 5){
					if($lenkod==1) $kod = "0000".$kod;
					if($lenkod==2) $kod = "000".$kod;
					if($lenkod==3) $kod = "00".$kod;
					if($lenkod==4) $kod = "0".$kod;
				} 
				
					$hidkod .= "
					<div class='tag66{$row[id]}' id='tag66' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$kod}</p></div>
					";					
					$hidPack .= "
					<div class='tag11{$row[id]}' id='tag11' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})'><p>{$myrowPrice['pack']}</p></div>
					";
					$hidCvet .= "<div class='tag44{$row[id]}' id='tag44' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})' style='width:{$widthCvet}px;'><p>{$myrowPrice['cvet']}</p></div>";
					$hidBlesk .= "<div class='tag55{$row[id]}' id='tag55' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})' style='width:{$widthBlesk}px;'><p>{$myrowPrice['blesk']}</p></div>";
					$hidPriceYp .= "<div class='tag22{$row[id]}' id='tag22' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})' style='width:{$widthPriceYp}px;'><p>{$pr2} ���</p></div>";
					$hidPriceLitr .= "<div class='tag33{$row[id]}' id='tag33' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})' style='width:{$widthPriceLitr}px;'><p>{$prl2} ���</p></div>";
					

					$numIt++;

				} while($myrowPrice = $db->get_array($resultPrice));
				
				
				$resultPrice = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}' ORDER BY K DESC, tm desc");
				$myrowPrice = $db->get_array($resultPrice);
				$countPrice = $db->num_rows($resultPrice);
				
				if($countPrice > 1) {
					$raskrSpis = "<img src='{THEME}/images/razv.png' style='margin-left:5px;'>";
				} else {
					$raskrSpis = "";
				}
				

				$resultTf = $db->query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$row[id]}'");
				$myrowTf = $db->get_array($resultTf);
				
				if($myrowPrice['price_yp'] == 0 || $myrowPrice['price_yp'] == "���") {
					$realPriceLitr = 0;
				}
				
				if($myrowTf['cvet_g'] == "true") {
				
				$ypak = "<div style='float:left;' id='toniz'>
								<p id='nazvkol2' style='letter-spacing:2px; text-align:center;'>��������</p>
								<div class='tag4' id='num2{$row[id]}' onclick='cng({$row[id]})' style='width:67px;'><p>{$myrowPrice['pack']}</p></div>
								<div style='display:none;' id='upack{$row[id]}'><br><br>{$hidPack}<br><br></div>
							</div>";				
				
				$kod = $myrowPrice['id'];
				$lenkod = strlen($myrowPrice['id']);
				if($lenkod < 5){
					if($lenkod==1) $kod = "0000".$kod;
					if($lenkod==2) $kod = "000".$kod;
					if($lenkod==3) $kod = "00".$kod;
					if($lenkod==4) $kod = "0".$kod;
				} 
				$kodp = "<div style='float:left;' id='toniz'>
								<p id='nazvkol2' style='letter-spacing:2px; padding-right:5px;'>��� ����.</p>
								<div class='tag6' id='num{$row[id]}' onclick='cng({$row[id]})' style='cursor:pointer;'><p>{$kod}$raskrSpis</p></div>
								<div style='display:none;' id='{$row[id]}'><br><br>{$hidkod}<br><br></div>
							</div>";
				
			
				} else {$ypak = "<div style='float:left;' id='{$row[id]}'><div id='num{$row[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['pack_g'] == "true") {
					$cvet = "<div style='float:left;' id='toniz'>
								<p id='nazvkol2' style='letter-spacing:2px; text-align:center;'>���� � ����</p> 
								<div class='tag4' id='num3{$row[id]}' onclick='cng({$row[id]})' style='width:{$widthCvet}px;'><p>{$myrowPrice['cvet']}</p></div>
								<div style='display:none;' id='cvet{$row[id]}'><br><br>{$hidCvet}<br><br></div>
							</div>";
				} else {$cvet = "<div style='float:left;' id='cvet{$row[id]}'><div id='num3{$row[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['blesk_g'] == "true") {
				$blesk = "<div style='float:left;' id='toniz'>
							<p id='nazvkol' style='letter-spacing:2px; text-align:center;'>������� ������</p>
							<div class='tag5' id='num4{$row[id]}' onclick='cng({$row[id]})' style='width:{$widthBlesk}px;'><p>{$myrowPrice['blesk']}</p></div>
							<div style='display:none;' id='blesk{$row[id]}'><br><br>{$hidBlesk}<br><br></div>
						</div>";
				} else {$blesk = "<div style='float:left;' id='blesk{$row[id]}'><div id='num4{$row[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['price_yp_g'] == "true") {
				$styp = "<div style='float:left;' id='toniz'>
							<p id='nazvkol4' style='letter-spacing:2px; text-align:center;'>���� ��������</p>
							<div class='tag2' id='num5{$row[id]}' onclick='cng({$row[id]})' style='width:{$widthPriceYp}px;'><p>{$pr} ���</p></div>
							<div style='display:none;' id='priceyp{$row[id]}'><br><br>{$hidPriceYp}<br><br></div>
						</div>";
				} else {$styp = "<div style='float:left;' id='priceyp{$row[id]}'><div id='num5{$row[id]}'> style='display:none;'</div></div>";}
				
				if($myrowTf['price_litr_g'] == "true") {
				$stl = "<div style='float:left;' id='toniz'>
							<p id='nazvkol'  style='letter-spacing:2px; text-align:center;'> ���� �� 1�</p>
							<div class='tag3' id='num6{$row[id]}' onclick='cng({$row[id]})' style='width:{$widthPriceLitr}px;'><p>{$prl} ���</p></div>
							<div style='display:none;' id='pricelitr{$row[id]}'><br><br>{$hidPriceLitr}<br><br></div>
						</div>";
				} else {$stl = "<div style='float:left;' id='pricelitr{$row[id]}'><div id='num6{$row[id]}' style='display:none;'></div></div>";}
				
				
				
				
				
				$price = "
				  <div style='clear:both; float;left; margin-right:130px; margin-top:-40px;'  id='showlogin' class='showlogin'>	
						{$kodp}{$ypak}{$cvet}{$blesk}{$styp}{$stl}
						<div class='showing'>
							
							<a href='' onclick='compare({$row[id]}); return false;' style='margin-top:33px; cursor:pointer;'><img src='{THEME}/images/buttons/compare.png'></a>
							<a onclick='intobusket({$row[id]}); return false;' style='cursor:pointer; margin:0px; padding:0px;'><img src='{THEME}/images/buttons/buy.png'></a>
							<a href='/index.php?do=feedback'><img src='{THEME}/images/buttons/complane.png'></a>
						<!--	<a {$go_page}href=\"" . $full_link . "\"><img src='{THEME}/images/buttons/podrobnee.png' alt='���������' border='0'></a>-->
						</div>
						<div style='float:left; width:75px; margin-top:35px; margin-left:7px; margin-right:-15px;' class='hiden'>
							<a href='' onclick='compare({$row[id]}); return false;' style='margin-top:33px; cursor:pointer;'><img src='{THEME}/images/buttons/compare.png'></a>
							<a onclick='intobusket({$row[id]}); return false;' style='cursor:pointer; margin:0px; padding:0px;'><img src='{THEME}/images/buttons/buy.png'></a>
						</div>
						
						<form>
							<input type='hidden' id='idd{$row[id]}' value='{$myrowPrice[id]}'>
						</form>
				  </div>
				  <div id='pojceny' class='hiden'><a href='/index.php?do=feedback'><img src='{THEME}/images/buttons/complane.png'></a><br><br><br><br><br></div>";

				  
			}

				
                
				if($price != "") {
					$tpl->set( '{top}', 70);
				} else {
					$tpl->set( '{top}', 10);
				}
				
                            
                            
                            
							$tpl->set( '{result-text}', "<div id='news-id-" . $row['id'] . "'>" . $row['story'] . "</div>" );
						
						}
						
						$tpl->set( '{search-id}', $search_id );
						
						if( $showposts == 0 ) {
							// �������� �������� �������
							$tpl->set_block( "'\\[shortresult\\].*?\\[/shortresult\\]'si", "" );
							$tpl->set( '[fullresult]', "" );
							$tpl->set( '[/fullresult]', "" );
							$alt_text = $row_title;
						} else {
							// �������� ������ ���������
							$tpl->set_block( "'\\[fullresult\\].*?\\[/fullresult\\]'si", "" );
							$tpl->set( '[shortresult]', "" );
							$tpl->set( '[/shortresult]', "" );
							$alt_text = create_description( $row['story'] );
						}
						
						if( $config['allow_alt_url'] == "yes" ) {
							
							if( $row['flag'] and $config['seo_type'] ) {
								
								if( $row['category'] and $config['seo_type'] == 2 ) {
									
									$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
								
								} else {
									
									$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
								
								}
							
							} else {
								
								$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['newsdate'] ) . $row['alt_name'] . ".html";
							}
						
						} else {
							
							$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
						
						}
                        if($full_link != "https://laki-kraski.com.ua/1296-1111111.html") {
						$tpl->set('{price}', $price . "<div style='margin-top:7px;'><a href='{$full_link}'><img src='{THEME}/images/buttons/podrobnee.png' alt='���������' border='0' id='podrobnee'></a></div>");
                        } else {
                        $tpl->set('{price}', "");
                        }
						$tpl->set( '[result-link]', "<a href=\"" . $full_link . "\" >" );
						$tpl->set( '[/result-link]', "</a>" );
						
						if( $row['output_comms'] == '1' ) {
							// ��� ������ ������������
							$tpl->set_block( "'\\[searchposts\\].*?\\[/searchposts\\]'si", "" );
							$tpl->set( '[searchcomments]', "<div id='comment-id-{$row['coms_id']}'>" );
							$tpl->set( '[/searchcomments]', "</div>" );
						} else {
							// ��� ������ ������
							$tpl->set_block( "'\\[searchcomments\\].*?\\[/searchcomments\\]'si", "" );
							$tpl->set( '[searchposts]', "" );
							$tpl->set( '[/searchposts]', "" );
						}
						
						$tpl->compile( 'content' );

						if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->result['content'] = preg_replace( "'\[hide\](.*?)\[/hide\]'si", "\\1", $tpl->result['content']);
						else $tpl->result['content'] = preg_replace ( "'\[hide\](.*?)\[/hide\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $tpl->result['content'] );

						
						if( $config['files_allow'] == "yes" ) {
							if( strpos( $tpl->result['content'], "[attachment=" ) !== false ) {
								$tpl->result['content'] = show_attach( $tpl->result['content'], $attachments );
							}
						}
					} // ���������� ������ � ������� � ������������
				} // while
				

				$tpl->clear();
				$db->free( $sql_result );
			}
		}
	}
	
	$tpl->clear();
	
	//####################################################################################################################
	//         ��������� �� ��������
	//####################################################################################################################
	if( $found_result > 0 ) {
		$tpl->load_template( 'navigation.tpl' );
		
		//----------------------------------
		// Previous link
		//----------------------------------
		if( isset( $search_start ) and $search_start != "" and $search_start > 0 ) {
			$prev = $search_start / $config_search_numbers;
			$prev_page = "<a name=\"prevlink\" id=\"prevlink\" onclick=\"javascript:list_submit($prev); return(false)\" href=#>";
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", $prev_page . "\\1</a>" );
		
		} else {
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>" );
			$no_prev = TRUE;
		}
		
		//----------------------------------
		// Pages
		//----------------------------------
		if( $config_search_numbers ) {
			$pages_count = @ceil( $count_result / $config_search_numbers );
			$pages_start_from = 0;
			$pages = "";
			$pages_per_side = ($config['pages_per_section'] - 1) / 2;
			$pages_to_display = ($config['pages_per_section'] * 3) + 1;
			if( $pages_count > $pages_to_display ) {
				for($j = 1; $j <= $config['pages_per_section']; $j ++) {
					if( $pages_start_from != $search_start ) {
						$pages .= "<a onclick=\"javascript:list_submit($j); return(false)\" href=#>$j</a> ";
					} else {
						$pages .= " <span>$j</span> ";
					}
					$pages_start_from += $config_search_numbers;
				}
				if( ((($search_start / $config_search_numbers) + 1) > ($pages_per_side + 1)) && ((($search_start / $config_search_numbers) + 1) < ($pages_count - $pages_per_side)) ) {
					$pages .= ((($search_start / $config_search_numbers) + 1) > ($config['pages_per_section'] + $pages_per_side + 1)) ? '... ' : ' ';
					$page_min = ((($search_start / $config_search_numbers) + 1) > ($config['pages_per_section'] + $pages_per_side)) ? (($search_start / $config_search_numbers) - $pages_per_side + 1) : ($config['pages_per_section'] + 1);
					$page_max = ((($search_start / $config_search_numbers) + 1) < ($pages_count - ($config['pages_per_section'] + $pages_per_side - 1))) ? (($search_start / $config_search_numbers) + $pages_per_side + 1) : ($pages_count - $config['pages_per_section']);
					
					$pages_start_from = ($page_min - 1) * $config_search_numbers;
					
					for($j = $page_min; $j < $page_max + 1; $j ++) {
						if( $pages_start_from != $search_start ) {
							$pages .= "<a onclick=\"javascript:list_submit($j); return(false)\" href=#>$j</a> ";
						} else {
							$pages .= " <span>$j</span> ";
						}
						$pages_start_from += $config_search_numbers;
					}
					$pages .= ((($search_start / $config_search_numbers) + 1) < $pages_count - ($config['pages_per_section'] + $pages_per_side)) ? '... ' : ' ';
				
				} else {
					$pages .= '... ';
				}
				
				$pages_start_from = ($pages_count - $config['pages_per_section']) * $config_search_numbers;
				for($j = ($pages_count - ($config['pages_per_section'] - 1)); $j <= $pages_count; $j ++) {
					if( $pages_start_from != $search_start ) {
						$pages .= "<a onclick=\"javascript:list_submit($j); return(false)\" href=#>$j</a> ";
					} else {
						$pages .= " <span>$j</span> ";
					}
					$pages_start_from += $config_search_numbers;
				}
			
			} else {
				for($j = 1; $j <= $pages_count; $j ++) {
					if( $pages_start_from != $search_start ) {
						$pages .= "<a onclick=\"javascript:list_submit($j); return(false)\" href=#>$j</a> ";
					} else {
						$pages .= " <span>$j</span> ";
					}
					$pages_start_from += $config_search_numbers;
				}
			}
			$tpl->set( '{pages}', $pages );
		}
		
		//----------------------------------
		// Next link
		//----------------------------------
		if( $config_search_numbers < $count_result and $to_num < $count_result ) {
			$next_page = $to_num / $config_search_numbers + 1;
			$next = "<a name=\"nextlink\" id=\"nextlink\" onclick=\"javascript:list_submit($next_page); return(false)\" href=#>";
			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", $next . "\\1</a>" );
		} else {
			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>" );
			$no_next = TRUE;
		}
		
		if( ! $no_prev or ! $no_next ) {
			$tpl->compile( 'content' );
		}
		
		$tpl->clear();
	}
}
?>