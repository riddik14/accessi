<?php
require_once('Connections/MyPresenze.php');
session_start();

// Aggiungi questo codice dopo session_start()
if (isset($_POST['delete_photo'])) {
    $id = $_POST['IDnome'];
    
    // Recupera il percorso della foto attuale
    $query = "SELECT Foto FROM pre_elenconomi WHERE IDnome = ?";
    $stmt = $PRES_conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Se esiste una foto, elimina il file fisico
    if (!empty($row['Foto']) && file_exists($row['Foto'])) {
        unlink($row['Foto']);
    }
    
    // Aggiorna il database impostando il campo Foto a NULL
    $updateSQL = "UPDATE pre_elenconomi SET Foto = NULL WHERE IDnome = ?";
    $stmt = $PRES_conn->prepare($updateSQL);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    echo '<script>
        alert("Foto eliminata con successo");
        window.location.href = "' . $_SERVER['PHP_SELF'] . '";
    </script>';
    exit();
}

if (isset($_GET['IDrecord'])) {
    $_SESSION['IDnome'] = $_GET['IDrecord'];
} elseif (isset($_POST['IDrecord'])) {
    $_SESSION['IDnome'] = $_POST['IDrecord'];
}


if (isset($_SESSION['redirect_id'])) {
    $IDap_Apparati = $_SESSION['redirect_id'];
    unset($_SESSION['redirect_id']); // Pulisci la sessione dopo l'uso
}

if (!isset($_SESSION['IDnome']) && isset($_POST['IDrecord'])) {
    $_SESSION['IDnome'] = $_POST['IDrecord'];
}

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
    $theValue = addslashes($theValue); // Always apply addslashes since magic_quotes_gpc no longer exists

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

// Gestione form di aggiornamento
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Update anagrafica
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    $updateSQL = sprintf("UPDATE pre_elenconomi SET 
        Cognome=%s, Nome=%s, UO=%s, Forza=%s, 
        TipoRazione=%s, CF=%s, SedeSomm=%s, Categoria=%s, 
        IDgrado=%s, TipoOrario=%s, TipoRazioneCe=%s, 
        TipoRazioneCol=%s, SISME=%s, FA=%s WHERE IDnome=%s",
        GetSQLValueString($_POST['cognome'], "text"),
        GetSQLValueString($_POST['nome'], "text"),
        GetSQLValueString($_POST['UO'], "int"),
        GetSQLValueString($_POST['forza'], "int"),
        GetSQLValueString($_POST['TipoRazione'], "int"), 
        GetSQLValueString($_POST['CF'], "text"),
        GetSQLValueString($_POST['Sede'], "int"),
        GetSQLValueString($_POST['select'], "text"),
        GetSQLValueString($_POST['Grado'], "int"),
        GetSQLValueString($_POST['orario'], "int"),
        GetSQLValueString($_POST['TipoRazioneCe'], "int"),
        GetSQLValueString($_POST['TipoRazioneCol'], "int"),
        GetSQLValueString(isset($row_Nomi['SISME']) ? $row_Nomi['SISME'] : '', "text"),
        GetSQLValueString($_POST['FA'], "text"),
        GetSQLValueString($_POST['IDun'], "int"));

    $Result1 = $PRES_conn->query($updateSQL);
    echo '<script>alert("Modifiche salvate con successo.");
    window.location.href = "/ACCESSI/ADMIN/GestAnagrafiche2.php";
    </script>';
    exit();
}

// Query per recuperare i dati
$query_Nomi = sprintf("SELECT n.*, u.DEN_UN_OPER 
    FROM pre_elenconomi n
    JOIN pre_uo u ON u.ID_UO = n.UO 
    WHERE n.IDnome = '%s'", 
    isset($_POST['IDrecord']) ? $_POST['IDrecord'] : $_SESSION['IDnome']);

$Nomi = $PRES_conn->query($query_Nomi);
$row_Nomi = mysqli_fetch_assoc($Nomi);

// Query per le altre tabelle necessarie
$query_UO = "SELECT ID_UO, COD_UN_OPER, DEN_UN_OPER FROM pre_uo ORDER BY DEN_UN_OPER";
$UO = $PRES_conn->query($query_UO);
$row_UO = mysqli_fetch_assoc($UO);

$query_Sedi = "SELECT IDsede, SEDE FROM pre_sedi";
$Sedi = $PRES_conn->query($query_Sedi);
$row_Sedi = mysqli_fetch_assoc($Sedi);

$query_TipoRazioni = "SELECT ID, TipoRazione FROM pre_tiporazione";
$TipoRazioni = $PRES_conn->query($query_TipoRazioni); 
$row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni);

$query_Categorie = "SELECT IDcat, Categoria FROM pre_categorie";
$Categorie = $PRES_conn->query($query_Categorie);
$row_Categorie = mysqli_fetch_assoc($Categorie);

$query_Gradi = "SELECT ID, Grado FROM pre_gradi ORDER BY Ordinamento";
$Gradi = $PRES_conn->query($query_Gradi);
$row_Gradi = mysqli_fetch_assoc($Gradi);

$query_Orario = "SELECT TIPO_ORARIO, DESCRIZIONE FROM pre_orari GROUP BY TIPO_ORARIO, DESCRIZIONE";
$Orario = $PRES_conn->query($query_Orario);
$row_Orario = mysqli_fetch_assoc($Orario);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Modifica Anagrafica</title>
    <link rel="stylesheet" href="style\fonta\css\all.min.css">
    <style>
        body {
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #FFFFFF;
        background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
        background-size: cover;
        margin: 0;
        padding: 20px;
        min-height: 100vh;
    }

    .container {
    max-width: 1000px; /* Ridotto da 1200px */
    margin: 0 auto;
    background: rgba(32, 77, 98, 255);
    padding: 20px; /* Ridotto da 25px */
    border-radius: 10px;
}

.photo-section {
    float: left;
    width: 200px;
    margin-right: 20px;
    margin-top: 40px; /* Aggiunto per spostare in basso */
}

.photo-box {
    background: linear-gradient(145deg, #1a3f66, #07406b);
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    margin-bottom: 20px;
    margin-top: 10px; /* Aggiunto per aumentare lo spazio sopra */
}

    .photo-container {
    background: #fff;
    padding: 8px;
    border-radius: 4px;
    margin-bottom: 15px;
    margin-top: 15px; /* Aggiunto per aumentare lo spazio sopra */
}

    .photo-container img {
    width: 100%;
    height: 250px; /* Ridotto da 250px */
    object-fit: cover;
    border-radius: 4px;
}

    .form-content {
        margin-left: 280px;
    }

    .grid-form {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #fff;
    }

    select, input[type="text"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fff;
        color: #333;
    }

    select:focus, input[type="text"]:focus {
        border-color: #07406b;
        outline: none;
        box-shadow: 0 0 5px rgba(7,64,107,0.5);
    }

    .btn {
        background: linear-gradient(to bottom, #07406b, #052c4a);
        color: #fff;
        padding: 12px 25px;
        border: 1px solid #fff;
        border-radius: 5px;
        cursor: pointer;
        text-shadow: 1px 1px 1px #000;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn:hover {
        background: linear-gradient(to bottom, #052c4a, #07406b);
        transform: translateY(-2px);
    }
    .back-button-container {
         position: fixed;
         top: 40px; /* Aumentato da 20px a 30px per abbassare leggermente */
         left: 20px;
         z-index: 1000;
         }
         .back-button {
         background-color: #07406b;
         color: #ffffff;
         padding: 8px 10px;
         border: 2px solid #ffffff;
         border-radius: 50%;
         cursor: pointer;
         display: flex;
         align-items: center;
         justify-content: center;
         transition: all 0.3s ease;
         width: 35px;
         height: 35px;
         text-decoration: none;
         }
         .back-button:hover {
         background-color: #0a5289;
         transform: scale(1.1);
         box-shadow: 0 0 10px rgba(255,255,255,0.3);
         }
         .back-button i {
         font-size: 16px;
         }
    
    </style>
</head>
<body>
    <div class="back-button-container">
         <a href="/ACCESSI/ADMIN/GestAnagrafiche2.php" class="back-button">
         <i class="fas fa-arrow-left"></i>
         </a>
      </div>
    <h3 align="center"><span class="Stile4"><font face="Verdana">Modifica anagrafica utenti</font></span></h3>
    <div class="container">
        <div class="photo-section">
            <form action="InvioFoto.php" method="post" name="form3" target="_blank">
                <div class="photo-container">
                    <img src="<?php echo $row_Nomi['Foto']; ?>" alt="Foto utente">
                </div>
                <input type="submit" name="Submit" value="Inserisci foto" class="btn" style="width: 100%; margin-bottom: 10px;">
                <input name="IDnome" type="hidden" value="<?php 
                    echo $row_Nomi['IDnome'] . " - " . (isset($_POST['IDrecord']) ? $_POST['IDrecord'] : ''); 
                ?>">
            </form>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form4">
                <input type="hidden" name="IDnome" value="<?php echo $row_Nomi['IDnome']; ?>">
                <input type="submit" name="delete_photo" value="Elimina foto" class="btn" style="width: 100%; background: linear-gradient(to bottom, #8b0000, #5c0000);">
            </form>
        </div>
        <div class="form-content">
            <form method="POST" action="<?php echo $editFormAction; ?>">
                <div class="grid-form">
                    <div class="form-group">
                        <label>F.A.</label>
                        <select name="FA">
                            <option value="EI" <?php if ($row_Nomi['FA'] == 'EI') echo 'selected'; ?>>EI</option>
                            <option value="AM" <?php if ($row_Nomi['FA'] == 'AM') echo 'selected'; ?>>AM</option>
                            <option value="MM" <?php if ($row_Nomi['FA'] == 'MM') echo 'selected'; ?>>MM</option>
                            <option value="CC" <?php if ($row_Nomi['FA'] == 'CC') echo 'selected'; ?>>CC</option>
                            <option value="CIV" <?php if ($row_Nomi['FA'] == 'CIV') echo 'selected'; ?>>CIV</option>
                            <option value="STRA" <?php if ($row_Nomi['FA'] == 'STRA') echo 'selected'; ?>>STRA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Comando/Ufficio</label>
                        <select name="UO">
                            <?php do { ?>
                                <option value="<?php echo $row_UO['ID_UO']?>" <?php if ($row_UO['ID_UO'] == $row_Nomi['UO']) echo 'selected'; ?>>
                                    <?php echo $row_UO['DEN_UN_OPER']?>
                                </option>
                            <?php } while ($row_UO = mysqli_fetch_assoc($UO)); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Grado</label>
                        <select name="Grado">
                            <?php do { ?>
                                <option value="<?php echo $row_Gradi['ID']?>" <?php if ($row_Gradi['ID'] == $row_Nomi['IDgrado']) echo 'selected'; ?>>
                                    <?php echo $row_Gradi['Grado']?>
                                </option>
                            <?php } while ($row_Gradi = mysqli_fetch_assoc($Gradi)); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>In Forza</label>
                        <select name="forza">
                            <option value="1" <?php if ($row_Nomi['Forza'] == 1) echo 'selected'; ?>>Si</option>
                            <option value="0" <?php if ($row_Nomi['Forza'] == 0) echo 'selected'; ?>>No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Categoria</label>
                        <select name="select">
                            <?php do { ?>
                                <option value="<?php echo $row_Categorie['IDcat']?>" <?php if ($row_Categorie['IDcat'] == $row_Nomi['Categoria']) echo 'selected'; ?>>
                                    <?php echo $row_Categorie['Categoria']?>
                                </option>
                            <?php } while ($row_Categorie = mysqli_fetch_assoc($Categorie)); ?>
                        </select>
                    </div>

                    <div class="form-group">
    <label>Orario</label>
    <select name="orario">
        <option value="">-- Seleziona Orario --</option>
        <?php 
        if ($row_Orario) { // Verifica se ci sono risultati
            do { 
                if ($row_Orario['TIPO_ORARIO'] !== null) { // Verifica che il valore non sia null
        ?>
                <option value="<?php echo $row_Orario['TIPO_ORARIO']; ?>" 
                    <?php if (isset($row_Nomi['TipoOrario']) && $row_Orario['TIPO_ORARIO'] == $row_Nomi['TipoOrario']) echo 'selected'; ?>>
                    <?php echo $row_Orario['DESCRIZIONE']; ?>
                </option>
        <?php 
                }
            } while ($row_Orario = mysqli_fetch_assoc($Orario));
        }
        ?>
    </select>
</div>

                    <div class="form-group">
                        <label>Cognome</label>
                        <input type="text" name="cognome" value="<?php echo $row_Nomi['Cognome']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" value="<?php echo $row_Nomi['Nome']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Codice Fiscale</label>
                        <input type="text" name="CF" value="<?php echo $row_Nomi['CF']; ?>" maxlength="16">
                    </div>

                    <div class="form-group">
                        <label>Sede Consumazione Pasti</label>
                        <select name="Sede">
                            <?php do { ?>
                                <option value="<?php echo $row_Sedi['IDsede']?>" <?php if ($row_Sedi['IDsede'] == $row_Nomi['SedeSomm']) echo 'selected'; ?>>
                                    <?php echo $row_Sedi['SEDE']?>
                                </option>
                            <?php } while ($row_Sedi = mysqli_fetch_assoc($Sedi)); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Razione Pranzo</label>
                        <select name="TipoRazione">
                            <?php do { ?>
                                <option value="<?php echo $row_TipoRazioni['ID']?>" <?php if ($row_TipoRazioni['ID'] == $row_Nomi['TipoRazione']) echo 'selected'; ?>>
                                    <?php echo $row_TipoRazioni['TipoRazione']?>
                                </option>
                            <?php } while ($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni)); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Razione Cena</label>
                        <select name="TipoRazioneCe">
                            <?php 
                            mysqli_data_seek($TipoRazioni, 0);
                            while ($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni)) { ?>
                                <option value="<?php echo $row_TipoRazioni['ID']?>" <?php if ($row_TipoRazioni['ID'] == $row_Nomi['TipoRazioneCe']) echo 'selected'; ?>>
                                    <?php echo $row_TipoRazioni['TipoRazione']?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Razione Colazione</label>
                        <select name="TipoRazioneCol">
                            <?php 
                            mysqli_data_seek($TipoRazioni, 0);
                            while ($row_TipoRazioni = mysqli_fetch_assoc($TipoRazioni)) { ?>
                                <option value="<?php echo $row_TipoRazioni['ID']?>" <?php if ($row_TipoRazioni['ID'] == $row_Nomi['TipoRazioneCol']) echo 'selected'; ?>>
                                    <?php echo $row_TipoRazioni['TipoRazione']?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="IDun" value="<?php echo $row_Nomi['IDnome']; ?>">
                <input type="hidden" name="MM_update" value="form1">
                
                <div class="form-group" style="text-align: center; margin-top: 20px;">
                    <button type="submit" class="btn">Salva Modifiche</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
mysqli_free_result($Nomi);
mysqli_free_result($UO);
mysqli_free_result($Sedi);
mysqli_free_result($TipoRazioni);
mysqli_free_result($Categorie);
mysqli_free_result($Gradi);
mysqli_free_result($Orario);
?>