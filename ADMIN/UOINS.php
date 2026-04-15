<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = addslashes($theValue);  // Magic quotes is deprecated and removed in PHP 7.4+

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO pre_uo (ID_UO, COD_UN_OPER, DEN_UN_OPER, PRE_UN_OPER, SEDE, RANGE_IN_DA, RANGE_IN_A, RANGE_OUT_DA, RANGE_OUT_A, ID_CTE) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['ID_UO'], "int"),
                       GetSQLValueString($_POST['COD_UNOPER'], "text"),
                       GetSQLValueString($_POST['DENUO'], "text"),
                       GetSQLValueString($_POST['DENUO'], "text"),
                       GetSQLValueString($_POST['SEDE'], "int"),
                       GetSQLValueString($_POST['IN_DA'], "date"),
                       GetSQLValueString($_POST['IN_A'], "date"),
                       GetSQLValueString($_POST['OUT_DA'], "date"),
                       GetSQLValueString($_POST['OUT_A'], "date"),
                       GetSQLValueString($_POST['CTE'], "int"));
  $Result1 = $PRES_conn->query($insertSQL);

  $insertGoTo = "ModUO.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO pre_sedi (SEDE) VALUES (%s)",
                       GetSQLValueString($_POST['textfield'], "text"));

  $Result1 = $PRES_conn->query($insertSQL);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE pre_sedi SET SEDE=%s WHERE IDsede=%s",
                       GetSQLValueString($_POST['sede'], "text"),
                       GetSQLValueString($_POST['IDsede'], "int"));

  $Result1 = $PRES_conn->query($updateSQL);
}

$currentPage = $_SERVER["PHP_SELF"];

$parUO_UO = "%";
if (isset($_POST['ID_UO'])) {
  $parUO_UO = addslashes($_POST['ID_UO']);
}

$query_UO = sprintf("SELECT pre_uo.ID_UO, pre_uo.COD_UN_OPER, pre_uo.DEN_UN_OPER, pre_uo.SEDE, pre_uo.RANGE_IN_DA, pre_uo.RANGE_IN_A, pre_uo.RANGE_OUT_DA, pre_uo.RANGE_OUT_A, pre_sedi.SEDE, pre_uo.ID_CTE FROM pre_uo, pre_sedi WHERE pre_sedi.IDsede=pre_uo.SEDE AND pre_uo.ID_UO = '%s'", $parUO_UO);
$UO = $PRES_conn->query($query_UO);
$row_UO = mysqli_fetch_assoc($UO);
$totalRows_UO = $UO->num_rows;

// Modifica gestione recordset Sedi
$query_Sedi = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sedi = $PRES_conn->query($query_Sedi);

// Crea array per le sedi
$sedi_array = array();
while ($row = mysqli_fetch_assoc($Sedi)) {
    $sedi_array[] = $row;
}
$totalRows_Sedi = count($sedi_array);

// Modifica gestione recordset Comandanti
$query_Comandanti = "SELECT pre_elenconomi.IDnome, pre_elenconomi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome FROM pre_elenconomi WHERE pre_elenconomi.Cte='1'";
$Comandanti = $PRES_conn->query($query_Comandanti);

// Crea array per i comandanti
$comandanti_array = array();
while ($row = mysqli_fetch_assoc($Comandanti)) {
    $comandanti_array[] = $row;
}
$totalRows_Comandanti = count($comandanti_array);

$queryString_UO = "";
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
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inserimento UO</title>
    <link rel="stylesheet" href="\ACCESSI\style\fonta\css\all.css">
    <style>
/* Mantieni solo questi stili e rimuovi tutti gli altri stili duplicati */
:root {
    --primary: #07406b;
    --primary-light: #0a5185;
    --white: #ffffff;
    --border-color: rgba(255, 255, 255, 0.2);
    --shadow: rgba(0, 0, 0, 0.2);
    --space-sm: 8px;
    --space-md: 16px;
    --space-lg: 24px;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
    color: var(--white);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.page-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    width: calc(100% - 120px);
    max-width: 1400px;
    margin: 140px 60px 20px 60px; /* Aumenta ulteriormente il margine superiore */
    padding-right: 20px;
}

.form-container {
    background-color: var(--primary);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px var(--shadow);
}

.form-container.main-form {
    height: fit-content;
}

.form-container.side-form {
    height: fit-content;
}

.back-icon {
    position: fixed;
    top: 40px; /* Aumenta il margine superiore */
    left: 40px; /* Aumenta il margine sinistro */
    color: var(--white);
    font-size: 20px;
    cursor: pointer;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary-light);
    border-radius: 50%;
    box-shadow: 0 2px 4px var(--shadow);
    z-index: 1000;
}

.form-title {
    margin-bottom: var(--space-md);
    font-size: 24px;
    font-weight: bold;
}

.form-group, .time-group {
    margin-bottom: var(--space-md);
}

.form-label, .time-label {
    display: block;
    margin-bottom: var(--space-sm);
    font-weight: bold;
}

.form-input, .time-input, .form-select {
    width: 100%;
    padding: var(--space-sm);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background-color: var(--white);
    color: var(--primary);
}

.time-inputs-container {
    display: flex;
    gap: var(--space-sm);
}

.form-submit {
    background-color: var(--primary-light);
    color: var(--white);
    padding: var(--space-sm) var(--space-md);
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.sede-form {
    display: flex;
    gap: var(--space-sm);
    margin-bottom: var(--space-md);
}

.sede-input {
    flex: 1;
}

.sede-table {
    width: 100%;
    border-collapse: collapse;
}

.sede-table td {
    padding: var(--space-sm) 0;
}

@media (max-width: 1200px) {
    .page-layout {
        width: calc(100% - 40px);
        margin: 140px 20px 20px 20px; /* Aumenta ulteriormente il margine superiore */
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .page-layout {
        grid-template-columns: 1fr;
    }
}
</style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestione input time
        const timeInputs = document.querySelectorAll('.time-input');
        
        // Primo gruppo di funzionalità
        timeInputs.forEach(input => {
            // Imposta il formato 24h
            input.setAttribute('type', 'time');
            
            // Gestisce il cambio di valore
            input.addEventListener('change', function() {
                if (this.value) {
                    // Assicura che il valore sia nel formato corretto
                    const time = this.value.split(':');
                    if (time.length === 2) {
                        const hours = parseInt(time[0]);
                        const minutes = parseInt(time[1]);
                        
                        // Valida ore e minuti
                        if (hours >= 0 && hours <= 23 && minutes >= 0 && minutes <= 59) {
                            // Formatta nel formato HH:mm
                            this.value = hours.toString().padStart(2, '0') + ':' + 
                                       minutes.toString().padStart(2, '0');
                        }
                    }
                }
            });
        });
        
        // Secondo gruppo di funzionalità
        timeInputs.forEach(input => {
            // Forza il formato 24h
            input.classList.add('force24h');
            
            // Imposta il formato personalizzato
            input.addEventListener('keydown', function(e) {
                // Permette solo numeri, backspace, tab e :
                if (!((e.key >= '0' && e.key <= '9') || 
                    e.key === 'Backspace' || 
                    e.key === 'Tab' || 
                    e.key === ':' ||
                    e.key === 'ArrowLeft' ||
                    e.key === 'ArrowRight' ||
                    e.key === 'Delete')) {
                    e.preventDefault();
                }
            });

            // Valida e formatta l'input
            input.addEventListener('blur', function() {
                if (this.value) {
                    const [hours, minutes] = this.value.split(':');
                    const h = parseInt(hours);
                    const m = parseInt(minutes || '0');
                    
                    if (h >= 0 && h < 24 && m >= 0 && m < 60) {
                        this.value = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                    } else {
                        this.value = '';
                    }
                }
            });
        });

        // Terzo gruppo di funzionalità
        timeInputs.forEach(input => {
            // Converti l'input in un campo testo
            input.type = 'text';
            input.maxLength = 5;
            
            input.addEventListener('input', function(e) {
                let value = e.target.value;
                
                // Rimuovi tutto tranne numeri e :
                value = value.replace(/[^\d:]/g, '');
                
                // Gestisci l'inserimento dei due punti
                if (value.length === 2 && !value.includes(':')) {
                    value += ':';
                }
                
                // Limita la lunghezza a 5 caratteri (HH:mm)
                if (value.length > 5) {
                    value = value.slice(0, 5);
                }
                
                e.target.value = value;
            });
            
            input.addEventListener('blur', function() {
                const value = this.value;
                if (value) {
                    const [hours, minutes] = value.split(':');
                    const h = parseInt(hours);
                    const m = parseInt(minutes || '0');
                    
                    // Valida ore e minuti
                    if (h >= 0 && h < 24 && m >= 0 && m < 60) {
                        this.value = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                    } else {
                        this.value = '';
                    }
                }
            });
        });
    });
    </script>
</head>

<body>
    <a href="ModUO.php" class="back-icon">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="page-layout">
        <div class="form-container main-form">
            <h2 class="form-title">Nuova U.O.</h2>
            <form name="form3" method="POST" action="<?php echo $editFormAction; ?>">
                <div class="form-group">
                    <label class="form-label">Denominazione U.O.</label>
                    <input name="DENUO" type="text" class="form-input" 
                           value="<?php echo isset($row_UO['DEN_UN_OPER'])? $row_UO['DEN_UN_OPER'] : '';?>"
                           placeholder="Inserisci denominazione">
                </div>

                <div class="time-group">
                    <label class="time-label">Orari ingresso</label>
                    <div class="time-inputs-container">
                        <input name="IN_DA" type="text" class="time-input" 
                               value="<?php echo isset($row_UO['RANGE_IN_DA'])? $row_UO['RANGE_IN_DA'] : '';?>"
                               placeholder="HH:mm"
                               pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                               maxlength="5">
                        <input name="IN_A" type="text" class="time-input" 
                               value="<?php echo isset($row_UO['RANGE_IN_A'])? $row_UO['RANGE_IN_A'] : '';?>"
                               placeholder="HH:mm"
                               pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                               maxlength="5">
                    </div>
                </div>

                <div class="time-group">
                    <label class="time-label">Orari uscita</label>
                    <div class="time-inputs-container">
                        <input name="OUT_DA" type="text" class="time-input" 
                               value="<?php echo isset($row_UO['RANGE_OUT_DA'])? $row_UO['RANGE_OUT_DA'] : '';?>"
                               placeholder="HH:mm"
                               pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                               maxlength="5">
                        <input name="OUT_A" type="text" class="time-input" 
                               value="<?php echo isset($row_UO['RANGE_OUT_A'])? $row_UO['RANGE_OUT_A'] : '';?>"
                               placeholder="HH:mm"
                               pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                               maxlength="5">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Comandante</label>
                    <select name="CTE" class="form-select">
                        <option value="">Seleziona comandante...</option>
                        <?php foreach ($comandanti_array as $comandante) { ?>
                            <option value="<?php echo $comandante['IDnome']?>"
                                    <?php if (isset($comandante['IDnome']) && isset($row_UO['ID_CTE']) &&
                                            !(strcmp($comandante['IDnome'], $row_UO['ID_CTE']))) {echo "SELECTED";}?>>
                                <?php echo $comandante['Grado']." ".$comandante['Cognome']." ".$comandante['Nome']?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Sede</label>
                    <select name="SEDE" class="form-select">
                        <option value="">Seleziona sede...</option>
                        <?php foreach ($sedi_array as $sede) { ?>
                            <option value="<?php echo $sede['IDsede']?>"
                                    <?php if (isset($sede['IDsede']) && isset($row_UO['SEDE']) &&
                                            !(strcmp($sede['IDsede'], $row_UO['SEDE']))) {echo "SELECTED";}?>>
                                <?php echo $sede['SEDE']?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <input name="ID_UO" type="hidden" value="<?php echo isset($row_UO['ID_UO'])? $row_UO['ID_UO'] : '';?>">
                <input name="COD_UNOPER" type="hidden" value="<?php echo isset($row_UO['COD_UN_OPER'])? $row_UO['COD_UN_OPER'] : +1;?>">
                <input type="submit" name="Submit" value="Salva" class="form-submit">
                <input type="hidden" name="MM_insert" value="form3">
            </form>
        </div>

        <div class="form-container side-form">
            <h2 class="form-title">Gestione sedi</h2>
            <form name="form1" method="POST" action="<?php echo $editFormAction; ?>" class="sede-form">
                <input type="text" name="textfield" class="form-input sede-input" placeholder="Inserisci nuova sede">
                <input type="submit" name="Submit" value="Salva" class="form-submit">
                <input type="hidden" name="MM_insert" value="form1">
            </form>

            <table class="sede-table">
                <tr>
                    <td><div class="form-label">Modifica sedi presenti</div></td>
                </tr>
                <?php foreach ($sedi_array as $sede) { ?>
                    <tr>
                        <td>
                            <form name="form2" method="POST" action="<?php echo $editFormAction; ?>" class="sede-form">
                                <input name="IDsede" type="hidden" value="<?php echo $sede['IDsede']; ?>">
                                <input name="sede" type="text" class="form-input sede-input" value="<?php echo $sede['SEDE']; ?>">
                                <input type="submit" name="Submit" value="Salva" class="form-submit">
                                <input type="hidden" name="MM_update" value="form2">
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
<?php
mysqli_free_result($UO);

mysqli_free_result($Sedi);

mysqli_free_result($Comandanti);
?>
