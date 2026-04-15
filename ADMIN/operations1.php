<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
session_start();

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

//Associa l'anagrafica ad un'altra unit� operativa scelta tra quelle a cui l'utente loggato � abilitato
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE pre_elenconomi SET UO=%s, Forza=1 WHERE CF=%s",
                       GetSQLValueString($_POST['UO_Select'], "int"),
                       GetSQLValueString($_POST['CF'], "text"));

  $Result1 = $PRES_conn->query($updateSQL);
  echo ('<script> alert("Unit� operativa assegnata.");
						 window.opener.location.href="GestAnagrafiche.php?";
						 self.close(); </script>');
}

// Se il $_GET['Grado'] � valido allora estraggo la categoria di appartenenza

If (isset($_GET['grado'])) {
$parGra = $_GET['grado'];

$query_Ruolo = "SELECT pre_gradi.ID, pre_gradi.Cat FROM pre_gradi Where pre_gradi.ID = '$parGra'";
$Ruolo = $PRES_conn->query($query_Ruolo);
$row_Ruolo = mysqli_fetch_assoc($Ruolo);
$Ruo = $row_Ruolo['Cat'];
}


//trova le unit� operative a cui � abilitato l'utente loggato
$Tx_cert = $_SESSION['UserID'];

if (isset($_GET['CF']) && !isset($_GET['op'])){

$parIDutente = $_SESSION['UserID'];


$query_UnitaOperative = "SELECT pre_uo.ID_UO, pre_uo.DEN_UN_OPER, pre_utentixunita.IDnome
						 FROM pre_uo Inner Join pre_utentixunita On pre_utentixunita.ID_UO = pre_uo.ID_UO
						 WHERE pre_utentixunita.IDnome = '$Tx_cert'";
$UnitaOperative = $PRES_conn->query($query_UnitaOperative);
$row_UnitaOperative = mysqli_fetch_assoc($UnitaOperative);
$totalRows_UnitaOperative = $UnitaOperative->num_rows;

//controllo sul codice fiscale
$parCF = $_GET['CF'];

$query_CF = "Select pre_elenconomi.CF, pre_elenconomi.IDnome, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.UO, pre_uo.DEN_UN_OPER
			 From pre_elenconomi Left Join pre_uo On pre_elenconomi.UO = pre_uo.ID_UO
			 Where pre_elenconomi.CF = '$parCF'";
$CF = $PRES_conn->query($query_CF);
$row_CF = mysqli_fetch_assoc($CF);
$totalRows_CF = $CF->num_rows;

if($totalRows_CF > 0){
$Cognome = $row_CF['Cognome'];
$Nome = $row_CF['Nome'];
$UO = $row_CF['DEN_UN_OPER'];
}


//inserimento nel dabatase di una nuova anagrafica

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formNew")) {
  $insertSQL = sprintf("INSERT INTO pre_elenconomi (Cognome, Nome, UO, Forza, TipoRazione, TipoRazioneCe, TipoRazioneCol, CF, Categoria, IDgrado, FA, SedeSomm) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['cognomeNw'], "text"),
                       GetSQLValueString($_POST['nomeNw'], "text"),
                       GetSQLValueString($_POST['UO'], "int"),
                       GetSQLValueString($_POST['Forza'], "int"),
                       GetSQLValueString($_POST['pranzo'], "int"),
					   GetSQLValueString($_POST['cena'], "int"),
					   GetSQLValueString($_POST['colazione'], "int"),
                       GetSQLValueString($_POST['CF'], "text"),
                       GetSQLValueString($_POST['categoria'], "int"),
                       GetSQLValueString($_POST['grado'], "int"),
					   GetSQLValueString($_POST['fa'], "text"),
					   GetSQLValueString($_POST['sede'], "int"));
 
  $Result1 = $PRES_conn->query($insertSQL);
    echo ('<script> alert("Anagrafica inserita correttamente.");
						 window.opener.location.href="GestAnagrafiche.php?";
						 self.close(); </script>');

 //aggiorna la pagine gestanagrafiche.php
 /* $insertGoTo = "GestAnagrafiche.php";

  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));*/
}

$query_Sedi = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sedi = $PRES_conn->query($query_Sedi);
$row_Sedi = mysqli_fetch_assoc($Sedi);
$totalRows_Sedi = $Sedi->num_rows;


$query_TipoRazioni = "SELECT pre_tiporazione.ID, pre_tiporazione.TipoRazione FROM pre_tiporazione";
$TipoRazioni = $PRES_conn->query($query_TipoRazioni);
$row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni);
$totalRows_TipoRazioni = $TipoRazioni->num_rows;


$query_Categorie = "SELECT pre_categorie.IDcat, pre_categorie.Categoria FROM pre_categorie";
$Categorie = $PRES_conn->query($query_Categorie);
$row_Categorie = mysqli_fetch_assoc($Categorie);
$totalRows_Categorie = $Categorie->num_rows;


$query_Gradi = "SELECT pre_gradi.ID, pre_gradi.Grado FROM pre_gradi ORDER BY pre_gradi.Ordinamento";
$Gradi = $PRES_conn->query($query_Gradi);
$row_Gradi = mysqli_fetch_assoc($Gradi);
$totalRows_Gradi = $Gradi->num_rows;


$query_UO = "SELECT pre_uo.COD_UN_OPER, pre_uo.DEN_UN_OPER, pre_uo.ID_UO FROM pre_uo ORDER BY pre_uo.DEN_UN_OPER";
$UO = $PRES_conn->query($query_UO);
$row_UO = mysqli_fetch_assoc($UO);
$totalRows_UO = $UO->num_rows;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="style/tbl.css">
<title>Anagrafiche</title>
<style type="text/css">
.Stile1 {font-size: small}
.Stile2 {font-size: medium}
</style>

<script type="text/javascript">
function Si() {
	   	   document.form1.submit();
		   
}
</script>
</head>
<body background="images/Img00.jpg">
<?php if ($totalRows_CF > 0) { ?>
<form method="POST" action="<?php echo $editFormAction; ?>" id="form1" name="form1">
  <span class="Stile1">  </span>
  <table width="600" border="1" align="center" class="headertbl">
  <tr>
    <td class="headertbl"><div align="center" class="Stile2">L'anagrafica <?php echo $Cognome?> <?php echo $Nome?> è già presente nel sistema<br> 
    ed è associata all'Unità Operativa <?php echo $row_CF['DEN_UN_OPER'];?> </div></td>
	</tr>
	<tr>
	<td><br><div align="center" class="Stile1">
	  Per modificare l'Unità Operativa di appartentenza selezionarne una tra le seguenti:
      <br>
      <br>
      <select name="UO_Select" id="UO_Select">
			<?php do { ?>
					<option value="<?php echo $row_UnitaOperative['ID_UO']?>"><?php echo $row_UnitaOperative['DEN_UN_OPER']?></option>
					<?php	} while ($row_UnitaOperative = mysqli_fetch_assoc($UnitaOperative));
			  $rows = $UnitaOperative->num_rows;
			  if($rows > 0) {
				  mysqli_data_seek($UnitaOperative, 0);
				  $row_UnitaOperative = mysqli_fetch_assoc($UnitaOperative);
			  }
			?>
      </select></div>
	  <br>	  <table border="0" align="center">
          <tr>
            <td><input type="button" name="si" id="si" value="avanti" onClick="Si();"></td>
          </tr>
      </table>	  <input name="CF" type="hidden" id="CF" value="<?php echo $parCF; ?>"></td>
	</tr>
</table>
 
  <input type="hidden" name="MM_update" value="form1">
</form>
<?php } else { 
// altrimento se il codice fiscale non � nel DB allora...
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="style/tbl.css">

<script type="text/javascript">
var fa = document.formNew.fa.value;
var grado = document.formNew.grado.value;
var categoria = document.formNew.categoria.value;
var cognome = document.formNew.cognome.value;
var nome = document.formNew.nome.value;
var CF = document.formNew.CF.value;
var UO = document.formNew.UO.value;
var forza = document.formNew.forza.value;
var sede = document.formNew.sede.value;
var pranzo = document.formNew.pranzo.value;
var cena = document.formNew.cena.value;
var colazione = document.formNew.colazione.value;

function controllo(){
	if ((fa == "") || (fa == "undefined")) {
		alert("Selezionare Forza Armata di appartentenza.");
		window.formNew.fa.focus();
	} else if ((grado == "") || (grado == "undefined")) {
		alert("Selezionare Grado.");
		window.formNew.grado.focus();
	} else if ((categoria == "") || (categoria== "undefined")) {
		alert("Selezionare Categoria.");
		window.formNew.categoria.focus();
	} else if ((cognome == "") || (cognome == "undefined")) {
		alert("Digitare Cognome.");
		window.formNew.cognome.focus();
	} else if ((nome == "") || (nome == "undefined")) {
		alert("Digitare Nome.");
		window.formNew.nome.focus();
	} else if ((CF == "") || (CF == "undefined")) {
		alert("Digitare Codice Fiscale.");
		window.formNew.CF.focus();
	} else if ((UO == "") || (UO == "undefined")) {
		alert("Selezionare un'Unità Operativa.");
		window.formNew.UO.focus();
	} else if ((forza == "") || (forza == "undefined")) {
		alert("Campo obbligatorio.");
		window.formNew.forza.focus();
	} else if ((sede == "") || (sede == "undefined")) {
		alert("Selezionare la sede principale di consumazione delle razioni.");
		window.formNew.sede.focus();
	} else if ((pranzo == "") || (pranzo == "undefined")) {
		alert("Selezionare la tipologia di razione pranzo.");
		window.formNew.pranzo.focus();
	} else if ((cena == "") || (cena == "undefined")) {
		alert("Selezionare la tipologia di razione cena.");
		window.formNew.cena.focus();
	} else if ((colazione == "") || (colazione == "undefined")) {
		alert("Selezionare la tipologia di razione colazione.");
		window.formNew.colazione.focus();
	} else {
		alert("Anagrafica inserita correttamente.");
		window.formNew.submit();
	}
}
</script>


<table style="background-color: #ffffff;
width: 35em;
height: 2em;
padding: .5em;
color: #ffffff;
text-shadow: 1px 1px 1px #000;
font: 20px Arial, Helvetica, sans-serif;
border: solid thin #ffffff;
-webkit-border-radius: .70em;
-moz-border-radius: .70em;
border-radius: .70em;
background-image: -webkit-gradient(linear, left top, left bottom,
color-stop(0.4, #07406b));" align="center">
  <tr>
    <td height="35"><div align="center">INSERIMENTO NUOVA ANAGRAFICA</div></td>
  </tr>
  <tr >
    <td>
	<br>
	<form id="formNew" name="formNew" method="POST" action="<?php echo $editFormAction; ?>">
	<table style="border-top: 1px solid white;border-bottom: 1px solid white; width:600px;" class="Stile1" align="center">
  <tr>
	<td><div align="left">F.A.</div></td>
    <td><div align="center">
      <select name="fa" id="fa" style="width:180px;">
		              <option value="EI" <?php if (isset($row_Nomi['FA']) && !(strcmp("EI", $row_Nomi['FA']))) {echo "SELECTED";} ?>>EI</option>
		              <option value="AM" <?php if (isset($row_Nomi['FA']) && !(strcmp("AM", $row_Nomi['FA']))) {echo "SELECTED";} ?>>AM</option>
		              <option value="MM" <?php if (isset($row_Nomi['FA']) && !(strcmp("MM", $row_Nomi['FA']))) {echo "SELECTED";} ?>>MM</option>
		              <option value="CC" <?php if (isset($row_Nomi['FA']) && !(strcmp("CC", $row_Nomi['FA']))) {echo "SELECTED";} ?>>CC</option>
					        <option value="CIV" <?php if (isset($row_Nomi['FA']) && !(strcmp("CIV", $row_Nomi['FA']))) {echo "SELECTED";} ?>>CIV</option>
              </select>
    </div></td>
  <td><div align="left">Comando/Ufficio </div></td>
    <td><div align="center">
      <select name="UO" id="select5" style="width:180px;">
        <option value="" <?php if (isset($row_Nomi['UO']) && !(strcmp("", $row_Nomi['UO']))) {echo "SELECTED";} ?>>- Selezionare -</option>
        <?php do { ?>
        <option value="<?php echo $row_UO['ID_UO']?>"<?php if (isset($row_Nomi['UO']) && !(strcmp($row_UO['ID_UO'], $row_Nomi['UO']))) {echo "SELECTED";} ?>><?php echo $row_UO['DEN_UN_OPER']?></option>
        <?php } while ($row_UO = mysqli_fetch_assoc($UO));
              $rows = $UO->num_rows;
              if($rows > 0) {
                mysqli_data_seek($UO, 0);
	              $row_UO = mysqli_fetch_assoc($UO);
        } ?>
      </select>
    </div></td>
  </tr>
  <tr>
    <td><div align="left">Grado</div></td>
    <td><div align="center">
      <select name="grado" id="select4" style="width:180px;">
        <?php do {  ?>
        <option value="<?php echo $row_Gradi['ID']?>"<?php if (!(strcmp($row_Gradi['ID'], $_GET['grado']))) {echo "SELECTED";} ?>><?php echo $row_Gradi['Grado']?></option>
        <?php } while ($row_Gradi = mysqli_fetch_assoc($Gradi));
            $rows = $Gradi->num_rows;
  if($rows > 0) {
      mysqli_data_seek($Gradi, 0);
	  $row_Gradi = mysqli_fetch_assoc($Gradi);
  }
?>
      </select>
    </div></td>
  <td><div align="left">in forza<input name="Forza" type="hidden" id="Forza" value="1"></div></td>
    <td><div align="center">
      <select name="forza" id="select6" style="width:180px;">
          <option value="1" <?php if (isset($row_Nomi['Forza']) && !(strcmp(1, $row_Nomi['Forza']))) {echo "SELECTED";} ?>>Si</option>
          <option value="0" <?php if (isset($row_Nomi['Forza']) && !(strcmp(0, $row_Nomi['Forza']))) {echo "SELECTED";} ?>>No</option>
      </select>
    </div></td>
  </tr>
  <tr>
    <td><div align="left">Cat.</div></td>
    <td><div align="center">
      <select name="categoria" id="categoria" style="width:180px;">
        <?php do {  ?>
        <option value="<?php echo $row_Categorie['IDcat']?>"<?php if (!(strcmp($row_Categorie['IDcat'], $Ruo))) {echo "SELECTED";} ?>><?php echo $row_Categorie['Categoria']?></option>
        <?php } while ($row_Categorie = mysqli_fetch_assoc($Categorie));
        $rows = $Categorie->num_rows;
  if($rows > 0) {
      mysqli_data_seek($Categorie, 0);
	  $row_Categorie = mysqli_fetch_assoc($Categorie);
  }
?>
              </select>
    </div></td>
 <td><div align="left">sede cons. pasti</div></td>
    <td><div align="center">
      <select name="sede" id="select8" style="width:180px;">
        <?php do {  ?>
        <option value="<?php echo $row_Sedi['IDsede']?>"<?php if (isset($row_Nomi['SedeSomm']) && !(strcmp($row_Sedi['IDsede'], $row_Nomi['SedeSomm']))) {echo "SELECTED";} ?>><?php echo $row_Sedi['SEDE']?></option>
        <?php	} while ($row_Sedi = mysqli_fetch_assoc($Sedi));
					$rows = $Sedi->num_rows;
					if($rows > 0) {
					mysqli_data_seek($Sedi, 0);
					$row_Sedi = mysqli_fetch_assoc($Sedi);
					}
					?>
      </select>
    </div></td>
  </tr>
  <tr>
    <td><div align="left">Cognome</div></td>
    <td><div align="center">
      <input name="cognomeNw" type="text" id="cognome2" value="<?php echo $_GET['cognome']; ?>" style="width: 172px;">
    </div></td>
 <td><div align="left">razione pranzo</div></td>
    <td><div align="center">
      <select name="pranzo" id="select9" style="width:180px;">
        <?php do {  ?>
        <option value="<?php echo $row_TipoRazioni['ID']?>"<?php if (isset($row_Nomi['TipoRazione']) && !(strcmp($row_TipoRazioni['ID'], $row_Nomi['TipoRazione']))) {echo "SELECTED";} ?>><?php echo $row_TipoRazioni['TipoRazione']?></option>
        <?php	} while ($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni));
						$rows = $TipoRazioni->num_rows;
						if($rows > 0) {
						mysqli_data_seek($TipoRazioni, 0);
						$row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni);
						}
						?>
      </select>
    </div></td>
  </tr>
  <tr>
    <td><div align="left">Nome</div></td>
    <td><div align="center"><span class="Stile6">
        <input name="nomeNw" type="text" id="nome3" value="<?php echo $_GET['nome']; ?>" style="width: 172px;">
    </span></div></td>
  <td><div align="left">razione cena</div></td>
    <td><div align="center">
      <select name="cena" id="select10" style="width:180px;">
        <?php do {  ?>
        <option value="<?php echo $row_TipoRazioni['ID']?>"<?php if (isset($row_Nomi['TipoRazioneCe']) && !(strcmp($row_TipoRazioni['ID'], $row_Nomi['TipoRazioneCe']))) {echo "SELECTED";} ?>><?php echo $row_TipoRazioni['TipoRazione']?></option>
        <?php	} while ($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni));
					$rows = $TipoRazioni->num_rows;
					if($rows > 0) {
					mysqli_data_seek($TipoRazioni, 0);
					$row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni);
					}
					?>
      </select>
    </div></td>
  </tr>
  <tr>
    <td><div align="left">Cod.Fiscale</div></td>
    <td><div align="center"><span class="Stile6">
        <input name="CF" type="text" id="CF3" value="<?php echo $parCF; ?>" style="width: 172px;" maxlength="16">
    </span></div></td>
  <td><div align="left">razione colazione</div></td>
    <td><div align="center">
      <select name="colazione" id="select12" style="width:180px;">
        <?php do { ?>
        <option value="<?php echo $row_TipoRazioni['ID']?>"<?php if (isset($row_Nomi['TipoRazioneCol']) && !(strcmp($row_TipoRazioni['ID'], $row_Nomi['TipoRazioneCol']))) {echo "SELECTED";} ?>><?php echo $row_TipoRazioni['TipoRazione']?></option>
        <?php } while ($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni));
						$rows = $TipoRazioni->num_rows;
						if($rows > 0) {
						mysqli_data_seek($TipoRazioni, 0);
						$row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni);
						}
						?>
      </select>
    </div></td>
  </tr>
  <tr>
    <td><div align="left">SISME</div></td>
    <td><div align="center">
      <input name="sisme" type="text" id="SISME4" style="width: 172px;" maxlength="5">
    </div></td>
 <td><div align="left"></div></td>
    <td><div align="center">
    </div></td>
  </tr>
</table>
	<br>
<table border="0" align="center">
  <tr>
    <td><input type="button" id="salva" name="salva" value="salva" onClick="controllo();">
	<input type="hidden" name="MM_insert" value="formNew"></td>
  </tr>
</table>
</form>
	</td>
  </tr>
</table>

</body>
</html>
<?php
mysqli_free_result($UnitaOperative);
mysqli_free_result($CF);

	}
mysqli_free_result($UO);
mysqli_free_result($Sedi);
mysqli_free_result($TipoRazioni);
mysqli_free_result($Categorie);
mysqli_free_result($Gradi);
}

?>