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
 Файл: show.full.php
-----------------------------------------------------
 Назначение: Просмотр полной новости и комментариев
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

	
	$sql_result = $db->query( $sql_news );
	
	$allow_list = explode( ',', $user_group[$member_id['user_group']]['allow_cats'] );
	
	$perm = 1;
	$i = 0;
	$news_found = false;
	
	while ( $row = $db->get_row( $sql_result ) ) {
		
		if( $i ) break;
		
		$xfields = xfieldsload();
		$options = news_permission( $row['access'] );
		
		if( $row['votes'] and $view_template != "print" ) include_once ENGINE_DIR . '/modules/poll.php';
		
		if( ! $row['category'] ) {
			$my_cat = "---";
			$my_cat_link = "---";
		} else {
			
			$my_cat = array ();
			$my_cat_link = array ();
			$cat_list = explode( ',', $row['category'] );
			
			if( count( $cat_list ) == 1 ) {
				
				if( $allow_list[0] != "all" and ! in_array( $cat_list[0], $allow_list ) ) $perm = 0;
				
				$my_cat[] = $cat_info[$cat_list[0]]['name'];
				
				$my_cat_link = get_categories( $cat_list[0] );
			
			} else {
				
				foreach ( $cat_list as $element ) {
					
					if( $allow_list[0] != "all" and ! in_array( $element, $allow_list ) ) $perm = 0;
					
					if( $element ) {
						$my_cat[] = $cat_info[$element]['name'];
						if( $config['allow_alt_url'] == "yes" ) $my_cat_link[] = "<a href=\"" . $config['http_home_url'] . get_url( $element ) . "/\">{$cat_info[$element]['name']}</a>";
						else $my_cat_link[] = "<a href=\"$PHP_SELF?do=cat&amp;category={$cat_info[$element]['alt_name']}\">{$cat_info[$element]['name']}</a>";
					}
				}
				
				$my_cat_link = implode( ', ', $my_cat_link );
			}
			
			$my_cat = implode( ', ', $my_cat );
		}
		
		$row['category'] = intval( $row['category'] );
		$category_id = $row['category'];
		
		if( isset( $view_template ) and $view_template == "print" ) $tpl->load_template( 'print.tpl' );
		elseif( $category_id and $cat_info[$category_id]['full_tpl'] != '' ) $tpl->load_template( $cat_info[$category_id]['full_tpl'] . '.tpl' );
		else $tpl->load_template( 'fullstory.tpl' );
		
		if( $options[$member_id['user_group']] and $options[$member_id['user_group']] != 3 ) $perm = 1;
		if( $options[$member_id['user_group']] == 3 ) $perm = 0;
		
		if( $options[$member_id['user_group']] == 1 ) $user_group[$member_id['user_group']]['allow_addc'] = 0;
		if( $options[$member_id['user_group']] == 2 ) $user_group[$member_id['user_group']]['allow_addc'] = 1;
		
		if( ! $row['approve'] and $member_id['name'] != $row['autor'] and $member_id['user_group'] != '1' ) $perm = 0;
		if( ! $row['approve'] ) $allow_comments = false;
		
		if( ! $perm ) break;

		if( $config['allow_read_count'] == "yes" AND !$news_page AND !$cstart) {
			if( $config['cache_count'] ) $db->query( "INSERT INTO " . PREFIX . "_views (news_id) VALUES ('{$row['id']}')" );
			else $db->query( "UPDATE " . PREFIX . "_post set news_read=news_read+1 where id='{$row['id']}'" );
		}
		
		$news_found = TRUE;
		$row['date'] = strtotime( $row['date'] );
		
		if( (strlen( $row['full_story'] ) < 13) and (strpos( $tpl->copy_template, "{short-story}" ) === false) ) {
			$row['full_story'] = $row['short_story'];
		}
		
		if( ! $news_page ) {
			$news_page = 1;
		}

		
		$news_seiten = explode( "{PAGEBREAK}", $row['full_story'] );
		$anzahl_seiten = count( $news_seiten );
		
		if( $news_page <= 0 or $news_page > $anzahl_seiten ) {
			
			$news_page = 1;
		}
		
		if( $config['allow_alt_url'] == "yes" ) {
			
			if( $row['flag'] and $config['seo_type'] ) {
				
				if( $category_id and $config['seo_type'] == 2 ) {
					
					$full_link = $config['http_home_url'] . get_url( $category_id ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
					$print_link = $config['http_home_url'] . get_url( $category_id ) . "/print:page,1," . $row['id'] . "-" . $row['alt_name'] . ".html";
					$short_link = $config['http_home_url'] . get_url( $category_id ) . "/";
					$row['alt_name'] = $row['id'] . "-" . $row['alt_name'];
					$link_page = $config['http_home_url'] . get_url( $category_id ) . "/" . 'page,' . $news_page . ',';
					$news_name = $row['alt_name'];
				
				} else {
					
					$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
					$print_link = $config['http_home_url'] . "print:page,1," . $row['id'] . "-" . $row['alt_name'] . ".html";
					$short_link = $config['http_home_url'];
					$row['alt_name'] = $row['id'] . "-" . $row['alt_name'];
					$link_page = $config['http_home_url'] . 'page,' . $news_page . ',';
					$news_name = $row['alt_name'];
				
				}
			
			} else {
				
				$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . $row['alt_name'] . ".html";
				$print_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . "print:page,1," . $row['alt_name'] . ".html";
				$short_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] );
				$link_page = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . 'page,' . $news_page . ',';
				$news_name = $row['alt_name'];
			
			}
		
		} else {
			
			$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
			$print_link = $config['http_home_url'] . "engine/print.php?newsid=" . $row['id'];
			$short_link = "";
		
		}
		
		$i ++;
		
		//
		// обработка страниц
		//
		if( isset($view_template) AND $view_template == "print" ) {
			
			$row['full_story'] = str_replace( "{PAGEBREAK}", "", $row['full_story'] );
			$row['full_story'] = str_replace( "{pages}", "", $row['full_story'] );
			$row['full_story'] = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "\\2", $row['full_story'] );

		
		} else {
			
			$row['full_story'] = $news_seiten[$news_page - 1];
			
			$row['full_story'] = preg_replace( '#(\A[\s]*<br[^>]*>[\s]*|<br[^>]*>[\s]*\Z)#is', '', $row['full_story'] ); // remove <br/> at end of string
			$news_seiten = "";
			unset( $news_seiten );
			
			if( $anzahl_seiten > 1 ) {
				
				if( $news_page < $anzahl_seiten ) {
					$pages = $news_page + 1;
					
					if( $config['allow_alt_url'] == "yes" ) {
						$nextpage = " | <a href=\"" . $short_link . "page," . $pages . "," . $row['alt_name'] . ".html\">" . $lang['news_next'] . "</a>";
					} else {
						$nextpage = " | <a href=\"$PHP_SELF?newsid=" . $row['id'] . "&amp;news_page=" . $pages . "\">" . $lang['news_next'] . "</a>";
					}
				}
				
				if( $news_page > 1 ) {
					$pages = $news_page - 1;
					
					if( $config['allow_alt_url'] == "yes" ) {
						$prevpage = "<a href=\"" . $short_link . "page," . $pages . "," . $row['alt_name'] . ".html\">" . $lang['news_prev'] . "</a> | ";
					} else {
						$prevpage = "<a href=\"$PHP_SELF?newsid=" . $row['id'] . "&amp;news_page=" . $pages . "\">" . $lang['news_prev'] . "</a> | ";
					}
				}
				
				$tpl->set( '{pages}', $prevpage . $lang['news_site'] . " " . $news_page . $lang['news_iz'] . $anzahl_seiten . $nextpage );
				
				if( $config['allow_alt_url'] == "yes" ) {
					
					$replacepage = "<a href=\"" . $short_link . "page," . "\\1" . "," . $row['alt_name'] . ".html\">\\2</a>";
				
				} else {
					
					$replacepage = "<a href=\"$PHP_SELF?newsid=" . $row['id'] . "&amp;news_page=\\1\">\\2</a>";
				}
				
				$row['full_story'] = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", $replacepage, $row['full_story'] );
			
			} else {
				
				$tpl->set( '{pages}', '' );
				$row['full_story'] = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "", $row['full_story'] );
			}
		}
		
		$metatags['title'] = stripslashes( $row['title'] );
		$comments_num = $row['comm_num'];
		
		$news_find = array ('{comments-num}' => $comments_num, '{views}' => $row['news_read'], '{category}' => $my_cat, '{link-category}' => $my_cat_link, '{news-id}' => $row['id'] );
		
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
		
		if( $row['editdate'] ) $_DOCUMENT_DATE = $row['editdate'];
		else $_DOCUMENT_DATE = $row['date'];
		
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
		
		$tpl->set( '', $news_find );
		
		if( $cat_info[$row['category']]['icon'] ) {
			
			$tpl->set( '{category-icon}', $cat_info[$row['category']]['icon'] );
		
		} else {
			
			$tpl->set( '{category-icon}', "{THEME}/dleimages/no_icon.gif" );
		
		}
		
		// Ссылки на версию для печати
		if ($config['allow_search_print']) {

			$tpl->set( '[print-link]', "<a href=\"" . $print_link . "\">" );
			$tpl->set( '[/print-link]', "</a>" );

		} else {

			$tpl->set( '[print-link]', "<a href=\"" . $print_link . "\" rel=\"nofollow\">" );
			$tpl->set( '[/print-link]', "</a>" );

		}
		// Ссылки на версию для печати
		

		if( $row['allow_rate'] ) $tpl->set( '{rating}', ShowRating( $row['id'], $row['rating'], $row['vote_num'], $user_group[$member_id['user_group']]['allow_rating'] ) );
		else $tpl->set( '{rating}', "" );
		
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
		
		$_SESSION['referrer'] = $_SERVER['REQUEST_URI'];
		
		$tpl->set( '[full-link]', "<a href=\"" . $full_link . "\">" );
		$tpl->set( '[/full-link]', "</a>" );
		
		$tpl->set( '{full-link}', $full_link );
		
		if( $row['allow_comm'] ) {
			
			$tpl->set( '[com-link]', "<a href=\"" . $full_link . "\">" );
			$tpl->set( '[/com-link]', "</a>" );
		
		} else
			$tpl->set_block( "'\\[com-link\\](.*?)\\[/com-link\\]'si", "" );
		
		if( ! $row['approve'] and ($member_id['name'] == $row['autor'] and ! $user_group[$member_id['user_group']]['allow_all_edit']) ) {
			$tpl->set( '[edit]', "<a href=\"" . $config['http_home_url'] . "index.php?do=addnews&amp;id=" . $row['id'] . "\" >" );
			$tpl->set( '[/edit]', "</a>" );
			if( $config['allow_quick_wysiwyg'] ) $allow_comments_ajax = true;
		} elseif( $is_logged and (($member_id['name'] == $row['autor'] and $user_group[$member_id['user_group']]['allow_edit']) or $user_group[$member_id['user_group']]['allow_all_edit']) ) {
			$tpl->set( '[edit]', "<a onclick=\"return dropdownmenu(this, event, MenuNewsBuild('" . $row['id'] . "', 'full'), '170px')\" href=\"#\">" );
			$tpl->set( '[/edit]', "</a>" );
			if( $config['allow_quick_wysiwyg'] ) $allow_comments_ajax = true;
		} else
			$tpl->set_block( "'\\[edit\\](.*?)\\[/edit\\]'si", "" );
		
		if( $config['related_news'] ) {
			
			if( $config['allow_cache'] != "yes" ) {
				$config['allow_cache'] = "yes";
				$revert_cache = true;
			} else
				$revert_cache = false;
			
			$buffer = dle_cache( "related", $row['id'] );
			
			if( $buffer === FALSE ) {
				
				if( strlen( $row['full_story'] ) < strlen( $row['short_story'] ) ) $body = $row['short_story'];
				else $body = $row['full_story'];
				
				$body = $db->safesql( strip_tags( stripslashes( $metatags['title'] . " " . $body ) ) );
				
				$config['related_number'] = intval( $config['related_number'] );
				if( $config['related_number'] < 1 ) $config['related_number'] = 5;
				
				$db->query( "SELECT id, title, date, category, alt_name, flag FROM " . PREFIX . "_post WHERE MATCH (title, short_story, full_story, xfields) AGAINST ('$body') AND id != " . $row['id'] . " AND approve='1'" . $where_date . " LIMIT " . $config['related_number'] );
				
				while ( $related = $db->get_row() ) {
					
					$related['date'] = strtotime( $related['date'] );
					$related['category'] = intval( $related['category'] );
					
					if( dle_strlen( $related['title'], $config['charset'] ) > 75 ) $related['title'] = dle_substr( $related['title'], 0, 75, $config['charset'] ) . " ...";
					
					if( $config['allow_alt_url'] == "yes" ) {
						
						if( $related['flag'] and $config['seo_type'] ) {
							
							if( $related['category'] and $config['seo_type'] == 2 ) {
								
								$full_link = $config['http_home_url'] . get_url( $related['category'] ) . "/" . $related['id'] . "-" . $related['alt_name'] . ".html";
							
							} else {
								
								$full_link = $config['http_home_url'] . $related['id'] . "-" . $related['alt_name'] . ".html";
							
							}
						
						} else {
							
							$full_link = $config['http_home_url'] . date( 'Y/m/d/', $related['date'] ) . $related['alt_name'] . ".html";
						}
					
					} else {
						
						$full_link = $config['http_home_url'] . "index.php?newsid=" . $related['id'];
					
					}
					
					$buffer .= "<li><a href=\"" . $full_link . "\">" . stripslashes( $related['title'] ) . "</a></li>";
				
				}
				
				$db->free();
				create_cache( "related", $buffer, $row['id'] );
			}
			
			if ( $buffer ) {

				$tpl->set( '[related-news]', "" );
				$tpl->set( '[/related-news]', "" );

			} else $tpl->set_block( "'\\[related-news\\](.*?)\\[/related-news\\]'si", "" );

			$tpl->set( '{related-news}', $buffer );
			
			if( $revert_cache ) $config['allow_cache'] = "no";
		
		}
		
		if( $is_logged ) {
			
			$fav_arr = explode( ',', $member_id['favorites'] );
			
			if( ! in_array( $row['id'], $fav_arr ) ) $tpl->set( '{favorites}', "<a id=\"fav-id-" . $row['id'] . "\" href=\"$PHP_SELF?do=favorites&amp;doaction=add&amp;id=" . $row['id'] . "\"><img src=\"" . $config['http_home_url'] . "templates/{$config['skin']}/dleimages/plus_fav.gif\" onclick=\"doFavorites('" . $row['id'] . "', 'plus'); return false;\" title=\"" . $lang['news_addfav'] . "\" style=\"vertical-align: middle;border: none;\" alt=\"\" /></a>" );
			else $tpl->set( '{favorites}', "<a id=\"fav-id-" . $row['id'] . "\" href=\"$PHP_SELF?do=favorites&amp;doaction=del&amp;id=" . $row['id'] . "\"><img src=\"" . $config['http_home_url'] . "templates/{$config['skin']}/dleimages/minus_fav.gif\" onclick=\"doFavorites('" . $row['id'] . "', 'minus'); return false;\" title=\"" . $lang['news_minfav'] . "\" style=\"vertical-align: middle;border: none;\" alt=\"\" /></a>" );
		
		} else
			$tpl->set( '{favorites}', "" );
		
		if( $row['votes'] ) $tpl->set( '{poll}', $tpl->result['poll'] );
		else $tpl->set( '{poll}', '' );
		
		if( $config['allow_banner'] ) include_once ENGINE_DIR . '/modules/banners.php';
		
		if( $config['allow_banner'] AND count( $banners ) ) {
			
			foreach ( $banners as $name => $value ) {
				$tpl->copy_template = str_replace( "{banner_" . $name . "}", $value, $tpl->copy_template );
			}
		}
		
		$tpl->set_block( "'{banner_(.*?)}'si", "" );
		
		if( strpos( $tpl->copy_template, "[category=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[category=(.+?)\\](.*?)\\[/category\\]#ies", "check_category('\\1', '\\2', '{$row['category']}')", $tpl->copy_template );
		}
		
		if( strpos( $tpl->copy_template, "[not-category=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[not-category=(.+?)\\](.*?)\\[/not-category\\]#ies", "check_category('\\1', '\\2', '{$row['category']}', false)", $tpl->copy_template );
		}
		
		$tpl->set( '{title}', $metatags['title'] );


		if ($smartphone_detected) {

			if (!$config['allow_smart_format']) {

					$row['short_story'] = strip_tags( $row['short_story'], '<p><br><a>' );
					$row['full_story'] = strip_tags( $row['full_story'], '<p><br><a>' );

			} else {

				if ( !$config['allow_smart_images'] ) {
	
					$row['short_story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['short_story'] );
					$row['short_story'] = preg_replace( "#<img(.+?)>#is", "", $row['short_story'] );
					$row['full_story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['full_story'] );
					$row['full_story'] = preg_replace( "#<img(.+?)>#is", "", $row['full_story'] );
	
				}
	
				if ( !$config['allow_smart_video'] ) {
	
					$row['short_story'] = preg_replace( "#<!--dle_video_begin(.+?)<!--dle_video_end-->#is", "", $row['short_story'] );
					$row['short_story'] = preg_replace( "#<!--dle_audio_begin(.+?)<!--dle_audio_end-->#is", "", $row['short_story'] );
					$row['full_story'] = preg_replace( "#<!--dle_video_begin(.+?)<!--dle_video_end-->#is", "", $row['full_story'] );
					$row['full_story'] = preg_replace( "#<!--dle_audio_begin(.+?)<!--dle_audio_end-->#is", "", $row['full_story'] );
	
				}

			}

		}
		$tpl->set( '{comments}', "<!--dlecomments-->" );
		$tpl->set( '{addcomments}', "<!--dleaddcomments-->" );
		$tpl->set( '{navigation}', "<!--dlenavigationcomments-->" );

		$tpl->set( '{short-story}', stripslashes( $row['short_story'] ) );
		$tpl->set( '{full-story}', stripslashes( "<div id=\"news-id-" . $row['id'] . "\" style=\"display:inline;\">" . $row['full_story'] . "</div>" ) );
		
		if( $row['keywords'] == '' and $row['descr'] == '' ) create_keywords( $row['short_story'] . $row['full_story'] );
		else {
			$metatags['keywords'] = $row['keywords'];
			$metatags['description'] = $row['descr'];
		}

		if ($row['metatitle']) $metatags['header_title'] = $row['metatitle'];

		if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) {
			
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
		
		
		$countPrice = 0;
			$ff = new db;
			$resultPrice = $ff->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}' ORDER BY k DESC, tm DESC");
			$myrowPrice = $ff->get_array($resultPrice);
			$countPrice = $ff->num_rows($resultPrice);
			
			
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
					$widthPriceLitr = $numsPriceLitr*8;
					
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
					
					$resultUser = $db->query("SELECT * FROM " . PREFIX . "_disc WHERE users_id='$_COOKIE[dle_user_id]' AND proizv LIKE '{$current}%'");
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
				
				
				$resultPrice = $db->query("SELECT * FROM " . PREFIX . "_price WHERE thisid='{$row[id]}' ORDER BY k desc, tm desc");
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
				
			
				} else {$ypak = "<div style='float:left;' id='{$row[id]}'><div id='num{$row[id]}' style='display:none;'></div></div>";}
				
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
				  <div style='clear:both; float;left; margin-right:130px; margin-top:-27px;'  id='showlogin'>	
						{$kodp}{$ypak}{$cvet}{$blesk}{$styp}{$stl}
						<div style='float:left; width:75px; margin-top:35px; margin-left:2px; margin-right:-15px;'>
							<a href='' onclick='compare({$row[id]}); return false;' style='margin-top:33px; cursor:pointer;'><img src='{THEME}/images/buttons/compare.png'></a>
							<a onclick='intobusket({$row[id]}); return false;' style='cursor:pointer; margin:0px; padding:0px;'><img src='{THEME}/images/buttons/buy.png'></a>
						</div>
						
						<form>
							<input type='hidden' id='idd{$row[id]}' value='{$myrowPrice[id]}'>
						</form>
				  </div>
				  <div id='pojceny'><a href='/index.php?do=feedback'><img src='{THEME}/images/buttons/complane.png'></a><br><br><br><br></div>";
				  
				  ?>

                  	<script type="text/javascript">
						
						
						s=1;

						function cng(v) {
							
							if(s==1) {
								document.getElementById(v).style.display = "block"; 
								document.getElementById("cvet" + v).style.display = "block";
								document.getElementById("blesk" + v).style.display = "block";
								document.getElementById("priceyp" + v).style.display = "block";
								document.getElementById("pricelitr" + v).style.display = "block";
								s=2;
							} else if(s==2) {
								document.getElementById(v).style.display = "none";
								document.getElementById("cvet" + v).style.display = "none"; 
								document.getElementById("blesk" + v).style.display = "none";
								document.getElementById("priceyp" + v).style.display = "none";
								document.getElementById("pricelitr" + v).style.display = "none";
								s=1;
							}
							
						}
						
						function fade(f1,f2) {
							tagArray11 = document.getElementsByClassName("tag11" + f2);
							tagArray22 = document.getElementsByClassName("tag22" + f2);
							tagArray33 = document.getElementsByClassName("tag33" + f2);
							tagArray44 = document.getElementsByClassName("tag44" + f2);
							tagArray55 = document.getElementsByClassName("tag55" + f2);
							
						
								tagArray11[f1].style.color = "#015283";
								tagArray22[f1].style.color = "#015283";
								tagArray33[f1].style.color = "#015283";
								tagArray44[f1].style.color = "#015283";
								tagArray55[f1].style.color = "#015283";
								
							
							
						}
						
						function fadeout(f1,f2) {
							tagArray11 = document.getElementsByClassName("tag11" + f2);
							tagArray22 = document.getElementsByClassName("tag22" + f2);
							tagArray33 = document.getElementsByClassName("tag33" + f2);
							tagArray44 = document.getElementsByClassName("tag44" + f2);
							tagArray55 = document.getElementsByClassName("tag55" + f2);
							
							
								tagArray11[f1].style.color = "#007cc7";
								tagArray22[f1].style.color = "#007cc7";
								tagArray33[f1].style.color = "#007cc7";
								tagArray44[f1].style.color = "#007cc7";
								tagArray55[f1].style.color = "#007cc7";
							
						}
						
						
					</script>
                  <?php
				  
			}
		
		
		$tpl->set( '{price}', $price);
		$tpl->compile( 'content' );

		if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->result['content'] = preg_replace( "'\[hide\](.*?)\[/hide\]'si", "\\1", $tpl->result['content']);
		else $tpl->result['content'] = preg_replace ( "'\[hide\](.*?)\[/hide\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $tpl->result['content'] );

		
		$news_id = $row['id'];
		$allow_comments = $row['allow_comm'];

		$allow_add = true;

		if ( $config['max_comments_days'] ) {

			if ($row['date'] < ($_TIME - ($config['max_comments_days'] * 3600 * 24)) )	$allow_add = false;

		}
		
		if( isset( $view_template ) ) $allow_comments = false;
	
	}
	
	$tpl->clear();
	$db->free( $sql_result );
	
	if( $config['files_allow'] == "yes" ) if( strpos( $tpl->result['content'], "[attachment=" ) !== false ) {
		$tpl->result['content'] = show_attach( $tpl->result['content'], $news_id );
	}
	
	if( ! $news_found and ! $perm ) msgbox( $lang['all_err_1'], "<b>{$user_group[$member_id['user_group']]['group_name']}</b> " . $lang['news_err_28'] );
	elseif( ! $news_found ) {
		@header( "HTTP/1.0 404 Not Found" );
		msgbox( $lang['all_err_1'], $lang['news_err_12'] );
	}

//####################################################################################################################
//		 Просмотр комментариев
//####################################################################################################################
if( $allow_comments AND $news_found) {
	
	if( $comments_num > 0 ) {

		include_once ENGINE_DIR . '/classes/comments.class.php';
		$comments = new DLE_Comments( $db, $comments_num, $config['comm_nummers'] );

		if( $config['comm_msort'] == "" ) $config['comm_msort'] = "ASC";

		if( $config['allow_cmod'] ) $where_approve = " AND " . PREFIX . "_comments.approve='1'";
		else $where_approve = "";

		$comments->query = "SELECT " . PREFIX . "_comments.id, post_id, " . PREFIX . "_comments.user_id, date, autor as gast_name, " . PREFIX . "_comments.email as gast_email, text, ip, is_register, name, " . USERPREFIX . "_users.email, news_num, comm_num, user_group, reg_date, signature, foto, fullname, land, icq, xfields FROM " . PREFIX . "_comments LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id WHERE " . PREFIX . "_comments.post_id = '$news_id'" . $where_approve . " ORDER BY date " . $config['comm_msort'];

		$comments->build_comments('comments.tpl', 'news' );

		unset ($tpl->result['comments']);

		if( isset($_GET['news_page']) AND $_GET['news_page'] ) $user_query = "newsid=" . $newsid . "&amp;news_page=" . intval( $_GET['news_page'] ); else $user_query = "newsid=" . $newsid;

		$comments->build_navigation('navigation.tpl', $link_page . "{page}," . $news_name . ".html#comment", $user_query);		

		unset ($comments);
		unset ($tpl->result['commentsnavigation']);
	
	}

	if (!isset($member_id['restricted'])) $member_id['restricted'] = false;
	
	if( $member_id['restricted'] AND $member_id['restricted_days'] AND $member_id['restricted_date'] < $_TIME ) {
		
		$member_id['restricted'] = 0;
		$db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET restricted='0', restricted_days='0', restricted_date='' WHERE user_id='{$member_id['user_id']}'" );
	
	}
	
	if( $user_group[$member_id['user_group']]['allow_addc'] AND $config['allow_comments'] == "yes" AND $allow_add AND ($member_id['restricted'] != 2 AND $member_id['restricted'] != 3) ) {

		if( !$comments_num ) {		
			if( strpos ( $tpl->result['content'], "<!--dlecomments-->" ) !== false ) {
	
				$tpl->result['content'] = str_replace ( "<!--dlecomments-->", "\n<div id=\"dle-ajax-comments\"></div>\n", $tpl->result['content'] );
	
			} else $tpl->result['content'] .= "\n<div id=\"dle-ajax-comments\"></div>\n";
		}
		
		$tpl->load_template( 'addcomments.tpl' );

		if ($config['allow_subscribe'] AND $user_group[$member_id['user_group']]['allow_subscribe']) $allow_subscribe = true; else $allow_subscribe = false;
		
		if( $config['allow_comments_wysiwyg'] == "yes" ) {
			include_once ENGINE_DIR . '/editor/comments.php';
			$bb_code = "";
			$allow_comments_ajax = true;
		} else
			include_once ENGINE_DIR . '/modules/bbcode.php';
		
		if( $user_group[$member_id['user_group']]['captcha'] ) {

			if ( $config['allow_recaptcha'] ) {

				$tpl->set( '[recaptcha]', "" );
				$tpl->set( '[/recaptcha]', "" );

				$tpl->set( '{recaptcha}', '<div id="dle_recaptcha"></div>' );

				$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );
				$tpl->set( '{reg_code}', "" );

			} else {

				$tpl->set( '[sec_code]', "" );
				$tpl->set( '[/sec_code]', "" );
				$path = parse_url( $config['http_home_url'] );
				$tpl->set( '{sec_code}', "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot.php\" alt=\"${lang['sec_image']}\" border=\"0\" alt=\"\" /><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a></span>" );
				$tpl->set_block( "'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "" );
				$tpl->set( '{recaptcha}', "" );
			}

		} else {
			$tpl->set( '{sec_code}', "" );
			$tpl->set( '{recaptcha}', "" );
			$tpl->set_block( "'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "" );
			$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );
		}

		if( $config['allow_comments_wysiwyg'] == "yes" ) {

			$tpl->set( '{editor}', $wysiwyg );

		} else {
			$tpl->set( '{editor}', $bb_code );

		}
		
		$tpl->set( '{text}', '' );
		$tpl->set( '{title}', $lang['news_addcom'] );
		
		if( ! $is_logged ) {
			$tpl->set( '[not-logged]', '' );
			$tpl->set( '[/not-logged]', '' );
		} else
			$tpl->set_block( "'\\[not-logged\\](.*?)\\[/not-logged\\]'si", "" );
		
		if( $is_logged ) $hidden = "<input type=\"hidden\" name=\"name\" id=\"name\" value=\"{$member_id['name']}\" /><input type=\"hidden\" name=\"mail\" id=\"mail\" value=\"\" />";
		else $hidden = "";
		
		$tpl->copy_template = "<form  method=\"post\" name=\"dle-comments-form\" id=\"dle-comments-form\" action=\"{$_SESSION['referrer']}\">" . $tpl->copy_template . "
		<input type=\"hidden\" name=\"subaction\" value=\"addcomment\" />{$hidden}
		<input type=\"hidden\" name=\"post_id\" id=\"post_id\" value=\"$news_id\" /></form>";

		if (!isset($path['path'])) $path['path'] = "/";

		$tpl->copy_template .= <<<HTML
<script language="javascript" type="text/javascript">
<!--
$(function(){

	$('#dle-comments-form').submit(function() {
	  doAddComments();
	  return false;
	});

});

function reload () {

	var rndval = new Date().getTime(); 

	document.getElementById('dle-captcha').innerHTML = '<img src="{$path['path']}engine/modules/antibot.php?rndval=' + rndval + '" border="0" width="120" height="50" alt="" /><br /><a onclick="reload(); return false;" href="#">{$lang['reload_code']}</a>';

};
//-->
</script>
HTML;

		if ( $config['allow_recaptcha'] ) {

		$tpl->copy_template .= <<<HTML
<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<script language="javascript" type="text/javascript">
<!--
$(function(){
	Recaptcha.create("{$config['recaptcha_public_key']}",
     "dle_recaptcha",
     {
       theme: "{$config['recaptcha_theme']}",
       lang:  "{$lang['wysiwyg_language']}"
     }
   );
});
//-->
</script>
HTML;
		
		}
		
		$tpl->compile( 'addcomments' );
		$tpl->clear();

		if ( strpos ( $tpl->result['content'], "<!--dleaddcomments-->" ) !== false ) {

			$tpl->result['content'] = str_replace ( "<!--dleaddcomments-->", $tpl->result['addcomments'], $tpl->result['content'] );

		} else {

			$tpl->result['content'] .= $tpl->result['addcomments'];

		}

		unset ($tpl->result['addcomments']);

	} elseif( $member_id['restricted'] ) {
		
		$tpl->load_template( 'info.tpl' );
		
		if( $member_id['restricted_days'] ) {
			
			$tpl->set( '{error}', $lang['news_info_2'] );
			$tpl->set( '{date}', langdate( "j F Y H:i", $member_id['restricted_date'] ) );
		
		} else
			$tpl->set( '{error}', $lang['news_info_3'] );
		
		$tpl->set( '{title}', $lang['all_info'] );
		$tpl->compile( 'content' );
		$tpl->clear();

	} elseif( !$allow_add ) {

		$tpl->load_template( 'info.tpl' );
		$tpl->set( '{error}', $lang['news_info_6'] );
		$tpl->set( '{days}', $config['max_comments_days'] );
		$tpl->set( '{title}', $lang['all_info'] );
		$tpl->compile( 'content' );
		$tpl->clear();
	
	} elseif( $config['allow_comments'] != "no") {
		
		$tpl->load_template( 'info.tpl' );
		$tpl->set( '{error}', $lang['news_info_1'] );
		$tpl->set( '{group}', $user_group[$member_id['user_group']]['group_name'] );
		$tpl->set( '{title}', $lang['all_info'] );
		$tpl->compile( 'content' );
		$tpl->clear();
	
	}
}
?>