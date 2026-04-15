<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

session_start();
if (!($_SESSION['UserID'])) {
  unset($_SESSION['UserID']);
  header("Location: LoginPresenze.php");
}

$Tx_cert = $_SESSION['UserID'];

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Nomi = "SELECT pre_nomiview.Grado, pre_nomiview.Cognome, pre_nomiview.Nome, pre_nomiview.IDnome FROM pre_nomiview WHERE pre_nomiview.ID_USERNAME='$Tx_cert' ORDER BY pre_nomiview.Cognome";
$Nomi = mysql_query($query_Nomi, $MyPresenze) or die(mysql_error());
$row_Nomi = mysql_fetch_assoc($Nomi);
$totalRows_Nomi = mysql_num_rows($Nomi);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Utente = "SELECT pre_elenconomi.IDnome, pre_elenconomi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.Foto, pre_elenconomi.Cte FROM pre_elenconomi WHERE pre_elenconomi.IDnome='$Tx_cert'";
$Utente = mysql_query($query_Utente, $MyPresenze) or die(mysql_error());
$row_Utente = mysql_fetch_assoc($Utente);
$totalRows_Utente = mysql_num_rows($Utente);

// Imposta le variabili d'ambiente relative a IDNAME (utente selezionato), mes (mese selezionato) e an (anno selezionato) nel form1
if (isset($_POST['IDnome'])) {
	$parIDpers = $_POST['IDnome'];
	$_SESSION['IDNAME'] = $_POST['IDnome'];
}

if (isset($_POST['mese'])) {
  $parMese = addslashes($_POST['mese']);
  $_SESSION['mes'] = $_POST['mese'];
}
$parAnno = "%";
if (isset($_POST['anno'])) {
  $parAnno = addslashes($_POST['anno']);
   $_SESSION['an']=$_POST['anno'];
}

// *************************** Esegue validazione dei record selezionati ***************************************

if ((isset($_POST['MM_form2'])) && ($_POST['Valida'] == "Validazione giorni selezionati")) {
		mysql_select_db($database_MyPresenze, $MyPresenze);
		$checked = $_POST['checkbox']; 
		$count = count($checked); 
		for($i=0; $i < $count; $i++) 
		{ 
			$query = sprintf("UPDATE pre_orari_gg SET VAL_DA=%s WHERE ID=%s",
                       GetSQLValueString($Tx_cert, "int"),
                       GetSQLValueString($checked[$i], "int")); 
			//echo $checked[$i];
			//echo $i;
			mysql_query($query); 
		} 
} 

// *************************** Esegue validazione dei record selezionati ***************************************

if ((isset($_POST['MM_form2'])) && ($_POST['Svalida'] == "Svalidazione giorni selezionati")) {
		mysql_select_db($database_MyPresenze, $MyPresenze);
		$checked = $_POST['checkbox']; 
		$count = count($checked); 
		for($i=0; $i < $count; $i++) 
		{ 
			$query = sprintf("UPDATE pre_orari_gg SET VAL_DA=%s WHERE ID=%s",
                       GetSQLValueString(null, "int"),
                       GetSQLValueString($checked[$i], "int")); 
			//echo $checked[$i];
			//echo $i;
			mysql_query($query); 
		} 
}



mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Giorno = "SELECT date_format(pre_orari_gg.GIORNO,'%d/%m/%Y') AS GIORNO, pre_orari_gg.GIORNO AS GIO,
				 pre_orari_gg.ORA_IN AS ENTRATA, pre_orari_gg.ORA_OUT AS USCITA, dayofweek(pre_orari_gg.GIORNO) AS GioSet,
				 timediff(`pre_orari_gg`.`ORA_OUT`,`pre_orari_gg`.`ORA_IN`) AS PERIODO, pre_orari_gg.IDnome,
				 pre_orari_gg.CAUSA AS CAUSALE, pre_giornisett.WEEKDAY, pre_orari_gg.GIORNO as GIO, pre_orari_gg.ID, pre_orari_gg.VAL_DA,
				 pre_elenconomi.Cognome AS Validatore, pre_orari_gg.NOTE 
				 FROM (pre_orari_gg LEFT JOIN pre_elenconomi ON pre_orari_gg.VAL_DA=pre_elenconomi.IDnome), pre_giornisett
				 WHERE pre_orari_gg.IDnome='$parIDpers' AND year(pre_orari_gg.GIORNO)='$parAnno' AND 
				 month(pre_orari_gg.GIORNO)='$parMese' AND dayofweek(pre_orari_gg.GIORNO)=pre_giornisett.ID
				 ORDER BY pre_orari_gg.GIORNO, pre_orari_gg.ORA_IN";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE pre_orari_gg SET VAL_DA=%s WHERE ID=%s",
                       GetSQLValueString($_POST['IDuser'], "int"),
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($updateSQL, $MyPresenze) or die(mysql_error());
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE pre_orari_gg SET VAL_DA=%s WHERE ID=%s",
                       GetSQLValueString($Tx_cert, "int"),
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($updateSQL, $MyPresenze) or die(mysql_error());
  // header("Location: SitPrenotazione.php");
}
//Record set User - seleziona l'utente in base alla variabile d'ambiente MM_UserGroup
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>VALIDAZIONE ingressi e uscite</title>
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
.Stile11 {font-size: small; font-weight: bold; }
.Stile12 {font-size: 10px}
a:link {
	color: #FFFFFF;
}
a:visited {
	color: #999999;
}
-->
</style>
</head>

<body>
<table width="200" align="center">
  <tr>
    <td><img name="Foto" src="<?php echo $row_Utente['Foto']; ?>" width="56" height="76" alt=""></td>
    <td><img src="images/BannerCealpi.jpg" width="600" height="75"></td>
    <td><a href="RegistroPresenze.php"><img src="images/xbox360power.jpg" name="Esci" width="75" height="75" border="0" id="Esci"></a></td>
  </tr>
  <tr>
    <td height="21" colspan="2"><span class="Stile12">UTENTE:   <?php echo $row_Utente['Grado']; ?> <?php echo $row_Utente['Nome']; ?> <?php echo $row_Utente['Cognome']; ?>  </span></td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="859" border="2" align="center">
  <tr align="center">
    <td width="847"><form name="form1" method="post" action="Validazione.php">
      <div align="center">
        <p>Selezione amministrato
          <select name="IDnome" id="IDnome">
              <?php
				do {  
			?>
              <option value="<?php echo $row_Nomi['IDnome']?>" <?php if ($row_Nomi['IDnome']==$_SESSION['IDNAME']) {echo "SELECTED";} ?>><?php echo $row_Nomi['Cognome']." ".$row_Nomi['Nome']." ". $row_Nomi['Grado'] ?></option>
              <?php
				} while ($row_Nomi = mysql_fetch_assoc($Nomi));
 				 $rows = mysql_num_rows($Nomi);
 				 if($rows > 0) {
     				 mysql_data_seek($Nomi, 0);
	 				$row_Nomi = mysql_fetch_assoc($Nomi);
	  			}
			?>
          </select>
          mese<span class="Stile6">
          <select name="mese" class="Spulsanti" id="mese">
              <option value="01" <?php if ($_SESSION['mes']==1) {echo "SELECTED";} ?>>gennaio</option>
              <option value="02" <?php if ($_SESSION['mes']==2) {echo "SELECTED";} ?>>febbraio</option>
              <option value="03" <?php if ($_SESSION['mes']==3) {echo "SELECTED";} ?>>marzo</option>
              <option value="04" <?php if ($_SESSION['mes']==4) {echo "SELECTED";} ?>>aprile</option>
              <option value="05" <?php if ($_SESSION['mes']==5) {echo "SELECTED";} ?>>maggio</option>
              <option value="06" <?php if ($_SESSION['mes']==6) {echo "SELECTED";} ?>>giugno</option>
              <option value="07" <?php if ($_SESSION['mes']==7) {echo "SELECTED";} ?>>luglio</option>
              <option value="08" <?php if ($_SESSION['mes']==8) {echo "SELECTED";} ?>>agosto</option>
              <option value="09" <?php if ($_SESSION['mes']==9) {echo "SELECTED";} ?>>settembre</option>
              <option value="10" <?php if ($_SESSION['mes']==10) {echo "SELECTED";} ?>>ottobre</option>
              <option value="11" <?php if ($_SESSION['mes']==11) {echo "SELECTED";} ?>>novembre</option>
              <option value="12" <?php if ($_SESSION['mes']==12) {echo "SELECTED";} ?>>dicembre</option>
          </select>
          </span>anno
            <input name="anno" type="text" id="anno" value="<?php echo date("Y"); ?>" size="6" maxlength="4">

            <input type="submit" name="Submit" value="Visualizza">
</p>
        </div>
    </form></td>
  </tr>
  <tr align="center">
    <td height="193"><?php if ($totalRows_Giorno > 0) { // Show if recordset not empty ?>
      <form name="form2" method="post" action="">
        <table width="924" border="1" align="center">
          <tr align="center" valign="middle" class="Stile9">
            <td width="218" height="19"><div align="center" class="Stile11">
                <div align="center">Giorno</div>
            </div></td>
            <td width="114"><div align="center" class="Stile11">
                <div align="center">ingresso </div>
            </div></td>
            <td width="100"><div align="center" class="Stile11">
                <div align="center">uscita</div>
            </div></td>
            <td width="107" class="Stile6"><div align="center"><strong>intervallo</strong></div></td>
            <td width="41"><span class="Stile11">causale</span></td>
            <td width="25" valign="middle" class="Stile11">NOTE</td>
            <td width="25" valign="middle" class="Stile11">Sel</td>
            <td width="105" valign="middle" class="Stile11">validato</td>
            <td width="131" valign="middle"><div align="center" class="Stile11">Modifica</div></td>
          </tr>
          <?php do { ?>
          <tr align="center" class="Stile6">
            <td height="22" nowrap class="Stile6"><div align="center"><?php echo $row_Giorno['WEEKDAY']; ?> <?php echo $row_Giorno['GIORNO']; ?></div></td>
            <td nowrap class="Stile6"><div align="center"><?php echo $row_Giorno['ENTRATA']; ?></div></td>
            <td nowrap class="Stile6"><div align="center"><?php echo $row_Giorno['USCITA']; ?></div></td>
            <td nowrap class="Stile6"><div align="center"><?php echo $row_Giorno['PERIODO']; ?></div></td>
            <?php if ($row_Giorno['CAUSALE'] == "COL") { 
        	$Class = "ORE";
			$CAU = $row_Giorno['CAUSALE']; 
		} else {
			$Class = "Stile6";
			$CAU = $row_Giorno['CAUSALE']; 
		}; ?>
            <td nowrap class="<?php echo $Class; ?>"><?php echo $CAU; ?>
                <div align="center"></div></td>
            <td valign="middle" nowrap class="Stile6"><?php echo $row_Giorno['NOTE']; ?></td>
            <td valign="middle" nowrap class="Stile6"><div align="left" class="Stile6">
              <div align="center"><p>
                <?php if ($row_Giorno['VAL_DA'] == "" or $row_Giorno['VAL_DA']== $row_Utente['IDnome']) { ?>
					<input type="checkbox" name="checkbox[]" value="<?php echo $row_Giorno['ID']; ?>">
				<?php }; ?>
                    </p>
              </div>
            </div></td>
            <td valign="middle" nowrap class="Stile6"><div align="center"><?php if (isset($row_Giorno['VAL_DA'])) { ?>
     					 <div align="center"><?php echo $row_Giorno['Validatore']; ?>
          						<?php } ;?></div></td>
            <td width="131" valign="middle" nowrap class="Stile9"><div align="center"><?php if (!isset($row_Giorno['VAL_DA'])) { ?>
     					 <a href="DettModificaGiorno.php?giorno=<?php echo $row_Giorno['GIO']; ?>&IDnome=<?php echo $row_Giorno['IDnome']; ?>" class="Stile11">Modifica/inserisci</a>
          						<?php } ;?> </div></td>
          </tr>
          <?php } while ($row_Giorno = mysql_fetch_assoc($Giorno)); ?>
        </table>
        <p>
          <input name="MM_form2" type="hidden" id="MM_form2" value="form2">
          <input name="Valida" type="submit" id="Valida" value="Validazione giorni selezionati">
           <input name="Svalida" type="submit" id="Svalida" value="Svalidazione giorni selezionati">
           <input name="IDnome" type="hidden" id="IDnome" value="<?php echo $_SESSION['IDNAME']; ?>">
          <input name="mese" type="hidden" id="mese" value="<?php echo $_SESSION['mes']; ?>">
          <input name="anno" type="hidden" id="anno" value="<?php echo $_SESSION['an']; ?>">
</p>
    </form>      <?php }; // Show if recordset not empty ?>      <span class="Stile6">	</span></td>
  </tr>
</table>



</body>
</html>
<?php
mysql_free_result($Utente);

mysql_free_result($Giorno);
?>
