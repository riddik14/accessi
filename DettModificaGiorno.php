<?php require_once('../Connections/MyPresenze.php'); ?>
<?php

session_start();
if (!($_SESSION['UserID'])) {
  unset($_SESSION['UserID']);
  header("Location: LoginPresenze.php");
}

$parIDpers = $_SESSION['UserID'];

if (isset($_SESSION['GIORNO'])) {
	$parGiorno_Giorno = $_SESSION['GIORNO'];
}
if (isset($_GET['giorno'])) {
  $_SESSION['GIORNO'] = $_GET['giorno'];
    $parGiorno_Giorno = $_GET['giorno'];
}
if (isset($_GET['IDnome'])) {
  $_SESSION['IDnome'] = $_GET['IDnome'];
    $parIDnome = $_GET['IDnome'];
}

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE pre_orari_gg SET CAUSA=%s, ORA_OUT=%s, ORA_MOD_OUT=%s, NOTE=%s, ID_MOD_OUT=%s WHERE ID=%s",
                       GetSQLValueString($_POST['select'], "text"),
					   GetSQLValueString($_POST['hh2'], "date"),
					   GetSQLValueString(date("Y-m-d H:i"), "date"),
					   GetSQLValueString($_POST['NOTE'], "text"),
					   GetSQLValueString($_SESSION['UserID'], "int"),
					   GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($updateSQL, $MyPresenze) or die(mysql_error());

  $updateGoTo = "Validazione.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
 // header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form5")) {
  $updateSQL = sprintf("UPDATE pre_orari_gg SET ORA_IN=%s, ORA_MOD_IN=%s, ID_MOD_IN=%s WHERE ID=%s",
                       GetSQLValueString($_POST['ORA_ING'], "date"),
					   GetSQLValueString(date("Y-m-d H:i"), "date"),
					   GetSQLValueString($_SESSION['UserID'], "int"),
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($updateSQL, $MyPresenze) or die(mysql_error());
   $updateGoTo = "Validazione.php";
//  header(sprintf("Location: %s", $updateGoTo));
}

$parAmministrato = $_SESSION['IDNAME'];
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Amministrato = "SELECT pre_elenconomi.IDnome, pre_elenconomi.Cognome, pre_elenconomi.UO, pre_elenconomi.Nome, pre_elenconomi.Foto, pre_gradi.Grado FROM pre_elenconomi, pre_gradi WHERE IDnome='$parAmministrato' AND pre_gradi.ID=pre_elenconomi.IDgrado";
$Amministrato = mysql_query($query_Amministrato, $MyPresenze) or die(mysql_error());
$row_Amministrato = mysql_fetch_assoc($Amministrato);
$totalRows_Amministrato = mysql_num_rows($Amministrato);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
	$oraRegOut = date("Y-m-d H:i:s");
	$oraRegIn = date("Y-m-d H:i:s");
	if (!isset($_POST['ORA_out']) && !isset($_POST['MIN_out'])) {
		$OrarioOUT = "";
	} else {
		$OrarioOUT = $_POST['ORA_out'].":". $_POST['MIN_out'];
	}
	
  $insertSQL = sprintf("INSERT INTO pre_orari_gg (IDnome, ORA_IN, ORA_OUT, GIORNO, ORA_REG_OUT, ORA_REG_IN, CAUSA) VALUES (%s, %s, %s, %s, %s, %s, %s)",
  					   GetSQLValueString($_POST['IDuser'], "int"),
                       GetSQLValueString($_POST['ORA_in'].":".$_POST['MIN_in'], "date"),
                       GetSQLValueString($OrarioOUT, "date"),
					   GetSQLValueString($_POST['AA_ins']."-".$_POST['MM_ins']."-".$_POST['GG_ins'], "date"),
					   GetSQLValueString($oraRegOut, "date"),
					   GetSQLValueString($oraRegIn, "date"),
					   GetSQLValueString($_POST['causa'], "text"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($insertSQL, $MyPresenze) or die(mysql_error());
}

if ((isset($_POST['IDrecord'])) && ($_POST['MM_delete'] ==  "form2")) {
  $deleteSQL = sprintf("DELETE FROM pre_orari_gg WHERE ID=%s",
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($deleteSQL, $MyPresenze) or die(mysql_error());

  $deleteGoTo = "Validazione.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
 // header(sprintf("Location: %s", $deleteGoTo));
}

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Giorno = "SELECT pre_orari_gg.ID, pre_orari_gg.IDnome, pre_orari_gg.ORA_IN, pre_orari_gg.ORA_OUT, 
				 date_format(pre_orari_gg.GIORNO,'%d/%m/%Y') AS GIORNO, pre_orari_gg.CAUSA, pre_elenconomi.Cognome, 
				 pre_elenconomi.Nome, pre_orari_gg.NOTE
				 FROM pre_orari_gg, pre_elenconomi 
				 WHERE pre_elenconomi.IDnome=pre_orari_gg.IDnome AND pre_orari_gg.GIORNO='$parGiorno_Giorno' AND pre_orari_gg.IDnome='$parIDnome'";
$Giorno = mysql_query($query_Giorno, $MyPresenze) or die(mysql_error());
$row_Giorno = mysql_fetch_assoc($Giorno);
$totalRows_Giorno = mysql_num_rows($Giorno);
//Record set User - seleziona l'utente in base alla variabile d'ambiente MM_UserGroup
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Documento senza titolo</title>
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
.Stile7 {font-size: x-small}
-->
</style>
</head>

<body>
<p align="center"><img src="./images/BannerCealpi.jpg" width="800" height="81"><a href="Validazione.php"><img src="./images/xbox360power.jpg" name="Esci" width="81" height="81" border="0" id="Esci"></a></p>

<table height="190" border="1" align="center">
  <tr align="center" valign="middle">
    <td width="141">Amministrato</td>
    <td width="98"><div align="center" class="Stile6">
        <div align="center">Giorno</div>
    </div></td>
    <td width="84">orario ingresso (hh:mm:ss)    </td>
    <td width="250">orario uscita e causale </td>
    <td width="71"><div align="center" class="Stile6">
        <div align="center">Elimina registrazione </div>
    </div></td>
  </tr>
  <?php do { ?>
  <tr align="center" valign="middle">
    <td><p><img name="Foto" src="<?php echo $row_Amministrato['Foto']; ?>" width="56" height="76" alt=""></p>
    <p><span class="Stile6"><?php echo $row_Amministrato['Grado']; ?> <?php echo $row_Amministrato['Cognome']; ?> <?php echo $row_Amministrato['Nome']; ?></span></p></td>
    <td height="159"><div align="center" class="Stile6"><?php echo $row_Giorno['GIORNO']; ?></div></td>
    <td><form name="form5" method="POST" action="<?php echo $editFormAction; ?>">
      <p>
        <input name="ORA_ING" type="text" class="Stile7" id="ORA_ING" value="<?php echo $row_Giorno['ORA_IN']; ?>" size="8" maxlength="8">
        <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Giorno['ID']; ?>">
        </p>
      <p>
          <input name="Submit" type="submit" class="Stile7" value="Salva modifica">
          <input type="hidden" name="MM_update" value="form5">
        </p>
    </form></td>
    <td><form name="form1" method="POST" action="<?php echo $editFormAction; ?>">
      <p>
        <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Giorno['ID']; ?>">
        <span class="Stile6"> ore (hh:mm:ss)
        <input name="hh2" type="text" class="Stile7" id="hh2" value="<?php echo $row_Giorno['ORA_OUT']; ?>" size="8" maxlength="8">
        </span></p>
      <p><span class="Stile6">causale 
          <select name="select" class="Stile7">
            <option value="NOL" <?php if (!(strcmp("NOL", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>NORMALE ORARIO DI LAVORO</option>
            <option value="COL" <?php if (!(strcmp("COL", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>COMANDO E LOGISTICA</option>
            <option value="SRD" <?php if (!(strcmp("SRD", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>SERVIZI DI CASERMA</option>
            <option value="REC" <?php if (!(strcmp("REC", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>RECUPERO COMPENSATIVO</option>
            <option value="SI1" <?php if (!(strcmp("SI1", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>SERVIZIO ISOLATO</option>
            <option value="PER" <?php if (!(strcmp("PER", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>PERMESSO</option>
          </select>
        </span></p>
      <p><span class="Stile6">          note
          <input name="NOTE" type="text" class="Stile7" id="NOTE" value="<?php echo $row_Giorno['NOTE']; ?>" size="50">
</span></p>
      <p><span class="Stile6">
        <input name="Submit" type="submit" class="Stile7" value="Salva modifica">
          </span>
          <input type="hidden" name="MM_update" value="form1">
        </p>
    </form></td>
    <td><div align="center" class="Stile7">
      <form name="form2" method="post" action="">
        <input name="MM_delete" type="hidden" id="MM_delete" value="form2">
        <input type="submit" name="Submit" value="Elimina">
        <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Giorno['ID']; ?>">
</form> 
      </div>
    </td>
  </tr>
  <?php } while ($row_Giorno = mysql_fetch_assoc($Giorno)); ?>
</table>
<form name="form3" method="post" action="">
  <div align="center">
    <input type="submit" name="Submit" value="Inserimento giornate">
    <input name="MM_insert" type="hidden" id="MM_insert" value="form3">
  </div>
</form>
<?php if (isset($_POST['MM_insert']) && $_POST['MM_insert'] == "form3") { ?>
<form name="form4" method="POST" action="<?php echo $editFormAction; ?>">
  <div align="center">Data
    <input name="GG_ins" type="text" id="GG_ins" size="2" maxlength="2"> 
  /
  <input name="MM_ins" type="text" id="MM_ins" size="2" maxlength="2"> 
  /
  <input name="AA_ins" type="text" id="AA_ins" size="6" maxlength="4"> 
  ingresso ore 
  <input name="ORA_in" type="text" id="ORA_in" size="2" maxlength="2"> 
  :
  <input name="MIN_in" type="text" id="MIN_in" size="2" maxlength="2"> 
  uscita ore 
  <input name="ORA_out" type="text" id="ORA_out" size="2" maxlength="2">
  :
  <input name="MIN_out" type="text" id="MIN_out" size="2" maxlength="2">
  <span class="Stile6">
   causale 
   <select name="causa" class="Stile7" id="causa">
     <option value="NOL" <?php if (!(strcmp("NOL", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>NORMALE ORARIO DI LAVORO</option>
     <option value="COL" <?php if (!(strcmp("COL", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>COMANDO E LOGISTICA</option>
     <option value="SRD" <?php if (!(strcmp("SRD", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>SERVIZI DI CASERMA</option>
     <option value="REC" <?php if (!(strcmp("REC", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>RECUPERO COMPENSATIVO</option>
     <option value="SI1" <?php if (!(strcmp("SI1", $row_Giorno['CAUSA']))) {echo "SELECTED";} ?>>SERVIZIO ISOLATO</option>
  </select>
  </span>
  <input type="submit" name="Submit" value="Salva">
  <input type="hidden" name="MM_insert" value="form4">
  <input name="IDuser" type="hidden" id="IDuser" value="<?php echo $_SESSION['IDNAME']; ?>">
</div>
</form>
<?php }; ?>

<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($Amministrato);

mysql_free_result($Giorno);
?>
