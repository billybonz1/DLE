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
 Файл: show.short.php
-----------------------------------------------------
 Назначение: вывод новостей
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if( $allow_active_news ) {
	
	if( $config['allow_banner'] ) include_once ENGINE_DIR . '/modules/banners.php';
	
	$i = $cstart;
	$news_found = FALSE;
	
	if( isset( $view_template ) and $view_template == "rss" ) {
	} elseif( $category_id and $cat_info[$category_id]['short_tpl'] != '' ) $tpl->load_template( $cat_info[$category_id]['short_tpl'] . '.tpl' );
	else $tpl->load_template( 'shortstory.tpl' );
	
	if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) { $xfound = true; $xfields = xfieldsload();}
	else $xfound = false;
	
	if( count( $banners ) AND $config['allow_banner'] AND !$smartphone_detected) {
		
		$news_c = 1;
		
		if( isset( $ban_short ) ) {
			for($indx = 0, $max = sizeof( $ban_short['top'] ), $banners_topz = ''; $indx < $max; $indx ++)
				if( $ban_short['top'][$indx]['zakr'] ) {
					$banners_topz .= $ban_short['top'][$indx]['text'];
					unset( $ban_short['top'][$indx] );
				}
			
			for($indx = 0, $max = sizeof( $ban_short['cen'] ), $banners_cenz = ''; $indx < $max; $indx ++)
				if( $ban_short['cen'][$indx]['zakr'] ) {
					$banners_cenz .= $ban_short['cen'][$indx]['text'];
					unset( $ban_short['cen'][$indx] );
				}
			
			for($indx = 0, $max = sizeof( $ban_short['down'] ), $banners_downz = ''; $indx < $max; $indx ++)
				if( $ban_short['down'][$indx]['zakr'] ) {
					$banners_downz .= $ban_short['down'][$indx]['text'];
					unset( $ban_short['down'][$indx] );
				}
			
			$middle = floor( $config['news_number'] / 2 );
			$middle_s = floor( ($middle - 1) / 2 );
			$middle_e = floor( $middle + (($config['news_number'] - $middle) / 2) + 1 );
		}
	}
	
	$sql_result = $db->query( $sql_select );
	
	if( ! isset( $view_template ) ) {
		
		$count_all = $db->super_query( $sql_count );
		$count_all = $count_all['count'];
	
	} else
		$count_all = 0;
	
	while ( $row = $db->get_row( $sql_result ) ) {
		
		$news_found = TRUE;
		$attachments[] = $row['id'];
		$row['date'] = strtotime( $row['date'] );
		
		if( isset( $middle ) ) {
			
			if( $news_c == $middle_s ) {
				$tpl->copy_template .= bannermass( $banners_topz, $ban_short['top'] );
			} else if( $news_c == $middle ) {
				$tpl->copy_template .= bannermass( $banners_cenz, $ban_short['cen'] );
			} else if( $news_c == $middle_e ) {
				$tpl->copy_template .= bannermass( $banners_downz, $ban_short['down'] );
			}
			$news_c ++;
		}
		
		$i ++;
		
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
				
				$my_cat_link = implode( ', ', $my_cat_link );
			}
			
			$my_cat = implode( ', ', $my_cat );
		}

		if( strpos( $tpl->copy_template, "[catlist=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[catlist=(.+?)\\](.*?)\\[/catlist\\]#ies", "check_category('\\1', '\\2', '{$row['category']}')", $tpl->copy_template );
		}
		
		if( strpos( $tpl->copy_template, "[not-catlist=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[not-catlist=(.+?)\\](.*?)\\[/not-catlist\\]#ies", "check_category('\\1', '\\2', '{$row['category']}', false)", $tpl->copy_template );
		}
		
		$row['category'] = intval( $row['category'] );
		
		$news_find = array ('{comments-num}' => $row['comm_num'], '{views}' => $row['news_read'], '{category}' => $my_cat, '{link-category}' => $my_cat_link, '{news-id}' => $row['id'], '{PAGEBREAK}' => '' );
		
		$tpl->set( '', $news_find );
		
		if( $cat_info[$row['category']]['icon'] ) {
			
			$tpl->set( '{category-icon}', $cat_info[$row['category']]['icon'] );
		
		} else {
			
			$tpl->set( '{category-icon}', "{THEME}/dleimages/no_icon.gif" );
		
		}
		
		if( date( 'Ymd', $row['date'] ) == date( 'Ymd', $_TIME ) ) {
			
			$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $row['date'] ) );
		
		} elseif( date( 'Ymd', $row['date'] ) == date( 'Ymd', ($_TIME - 86400) ) ) {
			
			$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $row['date'] ) );
		
		} else {
			
			$tpl->set( '{date}', langdate( $config['timestamp_active'], $row['date'] ) );
		
		}

		$tpl->copy_template = preg_replace ( "#\{date=(.+?)\}#ie", "langdate('\\1', '{$row['date']}')", $tpl->copy_template );


		if ( $row['fixed'] ) {

			$tpl->set( '[fixed]', "" );
			$tpl->set( '[/fixed]', "" );
			$tpl->set_block( "'\\[not-fixed\\](.*?)\\[/not-fixed\\]'si", "" );

		} else {

			$tpl->set( '[not-fixed]', "" );
			$tpl->set( '[/not-fixed]', "" );
			$tpl->set_block( "'\\[fixed\\](.*?)\\[/fixed\\]'si", "" );
		}

		if ( $row['votes'] ) {

			$tpl->set( '[poll]', "" );
			$tpl->set( '[/poll]', "" );
			$tpl->set_block( "'\\[not-poll\\](.*?)\\[/not-poll\\]'si", "" );

		} else {

			$tpl->set( '[not-poll]', "" );
			$tpl->set( '[/not-poll]', "" );
			$tpl->set_block( "'\\[poll\\](.*?)\\[/poll\\]'si", "" );
		}		



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
		
		if( $row['allow_rate'] ) {
			
			if( $config['short_rating'] and $user_group[$member_id['user_group']]['allow_rating'] ) $tpl->set( '{rating}', ShortRating( $row['id'], $row['rating'], $row['vote_num'], 1 ) );
			else $tpl->set( '{rating}', ShortRating( $row['id'], $row['rating'], $row['vote_num'], 0 ) );
		
		} else
			$tpl->set( '{rating}', "" );
		
		if( $config['allow_alt_url'] == "yes" ) {
			
			$go_page = $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/";
			$tpl->set( '[day-news]', "<a href=\"".$config['http_home_url'] . date( 'Y/m/d/', $row['date'])."\" >" );
		
		} else {
			
			$go_page = "$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['autor'] );
			$tpl->set( '[day-news]', "<a href=\"$PHP_SELF?year=".date( 'Y', $row['date'])."&amp;month=".date( 'm', $row['date'])."&amp;day=".date( 'd', $row['date'])."\" >" );
		
		}

		$tpl->set( '[/day-news]', "</a>" );
		$tpl->set( '[profile]', "<a href=\"" . $go_page . "\">" );
		$tpl->set( '[/profile]', "</a>" );

		$tpl->set( '{login}', $row['autor'] );
		
		$tpl->set( '{author}', "<a onclick=\"ShowProfile('" . urlencode( $row['autor'] ) . "', '" . $go_page . "'); return false;\" href=\"" . $go_page . "\">" . $row['autor'] . "</a>" );
		
		if( $allow_userinfo and ! $row['approve'] and ($member_id['name'] == $row['autor'] and ! $user_group[$member_id['user_group']]['allow_all_edit']) ) {
			$tpl->set( '[edit]', "<a href=\"" . $config['http_home_url'] . "index.php?do=addnews&id=" . $row['id'] . "\" >" );
			$tpl->set( '[/edit]', "</a>" );
		} elseif( $is_logged and (($member_id['name'] == $row['autor'] and $user_group[$member_id['user_group']]['allow_edit']) or $user_group[$member_id['user_group']]['allow_all_edit']) ) {
			
			$_SESSION['referrer'] = $_SERVER['REQUEST_URI'];
			$tpl->set( '[edit]', "<a onclick=\"return dropdownmenu(this, event, MenuNewsBuild('" . $row['id'] . "', 'short'), '170px')\" href=\"#\">" );
			$tpl->set( '[/edit]', "</a>" );
			$allow_comments_ajax = true;
		} else
			$tpl->set_block( "'\\[edit\\](.*?)\\[/edit\\]'si", "" );
		
		if( $config['allow_alt_url'] == "yes" ) {
			
			if( $row['flag'] and $config['seo_type'] ) {
				
				if( $row['category'] and $config['seo_type'] == 2 ) {
					
					$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
				
				} else {
					
					$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
				
				}
			
			} else {
				
				$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . $row['alt_name'] . ".html";
			}
		
		} else {
			
			$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
		
		}
		
		if( (strlen( $row['full_story'] ) < 13) and $config['hide_full_link'] == "yes" ) $tpl->set_block( "'\\[full-link\\](.*?)\\[/full-link\\]'si", "" );
		else {
			
			$tpl->set( '[full-link]', "<a href=\"" . $full_link . "\">" );
			
			$tpl->set( '[/full-link]', "</a>" );
		}
		
		$tpl->set( '{full-link}', $full_link );
		
		if( $row['allow_comm'] ) {
			
			$tpl->set( '[com-link]', "<a href=\"" . $full_link . "#comment\">" );
			$tpl->set( '[/com-link]', "</a>" );
		
		} else
			$tpl->set_block( "'\\[com-link\\](.*?)\\[/com-link\\]'si", "" );
		
		if( strpos( $tpl->copy_template, "[category=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[category=(.+?)\\](.*?)\\[/category\\]#ies", "check_category('\\1', '\\2', '{$category_id}')", $tpl->copy_template );
		}
		
		if( strpos( $tpl->copy_template, "[not-category=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[not-category=(.+?)\\](.*?)\\[/not-category\\]#ies", "check_category('\\1', '\\2', '{$category_id}', false)", $tpl->copy_template );
		}
		
		if( $is_logged ) {
			
			$fav_arr = explode( ',', $member_id['favorites'] );
			
			if( ! in_array( $row['id'], $fav_arr ) or $config['allow_cache'] == "yes" ) $tpl->set( '{favorites}', "<a id=\"fav-id-" . $row['id'] . "\" href=\"$PHP_SELF?do=favorites&amp;doaction=add&amp;id=" . $row['id'] . "\"><img src=\"" . $config['http_home_url'] . "templates/{$config['skin']}/dleimages/plus_fav.gif\" onclick=\"doFavorites('" . $row['id'] . "', 'plus'); return false;\" title=\"" . $lang['news_addfav'] . "\" style=\"vertical-align: middle;border: none;\" alt=\"\" /></a>" );
			else $tpl->set( '{favorites}', "<a id=\"fav-id-" . $row['id'] . "\" href=\"$PHP_SELF?do=favorites&amp;doaction=del&amp;id=" . $row['id'] . "\"><img src=\"" . $config['http_home_url'] . "templates/{$config['skin']}/dleimages/minus_fav.gif\" onclick=\"doFavorites('" . $row['id'] . "', 'minus'); return false;\" title=\"" . $lang['news_minfav'] . "\" style=\"vertical-align: middle;border: none;\" alt=\"\" /></a>" );
		
		} else
			$tpl->set( '{favorites}', "" );
		
		if( $allow_userinfo and ! $row['approve'] ) {
			
			$tpl->set( '{approve}', $lang['approve'] );
		
		} else
			$tpl->set( '{approve}', "" );
			
		// Обработка дополнительных полей
		if( $xfound ) {
			$xfieldsdata = xfieldsdataload( $row['xfields'] );
			
			foreach ( $xfields as $value ) {
				$preg_safe_name = preg_quote( $value[0], "'" );
				
				if( empty( $xfieldsdata[$value[0]] ) ) {
					$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
				} else {
					$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "\\1", $tpl->copy_template );
				}
				
				$tpl->copy_template = str_replace( "[xfvalue_{$preg_safe_name}]", stripslashes( $xfieldsdata[$value[0]] ), $tpl->copy_template );
			}
		}
		// Обработка дополнительных полей
		

		if( isset($view_template) AND $view_template == "rss" ) {
			
			$tpl->set( '{rsslink}', $full_link );
			$tpl->set( '{rssauthor}', $row['autor'] );
			$tpl->set( '{rssdate}', date( "r", $row['date'] ) );
			$tpl->set( '{title}', htmlspecialchars( strip_tags( stripslashes( $row['title'] ) ) ) );
			
			if( $config['rss_format'] != 1 ) {
				$row['short_story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['short_story'] );				
				$row['short_story'] = trim (htmlspecialchars( strip_tags( stripslashes( str_replace( "<br />", " ", $row['short_story'] ) ) ) ) );
			
			} else {
				
				$row['short_story'] = stripslashes( $row['short_story'] );
			
			}
			
			$tpl->set( '{short-story}', $row['short_story'] );
			
			if( $config['rss_format'] == 2 ) {

				$row['full_story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['full_story'] );

				$row['full_story'] = trim (htmlspecialchars( strip_tags( stripslashes( str_replace( "<br />", " ", $row['full_story'] ) ), '<a>' ), ENT_QUOTES ) );

				if( $row['full_story'] == "" ) $row['full_story'] = $row['short_story'];
				
				$tpl->set( '{full-story}', $row['full_story'] );
			
			}
		
		} else {

			if ($smartphone_detected) {

				if (!$config['allow_smart_format']) {

						$row['short_story'] = strip_tags( $row['short_story'], '<p><br><a>' );

				} else {


					if ( !$config['allow_smart_images'] ) {
	
						$row['short_story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['short_story'] );
						$row['short_story'] = preg_replace( "#<img(.+?)>#is", "", $row['short_story'] );
	
					}
	
					if ( !$config['allow_smart_video'] ) {
	
						$row['short_story'] = preg_replace( "#<!--dle_video_begin(.+?)<!--dle_video_end-->#is", "", $row['short_story'] );
						$row['short_story'] = preg_replace( "#<!--dle_audio_begin(.+?)<!--dle_audio_end-->#is", "", $row['short_story'] );
	
					}

				}

			}
			
			$countPrice = 0;

			$resultPrice = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}' ORDER BY K desc, tm desc");
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
				
				//var_dump($widthCvet);
				if( $row['allow_rate'] ) {
				
				if( $config['short_rating'] and $user_group[$member_id['user_group']]['allow_rating'] ) 
				{
						if ($widthCvet < 120) $tpl->set( '{rating}', ShortRating( $row['id'], $row['rating'], $row['vote_num'], 1 ));
						else $tpl->set( '{rating}', "<div class='rating' style='float:left;'></div>" );
				}
				else $tpl->set( '{rating}', ShortRating( $row['id'], $row['rating'], $row['vote_num'], 0 ) );

				} else
				$tpl->set( '{rating}', "" );
				
				
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
					$hidPriceYp .= "<div class='tag22{$row[id]}' id='tag22' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})' style='width:{$widthPriceYp}px;'><p>{$pr2} грн</p></div>";
					$hidPriceLitr .= "<div class='tag33{$row[id]}' id='tag33' onmouseover='fade({$numIt},{$row[id]})' onmouseout='fadeout({$numIt},{$row[id]})' onclick='process({$row[id]}, {$numIt}, {$myrowPrice[id]})' style='width:{$widthPriceLitr}px;'><p>{$prl2} грн</p></div>";
					

					$numIt++;

				} while($myrowPrice = $db->get_array($resultPrice));
				
				
				$resultPrice = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}' ORDER BY K desc, tm desc");
				$myrowPrice = $db->get_array($resultPrice);
				$countPrice = $db->num_rows($resultPrice);
				
				if($countPrice > 1) {
					$raskrSpis = "<img src='{THEME}/images/razv.png' style='margin-left:5px;'>";
				} else {
					$raskrSpis = "";
				}
				

				$resultTf = $db->query("SELECT * FROM " . PREFIX . "_price_g WHERE cat='{$row[id]}'");
				$myrowTf = $db->get_array($resultTf);
				
				if($myrowPrice['price_yp'] == 0 || $myrowPrice['price_yp'] == "Нет") {
					$realPriceLitr = 0;
				}
				
				if($myrowTf['cvet_g'] == "true") {
				
				$ypak = "<div style='float:left;' id='toniz'>
								<p id='nazvkol2' style='letter-spacing:2px; text-align:center;'>упаковка</p>
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
								<p id='nazvkol2' style='letter-spacing:2px; padding-right:5px;'>Код прод.</p>
								<div class='tag6' id='num{$row[id]}' onclick='cng({$row[id]})' style='cursor:pointer;'><p>{$kod}$raskrSpis</p></div>
								<div style='display:none;' id='{$row[id]}'><br><br>{$hidkod}<br><br></div>
							</div>";
				
				
			
				} else {$ypak = "<div style='float:left;' id='{$row[id]}'><div id='num{$row[id]}' style='display:none;'></div></div>";
				}
				
				if($myrowTf['pack_g'] == "true") {
					$cvet = "<div style='float:left;' id='toniz'>
								<p id='nazvkol2' style='letter-spacing:2px; text-align:center;'>база и цвет</p> 
								<div class='tag4' id='num3{$row[id]}' onclick='cng({$row[id]})' style='width:{$widthCvet}px;'><p>{$myrowPrice['cvet']}</p></div>
								<div style='display:none;' id='cvet{$row[id]}'><br><br>{$hidCvet}<br><br></div>
							</div>";
				} else {$cvet = "<div style='float:left;' id='cvet{$row[id]}'><div id='num3{$row[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['blesk_g'] == "true") {
				$blesk = "<div style='float:left;' id='toniz'>
							<p id='nazvkol' style='letter-spacing:2px; text-align:center;'>степень блеска</p>
							<div class='tag5' id='num4{$row[id]}' onclick='cng({$row[id]})' style='width:{$widthBlesk}px;'><p>{$myrowPrice['blesk']}</p></div>
							<div style='display:none;' id='blesk{$row[id]}'><br><br>{$hidBlesk}<br><br></div>
						</div>";
				} else {$blesk = "<div style='float:left;' id='blesk{$row[id]}'><div id='num4{$row[id]}' style='display:none;'></div></div>";}
				
				if($myrowTf['price_yp_g'] == "true") {
				$styp = "<div style='float:left;' id='toniz'>
							<p id='nazvkol4' style='letter-spacing:2px; text-align:center;'>цена упаковки</p>
							<div class='tag2' id='num5{$row[id]}' onclick='cng({$row[id]})' style='width:{$widthPriceYp}px;'><p>{$pr} грн</p></div>
							<div style='display:none;' id='priceyp{$row[id]}'><br><br>{$hidPriceYp}<br><br></div>
						</div>";
				} else {$styp = "<div style='float:left;' id='priceyp{$row[id]}'><div id='num5{$row[id]}'> style='display:none;'</div></div>";}
				
				if($myrowTf['price_litr_g'] == "true") {
				$stl = "<div style='float:left;' id='toniz'>
							<p id='nazvkol'  style='letter-spacing:2px; text-align:center;'> цена за 1л</p>
							<div class='tag3' id='num6{$row[id]}' onclick='cng({$row[id]})' style='width:{$widthPriceLitr}px;'><p>{$prl} грн</p></div>
							<div style='display:none;' id='pricelitr{$row[id]}'><br><br>{$hidPriceLitr}<br><br></div>
						</div>";
				} else {$stl = "<div style='float:left;' id='pricelitr{$row[id]}'><div id='num6{$row[id]}' style='display:none;'></div></div>";}
				
				
				
				
				
				$price = "
				  <div style='clear:both; float;left; margin-right:130px; margin-top:-90px;'  id='showlogin' class='showlogin'>	
						{$kodp}{$ypak}{$cvet}{$blesk}{$styp}{$stl}
						<div class='showing'>
							
							<a href='' onclick='compare({$row[id]}); return false;' style='margin-top:33px; cursor:pointer;'><img src='{THEME}/images/buttons/compare.png'></a>
							<a onclick='intobusket({$row[id]}); return false;' style='cursor:pointer; margin:0px; padding:0px;'><img src='{THEME}/images/buttons/buy.png'></a>
							<a href='/index.php?do=feedback'><img src='{THEME}/images/buttons/complane.png'></a>
						<!--	<a {$go_page}href=\"" . $full_link . "\"><img src='{THEME}/images/buttons/podrobnee.png' alt='Подробнее' border='0'></a>-->
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

				if($_SERVER['REQUEST_URI'] == "/" || $_SERVER['REQUEST_URI'] == "/index.php") {
					$tpl->set("{podr}", "");
				} else {
					$tpl->set("{podr}", "<img src='{THEME}/images/buttons/podrobnee.png' alt='Подробнее' border='0' id='podrobnee'>");
				}
				if($price != "") {
					$tpl->set( '{top}', 70);
				} else {
					$tpl->set( '{top}', 10);
				}
				$tpl->set( '{price}', $price);
			
			
			$tpl->set( '{title}', stripslashes( $row['title'] ) );
			$tpl->set( '{short-story}', stripslashes( "<div id=\"news-id-" . $row['id'] . "\" style=\"display:inline;\">" . $row['short_story'] . "</div>" ) );
		
		}
		
		$tpl->compile( 'content' );

		if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->result['content'] = preg_replace( "'\[hide\](.*?)\[/hide\]'si", "\\1", $tpl->result['content']);
		else $tpl->result['content'] = preg_replace ( "'\[hide\](.*?)\[/hide\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $tpl->result['content'] );

	}
	
	$tpl->clear();
	$db->free( $sql_result );
	
	if( $do == "" ) $do = $subaction;
	if( $do == "" and $year ) $do = "date";
	$ban_short = array ();
	unset( $ban_short );

	if( ! $news_found and $allow_userinfo and $member_id['name'] == $user and $user_group[$member_id['user_group']]['allow_adds'] ) {
		$tpl->load_template( 'info.tpl' );
		$tpl->set( '{error}', $lang['mod_list_f'] );
		$tpl->set( '{title}', $lang['all_info'] );
		$tpl->compile( 'content' );
		$tpl->clear();
	} elseif( !$news_found and $do == 'newposts' and $view_template != 'rss') {
		msgbox( $lang['all_info'], $lang['newpost_notfound'] );
	} elseif( ! $news_found and ! $allow_userinfo and $do != '' and $do != 'favorites' and $view_template != 'rss' ) {
		if ( $newsmodule ) @header( "HTTP/1.0 404 Not Found" );
		msgbox( $lang['all_err_1'], $lang['news_err_27'] );
	} elseif( ! $news_found and $catalog != "" ) {
		if ( $newsmodule ) @header( "HTTP/1.0 404 Not Found" );
		msgbox( $lang['all_err_1'], $lang['news_err_27'] );
	} elseif( ! $news_found and $do == 'favorites' ) {

		if ( $member_id['favorites'] AND !$count_all ) $db->query( "UPDATE " . USERPREFIX . "_users SET favorites='' WHERE user_id = '{$member_id['user_id']}'" );

		if (!$count_all) msgbox( $lang['all_info'], $lang['fav_notfound'] ); else msgbox( $lang['all_info'], $lang['fav_notfound_1'] );
	}
	
	//####################################################################################################################
	//         Навигация по новостям
	//####################################################################################################################
	if( ! isset( $view_template ) and $count_all ) {
		
		$tpl->load_template( 'navigation.tpl' );
		
		//----------------------------------
		// Previous link
		//----------------------------------
		

		$no_prev = false;
		$no_next = false;
		
		if( isset( $cstart ) and $cstart != "" and $cstart > 0 ) {
			$prev = $cstart / $config['news_number'];
			
			if( $config['allow_alt_url'] == "yes" ) {

				if ($prev == 1)
					$prev_page = $url_page . "/";
				else
					$prev_page = $url_page . "/page/" . $prev . "/";

				$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"" . $prev_page . "\">\\1</a>" );

			} else {

				if ($prev == 1)
					$prev_page = $PHP_SELF . "?" . $user_query;
				else
					$prev_page = $PHP_SELF . "?cstart=" . $prev . "&amp;" . $user_query;

				$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"" . $prev_page . "\">\\1</a>" );
			}
		
		} else {
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>" );
			$no_prev = TRUE;
		}
		
		//----------------------------------
		// Pages
		//----------------------------------
		if( $config['news_number'] ) {

			$pages = "";
			
			if( $count_all > $config['news_number'] ) {
				
				$enpages_count = @ceil( $count_all / $config['news_number'] );
				
				$cstart = ($cstart / $config['news_number']) + 1;
				
				if( $enpages_count <= 10 ) {
					
					for($j = 1; $j <= $enpages_count; $j ++) {
						
						if( $j != $cstart ) {
							
							if( $config['allow_alt_url'] == "yes" ) {

								if ($j == 1)
									$pages .= "<a href=\"" . $url_page . "/\">$j</a> ";
								else
									$pages .= "<a href=\"" . $url_page . "/page/" . $j . "/\">$j</a> ";

							} else {

								if ($j == 1)
									$pages .= "<a href=\"$PHP_SELF?{$user_query}\">$j</a> ";
								else
									$pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;$user_query\">$j</a> ";

							}
						
						} else {
							
							$pages .= "<span>$j</span> ";
						}
					
					}
				
				} else {
					
					$start = 1;
					$end = 10;
					$nav_prefix = "<span class=\"nav_ext\">{$lang['nav_trennen']}</span> ";
					
					if( $cstart > 0 ) {
						
						if( $cstart > 6 ) {
							
							$start = $cstart - 4;
							$end = $start + 8;
							
							if( $end >= $enpages_count ) {
								$start = $enpages_count - 9;
								$end = $enpages_count - 1;
								$nav_prefix = "";
							} else
								$nav_prefix = "<span class=\"nav_ext\">{$lang['nav_trennen']}</span> ";
						
						}
					
					}
					
					if( $start >= 2 ) {
						
						if( $config['allow_alt_url'] == "yes" ) $pages .= "<a href=\"" . $url_page . "/\">1</a> <span class=\"nav_ext\">{$lang['nav_trennen']}</span> ";
						else $pages .= "<a href=\"$PHP_SELF?{$user_query}\">1</a> <span class=\"nav_ext\">{$lang['nav_trennen']}</span> ";
					
					}
					
					for($j = $start; $j <= $end; $j ++) {
						
						if( $j != $cstart ) {

							if( $config['allow_alt_url'] == "yes" ) {

								if ($j == 1)
									$pages .= "<a href=\"" . $url_page . "/\">$j</a> ";
								else
									$pages .= "<a href=\"" . $url_page . "/page/" . $j . "/\">$j</a> ";

							} else {

								if ($j == 1)
									$pages .= "<a href=\"$PHP_SELF?{$user_query}\">$j</a> ";
								else
									$pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;$user_query\">$j</a> ";

							}
						
						} else {
							
							$pages .= "<span>$j</span> ";
						}
					
					}
					
					if( $cstart != $enpages_count ) {
						
						if( $config['allow_alt_url'] == "yes" ) $pages .= $nav_prefix . "<a href=\"" . $url_page . "/page/{$enpages_count}/\">{$enpages_count}</a>";
						else $pages .= $nav_prefix . "<a href=\"$PHP_SELF?cstart={$enpages_count}&amp;$user_query\">{$enpages_count}</a>";
					
					} else
						$pages .= "<span>{$enpages_count}</span> ";
				
				}
			
			}
			$tpl->set( '{pages}', $pages );
		}
		
		//----------------------------------
		// Next link
		//----------------------------------
		if( $config['news_number'] and $config['news_number'] < $count_all and $i < $count_all ) {
			$next_page = $i / $config['news_number'] + 1;
			
			if( $config['allow_alt_url'] == "yes" ) {
				$next = $url_page . '/page/' . $next_page . '/';
				$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"" . $next . "\">\\1</a>" );
			} else {
				$next = $PHP_SELF . "?cstart=" . $next_page . "&amp;" . $user_query;
				$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"" . $next . "\">\\1</a>" );
			}
			;
		
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