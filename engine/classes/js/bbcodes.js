var uagent=navigator.userAgent.toLowerCase(),is_safari=uagent.indexOf("safari")!=-1||navigator.vendor=="Apple Computer, Inc.",is_ie=uagent.indexOf("msie")!=-1&&!is_opera&&!is_safari&&!is_webtv,is_ie4=is_ie&&uagent.indexOf("msie 4.")!=-1,is_moz=navigator.product=="Gecko",is_ns=uagent.indexOf("compatible")==-1&&uagent.indexOf("mozilla")!=-1&&!is_opera&&!is_webtv&&!is_safari,is_ns4=is_ns&&parseInt(navigator.appVersion)==4,is_opera=uagent.indexOf("opera")!=-1,is_kon=uagent.indexOf("konqueror")!=-1,is_webtv=
uagent.indexOf("webtv")!=-1,is_win=uagent.indexOf("win")!=-1||uagent.indexOf("16bit")!=-1,is_mac=uagent.indexOf("mac")!=-1||navigator.vendor=="Apple Computer, Inc.",ua_vers=parseInt(navigator.appVersion),b_open=0,i_open=0,u_open=0,s_open=0,quote_open=0,code_open=0,sql_open=0,html_open=0,left_open=0,center_open=0,right_open=0,hide_open=0,color_open=0,spoiler_open=0,ie_range_cache="",list_open_tag="",list_close_tag="",listitems="",bbtags=[],rus_lr2="\u0415-\u0435-\u041e-\u043e-\u0401-\u0401-\u0401-\u0401-\u0416-\u0416-\u0427-\u0427-\u0428-\u0428-\u0429-\u0429-\u042a-\u042c-\u042d-\u042d-\u042e-\u042e-\u042f-\u042f-\u042f-\u042f-\u0451-\u0451-\u0436-\u0447-\u0448-\u0449-\u044d-\u044e-\u044f-\u044f".split("-"),
lat_lr2=("/E-/e-/O-/o-\u042bO-\u042bo-\u0419O-\u0419o-\u0417H-\u0417h-\u0426H-\u0426h-\u0421H-\u0421h-\u0428H-\u0428h-\u044a"+String.fromCharCode(35)+"-\u044c"+String.fromCharCode(39)+"-\u0419E-\u0419e-\u0419U-\u0419u-\u0419A-\u0419a-\u042bA-\u042ba-\u044bo-\u0439o-\u0437h-\u0446h-\u0441h-\u0448h-\u0439e-\u0439u-\u0439a-\u044ba").split("-"),rus_lr1="\u0410-\u0411-\u0412-\u0413-\u0414-\u0415-\u0417-\u0418-\u0419-\u041a-\u041b-\u041c-\u041d-\u041e-\u041f-\u0420-\u0421-\u0422-\u0423-\u0424-\u0425-\u0425-\u0426-\u0429-\u042b-\u042f-\u0430-\u0431-\u0432-\u0433-\u0434-\u0435-\u0437-\u0438-\u0439-\u043a-\u043b-\u043c-\u043d-\u043e-\u043f-\u0440-\u0441-\u0442-\u0443-\u0444-\u0445-\u0445-\u0446-\u0449-\u044a-\u044b-\u044c-\u044c-\u044f".split("-"),
lat_lr1=("A-B-V-G-D-E-Z-I-J-K-L-M-N-O-P-R-S-T-U-F-H-X-C-W-Y-Q-a-b-v-g-d-e-z-i-j-k-l-m-n-o-p-r-s-t-u-f-h-x-c-w-"+String.fromCharCode(35)+"-y-"+String.fromCharCode(39)+"-"+String.fromCharCode(96)+"-q").split("-");function stacksize(a){for(i=0;i<a.length;i++)if(a[i]==""||a[i]==null||a=="undefined")return i;return a.length}function pushstack(a,b){arraysize=stacksize(a);a[arraysize]=b}function popstack(a){arraysize=stacksize(a);theval=a[arraysize-1];delete a[arraysize-1];return theval}
function setFieldName(a){if(a!=selField){allcleartags();selField=a}}function cstat(){stacksize(bbtags)}function closeall(){if(bbtags[0])for(;bbtags[0];){tagRemove=popstack(bbtags);eval("fombj."+selField+".value += closetags");if(tagRemove!="font"&&tagRemove!="size"){eval(tagRemove+"_open = 0");document.getElementById("b_"+tagRemove).className="editor_button"}}bbtags=[]}
function allcleartags(){if(bbtags[0])for(;bbtags[0];){tagRemove=popstack(bbtags);eval(tagRemove+"_open = 0");document.getElementById("b_"+tagRemove).className="editor_button"}bbtags=[]}function emoticon(a){doInsert(" "+a+" ","",false)}function pagebreak(){doInsert("{PAGEBREAK}","",false)}function add_code(a){fombj.selField.value+=a;fombj.selField.focus()}
function simpletag(a){if(eval(a+"_open")==0){if(doInsert("["+a+"]","[/"+a+"]",true)){eval(a+"_open = 1");document.getElementById("b_"+a).className="editor_buttoncl";pushstack(bbtags,a);cstat()}}else{for(i=lastindex=0;i<bbtags.length;i++)if(bbtags[i]==a)lastindex=i;for(;bbtags[lastindex];){tagRemove=popstack(bbtags);doInsert("[/"+tagRemove+"]","",false);if(tagRemove!="font"&&tagRemove!="size"){eval(tagRemove+"_open = 0");document.getElementById("b_"+tagRemove).className="editor_button"}}cstat()}}
function tag_url(){var a=get_sel(eval("fombj."+selField));a||(a="My Webpage");DLEprompt(text_enter_url,"http://",dle_prompt,function(b){DLEprompt(text_enter_url_name,a,dle_prompt,function(d){doInsert("[url="+b+"]"+d+"[/url]","",false);ie_range_cache=null})})}
function tag_leech(){var a=get_sel(eval("fombj."+selField));a||(a="My Webpage");DLEprompt(text_enter_url,"http://",dle_prompt,function(b){DLEprompt(text_enter_url_name,a,dle_prompt,function(d){doInsert("[leech="+b+"]"+d+"[/leech]","",false);ie_range_cache=null})})}function tag_youtube(){var a=get_sel(eval("fombj."+selField));a||(a="http://");DLEprompt(text_enter_url,a,dle_prompt,function(b){doInsert("[youtube="+b+"]","",false);ie_range_cache=null})}
function tag_flash(){var a=get_sel(eval("fombj."+selField));a||(a="http://");DLEprompt(text_enter_flash,a,dle_prompt,function(b){DLEprompt(text_enter_size,"425,264",dle_prompt,function(d){doInsert("[flash="+d+"]"+b+"[/flash]","",false);ie_range_cache=null})})}function tag_list(a){list_open_tag=a=="ol"?"[ol=1]\n":"[list]\n";list_close_tag=a=="ol"?"[/ol]":"[/list]";listitems="";(a=get_sel(eval("fombj."+selField)))||(a="");insert_list(a)}
function insert_list(a){DLEprompt(text_enter_list,a,dle_prompt,function(b){if(b!=""){listitems+="[*]"+b+"\n";insert_list("")}else if(listitems){doInsert(list_open_tag+listitems+list_close_tag,"",false);ie_range_cache=null}},true)}
function tag_image(){var a=get_sel(eval("fombj."+selField));a||(a="http://");DLEprompt(text_enter_image,a,dle_prompt,function(b){DLEprompt(img_title,image_align,dle_prompt,function(d){if(d=="")doInsert("[img]"+b+"[/img]","",false);else d=="center"?doInsert("[center][img]"+b+"[/img][/center]","",false):doInsert("[img="+d+"]"+b+"[/img]","",false);ie_range_cache=null})})}
function tag_video(){var a=get_sel(eval("fombj."+selField));a||(a="http://");DLEprompt(text_enter_url,a,dle_prompt,function(b){doInsert("[video="+b+"]","",false);ie_range_cache=null})}function tag_audio(){var a=get_sel(eval("fombj."+selField));a||(a="http://");DLEprompt(text_enter_url,a,dle_prompt,function(b){doInsert("[audio="+b+"]","",false);ie_range_cache=null})}
function tag_email(){var a=get_sel(eval("fombj."+selField));a||(a="");DLEprompt(text_enter_email,"",dle_prompt,function(b){DLEprompt(email_title,a,dle_prompt,function(d){doInsert("[email="+b+"]"+d+"[/email]","",false);ie_range_cache=null})})}
function doInsert(a,b,d){var c=eval("fombj."+selField);if(ua_vers>=4&&is_ie&&is_win){if(c.isTextEdit){c.focus();var f=document.selection,e=ie_range_cache?ie_range_cache:f.createRange();if((f.type=="Text"||f.type=="None")&&e!=null){if(b!=""&&e.text.length>0)a+=e.text+b;else if(d)a+=e.text+b;e.text=a}}else c.value+=a+b;e.select();ie_range_cache=null}else if(c.selectionEnd){e=c.selectionStart;f=c.scrollTop;var h=c.selectionEnd,j=c.value.substring(0,e),g=c.value.substring(e,h);h=c.value.substring(h,c.textLength);
d||(g="");g=a+g+b;c.value=j+g+h;a=e+g.length;c.selectionStart=a;c.selectionEnd=a;c.scrollTop=f}else c.value+=a+b;c.focus();return false}
function ins_color(){if(color_open==0){document.getElementById(selField).focus();if(is_ie){document.getElementById(selField).focus();ie_range_cache=document.selection.createRange()}$("#cp").remove();$("body").append("<div id='cp' title='"+bb_t_col+'\' style=\'display:none\'><br /><iframe width="154" height="104" src="'+dle_root+"templates/"+dle_skin+'/bbcodes/color.html" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe></div>');$("#cp").dialog({autoOpen:true,width:180})}else{for(i=
lastindex=0;i<bbtags.length;i++)if(bbtags[i]=="color")lastindex=i;for(;bbtags[lastindex];){tagRemove=popstack(bbtags);doInsert("[/"+tagRemove+"]","",false);eval(tagRemove+"_open = 0");document.getElementById("b_"+tagRemove).className="editor_button"}}}function setColor(a){if(doInsert("[color="+a+"]","[/color]",true)){color_open=1;document.getElementById("b_color").className="editor_buttoncl";pushstack(bbtags,"color")}$("#cp").dialog("close");cstat()}
function ins_emo(){document.getElementById(selField).focus();if(is_ie){document.getElementById(selField).focus();ie_range_cache=document.selection.createRange()}$("#dle_emo").remove();$("body").append("<div id='dle_emo' title='"+bb_t_emo+"' style='display:none'>"+document.getElementById("dle_emos").innerHTML+"</div>");var a="300",b="auto";if($("#dle_emos").width()>=450){$("#dle_emos").width(450);a="505"}if($("#dle_emos").height()>=300){$("#dle_emos").height(300);b="340"}$("#dle_emo").dialog({autoOpen:true,
width:a,height:b})}function dle_smiley(a){doInsert(" "+a+" ","",false);$("#dle_emo").dialog("close");ie_range_cache=null}function pagelink(){var a=get_sel(eval("fombj."+selField));a||(a=text_pages);DLEprompt(text_enter_page,"1",dle_prompt,function(b){DLEprompt(text_enter_page_name,a,dle_prompt,function(d){doInsert("[page="+b+"]"+d+"[/page]","",false);ie_range_cache=null})})}
function translit(){var a=eval("fombj."+selField);if(ua_vers>=4&&is_ie&&is_win)if(a.isTextEdit){a.focus();var b=document.selection,d=b.createRange();if((b.type=="Text"||b.type=="None")&&d!=null)d.text=dotranslate(d.text)}else a.value=dotranslate(a.value);else a.value=dotranslate(a.value);a.focus()}
function dotranslate(a){var b="",d=0;d="";var c=1;for(kk=0;kk<a.length;kk++){d=a.substr(kk,1);if(d=="["||d=="<")c=0;if(d=="]"||d==">")c=1;d=c?transsymbtocyr(b.substr(b.length-1,1),d):b.substr(b.length-1,1)+d;b=b.substr(0,b.length-1)+d}return b}function transsymbtocyr(a,b){var d=a+b,c=b.charCodeAt(0);if(!(c>=65&&c<=123||c==35||c==39))return d;for(c=0;c<lat_lr2.length;c++)if(lat_lr2[c]==d)return rus_lr2[c];for(c=0;c<lat_lr1.length;c++)if(lat_lr1[c]==b)return a+rus_lr1[c];return d}
function insert_font(a,b){if(a!=0){doInsert("["+b+"="+a+"]","[/"+b+"]",true)&&pushstack(bbtags,b);fombj.bbfont.selectedIndex=0;fombj.bbsize.selectedIndex=0}}
function get_sel(a){if(document.selection){if(is_ie){document.getElementById(selField).focus();ie_range_cache=document.selection.createRange()}a=document.selection.createRange();if(a.text)return a.text}else if(typeof a.selectionStart=="number")if(a.selectionStart!=a.selectionEnd){var b=a.selectionStart;return a.value.substr(b,a.selectionEnd-b)}return false};