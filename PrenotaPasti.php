<?php require_once('../Connections/MyPresenze.php');
session_start(); 
if (!($_SESSION['UserID'])) {
  unset($_SESSION['UserID']);
  header("Location: LoginPresenze.php");
}
?>
<?php
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

// Inserisce record per pranzo da Form 1

if ((isset($_POST["MM_insert1"])) && ($_POST["MM_insert1"] == "form1")) {
	 $insertSQL = sprintf("INSERT INTO pre_accessi (IDnome, GIORNO, PASTO, Ora_pren, USR, Ti_R, Se, Pagamento) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDUSR'], "int"),
                       GetSQLValueString($_POST['GIORNO'], "date"),
                       GetSQLValueString($_POST['Pasto'], "int"),
                       GetSQLValueString($_POST['Ora_pren'], "date"),
                       GetSQLValueString($_POST['IDUSR'], "int"),
                       GetSQLValueString($_POST['TipoRazione'], "int"),
                       GetSQLValueString($_POST['sede'], "int"),
					   GetSQLValueString($_POST['Pagamento'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($insertSQL, $MyPresenze) or die(mysql_error());
  
}

if ((isset($_POST["MM_insert2"])) && ($_POST["MM_insert2"] == "form2")) {
	
  $insertSQL = sprintf("INSERT INTO pre_accessi (IDnome, GIORNO, PASTO, Ora_pren, USR, Ti_R, Se, Pagamento) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDUSR'], "int"),
                       GetSQLValueString($_POST['GIORNO'], "date"),
                       GetSQLValueString($_POST['Pasto'], "int"),
                       GetSQLValueString($_POST['Ora_pren'], "date"),
                       GetSQLValueString($_POST['IDUSR'], "int"),
                       GetSQLValueString($_POST['TipoRazione'], "int"),
                       GetSQLValueString($_POST['sede'], "int"),
					   GetSQLValueString($_POST['Pagamento'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($insertSQL, $MyPresenze) or die(mysql_error());
}

if ((isset($_POST["MM_insert3"])) && ($_POST["MM_insert3"] == "form3")) {
	  $insertSQL = sprintf("INSERT INTO pre_accessi (IDnome, GIORNO, PASTO, Ora_pren, USR, Ti_R, Se, Pagamento) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDUSR'], "int"),
                       GetSQLValueString($_POST['GIORNO'], "date"),
                       GetSQLValueString($_POST['Pasto'], "int"),
                       GetSQLValueString($_POST['Ora_pren'], "date"),
                       GetSQLValueString($_POST['IDUSR'], "int"),
                       GetSQLValueString($_POST['TipoRazione'], "int"),
                       GetSQLValueString($_POST['sede'], "int"),
					   GetSQLValueString($_POST['Pagamento'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($insertSQL, $MyPresenze) or die(mysql_error());
}


if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "form5")) {
$deleteSQL = sprintf("DELETE FROM pre_accessi WHERE IDrecord=%s",
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($deleteSQL, $MyPresenze) or die(mysql_error());
}

 if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "form6")) {
$deleteSQL = sprintf("DELETE FROM pre_accessi WHERE IDrecord=%s",
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($deleteSQL, $MyPresenze) or die(mysql_error());
} 
 if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "form7")) {
$deleteSQL = sprintf("DELETE FROM pre_accessi WHERE IDrecord=%s",
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($deleteSQL, $MyPresenze) or die(mysql_error());
}
$parID_User = "%";
if (isset($_SESSION['UserID'])) {
  $parID_User = addslashes($_SESSION['UserID']);
}
//Record set User - seleziona l'utente in base alla variabile d'ambiente MM_UserGroup
mysql_select_db($database_MyPresenze, $MyPresenze);
$parIDuser = $_SESSION['UserID'];
$query_User = "SELECT pre_elenconomi.IDnome, pre_gradi.Grado, pre_elenconomi.Cognome, 
				pre_elenconomi.Nome, pre_elenconomi.UO, pre_elenconomi.SedeSomm, pre_elenconomi.TipoRazione
				FROM pre_elenconomi, pre_gradi WHERE pre_elenconomi.IDnome='$parIDuser' AND pre_elenconomi.IDgrado=pre_gradi.ID";
$User = mysql_query($query_User, $MyPresenze) or die(mysql_error());
$row_User = mysql_fetch_assoc($User);
$totalRows_User = mysql_num_rows($User);

$parUser_Registro = "%";
if (isset($_SESSION['UserID'])) {
  $parUser_Registro = addslashes($_SESSION['UserID']);
}
mysql_select_db($database_MyPresenze, $MyPresenze);

$parUser_DatiPres = "%";
if (isset($_SESSION['UserID'])) {
  $parUser_DatiPres = addslashes($_SESSION['UserID']);
}

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Sede = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sede = mysql_query($query_Sede, $MyPresenze) or die(mysql_error());
$row_Sede = mysql_fetch_assoc($Sede);
$totalRows_Sede = mysql_num_rows($Sede);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_TipoRazione = "SELECT pre_tiporazione.ID, pre_tiporazione.TipoRazione FROM pre_tiporazione";
$TipoRazione = mysql_query($query_TipoRazione, $MyPresenze) or die(mysql_error());
$row_TipoRazione = mysql_fetch_assoc($TipoRazione);
$totalRows_TipoRazione = mysql_num_rows($TipoRazione);
mysql_select_db($database_MyPresenze, $MyPresenze);

$ParGG = date("Y-m-d");
$ParIDnome = $_SESSION['UserID'];
$query_Prenotazione_Pr = "SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, pre_accessi.USR 
						  FROM pre_accessi WHERE pre_accessi.PASTO=1 AND pre_accessi.GIORNO='$ParGG' AND pre_accessi.IDnome='$ParIDnome'" ;
$Prenotazione_Pr = mysql_query($query_Prenotazione_Pr, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Pr = mysql_fetch_assoc($Prenotazione_Pr);
$totalRows_Prenotazione_Pr = mysql_num_rows($Prenotazione_Pr);
mysql_select_db($database_MyPresenze, $MyPresenze);


$query_Prenotazione_Ce = "SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, pre_accessi.USR 
							FROM pre_accessi WHERE pre_accessi.PASTO=2 AND pre_accessi.GIORNO='$ParGG'AND pre_accessi.IDnome='$ParIDnome'";
$Prenotazione_Ce = mysql_query($query_Prenotazione_Ce, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Ce = mysql_fetch_assoc($Prenotazione_Ce);
$totalRows_Prenotazione_Ce = mysql_num_rows($Prenotazione_Ce);
mysql_select_db($database_MyPresenze, $MyPresenze);

$ParGG_Col = date ("Y-m-d", mktime(0,0,0,date("m"),date("d")+1,date("Y")));
$query_Prenotazione_Col = "SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, pre_accessi.USR 
							FROM pre_accessi WHERE pre_accessi.PASTO=3 AND pre_accessi.GIORNO='$ParGG_Col'AND pre_accessi.IDnome='$ParIDnome'";
$Prenotazione_Col = mysql_query($query_Prenotazione_Col, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Col = mysql_fetch_assoc($Prenotazione_Col);
$totalRows_Prenotazione_Col = mysql_num_rows($Prenotazione_Col);

mysql_select_db($database_MyPresenze, $MyPresenze);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Utente corrente <?php echo ($row_User['Cognome']." ". $row_User['Nome']);?> </title>
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
	font-size: 18px;
	font-style: normal;
	font-weight: bold;
	color: #33CC33;
	text-decoration: blink;
}
.Stile8 {font-size: small; font-family: Arial, Helvetica, sans-serif; line-height: normal; }
.Stile10 {
	color: #FF0000;
	font-weight: bold;
}
.Stile12 {color: #FF0000; font-weight: bold; font-size: 10px; }
.Stile13 {color: #333333}
.Stile14 {color: #333333; font-weight: bold; }
-->
</style>
</head>

<body background="images/Sfondo_vista.jpg" style="background-repeat:no-repeat;background-position:top center;">
<p align="center"><img src="./images/BannerCealpi.jpg" width="678" height="63"></p>
<p align="center"><span class="ORE"><?php echo $row_User['Grado']; ?> <?php echo $row_User['Cognome']; ?> <?php echo $row_User['Nome'];?></span></p>
<p align="center" class="Stile14">Prenotazione pasti per il giorno <?php echo date ("d-m-Y");?> </p>
<table width="544" border="1" align="center">
  <tr valign="top">
    <td width="174" align="center"><form name="form1" method="POST" action="<?php echo $editFormAction; ?>" onSubmit="javascript:disabilita_pranzo()">
      <div align="center" class="Stile10"> PRANZO
<?php if (!(isset($row_Prenotazione_Pr['IDnome']))) { ; ?>
      </div>
      <table width="174" align="center">
        <tr>
          <th width="90" scope="col"><div align="center" class="Stile13">Razione</div></th>
          <th width="68" scope="col"><div align="center" class="Stile13">Sede</div></th>
          </tr>
        <tr>
          <td><select name="TipoRazione" class="Stile8" id="select7">
              <?php
do {  
?>
              <option value="<?php echo $row_TipoRazione['ID']?>"<?php if (!(strcmp($row_TipoRazione['ID'], $row_User['TipoRazione']))) {echo "SELECTED";} ?>><?php echo $row_TipoRazione['TipoRazione']?></option>
              <?php
} while ($row_TipoRazione = mysql_fetch_assoc($TipoRazione));
  $rows = mysql_num_rows($TipoRazione);
  if($rows > 0) {
      mysql_data_seek($TipoRazione, 0);
	  $row_TipoRazione = mysql_fetch_assoc($TipoRazione);
  }
?>
          </select></td>
          <td><select name="sede" class="Stile8" id="select8">
              <?php
do {  
?>
              <option value="<?php echo $row_Sede['IDsede']?>"<?php if (!(strcmp($row_Sede['IDsede'], $row_User['SedeSomm']))) {echo "SELECTED";} ?>><?php echo $row_Sede['SEDE']?></option>
              <?php
} while ($row_Sede = mysql_fetch_assoc($Sede));
  $rows = mysql_num_rows($Sede);
  if($rows > 0) {
      mysql_data_seek($Sede, 0);
	  $row_Sede = mysql_fetch_assoc($Sede);
  }
?>
          </select></td>
          </tr>
      </table>
      <p><span class="Stile13"><strong>a pagamento (solo previo pagamento scotto rancio) </strong></span>          
        <select name="Pagamento" class="Stile8" id="Pagamento">
          <option value="1">Si</option>
          <option value="0" selected>No</option>
        </select>
      </p>
      <p>            <input name="IDUSR" type="hidden" id="IDUSR22" value="<?php echo $_SESSION['UserID'];?>">
          <input name="Ora_pren" type="hidden" id="Ora_pren22" value="<?php echo date ("Y-m-d H:i");?>">
          <input name="Pasto" type="hidden" id="Pasto22" value="1">
          <span class="Stile10">
          <input name="GIORNO" type="hidden" id="GIORNO22" value=" <?php echo date("Y-m-d"); ?>" size="8" maxlength="10">
          </span>
          <input name="MM_insert1" type="hidden" id="MM_insert14" value="form1">
          <input type="submit" name="Submit4" value="Prenota">
          <?php }; ?>
        </p>
    </form></td>
    <td width="174" align="center"><form name="form2" method="POST" action="<?php echo $editFormAction; ?>" onSubmit="javascript:disabilita_cena()">
      <span class="Stile10">CENA</span>
      <?php if (!(isset($row_Prenotazione_Ce['IDnome']))) { ; ?>
      <table width="174" align="center">
        <tr>
          <th width="90" scope="col"><div align="center" class="Stile13">Razione</div></th>
          <th width="68" scope="col"><div align="center" class="Stile13">Sede</div></th>
          </tr>
        <tr>
          <td><select name="TipoRazione" class="Stile8" id="select9">
              <?php
do {  
?>
              <option value="<?php echo $row_TipoRazione['ID']?>"<?php if (!(strcmp($row_TipoRazione['ID'], $row_User['TipoRazione']))) {echo "SELECTED";} ?>><?php echo $row_TipoRazione['TipoRazione']?></option>
              <?php
} while ($row_TipoRazione = mysql_fetch_assoc($TipoRazione));
  $rows = mysql_num_rows($TipoRazione);
  if($rows > 0) {
      mysql_data_seek($TipoRazione, 0);
	  $row_TipoRazione = mysql_fetch_assoc($TipoRazione);
  }
?>
          </select></td>
          <td><select name="sede" class="Stile8" id="select10">
              <?php
do {  
?>
              <option value="<?php echo $row_Sede['IDsede']?>"<?php if (!(strcmp($row_Sede['IDsede'], $row_User['SedeSomm']))) {echo "SELECTED";} ?>><?php echo $row_Sede['SEDE']?></option>
              <?php
} while ($row_Sede = mysql_fetch_assoc($Sede));
  $rows = mysql_num_rows($Sede);
  if($rows > 0) {
      mysql_data_seek($Sede, 0);
	  $row_Sede = mysql_fetch_assoc($Sede);
  }
?>
          </select></td>
          </tr>
      </table>
      <p><span class="Stile13">a pagamento <strong>(solo previo pagamento scotto rancio)</strong></span>        
        <select name="Pagamento" class="Stile8" id="Pagamento">
          <option value="1">Si</option>
          <option value="0" selected>No</option>
        </select>
</p>
      <p>
          <input name="Ora_pren" type="hidden" id="Ora_pren32" value="<?php echo date ("Y-m-d H:i");?>">
          <input name="Pasto" type="hidden" id="Pasto32" value="2">
          <input name="IDUSR" type="hidden" id="IDUSR32" value="<?php echo $_SESSION['UserID'];?>">
          <span class="Stile10">
          <input name="GIORNO" type="hidden" id="GIORNO32" value=" <?php echo date("Y-m-d"); ?>" size="8" maxlength="10">
          </span>
          <input name="MM_insert2" type="hidden" id="MM_insert23" value="form2">
          <input type="submit" name="Submit5" value="Prenota">
          <?php }; ?>
        </p>
    </form></td>
    <td width="174" height="134" align="center"><form name="form3" method="POST" action="<?php echo $editFormAction; ?>" onSubmit="javascript:disabilita_colazione()">
      <span class="Stile10">COLAZIONE</span>
<?php if (!(isset($row_Prenotazione_Col['IDnome']))) { ; ?>
        <table width="174" align="center">
        <tr>
          <th width="90" scope="col"><div align="center" class="Stile13">Razione</div></th>
          <th width="68" scope="col"><div align="center" class="Stile13">Sede</div></th>
          </tr>
        <tr>
          <td>
            <select name="TipoRazione" class="Stile8" id="select11">
              <?php
do {  
?>
              <option value="<?php echo $row_TipoRazione['ID']?>"<?php if (!(strcmp($row_TipoRazione['ID'], $row_User['TipoRazione']))) {echo "SELECTED";} ?>><?php echo $row_TipoRazione['TipoRazione']?></option>
              <?php
} while ($row_TipoRazione = mysql_fetch_assoc($TipoRazione));
  $rows = mysql_num_rows($TipoRazione);
  if($rows > 0) {
      mysql_data_seek($TipoRazione, 0);
	  $row_TipoRazione = mysql_fetch_assoc($TipoRazione);
  }
?>
          </select></td>
          <td><select name="sede" class="Stile8" id="select12">
              <?php
do {  
?>
              <option value="<?php echo $row_Sede['IDsede']?>"<?php if (!(strcmp($row_Sede['IDsede'], $row_User['SedeSomm']))) {echo "SELECTED";} ?>><?php echo $row_Sede['SEDE']?></option>
              <?php
} while ($row_Sede = mysql_fetch_assoc($Sede));
  $rows = mysql_num_rows($Sede);
  if($rows > 0) {
      mysql_data_seek($Sede, 0);
	  $row_Sede = mysql_fetch_assoc($Sede);
  }
?>
          </select></td>
          </tr>
      </table>
        <p><span class="Stile13">a pagamento <strong>(solo previo pagamento scotto rancio)</strong></span>          
          <select name="Pagamento" class="Stile8" id="Pagamento">
            <option value="1">Si</option>
            <option value="0" selected>No</option>
          </select>
        </p>
        <p>
          <input name="Ora_pren" type="hidden" id="Ora_pren5" value="<?php echo date ("Y-m-d H:i");?>">
          <input name="Pasto" type="hidden" id="Pasto5" value="3">
          <input name="IDUSR" type="hidden" id="IDUSR5" value="<?php echo $_SESSION['UserID'];?>">
          <span class="Stile10">
          <input name="GIORNO" type="hidden" id="GIORNO5" value=" <?php echo date ("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y"))); ?>" size="10" maxlength="10">
          <input name="MM_insert3" type="hidden" id="MM_insert32" value="form3">
          <input type="submit" name="Submit" value="Prenota">
          <?php }; ?>
          </span>
        </p>
    </form>
    <span class="Stile12">(*) La prenotazione della colazione &egrave; relativa alla giornata del <?php echo date ("d-m-Y",mktime(0,0,0,date("m"),date("d")+1,date("Y"))); ?> </span></td>
  </tr>
  <tr>
    <td height="89" align="center" valign="top"><?php if ($row_Prenotazione_Pr['PASTO'] == 1) { ?>      <img src="images/spunta.gif" width="25" height="20">      <?php } ;?>        
      <div align="center">
        <form name="form5" method="post" action="<?php echo $editFormAction; ?>">
          <input type="submit" name="Submit2" value="Cancella">
          <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Prenotazione_Pr['IDrecord']; ?> ">
          <input name="MM_delete" type="hidden" id="MM_delete" value="form5">
        </form>
    </div></td>
    <td height="89" align="center" valign="top"><?php if ($row_Prenotazione_Ce['PASTO'] == 2) { ?>      <img src="images/spunta.gif" width="25" height="20">      <?php } ;?> <form action="<?php echo $editFormAction; ?>" method="post" name="form6" id="form6">
      <input type="submit" name="Submit2" value="Cancella">
      <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Prenotazione_Ce['IDrecord']; ?> ">
      <input name="MM_delete" type="hidden" id="MM_delete" value="form6">
    </form></td>
    <td height="89" align="center" valign="top"><?php if ($row_Prenotazione_Col['PASTO'] == 3) { ?>      <img src="images/spunta.gif" width="25" height="20">      <?php } ;?> <form action="<?php echo $editFormAction; ?>" method="post" name="form7" id="form7">
      <input type="submit" name="Submit22" value="Cancella">
      <input name="IDrecord" type="hidden" id="IDrecord3" value="<?php echo $row_Prenotazione_Col['IDrecord']; ?> ">
      <input name="MM_delete" type="hidden" id="MM_delete3" value="form7">
    </form></td>
  </tr>
  <tr valign="middle">
  <td height="44" colspan="3" align="center"><form name="form4" method="post" action="ConfermaSalva.php">
          <div align="center">
            <input name="Submit3" type="submit" class="ORE" value="Fine &gt;">
</div>
      </form></td>
  </tr>
</table>
<p align="center">&nbsp;</p>

<script language="javascript">
function disabilita_pranzo() {
document.form1.Submit4.disabled=true;
}

function disabilita_cena() {
document.form2.Submit3.disabled=true;
}

function disabilita_colazione() {
document.form3.Submit.disabled=true;
}
</script>
</body>
</html>
<?php
mysql_free_result($Prenotazione_Col);
mysql_free_result($Sede);
mysql_free_result($TipoRazione);
?>