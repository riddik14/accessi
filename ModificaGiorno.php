<?php require_once('../Connections/MyPresenze.php'); ?>
<?php


$currentPage = $_SERVER["PHP_SELF"];

session_start();
if (!($_SESSION['UserID'])) {
  unset($_SESSION['UserID']);
  header("Location: LoginPresenze.php");
}

$parIDpers = $_SESSION['UserID'];

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_User = "SELECT pre_elenconomi.IDnome, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.CF FROM pre_elenconomi WHERE pre_elenconomi.IDnome='$parIDpers'";
$User = mysql_query($query_User, $MyPresenze) or die(mysql_error());
$row_User = mysql_fetch_assoc($User);
$totalRows_User = mysql_num_rows($User);


$parMese = "%";
if (isset($_POST['mese'])) {
  $parMese = $_POST['mese'];
  $_SESSION['mes'] = $_POST['mese'];
}
$parAnno = "%";
if (isset($_POST['anno'])) {
  $parAnno = $_POST['anno'];
   $_SESSION['an']=$_POST['anno'];
}
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Giorno = "SELECT date_format(pre_orari_gg.GIORNO,'%d/%m/%Y') AS GIORNO, 
				 pre_orari_gg.ORA_IN AS ENTRATA, pre_orari_gg.ORA_OUT AS USCITA, dayofweek(pre_orari_gg.GIORNO) AS GioSet,
				 timediff(`pre_orari_gg`.`ORA_OUT`,`pre_orari_gg`.`ORA_IN`) AS PERIODO, 
				 pre_orari_gg.CAUSA AS CAUSALE, pre_giornisett.WEEKDAY, pre_orari_gg.GIORNO as GIO
				 FROM pre_orari_gg, pre_giornisett
				 WHERE pre_orari_gg.IDnome='$parIDpers' AND year(pre_orari_gg.GIORNO)='$parAnno' AND 
				 month(pre_orari_gg.GIORNO)='$parMese' AND dayofweek(pre_orari_gg.GIORNO)=pre_giornisett.ID
				 ORDER BY pre_orari_gg.GIORNO";
$Giorno = mysql_query($query_Giorno, $MyPresenze) or die(mysql_error());
$row_Giorno = mysql_fetch_assoc($Giorno);
$totalRows_Giorno = mysql_num_rows($Giorno);

$queryString_Giorno = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Giorno") == false && 
        stristr($param, "totalRows_Giorno") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Giorno = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Giorno = sprintf("&totalRows_Giorno=%d%s", $totalRows_Giorno, $queryString_Giorno);

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

//Record set User - seleziona l'utente in base alla variabile d'ambiente MM_UserGroup
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Gestione ingressi e uscite</title>
<style type="text/css">
<!--
body,td,th {
	color: #CCCCCC;
}
body {
	background-color: #1E3871;
}
.ORE {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 16px;
	font-style: normal;
	font-weight: bold;
	color: #33CC33;
	text-decoration: blink;
}
.Stile6 {font-size: small; }
.Stile9 {font-size: x-small}
a:link {
	color: #FFFFFF;
}
a:visited {
	color: #CCCCCC;
}
.Stile12 {color: #333333}
.Stile13 {font-size: small; font-weight: bold; color: #333333; }
.Stile14 {font-size: small; color: #333333; }
-->
</style>
</head>

<body background="images/Sfondo_vista.jpg" style="background-repeat:no-repeat;background-position:top center;">
<p align="center"><img src="images/BannerCealpi.jpg" width="600" height="78"><a href="Master.php"><img src="images/xbox360power.jpg" name="Esci" width="75" height="75" border="0" id="Esci"></a></p>
<table width="931" border="2" align="center">
  <tr align="center">
    <td height="94"><form name="form1" method="post" action="ModificaGiorno.php">
      <div align="center">
        <p class="Stile12">Gestione orari </p>
        <p class="Stile12">mese<span class="Stile6">
          <select name="mese" class="Spulsanti" id="mese">
            <option value="01" <?php if (date("m")==01) {echo "SELECTED";} ?>>gen</option>
            <option value="02" <?php if (date("m")==02) {echo "SELECTED";} ?>>feb</option>
            <option value="03" <?php if (date("m")==03) {echo "SELECTED";} ?>>mar</option>
            <option value="04" <?php if (date("m")==04) {echo "SELECTED";} ?>>apr</option>
            <option value="05" <?php if (date("m")==05) {echo "SELECTED";} ?>>mag</option>
            <option value="06" <?php if (date("m")==06) {echo "SELECTED";} ?>>giu</option>
            <option value="07" <?php if (date("m")==07) {echo "SELECTED";} ?>>lug</option>
            <option value="08" <?php if (date("m")==08) {echo "SELECTED";} ?>>ago</option>
            <option value="09" <?php if (date("m")==09) {echo "SELECTED";} ?>>set</option>
            <option value="10" <?php if (date("m")==10) {echo "SELECTED";} ?>>ott</option>
            <option value="11" <?php if (date("m")==11) {echo "SELECTED";} ?>>nov</option>
            <option value="12" <?php if (date("m")==12) {echo "SELECTED";} ?>>dic</option>
          </select>
        </span>            
      </p>
        <p class="Stile12">anno
            <input name="anno" type="text" id="anno" value="<?php echo date("Y"); ?>" size="6" maxlength="4">
          </p>
        <p>
          <input type="submit" name="Submit" value="Visualizza">
          </p>
      </div>
    </form></td>
    <td width="698" rowspan="4"><?php if ($totalRows_Giorno > 0) { // Show if recordset not empty ?>
	<table width="673" border="1" align="center">
      <tr align="center" valign="middle" class="Stile9">
        <td width="218" height="26"><div align="center" class="Stile13">
            <div align="center">Giorno</div>
        </div></td>
        <td width="111"><div align="center" class="Stile13">
            <div align="center">ingresso (hh:mm) </div>
        </div></td>
        <td width="97"><div align="center" class="Stile13">
            <div align="center">uscita (hh:mm) </div>
        </div></td>
        <td width="104" class="Stile6"><div align="center" class="Stile12"><strong>intervallo</strong></div></td>
        <td width="109"><span class="Stile13">causale</span></td>
        </tr>
      <?php do { ?>
      <tr align="center" class="Stile6">
        <td height="19" nowrap class="Stile9"><div align="left" class="Stile14"><?php echo $row_Giorno['WEEKDAY']; ?> <?php echo $row_Giorno['GIORNO']; ?></div></td>
        <td nowrap class="Stile14"><?php echo $row_Giorno['ENTRATA']; ?></td>
        <td nowrap class="Stile14"><?php echo $row_Giorno['USCITA']; ?></td>
        <td nowrap class="Stile14"><?php echo $row_Giorno['PERIODO']; ?></td>
        <td nowrap class="Stile14"><?php echo $row_Giorno['CAUSALE']; ?></td>
        </tr>
      <?php } while ($row_Giorno = mysql_fetch_assoc($Giorno)); ?>
    </table>
	<?php } // Show if recordset not empty ?></td>
	
  </tr>
  <tr align="center">
    <td height="98"><form action="pdf.php" method="post" name="form3" target="_blank">
      <p class="Stile12">Stampa statino personale </p>
      <p class="Stile12">mese <span class="Stile6">
        <select name="mese" class="Spulsanti" id="mese">
          <option value="01" <?php if (date("m")==01) {echo "SELECTED";} ?>>gen</option>
          <option value="02" <?php if (date("m")==02) {echo "SELECTED";} ?>>feb</option>
          <option value="03" <?php if (date("m")==03) {echo "SELECTED";} ?>>mar</option>
          <option value="04" <?php if (date("m")==04) {echo "SELECTED";} ?>>apr</option>
          <option value="05" <?php if (date("m")==05) {echo "SELECTED";} ?>>mag</option>
          <option value="06" <?php if (date("m")==06) {echo "SELECTED";} ?>>giu</option>
          <option value="07" <?php if (date("m")==07) {echo "SELECTED";} ?>>lug</option>
          <option value="08" <?php if (date("m")==08) {echo "SELECTED";} ?>>ago</option>
          <option value="09" <?php if (date("m")==09) {echo "SELECTED";} ?>>set</option>
          <option value="10" <?php if (date("m")==10) {echo "SELECTED";} ?>>ott</option>
          <option value="11" <?php if (date("m")==11) {echo "SELECTED";} ?>>nov</option>
          <option value="12" <?php if (date("m")==12) {echo "SELECTED";} ?>>dic</option>
        </select>
      </span></p>
      <p class="Stile12">anno
          <input name="anno" type="text" id="anno" value="<?php echo date("Y"); ?>" size="6" maxlength="4">
          <?php echo $row_Giorno['IDnome']; ?> </p>
      <p>
        <input type="submit" name="Submit22" value="Stampa statino rilevazioni">
        <input name="report" type="hidden" id="report" value="1">
        <input name="IDnome" type="hidden" id="IDnome" value="<?php echo $_SESSION['UserID']; ?>">
      </p>
    </form></td>
  </tr>
  
  <tr align="center">
    <td height="48"><p> PIN: <?php echo $row_User['IDnome'] . substr($row_User['CF'], 6, 2) .substr($row_User['CF'], 9, 2); ?></p>
      <p><a href="ExportTxt.php">ExportTxt</a></p></td>
  </tr>
</table>



<p align="center">&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($User);

mysql_free_result($Giorno);
?>
