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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
  $updateSQL = sprintf("UPDATE pre_uo SET DEN_UN_OPER=%s, SEDE=%s, RANGE_IN_DA=%s, RANGE_IN_A=%s, RANGE_OUT_DA=%s, RANGE_OUT_A=%s, ID_CTE=%s, ObbligoCMD=%s, IDturno=%s WHERE ID_UO=%s",
                       GetSQLValueString($_POST['DENUO'], "text"),
                       GetSQLValueString($_POST['SEDE'], "int"),
                       GetSQLValueString($_POST['IN_DA'], "date"),
                       GetSQLValueString($_POST['IN_A'], "date"),
                       GetSQLValueString($_POST['OUT_DA'], "date"),
                       GetSQLValueString($_POST['OUT_A'], "date"),
                       GetSQLValueString($_POST['CTE'], "int"),
					   GetSQLValueString($_POST['CMD'], "int"),
					   GetSQLValueString($_POST['Turno'], "int"),
                       GetSQLValueString($_POST['ID_UO'], "int"));

  $Result1 = $PRES_conn->query($updateSQL);

  $updateGoTo = "ModUO.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST['ID_UO'])) && (isset($_POST['MM_Delete']))) {
$updateGoTo = "ModUO.php";
  $deleteSQL = sprintf("DELETE FROM pre_uo WHERE ID_UO=%s",
                       GetSQLValueString($_POST['ID_UO'], "int"));

 $Result1 = $PRES_conn->query($deleteSQL);
  header(sprintf("Location: %s", $updateGoTo));
}


$parUO_UO = "%";
if (isset($_POST['ID_UO'])) {
  $parUO_UO = addslashes($_POST['ID_UO']);
}

$query_UO = sprintf("SELECT pre_uo.ID_UO, pre_uo.COD_UN_OPER, pre_uo.DEN_UN_OPER, pre_uo.SEDE, pre_uo.RANGE_IN_DA, pre_uo.RANGE_IN_A, pre_uo.RANGE_OUT_DA, pre_uo.RANGE_OUT_A, pre_sedi.SEDE, pre_uo.ID_CTE, pre_uo.ObbligoCMD, pre_uo.IDturno FROM pre_uo LEFT JOIN pre_sedi ON pre_uo.SEDE = pre_sedi.IDsede WHERE pre_uo.ID_UO = '%s'", $parUO_UO);
$UO = $PRES_conn->query($query_UO);
$row_UO = mysqli_fetch_assoc($UO);
$totalRows_UO = $UO->num_rows;


$query_Sedi = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sedi = $PRES_conn->query($query_Sedi);
$row_Sedi = mysqli_fetch_assoc($Sedi);
$totalRows_Sedi = $Sedi->num_rows;


$query_Comandanti = "
    SELECT pre_elenconomi.IDnome, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_gradi.Grado
    FROM pre_elenconomi
    LEFT JOIN pre_gradi ON pre_gradi.ID = pre_elenconomi.IDgrado
    WHERE pre_elenconomi.Cte = '1'
";
$Comandanti = $PRES_conn->query($query_Comandanti);
$row_Comandanti = mysqli_fetch_assoc($Comandanti);
$totalRows_Comandanti = $Comandanti->num_rows;


$query_Turno = "SELECT ID, Descr FROM pre_turni WHERE ID IS NOT NULL AND Descr IS NOT NULL";
$Turni = $PRES_conn->query($query_Turno);
$row_Turni = mysqli_fetch_assoc($Turni);

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
    <title>Dettaglio U.O.</title>
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
        background: url('/ACCESSI/images/Img00.jpg') center/cover no-repeat fixed;
        color: var(--white);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .page-layout {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 60px 20px 20px 20px; /* Aumenta il margin-top da 40px a 60px */
        padding-right: 20px;
    }

    .form-container {
        background-color: var(--primary);
        padding: 16px; /* Riduci il padding da 20px a 16px */
        border-radius: 12px;
        box-shadow: 0 4px 12px var(--shadow);
        width: 100%;
        max-width: 800px;
    }

    .back-icon {
        position: fixed;
        top: 40px;
        left: 40px;
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
        text-align: center;
    }

    .form-group, .time-group {
        margin-bottom: var(--space-sm); /* Riduci il margin-bottom */
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

    .form-note {
        font-size: 14px;
        color: var(--white);
        margin-top: var(--space-sm);
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Seleziona tutti gli input con classe time-input
    const timeInputs = document.querySelectorAll('.time-input');
    
    timeInputs.forEach(input => {
        // Imposta il tipo e la lunghezza massima
        input.type = 'text';
        input.maxLength = 5;
        
        // Gestione dell'input durante la digitazione
        input.addEventListener('input', function(e) {
            // Rimuove caratteri non numerici e ":"
            let value = e.target.value.replace(/[^\d:]/g, '');
            
            // Aggiunge ":" automaticamente dopo le prime due cifre
            if (value.length === 2 && !value.includes(':')) {
                value += ':';
            }
            
            // Limita la lunghezza a 5 caratteri (HH:MM)
            if (value.length > 5) {
                value = value.slice(0, 5);
            }
            
            e.target.value = value;
        });
        
        // Validazione al perdere il focus
        input.addEventListener('blur', function() {
            const value = this.value;
            if (value) {
                const [hours, minutes] = value.split(':');
                const h = parseInt(hours);
                const m = parseInt(minutes || '0');
                
                // Verifica che ore e minuti siano validi
                if (h >= 0 && h < 24 && m >= 0 && m < 60) {
                    // Formatta con zeri iniziali
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
        <div class="form-container">
            <h2 class="form-title">Dettaglio U.O.</h2>
            <form name="form3" method="post" action="">
                <div class="form-group">
                    <label class="form-label">Denominazione</label>
                    <input name="DENUO" type="text" class="form-input" value="<?php echo $row_UO['DEN_UN_OPER']; ?>">
                </div>

                <div class="time-group">
                    <label class="time-label">Tolleranza orari ingresso</label>
                    <div class="time-inputs-container">
                        <input name="IN_DA" type="text" class="time-input" value="<?php echo $row_UO['RANGE_IN_DA']; ?>" placeholder="HH:mm" maxlength="5">
                        <input name="IN_A" type="text" class="time-input" value="<?php echo $row_UO['RANGE_IN_A']; ?>" placeholder="HH:mm" maxlength="5">
                    </div>
                </div>

                <div class="time-group">
                    <label class="time-label">Tolleranza orari uscita</label>
                    <div class="time-inputs-container">
                        <input name="OUT_DA" type="text" class="time-input" value="<?php echo $row_UO['RANGE_OUT_DA']; ?>" placeholder="HH:mm" maxlength="5">
                        <input name="OUT_A" type="text" class="time-input" value="<?php echo $row_UO['RANGE_OUT_A']; ?>" placeholder="HH:mm" maxlength="5">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Comandante</label>
                    <select name="CTE" class="form-select">
                        <option value="" <?php if (!(strcmp("", $row_UO['ID_CTE']))) {echo "SELECTED";} ?>>-</option>
                        <?php do {  ?>
                        <option value="<?php echo $row_Comandanti['IDnome']?>" <?php if (!(strcmp($row_Comandanti['IDnome'], $row_UO['ID_CTE']))) {echo "SELECTED";} ?>>
                            <?php echo $row_Comandanti['Grado']." ".$row_Comandanti['Cognome']." ".$row_Comandanti['Nome']?>
                        </option>
                        <?php } while ($row_Comandanti = mysqli_fetch_assoc($Comandanti)); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Obbligo utilizzo CMD</label>
                    <select name="CMD" class="form-select">
                        <option value="1" <?php if (!(strcmp(1, $row_UO['ObbligoCMD']))) {echo "SELECTED";} ?>>Si</option>
                        <option value="0" <?php if (!(strcmp(0, $row_UO['ObbligoCMD']))) {echo "SELECTED";} ?>>No</option>
                    </select>
                    <span class="form-note">Se "Si", gli utenti non potranno prenotare i pasti se non eseguiranno la registrazione presenze su SIGE con CMD.</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Sede</label>
                    <select name="SEDE" class="form-select">
                        <?php do {  ?>
                        <option value="<?php echo $row_Sedi['IDsede']?>" <?php if (!(strcmp($row_Sedi['IDsede'], $row_UO['SEDE']))) {echo "SELECTED";} ?>>
                            <?php echo $row_Sedi['SEDE']?>
                        </option>
                        <?php } while ($row_Sedi = mysqli_fetch_assoc($Sedi)); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Turno a mensa</label>
                    <select name="Turno" class="form-select">
    <option value="">Selezionare il turno a mensa</option>
    <?php 
    if ($row_Turni) {
        do {  
            if (!is_null($row_Turni['ID']) && !is_null($row_Turni['Descr'])) {
    ?>
            <option value="<?php echo $row_Turni['ID']?>" 
                    <?php if (!(strcmp($row_Turni['ID'], $row_UO['IDturno']))): ?>SELECTED<?php endif;?>>
                <?php echo $row_Turni['Descr']?>
            </option>
    <?php 
            }
        } while ($row_Turni = mysqli_fetch_assoc($Turni));
    }
    ?>
</select>
                    <span class="form-note">Se presso la sede la distribuzione rancio non viene eseguita a turni non selezionare alcuna opzione.</span>
                </div>

                <input name="ID_UO" type="hidden" value="<?php echo $row_UO['ID_UO']; ?>">
                <input type="submit" name="Submit" value="Salva" class="form-submit">
                <input type="hidden" name="MM_update" value="form3">
            </form>
        </div>

        <form name="form1" method="post" action="">
            <input name="ID_UO" type="hidden" value="<?php echo $row_UO['ID_UO']; ?>">
            <input type="submit" name="Submit" value="Elimina" class="form-submit" style="margin-top: 20px;">
            <input name="MM_Delete" type="hidden" value="form1">
        </form>
    </div>
</body>
</html>
<?php
mysqli_free_result($UO);
mysqli_free_result($Turni);
mysqli_free_result($Sedi);
mysqli_free_result($Comandanti);
?>
