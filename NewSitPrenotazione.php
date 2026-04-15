<?php require_once('Connections/MyPresenze.php');
$currentPage = $_SERVER["PHP_SELF"];
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
if ((isset($_POST['Pasto'])) && ($_POST['GiornoPren'])) {
	 $_SESSION['Pasto'] = $_POST['Pasto'];
     $_SESSION['GIORNO'] = $_POST['GiornoPren'];
} else {
    if (!(isset($_SESSION['Pasto'])) && ($_SESSION['GiornoPren'])) {
        $_SESSION['Pasto'] =1;
        $_SESSION['GIORNO'] = date('Y-m-d');
    }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// ********************************** Query di modifica della sede di consumazione 

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
  $updateSQL = sprintf("UPDATE pre_accessi SET Se=%s WHERE IDrecord=%s",
                       GetSQLValueString($_POST['SEDE'], "int"),
                       GetSQLValueString($_POST['IDrecord'], "int"));
  $PRES_conn->query($updateSQL);
}

// ***************************** Query di modifica della razione

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form5")) {
  $updateSQL = sprintf("UPDATE pre_accessi SET Ti_R=%s WHERE IDrecord=%s",
                       GetSQLValueString($_POST['Razione'], "int"),
                       GetSQLValueString($_POST['IDrecord'], "int"));
  $PRES_conn->query($updateSQL);
}

//****************************** Query di eliminazione record prenotazione

if ((isset($_POST["MM_deleteForm4"])) && ($_POST["MM_deleteForm4"] == "form4")) {
	if ((isset($_POST['IDrecord'])) && ($_POST['IDrecord'] != "")) {
 	 $deleteSQL = sprintf("DELETE FROM pre_accessi WHERE IDrecord=%s",
                       GetSQLValueString($_POST['IDrecord'], "int"));
  	$PRES_conn->query($deleteSQL);
	}
}	
// ********************** Inserisce flag a pagamento ***********************************************************
	
	if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form6")) {
  		 $updateSQL = sprintf("UPDATE pre_accessi SET Pagamento=%s WHERE IDrecord=%s",
                       GetSQLValueString($_POST['Pagamento'], "int"),
                       GetSQLValueString($_POST['IDrecord'], "int"));

	     $PRES_conn->query($updateSQL);
	}


if (!isset($_SESSION['UserID'])) {
	header("Location: LoginMassiva.php");
}

$Tx_cert = $_SESSION['UserID'];
if(isset($_POST['Giorno'])){
	$giorno = $_POST['Giorno'];
}
//$_SESSION['GioPre'] = $_POST['GiornoPren'];


$query_Giorni = "SELECT date_format(selectgiorno.GIORNO,'%d-%m-%Y') AS GIO, selectgiorno.GIORNO FROM selectgiorno GROUP BY selectgiorno.GIORNO ORDER BY selectgiorno.GIORNO DESC";
$Giorni = $PRES_conn->query($query_Giorni);
$row_Giorni = mysqli_fetch_assoc($Giorni);
$totalRows_Giorni = $Giorni->num_rows;



if (isset($_GET['pageNum_Prenotazioni'])) {
  $pageNum_Prenotazioni = $_GET['pageNum_Prenotazioni'];
}


$parIDuser_Prenotazioni = "%";
if (isset($_SESSION['UserID'])) {
  $parIDuser_Prenotazioni = $_SESSION['UserID'];
}
$parPasto_Prenotazioni = "%";
if (isset($_SESSION['Pasto'])) {
  $parPasto_Prenotazioni = $_SESSION['Pasto'];
} else {
    $parPasto_Prenotazioni = 1;
}

$parGiorno_Prenotazioni = "%";
if (isset($_SESSION['GIORNO'])) {
  $parGiorno_Prenotazioni = $_SESSION['GIORNO'];
} else {
    $parGiorno_Prenotazioni = date('Y-m-d');
}

$query_Prenotazioni = sprintf("SELECT pre_accessi.IDrecord, pre_accessi.GIORNO, 
    pre_accessi.PASTO, pre_accessi.Ti_R, pre_accessi.Se, pre_gradi.Grado, 
    pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_uo.DEN_UN_OPER, 
    pre_tiporazione.TipoRazione, pre_accessi.Se, pre_accessi.Pagamento, 
    pre_accessi.Ora_cons_pr 
    FROM pre_accessi, pre_elenconomi, pre_uo, pre_tiporazione, pre_sedi, 
    pre_utentixunita, pre_gradi 
    WHERE pre_accessi.GIORNO='%s' 
    AND pre_elenconomi.IDnome=pre_accessi.IDnome 
    AND pre_uo.ID_UO=pre_elenconomi.UO 
    AND pre_tiporazione.ID=pre_accessi.Ti_R 
    AND pre_accessi.Se=pre_sedi.IDsede 
    AND pre_accessi.PASTO='%s' 
    AND pre_utentixunita.ID_UO=pre_elenconomi.UO 
    AND pre_utentixunita.IDnome='%s' 
    AND pre_gradi.ID=pre_elenconomi.IDgrado 
    ORDER BY pre_elenconomi.Cognome", 
    $parGiorno_Prenotazioni,
    $parPasto_Prenotazioni,
    $parIDuser_Prenotazioni);

// Eseguire la query direttamente
$Prenotazioni = $PRES_conn->query($query_Prenotazioni);
$totalRows_Prenotazioni = mysqli_num_rows($Prenotazioni);
$query_Sedi = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sedi = $PRES_conn->query($query_Sedi);
$row_Sedi = mysqli_fetch_assoc($Sedi);
$totalRows_Sedi = $Sedi->num_rows;
$query_Razione = "SELECT pre_tiporazione.ID, pre_tiporazione.TipoRazione FROM pre_tiporazione";
$Razione = $PRES_conn->query($query_Razione);
$row_Razione = mysqli_fetch_assoc($Razione);
$totalRows_Razione = $Razione->num_rows;
$queryString_Prenotazioni = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Prenotazioni") == false && 
        stristr($param, "totalRows_Prenotazioni") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Prenotazioni = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Prenotazioni = sprintf("&totalRows_Prenotazioni=%d%s", $totalRows_Prenotazioni, $queryString_Prenotazioni);
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<title>Riepilogo prenotazione pasti</title>
<style>
:root {
    --primary-color: rgba(32, 77, 98, 255);
    --text-color: #ffffff;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    color: var(--text-color);
    background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    margin: 0;
    padding: 5px;
    min-height: 100vh;
    overflow-x: hidden;
}

.header-banner {
    text-align: center;
    margin-bottom: 1rem;
}

.header-banner img {
    border-radius: 12px;
    width: 1020px;
    height: 100px;
    object-fit: cover;
}

.back-button {
    background: var(--primary-color);
    border: none;
    padding: 0.7em 1.5em;
    color: white;
    border-radius: 0.7em;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    cursor: pointer;
    margin: 10px;
}

.back-button:hover {
    background: rgba(32, 77, 98, 0.8);
}

.table-container {
    max-width: 90%;
    max-height: 500px; /* Altezza massima per lo scorrimento verticale */
    margin: 20px auto;
    overflow-x: auto;
    overflow-y: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    overflow: hidden;
    margin: 20px 0;
    font-size: 0.9rem;
}

.data-table th {
    background: rgba(32, 77, 98, 0.9);
    color: white;
    padding: 0.5rem;
    text-align: center;
}

.data-table td {
    padding: 0.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
}

select {
    padding: 5px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 14px;
}
select option {
    background: rgba(32, 77, 98, 0.9);
    color: white;
}

.summary-info {
    background: rgba(32, 77, 98, 0.9);
    padding: 15px;
    border-radius: 8px;
    margin: 20px auto;
    max-width: 600px;
    text-align: center;
}

input[type="submit"] {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

input[type="submit"]:hover {
    background: rgba(32, 77, 98, 0.8);
}
.summary-table {
    width: 100%;
    max-width: 800px;
    margin: 20px auto;
    background: rgba(32, 77, 98, 0.9);
    border-radius: 8px;
    padding: 15px;
}
.print-button {
    background: #204d62;
    color: #ffffff;
    border: 2px solid #ffffff;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    transition: all 0.2s ease;
}

.print-button:hover {
    background: #2c5f7c;
    border-color: #e6e6e6;
}
.table-container {
    max-width: 90%;
    margin: 20px auto;
    overflow-y: auto;
    max-height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 10px;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    background: transparent;
}

.data-table thead {
    position: sticky;
    top: 0;
    background: rgba(32, 77, 98, 0.9);
    z-index: 1;
}
.summary-container {
    background: rgba(32, 77, 98, 0.9);
    border-radius: 8px;
    padding: 20px;
    margin: 20px auto;
    max-width: 800px;
    color: white;
}
.summary-header {
    margin-bottom: 15px;
    font-size: 0.95rem;
}
.summary-content {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
}
.fas {
    transition: transform 0.2s ease;
}
.fas:hover {
    transform: scale(1.2);
}
.fa-lock {
    transition: all 0.3s ease;
}
.fa-lock:hover {
    transform: scale(1.1);
    opacity: 0.8;
}
</style>
</head>
<body>
<div style="text-align: left; margin: 20px;">
    <button class="back-button" onclick="location.href='PrenotazioneMassiva.php'">
        ← Indietro
    </button>
</div>

<form name="form1" method="post" action="<?php echo $editFormAction; ?>">
<table border="0" align="center" class="data-table">
  <tr>
    <td><div align="center"> <span style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 1.10rem;">Prenotazioni</span>
          <select name="Pasto" id="select2" onChange="this.form.submit()">
            <option value="1" <?php if (!(strcmp(1, $_SESSION['Pasto']))) {echo "SELECTED";} ?>>PRANZI</option>
            <option value="2" <?php if (!(strcmp(2, $_SESSION['Pasto']))) {echo "SELECTED";} ?>>CENE</option>
            <option value="3" <?php if (!(strcmp(3, $_SESSION['Pasto']))) {echo "SELECTED";} ?>>COLAZIONI</option>
          </select>
          <span style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 1.10rem;">del</span>
   <select name="GiornoPren" id="GiornoPren" onChange="this.form.submit()">
      <?php do {  ?>
      <option value="<?php echo $row_Giorni['GIORNO']?>"<?php if (!(strcmp($row_Giorni['GIORNO'], $_SESSION['GIORNO']))) {echo "SELECTED";} ?>><?php echo $row_Giorni['GIO']?></option>
      <?php } while ($row_Giorni = mysqli_fetch_assoc($Giorni));
  $rows = $Giorni->num_rows;
  if($rows > 0) {
      mysqli_data_seek($Giorni, 0);
      $row_Giorni = mysqli_fetch_assoc($Giorni);
  }
?>
   </select>
    </div></td>
  </tr>
</table>
</form>
<?php if ($totalRows_Prenotazioni > 0) { // Show if recordset not empty ?>
  <table class="summary-table">
  <tr>
    <td align="center">
    <form action="pdf.php" method="post" name="form2" target="_blank">
    <div align="center">
        <span>Sede di consumazione
            <select name="Sede" id="Sede">
                <?php do { ?>
                    <option value="<?php echo $row_Sedi['IDsede']?>"><?php echo $row_Sedi['SEDE']?></option>
                <?php } while ($row_Sedi = mysqli_fetch_assoc($Sedi));
                mysqli_data_seek($Sedi, 0);
                $row_Sedi = mysqli_fetch_assoc($Sedi);
                ?>
            </select>
        </span>
        <br><br>
        <div>Totale prenotazioni n°: <?php echo $totalRows_Prenotazioni ?></div>
        <br>
        <input type="hidden" name="report" value="2">
        <input type="hidden" name="gio" value="<?php echo $_SESSION['GIORNO'];?>">
        <input type="hidden" name="Pasto" value="<?php echo $_SESSION['Pasto']; ?>">
        <input type="hidden" name="totalRows" value="<?php echo $totalRows_Prenotazioni; ?>">
        <input type="submit" value="Stampa prenotazione pasti" class="print-button">
    </div>
</form>
    </td>
  </tr>
</table>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Grado</th>
                <th>Cognome</th>
                <th>Nome</th>
                <th>U.O.</th>
                <th>Razione</th>
                <th>Sede consumazione</th>
                <th>a pagamento</th>
                <?php if (!(isset($row_Prenotazioni['Ora_cons_pr']))) { ?>
                    <th>
    <?php 
    // Controlla il primo record per determinare se ci sono pasti consumati
    $row = mysqli_fetch_assoc($Prenotazioni);
    mysqli_data_seek($Prenotazioni, 0); // Riporta il puntatore all'inizio
    
    if (isset($row['Ora_cons_pr'])) {
        echo "Consumato";
    } else {
        echo "Cancella";
    }
    ?>
</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row_Prenotazioni = mysqli_fetch_assoc($Prenotazioni)) { ?>
                <tr>
                    <td align="center" height="28" nowrap><span><?php echo $row_Prenotazioni['Grado']; ?></span></td>
                    <td nowrap><div align="center"><?php echo $row_Prenotazioni['Cognome']; ?></div></td>
                    <td nowrap><div align="center"><span><?php echo $row_Prenotazioni['Nome']; ?></span></div></td>
                    <td nowrap><div align="center"><?php echo $row_Prenotazioni['DEN_UN_OPER']; ?></div></td>
                    <td nowrap>
                        <form name="form5" method="post" action="">
                            <select name="Razione" id="Razione" <?php if (!(isset($row_Prenotazioni['Ora_cons_pr']))) { ?> onChange="this.form.submit()" <?php } else {?> disabled="true" <?php } ?>>
                                <?php do { ?>
                                    <option value="<?php echo $row_Razione['ID']?>"<?php if (!(strcmp($row_Razione['ID'], $row_Prenotazioni['Ti_R']))) {echo "SELECTED";} ?>><?php echo $row_Razione['TipoRazione']?></option>
                                <?php } while ($row_Razione = mysqli_fetch_assoc($Razione));
                                mysqli_data_seek($Razione, 0);
                                $row_Razione = mysqli_fetch_assoc($Razione);
                                ?>
                            </select>
                            <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Prenotazioni['IDrecord']; ?>">
                            <input type="hidden" name="MM_update" value="form5">
                        </form>
                    </td>
                    <td nowrap>
                        <form action="<?php echo $editFormAction; ?>" method="POST" name="form3">
                            <select name="SEDE" id="SEDE" onChange="this.form.submit()">
                                <?php do { ?>
                                    <option value="<?php echo $row_Sedi['IDsede']?>"<?php if (!(strcmp($row_Sedi['IDsede'], $row_Prenotazioni['Se']))) {echo "SELECTED";} ?>><?php echo $row_Sedi['SEDE']?></option>
                                <?php } while ($row_Sedi = mysqli_fetch_assoc($Sedi));
                                mysqli_data_seek($Sedi, 0);
                                $row_Sedi = mysqli_fetch_assoc($Sedi);
                                ?>
                            </select>
                            <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Prenotazioni['IDrecord']; ?>">
                            <input type="hidden" name="MM_update" value="form3">
                        </form>
                    </td>
                    <td nowrap>
                        <form name="form6" method="post" action="<?php echo $editFormAction; ?>">
                            <select name="Pagamento" id="Pagamento" onChange="this.form.submit()">
                                <option value="1" <?php if (!(strcmp(1, $row_Prenotazioni['Pagamento']))) {echo "SELECTED";} ?>>Si</option>
                                <option value="0" <?php if (!(strcmp(0, $row_Prenotazioni['Pagamento']))) {echo "SELECTED";} ?>>No</option>
                            </select>
                            <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Prenotazioni['IDrecord']; ?>">
                            <input type="hidden" name="MM_update" value="form6">
                        </form>
                    </td>
                    <td nowrap>
                        <?php if (!(isset($row_Prenotazioni['Ora_cons_pr']))) { 
                            $ora_attuale = date('H:i');
                            $data_oggi = date('Y-m-d');
                            if ($row_Prenotazioni['GIORNO'] < $data_oggi || ($row_Prenotazioni['GIORNO'] == $data_oggi && $ora_attuale >= '09:30')) {
                                echo '<i class="fas fa-ban" style="color: #dc3545; font-size: 28px;" title="Non è più possibile cancellare la prenotazione"></i>';
                            } else { ?>
                                <form name="form4" method="post" action="">
                                    <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Prenotazioni['IDrecord']; ?>">
                                    <input name="MM_deleteForm4" type="hidden" id="MM_deleteForm4" value="form4">
                                    <input name="Submit" type="submit" value="Cancella">
                                </form>
                            <?php }
                        } else { ?>
                            <i class="fas fa-utensils" style="color: #4CAF50; font-size: 22px;" title="Pasto consumato"></i>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } // Show if recordset not empty ?>
<p>&nbsp;</p>
<?php if ($totalRows_Prenotazioni == 0) { // Show if recordset empty ?>
<p align="center">Non ci sono pasti prenotati per i criteri di ricerca impostati. </p>
<?php } // Show if recordset empty ?>
</body>
</html>
<?php
mysqli_free_result($Giorni);
mysqli_free_result($Prenotazioni);
mysqli_free_result($Sedi);
mysqli_free_result($Razione);
?>