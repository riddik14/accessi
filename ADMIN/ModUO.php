<?php require_once('../Connections/MyPresenze.php'); ?>
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
  $updateSQL = sprintf("UPDATE pre_uo SET SEDE=%s WHERE ID_UO=%s",
                       GetSQLValueString($_POST['Sede'], "int"),
                       GetSQLValueString($_POST['ID'], "int"));
  $Result1 = $PRES_conn->query($updateSQL);
}

$query_UO = "Select pre_uo.ID_UO, pre_uo.COD_UN_OPER, pre_uo.DEN_UN_OPER, pre_uo.SEDE, pre_uo.RANGE_IN_DA, pre_uo.RANGE_IN_A, pre_uo.RANGE_OUT_DA,
             pre_uo.RANGE_OUT_A, pre_sedi.SEDE, Count(pre_elenconomi.IDnome) As NPers
             From pre_uo Left Join pre_sedi On pre_uo.SEDE = pre_sedi.IDsede 
             Left Join pre_elenconomi On pre_uo.ID_UO = pre_elenconomi.UO
             Group By pre_uo.ID_UO, pre_uo.COD_UN_OPER, pre_uo.DEN_UN_OPER, pre_uo.SEDE, pre_uo.RANGE_IN_DA, pre_uo.RANGE_IN_A, pre_uo.RANGE_OUT_DA,
             pre_uo.RANGE_OUT_A, pre_sedi.SEDE";

$UO = $PRES_conn->query($query_UO);
$row_UO = mysqli_fetch_assoc($UO);

$query_Sedi = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sedi = $PRES_conn->query($query_Sedi);
$row_Sedi = mysqli_fetch_assoc($Sedi);
$totalRows_Sedi = $Sedi->num_rows;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gest. UO</title>
    <style>
:root {
  /* Sistema colori */
  --primary: #07406b;
  --primary-light: #0a5185;
  --primary-hover: #0c5c96;
  --white: #ffffff;
  --border-color: rgba(255, 255, 255, 0.2);
  --shadow: rgba(0, 0, 0, 0.2);

  /* Sistema spaziature */
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 16px;
  --space-lg: 24px;
  --space-xl: 32px;
  --content-margin: 20px;
}

/* Reset e stili base */
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  margin: 0;
  padding: var(--space-xl) 0 0 var(--content-margin);
  background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
  color: var(--white);
  min-height: 100vh;
}

/* Layout principale */
.container {
  max-width: 1200px;
  margin: 0;
  padding: var(--space-sm);
}

/* Bottone principale */
.button {
  padding: var(--space-md) var(--space-xl);
  margin: var(--space-lg) 0 var(--space-lg) var(--content-margin);
  background-color: var(--primary-light);
  color: var(--white);
  border: none;
  border-radius: 8px;
  font-size: 18px;
  font-weight: bold;
  text-transform: uppercase;
  min-width: 280px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-shadow: 1px 1px 1px #000;
}

.button:hover {
  background-color: var(--primary-hover);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px var(--shadow);
}

/* Container tabella */
.table-container {
  max-height: 70vh;
  overflow: auto;
  margin: var(--space-xl) 0 0 var(--content-margin);
  border-radius: 8px;
  box-shadow: 0 4px 12px var(--shadow);
  max-width: calc(95% - var(--content-margin));
}

/* Scrollbar personalizzata */
.table-container::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

.table-container::-webkit-scrollbar-track {
  background: var(--primary);
  border-radius: 8px;
}

.table-container::-webkit-scrollbar-thumb {
  background: var(--primary-light);
  border-radius: 8px;
}

.table-container::-webkit-scrollbar-thumb:hover {
  background: var(--primary-hover);
}

/* Tabella */
.tbl_generica {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background-color: var(--primary);
  font-size: 14px;
}

.tbl_generica thead {
  position: sticky;
  top: 0;
  z-index: 1;
}

.tbl_generica th {
  padding: var(--space-sm) var(--space-md);
  background-color: var(--primary-light);
  color: var(--white);
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.85em;
  letter-spacing: 0.5px;
  white-space: nowrap;
  border: 1px solid var(--border-color);
}

.tbl_generica td {
  padding: var(--space-sm) var(--space-md);
  border: 1px solid var(--border-color);
  color: var(--white);
  vertical-align: middle;
}

.tbl_generica tbody tr {
  transition: background-color 0.3s;
}

.tbl_generica tbody tr:hover {
  background-color: rgba(10, 81, 133, 0.7);
}

/* Elementi form */
.tbl_generica input[type="submit"] {
  padding: var(--space-sm) var(--space-md);
  background-color: var(--white);
  color: var(--primary);
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
  text-transform: uppercase;
  font-size: 0.8em;
  letter-spacing: 0.5px;
}

.tbl_generica input[type="submit"]:hover {
  background-color: #f0f0f0;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px var(--shadow);
}

/* Responsive */
@media (max-width: 1200px) {
  :root {
    --content-margin: 10px;
  }
  
  .tbl_generica {
    font-size: 13px;
  }
}

@media (max-width: 768px) {
  :root {
    --content-margin: 5px;
  }
  
  body {
    padding: var(--space-lg) 0 0 var(--content-margin);
  }
  
  .button {
    width: calc(100% - 2 * var(--content-margin));
    margin: var(--space-sm) var(--content-margin);
  }
  
  .table-container {
    margin: var(--space-sm) var(--content-margin);
  }
  
  .tbl_generica td,
  .tbl_generica th {
    padding: var(--space-xs) var(--space-sm);
  }
}    </style>
</head>
<body>
        <input type="button" value="Inserisci nuova UO" onclick="location.href='UOINS.php'" class="button">
        <font face="Verdana">
        <div class="table-container">
            <table class="tbl_generica">
                <thead>
                    <tr>
                        <th><strong>UO</strong></th>
                        <th colspan="2"><div align="center"><strong>Range tolleranza orari ingresso </strong></div></th>
                        <th colspan="2"><div align="center"><strong>Range tolleranza orari uscita </strong></div></th>
                        <th><div align="center">N amministrati</div></th>
                        <th><div align="center"><strong>Sede</strong></div></th>
                        <th><div align="center"><strong>Modifica</strong></div></th>
                    </tr>
                </thead>
                <tbody>
                    <?php do { ?>
                    <tr>
                        <td><div align="center"><?php echo $row_UO['DEN_UN_OPER']; ?></div></td>
                        <td><div align="center">dalle <?php echo $row_UO['RANGE_IN_DA']; ?></div></td>
                        <td><div align="center">alle <?php echo $row_UO['RANGE_IN_A']; ?></div></td>
                        <td><div align="center">dalle <?php echo $row_UO['RANGE_OUT_DA']; ?></div></td>
                        <td><div align="center">alle <?php echo $row_UO['RANGE_OUT_A']; ?></div></td>
                        <td align="center"><?php echo $row_UO['NPers']; ?></td>
                        <td><div align="center"><?php echo $row_UO['SEDE']; ?></div></td>
                        <td><div align="center">
                            <form action="UODETT.php" method="post">
                                <input name="ID_UO" type="hidden" value="<?php echo $row_UO['ID_UO']; ?>">
                                <input type="submit" name="Submit" value="Modifica">
                            </form>
                        </div></td>
                    </tr>
                    <?php } while ($row_UO = mysqli_fetch_assoc($UO)); ?>
                </tbody>
            </table>
        </div>
        </font>
    </div>
    <script>
        // Assicurati che tutti i link rimangano nell'iframe
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.getElementsByTagName('a');
            Array.from(links).forEach(link => {
                link.target = 'contentFrame';
            });
            
            const forms = document.getElementsByTagName('form');
            Array.from(forms).forEach(form => {
                form.target = 'contentFrame';
            });
        });
    </script>
</body>
</html>
<?php
mysqli_free_result($UO);
mysqli_free_result($Sedi);
?>
