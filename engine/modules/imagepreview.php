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
 Файл: imagepreview.php
-----------------------------------------------------
 Назначение: Просмотр картинок
=====================================================
*/

$_GET['image'] = @htmlspecialchars ($_GET['image'], ENT_QUOTES);

$find = array ('/data:/i', '/about:/i', '/vbscript:/i', '/onclick/i', '/onload/i', '/onunload/i', '/onabort/i', '/onerror/i', '/onblur/i', '/onchange/i', '/onfocus/i', '/onreset/i', '/onsubmit/i', '/ondblclick/i', '/onkeydown/i', '/onkeypress/i', '/onkeyup/i', '/onmousedown/i', '/onmouseup/i', '/onmouseover/i', '/onmouseout/i', '/onselect/i', '/javascript/i' );
$replace = array ("d&#097;ta:", "&#097;bout:", "vbscript<b></b>:", "&#111;nclick", "&#111;nload", "&#111;nunload", "&#111;nabort", "&#111;nerror", "&#111;nblur", "&#111;nchange", "&#111;nfocus", "&#111;nreset", "&#111;nsubmit", "&#111;ndblclick", "&#111;nkeydown", "&#111;nkeypress", "&#111;nkeyup", "&#111;nmousedown", "&#111;nmouseup", "&#111;nmouseover", "&#111;nmouseout", "&#111;nselect", "j&#097;vascript" );

$_GET['image'] = preg_replace( $find, $replace, $_GET['image'] );

$_GET['image'] = str_replace( "document.cookie", "d&#111;cument.cookie", $_GET['image'] );


if ( preg_match( "/[?&;%<\[\]]/", $_GET['image']) ) {

	$_GET['image'] = "";

}

$preview = <<<HTML
<HTML>
<HEAD>
<TITLE>Image Preview</TITLE>
<script language='javascript'>
     var NS = (navigator.appName=="Netscape")?true:false;

            function fitPic() {
            iWidth = (NS)?window.innerWidth:document.body.clientWidth;
            iHeight = (NS)?window.innerHeight:document.body.clientHeight;
            iWidth = document.images[0].width - iWidth;
            iHeight = document.images[0].height - iHeight;
            window.resizeBy(iWidth, iHeight);
            self.focus();
            };
</script>
</HEAD>
<BODY bgcolor="#FFFFFF" onload='fitPic();' topmargin="0" marginheight="0" leftmargin="0" marginwidth="0">
<script language='javascript'>
document.write( "<img src='{$_GET['image']}' border=0>" );
</script>
</BODY>
</HTML>
HTML;

echo $preview;
?>