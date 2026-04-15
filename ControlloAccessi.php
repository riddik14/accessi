<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
//initialize the session
session_start();

if (isset($_GET['UO'])) { 
	$_SESSION['UO'] = $_GET['UO'];
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  session_unregister('MM_Username');
  session_unregister('MM_UserGroup');
	
  $logoutGoTo = "../Home.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

function dec2time($dectime) {
	list($hours, $min) =  sscanf($dectime, '%d,%d');
	if ($min < 9) { $min =$min*10; };
	return str_pad($hours, 2, "0", STR_PAD_LEFT). ":" . str_pad($min, 2, "0", STR_PAD_LEFT);
}

?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

$parGiorno_Giorno = "%";
if (isset($_POST['SelectGiorno'])) {
  $parGiorno_Giorno = (get_magic_quotes_gpc()) ? $_POST['SelectGiorno'] : addslashes($_POST['SelectGiorno']);
  $_SESSION['GIORNO'] = $parGiorno_Giorno;
}
$parPasto = $_POST['Pasto'];
mysql_select_db($database_MyPresenze, $MyPresenze);
 mysql_query("set names 'utf8'");
$query_Giorno = "SELECT pre_gradi.Grado, pre_accessi.PASTO, pre_accessi.Ora_pren, pre_accessi.IDnome, pre_elenconomi.Cognome, pre_elenconomi.Nome,
				 pre_elenconomi.CF, pre_sedi.SEDE 
				 FROM pre_accessi, pre_elenconomi, pre_gradi, pre_sedi 
				 WHERE pre_gradi.ID=pre_elenconomi.IDgrado AND pre_accessi.Ora_cons_pr <>'' AND pre_elenconomi.IDnome=pre_accessi.IDnome AND 
				 pre_accessi.GIORNO = '$parGiorno_Giorno' AND PASTO=1 AND pre_sedi.IDsede=pre_accessi.Se AND pre_elenconomi.Categoria <> 3 AND 
				 pre_elenconomi.Categoria <> 5 
				 ORDER BY SEDE, Cognome";
$Giorno = mysql_query($query_Giorno, $MyPresenze) or die(mysql_error());
$row_Giorno = mysql_fetch_assoc($Giorno);
$totalRows_Giorno = mysql_num_rows($Giorno);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_SelectGiorno = "SELECT pre_accessi.GIORNO, date_format(GIORNO, '%d-%m-%Y') as GIO FROM pre_accessi GROUP BY pre_accessi.GIORNO ORDER BY pre_accessi.GIORNO DESC";
$SelectGiorno = mysql_query($query_SelectGiorno, $MyPresenze) or die(mysql_error());
$row_SelectGiorno = mysql_fetch_assoc($SelectGiorno);
$totalRows_SelectGiorno = mysql_num_rows($SelectGiorno);

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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Gestione anagrafiche</title>
<style type="text/css">
<!--
body {
	background-color: #3366CC;
}
.Stile4 {color: #FFFFFF}
.Stile8 {font-size: small; }
a:link {
	color: #FFFFFF;
}
a:visited {
	color: #CCCCCC;
}
a:hover {
	color: #999999;
}
a:active {
	color: #FFFFFF;
}
-->
</style></head>

<body>
<div align="center">
  <p><img src="../images/BannerCealpi.jpg" width="600" height="75">
    <a href="Master.php"><img src="images/xbox360power.jpg" width="75" height="75" border="0"></a></p>
  <form name="form2" method="post" action="">
    <span class="Stile4">Giorno
    <select name="SelectGiorno" id="SelectGiorno">
      <?php
do {  
?>
      <option value="<?php echo $row_SelectGiorno['GIORNO']?>"><?php echo $row_SelectGiorno['GIO']?></option>
      <?php
} while ($row_SelectGiorno = mysql_fetch_assoc($SelectGiorno));
  $rows = mysql_num_rows($SelectGiorno);
  if($rows > 0) {
      mysql_data_seek($SelectGiorno, 0);
	  $row_SelectGiorno = mysql_fetch_assoc($SelectGiorno);
  }
?>
    </select>
    </span> 
    <input type="submit" name="Submit" value="Carica dati">
  </form>

  <?php if ($totalRows_Giorno> 0 ) { ?>
  <span class="Stile4">Elenco pranzi prenotati per la giornata e  ingressi con CMD registrati - Numero  <?php echo $totalRows_Giorno; ?>
  </span>    
    <table width="569" border="1" bgcolor="#FFFFFF">
      <tr>
        <th colspan="5" scope="col">Dati consumazione pasto </th>
        <th colspan="2" scope="col">Orari CMD </th>
      </tr>
      <tr class="Stile8">
        <th scope="col"><span class="Stile8">Grado</span></th>
        <th scope="col"><span class="Stile8">Cognome</span></th>
        <th scope="col"><span class="Stile8">Nome</span></th>
        <th scope="col">ora prenotazione pranzo </th>
        <th scope="col">sede</th>
        <th scope="col"><div align="center" class="Stile8">E/U</div></th>
        <th scope="col"><span class="Stile8">ora </span>
            <div align="center" class="Stile8">passaggio CMD </div></th>
      </tr>
      <?php do { 
	    $parData = date("d-m-Y", strtotime($_POST['SelectGiorno']));
	  	$parIDpers = $row_Giorno['CF'];
	 	$connect = odbc_connect("SIGE", "SIEIMP", "SIE");
		$query = "Select SIEIMP.ANAGPERS_MTR.COD_FISCALE, SIEIMP.BADGE_SIGE.DATA, To_Char(SIEIMP.BADGE_SIGE.DATA, 'HH24:MI'), SIEIMP.BADGE_SIGE.MOVIMENTO
				From SIEIMP.ANAGPERS_MTR Left Join SIEIMP.BADGE_SIGE On SIEIMP.ANAGPERS_MTR.ID_PERS_MTR = SIEIMP.BADGE_SIGE.ID_PERS_MTR
				Where SIEIMP.ANAGPERS_MTR.COD_FISCALE = '$parIDpers' And To_Char(SIEIMP.BADGE_SIGE.DATA, 'DD-MM-YYYY') = '$parData'";
    	$result = odbc_exec($connect, $query);
	    # fetch the data from the database
		?>
      <tr>
        <td nowrap><div align="left"><span class="Stile8"><?php echo $row_Giorno['Grado']; ?></span></div></td>
        <td nowrap><div align="left"><span class="Stile8"><?php echo $row_Giorno['Cognome']; ?></span></div></td>
        <td nowrap><div align="left"><span class="Stile8"><?php echo $row_Giorno['Nome']; ?></span></div></td>
        <td nowrap><span class="Stile8"><?php echo $row_Giorno['Ora_pren']; ?></span></td>
        <td nowrap><div align="left"><span class="Stile8"><?php echo $row_Giorno['SEDE']; ?></span></div></td>
        <td nowrap><span class="Stile8">
          <?php if(odbc_result($result, 4)=="E") { echo "ENTRATA";}
				else if (odbc_result($result, 4)=="U") { echo "USCITA";}
				else if (odbc_result($result, 4)=="") { ?>
          <img src="images/attenzione.gif" width="30" height="30">
          <?php } ?>
        </span></td>
        <td colspan="2" nowrap><span class="Stile8"> </span>
            <div align="center" class="Stile8"> <?php echo (odbc_result($result, 3));  ?> </div>
            <span class="Stile8"> </span>
            <div align="center" class="Stile8"></div>
            <span class="Stile8"> </span> </td>
      </tr>
      <?php 
	  odbc_close($connect);
	  } while ($row_Giorno = mysql_fetch_assoc($Giorno)); ?>
    </table>
    <?php } ?>
</div>
</body>
</html>
<?php
mysql_free_result($Giorno);

mysql_free_result($SelectGiorno);
?>
