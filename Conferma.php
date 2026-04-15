<?php require_once('../Connections/MyPresenze.php'); 

session_start();

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

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

$ParGG = date("Y-m-d");
$ParIDnome = $_SESSION['UserID'];
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Prenotazione_Pr = sprintf("SELECT pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.USR, pre_accessi.Pagamento 
						         FROM pre_accessi 
								 WHERE pre_accessi.PASTO=1 AND pre_accessi.GIORNO='$ParGG'AND pre_accessi.IDnome='$ParIDnome'");
$Prenotazione_Pr = mysql_query($query_Prenotazione_Pr, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Pr = mysql_fetch_assoc($Prenotazione_Pr);
$totalRows_Prenotazione_Pr = mysql_num_rows($Prenotazione_Pr);

mysql_select_db($database_MyPresenze, $MyPresenze);

$ParGG = date("Y-m-d");
$ParIDnome = $_SESSION['UserID'];
$query_Prenotazione_Ce = "SELECT pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, pre_accessi.USR, pre_accessi.Pagamento
							FROM pre_accessi WHERE pre_accessi.PASTO=2 AND pre_accessi.GIORNO='$ParGG'AND pre_accessi.IDnome='$ParIDnome'";
$Prenotazione_Ce = mysql_query($query_Prenotazione_Ce, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Ce = mysql_fetch_assoc($Prenotazione_Ce);
$totalRows_Prenotazione_Ce = mysql_num_rows($Prenotazione_Ce);

mysql_select_db($database_MyPresenze, $MyPresenze);
$ParIDnome = $_SESSION['UserID'];
$ParGG = date("Y-m-d");
$query_Prenotazione_Col = "SELECT pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, pre_accessi.USR, pre_accessi.Pagamento
							FROM pre_accessi WHERE pre_accessi.PASTO=3 AND pre_accessi.GIORNO='$ParGG' AND pre_accessi.IDnome='$ParIDnome'";
$Prenotazione_Col = mysql_query($query_Prenotazione_Col, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Col = mysql_fetch_assoc($Prenotazione_Col);
$totalRows_Prenotazione_Col = mysql_num_rows($Prenotazione_Col);

mysql_select_db($database_MyPresenze, $MyPresenze);

//Record set User - seleziona l'utente in base alla variabile d'ambiente MM_UserGroup

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_User = "SELECT pre_elenconomi.IDnome, pre_elenconomi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, 
				pre_elenconomi.UO 
				FROM pre_elenconomi WHERE pre_elenconomi.IDnome='$ParIDnome'";
$User = mysql_query($query_User, $MyPresenze) or die(mysql_error());
$row_User = mysql_fetch_assoc($User);
$totalRows_User = mysql_num_rows($User);

$parUser_Registro = "%";
if (isset($_SESSION['UserID'])) {
  $parUser_Registro = (get_magic_quotes_gpc()) ? $_SESSION['UserID'] : addslashes($_SESSION['UserID']);
}
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Registro = sprintf("SELECT pre_orari_gg.IDnome, date_format(pre_orari_gg.GIORNO,'%%d/%%m/%%Y') as GIORNO,
						 date_format(pre_orari_gg.ORA_IN,'%%H:%%i') as O_IN, date_format(pre_orari_gg.ORA_OUT,'%%H:%%i') AS O_OUT, 
						 timediff(`pre_orari_gg`.`ORA_OUT`,`pre_orari_gg`.`ORA_IN`) AS INTERVALLO, pre_orari_gg.CAUSA 
						 FROM pre_orari_gg WHERE pre_orari_gg.IDnome='%s' AND pre_orari_gg.GIORNO='%s'", $parUser_Registro, $ParGG);
$Registro = mysql_query($query_Registro, $MyPresenze) or die(mysql_error());
$row_Registro = mysql_fetch_assoc($Registro);
$totalRows_Registro = mysql_num_rows($Registro);

mysql_select_db($database_MyPresenze, $MyPresenze);
$giorno = date("w");
$query_Orario = sprintf("SELECT pre_orari.N_GIORNO, pre_orari.GIORNO, pre_orari.ORA_OUT, pre_orari.ORA_IN, pre_orari.TIPO_ORARIO, 
						pre_elenconomi.ID_PERS_MTR, pre_orari.INTERVALLO 
						FROM pre_orari, pre_elenconomi 
						WHERE pre_elenconomi.TipoOrario=pre_orari.TIPO_ORARIO AND pre_orari.N_GIORNO=$giorno");
$Orario = mysql_query($query_Orario, $MyPresenze) or die(mysql_error());
$row_Orario = mysql_fetch_assoc($Orario);
$totalRows_Orario = mysql_num_rows($Orario);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META http-equiv="refresh" content="2;URL=LoginPrenota.php">
<title>Documento senza titolo</title>
<style type="text/css">
<!--
body,td,th {
	color: #CCCCCC;
}
body {
	background-color: #1E3871;
}
.Stile1 {
	font-size: 12px;
	font-weight: bold;
}
.ORE {	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 16px;
	font-style: normal;
	font-weight: bold;
	color: #33CC33;
	text-decoration: blink;
}
.Stile2 {font-size: 16px}
-->
</style>
</head>

<body>
<p align="center"><img src="images/BannerCealpi.jpg" width="800" height="113"></p>
<p align="center">Utente connesso: <span class="ORE"><?php echo $row_User['Grado']; ?> <?php echo $row_User['Cognome']; ?></span> <span class="ORE"><?php echo $row_User['Nome'];?></span>
</p>

<h3 align="center">&nbsp;</h3>
<h3 align="center">&nbsp;</h3>
<h3 align="center">&nbsp;</h3>
<h3 align="center">Operazione completata con successo.</h3>
<p align="center" class="Stile1">&nbsp;</p>
<p align="center" class="Stile1 Stile2">ATTENDERE PREGO. </p>
</body>
</html>
<?php
mysql_free_result($User);
mysql_free_result($Registro);
mysql_free_result($Orario);
?>
