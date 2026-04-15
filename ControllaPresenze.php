<?php require_once('../Connections/MyPresenze.php'); 
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

$currentPage = $_SERVER["PHP_SELF"];

$parGiorno_Giorno = "%";
if (isset($_POST['SelectGiorno'])) {
  $parGiorno_Giorno = (get_magic_quotes_gpc()) ? $_POST['SelectGiorno'] : addslashes($_POST['SelectGiorno']);
  $_SESSION['GIORNO'] = $parGiorno_Giorno;
}
$parPasto = $_POST['Pasto'];
mysql_select_db($database_MyPresenze, $MyPresenze);
 mysql_query("set names 'utf8'");
$query_Giorno = "SELECT pre_gradi.Grado, pre_accessi_bk.PASTO, pre_accessi_bk.Ora_cons_pr, pre_accessi_bk.Pagamento,
				 pre_accessi_bk.IDnome, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.ID_PERS_MTR, pre_sedi.SEDE 
				 FROM pre_accessi_bk, pre_elenconomi, pre_gradi, pre_sedi WHERE pre_gradi.ID=pre_elenconomi.IDgrado AND 
				 pre_accessi_bk.Ora_cons_pr <>'' AND pre_elenconomi.IDnome=pre_accessi_bk.IDnome AND 
				 pre_accessi_bk.GIORNO = '$parGiorno_Giorno' AND PASTO='$parPasto' AND pre_elenconomi.Categoria<>3 AND pre_sedi.IDsede=pre_accessi_bk.Se 
				 ORDER BY Cognome";
$Giorno = mysql_query($query_Giorno, $MyPresenze) or die(mysql_error());
$row_Giorno = mysql_fetch_assoc($Giorno);
$totalRows_Giorno = mysql_num_rows($Giorno);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_SelectGiorno = "SELECT pre_accessi_bk.GIORNO, date_format(GIORNO, '%d-%m-%Y') as GIO FROM pre_accessi_bk GROUP BY pre_accessi_bk.GIORNO ORDER BY pre_accessi_bk.GIORNO DESC";
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
    pasto 
    <select name="Pasto" id="Pasto">
      <option value="3">Colazione</option>
      <option value="1">Pranzo</option>
      <option value="2">Cena</option>
    </select> 
    </span> 
    <input type="submit" name="Submit" value="Elenco consumazioni">
  </form>

  <?php if ($totalRows_Giorno> 0 ) { ?>
    <span class="Stile4">Elenco consumazioni - Numero  <?php echo $totalRows_Giorno; ?>
    </span>    
    <table width="569" border="1" bgcolor="#FFFFFF">
      <tr>
        <th colspan="7" scope="col">Dati consumazione pasto </th>
        <th colspan="4" scope="col">presenze rilevate </th>
      </tr>
      <tr>
        <th scope="col"><span class="Stile8">Grado</span></th>
        <th scope="col"><span class="Stile8">Cognome</span></th>
        <th scope="col"><span class="Stile8">Nome</span></th>
        <th scope="col">ora consumazione </th>
        <th scope="col">sede</th>
        <th scope="col"><span class="Stile8">pasto</span></th>
        <th scope="col"><span class="Stile8">a pagamento </span></th>
        <th scope="col"><div align="center" class="Stile8">ora ingresso </div></th>
        <th scope="col"><span class="Stile8">ora uscita</span></th>
        <th scope="col"><div align="center" class="Stile8">causale</div></th>
      <th scope="col">&nbsp;</th>
      </tr>
      <?php do { 
	    $parData = date("d-m-Y", strtotime($_POST['SelectGiorno']));
	  	$parIDpers = $row_Giorno['ID_PERS_MTR'];
	 	$connect = odbc_connect("SIGE", "SIEIMP", "SIE");
		$query = "SELECT ORA_INGRESSO_DETT, ORA_USCITA_DETT, COD_ATT_STR FROM SIEIMP.PRES_HH_ANAG WHERE GIORNO =  TO_DATE('$parData', 'DD-MM-YYYY') AND ID_PERS_MTR='$parIDpers'";
    	$result = odbc_exec($connect, $query);
	    # fetch the data from the database
		?>
      <tr>
        <td><span class="Stile8"><?php echo $row_Giorno['Grado']; ?></span></td>
        <td><span class="Stile8"><?php echo $row_Giorno['Cognome']; ?></span></td>
        <td><span class="Stile8"><?php echo $row_Giorno['Nome']; ?></span></td>
        <td nowrap><span class="Stile8"><?php echo $row_Giorno['Ora_cons_pr']; ?></span></td>
        <td nowrap><span class="Stile8">
        <?php echo $row_Giorno['SEDE']; ?>        </span></td>
        <td><span class="Stile8"><?php if ($row_Giorno['PASTO']==1) { echo "Pranzo"; } else if ($row_Giorno['PASTO']== 2) { echo "Cena"; } else { echo "Colazione"; }?></span></td>
        <td><span class="Stile8"><?php if($row_Giorno['Pagamento'] == 0){ echo "No"; } else {echo "Si";}; ?></span></td>
        <td nowrap><span class="Stile8">
        <?php
		 echo dec2time(odbc_result($result, 1));  ?>
        </span>
        <div align="center" class="Stile8"></div></td>
        <td nowrap><span class="Stile8"><?php echo dec2time(odbc_result($result, 2)); ?></span></td>
        <td nowrap>
        <div align="center" class="Stile8"><?php if(odbc_result($result, 3)=="CRD") { echo "C.do e Log.";}
												 else if (odbc_result($result, 3)=="SI1") { echo "Serv.isolato";}
												 else if (odbc_result($result, 3)=="SRD") { echo "Serv.di caserma";}
												 ?></div></td>
      <td nowrap><?php  $difora =(strtotime($row_Giorno['GIORNO']. " " . dec2time(odbc_result($result, 2)))  - strtotime($row_Giorno['GIORNO']." 15:30")); /*echo $difora; */if ($difora < 0) {?><img src="images/attenzione.gif" width="30" height="30"> <?php } ?></td>
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
