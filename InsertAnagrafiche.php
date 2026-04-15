<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
session_start();
$Tx_cert = $_SESSION['UserID'];

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = addslashes($theValue); // Always escape since magic_quotes_gpc is removed in PHP 8.0

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
  $editFormAction .= htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO pre_elenconomi (Cognome, Nome, UO, Forza, TipoRazione, TipoRazioneCe, TipoRazioneCol, CF, Categoria, IDgrado, FA, SedeSomm) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['cognome'], "text"),
                       GetSQLValueString($_POST['nome'], "text"),
                       GetSQLValueString($_POST['UO'], "int"),
                       GetSQLValueString($_POST['Forza'], "int"),
                       GetSQLValueString($_POST['select'], "int"),
					   GetSQLValueString($_POST['selectcena'], "int"),
					   GetSQLValueString($_POST['selectcol'], "int"),
                       GetSQLValueString($_POST['CF'], "text"),
                       GetSQLValueString($_POST['Categoria'], "int"),
                       GetSQLValueString($_POST['Grado'], "int"),
					   GetSQLValueString($_POST['FA'], "text"),
					   GetSQLValueString($_POST['sede'], "int"));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $Result1 = mysql_query($insertSQL, $MyPresenze) or die(mysql_error());

  $insertGoTo = "InsertAnagrafiche.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_Nomi = 20;
$pageNum_Nomi = 0;
if (isset($_GET['pageNum_Nomi'])) {
  $pageNum_Nomi = $_GET['pageNum_Nomi'];
}
$startRow_Nomi = $pageNum_Nomi * $maxRows_Nomi;

$parUO_Nomi = $_SESSION['UserID'];

$parNome_Nomi = "%";
if (isset($_POST['Nome'])) {
  $parNome_Nomi = addslashes($_POST['Nome']);
}
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Nomi = sprintf("SELECT pre_elenconomi.IDnome AS IDpersona, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.UO, 
						pre_elenconomi.TipoOrario, pre_uo.DEN_UN_OPER, pre_elenconomi.Forza, pre_elenconomi.CF, pre_elenconomi.TipoRazione,
						pre_tiporazione.TipoRazione, pre_sedi.SEDE, pre_categorie.Categoria, pre_gradi.Grado, pre_elenconomi.FA
						FROM pre_elenconomi, pre_uo, pre_tiporazione, pre_sedi, pre_categorie, pre_gradi
						WHERE pre_elenconomi.UO = 74 AND pre_elenconomi.TipoRazione=pre_tiporazione.ID 
						AND pre_elenconomi.Cognome LIKE '%s' AND pre_sedi.IDsede=pre_elenconomi.SedeSomm AND 
						pre_elenconomi.categoria=pre_categorie.IDcat AND pre_elenconomi.IDgrado=pre_gradi.ID AND pre_elenconomi.UO=pre_uo.ID_UO
						GROUP BY pre_elenconomi.IDnome ORDER BY pre_elenconomi.Cognome", $parNome_Nomi);
$query_limit_Nomi = sprintf("%s LIMIT %d, %d", $query_Nomi, $startRow_Nomi, $maxRows_Nomi);
$Nomi = mysql_query($query_Nomi, $MyPresenze) or die(mysql_error());
$row_Nomi = mysql_fetch_assoc($Nomi);

if (isset($_GET['totalRows_Nomi'])) {
  $totalRows_Nomi = $_GET['totalRows_Nomi'];
} else {
  $all_Nomi = mysql_query($query_Nomi);
  $totalRows_Nomi = mysql_num_rows($all_Nomi);
}
$totalPages_Nomi = ceil($totalRows_Nomi/$maxRows_Nomi)-1;

$maxRows_UO = 200;
$pageNum_UO = 0;
if (isset($_GET['pageNum_UO'])) {
  $pageNum_UO = $_GET['pageNum_UO'];
}
$startRow_UO = $pageNum_UO * $maxRows_UO;
$Tx_cert = $_SESSION['UserID'];
$maxRows_UO = 200;;
$pageNum_UO = 0;
if (isset($_GET['pageNum_UO'])) {
  $pageNum_UO = $_GET['pageNum_UO'];
}
$startRow_UO = $pageNum_UO * $maxRows_UO;

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_UO = "SELECT pre_uo.ID_UO, pre_uo.DEN_UN_OPER FROM pre_uo WHERE pre_uo.ID_UO=74 ORDER BY pre_uo.DEN_UN_OPER";
$query_limit_UO = sprintf("%s LIMIT %d, %d", $query_UO, $startRow_UO, $maxRows_UO);
$UO = mysql_query($query_limit_UO, $MyPresenze) or die(mysql_error());
$row_UO = mysql_fetch_assoc($UO);

if (isset($_GET['totalRows_UO'])) {
  $totalRows_UO = $_GET['totalRows_UO'];
} else {
  $all_UO = mysql_query($query_UO);
  $totalRows_UO = mysql_num_rows($all_UO);
}
$totalPages_UO = ceil($totalRows_UO/$maxRows_UO)-1;

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_TipoRazione = "SELECT pre_tiporazione.ID, pre_tiporazione.TipoRazione FROM pre_tiporazione";
$TipoRazione = mysql_query($query_TipoRazione, $MyPresenze) or die(mysql_error());
$row_TipoRazione = mysql_fetch_assoc($TipoRazione);
$totalRows_TipoRazione = mysql_num_rows($TipoRazione);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Sede = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi WHERE IDsede = 2";
$Sede = mysql_query($query_Sede, $MyPresenze) or die(mysql_error());
$row_Sede = mysql_fetch_assoc($Sede);
$totalRows_Sede = mysql_num_rows($Sede);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_TipoRaz = "SELECT pre_tiporazione.ID, pre_tiporazione.TipoRazione FROM pre_tiporazione";
$TipoRaz = mysql_query($query_TipoRaz, $MyPresenze) or die(mysql_error());
$row_TipoRaz = mysql_fetch_assoc($TipoRaz);
$totalRows_TipoRaz = mysql_num_rows($TipoRaz);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Gradi = "SELECT pre_gradi.ID, pre_gradi.Grado FROM pre_gradi ORDER BY pre_gradi.Ordinamento";
$Gradi = mysql_query($query_Gradi, $MyPresenze) or die(mysql_error());
$row_Gradi = mysql_fetch_assoc($Gradi);
$totalRows_Gradi = mysql_num_rows($Gradi);

mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Categoria = "SELECT pre_categorie.IDcat, pre_categorie.Categoria FROM pre_categorie";
$Categoria = mysql_query($query_Categoria, $MyPresenze) or die(mysql_error());
$row_Categoria = mysql_fetch_assoc($Categoria);
$totalRows_Categoria = mysql_num_rows($Categoria);

$queryString_Nomi = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Nomi") == false && 
        stristr($param, "totalRows_Nomi") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Nomi = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Nomi = sprintf("&totalRows_Nomi=%d%s", $totalRows_Nomi, $queryString_Nomi);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<script type="text/javascript">
    /**
     * Modern browsers no longer support VBScript.
     * This functionality needs to be reimplemented using JavaScript.
     * The original code was trying to read certificate data from a smart card.
     */
    
    function ListCerts() {
        // Modern implementation would use Web Cryptography API or other JavaScript libraries
        // For now, just show an alert explaining the situation
        alert("La lettura della CMD non è più supportata in questo browser. Contattare l'amministratore del sistema per aggiornare questa funzionalità.");
        
        // If you need to test form input, you can uncomment these lines:
        // document.forms.form3.cognome.value = "Cognome di test";
        // document.forms.form3.nome.value = "Nome di test";
        // document.forms.form3.CF.value = "CODICEFISCALETEST";
    }
</script>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Gestione anagrafiche</title>
<style type="text/css">
<!--
body {
	background-color: #0066CC;
}
.Stile4 {color: #FFFFFF}
.Stile6 {color: #FFFFFF; font-size: small; }
.Stile8 {font-size: small; }
.Stile11 {font-size: 10px; }
.Stile13 {
	color: #000000;
	font-weight: bold;
	font-size: 10px;
}
.Stile14 {color: #FFFFFF; font-size: x-small; }
-->
</style></head>

<body>
<div align="center">
  <p><img src="./images/BannerCealpi.jpg" width="671" height="74"> </p>
  <table width="487" border="1">
    <tr>
      <th width="298" scope="col"><form name="form2" method="post" action="InsertAnagrafiche.php">
        <span class="Stile4">Trova nome</span>
        <input name="Nome" type="text" id="Nome">
        <input type="submit" name="Submit" value="Trova">
      </form></th>
      <th width="173" scope="col"><form name="form5" method="post" action="">
        <input name="MM_form5" type="hidden" id="MM_form5" value="form5">
        <input type="submit" name="Submit" value="Inserisci nominativi">
      </form></th>
    </tr>
  </table>
  <?php if (isset($_POST['MM_form5']) && $_POST['MM_form5'] == 'form5') { ?>
  <form name="form3" method="POST" action="<?php echo $editFormAction; ?>">
    <h3 class="Stile4">Inserimento manuale nuovi nominativi </h3>
    <table width="846" border="1">
      <tr class="Stile6">
        <td width="144">Codice fiscale (OBBLIGATORIO)</td>
        <td width="41">Grado</td>
        <td width="144">FF.AA. (obbligatorio)</td>
        <td width="144">Cognome</td>
        <td width="144">Nome</td>
        <td width="41">UO</td>
        <td width="63">Categoria</td>
        <td width="73">&nbsp;</td>
      </tr>
      <tr>
        <td><input name="CF" type="text" id="CF" maxlength="16"></td>
        <td><span class="Stile14">
          <select name="Grado" id="Grado">
            <?php
do {  
?>
            <option value="<?php echo $row_Gradi['ID']?>"<?php if (!(strcmp($row_Gradi['ID'], $row_Nomi['IDgrado']))) {echo "SELECTED";} ?>><?php echo $row_Gradi['Grado']?></option>
            <?php
} while ($row_Gradi = mysql_fetch_assoc($Gradi));
  $rows = mysql_num_rows($Gradi);
  if($rows > 0) {
      mysql_data_seek($Gradi, 0);
	  $row_Gradi = mysql_fetch_assoc($Gradi);
  }
?>
          </select>
        </span></td>
        <td><select name="FA" id="FA">
          <option value="EI">EI</option>
          <option value="MM">MM</option>
          <option value="AM">AM</option>
          <option value="CC">CC</option>
		  <option value="CIV">CIV</option>
		  <option value="STRA">STRA</option>
        </select></td>
        <td><input name="cognome" type="text" id="cognome"></td>
        <td><input name="nome" type="text" id="nome"></td>
        <td><select name="UO" id="UO">
          <?php
do {  
?>
          <option value="<?php echo $row_UO['ID_UO']?>"><?php echo $row_UO['DEN_UN_OPER']?></option>
            <?php
} while ($row_UO = mysql_fetch_assoc($UO));
  $rows = mysql_num_rows($UO);
  if($rows > 0) {
      mysql_data_seek($UO, 0);
	  $row_UO = mysql_fetch_assoc($UO);
  }
?>
        </select></td>
        <td><select name="Categoria" id="Categoria">
          <?php
do {  
?>
          <option value="<?php echo $row_Categoria['IDcat']?>"><?php echo $row_Categoria['Categoria']?></option>
          <?php
} while ($row_Categoria = mysql_fetch_assoc($Categoria));
  $rows = mysql_num_rows($Categoria);
  if($rows > 0) {
      mysql_data_seek($Categoria, 0);
	  $row_Categoria = mysql_fetch_assoc($Categoria);
  }
?>
        </select>
        <input name="Forza" type="hidden" id="Forza" value="1"></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td class="Stile6">Razione pranzo </td>
        <td><span class="Stile14">
          <select name="select">
            <?php
do {  
?>
            <option value="<?php echo $row_TipoRazione['ID']?>"><?php echo $row_TipoRazione['TipoRazione']?></option>
            <?php
} while ($row_TipoRazione = mysql_fetch_assoc($TipoRazione));
  $rows = mysql_num_rows($TipoRazione);
  if($rows > 0) {
      mysql_data_seek($TipoRazione, 0);
	  $row_TipoRazione = mysql_fetch_assoc($TipoRazione);
  }
?>
          </select>
        </span></td>
        <td class="Stile6">Razione cena </td>
        <td><span class="Stile14">
          <select name="selectcena" id="selectcena">
            <?php
do {  
?>
            <option value="<?php echo $row_TipoRazione['ID']?>"><?php echo $row_TipoRazione['TipoRazione']?></option>
            <?php
} while ($row_TipoRazione = mysql_fetch_assoc($TipoRazione));
  $rows = mysql_num_rows($TipoRazione);
  if($rows > 0) {
      mysql_data_seek($TipoRazione, 0);
	  $row_TipoRazione = mysql_fetch_assoc($TipoRazione);
  }
?>
          </select>
        </span></td>
        <td class="Stile6">Razione colazione </td>
        <td><span class="Stile14">
          <select name="selectcol" id="selectcol">
            <?php
do {  
?>
            <option value="<?php echo $row_TipoRazione['ID']?>"><?php echo $row_TipoRazione['TipoRazione']?></option>
            <?php
} while ($row_TipoRazione = mysql_fetch_assoc($TipoRazione));
  $rows = mysql_num_rows($TipoRazione);
  if($rows > 0) {
      mysql_data_seek($TipoRazione, 0);
	  $row_TipoRazione = mysql_fetch_assoc($TipoRazione);
  }
?>
          </select>
        </span></td>
        <td class="Stile6">Sede consumazione           </td>
        <td><select name="sede" id="sede">
          <?php
do {  
?>
          <option value="<?php echo $row_Sede['IDsede']?>"><?php echo $row_Sede['SEDE']?></option>
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
    <input type="submit" name="Submit" value="Salva">
    <input type="hidden" name="MM_insert" value="form3">
  </form>
  <p>
    <input type="submit" name="Submit" onClick="ListCerts()" value="Carica dati dalla CMD dell'amministrato">
  </p>
  <p>
    <?php } else { ?>
</p>
  <table width="973" border="1">
    <tr class="Stile8">
      <td width="77"><div align="center" class="Stile4">
          <div align="center">Grado</div>
      </div></td>
      <td width="75" class="Stile6">F.A.</td>
      <td width="75" class="Stile6"><div align="center">Categoria</div></td>
      <td width="113"><div align="center" class="Stile4">
          <div align="center">Cognome</div>
      </div></td>
      <td width="74"><div align="center" class="Stile4">
          <div align="center">Nome</div>
      </div></td>
      <td width="131"><div align="center" class="Stile4">
          <div align="center">Comando/Ufficio</div>
      </div></td>
      <td width="51"><div align="center"><span class="Stile4">In forza </span></div></td>
      <td width="91" nowrap class="Stile6"><div align="center">Tipo razione</div></td>
      <td width="73" class="Stile6"><div align="center">Sede </div></td>
      <td width="149"><div align="center"><span class="Stile4">Modifica</span></div></td>
    </tr>
		
    <?php $n_row = 1;
	do { 
   if ( $odd = $n_row%2 ) { ?>
   <tr bgcolor="#999999" class="Stile6">
    <?php }; ?>
      <td nowrap class="Stile6"><div align="center"><?php echo $row_Nomi['Grado']; ?></div></td>
      <td nowrap class="Stile6"><span ><?php echo $row_Nomi['FA']; ?></span></td>
      <td nowrap class="Stile6"><span ><?php echo $row_Nomi['Categoria']; ?></span></td>
      <td nowrap class="Stile6"><div align="left">
        <div align="center"><?php echo $row_Nomi['Cognome']; ?></div>
      </div></td>
      <td nowrap class="Stile6"><div align="left">
        <div align="center"><?php echo $row_Nomi['Nome']; ?></div>
      </div></td>
      <td nowrap class="Stile6"><div align="left">
        <div align="center"><?php echo $row_Nomi['DEN_UN_OPER']; ?></div>
      </div></td>
      <td nowrap class="Stile6"><div align="center" ><?php if (!(strcmp($row_Nomi['Forza'],1))) {echo "SI";} else {echo "NO";} ?></div></td>
      <td nowrap class="Stile6"><div align="center"><?php echo $row_Nomi['TipoRazione']; ?></div></td>
      <td nowrap class="Stile6"><div align="center"><?php echo $row_Nomi['SEDE']; ?></div></td>
      <td class="Stile6"><form action="ModAnagrafiche2.php" method="post" name="form1" class="Stile4">
        <div align="center">
          <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Nomi['IDpersona']; ?>">
          <input name="Submit" type="submit" class="Stile11" value="Modifica">
        </div>
      </form>        </td>
    </tr>
    <?php $n_row++; 
		} while ($row_Nomi = mysql_fetch_assoc($Nomi)); ?>
  </table>
  
  <?php }; ?>
</div>
</body>
</html>
<?php
mysql_free_result($Nomi);
mysql_free_result($UO);
mysql_free_result($TipoRazione);
mysql_free_result($Sede);
mysql_free_result($TipoRaz);
mysql_free_result($Gradi);
mysql_free_result($Categoria);
?>
