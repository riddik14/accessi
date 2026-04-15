<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
//initialize the session
session_start();
// Controllo ricerca e query al database
$showTable = false;
$searchResults = null;

if (isset($_GET['Nome']) && $_GET['Nome'] != '') {
    $showTable = true;
    $searchTerm = htmlspecialchars($_GET['Nome']);
    
    // Query per la ricerca
    $query = "SELECT a.*, g.Grado 
          FROM anagrafica a 
          LEFT JOIN gradi g ON a.IDgrado = g.ID 
          WHERE a.Cognome LIKE '%$searchTerm%'"; 
    
}


// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
    
  $logoutGoTo = "../Home.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE pre_elenconomi SET Forza=%s WHERE IDnome=%s",
                       GetSQLValueString($_POST['forza'], "int"),
                       GetSQLValueString($_POST['ID_pers'], "int"));
  $Result1 = $PRES_conn->query($updateSQL);
}

$parNome_Nomi = "%";
if (isset($_GET['Nome'])) {
  $parNome_Nomi = addslashes($_GET['Nome']);
}

$query_Nomi = sprintf("Select
  pre_elenconomi.IDnome,
  pre_gradi.Grado,
  pre_elenconomi.Cognome,
  pre_elenconomi.Nome,
  pre_elenconomi.UO,
  pre_elenconomi.TipoOrario,
  pre_uo.DEN_UN_OPER,
  pre_elenconomi.ID_PERS_MTR,
  pre_elenconomi.Forza,
  pre_elenconomi.CF,
  pre_elenconomi.FA
From
  pre_elenconomi Left Join
  pre_gradi On pre_elenconomi.IDgrado = pre_gradi.ID Left Join
  pre_uo On pre_elenconomi.UO = pre_uo.ID_UO
Where
  pre_elenconomi.Cognome Like '%s'
Order By
  pre_elenconomi.Cognome",$parNome_Nomi );
  $Nomi = $PRES_conn->query($query_Nomi);
$row_Nomi = mysqli_fetch_assoc($Nomi);

if (isset($_GET['totalRows_Nomi'])) {
  $totalRows_Nomi = $_GET['totalRows_Nomi'];
} else {
  $all_Nomi = $PRES_conn->query($query_Nomi);
  $totalRows_Nomi = $all_Nomi->num_rows;
}


$query_Gradi = "SELECT pre_gradi.ID, pre_gradi.Grado, pre_gradi.Cat FROM pre_gradi ORDER BY pre_gradi.Ordinamento";
$Gradi = $PRES_conn->query($query_Gradi);
$row_Gradi = mysqli_fetch_assoc($Gradi);
$totalRows_Gradi = $Gradi->num_rows;


$query_UO = "SELECT pre_uo.COD_UN_OPER, pre_uo.DEN_UN_OPER FROM pre_uo";
$UO = $PRES_conn->query($query_UO);
$row_UO = mysqli_fetch_assoc($UO);
$totalRows_UO = $UO->num_rows;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <title>Gestione anagrafiche</title>
    
    <style>
        :root {
    --primary-color: #07406b;
    --text-color: #FFFFFF;
    --hover-color: #CCCCCC;
    --border-color: #e0e0e0;
    --bg-alt-color: #f8f9fa;
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
    background-attachment: fixed; /* Mantiene lo sfondo fisso durante lo scroll */
    margin: 0;
    padding: 5px;
    min-height: 100vh;
    overflow-x: hidden;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header-banner {
    border-radius: 15px;
    width: 100%;
    max-width: 1000px;
    height: 100px;
    margin-bottom: 20px;
}


.search-form {
    margin: 20px 0;
    /* rimuovo background: rgba(255, 255, 255, 0.1); */
    padding: 15px;
    /* rimuovo border-radius: 8px; */
}


/* Stili Tabella */
.tbl_generica {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 20px 0;
    background: #ffffff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Header Tabella */
.tbl_generica tr.headertbl td,
.tbl_generica tr:first-child td {
    background-color: var(--primary-color);
    color: #ffffff;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 14px;
    padding: 10px;
    border-bottom: 2px solid #053353;
}

/* Celle Tabella */
.tbl_generica td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid var(--border-color);
    color: #333333;
    background-color: #ffffff;
}

/* Righe Alternate */
.tbl_generica tr:nth-child(even) td {
    background-color: var(--bg-alt-color);
}

/* Hover sulle righe */
.tbl_generica tr:not(:first-child):hover td {
    background-color: #e8f0fe;
    transition: background-color 0.3s ease;
}

/* Bottoni e Input */
.button {
    background-color: var(--primary-color);
    color: var(--text-color);
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.button:hover {
    background-color: #0a5185;
    transform: translateY(-1px);
}

/* Form Elements */
select, 
input[type="text"] {
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 14px;
    width: auto;
    min-width: 120px;
}

select:focus, 
input[type="text"]:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 2px rgba(7, 64, 107, 0.2);
}

/* Links */
a {
    color: var(--text-color);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

a:hover {
    color: var(--hover-color);
}

/* Link nella tabella */
.tbl_generica a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.tbl_generica a:hover {
    text-decoration: underline;
    color: #0a5185;
}
.results-table {
    opacity: 0;
    transform: translateY(-20px);
    animation: fadeIn 0.3s ease forwards;
}

.no-results {
    text-align: center;
    color: #ffffff;
    padding: 20px;
    background: rgba(0,0,0,0.5);
    border-radius: 8px;
    margin-top: 20px;
}

@keyframes fadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
input[name="Nome"] {
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 200px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

input[name="Nome"]:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 2px rgba(7, 64, 107, 0.2);
}

.results-table {
    transition: opacity 0.3s ease;
}
    </style>

    <script>
        function controllo() {
            const form = document.formControllo;
            const fields = {
                CF: "Codice Fiscale",
                cognome: "Cognome",
                nome: "Nome",
                grado: "grado"
            };

            for (let [field, label] of Object.entries(fields)) {
                if (!form[field].value || form[field].value === "undefined") {
                    alert(`Digitare ${label}.`);
                    form[field].focus();
                    return;
                }
            }

            window.open(
                `operations1.php?CF=${form.CF.value}&nome=${form.nome.value}&cognome=${form.cognome.value}&grado=${form.grado.value}`,
                "",
                "top=200, left=350, width=750, height=450, toolbar=no, scrollbars=yes, resizable=yes"
            );
        }

        document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('Nome');
    let timeoutId;

    searchInput.addEventListener('input', function() {
        // Cancella il timer precedente
        clearTimeout(timeoutId);
        
        // Imposta un nuovo timer (300ms di ritardo)
        timeoutId = setTimeout(() => {
            // Recupera il valore di ricerca
            const searchTerm = this.value;
            
            // Esegue la richiesta anche con ricerca parziale
            fetch(`GestAnagrafiche.php?Nome=${encodeURIComponent(searchTerm)}`)
                .then(response => response.text())
                .then(html => {
                    // Aggiorna la tabella dei risultati
                    const container = document.querySelector('.container');
                    const existingResults = container.querySelector('.results-table, .no-results');
                    if (existingResults) {
                        existingResults.remove();
                    }

                    // Inserisce i nuovi risultati
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newResults = tempDiv.querySelector('.results-table, .no-results');
                    if (newResults) {
                        container.appendChild(newResults);
                    }
                });
        }, 300);
    });
});
        document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('Nome');
    let timeoutId;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeoutId);
        
        timeoutId = setTimeout(() => {
            const searchTerm = this.value;
            
            fetch(`GestAnagrafiche.php?Nome=${encodeURIComponent(searchTerm)}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const resultsTable = doc.querySelector('.results-table');
                    const noResults = doc.querySelector('.no-results');
                    
                    const existingTable = document.querySelector('.results-table');
                    const existingNoResults = document.querySelector('.no-results');
                    
                    if (existingTable) existingTable.remove();
                    if (existingNoResults) existingNoResults.remove();
                    
                    if (resultsTable) {
                        document.querySelector('.container').appendChild(resultsTable);
                    } else if (noResults) {
                        document.querySelector('.container').appendChild(noResults);
                    }
                });
        }, 300); // Ritardo di 300ms per evitare troppe richieste
    });
});
function filterTable() {
    let input = document.getElementById("Nome");
    let filter = input.value.toLowerCase();
    let table = document.querySelector(".tbl_generica");
    let tr = table.getElementsByTagName("tr");
    
    // Salta l'header della tabella iniziando da 1
    for (let i = 1; i < tr.length; i++) {
        let visible = false;
        let td = tr[i].getElementsByTagName("td");
        // Controlla tutte le colonne tranne l'ultima (modifica ADM)
        for (let j = 0; j < td.length - 1; j++) {
            let cell = td[j];
            if (cell) {
                let text = cell.textContent || cell.innerText;
                if (text.toLowerCase().indexOf(filter) > -1) {
                    visible = true;
                    break;
                }
            }
        }
        tr[i].style.display = visible ? "" : "none";
    }
}
    </script>
</head>
<body>
        <div class="container">
        <br>
        <form name="form2" method="get" action="GestAnagrafiche.php" class="search-form">
    <table class="tbl_generica">
        <tr>
            <td>
                <div style="text-align: center">
                    <span>Cerca cognome:</span> 
                    <input name="Nome" type="text" id="Nome" autocomplete="off" onkeyup="filterTable()">
                </div>
            </td>
        </tr>
    </table>
</form>

<?php if ($showTable && $totalRows_Nomi > 0) { ?>
    <div class="results-table">
        <table class="tbl_generica">
            <tr class="headertbl">
                <td>Cod. fiscale</td>
                <td>Grado</td>
                <td>F.A.</td>
                <td>Cognome</td>
                <td>Nome</td>
                <td>UO</td>
                <td>in forza</td>
                <td>modifica ADM</td>
            </tr>
            <?php do { ?>
                <tr>
                    <td><?php echo $row_Nomi['CF']; ?></td>
                    <td><?php echo $row_Nomi['Grado']; ?></td>
                    <td><?php echo $row_Nomi['FA']; ?></td>
                    <td><?php echo $row_Nomi['Cognome']; ?></td>
                    <td><?php echo $row_Nomi['Nome']; ?></td>
                    <td><?php echo $row_Nomi['DEN_UN_OPER']; ?></td>
                    <td>
                        <form action="<?php echo $editFormAction; ?>" method="POST" name="form1">
                            <select name="forza" id="forza" onChange="this.form.submit()">
                                <option value="1" <?php if (!(strcmp(1, $row_Nomi['Forza']))) {echo "SELECTED";} ?>>Si</option>
                                <option value="0" <?php if (!(strcmp(0, $row_Nomi['Forza']))) {echo "SELECTED";} ?>>No</option>
                            </select>
                            <input name="ID_pers" type="hidden" value="<?php echo $row_Nomi['IDnome']; ?>">
                            <input type="hidden" name="MM_update" value="form1">
                        </form>
                    </td>
                    <td>
                        <a href="ModAnagrafiche.php?ID_pers=<?php echo $row_Nomi['IDnome']; ?>">Modifica</a>
                    </td>
                </tr>
            <?php } while ($row_Nomi = mysqli_fetch_assoc($Nomi)); ?>
        </table>
    </div>
<?php } elseif ($showTable && $totalRows_Nomi == 0) { ?>
    <div class="no-results">
        <p>Nessun risultato trovato per la ricerca</p>
    </div>
<?php } ?>

        <?php if (isset($_POST['MM_form5']) && $_POST['MM_form5'] == 'form5') { ?>
            <form action="<?php echo $editFormAction; ?>" method="GET" name="formControllo" id="formControllo">
                <table class="tbl_generica">
                    <tr class="headertbl">
                        <td>Cod.Fiscale</td>
                        <td>Grado</td>
                        <td>Cognome</td>
                        <td>Nome</td>
                    </tr>
                    <tr>
                        <td><input name="CF" type="text" id="CF" maxlength="16"></td>
                        <td>
                            <select name="grado" id="select4" style="width:180px;">
                                <?php do { ?>
                                    <option value="<?php echo $row_Gradi['ID']?>"<?php if (isset($row_Nomi['IDgrado']) && !(strcmp($row_Gradi['ID'], $row_Nomi['IDgrado']))) {echo "SELECTED";} ?>><?php echo $row_Gradi['Grado']?></option>
                                <?php } while ($row_Gradi = mysqli_fetch_assoc($Gradi)); ?>
                            </select>
                        </td>
                        <td><input type="text" id="cognome" name="cognome"></td>
                        <td><input type="text" id="nome" name="nome"></td>
                    </tr>
                </table>
                <div style="text-align: center; margin-top: 20px;">
                    <input type="button" class="button" value="Avanti" onClick="controllo();">
                    <input type="hidden" name="MM_insert" value="formControllo">
                </div>
            </form>
        <?php } ?>
    </div>
</body>
</html>
<?php
mysqli_free_result($Nomi);
mysqli_free_result($UO);
?>
