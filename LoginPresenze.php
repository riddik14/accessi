<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Setup = "SELECT pre_setup.LoginMode FROM pre_setup";
$Setup = mysql_query($query_Setup, $MyPresenze) or die(mysql_error());
$row_Setup = mysql_fetch_assoc($Setup);
$totalRows_Setup = mysql_num_rows($Setup);
 session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<link href="Pulsante.css" rel="stylesheet" type="text/css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Login presenze</title>
<script language="javascript">
function OpenWindow(){
var v = window.open("LoginPresenze.php","","location=no,menubar=0,status=yes,toolbar=no");
}
function Enlarge(){
window.resizeTo(screen.availWidth,screen.availHeight);
}
</script>

<style type="text/css">
<!--
body {
	background-color: #0066CC;
}
body,td,th {
	color: #CCCCCC;
}
.Stile5 {color: #333333}
-->
</style>
<script type="text/javascript">
window.onload=function()
	{document.getElementById("barcode").focus()}
</script>
</head>
<META http-equiv="refresh" content="50;URL=LoginPresenze.php">
<body background="images/Sfondo_vista.jpg" style="background-repeat:no-repeat;background-position:top center;" onLoad="form1.CardCode.focus()">
<p align="center"><img src="images/BannerCealpi.jpg" width="600" height="73"><a href="<?php echo $logoutAction ?>"><img src="images/xbox360power.jpg" width="70" height="70"></a></p>
<h3 align="center" class="Stile5">Sistema di rilevazione automatizzata degli accessi e prenotazione dei pasti </h3>
<table align="center">
  <tr valign="top">
  <?php if ($row_Setup['LoginMode'] == 1 ) { ?>
    <td height="333"><p align="center" class="Stile5"><strong>Eseguire lettura  codice a barre </strong></p>
      <form name="form1" action="ValidateLoginPresenze.php" method="POST" style="position:absolute; top:-100px;">
        <div align="center">
          <input name="CardCode" type="text"  size="0" id="barcode" style="opacity:0; filter:alpha(opacity=0);" ">
        </div>
      </form>     <img src="images/barcodescanning.gif" width="355" height="265"> 
	</td>
	<?php }; ?>
  </tr>
</table>
<p align="center" class="Stile5"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="156" height="45">
    <param name="movie" value="Flash/Digitale_Uhr_virtualsystem_de.swf">
    <param name="quality" value="high">
    <embed src="Flash/Digitale_Uhr_virtualsystem_de.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="156" height="45"></embed>
  </object>
  <script language="javascript"> 
function noRightClick() { 
if (event.button==2) { 
alert('Tasto destro del mouse disabiltato.') 
} 
}
document.onmousedown=noRightClick 
  </script>
</p>
<script language="javascript">
function disabilita_pranzo() {
document.form2.Submit2.disabled=true;
}
</script>
</body>
</html>
<?php
mysql_free_result($Setup);
?>
