<?php
//initialize the session
session_start();
if (!(isset($_SESSION['Sede']))) {
	$_SESSION['Sede'] = $_GET['Sede'];
}
$Sede = $_SESSION['Sede'];
if (date("H") >= 10 && date("H") <= 15) {
	$Pasto = 1;
	$_SESSION['Pasto'] = $Pasto;
} else {
	if (date("H") >= 5 && date("H")< 10) {
		$Pasto = 3;
		$_SESSION['Pasto'] = $Pasto;
	} else {
		$Pasto = 2;
		$_SESSION['Pasto'] = $Pasto;
	}	
};
$Giorno = date("Y-m-d");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<title></title>
<script LANGUAGE="JavaScript" type="text/javascript">
winext=window.open("PostMensaFull.php", "miaweb", "status=no, scrollbars=no");	
if (window.screen) {
    winext.window.moveTo(0,0);
    winext.window.resizeTo(screen.availWidth,screen.availHeight);
    winext.focus();
    }
</script>
<style type="text/css">

body,td,th {
	color: #FFFFFF;
}
body {
	background-color: #FFFFFF;
}
.Stile1 {font-size: x-small}
.Stile6 {
	color: #FFFFFF;
	font-weight: bold;
}
.Stile13 {
	font-size: medium;
	color: #FF6600;
	font-weight: bold;
}
.Stile20 {color: #00CC33; font-weight: bold; font-size: 36px; }
.Stile21 {color: #FF0000; font-weight: bold; font-size: 36px; }
.Stile22 {color: #FFFF00; font-weight: bold; font-size: 36px; }

</style></head>

<body>
<p align="center">&nbsp;</p>
</body>
</html>

