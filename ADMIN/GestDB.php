<?php require_once('../Connections/MyPresenze.php'); ?>
<?php

// ******************************** Procedura di bakup da form1 ************************************

if (isset($_POST['MM_form1']) && $_POST['MM_form1'] == "form1") {

$parDal_RecordMensa = "%";
if (isset($_POST['dal'])) {
  $parDal_RecordMensa = addslashes($_POST['dal']);
}
$parAl_RecordMensa = "%";
if (isset($_POST['al'])) {
  $parAl_RecordMensa = addslashes($_POST['al']);
}

// ************************************* Query di accodamento *******************************************

$query_RecordMensa = sprintf("INSERT INTO pre_accessi_bk ( IDrecord, IDnome, GIORNO, PASTO, COD_VAR, Ora_pren, USR, Ti_R, Se, Ora_cons_pr, Cons, Pagamento) SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.COD_VAR, pre_accessi.Ora_pren, pre_accessi.USR, pre_accessi.Ti_R, pre_accessi.Se, pre_accessi.Ora_cons_pr, pre_accessi.Pagamento, pre_accessi.Cons FROM pre_accessi WHERE pre_accessi.GIORNO >='%s' AND pre_accessi.GIORNO <='%s'", $parDal_RecordMensa,$parAl_RecordMensa);
$RecordMensa = $PRES_conn->query($query_RecordMensa);
//$row_RecordMensa = mysqli_fetch_assoc($RecordMensa);
//$totalRows_RecordMensa = mysql_num_rows($RecordMensa);
// *************************************** Query di eliminazione ****************************************

$query_DelRecordMensa = sprintf("DELETE FROM pre_accessi WHERE pre_accessi.GIORNO >='%s' AND pre_accessi.GIORNO <='%s'", $parDal_RecordMensa,$parAl_RecordMensa);
$DelRecordMensa = $PRES_conn->query($query_DelRecordMensa);
//$row_DelRecordMensa = mysqli_fetch_assoc($DelRecordMensa);
//$totalRows_DelRecordMensa = mysql_num_rows($DelRecordMensa);
$PRES_conn->query("OPTIMIZE TABLE pre_accessi") or die($PRES_conn->error);
//header("Location:GestDB.php");
}

// *********************************** Procedura di ripristino dati da form2 ***************************************

if (isset($_POST['MM_form2']) && $_POST['MM_form2'] == "form2") {

$parDal_RecordMensa = "%";
if (isset($_POST['dal'])) {
  $parDal_RecordMensa = addslashes($_POST['dal']);
}
$parAl_RecordMensa = "%";
if (isset($_POST['al'])) {
  $parAl_RecordMensa = addslashes($_POST['al']);
}

// ************************************* Query di accodamento alla tabella pre_accessi *******************************************

$query_RecordMensa = sprintf("INSERT INTO pre_accessi ( IDrecord, IDnome, GIORNO, PASTO, COD_VAR, Ora_pren, USR, Ti_R, Se, Ora_cons_pr, Cons, Pagamento) SELECT pre_accessi_bk.IDrecord, pre_accessi_bk.IDnome, pre_accessi_bk.GIORNO, pre_accessi_bk.PASTO, pre_accessi_bk.COD_VAR, pre_accessi_bk.Ora_pren, pre_accessi_bk.USR, pre_accessi_bk.Ti_R, pre_accessi_bk.Se, pre_accessi_bk.Ora_cons_pr, pre_accessi_bk.Pagamento, pre_accessi_bk.Cons FROM pre_accessi_bk WHERE pre_accessi_bk.GIORNO >='%s' AND pre_accessi_bk.GIORNO <='%s'", $parDal_RecordMensa,$parAl_RecordMensa);
$RecordMensa = $PRES_conn->query($query_RecordMensa);
//$row_RecordMensa = mysqli_fetch_assoc($RecordMensa);
//$totalRows_RecordMensa = mysql_num_rows($RecordMensa);
// *************************************** Query di eliminazione record dalla tabella pre_accessi_bk  ****************************************

$query_DelRecordMensa = sprintf("DELETE FROM pre_accessi_bk WHERE pre_accessi_bk.GIORNO >='%s' AND pre_accessi_bk.GIORNO <='%s'", $parDal_RecordMensa,$parAl_RecordMensa);
$DelRecordMensa = $PRES_conn->query($query_DelRecordMensa);
//$row_DelRecordMensa = mysqli_fetch_assoc($DelRecordMensa);
//$totalRows_DelRecordMensa = mysql_num_rows($DelRecordMensa);
$PRES_conn->query("OPTIMIZE TABLE pre_accessi_bk") or die($PRES_conn->error);
//header("Location:GestDB.php");
}

// ******************************** Procedura di bakup da form4 ************************************

if (isset($_POST['MM_form4']) && $_POST['MM_form4'] == "form4") {

$parDal_RecordMensa = "%";
if (isset($_POST['dal'])) {
  $parDal_RecordMensa = addslashes($_POST['dal']);
}
$parAl_RecordMensa = "%";
if (isset($_POST['al'])) {
  $parAl_RecordMensa = addslashes($_POST['al']);
}

// ************************************* Query di accodamento *******************************************

$query_RecordMensa = sprintf("INSERT INTO pre_orari_gg_bk ( ID, IDnome, ORA_IN, GIORNO, CAUSA, ORA_OUT, ORA_REG_OUT, INTERVALLO, VAL_DA,
							ORA_REG_IN, NOTE, ORA_MOD_IN, ORA_MOD_OUT, ID_MOD_IN, ID_MOD_OUT) 
							SELECT ID, IDnome, ORA_IN, GIORNO, CAUSA, ORA_OUT, ORA_REG_OUT, INTERVALLO, VAL_DA,
							ORA_REG_IN, NOTE, ORA_MOD_IN, ORA_MOD_OUT, ID_MOD_IN, ID_MOD_OUT 
							FROM pre_orari_gg 
							WHERE pre_orari_gg.GIORNO >='%s' AND pre_orari_gg.GIORNO <='%s'", $parDal_RecordMensa,$parAl_RecordMensa);
$RecordMensa = $PRES_conn->query($query_RecordMensa);
//$row_RecordMensa = mysqli_fetch_assoc($RecordMensa);
//$totalRows_RecordMensa = mysql_num_rows($RecordMensa);
// *************************************** Query di eliminazione ****************************************

$query_DelRecordMensa = sprintf("DELETE FROM pre_orari_gg WHERE pre_orari_gg.GIORNO >='%s' AND pre_orari_gg.GIORNO <='%s'", $parDal_RecordMensa,$parAl_RecordMensa);
$DelRecordMensa = $PRES_conn->query($query_DelRecordMensa);
//$row_DelRecordMensa = mysqli_fetch_assoc($DelRecordMensa);
//$totalRows_DelRecordMensa = mysql_num_rows($DelRecordMensa);
$PRES_conn->query("OPTIMIZE TABLE pre_orari_gg") or die($PRES_conn->error);
//header("Location:GestDB.php");
}

// *********************************** Procedura di ripristino dati da form2 ***************************************

if (isset($_POST['MM_form5']) && $_POST['MM_form5'] == "form5") {

$parDal_RecordMensa = "%";
if (isset($_POST['dal'])) {
  $parDal_RecordMensa = addslashes($_POST['dal']);
}
$parAl_RecordMensa = "%";
if (isset($_POST['al'])) {
  $parAl_RecordMensa = addslashes($_POST['al']);
}

// ************************************* Query di accodamento alla tabella pre_accessi *******************************************

$query_RecordMensa = sprintf("INSERT INTO pre_orari_gg ( ID, IDnome, ORA_IN, GIORNO, CAUSA, ORA_OUT, ORA_REG_OUT, INTERVALLO, VAL_DA,
							ORA_REG_IN, NOTE, ORA_MOD_IN, ORA_MOD_OUT, ID_MOD_IN, ID_MOD_OUT) 
							SELECT ID, IDnome, ORA_IN, GIORNO, CAUSA, ORA_OUT, ORA_REG_OUT, INTERVALLO, VAL_DA,
							ORA_REG_IN, NOTE, ORA_MOD_IN, ORA_MOD_OUT, ID_MOD_IN, ID_MOD_OUT 
							FROM pre_orari_gg_bk 
							WHERE pre_orari_gg_bk.GIORNO >='%s' AND pre_orari_gg_bk.GIORNO <='%s'", $parDal_RecordMensa,$parAl_RecordMensa);
$RecordMensa = $PRES_conn->query($query_RecordMensa);
//$row_RecordMensa = mysqli_fetch_assoc($RecordMensa);
//$totalRows_RecordMensa = mysql_num_rows($RecordMensa);
// *************************************** Query di eliminazione record dalla tabella pre_accessi_bk  ****************************************

$query_DelRecordMensa = sprintf("DELETE FROM pre_orari_gg_bk WHERE pre_orari_gg_bk.GIORNO >='%s' AND pre_orari_gg_bk.GIORNO <='%s'", $parDal_RecordMensa,$parAl_RecordMensa);
$DelRecordMensa = $PRES_conn->query($query_DelRecordMensa);
//$row_DelRecordMensa = mysqli_fetch_assoc($DelRecordMensa);
//$totalRows_DelRecordMensa = mysql_num_rows($DelRecordMensa);
$PRES_conn->query("OPTIMIZE TABLE pre_orari_gg_bk") or die($PRES_conn->error);
//header("Location:GestDB.php");
}

$query_GiornoMassimo = "SELECT pre_accessi.GIORNO, date_format(pre_accessi.GIORNO,'%d/%m/%Y') AS GIO FROM pre_accessi GROUP BY pre_accessi.GIORNO ORDER BY pre_accessi.GIORNO DESC";
$GiornoMassimo = $PRES_conn->query($query_GiornoMassimo);
$row_GiornoMassimo = mysqli_fetch_assoc($GiornoMassimo);

$query_GiornoMinimo = "SELECT pre_accessi.GIORNO, date_format(pre_accessi.GIORNO,'%d/%m/%Y') AS GIO FROM pre_accessi GROUP BY pre_accessi.GIORNO ORDER BY pre_accessi.GIORNO";
$GiornoMinimo = $PRES_conn->query($query_GiornoMinimo);
$row_GiornoMinimo = mysqli_fetch_assoc($GiornoMinimo);

$query_GiornoMassimoBK = "SELECT pre_accessi_bk.GIORNO, date_format(pre_accessi_bk.GIORNO,'%d/%m/%Y') AS GIO FROM pre_accessi_bk GROUP BY pre_accessi_bk.GIORNO ORDER BY pre_accessi_bk.GIORNO DESC";
$GiornoMassimoBK = $PRES_conn->query($query_GiornoMassimoBK);
$row_GiornoMassimoBK = mysqli_fetch_assoc($GiornoMassimoBK);

$query_GiornoMinimoBK = "SELECT pre_accessi_bk.GIORNO, date_format(pre_accessi_bk.GIORNO,'%d/%m/%Y') AS GIO FROM pre_accessi_bk GROUP BY pre_accessi_bk.GIORNO ORDER BY pre_accessi_bk.GIORNO";
$GiornoMinimoBK = $PRES_conn->query($query_GiornoMinimoBK);
$row_GiornoMinimoBK = mysqli_fetch_assoc($GiornoMinimoBK);

$query_PresMassimo = "SELECT pre_orari_gg.GIORNO, date_format(pre_orari_gg.GIORNO,'%d/%m/%Y') AS GIO FROM pre_orari_gg GROUP BY pre_orari_gg.GIORNO ORDER BY pre_orari_gg.GIORNO DESC";
$PresMassimo = $PRES_conn->query($query_PresMassimo);
$row_PresMassimo = mysqli_fetch_assoc($PresMassimo);

$query_PresMinimo = "SELECT pre_orari_gg.GIORNO, date_format(pre_orari_gg.GIORNO,'%d/%m/%Y') AS GIO FROM pre_orari_gg GROUP BY pre_orari_gg.GIORNO ORDER BY pre_orari_gg.GIORNO";
$PresMinimo = $PRES_conn->query($query_PresMinimo);
$row_PresMinimo = mysqli_fetch_assoc($PresMinimo);

$query_PreMassimoBk = "SELECT pre_orari_gg_bk.GIORNO, date_format(pre_orari_gg_bk.GIORNO,'%d/%m/%Y') AS GIO FROM pre_orari_gg_bk GROUP BY pre_orari_gg_bk.GIORNO ORDER BY pre_orari_gg_bk.GIORNO DESC";
$PreMassimoBk = $PRES_conn->query($query_PreMassimoBk);
$row_PreMassimoBk = mysqli_fetch_assoc($PreMassimoBk);

$query_PreMinimoBk = "SELECT pre_orari_gg_bk.GIORNO, date_format(pre_orari_gg_bk.GIORNO,'%d/%m/%Y') AS GIO FROM pre_orari_gg_bk GROUP BY pre_orari_gg_bk.GIORNO ORDER BY pre_orari_gg_bk.GIORNO";
$PreMinimoBk = $PRES_conn->query($query_PreMinimoBk);
$row_PreMinimoBk = mysqli_fetch_assoc($PreMinimoBk);

?>

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Gestione DB ADMIN</title>
    <link rel="stylesheet" href="\ACCESSI\style\fonta\css\all.css">
    <style>
        :root {
    /* Colori principali */
    --primary: #07406b;
    --primary-light: #0a5185;
    --white: #ffffff;
    --text-light: #e0e0e0;
    --warning-bg: #1a4d73;
    --warning-text: #ffd700;
    --border: #2c5f8a;
    --shadow: rgba(0, 0, 0, 0.2);
    
    /* Font */
    --font-family: 'Segoe UI', Arial, sans-serif;
    --font-size-base: 14px;
    --font-size-lg: 16px;
    --font-size-xl: 18px;
}

body {
    font-family: var(--font-family);
    color: var(--black);
    background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
    background-size: cover;
    background-position: top center;
    background-repeat: no-repeat;
    margin: 0;
    padding: 0;
    min-height: 100vh;
    overflow-x: hidden;
}

.container {
    max-width: 1200px;
    margin: 100px 0 0 100px; /* Modificato da 'margin: 100px auto 0' */
    padding: 20px;
    box-sizing: border-box;
}

.header-banner {
    width: 100%;
    max-height: 150px;
    object-fit: contain;
    margin-bottom: 30px;
}

.back-button {
    background-color: var(--primary);
    color: var(--white);
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: var(--font-size-lg);
    transition: background-color 0.3s ease;
    margin-bottom: 30px;
}

.back-button:hover {
    background-color: #0a5185;
}

.form-section {
    background-color: var(--primary);
    color: var(--white);
    border-radius: 8px;
    box-shadow: 0 2px 10px var(--shadow);
    padding: 25px;
    margin-bottom: 30px;
}

.form-section h3 {
    color: var(--text-light);
    margin-top: 0;
    margin-bottom: 20px;
    font-size: var(--font-size-xl);
}

.form-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

select {
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 4px;
    background-color: var(--white);
    color: var(--primary);
    font-family: var(--font-family);
    font-size: var(--font-size-base);
    min-width: 120px;
}

.submit-button {
    background-color: var(--warning-bg);
    color: var(--text-light);
    padding: 8px 20px;
    border: 1px solid var(--border);
    border-radius: 4px;
    cursor: pointer;
    font-size: var(--font-size-base);
    transition: background-color 0.3s ease;
}

.submit-button:hover {
    background-color: var(--primary-light);
}

.warning-text {
    background-color: var(--warning-bg);
    border-radius: 4px;
    padding: 15px;
    margin-top: 15px;
    font-size: var(--font-size-base);
    line-height: 1.5;
    color: var(--warning-text);
    border: 1px solid var(--border);
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        margin-top: 50px;
        padding: 15px;
    }
    
    .form-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    select {
        width: 100%;
    }
}
/* Stili per i titoli */
.page-title {
    font-size: 2.2rem;
    color: var(--white);
    text-transform: uppercase;
    letter-spacing: 1px;
    text-align: center;
    padding: 15px 0;
    margin-bottom: 30px;
    border-bottom: 2px solid var(--white);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.section-title {
    font-size: 1.8rem;
    color: var(--white);
    margin: 25px 0;
    padding-left: 15px;
    border-left: 4px solid var(--white);
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
}

.form-title {
    font-size: 1.6rem;
    color: var(--white);
    margin: 20px 0;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}
    </style>
</head>
<body>
    <div class="container">        
        <div class="form-section">
            <form name="form1" id="form1" method="post" action="">
                <h3>Backup dati relativi alle prenotazioni mensa</h3>
                <div>
                    Dal:
                    <select name="dal" id="dal">
                        <?php do { ?>
                            <option value="<?php echo $row_GiornoMinimo['GIORNO']?>"><?php echo $row_GiornoMinimo['GIO']?></option>
                        <?php } while ($row_GiornoMinimo = mysqli_fetch_assoc($GiornoMinimo)); ?>
                    </select>
                    
                    Al:
                    <select name="al" id="al">
                        <?php do { ?>
                            <option value="<?php echo $row_GiornoMassimo['GIORNO']?>"><?php echo $row_GiornoMassimo['GIO']?></option>
                        <?php } while ($row_GiornoMassimo = mysqli_fetch_assoc($GiornoMassimo)); ?>
                    </select>
                    
                    <input type="submit" class="submit-button" value="Esegui" />
                    <input name="MM_form1" type="hidden" value="form1" />
                </div>
                <p class="warning-text">
                    ATTENZIONE: questa procedura sposta i dati dalla tabella corrente relativa alle prenotazioni e consumazioni mensa in una tabella di backup. Dopo l'operazione i dati non saranno più consultabili se non previa esecuzione della procedura di ripristino dati.
                </p>
            </form>
        </div>

        <div class="form-section">
            <form name="form2" id="form2" method="post" action="">
                <h3>Ripristino dati relativi alle prenotazioni mensa</h3>
                <div>
                    Dal:
                    <select name="dal" id="dal">
                        <?php do { ?>
                            <option value="<?php echo $row_GiornoMinimoBK['GIORNO']?>"><?php echo $row_GiornoMinimoBK['GIO']?></option>
                        <?php } while ($row_GiornoMinimoBK = mysqli_fetch_assoc($GiornoMinimoBK)); ?>
                    </select>
                    
                    Al:
                    <select name="al" id="al">
                        <?php do { ?>
                            <option value="<?php echo $row_GiornoMassimoBK['GIORNO']?>"><?php echo $row_GiornoMassimoBK['GIO']?></option>
                        <?php } while ($row_GiornoMassimoBK = mysqli_fetch_assoc($GiornoMassimoBK)); ?>
                    </select>
                    
                    <input type="submit" class="submit-button" value="Esegui" />
                    <input name="MM_form2" type="hidden" value="form2" />
                </div>
                <p class="warning-text">
                    ATTENZIONE: questa procedura ripristinerà i dati dalla tabella di backup alla tabella corrente relativa alle prenotazioni e consumazioni mensa. Dopo l'operazione i dati saranno consultabili dal sistema.
                </p>
            </form>
        </div>
    </div>

    <script>
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
mysqli_free_result($GiornoMassimo);
mysqli_free_result($GiornoMinimo);
mysqli_free_result($GiornoMassimoBK);
mysqli_free_result($GiornoMinimoBK);
mysqli_free_result($PresMassimo);
mysqli_free_result($PresMinimo);
mysqli_free_result($PreMassimoBk);
mysqli_free_result($PreMinimoBk);
?>
