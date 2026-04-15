<?php require_once('Connections/MyPresenze.php'); 
   //initialize the session
   session_start();
   
   //Setta la variabile d'ambiente GIORNO
   if (!isset($_SESSION['GIOR'])){
   	$_SESSION['GIOR'] = date("Y-m-d")." 00:00:00";
   	$ParGiorno_Prenotazioni = date("Y-m-d");
   }
   
   if (!isset($_SESSION['Pas'])){
   	$_SESSION['Pas'] = "%";
   	$ParPasto = "%";
   }
   
   if (isset($_POST['GIORNO'])) {
       $ParGiorno_Prenotazioni = addslashes($_POST['GIORNO']);
      $_SESSION['GIOR'] = $ParGiorno_Prenotazioni;
   } else {
   	$ParGiorno_Prenotazioni = date("Y-m-d");
   	$_SESSION['GIOR'] = date("Y-m-d");
   }
   
   //Setta la variabile d'ambiente PASTO
   
   if (isset($_POST['Pas'])) {
       $ParPasto = addslashes($_POST['Pas']);
      $_SESSION['Pas'] = $ParPasto;
   } else {
   	$ParPasto = "%";
   	$_SESSION['Pas'] = "%";
   }
   // ** Logout the current user. **
   $logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
   if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
     $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
   }
   
   if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
     //to fully log out a visitor we need to clear the session varialbles
       unset($_SESSION['UserID']);
   	unset($_SESSION['autorized']);
   	
     $logoutGoTo = "Home.php";
     if ($logoutGoTo) {
       header("Location: $logoutGoTo");
       
     }
   }
   
   ?>
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
   
   // ******************************** Procedura di bakup da form8 ************************************
   
   if (isset($_POST['MM_form8']) && $_POST['MM_form8'] == "form8") {
   
   $parDal_RecordMensa = "%";
   if (isset($_POST['dal'])) {
     $parDal_RecordMensa = addslashes($_POST['dal']);
   }
   $parAl_RecordMensa = "%";
   if (isset($_POST['al'])) {
     $parAl_RecordMensa = addslashes($_POST['al']);
   }
   
   // ************************************* Query di accodamento *******************************************
   
   $query_RecordMensa = "INSERT INTO pre_accessi_bk (IDrecord, IDnome, GIORNO, PASTO, COD_VAR, Ora_pren, USR, Ti_R, Se, Ora_cons_pr, Cons, Pagamento) SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.COD_VAR, pre_accessi.Ora_pren, pre_accessi.USR, pre_accessi.Ti_R, pre_accessi.Se, pre_accessi.Ora_cons_pr, pre_accessi.Cons, pre_accessi.Pagamento FROM pre_accessi WHERE pre_accessi.GIORNO >='$parDal_RecordMensa' AND pre_accessi.GIORNO <='$parAl_RecordMensa'";
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
   //mysql_free_result($RecordMensa);
   }
   
   if (isset($_POST['MM_form9']) && $_POST['MM_form9'] == "form9") {
     $parDal_RecordMensa = $_POST['dal'];
     $parAl_RecordMensa = $_POST['al'];
   
     // Modifica della query di inserimento per gestire correttamente le date
     $query_RecordMensa = "INSERT INTO pre_accessi 
         (IDrecord, IDnome, GIORNO, PASTO, COD_VAR, Ora_pren, USR, Ti_R, Se, Ora_cons_pr, Cons, Pagamento)
         SELECT IDrecord, IDnome, GIORNO, PASTO, COD_VAR, Ora_pren, USR, Ti_R, Se, Ora_cons_pr, Cons, Pagamento 
         FROM pre_accessi_bk 
         WHERE DATE(GIORNO) BETWEEN DATE('$parDal_RecordMensa') AND DATE('$parAl_RecordMensa')";
   
     if($PRES_conn->query($query_RecordMensa)) {
         // Se l'inserimento è riuscito, procediamo con l'eliminazione
         $query_DelRecordMensa = "DELETE FROM pre_accessi_bk 
             WHERE DATE(GIORNO) BETWEEN DATE('$parDal_RecordMensa') AND DATE('$parAl_RecordMensa')";
         
         if($PRES_conn->query($query_DelRecordMensa)) {
             // Ottimizziamo le tabelle
             $PRES_conn->query("OPTIMIZE TABLE pre_accessi");
             $PRES_conn->query("OPTIMIZE TABLE pre_accessi_bk");
   
             echo "<script>
                 alert('Ripristino completato con successo');
                 window.location.href='VTV.php';
             </script>";
         } else {
             echo "<script>
                 alert('Errore durante la pulizia del backup: " . $PRES_conn->error . "');
                 window.location.href='VTV.php';
             </script>";
         }
     } else {
         echo "<script>
             alert('Errore durante il ripristino: " . $PRES_conn->error . "');
             window.location.href='VTV.php';
         </script>";
     }
   }
   if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form12")) {
   	$oracons = date("Y-m-d h:i:s");
   	$pargiorno = $_POST['gio'];
   	$sede = $_POST['Sede'];
   	$parUO = $_POST['UO'];
   	$parPranzo = $_POST['Pasto'];
       $updateSQL = sprintf("UPDATE pre_accessi LEFT JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome 
     						SET pre_accessi.Ora_cons_pr='$oracons'
     						WHERE pre_accessi.GIORNO='$pargiorno' AND pre_accessi.Se='$sede' AND pre_elenconomi.UO='$parUO' AND pre_accessi.PASTO = '$parPranzo';");
       $Result1 = $PRES_conn->query($updateSQL);
   }
   
   //Query di selezione delle giornate di prenotazione
   
   $query_Giorni = "SELECT date_format(selectgiorno.GIORNO,'%d-%m-%Y') AS GIO, selectgiorno.GIORNO FROM selectgiorno GROUP BY selectgiorno.GIORNO ORDER BY GIORNO DESC";
   $Giorni = $PRES_conn->query($query_Giorni);
   $row_Giorni = mysqli_fetch_assoc($Giorni);
   $totalRows_Giorni = $Giorni->num_rows;
   
   //Query di selezione delle sedi
   
   $query_Sede = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi ORDER BY SEDE";
   $Sede = $PRES_conn->query($query_Sede);
   $row_Sede = mysqli_fetch_assoc($Sede);
   $totalRows_Sede = $Sede->num_rows;
   
   //Query di selezione delle razioni Fuori sede
   
   $query_GiorniCres = "SELECT date_format(selectgiorno.GIORNO,'%d-%m-%Y') AS GIO, selectgiorno.GIORNO FROM selectgiorno GROUP BY selectgiorno.GIORNO";
   $GiorniCres = $PRES_conn->query($query_GiorniCres);
   $row_GiorniCres = mysqli_fetch_assoc($GiorniCres);
   $totalRows_GiorniCres = $GiorniCres->num_rows;
   
   
   $query_GioFA = "SELECT pre_accessi_bk.GIORNO, date_format(pre_accessi_bk.GIORNO, '%d-%m-%Y') as GIO FROM pre_accessi_bk GROUP BY pre_accessi_bk.GIORNO";
   $GioFA = $PRES_conn->query($query_GioFA);
   $row_GioFA = mysqli_fetch_assoc($GioFA);
   $totalRows_GioFA = $GioFA->num_rows;
   
   
   $query_GioFAdesc = "SELECT pre_accessi_bk.GIORNO, date_format(pre_accessi_bk.GIORNO, '%d-%m-%Y') as GIO FROM pre_accessi_bk GROUP BY pre_accessi_bk.GIORNO ORDER BY pre_accessi_bk.GIORNO";
   $GioFAdesc = $PRES_conn->query($query_GioFAdesc);
   $row_GioFAdesc = mysqli_fetch_assoc($GioFAdesc);
   $totalRows_GioFAdesc = $GioFAdesc->num_rows;
   
   
   $query_Categorie = "SELECT pre_categorie.IDcat, pre_categorie.Categoria FROM pre_categorie";
   $Categorie = $PRES_conn->query($query_Categorie);
   $row_Categorie = mysqli_fetch_assoc($Categorie);
   $totalRows_Categorie = $Categorie->num_rows;
   
   
   $query_GiornoMassimo = "SELECT pre_accessi.GIORNO, date_format(pre_accessi.GIORNO,'%d/%m/%Y') AS GIO FROM pre_accessi GROUP BY pre_accessi.GIORNO ORDER BY pre_accessi.GIORNO DESC";
   $GiornoMassimo = $PRES_conn->query($query_GiornoMassimo);
   $row_GiornoMassimo = mysqli_fetch_assoc($GiornoMassimo);
   $totalRows_GiornoMassimo = $GiornoMassimo->num_rows;
   
   
   $query_GiornoMinimo = "SELECT pre_accessi.GIORNO, date_format(pre_accessi.GIORNO,'%d/%m/%Y') AS GIO FROM pre_accessi GROUP BY pre_accessi.GIORNO ORDER BY pre_accessi.GIORNO";
   $GiornoMinimo = $PRES_conn->query($query_GiornoMinimo);
   $row_GiornoMinimo = mysqli_fetch_assoc($GiornoMinimo);
   $totalRows_GiornoMinimo = $GiornoMinimo->num_rows;
   
   
   $query_GiornoMassimoBK = "SELECT pre_accessi_bk.GIORNO, date_format(pre_accessi_bk.GIORNO,'%d/%m/%Y') AS GIO FROM pre_accessi_bk GROUP BY pre_accessi_bk.GIORNO ORDER BY pre_accessi_bk.GIORNO DESC";
   $GiornoMassimoBK = $PRES_conn->query($query_GiornoMassimoBK);
   $row_GiornoMassimoBK = mysqli_fetch_assoc($GiornoMassimoBK);
   $totalRows_GiornoMassimoBK = $GiornoMassimoBK->num_rows;
   
   
   $query_GiornoMinimoBK = "SELECT pre_accessi_bk.GIORNO, date_format(pre_accessi_bk.GIORNO,'%d/%m/%Y') AS GIO FROM pre_accessi_bk GROUP BY pre_accessi_bk.GIORNO ORDER BY pre_accessi_bk.GIORNO";
   $GiornoMinimoBK = $PRES_conn->query($query_GiornoMinimoBK);
   $row_GiornoMinimoBK = mysqli_fetch_assoc($GiornoMinimoBK);
   $totalRows_GiornoMinimoBK = $GiornoMinimoBK->num_rows;
   
   
   $query_Razioni = "SELECT pre_accessi.GIORNO, count(pre_accessi.PASTO) as N_Pasti, pre_accessi.PASTO, pre_sedi.SEDE, pre_uo.DEN_UN_OPER, pre_tiporazione.TipoRazione, pre_accessi.Ti_R, pre_accessi.Se,
   				  pre_elenconomi.UO, pre_accessi.Ora_cons_pr
   				  FROM pre_accessi, pre_sedi, pre_tiporazione, pre_uo, pre_elenconomi 
   				  WHERE pre_sedi.FSede AND pre_sedi.IDsede=pre_accessi.Se AND pre_tiporazione.ID=pre_accessi.Ti_R AND pre_accessi.IDnome=pre_elenconomi.IDnome AND pre_elenconomi.UO=pre_uo.ID_UO AND pre_accessi.GIORNO like '$ParGiorno_Prenotazioni'
   				  GROUP BY pre_accessi.GIORNO, pre_accessi.PASTO, pre_sedi.SEDE, pre_uo.DEN_UN_OPER, pre_tiporazione.TipoRazione, pre_accessi.Ti_R, pre_accessi.Se, pre_elenconomi.UO, pre_accessi.Ora_cons_pr
   				  ORDER BY pre_accessi.PASTO";
             if(!$Razioni = $PRES_conn->query($query_Razioni)){
               echo $PRES_conn->error;
             }
   
   $row_Razioni = mysqli_fetch_assoc($Razioni);
   $totalRows_Razioni = $Razioni->num_rows;
   
   // Query di conteggio dei pasti prenotati in base al parametro ParGiorno_Prenotazioni
   
   	
   	$query_Prenotazioni = sprintf("SELECT pre_accessi.GIORNO, Count(pre_accessi.PASTO) AS SommaDiPASTO, pre_sedi.SEDE, pre_accessi.Se, pre_tiporazione.TipoRazione,
   							pre_accessi.PASTO, pre_accessi.Pagamento FROM ((pre_accessi INNER JOIN pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID) 
   							INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede) GROUP BY 
   							pre_accessi.GIORNO, pre_sedi.SEDE, pre_tiporazione.TipoRazione, pre_accessi.PASTO, pre_accessi.Pagamento, pre_accessi.Se 
   							HAVING pre_accessi.GIORNO='%s' AND pre_accessi.PASTO='%s'
   							ORDER BY pre_accessi.Se, pre_accessi.PASTO", $ParGiorno_Prenotazioni, $ParPasto);
   $Prenotazioni = $PRES_conn->query($query_Prenotazioni);
   $row_Prenotazioni = mysqli_fetch_assoc($Prenotazioni);
   $totalRows_Prenotazioni = $Prenotazioni->num_rows;
   //}
   
   ?>
<!DOCTYPE html>
<html lang="it">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>AREA VTV</title>
      <link rel="stylesheet" type="text/css" href="tcal.css">
      <script type="text/javascript" src="tcal.js"></script>
      <style>
         :root {
         --primary-color: rgba(32, 77, 98, 255);
         --text-color: #ffffff;
         --card-bg: rgba(32, 77, 98, 0.9);
         --form-bg: rgba(255, 255, 255, 0.1);
         }
         body {
         font-family: 'Segoe UI', Arial, sans-serif;
         color: var(--text-color);
         background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
         background-size: cover;
         background-attachment: fixed;
         margin: 0;
         padding: 5px;
         min-height: 100vh;
         }
         
         .user-info {
         text-align: center;
         font-size: 1.1rem;
         margin: 1rem 0;
         }
         /* Back Button */
         .back-button {
         background: rgba(32, 77, 98, 255);
         border: none;
         padding: 0.7em 1.5em;
         color: white;
         border-radius: 0.7em;
         font-size: 1.2rem;
         margin: 1rem;
         cursor: pointer;
         }
         /* Main Content */
         .page-content {
         display: grid;
         grid-template-columns: repeat(2, 1fr);
         gap: 2rem;
         max-width: 1400px;
         margin: 0 auto;
         padding: 20px;
         }
         /* Cards */
         .card {
         background: var(--card-bg);
         border-radius: 10px;
         padding: 1.5rem;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
         height: fit-content;
         }
         .card.prenotazioni {
         grid-column: 1;
         max-width: 600px;
         }
         .card.report-giornalieri {
         grid-column: 2;
         max-width: 500px;
         }
         .card.ricerche-avanzate {
         grid-column: 1;
         }
         .card.ricerche-avanzate .form-group {
         padding: 0.8rem;
         margin-bottom: 0.8rem;
         }
         .card.ricerche-avanzate select {
         margin: 0.3rem 0;
         }
         .card.ricerche-avanzate .button {
         margin: 0.3rem;
         }
         .card.backup-restore {
         grid-column: 1 / -1;
         margin-top: -2rem;
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 2rem;
         }
         /* Form Elements */
         .form-group {
         background: var(--form-bg);
         padding: 1.2rem;
         border-radius: 8px;
         margin-bottom: 1.2rem;
         }
         .form-group:last-child {
         margin-bottom: 0;
         }
         .form-group h4 {
         margin-top: 0;
         margin-bottom: 1rem;
         color: white;
         }
         select {
         width: 100%;
         padding: 0.8rem;
         margin: 0.5rem 0;
         border: 1px solid rgba(255, 255, 255, 0.3);
         background: rgba(255, 255, 255, 0.15);
         color: white;
         border-radius: 4px;
         }
         select option {
         background: var(--primary-color);
         color: white;
         padding: 8px;
         }
         /* Buttons */
         .button {
         background: var(--primary-color);
         border: none;
         color: white;
         padding: 0.8em 1.2em;
         border-radius: 0.7em;
         cursor: pointer;
         transition: all 0.3s ease;
         font-size: 0.9rem;
         margin: 0.5rem;
         }
         .button:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 8px rgba(0,0,0,0.3);
         }
         /* Tables */
         table {
         width: 100%;
         border-collapse: collapse;
         background: var(--form-bg);
         border-radius: 8px;
         overflow: hidden;
         margin: 1rem 0;
         }
         th, td {
         padding: 1rem;
         text-align: left;
         border-bottom: 1px solid rgba(255, 255, 255, 0.1);
         }
         th {
         background: rgba(32, 77, 98, 0.8);
         }
         /* Grid Layout */
         .grid-container {
         display: grid;
         grid-template-columns: repeat(2, 1fr);
         gap: 1rem;
         }
         /* Helper Text */
         .helper-text {
         font-size: 0.9rem;
         opacity: 0.8;
         margin-top: 0.5rem;
         }
         /* Responsive */
         @media (max-width: 1200px) {
         .page-content {
         grid-template-columns: 1fr;
         padding: 15px;
         }
         .card {
         grid-column: 1;
         max-width: 100%;
         }
         .card.backup-restore {
         grid-template-columns: 1fr;
         margin-top: 0;
         }
         }
      </style>
   </head>
   <body>
      <button class="back-button" onclick="location.href='LoginPrenota.php'">
      ← Indietro
      </button>
      <div class="user-info">
         <h3>Consultazione stato prenotazioni e consumazioni - utente: <?php echo $_SESSION['UsernameExt'];?></h3>
      </div>
      <div class="card report-card">
         <form action="pdf.php" method="post" name="form2" target="_blank">
            <div class="form-group">
               <h4>Report giornalieri</h4>
               <?php
                  // Prima del secondo select
                  $Giorni = $PRES_conn->query($query_Giorni);
                  ?>
               <select name="gio" id="gio">
                  <?php while ($row_Giorni = mysqli_fetch_assoc($Giorni)) { ?>
                  <option value="<?php echo $row_Giorni['GIORNO']?>" <?php if (!(strcmp($row_Giorni['GIORNO'], date("Y-m-d")))) {echo "SELECTED";} ?>>
                     <?php echo $row_Giorni['GIO']?>
                  </option>
                  <?php } ?>
               </select>
               <select name="Sede" id="Sede">
                  <option value="-" selected>-</option>
                  <?php do { ?>
                  <option value="<?php echo $row_Sede['IDsede']?>"><?php echo $row_Sede['SEDE']?></option>
                  <?php } while ($row_Sede = mysqli_fetch_assoc($Sede)); ?>
               </select>
               <select name="Pasto">
                  <option value="3">Colazione</option>
                  <option value="1">Pranzo</option>
                  <option value="2">Cena</option>
               </select>
               <div class="grid-container">
                  <input type="submit" class="button" name="report" value="Report consumazioni">
                  <input type="submit" class="button" name="report1" value="Prenotati non consumati">
                  <input type="submit" class="button" name="report2" value="Razioni a pagamento">
                  <input type="submit" class="button" name="report3" value="Dimostrazione numerica">
               </div>
            </div>
         </form>
      </div>
      <div class="page-content">
         <div class="card report-card">
            <form name="form1" method="post" action="<?php echo $editFormAction; ?>">
               <div class="form-group">
                  <h4>Situazione prenotazioni</h4>
                  <?php
                     // Prima del primo select
                     $Giorni = $PRES_conn->query($query_Giorni);
                     ?>
                  <select name="GIORNO" id="GIORNO">
                     <?php while ($row_Giorni = mysqli_fetch_assoc($Giorni)) { ?>
                     <option value="<?php echo $row_Giorni['GIORNO']?>" <?php if (!(strcmp($row_Giorni['GIORNO'], $_SESSION['GIOR']))) {echo "SELECTED";} ?>>
                        <?php echo $row_Giorni['GIO']?>
                     </option>
                     <?php } ?>
                  </select>
                  <select name="Pas" id="Pas">
                     <option value="1" <?php if (!(strcmp(1, $_SESSION['Pas']))) {echo "SELECTED";} ?>>Pranzo</option>
                     <option value="2" <?php if (!(strcmp(2, $_SESSION['Pas']))) {echo "SELECTED";} ?>>Cena</option>
                     <option value="3" <?php if (!(strcmp(3, $_SESSION['Pas']))) {echo "SELECTED";} ?>>Colazione</option>
                  </select>
                  <input type="submit" class="button" name="Submit" value="Visualizza dati">
               </div>
            </form>
            <?php if ($totalRows_Prenotazioni > 0) { ?>
            <div class="form-group">
               <table>
                  <tr>
                     <th>N°</th>
                     <th>Pag.</th>
                     <th>Pasto</th>
                     <th>Sede</th>
                     <th>Tipo razione</th>
                     <th>Dettaglio</th>
                     <th>Elenco</th>
                  </tr>
                  <?php do { ?>
                  <tr>
                     <td><?php echo $row_Prenotazioni['SommaDiPASTO']; ?></td>
                     <td><?php if($row_Prenotazioni['Pagamento'] == 1) { ?><img src="images/386px-Euro_symbol_gold_svg.png" width="15" height="15"><?php } ?></td>
                     <td><?php 
                        if ($row_Prenotazioni['PASTO']==1) echo "PRANZO"; 
                        if ($row_Prenotazioni['PASTO']==2) echo "CENA"; 
                        if ($row_Prenotazioni['PASTO']==3) echo "COLAZIONE"; 
                        ?></td>
                     <td><?php echo $row_Prenotazioni['SEDE']; ?></td>
                     <td><?php echo $row_Prenotazioni['TipoRazione']; ?></td>
                     <td>
                        <form action="pdf.php" method="post" target="_blank">
                           <input name="Sede" type="hidden" value="<?php echo $row_Prenotazioni['Se']; ?>">
                           <input name="gio" type="hidden" value="<?php echo $row_Prenotazioni['GIORNO']; ?>">
                           <input name="report" type="hidden" value="10">
                           <input name="Pasto" type="hidden" value="<?php echo $row_Prenotazioni['PASTO']; ?>">
                           <input type="submit" class="button" value="Report">
                        </form>
                     </td>
                     <td>
                        <form action="pdf.php" method="post" target="_blank">
                           <input name="Sede" type="hidden" value="<?php echo $row_Prenotazioni['Se']; ?>">
                           <input name="gio" type="hidden" value="<?php echo $row_Prenotazioni['GIORNO']; ?>">
                           <input name="report" type="hidden" value="8">
                           <input name="Pasto" type="hidden" value="<?php echo $row_Prenotazioni['PASTO']; ?>">
                           <input type="submit" class="button" value="Report">
                        </form>
                     </td>
                  </tr>
                  <?php } while ($row_Prenotazioni = mysqli_fetch_assoc($Prenotazioni)); ?>
               </table>
            </div>
            <?php } ?>
         </div>
         <div class="card report-card">
            <form action="pdf.php" method="post" name="form4" target="_blank">
               <div class="form-group">
                  <h4>Ricerche avanzate (storico)</h4>
                  <div class="date-range">
                     <select name="sto_dal">
                        <?php do { ?>
                        <option value="<?php echo $row_GioFA['GIORNO']?>"><?php echo $row_GioFA['GIO']?></option>
                        <?php } while ($row_GioFA = mysqli_fetch_assoc($GioFA)); ?>
                     </select>
                     <select name="sto_al">
                        <?php do { ?>
                        <option value="<?php echo $row_GioFAdesc['GIORNO']?>"><?php echo $row_GioFAdesc['GIO']?></option>
                        <?php } while ($row_GioFAdesc = mysqli_fetch_assoc($GioFAdesc)); ?>
                     </select>
                  </div>
                  <select name="Sede" id="Sede">
    <option value="-" selected>Tutte le sedi</option>
    <?php 
    $Sede = $PRES_conn->query($query_Sede);
    while ($row_Sede = mysqli_fetch_assoc($Sede)) { 
    ?>
        <option value="<?php echo $row_Sede['IDsede']?>"><?php echo $row_Sede['SEDE']?></option>
    <?php } ?>
</select>
                  <select name="Pasto">
                     <option value="3">Colazione</option>
                     <option value="1">Pranzo</option>
                     <option value="2">Cena</option>
                  </select>
                  <input type="submit" class="button" name="ReportRic1" value="Conteggio razioni distribuite">
               </div>
               <div class="form-group">
                  <h4>Conteggio per FF.AA.</h4>
                  <select name="FA">
                     <option value="EI">EI</option>
                     <option value="MM">MM</option>
                     <option value="AM">AM</option>
                     <option value="CC">CC</option>
                     <option value="STRA">STRA</option>
                  </select>
                  <input type="submit" class="button" name="ConteggioFFAA" value="Conteggio consumazioni per FFAA">
               </div>
               <div class="form-group">
                  <h4>Conteggio per categoria</h4>
                  <select name="Categoria">
                     <?php do { ?>
                     <option value="<?php echo $row_Categorie['Categoria']?>"><?php echo $row_Categorie['Categoria']?></option>
                     <?php } while ($row_Categorie = mysqli_fetch_assoc($Categorie)); ?>
                  </select>
                  <input type="submit" class="button" name="ConteggioCAT" value="Conteggio consumazioni per categoria">
               </div>
            </form>
         </div>
         <div class="card backup-restore">
            <form name="form8" method="post" action="">
               <div class="form-group">
                  <h4>Backup dei dati</h4>
                  <p>Dal:</p>
                  <select name="dal" id="dal">
                     <?php do { ?>
                     <option value="<?php echo $row_GiornoMinimo['GIORNO']?>"><?php echo $row_GiornoMinimo['GIO']?></option>
                     <?php } while ($row_GiornoMinimo = mysqli_fetch_assoc($GiornoMinimo)); ?>
                  </select>
                  <p>Al:</p>
                  <select name="al" id="al">
                     <?php do { ?>
                     <option value="<?php echo $row_GiornoMassimo['GIORNO']?>"><?php echo $row_GiornoMassimo['GIO']?></option>
                     <?php } while ($row_GiornoMassimo = mysqli_fetch_assoc($GiornoMassimo)); ?>
                  </select>
                  <input type="submit" class="button" name="Submit" value="Esegui">
                  <input name="MM_form8" type="hidden" value="form8">
                  <p class="helper-text">Questa procedura sposta i dati dalla tabella corrente relativa alle prenotazioni e consumazioni mensa in una tabella di backup.</p>
               </div>
            </form>
            <form name="form9" method="post" action="">
               <div class="form-group">
                  <h4>Ripristino dei dati</h4>
                  <p>Dal:</p>
                  <select name="dal" id="dal">
                     <?php do { ?>
                     <option value="<?php echo $row_GiornoMinimoBK['GIORNO']?>"><?php echo $row_GiornoMinimoBK['GIO']?></option>
                     <?php } while ($row_GiornoMinimoBK = mysqli_fetch_assoc($GiornoMinimoBK)); ?>
                  </select>
                  <p>Al:</p>
                  <select name="al" id="al">
                     <?php do { ?>
                     <option value="<?php echo $row_GiornoMassimoBK['GIORNO']?>"><?php echo $row_GiornoMassimoBK['GIO']?></option>
                     <?php } while ($row_GiornoMassimoBK = mysqli_fetch_assoc($GiornoMassimoBK)); ?>
                  </select>
                  <input type="submit" class="button" name="Submit" value="Esegui">
                  <input name="MM_form9" type="hidden" value="form9">
                  <p class="helper-text">Questa procedura ripristinerà i dati dalla tabella di backup alla tabella corrente.</p>
               </div>
            </form>
         </div>
      </div>
   </body>
</html>
<?php
   mysqli_free_result($Giorni);
   mysqli_free_result($Sede);
   mysqli_free_result($GiorniCres);
   mysqli_free_result($GioFA);
   mysqli_free_result($GioFAdesc);
   mysqli_free_result($Categorie);
   mysqli_free_result($GiornoMassimo);
   mysqli_free_result($GiornoMinimo);
   mysqli_free_result($GiornoMassimoBK);
   mysqli_free_result($GiornoMinimoBK);
   mysqli_free_result($Razioni);
   mysqli_free_result($Prenotazioni);
   ?>