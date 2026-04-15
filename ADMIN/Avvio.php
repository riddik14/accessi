<?php require_once('../../Connections/MyPresenze.php'); ?>
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
  $updateSQL = sprintf("UPDATE pre_elenconomi SET VTV=%s, `ADMIN`=%s WHERE CF=%s",
                       GetSQLValueString($_POST['VTV'], "int"),
                       GetSQLValueString($_POST['ADMIN'], "int"),
                       GetSQLValueString($_POST['CF'], "text"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($updateSQL, $MyPresenze) or die(mysql_error());

  $updateGoTo = "../Home.html";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento senza titolo</title>
<style type="text/css">
<!--
body {
	background-color: #0066CC;
}
.Stile1 {color: #FFFFFF}
-->
</style></head>

<body>

<p>&nbsp;</p>
<p align="center" class="Stile1">SISTEMA RILEVAZIONE PRESENZE</p>
<p align="center" class="Stile1">Configurazione primo avvio</p>
<form action="<?php echo $editFormAction; ?>" name="form1" id="form1" method="POST">
  <span class="Stile1">Inserire il CF dell'utente amministratore:</span>
  <input name="CF" type="text" id="CF" size="16" maxlength="16" />  
  <input name="ADMIN" type="hidden" id="ADMIN" value="1" />
    <input name="VTV" type="hidden" id="VTV" value="2" />
<input type="submit" name="Submit" value="Avanti" />
<input type="hidden" name="MM_update" value="form1">
</form>
<p>&nbsp; </p>
</body>
</html>
