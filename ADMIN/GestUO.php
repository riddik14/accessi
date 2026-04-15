<?php require_once('../Connections/MyPresenze.php');
//initialize the session
session_start();

foreach ($_POST as $key => $value) {
    if (strpos($key, 'forza_') === 0) {
        $id = substr($key, 6); // Estrae l'ID dopo "forza_"
        $updateSQL = sprintf("UPDATE pre_elenconomi SET Forza=%s WHERE IDnome=%s",
            GetSQLValueString($value, "int"),
            GetSQLValueString($id, "int")
        );
        $Result1 = $PRES_conn->query($updateSQL);
    }
}

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

if (isset($_POST['Submit']) && $_POST['Submit'] == "Cambio UO"){
    if (isset($_POST['checkbox'])) {
        $checked = $_POST['checkbox']; 
        $count = count($checked); 
        for($i=0; $i < $count; $i++) 
        {
            $updateSQL = sprintf("UPDATE pre_elenconomi SET UO=%s WHERE IDnome=%s",
                       GetSQLValueString($_POST['UO'], "int"),
                       GetSQLValueString($checked[$i], "int"));
            $Result1 = $PRES_conn->query($updateSQL);
        }
    }
}

if (isset($_POST['Submit']) && $_POST['Submit'] == "Esegui perdita di forza"){
    if (isset($_POST['checkbox'])) {
        $checked = $_POST['checkbox']; 
        $count = count($checked); 
        for($i=0; $i < $count; $i++) 
        {
            $updateSQL = sprintf("UPDATE pre_elenconomi SET FORZA=%s WHERE IDnome=%s",
                       GetSQLValueString(0, "int"),
                       GetSQLValueString($checked[$i], "int"));
            $Result1 = $PRES_conn->query($updateSQL);
        }
    }
}

if (isset($_POST['Submit']) && $_POST['Submit'] == "Cancellazione definitiva record"){
    if (isset($_POST['checkbox'])) {
        $checked = $_POST['checkbox']; 
        $count = count($checked); 
        for($i=0; $i < $count; $i++) 
        {
            $updateSQL = sprintf("DELETE FROM pre_elenconomi WHERE IDnome=%s",
                      GetSQLValueString($checked[$i], "int"));
            $Result1 = $PRES_conn->query($updateSQL);
        }
    }
}

if (isset($_POST['Submit']) && $_POST['Submit'] == "Perdita di forza globale"){
    $parUO = $_SESSION['UO'];
    $updateSQL = "UPDATE pre_elenconomi SET FORZA=0 WHERE UO = '$parUO'";
    $Result1 = $PRES_conn->query($updateSQL);
}

// Rimuovere questi blocchi perché non servono più per la paginazione
$maxRows_Nomi = 999999; // Modificato per mostrare tutti i risultati
$pageNum_Nomi = 0;
$startRow_Nomi = 0;

$parNome_Nomi = "%";
if (isset($_GET['UO'])) {
  $parNome_Nomi = addslashes($_GET['UO']);
}

$query_Nomi = sprintf("SELECT pre_elenconomi.IDnome, pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.UO, pre_elenconomi.TipoOrario, pre_uo.DEN_UN_OPER, pre_elenconomi.Forza, pre_elenconomi.CF, pre_elenconomi.FA FROM pre_elenconomi LEFT JOIN pre_gradi ON pre_elenconomi.IDgrado = pre_gradi.ID, pre_uo WHERE pre_uo.ID_UO=pre_elenconomi.UO AND pre_elenconomi.UO= '%s' ORDER BY pre_elenconomi.Cognome", $parNome_Nomi);
$query_limit_Nomi = sprintf("%s LIMIT %d, %d", $query_Nomi, $startRow_Nomi, $maxRows_Nomi);
$Nomi = $PRES_conn->query($query_limit_Nomi);
$row_Nomi = mysqli_fetch_assoc($Nomi);

if (isset($_GET['totalRows_Nomi'])) {
  $totalRows_Nomi = $_GET['totalRows_Nomi'];
} else {
  $all_Nomi = $PRES_conn->query($query_Nomi);
  $totalRows_Nomi = $all_Nomi->num_rows;
}
$totalPages_Nomi = ceil($totalRows_Nomi/$maxRows_Nomi)-1;

$query_UO = "SELECT pre_uo.ID_UO, pre_uo.DEN_UN_OPER FROM pre_uo ORDER BY DEN_UN_OPER";
$UO = $PRES_conn->query($query_UO);
$row_UO = mysqli_fetch_assoc($UO);
$totalRows_UO = $UO->num_rows;

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
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <link rel="stylesheet" href="\ACCESSI\style\fonta\css\all.css">
    <title>Gestione UO</title>
    <style>
:root {
    --primary-color: #07406b;
    --secondary-color: #0a5185;
    --text-color: #2c3e50;
    --table-bg: rgba(255, 255, 255, 0.95);
    --hover-color: rgba(52, 152, 219, 0.1);
    --border-color: #bdc3c7;
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
    padding: 20px;
    min-height: 100vh;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    margin-left: 15px; /* Aggiungi questa linea */
}

.controls-container, .actions-container {
    background: var(--table-bg);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table-container {
    background: var(--table-bg);
    border-radius: 8px;
    padding: 10px;
    height: 40em;
    margin-bottom: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tbl_generica {
    width: 100%;
    margin-top: 0.4rem;
    border-collapse: separate;
    border-spacing: 0;
    color: var(--text-color);
}

.tbl_generica thead {
    background: var(--primary-color);
    position: sticky;
    top: 0;
    z-index: 10;
}

.tbl_generica thead td {
    padding: 12px 8px;
    font-weight: bold;
    color: white;
    border-bottom: 2px solid var(--secondary-color);
}

.tbl_generica tbody tr:hover {
    background: var(--hover-color);
    transition: background 0.2s ease;
}

.tbl_generica td {
    padding: 8px;
    border-bottom: 1px solid var(--border-color);
}

select, input[type="submit"] {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    margin: 0 5px;
    transition: all 0.2s ease;
}

select:hover, input[type="submit"]:hover {
    background: var(--secondary-color);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

a {
    color: white;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 4px;
    background: var(--primary-color);
    transition: all 0.2s ease;
    display: inline-block;
}

a:hover {
    background: var(--secondary-color);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table-container {
    max-height: calc(100vh - 250px);
    overflow-y: auto;
}

.table-container::-webkit-scrollbar {
    width: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: rgba(189, 195, 199, 0.2);
}

.table-container::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 4px;
}

.cell-xs { width: 5%; }
.cell-sm { width: 8%; }
.cell-md { width: 12%; }
.cell-lg { width: 15%; }

/* Aggiunta di stili per checkbox più moderni */
input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--primary-color);
}
.input-group {
  border-radius: 8px;
  overflow: hidden;
}

.form-control {
  border: 2px solid #dee2e6;
  padding: 0.75rem 1rem;
  transition: all 0.2s ease-in-out;
}

.form-control:focus {
  border-color: #0d6efd;
  outline: none;
}

.btn-primary {
  padding: 0.75rem 1.5rem;
  font-weight: 500;
  border: none;
  transition: all 0.2s ease-in-out;
}

.btn-primary:hover {
  background-color: #0b5ed7;
  transform: translateY(-1px);
}

.shadow-sm {
  box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}

</style>
</head>
<body>
    <div class="container">
        <div class="controls-container">
            <form name="form2" method="get" action="GestUO.php">
                <span>Filtra per UO
                <select name="UO" id="UO">
                  <?php
                do {  
                ?>
                  <option value="<?php echo $row_UO['ID_UO']?>"<?php if (isset($_SESSION['UO']) && !(strcmp($row_UO['ID_UO'], $_SESSION['UO']))) {echo "SELECTED";} ?>><?php echo $row_UO['DEN_UN_OPER']?></option>
                  <?php
                } while ($row_UO = mysqli_fetch_assoc($UO));
                  $rows = $UO->num_rows;
                  if($rows > 0) {
                      mysqli_data_seek($UO, 0);
                      $row_UO = mysqli_fetch_assoc($UO);
                  }
                ?>
                </select> 
                </span> 
                <input type="submit" name="Submit" value="Trova">
                <span>totale: <?php echo $totalRows_Nomi ?> anagrafiche</span>  
            </form>
        </div>
        <?php if (isset($_GET['Submit']) && $_GET['Submit'] == "Trova") { ?>
        <div class="table-container">
    <form name="form3" method="post" action="">
        <?php if ($totalRows_Nomi > 0) { // Show if recordset not empty ?>
            <div class="row justify-content-end mb-4">
  <div class="col-md-4">
  <div class="input-group shadow-sm">
    <input type="text" 
           id="cercaInput" 
           class="form-control"
           placeholder="Cerca nome..."
           aria-label="Cerca"
           style="border: 1px solid #bdc3c7; padding: 8px;">
    <button type="button" 
            onclick="cercaNomi()"
            style="background: #07406b; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; transition: all 0.2s ease;">
        <i class="fas fa-search"></i>
        Cerca
    </button>
</div>
  </div>
</div>
        <table class="tbl_generica">
            <thead>
                <tr>
                <td class="cell-xs">
    <div align="center"> 
        <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes()">
    </div>
</td>
                    <td class="cell-md"><div align="center">Cod. fiscale</div></td>
                    <td class="cell-sm"><div align="center">Grado</div></td>
                    <td class="cell-xs"><div align="center">F.A.</div></td>
                    <td class="cell-lg"><div align="center">Cognome</div></td>
                    <td class="cell-md"><div align="center">Nome</div></td>
                    <td class="cell-lg"><div align="center">UO</div></td>
                    <td class="cell-sm"><div align="center">In forza</div></td>
                    <td class="cell-xs"><div align="center">Modifica</div></td>
                </tr>
            </thead>
            <tbody>
                <?php do { ?>
                <tr>
                    <td><div align="center">
                        <input name="checkbox[]" type="checkbox" value="<?php echo $row_Nomi['IDnome']; ?>">
                    </div></td>
                    <td><?php echo $row_Nomi['CF']; ?></td>
                    <td><div align="center"><?php echo $row_Nomi['Grado']; ?></div></td>
                    <td><div align="center"><?php echo $row_Nomi['FA']; ?></div></td>
                    <td><div align="center"><?php echo $row_Nomi['Cognome']; ?></div></td>
                    <td><div align="center"><?php echo $row_Nomi['Nome']; ?></div></td>
                    <td><div align="center"><?php echo $row_Nomi['DEN_UN_OPER']; ?></div></td>
                    <td><div align="center">
                        <select name="forza_<?php echo $row_Nomi['IDnome']; ?>" onChange="this.form.submit()">
                            <option value="1" <?php if (!(strcmp(1, $row_Nomi['Forza']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="0" <?php if (!(strcmp(0, $row_Nomi['Forza']))) {echo "SELECTED";} ?>>No</option>
                        </select>
                    </div></td>
                    <td><div align="center"><a href="ModAnagrafiche.php?ID_pers=<?php echo $row_Nomi['IDnome'];?>">Modifica</a></div></td>
                </tr>
                <?php } while ($row_Nomi = mysqli_fetch_assoc($Nomi)); ?>
            </tbody>
        </table>
        <?php } // Show if recordset not empty ?>        
        
</div>
        <div class="actions-container">
            <form name="form4" method="post" action="">
                <p><span>Dei record selezionati esegui: 
                    <select name="UO" id="UO">
                        <?php
                        do {  
                        ?>
                        <option value="<?php echo $row_UO['ID_UO']?>"><?php echo $row_UO['DEN_UN_OPER']?></option>
                        <?php
                        } while ($row_UO = mysqli_fetch_assoc($UO));
                        $rows = $UO->num_rows;
                        if($rows > 0) {
                            mysqli_data_seek($UO, 0);
                            $row_UO = mysqli_fetch_assoc($UO);
                        }
                        ?>
                    </select>
                    <input type="submit" name="Submit" value="Cambio UO">
                </span>       
                <span>
                <input type="submit" name="Submit" value="Esegui perdita di forza">
                <input type="submit" name="Submit" value="Perdita di forza globale">
                <input type="submit" name="Submit" value="Cancellazione definitiva record">
                </span></p>
            </form>
        </div>
        <?php } ?>
    </div>
    <script>
        function cercaNomi() {
    let input = document.getElementById("cercaInput");
    let filter = input.value.toUpperCase();
    let table = document.querySelector(".tbl_generica");
    let tr = table.getElementsByTagName("tr");

    // Inizia da 1 per saltare l'header
    for (let i = 1; i < tr.length; i++) {
        let cognome = tr[i].getElementsByTagName("td")[4]; // Colonna Cognome
        let nome = tr[i].getElementsByTagName("td")[5];    // Colonna Nome
        let cf = tr[i].getElementsByTagName("td")[1];      // Colonna CF
        
        if (cognome && nome && cf) {
            let testoCompleto = cognome.textContent + " " + 
                              nome.textContent + " " + 
                              cf.textContent;
            
            if (testoCompleto.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const cercaInput = document.getElementById('cercaInput');
    if (cercaInput) {
        cercaInput.addEventListener('keypress', (e) => {
            // Controlla se il tasto premuto è Invio (codice 13)
            if (e.key === 'Enter') {
                // Previene il comportamento predefinito del form
                e.preventDefault();
                // Esegue la ricerca
                cercaNomi();
            }
        });
    }
});
    // Attende che il DOM sia completamente caricato
    document.addEventListener('DOMContentLoaded', () => {
        try {
            // Funzione per impostare il target dei link
            const setupLinks = () => {
                const links = document.getElementsByTagName('a');
                Array.from(links).forEach(link => {
                    if (link) link.target = 'contentFrame';
                });
            };

            // Funzione per impostare il target dei form
            const setupForms = () => {
                const forms = document.getElementsByTagName('form');
                Array.from(forms).forEach(form => {
                    if (form) form.target = 'contentFrame';
                });
            };

            // Funzione per gestire la selezione/deselezione di tutte le checkbox
            window.toggleAllCheckboxes = () => {
                try {
                    const selectAllCheckbox = document.getElementById('selectAll');
                    if (!selectAllCheckbox) return;

                    const checkboxes = document.getElementsByName('checkbox[]');
                    if (!checkboxes.length) return;

                    const isChecked = selectAllCheckbox.checked;
                    checkboxes.forEach(checkbox => {
                        if (checkbox) checkbox.checked = isChecked;
                    });
                } catch (error) {
                    console.error('Errore durante la gestione delle checkbox:', error);
                }
            };

            // Inizializzazione
            setupLinks();
            setupForms();

            // Aggiunge event listener per la checkbox "seleziona tutto"
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', toggleAllCheckboxes);
            }

        } catch (error) {
            console.error('Errore durante l\'inizializzazione:', error);
        }
    });
</script>
</body>
</html>
<?php
mysqli_free_result($Nomi);
mysqli_free_result($UO);
?>