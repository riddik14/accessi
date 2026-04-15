<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
session_start();

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

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
?>
<?php 

// ********************************** Query selezione pranzi prenotati **************************************
mysql_select_db($database_MyPresenze, $MyPresenze);
$ParGG = date("Y-m-d");
$ParIDnome = $_SESSION['UserID'];
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Prenotazione_Pr = sprintf("SELECT pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.USR, pre_accessi.Pagamento 
						         FROM pre_accessi 
								 WHERE pre_accessi.PASTO=1 AND pre_accessi.GIORNO='$ParGG'AND pre_accessi.IDnome='$ParIDnome'");
$Prenotazione_Pr = mysql_query($query_Prenotazione_Pr, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Pr = mysql_fetch_assoc($Prenotazione_Pr);
$totalRows_Prenotazione_Pr = mysql_num_rows($Prenotazione_Pr);


// ********************************** Query selezione cene prenotate ($row_Prenotazione_Ce) **************************************
mysql_select_db($database_MyPresenze, $MyPresenze);
$ParGG = date("Y-m-d");
$ParIDnome = $_SESSION['UserID'];
$query_Prenotazione_Ce = "SELECT pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, pre_accessi.USR, pre_accessi.Pagamento
						  FROM pre_accessi WHERE pre_accessi.PASTO=2 AND pre_accessi.GIORNO='$ParGG'AND pre_accessi.IDnome='$ParIDnome'";
$Prenotazione_Ce = mysql_query($query_Prenotazione_Ce, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Ce = mysql_fetch_assoc($Prenotazione_Ce);
$totalRows_Prenotazione_Ce = mysql_num_rows($Prenotazione_Ce);

// ********************************** Query selezione colazioni prenotate ($row_Prenotazione_Col) pranzi prenotati **************************************
mysql_select_db($database_MyPresenze, $MyPresenze);
$ParIDnome = $_SESSION['UserID'];
$ParGG = date("Y-m-d");
$query_Prenotazione_Col = "SELECT pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, pre_accessi.USR, pre_accessi.Pagamento
						   FROM pre_accessi WHERE pre_accessi.PASTO=3 AND pre_accessi.GIORNO='$ParGG' AND pre_accessi.IDnome='$ParIDnome'";
$Prenotazione_Col = mysql_query($query_Prenotazione_Col, $MyPresenze) or die(mysql_error());
$row_Prenotazione_Col = mysql_fetch_assoc($Prenotazione_Col);
$totalRows_Prenotazione_Col = mysql_num_rows($Prenotazione_Col);


// ********************************* Seleziona l'utente in base alla variabile d'ambiente ***************************************
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_User = "SELECT pre_elenconomi.IDnome, pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.UO 
			   FROM pre_elenconomi, pre_gradi WHERE pre_elenconomi.IDnome='$ParIDnome' AND pre_gradi.ID=pre_elenconomi.IDgrado";
$User = mysql_query($query_User, $MyPresenze) or die(mysql_error());
$row_User = mysql_fetch_assoc($User);
$totalRows_User = mysql_num_rows($User);


// ************************************ Query $row_Registro di selezione degli orari registrati nella giornata ****************************
$parUser_Registro = "%";
if (isset($_SESSION['UserID'])) {
  $parUser_Registro = (get_magic_quotes_gpc()) ? $_SESSION['UserID'] : addslashes($_SESSION['UserID']);
}
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Registro = sprintf("SELECT pre_orari_gg.ID, pre_orari_gg.IDnome, date_format(pre_orari_gg.GIORNO,'%%d/%%m/%%Y') as GIORNO,
						 date_format(pre_orari_gg.ORA_IN,'%%H:%%i') as O_IN, date_format(pre_orari_gg.ORA_OUT,'%%H:%%i') AS O_OUT, 
						 timediff(`pre_orari_gg`.`ORA_OUT`,`pre_orari_gg`.`ORA_IN`) AS INTERVALLO, pre_orari_gg.CAUSA,pre_orari_gg.EU
						 FROM pre_orari_gg WHERE pre_orari_gg.IDnome='%s' AND pre_orari_gg.GIORNO='%s'", $parUser_Registro, $ParGG);
$Registro = mysql_query($query_Registro, $MyPresenze) or die(mysql_error());
$row_Registro = mysql_fetch_assoc($Registro);
$totalRows_Registro = mysql_num_rows($Registro);

// ************************************ Query di somma ($row_SomOrari) degli orari registrati nella giornata ****************************
mysql_select_db($database_MyPresenze, $MyPresenze);
$giorno = date("Y-m-d");
$parID_User = $_SESSION['UserID'];
$query_SomOrari = "SELECT pre_orari_gg.IDnome, pre_orari_gg.GIORNO,	sum(TIME_TO_SEC(TIMEDIFF(`pre_orari_gg`.`ORA_OUT`,`pre_orari_gg`.`ORA_IN` ))) AS TOT_ORARI, pre_orari_gg.CAUSA  
				   FROM pre_orari_gg 
				   GROUP BY pre_orari_gg.IDnome, pre_orari_gg.GIORNO
				   HAVING pre_orari_gg.IDnome='$parID_User' AND pre_orari_gg.GIORNO='$giorno' AND pre_orari_gg.CAUSA <>'SRD';";
$SomOrari = mysql_query($query_SomOrari, $MyPresenze) or die(mysql_error());
$row_SomOrari = mysql_fetch_assoc($SomOrari);
$totalRows_SomOrari = mysql_num_rows($SomOrari);

// ***************************** Selezione dell'orario ($row_Orario) standard di ingresso previsto per la giornata ****************************
mysql_select_db($database_MyPresenze, $MyPresenze);
$giorno = date("w");
$query_Orario = sprintf("SELECT pre_orari.N_GIORNO, pre_orari.GIORNO, pre_orari.ORA_OUT, pre_orari.ORA_IN, pre_orari.TIPO_ORARIO, 
						pre_elenconomi.ID_PERS_MTR, pre_orari.INTERVALLO, pre_orari.STRTIME
						FROM pre_orari, pre_elenconomi 
						WHERE pre_elenconomi.TipoOrario=pre_orari.TIPO_ORARIO AND pre_orari.N_GIORNO=$giorno");
$Orario = mysql_query($query_Orario, $MyPresenze) or die(mysql_error());
$row_Orario = mysql_fetch_assoc($Orario);
$totalRows_Orario = mysql_num_rows($Orario);
//*******************************************************************************************************************************
?>
<?php
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	if (isset($_POST['select']) && $_POST['select'] <> "A") {
  		$updateSQL = sprintf("UPDATE pre_orari_gg SET CAUSA=%s WHERE ID=%s",
                       GetSQLValueString($_POST['select'], "text"),
                       GetSQLValueString($_POST['IDreco'], "int"));

 		 mysql_select_db($database_MyPresenze, $MyPresenze);
 		 $Result1 = mysql_query($updateSQL, $MyPresenze) or die(mysql_error());
		 unset ($_SESSION['Recu']);
 		 header(sprintf("Location:LoginPresenze.php"));
	} else {
  		$_SESSION['Recu'] = 1;
	}
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
 <META http-equiv="refresh" content="3;URL=LoginPresenze.php"> 
<title>Conferma salvataggio</title>
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
.Stile4 {font-weight: bold; text-decoration: blink; font-family: Georgia, "Times New Roman", Times, serif; font-style: normal;}
.Stile5 {color: #333333}
.Stile6 {font-weight: bold; text-decoration: blink; font-family: Georgia, "Times New Roman", Times, serif; font-style: normal; color: #333333; }
.Stile7 {font-size: 12px; font-weight: bold; color: #333333; }
.Stile8 {
	color: #FFFFFF;
	font-size: 16px;
}
.Stile9 {font-weight: bold; text-decoration: blink; font-family: Georgia, "Times New Roman", Times, serif; font-style: normal; color: #FFFFFF; font-size: 16; }
.Stile10 {	color: #FF0000;
	font-weight: bold;
}
-->
</style>
</head>

<body background="images/Sfondo_vista.jpg" style="background-repeat:no-repeat;background-position:top center;">
<p align="center"><img src="images/BannerCealpi.jpg" width="759" height="89"></p>
<p align="center" class="Stile6">Utente: <?php echo $row_User['Grado']; ?> <?php echo $row_User['Cognome']; ?> <?php echo $row_User['Nome'];?>
</p>

<h3 align="center" class="Stile4 Stile5">Salvataggio eseguito</h3>
<table width="712" border="1" align="center">
  <tr class="Stile1">
    <td><div align="center" class="Stile6">E/U</div></td>
    <td><div align="center" class="Stile6">Giorno</div></td>
    <td><div align="center" class="Stile6">
      <div align="center" class="Stile6">Ingresso</div>
    </div></td>
    <td><div align="center" class="Stile6">Causale</div></td>
  </tr>
  <?php do { ?>
  <tr valign="middle" class="Stile1">
    <td height="30" nowrap><div align="center" class="Stile7">
      <div align="center" class="Stile6"><?php echo $row_Registro['EU']; ?></div></div>
    </td>
    <td nowrap><div align="center" class="Stile6"><?php echo $row_Registro['GIORNO']; ?></div></td>
    <td nowrap><span class="Stile6"><?php echo $row_Registro['O_IN']; ?></span></td>
    <td><div align="center" class="Stile6"><?php if (isset($row_Registro['CAUSA'])){ echo $row_Registro['CAUSA']; } else { ?> <form name="form1" method="POST" action="<?php echo $editFormAction; ?>">
			<span class="Stile10"><img src="images/attenzione.gif" width="25" height="20"></span>
			<select name="select">
			  <option value="A" selected>Selezionare</option>
              <option value="REC">RECUPERO COMPENSATIVO</option>
              <option value="SI1">SERVIZIO ISOLATO</option>
              <option value="RIP">RIPOSO MEDICO</option>
              <option value="RIT">RITARDO</option>
              <option value="TUR">TURNISTA</option>
            </select>           
		   <input type="submit" name="Submit" value="Salva">           
           <span class="Stile10"><img src="images/attenzione.gif" width="25" height="20"></span>
           <input name="IDreco" type="hidden" id="IDreco" value="<?php echo $row_Registro['ID']; ?>">
           <input type="hidden" name="MM_update" value="form1">
          <?php }; ?>
        </form> 
	      </div>
    </td>
  </tr>
  <?php } while ($row_Registro = mysql_fetch_assoc($Registro)); ?>
</table>
<h3 align="center" class="Stile6">Riepilogo</h3>
<table width="443" border="1" align="center">
  <tr class="Stile9">
    <td width="129" nowrap class="Stile6"><div align="center" class="Stile6">Ore previste </div></td>
    <td width="112" nowrap class="Stile6"><div align="center" class="Stile6">Ore svolte </div></td>
    <td width="180" nowrap class="Stile6"><div align="center" class="Stile6">Ore lavorate (+/-)</div></td>
  </tr>
  <tr>
    <td><div align="center"><span class="Stile6">
      <?php If ($row_Orario['STRTIME'] > 3600)	{ 
			  	$t_appo = $row_Orario['STRTIME']-3600;
				$Orario = strftime("%H:%M:%S",$t_appo);
			} else {
				$Orario = "00:".strftime("%M:%S",$row_Orario['STRTIME']);
			};
			 echo $Orario; ?>
	     </span></div></td>
    <td><div align="center"><span class="Stile6">
	  <?php If ($row_SomOrari['TOT_ORARI'] > 3600)	{ 
			  	$tot_appo = $row_SomOrari['TOT_ORARI']-3600;
				$TotOrario = strftime("%H:%M:%S",$tot_appo);
			} else {
				$TotOrario = "00:".strftime("%M:%S",$row_SomOrari['TOT_ORARI']);
			};
			 echo $TotOrario; ?>
         </span></div></td>
    <td><div align="center"><span class="Stile6">	
	  <?php
		 $differenza = $row_Orario['STRTIME'] - $row_SomOrari['TOT_ORARI'];
		    If ($differenza > 3600)	{ 
			  	$tot_dif = $differenza-3600;
				$TotDiff = strftime("%H:%M:%S",$tot_dif);
			} else {
				$TotDiff = "00:".strftime("%M:%S", $differenza);
			};
		if ($row_Orario['STRTIME'] < $row_SomOrari['TOT_ORARI']) { 
			echo "+ " .$TotDiff; 
		} else { 
			echo "- ". $TotDiff;
		} ?>
	
	</div></td>
  </tr>
</table>
<h3 align="center" class="Stile6">Pasti prenotati </h3>
<table width="299" border="1" align="center">
  <tr>
    <th width="76" class="Stile6" scope="col">Pranzo</th>
    <th width="90" class="Stile6" scope="col">Cena</th>
    <th width="111" class="Stile6" scope="col">Colazione</th>
  </tr>
  <tr>
    <td>      <div align="center"><span class="Stile6">
        <?php if ($row_Prenotazione_Pr['PASTO'] == 1) { ?>
      </span><img src="images/spunta.gif" width="25" height="20">
            <?php } ;?>
      </div></td>
    <td>      <div align="center"><span class="Stile6">
        <?php if ($row_Prenotazione_Ce['PASTO'] == 2) { ?>
      </span><img src="images/spunta.gif" width="25" height="20">      <?php } ;?>      
    </div></td>
    <td>      <div align="center"><span class="Stile6">
        <?php if ($row_Prenotazione_Col['PASTO'] == 3) { ?>
      </span><img src="images/spunta.gif" width="25" height="20">
            <?php } ;?>
      </div></td>
  </tr>
  <tr>
    <td><div align="center" class="Stile5">
      <span class="Stile4">
      <?php if ($row_Prenotazione_Pr['Pagamento'] == 1) { ?>
      </span>
      <div align="center" class="Stile4"><span class="Stile6">a pagamento</span>          <?php } ;?>
      </div>
    </div></td>
    <td><div align="center" class="Stile5">
      <span class="Stile4">
      <?php if ($row_Prenotazione_Ce['Pagamento'] == 1) { ?>
      </span>
      <div align="center" class="Stile4"><span class="Stile6">a pagamento</span>          <?php } ;?>
      </div>
    </div></td>
    <td><div align="center" class="Stile5">
      <span class="Stile4">
      <?php if ($row_Prenotazione_Col['Pagamento'] == 1) { ?>
      </span>
      <div align="center" class="Stile4"><span class="Stile6">a pagamento 
          </span>
        <?php } ; ?>
      </div>
    </div></td>
  </tr>
</table>
<?php
if ($_SESSION['Recu'] == 1){
		unset ($_SESSION['Recu']);
 		echo "<script type=\"text/javascript\">alert(\"L'orario registrato è diverso da quello sprevisto per la giornata odierna. Selezionare la causale per l'ingresso posticipato o l'uscita anticipata.\");</script>";
	}; ?>
</body>
</html>
<?php
mysql_free_result($User);
mysql_free_result($Registro);
?>
