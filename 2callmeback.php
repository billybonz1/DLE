<? // WR-CalMeBack v 1.0  //  29.08.05�.  //  Miha-ingener@yandex.ru

#error_reporting  (E_ALL);


// --------------------------- ���������������� -------------------------- //

$adminemail="infokraska@gmail.com";  // ����� ������ - ���� ��������
$date=date("d.m.Y"); // �����.�����.���
$time=date("H:i:s"); // ����:������:�������
$backurl="https://laki-kraski.com.ua/";  // �� ����� ��������� ��������� ����� �������� ������
// ---------------------------------------------------------------------- //



if (isset($_POST['name'])) {

// ��������� ������ �����:

// �����
$phone_number=$_POST['phone_number'];
if ($phone_number=="") {print"<center>��������� <a href='javascript:history.back(1)'><B>�����</B></a>. �� �� ������� ����� ."; exit;}


// ��� ����������� ���� 
$name=$_POST['name'];





// �������� ������ ���������
$headers=null; // ��������� ��� �������� �����
$headers.="Content-Type: text/html; charset=windows-1251\r\n";
$headers.="From: ������������� <".$adminemail.">\r\n";
$headers.="X-Mailer: PHP/".phpversion()."\r\n";

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$cmburl="http://$host$self";
$cmburl=str_replace("callmeback.php", "$backurl", $cmburl);

// �������� ��� ���������� � ���� ������
$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset='></head>
<body><BR><BR><center>
<table width=500><tr><td height='25' bgcolor='#000066' align=center>
<font color=white>������ ����� <B>����� ��� ���������!</B></font></td></tr></table><br>

<table border=0 cellpadding=0 cellspacing=0 width=500 bgcolor=navy><tr><td width=964>
<table border=0 cellpadding=3 cellspacing=1 width='100%'>

<tr><td width=114 bgcolor='#E6E6E6' height=24><font size=2>��� ����������� ����</font></td>
<td width=483 bgcolor='#F6F6F6'><font size=2>$name</font></td></tr>


<tr><td 'bgcolor=#E6E6E6' align=center><font size='-1'>����� ��������</font></td>
<td bgcolor=#F6F6F6><font size='-1'>

<table border=0 cellpadding=0 cellspacing=0><TR><TD>�����</TD>
<TR align=center><TD><B>$phone_number</B></TD></TABLE>

</font></td></tr>






</table></td></tr></table><br>

<table width=500><tr><td height=25 bgcolor='#000066' align='center'>
<a href='$cmburl'><font size='-1' color='white'>���������� �� ��������</font></a></td></tr></table>
<BR><BR><BR>
* ��� ��������� ������������� � ���������� ������� � ����� $cmburl. �������� �� ���� �������.
</body></html>";

// ���������� ������ ������� �� �������� ���� ��������� �������� ;-)
mail("$adminemail", "����� ��������� ������ �� \"$name\"", $allmsg, $headers);

print "<script language='Javascript'><!--
function reload() {location = \"$backurl\"}; setTimeout('reload()', 2000);
//--></script>
$allmsg <BR><BR>"; exit;

}
else {exit;}

?>