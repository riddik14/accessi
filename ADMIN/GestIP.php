<?php require_once('../Connections/MyPresenze.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO pre_ip (IP, Descr) VALUES (%s, %s)",
                       GetSQLValueString($_POST['ip'], "text"),
                       GetSQLValueString($_POST['descrizione'], "text"));
  $Result1 = $PRES_conn->query($insertSQL);
}


$query_IP = "SELECT pre_ip.ID, pre_ip.IP, pre_ip.Descr FROM pre_ip";
$IP = $PRES_conn->query($query_IP);
$row_IP = mysqli_fetch_assoc($IP);
$totalRows_IP = $IP->num_rows;


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  if (isset($_POST["TastoCancella"]) && $_POST["TastoCancella"] == "Elimina") {
      // Esegue la query di eliminazione
      $deleteSQL = sprintf("DELETE FROM pre_ip WHERE ID=%s",
          GetSQLValueString($_POST['ID'], "int"));
      $Result1 = $PRES_conn->query($deleteSQL);
      
      if ($Result1) {
          // Redirect dopo l'eliminazione
          header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
          exit();
      }
  } elseif (isset($_POST["TastoSalva"]) && $_POST["TastoSalva"] == "Salva") {
      $updateSQL = sprintf("UPDATE pre_ip SET IP=%s, Descr=%s WHERE ID=%s",
                     GetSQLValueString($_POST['ip'], "text"),
                     GetSQLValueString($_POST['descr'], "text"),
                     GetSQLValueString($_POST['ID'], "int"));
      $Result1 = $PRES_conn->query($updateSQL);
      
      if ($Result1) {
          // Redirect dopo il salvataggio
          header("Location: " . $_SERVER['PHP_SELF'] . "?updated=1");
          exit();
      }
  }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestione terminali</title>
    <style>
        :root {
    --primary: #07406b;
    --primary-light: #0a5185;
    --white: #ffffff;
    --border: rgba(255, 255, 255, 0.2);
    --shadow: rgba(0, 0, 0, 0.2);
    --danger: #dc3545;
    --danger-hover: #c82333;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
    background-size: cover;
    color: var(--white);
    min-height: 100vh;
}

.container {
    max-width: 1400px;
    margin: 100px 50 50 20px;;
    padding: 20px;
}

h3, h4 {
    color: var(--white);
    margin-bottom: 1.5rem;
    text-shadow: 1px 1px 2px var(--shadow);
    font-size: 1.5rem;
}

.section-title {
    font-size: 1.8rem;
    border-bottom: 2px solid var(--white);
    padding-bottom: 10px;
}

.form-title {
    margin-top: 40px;
}

.tbl_generica {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background-color: var(--primary);
    box-shadow: 0 4px 6px var(--shadow);
    border-radius: 8px;
    overflow: hidden;
}

.tbl_generica th {
    background-color: var(--primary-light);
    padding: 15px;
    text-align: left;
    font-weight: bold;
    border: 1px solid var(--border);
    color: var(--white);
}

.tbl_generica td {
    padding: 12px;
    border: 1px solid var(--border);
    color: var(--white);
    vertical-align: middle;
}

.tbl_generica td:last-child {
    width: 800px;
}

.form-row {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: nowrap;
}

input[type="text"] {
    padding: 10px 15px;
    border: 1px solid var(--border);
    border-radius: 4px;
    background-color: var(--white);
    color: var(--primary);
    font-size: 14px;
    width: 300px;
    transition: all 0.3s ease;
}

input[name="descr"] {
    width: 400px;
}

input[type="text"]:focus {
    outline: none;
    box-shadow: 0 0 0 2px var(--primary-light);
}

input[type="submit"],
button {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    background-color: var(--primary-light);
    color: var(--white);
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 14px;
    min-width: 100px;
}

input[type="submit"]:hover,
button:hover {
    background-color: #0c5c96;
    transform: translateY(-1px);
}

.save-btn {
    background-color: var(--primary-light);
}

.delete-btn {
    background-color: var(--danger);
}

.delete-btn:hover {
    background-color: var(--danger-hover);
}

.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: nowrap;
    justify-content: flex-end;
    min-width: 800px;
    align-items: center;
}

form.tbl_generica {
    background-color: var(--primary);
    padding: 25px;
    border-radius: 8px;
    margin-top: 20px;
    max-width: 400px;
    margin-left: 0;
}
form.tbl_generica .form-row:last-child {
    justify-content: center; /* Centra l'ultimo form-row che contiene il pulsante */
    margin-top: 25px; /* Aggiunge spazio sopra il pulsante */
}
form.tbl_generica input[type="submit"] {
    min-width: 120px; /* Larghezza minima del pulsante */
    text-align: center;
}

.form-row label {
    min-width: 120px;
    color: var(--white);
    font-weight: 500;
}

@media (max-width: 1200px) {
    .container {
        max-width: 95%;
    }
    
    .action-buttons {
        min-width: auto;
        flex-wrap: wrap;
    }
    
    input[name="descr"],
    input[type="text"] {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-row label {
        margin-bottom: 5px;
    }
    
    .tbl_generica td:last-child {
        width: auto;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 8px;
    }
    
    input[type="submit"],
    button {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <div class="container">
    <h3 class="section-title">PC Abilitati alla Rilevazione Presenze Mensa</h3>

        <?php if ($totalRows_IP > 0) { ?>
        <table class="tbl_generica">
            <thead>
                <tr class="headertbl">
                    <th>IP</th>
                    <th>Descrizione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php do { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row_IP['IP']); ?></td>
                    <td><?php echo htmlspecialchars($row_IP['Descr']); ?></td>
                    <td>
                        <form action="<?php echo $editFormAction; ?>" method="post" class="action-buttons">
                            <input name="ip" type="text" value="<?php echo htmlspecialchars($row_IP['IP']); ?>" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" required>
                            <input name="descr" type="text" value="<?php echo htmlspecialchars($row_IP['Descr']); ?>" required>
                            <input name="TastoSalva" type="submit" value="Salva" class="save-btn">
                            <input name="TastoCancella" type="submit" value="Elimina" class="delete-btn" onclick="return confirm('Sei sicuro di voler eliminare questo record?');">
                            <input name="ID" type="hidden" value="<?php echo $row_IP['ID']; ?>">
                            <input type="hidden" name="MM_update" value="form1">
                        </form>
                    </td>
                </tr>
                <?php } while ($row_IP = mysqli_fetch_assoc($IP)); ?>
            </tbody>
        </table>
        <?php } ?>

        <h3 class="form-title">Inserimento Nuovo Terminale</h3>
        <form name="form2" method="POST" action="<?php echo $editFormAction; ?>" class="tbl_generica">
            <div class="form-row">
                <label for="descrizione">Descrizione:</label>
                <input id="descrizione" name="descrizione" type="text" required>
            </div>
            <div class="form-row">
                <label for="ip">Indirizzo IP:</label>
                <input id="ip" name="ip" type="text" required pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" title="Inserire un indirizzo IP valido (es. 192.168.1.1)">
            </div>
            <div class="form-row">
                <input type="submit" name="Submit" value="Salva">
                <input type="hidden" name="MM_insert" value="form2">
            </div>
        </form>
    </div>
</body>
</html>
<?php
mysqli_free_result($IP);
?>
