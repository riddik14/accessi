<?php require_once('Connections/MyPresenze.php'); ?>
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
                       GetSQLValueString($_POST['forza'], "int"),
                       GetSQLValueString($_POST['pranzo'], "int"),
					   GetSQLValueString($_POST['cena'], "int"),
					   GetSQLValueString($_POST['colazione'], "int"),
                       GetSQLValueString($_POST['CF'], "text"),
                       GetSQLValueString($_POST['categoria'], "int"),
                       GetSQLValueString($_POST['grado'], "int"),
					   GetSQLValueString($_POST['fa'], "text"),
					   GetSQLValueString($_POST['sede'], "int"));
 
  $Result1 = $PRES_conn->query($insertSQL);
  echo "<script>
  alert('Anagrafica inserita correttamente');
  window.parent.location.href = 'GestAnagrafiche.php?MM_form5=form5';
</script>";
exit;

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


$query_Categorie = "SELECT pre_categorie.IDcat, pre_categorie.Categoria 
                    FROM pre_categorie 
                    ORDER BY pre_categorie.IDcat";
$Categorie = $PRES_conn->query($query_Categorie);

echo "<!-- Debug categorie: -->";
while($row = mysqli_fetch_assoc($Categorie)) {
    echo "<!-- " . $row['IDcat'] . ": " . $row['Categoria'] . " -->";
}
mysqli_data_seek($Categorie, 0); // Riporta il puntatore all'inizio
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

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserimento Anagrafica</title>
    <style>
        :root {
            --primary-color: rgba(32, 77, 98, 255);
            --text-color: #ffffff;
        }

        body {
    margin: 0;
    padding: 0;
    background: transparent;
    overflow: hidden;
}

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: var(--primary-color);
            padding: 20px;
            border-radius: 8px;
            color: var(--text-color);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    background: rgba(255,255,255,0.9); /* Sfondo più chiaro */
    color: var(--primary-color); /* Testo scuro */
    border: 1px solid white;
}
.form-control option {
    background-color: white;
    color: var(--primary-color);
    padding: 8px;
}
.form-control option:hover {
    background-color: var(--primary-color);
    color: white;
}
.form-control:focus {
    outline: none;
    border-color: white;
    box-shadow: 0 0 5px rgba(255,255,255,0.5);
}

        .btn {
            background-color: #ffffff;
            color: var(--primary-color);
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            width: 200px;
        }

        .btn:hover {
            background-color: #f0f0f0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        table {
    border: none;
    border-collapse: collapse;
    background: rgba(32, 77, 98, 0.9);
    width: 100%;
}

td, th {
    border: none;
    padding: 10px;
    color: white;
}

form {
    margin: 0;
    padding: 0;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Inserimento Nuova Anagrafica</h2>
        </div>

        <form id="formNew" name="formNew" method="POST" action="<?php echo $editFormAction; ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Forza Armata</label>
                    <select name="fa" class="form-control">
    <option value="">- Selezionare -</option>
    <option value="EI">EI</option>
    <option value="AM">AM</option>
    <option value="MM">MM</option>
    <option value="CC">CC</option>
    <option value="CIV">CIV</option>
</select>
                </div>

                <div class="form-group">
                    <label>Comando/Ufficio</label>
                    <select name="UO" class="form-control">
                        <option value="">- Selezionare -</option>
                        <?php 
                        mysqli_data_seek($UO, 0);
                        while($row_UO = mysqli_fetch_assoc($UO)): ?>
                            <option value="<?php echo $row_UO['ID_UO']; ?>"><?php echo $row_UO['DEN_UN_OPER']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
    <label>Grado</label>
    <select name="grado" class="form-control">
        <?php 
        mysqli_data_seek($Gradi, 0);
        while($row_Gradi = mysqli_fetch_assoc($Gradi)): ?>
            <option value="<?php echo $row_Gradi['ID']; ?>" 
                <?php if(isset($_GET['grado']) && $_GET['grado'] == $row_Gradi['ID']) echo 'selected'; ?>>
                <?php echo $row_Gradi['Grado']; ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<div class="form-group">
    <label>In Forza</label>
    <select name="forza" class="form-control">
        <option value="1" <?php if (isset($row_Nomi['Forza']) && !(strcmp(1, $row_Nomi['Forza']))) {echo "SELECTED";} ?>>Si</option>
        <option value="0" <?php if (isset($row_Nomi['Forza']) && !(strcmp(0, $row_Nomi['Forza']))) {echo "SELECTED";} ?>>No</option>
    </select>
</div>

                <div class="form-group">
                    <label>Categoria</label>
                    <select name="categoria" class="form-control">
    <option value="">- Selezionare -</option>
    <?php 
    mysqli_data_seek($Categorie, 0); // Reset puntatore
    while($row_Categorie = mysqli_fetch_assoc($Categorie)): ?>
        <option value="<?php echo $row_Categorie['IDcat']; ?>"><?php echo $row_Categorie['Categoria']; ?></option>
    <?php endwhile; ?>
</select>
                </div>

                <div class="form-group">
    <label>Sede Consumazione Pasti</label>
    <select name="sede" class="form-control">
        <option value="">- Selezionare -</option>
        <?php 
        mysqli_data_seek($Sedi, 0); // Reset puntatore
        while($row_Sedi = mysqli_fetch_assoc($Sedi)): ?>
            <option value="<?php echo $row_Sedi['IDsede']; ?>"><?php echo $row_Sedi['SEDE']; ?></option>
        <?php endwhile; ?>
    </select>
</div>

                <div class="form-group">
                    <label>Cognome</label>
                    <input type="text" name="cognomeNw" class="form-control" value="<?php echo isset($_GET['cognome']) ? $_GET['cognome'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Razione Pranzo</label>
                    <select name="pranzo" class="form-control">
    <option value="">- Selezionare -</option>
    <?php 
    mysqli_data_seek($TipoRazioni, 0); // Reset puntatore
    while($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni)): ?>
        <option value="<?php echo $row_TipoRazioni['ID']; ?>"><?php echo $row_TipoRazioni['TipoRazione']; ?></option>
    <?php endwhile; mysqli_data_seek($TipoRazioni, 0); ?>
</select>
                </div>

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nomeNw" class="form-control" value="<?php echo isset($_GET['nome']) ? $_GET['nome'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Razione Cena</label>
                    <select name="cena" class="form-control">
    <option value="">- Selezionare -</option>
    <?php 
    mysqli_data_seek($TipoRazioni, 0); // Reset puntatore
    while($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni)): ?>
        <option value="<?php echo $row_TipoRazioni['ID']; ?>"><?php echo $row_TipoRazioni['TipoRazione']; ?></option>
    <?php endwhile; mysqli_data_seek($TipoRazioni, 0); ?>
</select>
                </div>

                <div class="form-group">
                    <label>Codice Fiscale</label>
                    <input type="text" name="CF" class="form-control" maxlength="16" value="<?php echo isset($parCF) ? $parCF : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Razione Colazione</label>
                    <select name="colazione" class="form-control">
    <option value="">- Selezionare -</option>
    <?php 
    mysqli_data_seek($TipoRazioni, 0); // Reset puntatore
    while($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni)): ?>
        <option value="<?php echo $row_TipoRazioni['ID']; ?>"><?php echo $row_TipoRazioni['TipoRazione']; ?></option>
    <?php endwhile; ?>
</select>
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <input type="button" class="btn" value="Salva" onclick="controllo();">
                <input type="hidden" name="MM_insert" value="formNew">
            </div>
        </form>
    </div>

    <script>
        function controllo() {
            var fa = document.formNew.fa.value;
            var grado = document.formNew.grado.value;
            var categoria = document.formNew.categoria.value;
            var cognome = document.formNew.cognomeNw.value;
            var nome = document.formNew.nomeNw.value;
            var CF = document.formNew.CF.value;
            var UO = document.formNew.UO.value;
            var forza = document.formNew.forza.value;
            var sede = document.formNew.sede.value;
            var pranzo = document.formNew.pranzo.value;
            var cena = document.formNew.cena.value;
            var colazione = document.formNew.colazione.value;

            if (!fa) {
                alert("Selezionare Forza Armata di appartenenza.");
                document.formNew.fa.focus();
                return false;
            }
            if (!grado) {
                alert("Selezionare Grado.");
                document.formNew.grado.focus();
                return false;
            }
            if (!categoria) {
                alert("Selezionare Categoria.");
                document.formNew.categoria.focus();
                return false;
            }
            if (!cognome) {
                alert("Digitare Cognome.");
                document.formNew.cognomeNw.focus();
                return false;
            }
            if (!nome) {
                alert("Digitare Nome.");
                document.formNew.nomeNw.focus();
                return false;
            }
            if (!CF) {
                alert("Digitare Codice Fiscale.");
                document.formNew.CF.focus();
                return false;
            }
            if (!UO) {
                alert("Selezionare un'Unità Operativa.");
                document.formNew.UO.focus();
                return false;
            }
            if (!forza) {
                alert("Campo obbligatorio.");
                document.formNew.forza.focus();
                return false;
            }
            if (!sede) {
                alert("Selezionare la sede principale di consumazione delle razioni.");
                document.formNew.sede.focus();
                return false;
            }
            if (!pranzo || !cena || !colazione) {
                alert("Selezionare tutte le tipologie di razione.");
                return false;
            }

            document.formNew.submit();
        }
    </script>
</body>
</html>
<?php
mysqli_free_result($UnitaOperative);
mysqli_free_result($CF);
mysqli_free_result($UO);
mysqli_free_result($Sedi);
mysqli_free_result($TipoRazioni);
mysqli_free_result($Categorie);
mysqli_free_result($Gradi);
}

?>