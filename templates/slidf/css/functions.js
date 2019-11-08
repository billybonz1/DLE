function I$(id) {return document.getElementById(id);}

function setCookie(name, value, expires, path) {
      document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "");
}

function getCookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=");
  if (c_start!=-1)
    {
    c_start=c_start + c_name.length+1;
    c_end=document.cookie.indexOf(";",c_start);
    if (c_end==-1) c_end=document.cookie.length;
    return unescape(document.cookie.substring(c_start,c_end));
    }
  }
return "";
}

img_width = 30;

function slide_left()
{
var s = jQuery("div.roller a:first-child").attr("num");

if(s != 1) {
cImg=jQuery("div.roller a:last-child").clone();
jQuery("div.roller a:last-child").remove();
jQuery("div.roller a:first-child").before(cImg);
var lpos=$(".roller").css("left");
var slleft=parseInt(lpos)-img_width;
/*$('.roller').animate({left:slleft+'px'}, 300);*/
var slleft=slleft+img_width;
$('.roller').animate({left:slleft+'px'}, 300);
if (num==0) {prnum=num; num=pics_co-1;} else {prnum=num; num=num-1;}
if (num==0) {nxnum=pics_co-1;} else {nxnum=num-1;}
$("#descr"+(prnum)).css("display","none");
$("#descr"+(num)).css("display","block");
$("#txt"+(prnum)).css("display","none");
$("#txt"+(num)).css("display","block");

$("#thumbnail_"+(prnum)).css("opacity","1");
$("#thumbnail_"+(nxnum)).css("opacity","1");
$("#thumbnail_"+(num)).animate({opacity:0.5},500);
}
}

function slide_right()
{
var s = jQuery("div.roller a:first-child").attr("num");
if(s != 10) {
cImg=jQuery("div.roller a:first-child").clone();
jQuery("div.roller a:first-child").remove();
jQuery("div.roller a:last-child").after(cImg);
var rpos=$(".roller").css("left");
var slright=parseInt(rpos)+img_width;
/*$('.roller').animate({left:slright+'px'}, 300);*/
var slright=slright-img_width;
$('.roller').animate({left:slright+'px'}, 300);
if (num==pics_co-1) {prnum=num; num=0;} else {prnum=num; num=num+1;}
if (num==pics_co-1) {nxnum=0;} else {nxnum=num+1;}
$("#descr"+(prnum)).css("display","none");
$("#descr"+(num)).css("display","block");
$("#txt"+(prnum)).css("display","none");
$("#txt"+(num)).css("display","block");

$("#thumbnail_"+(prnum)).css("opacity","1");
$("#thumbnail_"+(nxnum)).css("opacity","1");
$("#thumbnail_"+(num)).animate({opacity:0.5},500);
}
}


function slide_left2()
{
var s = jQuery("div.roller2 a:first-child").attr("num");

if(s != 1) {
cImg=jQuery("div.roller2 a:last-child").clone();
jQuery("div.roller2 a:last-child").remove();
jQuery("div.roller2 a:first-child").before(cImg);
var lpos=$(".roller2").css("left");
var slleft=parseInt(lpos)-img_width;
/*$('.roller').animate({left:slleft+'px'}, 300);*/
var slleft=slleft+img_width;
$('.roller2').animate({left:slleft+'px'}, 300);
if (num==0) {prnum=num; num=pics_co-1;} else {prnum=num; num=num-1;}
if (num==0) {nxnum=pics_co-1;} else {nxnum=num-1;}
$("#descr"+(prnum)).css("display","none");
$("#descr"+(num)).css("display","block");
$("#txt"+(prnum)).css("display","none");
$("#txt"+(num)).css("display","block");

$("#thumbnail_"+(prnum)).css("opacity","1");
$("#thumbnail_"+(nxnum)).css("opacity","1");
$("#thumbnail_"+(num)).animate({opacity:0.5},500);
}
}



function slide_right2()
{
var s = jQuery("div.roller2 a:first-child").attr("num");
if(s != 4) {
cImg=jQuery("div.roller2 a:first-child").clone();
jQuery("div.roller2 a:first-child").remove();
jQuery("div.roller2 a:last-child").after(cImg);
var rpos=$(".roller2").css("left");
var slright=parseInt(rpos)+img_width;
/*$('.roller').animate({left:slright+'px'}, 300);*/
var slright=slright-img_width;
$('.roller2').animate({left:slright+'px'}, 300);
if (num==pics_co-1) {prnum=num; num=0;} else {prnum=num; num=num+1;}
if (num==pics_co-1) {nxnum=0;} else {nxnum=num+1;}
$("#descr"+(prnum)).css("display","none");
$("#descr"+(num)).css("display","block");
$("#txt"+(prnum)).css("display","none");
$("#txt"+(num)).css("display","block");

$("#thumbnail_"+(prnum)).css("opacity","1");
$("#thumbnail_"+(nxnum)).css("opacity","1");
$("#thumbnail_"+(num)).animate({opacity:0.5},500);
}
}


function slide_left3()
{
var s = jQuery("div.roller3 a:first-child").attr("num");

if(s != 1) {
cImg=jQuery("div.roller3 a:last-child").clone();
jQuery("div.roller3 a:last-child").remove();
jQuery("div.roller3 a:first-child").before(cImg);
var lpos=$(".roller3").css("left");
var slleft=parseInt(lpos)-img_width;
/*$('.roller').animate({left:slleft+'px'}, 300);*/
var slleft=slleft+img_width;
$('.roller3').animate({left:slleft+'px'}, 300);
if (num==0) {prnum=num; num=pics_co-1;} else {prnum=num; num=num-1;}
if (num==0) {nxnum=pics_co-1;} else {nxnum=num-1;}
$("#descr"+(prnum)).css("display","none");
$("#descr"+(num)).css("display","block");
$("#txt"+(prnum)).css("display","none");
$("#txt"+(num)).css("display","block");

$("#thumbnail_"+(prnum)).css("opacity","1");
$("#thumbnail_"+(nxnum)).css("opacity","1");
$("#thumbnail_"+(num)).animate({opacity:0.5},500);
}
}



function slide_right3()
{
var s = jQuery("div.roller3 a:first-child").attr("num");
if(s != 11) {
cImg=jQuery("div.roller3 a:first-child").clone();
jQuery("div.roller3 a:first-child").remove();
jQuery("div.roller3 a:last-child").after(cImg);
var rpos=$(".roller3").css("left");
var slright=parseInt(rpos)+img_width;
/*$('.roller').animate({left:slright+'px'}, 300);*/
var slright=slright-img_width;
$('.roller3').animate({left:slright+'px'}, 300);
if (num==pics_co-1) {prnum=num; num=0;} else {prnum=num; num=num+1;}
if (num==pics_co-1) {nxnum=0;} else {nxnum=num+1;}
$("#descr"+(prnum)).css("display","none");
$("#descr"+(num)).css("display","block");
$("#txt"+(prnum)).css("display","none");
$("#txt"+(num)).css("display","block");

$("#thumbnail_"+(prnum)).css("opacity","1");
$("#thumbnail_"+(nxnum)).css("opacity","1");
$("#thumbnail_"+(num)).animate({opacity:0.5},500);
}
}


function changeBtn(e){
	$(".topArrow"+e).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/click_top.png)");
}

function changeBtn2(e){
	$(".topArrow"+e).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/hover_top.png)");
}

function changeBtn3(e){
	$(".topArrow"+e).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/passive_top.png)");
}

function changeBtn4(e){
	$(".topArrow"+e).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/hover_top.png)");
}


function tchangeBtn(e){
	$(".bottomArrow"+e).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/click_bottom.png)");
}

function tchangeBtn2(e){
	$(".bottomArrow"+e).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/hover_bottom.png)");
}

function tchangeBtn3(e){
	$(".bottomArrow"+e).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/passive_bottom.png)");
}

function tchangeBtn4(e){
	$(".bottomArrow"+e).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/hover_bottom.png)");
}
