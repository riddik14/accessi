<?php require_once('../Connections/MyPresenze.php');

$currentPage = $_SERVER["PHP_SELF"];

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO pre_turni (Descr, dalle, alle, sede, pasto) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Descr'], "text"),
                       GetSQLValueString($_POST['dalle'], "date"),
                       GetSQLValueString($_POST['alle'], "date"),
                       GetSQLValueString($_POST['sede'], "int"),
                       GetSQLValueString($_POST['pasto'], "int"));
  $Result1 = $PRES_conn->query($insertSQL);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
  $updateSQL = sprintf("UPDATE pre_turni SET Descr=%s, dalle=%s, alle=%s, sede=%s, pasto=%s WHERE ID=%s",
                       GetSQLValueString($_POST['Descr'], "text"),
                       GetSQLValueString($_POST['dalle'], "date"),
                       GetSQLValueString($_POST['alle'], "date"),
                       GetSQLValueString($_POST['sede'], "int"),
                       GetSQLValueString($_POST['pasto'], "int"),
                       GetSQLValueString($_POST['ID_UO'], "int"));
  $Result1 = $PRES_conn->query($updateSQL);
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_Servizi = 1;
$pageNum_Servizi = 0;
if (isset($_GET['pageNum_Servizi'])) {
  $pageNum_Servizi = $_GET['pageNum_Servizi'];
}
$startRow_Servizi = $pageNum_Servizi * $maxRows_Servizi;


$query_Servizi = "SELECT pre_turni.ID, pre_turni.Descr, pre_turni.dalle, pre_turni.alle, pre_turni.sede, pre_turni.Pasto FROM pre_turni";
$query_limit_Servizi = sprintf("%s LIMIT %d, %d", $query_Servizi, $startRow_Servizi, $maxRows_Servizi);
$Servizi = $PRES_conn->query($query_limit_Servizi);
$row_Servizi = mysqli_fetch_assoc($Servizi);

if (isset($_GET['totalRows_Servizi'])) {
  $totalRows_Servizi = $_GET['totalRows_Servizi'];
} else {
  $all_Servizi = $PRES_conn->query($query_Servizi);
  $totalRows_Servizi = $all_Servizi->num_rows;
}
$totalPages_Servizi = ceil($totalRows_Servizi/$maxRows_Servizi)-1;


$query_Sede = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sede = $PRES_conn->query($query_Sede);
$row_Sede = mysqli_fetch_assoc($Sede);
$totalRows_Sede = $Sede->num_rows;

$queryString_Servizi = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Servizi") == false && 
        stristr($param, "totalRows_Servizi") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Servizi = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Servizi = sprintf("&totalRows_Servizi=%d%s", $totalRows_Servizi, $queryString_Servizi);


$query_UO = "SELECT pre_uo.ID_UO, pre_uo.DEN_UN_OPER FROM pre_uo, pre_utentixunita";
$UO = $PRES_conn->query($query_UO);
$row_UO = mysqli_fetch_assoc($UO);
$totalRows_UO = $UO->num_rows;





$queryString_UO = " ";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_UO") == false && 
        stristr($param, "totalRows_UO") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_UO = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_UO = sprintf("&totalRows_UO=%d%s", $totalRows_UO, $queryString_UO);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Gestione servizi di caserma</title>
<style type="text/css">
<!--
body {
	background-color: #3366CC;
}
.Stile1 {
	color: #FFFFFF;
	font-weight: bold;
}
.Stile4 {color: #FFFFFF; font-size: 12px; }
-->
</style></head>

<body>
<div align="center">
  <p><img src="../images/BannerCealpi.jpg" width="800" height="75"> <a href="Master.php"><img src="../images/xbox360power.jpg" width="75" height="75" border="0" /></a> </p>
  <p class="Stile1">GESTIONE TURNI MENSA
    <?php if (!isset($_POST['Inserimento'])) { ?>
  </p>
  <form name="form3" method="POST" action="<?php echo $editFormAction; ?>">
    <table width="467" border="1">
      <tr>
        <td width="141" class="Stile4">Descrizione</td>
        <td colspan="3"><input name="Descr" type="text" id="Descr" value="<?php echo $row_Servizi['Descr']; ?>" size="50"></td>
      </tr>
      <tr>
        <td class="Stile4">ora inizio </td>
        <td width="70"><div align="left">
          <input name="dalle" type="text" id="dalle" value="<?php echo $row_Servizi['dalle']; ?>" size="6" maxlength="6">
        </div></td>
        <td width="63"><span class="Stile4">ora fine </span></td>
        <td width="165"><span class="Stile4">
          <input name="alle" type="text" id="alle" value="<?php echo $row_Servizi['alle']; ?>" size="6" maxlength="6">
        </span></td>
      </tr>
      <tr>
        <td height="30" class="Stile4">Sede</td>
        <td colspan="3"><div align="left">
          <select name="sede" id="sede">
              <?php do {  ?>
              <option value="<?php echo $row_Sede['IDsede']?>"<?php if (!(strcmp($row_Sede['IDsede'], $row_Servizi['SEDE']))) {echo "SELECTED";} ?>><?php echo $row_Sede['SEDE']?></option>
              <?php } while ($row_Sede = mysqli_fetch_assoc($Sede));
  $rows = $Sede->num_rows;
  if($rows > 0) {
      mysqli_data_seek($Sede, 0);
	  $row_Sede = mysqli_fetch_assoc($Sede);
  }
?>
            </select>
        </div></td>
      </tr>
      <tr>
        <td height="23" class="Stile4">Pasto</td>
        <td colspan="3"><p align="left">
          <select name="pasto" id="pasto">
            <option value="1">Pranzo</option>
            <option value="2">Cena</option>
            <option value="3">Colazione</option>
          </select>
</p>
          </td>
      </tr>
      <tr>
        <td height="46" colspan="4" class="Stile4"><div align="center"><span class="Stile4">
            <input name="ID_UO" type="hidden" id="ID_UO" value="<?php echo $row_Servizi['ID']; ?>">
            <input type="submit" name="Submit" value="Salva">
</span></div></td>
      </tr>
    </table>
    <table width="124">
      <tr>
        <td width="26"><div align="center"><a href="<?php printf("%s?pageNum_Servizi=%d%s", $currentPage, 0, $queryString_Servizi); ?>">
          <?php if ($pageNum_Servizi > 0) { // Show if not first page ?>
          <img src="../images/freccia_SX_Doppia.jpg" width="22" height="24">
          <?php } // Show if not first page ?>
        </a></div></td>
        <td width="26"><div align="center"><a href="<?php printf("%s?pageNum_Servizi=%d%s", $currentPage, max(0, $pageNum_Servizi - 1), $queryString_Servizi); ?>">
          <?php if ($pageNum_Servizi > 0) { // Show if not first page ?>
          <img src="../images/freccia_SX.jpg" width="22" height="24">
          <?php } // Show if not first page ?>
        </a></div></td>
        <td width="26"><div align="center"><a href="<?php printf("%s?pageNum_Servizi=%d%s", $currentPage, min($totalPages_Servizi, $pageNum_Servizi + 1), $queryString_Servizi); ?>">
          <?php if ($pageNum_Servizi < $totalPages_Servizi) { // Show if not last page ?>
          <img src="../images/freccia_DX.jpg" width="22" height="24">
          <?php } // Show if not last page ?>
        </a></div></td>
        <td width="26"><div align="center"><a href="<?php printf("%s?pageNum_Servizi=%d%s", $currentPage, $totalPages_Servizi, $queryString_Servizi); ?>">
          <?php if ($pageNum_Servizi < $totalPages_Servizi) { // Show if not last page ?>
          <img src="../images/freccia_DX_Doppia.jpg" width="22" height="24">
          <?php } // Show if not last page ?>
        </a></div></td>
      </tr>
    </table>
    <input type="hidden" name="MM_update" value="form3">
  </form>
  
  <form name="form1" method="post" action="">
    <input type="submit" name="Submit" value="Inserisci nuovo turno">
    <input name="Inserimento" type="hidden" id="Inserimento" value="1">
  </form>
  <?php } else {  ?>
  <form action="<?php echo $editFormAction; ?>" method="POST" name="form2" id="form2">
    <table width="467" border="1">
      <tr>
        <td width="141" class="Stile4">Descrizione</td>
        <td colspan="3"><input name="Descr" type="text" id="Descr" size="50"></td>
      </tr>
      <tr>
        <td class="Stile4">ora inizio </td>
        <td width="70"><div align="left">
            <input name="dalle" type="text" id="dalle" size="6" maxlength="6">
        </div></td>
        <td width="63"><span class="Stile4">ora fine </span></td>
        <td width="165"><span class="Stile4">
          <input name="alle" type="text" id="alle" size="6" maxlength="6">
        </span></td>
      </tr>
      <tr>
        <td height="25" class="Stile4">Sede</td>
        <td colspan="3"><select name="sede" id="sede">
          <?php do {  ?>
          <option value="<?php echo $row_Sede['IDsede']?>"><?php echo $row_Sede['SEDE']?></option>
          <?php } while ($row_Sede = mysqli_fetch_assoc($Sede));
  $rows = $Sede->num_rows;
  if($rows > 0) {
      mysqli_data_seek($Sede, 0);
	  $row_Sede = mysqli_fetch_assoc($Sede);
  }
?>
        </select></td>
      </tr>
      <tr>
        <td height="23" class="Stile4">Pasto</td>
        <td colspan="3"><p align="left">
          <select name="pasto" id="pasto">
            <option value="1">Pranzo</option>
            <option value="2">Cena</option>
            <option value="3">Colazione</option>
          </select>
 </p>
          </td>
      </tr>
      <tr>
        <td height="46" colspan="4" class="Stile4"><div align="center">            <input type="submit" name="Submit" value="Salva">
        </div></td>
      </tr>
    </table>
    <input type="hidden" name="MM_insert" value="form2">
  </form>
  <?php }; ?>
  <p>&nbsp;</p>
</div>
</body>
</html>
<?php
mysqli_free_result($Servizi);
mysqli_free_result($Sede);

?>