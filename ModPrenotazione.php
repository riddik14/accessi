<?php require_once('../Connections/MyPresenze.php'); ?>
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE pre_accessi SET PASTO=%s, Ti_R=%s, Se=%s WHERE IDrecord=%s",
                       GetSQLValueString($_POST['PASTO'], "int"),
                       GetSQLValueString($_POST['TiRazione'], "int"),
                       GetSQLValueString($_POST['Sede'], "int"),
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($updateSQL, $MyPresenze) or die(mysql_error());
   header("Location: SitPrenotazione.php");
}

if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "form2")) {
  $deleteSQL = sprintf("DELETE FROM pre_accessi WHERE IDrecord=%s",
                       GetSQLValueString($_POST['IDrecord'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($deleteSQL, $MyPresenze) or die(mysql_error());
    header("Location: SitPrenotazione.php");
}


$parID_Nomi = "%";
if (isset($_POST['IDrecord'])) {
  $parID_Nomi = addslashes($_POST['IDrecord']);
}
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Nomi = sprintf("SELECT pre_elenconomi.IDnome, pre_elenconomi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.UO, 
						pre_elenconomi.TipoOrario, pre_uo.DEN_UN_OPER, pre_elenconomi.Forza, pre_elenconomi.TipoRazione, pre_elenconomi.ModoSomm, 
						pre_elenconomi.SedeSomm, pre_elenconomi.Categoria FROM pre_elenconomi, pre_uo WHERE pre_uo.ID_UO=pre_elenconomi.UO AND 
						pre_elenconomi.IDnome='%s'", $parID_Nomi);
$Nomi = mysql_query($query_Nomi, $MyPresenze) or die(mysql_error());
$row_Nomi = mysql_fetch_assoc($Nomi);
$totalRows_Nomi = mysql_num_rows($Nomi);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_TipoRazioni = "SELECT pre_tiporazione.ID, pre_tiporazione.TipoRazione FROM pre_tiporazione";
$TipoRazioni = mysql_query($query_TipoRazioni, $MyPresenze) or die(mysql_error());
$row_TipoRazioni = mysql_fetch_assoc($TipoRazioni);
$totalRows_TipoRazioni = mysql_num_rows($TipoRazioni);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Categorie = "SELECT pre_categorie.IDcat, pre_categorie.Categoria FROM pre_categorie";
$Categorie = mysql_query($query_Categorie, $MyPresenze) or die(mysql_error());
$row_Categorie = mysql_fetch_assoc($Categorie);
$totalRows_Categorie = mysql_num_rows($Categorie);

$maxRows_Prenotazioni = 10;
$pageNum_Prenotazioni = 0;
if (isset($_GET['pageNum_Prenotazioni'])) {
  $pageNum_Prenotazioni = $_GET['pageNum_Prenotazioni'];
}
$startRow_Prenotazioni = $pageNum_Prenotazioni * $maxRows_Prenotazioni;

$parIDnome_Prenotazioni = "%";
if (isset($_POST['IDnome'])) {
  $parIDnome_Prenotazioni = addslashes($_POST['IDnome']);
}
$parGG_Prenotazioni = "%";
if (isset($_POST['gg'])) {
  $parGG_Prenotazioni = addslashes($_POST['gg']);
}
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Prenotazioni = sprintf("SELECT pre_accessi.IDrecord, pre_accessi.IDnome, date_format(pre_accessi.GIORNO ,'%%d/%%m/%%Y') as GIORNO, pre_accessi.PASTO, pre_accessi.Ti_R, pre_accessi.Se, pre_accessi.Cons, pre_elenconomi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome FROM pre_accessi, pre_elenconomi WHERE pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_accessi.IDnome='%s' AND pre_accessi.GIORNO='%s'", $parIDnome_Prenotazioni,$parGG_Prenotazioni);
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

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Sede = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sede = mysql_query($query_Sede, $MyPresenze) or die(mysql_error());
$row_Sede = mysql_fetch_assoc($Sede);
$totalRows_Sede = mysql_num_rows($Sede);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Documento senza titolo</title>
<style type="text/css">
<!--
body {
	background-color: #3366CC;
}
.Stile4 {color: #FFFFFF}
.Stile5 {font-size: x-small}
.Stile6 {color: #FFFFFF; font-size: x-small; }
.Stile7 {font-size: small}
.Stile8 {color: #FFFFFF; font-size: small; }
-->
</style></head>

<body>
<div align="center">
  <p>&nbsp;  </p>
  <p class="Stile4">Modifica prenotazione pasti per il giorno <?php echo $row_Prenotazioni['GIORNO']; ?> relative al <?php echo $row_Prenotazioni['Grado']; ?> <?php echo $row_Prenotazioni['Cognome']; ?> <?php echo $row_Prenotazioni['Nome']; ?> <span class="Stile5"><a href="SitPrenotazione.php"><img src="../Immagini/xbox360power.jpg" width="106" height="101" border="0"></a></span></p>
  <p class="Stile4">&nbsp;</p>
  <?php do { ?>
  <table width="949" border="1">
    <tr>
      <th width="747" height="41" scope="col"><form name="form1" method="post" action="">
        <p><span class="Stile8">Pasto</span>            <select name="PASTO" id="PASTO">
              <option value="1" <?php if (!(strcmp(1, $row_Prenotazioni['PASTO']))) {echo "SELECTED";} ?>>PRANZO</option>
              <option value="2" <?php if (!(strcmp(2, $row_Prenotazioni['PASTO']))) {echo "SELECTED";} ?>>CENA</option>
              <option value="3" <?php if (!(strcmp(3, $row_Prenotazioni['PASTO']))) {echo "SELECTED";} ?>>COLAZIONE</option>
            </select> 
            <span class="Stile8">tipo razione
            <select name="TiRazione" id="TiRazione">
              <?php
do {  
?>
              <option value="<?php echo $row_TipoRazioni['ID']?>"<?php if (!(strcmp($row_TipoRazioni['ID'], $row_Prenotazioni['Ti_R']))) {echo "SELECTED";} ?>><?php echo $row_TipoRazioni['TipoRazione']?></option>
              <?php
} while ($row_TipoRazioni = mysql_fetch_assoc($TipoRazioni));
  $rows = mysql_num_rows($TipoRazioni);
  if($rows > 0) {
      mysql_data_seek($TipoRazioni, 0);
	  $row_TipoRazioni = mysql_fetch_assoc($TipoRazioni);
  }
?>
            </select>
</span><span class="Stile8">sede
            <select name="Sede" id="Sede">
              <?php
do {  
?>
              <option value="<?php echo $row_Sede['IDsede']?>"<?php if (!(strcmp($row_Sede['IDsede'], $row_Prenotazioni['Se']))) {echo "SELECTED";} ?>><?php echo $row_Sede['SEDE']?></option>
              <?php
} while ($row_Sede = mysql_fetch_assoc($Sede));
  $rows = mysql_num_rows($Sede);
  if($rows > 0) {
      mysql_data_seek($Sede, 0);
	  $row_Sede = mysql_fetch_assoc($Sede);
  }
?>
            </select>
            </span>            <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Prenotazioni['IDrecord']; ?>">
            <input type="hidden" name="MM_update" value="form1">

          <input type="submit" name="Submit" value="Salva">
          </p>
      </form></th>
      <th width="186" scope="col"><form name="form2" method="post" action="">
        <input type="submit" name="Submit" value="Cancella prenotazione">
        <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Prenotazioni['IDrecord']; ?>">
        <input name="MM_delete" type="hidden" id="MM_delete" value="form2">
      </form></th>
    </tr>
  </table>
  <?php } while ($row_Prenotazioni = mysql_fetch_assoc($Prenotazioni)); ?>
  <p class="Stile4">&nbsp;</p>
  <p class="Stile4">&nbsp; </p>
</div>
</body>
</html>
<?php
mysql_free_result($Nomi);
mysql_free_result($TipoRazioni);
mysql_free_result($Categorie);
mysql_free_result($Prenotazioni);
mysql_free_result($Sede);
?>
