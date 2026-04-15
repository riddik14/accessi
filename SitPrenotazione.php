<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
session_start();

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = addslashes($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

if ((isset($_POST['IDpra'])) && ($_POST['IDpra'] != "")) {
  $deleteSQL = sprintf("DELETE FROM pre_accessi WHERE IDrecord=%s",
                       GetSQLValueString($_POST['IDpra'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($deleteSQL, $MyPresenze) or die(mysql_error());

  $deleteGoTo = "SitPrenotazione.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST['IDce'])) && ($_POST['IDce'] != "")) {
  $deleteSQL = sprintf("DELETE FROM pre_accessi WHERE IDrecord=%s",
                       GetSQLValueString($_POST['IDce'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($deleteSQL, $MyPresenze) or die(mysql_error());

  $deleteGoTo = "SitPrenotazione.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_POST['IDcol'])) && ($_POST['IDcol'] != "")) {
  $deleteSQL = sprintf("DELETE FROM pre_accessi WHERE IDrecord=%s",
                       GetSQLValueString($_POST['IDcol'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($deleteSQL, $MyPresenze) or die(mysql_error());
}

if (!isset($_SESSION['UserID'])) {
	header("Location: LoginMassiva.php");
}

$Tx_cert = $_SESSION['UserID'];
$giorno = $_POST['GiornoPren'];
$_SESSION['GioPre'] = $_POST['GiornoPren'];

$maxRows_Prenotazioni = 20;
$pageNum_Prenotazioni = 0;
if (isset($_GET['pageNum_Prenotazioni'])) {
  $pageNum_Prenotazioni = $_GET['pageNum_Prenotazioni'];
}
$startRow_Prenotazioni = $pageNum_Prenotazioni * $maxRows_Prenotazioni;

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Prenotazioni = "SELECT prenotazioni.GIORNO, prenotazioni.IDnome, prenotazioni.IDPra, prenotazioni.SedePra, prenotazioni.TiRaPr,  
						prenotazioni.SedeCe, prenotazioni.IDCe, prenotazioni.TiRaCe, prenotazioni.SedeCol, prenotazioni.IDCol, prenotazioni.TiRaCol, 
						pre_elenconomi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, prenotazioni.USR_pra, prenotazioni.USR_col, prenotazioni.USR_ce 
						FROM prenotazioni, pre_elenconomi, pre_utentixunita
						WHERE prenotazioni.IDnome=pre_elenconomi.IDnome AND prenotazioni.GIORNO='$giorno' AND pre_utentixunita.ID_UO=pre_elenconomi.UO
						AND pre_utentixunita.IDnome='$Tx_cert'";
$query_limit_Prenotazioni = sprintf("%s LIMIT %d, %d", $query_Prenotazioni, $startRow_Prenotazioni, $maxRows_Prenotazioni);
$Prenotazioni = mysql_query($query_limit_Prenotazioni, $MyPresenze) or die(mysql_error());
$row_Prenotazioni = mysql_fetch_assoc($Prenotazioni);

if (isset($_GET['totalRows_Prenotazioni'])) {
  $totalRows_Prenotazioni = $_GET['totalRows_Prenotazioni'];
} else {
  $all_Prenotazioni = mysql_query($query_Prenotazioni);
  $totalRows_Prenotazioni = mysql_num_rows($all_Prenotazioni);
}
$totalPages_Prenotazioni = ceil($totalRows_Prenotazioni/$maxRows_Prenotazioni)-1;

$maxRows_Pranzi = 20;;
$pageNum_Pranzi = 0;
if (isset($_GET['pageNum_Pranzi'])) {
  $pageNum_Pranzi = $_GET['pageNum_Pranzi'];
}
$startRow_Pranzi = $pageNum_Pranzi * $maxRows_Pranzi;

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Pranzi = "SELECT selectpra.IDrecord, selectpra.GIORNO FROM selectpra WHERE selectpra.GIORNO='$giorno'";
$query_limit_Pranzi = sprintf("%s LIMIT %d, %d", $query_Pranzi, $startRow_Pranzi, $maxRows_Pranzi);
$Pranzi = mysql_query($query_limit_Pranzi, $MyPresenze) or die(mysql_error());
$row_Pranzi = mysql_fetch_assoc($Pranzi);

if (isset($_GET['totalRows_Pranzi'])) {
  $totalRows_Pranzi = $_GET['totalRows_Pranzi'];
} else {
  $all_Pranzi = mysql_query($query_Pranzi);
  $totalRows_Pranzi = mysql_num_rows($all_Pranzi);
}
$totalPages_Pranzi = ceil($totalRows_Pranzi/$maxRows_Pranzi)-1;

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Cene = "SELECT selectcene.IDnome, selectcene.GIORNO FROM selectcene WHERE selectcene.GIORNO='$giorno'";
$Cene = mysql_query($query_Cene, $MyPresenze) or die(mysql_error());
$row_Cene = mysql_fetch_assoc($Cene);
$totalRows_Cene = mysql_num_rows($Cene);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Colazioni = "SELECT selectcol.IDrecord, selectcol.GIORNO FROM selectcol WHERE selectcol.GIORNO='$giorno'";
$Colazioni = mysql_query($query_Colazioni, $MyPresenze) or die(mysql_error());
$row_Colazioni = mysql_fetch_assoc($Colazioni);
$totalRows_Colazioni = mysql_num_rows($Colazioni);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Giorni = "SELECT date_format(selectgiorno.GIORNO,'%d-%m-%Y') AS GIO, selectgiorno.GIORNO FROM selectgiorno GROUP BY selectgiorno.GIORNO DESC";
$Giorni = mysql_query($query_Giorni, $MyPresenze) or die(mysql_error());
$row_Giorni = mysql_fetch_assoc($Giorni);
$totalRows_Giorni = mysql_num_rows($Giorni);
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Documento senza titolo</title>
<style type="text/css">
<!--
body,td,th {
	color: #CCCCCC;
}
body {
	background-color: #333333;
}
.Stile4 {
	color: #00FF00;
	font-size: 12px;
}
.Stile5 {
	color: #FF6633;
	font-size: 12px;
}
.Stile6 {
	color: #00CCFF;
	font-size: 12px;
}
.Stile10 {font-size: 12px}
.Stile22 {
	font-size: 11px;
	font-weight: bold;
}
.Stile24 {color: #FFFFFF; font-size: 11px; font-weight: bold; }
.Stile1 {font-size: x-small}
.Stile25 {font-size: 12}
-->
</style></head>

<body>
<p align="center"><img src="../images/BannerCealpi1.jpg" width="827" height="99"><span class="Stile1"><a href="PrenotazioneMassiva.php"><img src="../Immagini/xbox360power.jpg" width="106" height="101" border="0"></a></span></p>
<form name="form1" method="post" action="">
  <div align="center">Elenco delle prenotazioni pasti per la giornata del  
    <select name="GiornoPren" id="GiornoPren">
      <?php
do {  
?>
      <option value="<?php echo $row_Giorni['GIORNO']?>"<?php if (!(strcmp($row_Giorni['GIORNO'], $_SESSION['GioPre']))) {echo "SELECTED";} ?>><?php echo $row_Giorni['GIO']?></option>
      <?php
} while ($row_Giorni = mysql_fetch_assoc($Giorni));
  $rows = mysql_num_rows($Giorni);
  if($rows > 0) {
      mysql_data_seek($Giorni, 0);
	  $row_Giorni = mysql_fetch_assoc($Giorni);
  }
?>
    </select>
    <input type="submit" name="Submit" value="Aggiorna">
</div>
</form>
<?php if ($totalRows_Prenotazioni > 0) { // Show if recordset not empty ?>
<table width="582" align="center">
  <tr>
    <th width="307" height="28" align="center" valign="middle" scope="col"><form action="pdf.php" method="post" name="form2" target="_blank">
      <div align="center">
        <input name="report" type="hidden" id="report" value="2">
        <input type="submit" name="Submit" value="Stampa prenotazione pasti" img src="../Immagini/pdf.gif" width="57" height="58" >        
        <input name="gio" type="hidden" id="gio" value="<?php echo $_SESSION['GioPre'];?>">
        </div>
    </form></th>
    <th width="259" align="center" valign="middle" scope="col"><form action="DC1.php" method="post" name="form2" target="_blank">
      <div align="center">
        <input name="report" type="hidden" id="report" value="2">
        <input type="submit" name="Submit" value="Stampa mod DC1" img src="../Immagini/pdf.gif" width="57" height="58" >
        <input name="gio" type="hidden" id="gio" value="<?php echo $_SESSION['GioPre'];?>">
        </div>
    </form></th>
  </tr>
</table>
<?php } // Show if recordset not empty ?>
<?php if ($totalRows_Prenotazioni > 0) { // Show if recordset not empty ?>
<table width="1000" border="1" align="center">
  <tr>
    <th width="14%" rowspan="2" scope="col"><span class="Stile10">Nome</span></th>
    <th height="33%" colspan="2" scope="col"><span class="Stile10"><span class="Stile5">Pranzo: <?php echo $totalRows_Pranzi ?></span></span></th>
    <th colspan="2" scope="col"><span class="Stile6">Cena: <?php echo $totalRows_Cene ?></span></th>
    <th colspan="2" scope="col"><span class="Stile4">Colazione: <span class="Stile10"><span class="Stile4"><?php echo $totalRows_Colazioni ?></span></span></span></th>
  </tr>
  <tr>
    <th width="20%" height="28%" nowrap scope="col"><span class="Stile10">  Razione - Sede</span></th>
    <th width="7%" class="Stile10" scope="col">Cancella</th>
    <th width="21%" nowrap scope="col"><span class="Stile10">Razione - Sede</span></th>
    <th width="8%" class="Stile10" scope="col">Cancella</th>
    <th width="21%" nowrap scope="col"><span class="Stile10">Razione - Sede</span></th>
    <th width="9%" class="Stile10" scope="col">Cancella</th>
  </tr>
  <?php $n_row = 1; 
  do { 
  if ( $odd = $n_row%2 ) {; ?>
  <tr bgcolor="#333333" class="Stile24">
    <?php }; ?>
    <td height="26" nowrap><span class="Stile24 Stile25"> <?php echo $row_Prenotazioni['Grado']; ?> <?php echo $row_Prenotazioni['Cognome']; ?> <?php echo $row_Prenotazioni['Nome']; ?> </span></td>
    <td nowrap><span class="Stile24"><span class="Stile22">
</span></span>
      <span class="Stile24"><?php echo $row_Prenotazioni['TiRaPr']; ?>-<?php echo $row_Prenotazioni['SedePra']; ?></span></td>
    <td nowrap>      <form name="form3" method="post" action="">
      <?php if($row_Prenotazioni['USR_pra'] == $_SESSION['UserID']){ ?>
      <input name="Submit2" type="submit" class="Stile5" value="Cancella">      
<input name="IDpra" type="hidden" id="IDpra" value="<?php echo $row_Prenotazioni['IDPra']; ?>">
<?php } ?>
    </form></td>
    <td nowrap><span class="Stile24"><?php echo $row_Prenotazioni['TiRaCe']; ?> - <?php echo $row_Prenotazioni['SedeCe']; ?></span></td>
    <td nowrap> <form name="form4" method="post" action="">
      <?php if($row_Prenotazioni['USR_ce'] == $_SESSION['UserID']){ ?>
      <input name="Submit3" type="submit" class="Stile6" value="Cancella">
      <input name="IDce" type="hidden" id="IDce" value="<?php echo $row_Prenotazioni['IDCe']; ?>">
      <?php } ?>
    </form></td>
    <td nowrap><span class="Stile24"><?php echo $row_Prenotazioni['TiRaCol']; ?> - <?php echo $row_Prenotazioni['SedeCol']; ?></span></td>
    <td nowrap> <form name="form5" method="post" action="">
      <?php if($row_Prenotazioni['USR_col'] == $_SESSION['UserID']){ ?>
      <input name="Submit4" type="submit" class="Stile4" value="Cancella">
      <input name="IDcol" type="hidden" id="IDcol" value="<?php echo $row_Prenotazioni['IDCol']; ?>">
      <?php } ?>
    </form></td>
  </tr>
  <?php $n_row ++; } while ($row_Prenotazioni = mysql_fetch_assoc($Prenotazioni)); ?>
</table>
<?php } // Show if recordset not empty ?>
<p>&nbsp;</p>

</body>
</html>
<?php
mysql_free_result($Prenotazioni);
mysql_free_result($Pranzi);
mysql_free_result($Cene);
mysql_free_result($Colazioni);
mysql_free_result($Giorni);

?>
