var n_cache=[],c_cache=[],comm_edit_id,s_id,e_id;
function RunAjaxJS(a,b){var c=new Date,d=false;c=c.getTime();var e=/<script.*?>(.|[\r\n])*?<\/script>/ig,f=e.exec(b);if(f!=null){var g=Array(f.shift());for(d=true;f;){f=e.exec(b);f!=null&&g.push(f.shift())}for(e=0;e<g.length;e++)b=b.replace(g[e],'<span id="'+c+e+'" style="display:none;"></span>')}$("#"+a).html(b);if(d){d=/<script.*?>((.|[\r\n])*?)<\/script>/ig;for(e=0;e<g.length;e++){var h=document.getElementById(c+""+e);f=h.parentNode;f.removeChild(h);d.lastIndex=0;h=d.exec(g[e]);f=f.appendChild(document.createElement("script"));
f.text=h[1];h=g[e].substring(g[e].indexOf(" ",0),g[e].indexOf(">",0)).split(" ");if(h.length>1)for(var j=0;j<h.length;j++)if(h[j].length>0){var i=h[j].split("=");i[1]=i[1].substr(1,i[1].length-2);f.setAttribute(i[0],i[1])}}}}
function IPMenu(a,b,c,d){var e=[];e[0]='<a href="https://www.nic.ru/whois/?ip='+a+'" target="_blank">'+b+"</a>";e[1]='<a href="'+dle_root+dle_admin+"?mod=iptools&ip="+a+'" target="_blank">'+c+"</a>";e[2]='<a href="'+dle_root+dle_admin+"?mod=blockip&ip="+a+'" target="_blank">'+d+"</a>";return e}
function MenuCommBuild(a,b){var c=[];c[0]="<a onclick=\"ajax_comm_edit('"+a+"', '"+b+'\'); return false;" href="#">'+menu_short+"</a>";c[1]='<a href="'+dle_root+"?do=comments&action=comm_edit&id="+a+"&area="+b+'">'+menu_full+"</a>";return c}function ajax_cancel_for_edit(a){n_cache[a]!=""&&$("#news-id-"+a).html(n_cache[a]);return false}
function ajax_save_for_edit(a,b){var c=0,d="";e_id=a;if(document.getElementById("allow_br_"+a).checked)c=1;d=quick_wysiwyg=="1"?$("#dleeditnews"+a).html():$("#dleeditnews"+a).val();var e=$("#edit-title-"+a).val(),f=$("#edit-reason-"+a).val();ShowLoading("");$.post(dle_root+"engine/ajax/editnews.php",{title:e,news_txt:d,id:a,allow_br:c,reason:f,field:b,action:"save"},function(g){HideLoading("");n_cache[e_id]="";$("#news-id-"+a).html(g)});return false}
function ajax_prep_for_edit(a,b){if(!n_cache[a]||n_cache[a]=="")n_cache[a]=$("#news-id-"+a).html();s_id=a;ShowLoading("");$.get(dle_root+"engine/ajax/editnews.php",{id:a,field:b,action:"edit"},function(c){HideLoading("");RunAjaxJS("news-id-"+a,c);setTimeout(function(){$("html:not(:animated)"+(!$.browser.opera?",body:not(:animated)":"")).animate({scrollTop:$("#news-id-"+s_id).position().top-70},700)},100)});return false}
function ajax_comm_edit(a,b){if(!c_cache[a]||c_cache[a]=="")c_cache[a]=$("#comm-id-"+a).html();ShowLoading("");$.get(dle_root+"engine/ajax/editcomments.php",{id:a,area:b,action:"edit"},function(c){HideLoading("");RunAjaxJS("comm-id-"+a,c);setTimeout(function(){$("html:not(:animated)"+(!$.browser.opera?",body:not(:animated)":"")).animate({scrollTop:$("#comm-id-"+a).position().top-70},700)},100)});return false}
function ajax_cancel_comm_edit(a){n_cache[a]!=""&&$("#comm-id-"+a).html(c_cache[a]);return false}function ajax_save_comm_edit(a,b){var c="";comm_edit_id=a;c=dle_wysiwyg=="yes"?$("#dleeditcomments"+a).html():$("#dleeditcomments"+a).val();ShowLoading("");$.post(dle_root+"engine/ajax/editcomments.php",{id:a,comm_txt:c,area:b,action:"save"},function(d){HideLoading("");c_cache[comm_edit_id]="";$("#comm-id-"+a).html(d)});return false}
function DeleteComments(a,b){DLEconfirm(dle_del_agree,dle_confirm,function(){ShowLoading("");$.get(dle_root+"engine/ajax/deletecomments.php",{id:a,dle_allow_hash:b},function(c){HideLoading("");c=parseInt(c);if(!isNaN(c)){$("html"+(!$.browser.opera?",body":"")).animate({scrollTop:$("#comment-id-"+c).position().top-70},700);setTimeout(function(){$("#comment-id-"+c).hide("blind",{},1400)},700)}})})}
function doFavorites(a,b){ShowLoading("");$.get(dle_root+"engine/ajax/favorites.php",{fav_id:a,action:b,skin:dle_skin},function(c){HideLoading("");$("#fav-id-"+a).html(c)});return false}function CheckLogin(){var a=document.getElementById("name").value;ShowLoading("");$.post(dle_root+"engine/ajax/registration.php",{name:a},function(b){HideLoading("");$("#result-registration").html(b)});return false}
function doCalendar(a,b,c){ShowLoading("");$.get(dle_root+"engine/ajax/calendar.php",{month:a,year:b},function(d){HideLoading("");c=="left"?$("#calendar-layer").hide("slide",{direction:"left"},500).html(d).show("slide",{direction:"right"},500):$("#calendar-layer").hide("slide",{direction:"right"},500).html(d).show("slide",{direction:"left"},500)})}
function ShowBild(a){window.open(dle_root+"engine/modules/imagepreview.php?image="+a,"","resizable=1,HEIGHT=200,WIDTH=200, top=0, left=0, scrollbars=yes")}function doRate(a,b){ShowLoading("");$.get(dle_root+"engine/ajax/rating.php",{go_rate:a,news_id:b,skin:dle_skin},function(c){HideLoading("");$("#ratig-layer").html(c)})}
function dleRate(a,b){ShowLoading("");$.get(dle_root+"engine/ajax/rating.php",{go_rate:a,news_id:b,skin:dle_skin,mode:"short"},function(c){HideLoading("");$("#ratig-layer-"+b).html(c)})}
function doAddComments(){var a=document.getElementById("dle-comments-form");if(dle_wysiwyg=="yes"){document.getElementById("comments").value=$("#comments").html();var b="wysiwyg"}else b="";if(a.comments.value==""||a.name.value==""){DLEalert(dle_req_field,dle_info);return false}var c=a.sec_code?a.sec_code.value:"";if(a.recaptcha_response_field)var d=Recaptcha.get_response(),e=Recaptcha.get_challenge();else e=d="";var f=a.allow_subscribe?a.allow_subscribe.checked==true?"1":"0":"0";ShowLoading("");$.post(dle_root+
"engine/ajax/addcomments.php",{post_id:a.post_id.value,comments:a.comments.value,name:a.name.value,mail:a.mail.value,editor_mode:b,skin:dle_skin,sec_code:c,recaptcha_response_field:d,recaptcha_challenge_field:e,allow_subscribe:f},function(g){if(a.sec_code){a.sec_code.value="";reload()}HideLoading("");RunAjaxJS("dle-ajax-comments",g);if(g!="error"&&document.getElementById("blind-animation")){$("html"+(!$.browser.opera?",body":"")).animate({scrollTop:$("#dle-ajax-comments").position().top-70},1100);
setTimeout(function(){$("#blind-animation").show("blind",{},1500)},1100)}})}function dle_copy_quote(a){dle_txt="";if(window.getSelection)dle_txt=window.getSelection();else if(document.selection)dle_txt=document.selection.createRange().text;if(dle_txt!="")dle_txt="[quote="+a+"]"+dle_txt+"[/quote]\n"}
function dle_ins(a){if(!document.getElementById("dle-comments-form"))return false;var b=document.getElementById("dle-comments-form").comments,c="";if(dle_wysiwyg=="no")b.value+=dle_txt!=""?dle_txt:"[b]"+a+"[/b],\n";else{c=dle_txt!=""?dle_txt:"<b>"+a+"</b>,<br />";tinyMCE.execInstanceCommand("comments","mceInsertContent",false,c,true)}}
function ShowOrHide(a){var b=$("#"+a);a=document.getElementById("image-"+a)?document.getElementById("image-"+a):null;if(b.css("display")=="none"){b.show("blind",{},1E3);if(a)a.src=dle_root+"templates/"+dle_skin+"/dleimages/spoiler-minus.gif"}else{b.hide("blind",{},1E3);if(a)a.src=dle_root+"templates/"+dle_skin+"/dleimages/spoiler-plus.gif"}}
function ckeck_uncheck_all(){for(var a=document.pmlist,b=0;b<a.elements.length;b++){var c=a.elements[b];if(c.type=="checkbox")c.checked=a.master_box.checked==true?false:true}a.master_box.checked=a.master_box.checked==true?false:true}function confirmDelete(a){DLEconfirm(dle_del_agree,dle_confirm,function(){document.location=a})}function setNewField(a,b){if(a!=selField){fombj=b;selField=a}}
function dle_news_delete(a){var b={};b[dle_act_lang[1]]=function(){$(this).dialog("close")};if(allow_dle_delete_news)b[dle_del_msg]=function(){$(this).dialog("close");var c={};c[dle_act_lang[3]]=function(){$(this).dialog("close")};c[dle_p_send]=function(){if($("#dle-promt-text").val().length<1)$("#dle-promt-text").addClass("ui-state-error");else{var d=$("#dle-promt-text").val();$(this).dialog("close");$("#dlepopup").remove();$.post(dle_root+"engine/ajax/message.php",{id:a,text:d},function(e){if(e==
"ok")document.location=dle_root+"index.php?do=deletenews&id="+a+"&hash="+dle_login_hash;else DLEalert("Send Error",dle_info)})}};$("#dlepopup").remove();$("body").append("<div id='dlepopup' title='"+dle_notice+"' style='display:none'><br />"+dle_p_text+"<br /><br /><textarea name='dle-promt-text' id='dle-promt-text' class='ui-widget-content ui-corner-all' style='width:97%;height:100px; padding: .4em;'></textarea></div>");$("#dlepopup").dialog({autoOpen:true,width:500,buttons:c})};b[dle_act_lang[0]]=
function(){$(this).dialog("close");document.location=dle_root+"index.php?do=deletenews&id="+a+"&hash="+dle_login_hash};$("#dlepopup").remove();$("body").append("<div id='dlepopup' title='"+dle_confirm+"' style='display:none'><br /><div id='dlepopupmessage'>"+dle_del_agree+"</div></div>");$("#dlepopup").dialog({autoOpen:true,width:500,buttons:b})}
function MenuNewsBuild(a,b){var c=[];c[0]="<a onclick=\"ajax_prep_for_edit('"+a+"', '"+b+'\'); return false;" href="#">'+menu_short+"</a>";if(dle_admin!="")c[1]='<a href="'+dle_root+dle_admin+"?mod=editnews&action=editnews&id="+a+'" target="_blank">'+menu_full+"</a>";if(allow_dle_delete_news){c[2]="<a onclick=\"sendNotice ('"+a+'\'); return false;" href="#">'+dle_notice+"</a>";c[3]="<a onclick=\"dle_news_delete ('"+a+'\'); return false;" href="#">'+dle_del_news+"</a>"}return c}
function sendNotice(a){var b={};b[dle_act_lang[3]]=function(){$(this).dialog("close")};b[dle_p_send]=function(){if($("#dle-promt-text").val().length<1)$("#dle-promt-text").addClass("ui-state-error");else{var c=$("#dle-promt-text").val();$(this).dialog("close");$("#dlepopup").remove();$.post(dle_root+"engine/ajax/message.php",{id:a,text:c,allowdelete:"no"},function(d){d=="ok"&&DLEalert(dle_p_send_ok,dle_info)})}};$("#dlepopup").remove();$("body").append("<div id='dlepopup' title='"+dle_notice+"' style='display:none'><br />"+
dle_p_text+"<br /><br /><textarea name='dle-promt-text' id='dle-promt-text' class='ui-widget-content ui-corner-all' style='width:97%;height:100px; padding: .4em;'></textarea></div>");$("#dlepopup").dialog({autoOpen:true,width:500,buttons:b})}
function DLEalert(a,b){$("#dlepopup").remove();$("body").append("<div id='dlepopup' title='"+b+"' style='display:none'><br />"+a+"</div>");$("#dlepopup").dialog({autoOpen:true,width:470,buttons:{Ok:function(){$(this).dialog("close");$("#dlepopup").remove()}}})}
function DLEconfirm(a,b,c){var d={};d[dle_act_lang[1]]=function(){$(this).dialog("close");$("#dlepopup").remove()};d[dle_act_lang[0]]=function(){$(this).dialog("close");$("#dlepopup").remove();c&&c()};$("#dlepopup").remove();$("body").append("<div id='dlepopup' title='"+b+"' style='display:none'><br />"+a+"</div>");$("#dlepopup").dialog({autoOpen:true,width:470,buttons:d})}
function DLEprompt(a,b,c,d,e){var f={};f[dle_act_lang[3]]=function(){$(this).dialog("close")};f[dle_act_lang[2]]=function(){if(!e&&$("#dle-promt-text").val().length<1)$("#dle-promt-text").addClass("ui-state-error");else{var g=$("#dle-promt-text").val();$(this).dialog("close");$("#dlepopup").remove();d&&d(g)}};$("#dlepopup").remove();$("body").append("<div id='dlepopup' title='"+c+"' style='display:none'><br />"+a+"<br /><br /><input type='text' name='dle-promt-text' id='dle-promt-text' class='ui-widget-content ui-corner-all' style='width:97%; padding: .4em;' value='"+
b+"'/></div>");$("#dlepopup").dialog({autoOpen:true,width:470,show:"blind",hide:"blind",buttons:f})}var dle_user_profile="",dle_user_profile_link="";
function ShowPopupProfile(a){var b={};b[menu_profile]=function(){document.location=dle_user_profile_link};if(dle_group!=5)b[menu_send]=function(){document.location=dle_root+"index.php?do=pm&doaction=newpm&username="+dle_user_profile};if(dle_group==1)b[menu_uedit]=function(){$(this).dialog("close");window.open(""+dle_root+dle_admin+"?mod=editusers&action=edituser&user="+dle_user_profile+"","User","toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=540,height=500")};
$("#dleprofilepopup").remove();$("body").append(a);$("#dleprofilepopup").dialog({autoOpen:true,show:"fade",hide:"fade",buttons:b,width:450});return false}function ShowProfile(a,b){if(dle_user_profile==a&&document.getElementById("dleprofilepopup")){$("#dleprofilepopup").dialog("open");return false}dle_user_profile=a;dle_user_profile_link=b;ShowLoading("");$.get(dle_root+"engine/ajax/profile.php",{name:a,skin:dle_skin},function(c){HideLoading("");ShowPopupProfile(c)});return false}
function FastSearch(){$("#story").attr("autocomplete","off");$("#story").blur(function(){$("#searchsuggestions").fadeOut()});$("#story").keyup(function(){var a=$(this).val();if(a.length==0)$("#searchsuggestions").fadeOut();else if(dle_search_value!=a&&a.length>3){clearInterval(dle_search_delay);dle_search_delay=setInterval(function(){dle_do_search(a)},600)}})}
function dle_do_search(a){clearInterval(dle_search_delay);$("#searchsuggestions").remove();$("body").append("<div id='searchsuggestions' style='display:none'></div>");$.post(dle_root+"engine/ajax/search.php",{query:""+a+""},function(b){$("#searchsuggestions").html(b).fadeIn().css({position:"absolute",top:0,left:0}).position({my:"left top",at:"left bottom",of:"#story",collision:"fit flip"})});dle_search_value=a}
function ShowLoading(a){a&&$("#loading-layer-text").html(a);a=($(window).width()-$("#loading-layer").width())/2;var b=($(window).height()-$("#loading-layer").height())/2;$("#loading-layer").css({left:a+"px",top:b+"px",position:"fixed",zIndex:"99"});$("#loading-layer").fadeTo("slow",0.6)}function HideLoading(){$("#loading-layer").fadeOut("slow")}
function ShowAllVotes(){if(document.getElementById("dlevotespopup")){$("#dlevotespopup").dialog("open");return false}$.ajaxSetup({cache:false});ShowLoading("");$.get(dle_root+"engine/ajax/allvotes.php?dle_skin="+dle_skin,function(a){HideLoading("");$("#dlevotespopup").remove();$("body").append(a);$(".dlevotebutton").button();$("#dlevotespopup").dialog({autoOpen:true,show:"fade",hide:"fade",width:600,height:150});$("#dlevotespopupcontent").height()>400&&$("#dlevotespopupcontent").height(400);$("#dlevotespopup").dialog("option",
"height",$("#dlevotespopupcontent").height()+40);$("#dlevotespopup").dialog("option","position","center")});return false}function fast_vote(a){var b=$("#vote_"+a+" input:radio[name=vote_check]:checked").val();ShowLoading("");$.get(dle_root+"engine/ajax/vote.php",{vote_id:a,vote_action:"vote",vote_mode:"fast_vote",vote_check:b,vote_skin:dle_skin},function(c){HideLoading("");$("#dle-vote_list-"+a).fadeOut(500,function(){$(this).html(c);$(this).fadeIn(500)})});return false}
function dropdownmenu(a,b,c,d){if(window.event)event.cancelBubble=true;else b.stopPropagation&&b.stopPropagation();b=$("#dropmenudiv");if(b.is(":visible")){clearhidemenu();b.fadeOut("fast");return false}b.remove();$("body").append('<div id="dropmenudiv" style="display:none;position:absolute;z-index:100;width:165px;"></div>');b=$("#dropmenudiv");b.html(c.join(""));d&&b.width(d);c=$(document).width()-15;d=$(a).offset();if(c-d.left<b.width())d.left-=b.width()-a.offsetWidth;b.css({left:d.left+"px",top:d.top+
a.offsetHeight+"px"});b.fadeTo("fast",0.9);b.mouseenter(function(){clearhidemenu()}).mouseleave(function(){delayhidemenu()});$(document).one("click",function(){hidemenu()});return false}function hidemenu(){$("#dropmenudiv").fadeOut("fast")}function delayhidemenu(){delayhide=setTimeout("hidemenu()",1E3)}function clearhidemenu(){typeof delayhide!="undefined"&&clearTimeout(delayhide)};