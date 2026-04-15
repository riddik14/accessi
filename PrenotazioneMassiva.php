<?php require_once('Connections/MyPresenze.php'); 
   session_start();


   if (!(isset($_SESSION['GioCor']))) {
      $_SESSION['GioCor'] = date("Y-m-d");
  }
   
   
   if (!(isset($_SESSION['Pasto']))) {
   	$_SESSION['Pasto'] = 1;
   }
   
   if (!(isset($_SESSION['GIORNO']))) {
   	$_SESSION['GIORNO'] = date("Y-m-d");
   }
   
   if (!(isset($_SESSION['ORDINAX']))) {
   	$_SESSION['ORDINAX'] = 2;
   }
   
   if (!(isset($_POST['GG_pre']))) {
   	$_SESSION['GG_pre'] = date("d");
   } else {
   	$_SESSION['GG_pre'] = $_POST['GG_pre'];
   }
   
   if (!(isset($_POST['MM_pre']))) {
   	$_SESSION['MM_pre'] = date("m");
   } else {
   	$_SESSION['MM_pre'] = $_POST['MM_pre'];
   }
   
   if (!(isset($_POST['AA_pre']))) {
   	$_SESSION['AA_pre'] = date("Y");
   } else {
   	$_SESSION['AA_pre'] = $_POST['AA_pre'];
   }
   
   if (isset($_POST['UO'])) {	
   	$_SESSION['UO'] = $_POST['UO'];
   }
   
   if(isset($_POST['Submit']) && $_POST['Submit'] =="Annulla filtro") {
   	$_SESSION['UO'] = "%";
   }
   
   $Tx_cert = $_SESSION['UserID'];
   // ** Logout the current user. **
   
   $logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
   if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
     $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
   }
   
   if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
     //to fully log out a visitor we need to clear the session varialbles
     unset ($_SESSION['UserID']);
     unset ($_SESSION['autorized']);
   	
     $logoutGoTo = "Home.php";
     if ($logoutGoTo) {
       header("Location: $logoutGoTo");
       exit;
     }
   }
   
   function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
   {
     // magic_quotes_gpc was removed in PHP 8.0
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
   
   //*******************************************  Query inserimento nominativo singolo da FORM 3 ****************************************
   
   if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
   		$_SESSION['ORDINAX'] = $_POST['OrdPer'];
   		$_SESSION['Pasto'] = $_POST['pasto'];
   }
   
   				
   	if ((isset($_POST["Submit"])) && ($_POST["Submit"] == "Prenota")) {		//prenotazione dei record selezionati
   		$checked = $_POST['checkbox']; 
   		$count = count($checked); 
   		$GIO = $_POST['AA_pre']."-".$_POST['MM_pre']."-".$_POST['GG_pre'];
   		$Orario = date ("Y-m-d H:i");
   		$UserID = $_SESSION['UserID'];
   		$Pasto = $_POST['pasto'];
   		$Sede = 0;
   		$MSG = "";
   		if (isset($_POST['SelSede'])) {
   			$Sede = $_POST['SelSede'];
   		} 
   									
   		for($i=0; $i < $count; $i++) {
   		  $IDnome = $checked[$i];
   		 
   			$query_Duplicati = "SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO 
   								FROM pre_accessi
   								WHERE pre_accessi.IDnome='$IDnome' AND pre_accessi.GIORNO='$GIO' AND pre_accessi.PASTO='$Pasto'";
   			$Duplicati = $PRES_conn->query($query_Duplicati);
   			$row_Duplicati = mysqli_fetch_assoc($Duplicati);
   			$totalRows_Duplicati = $Duplicati->num_rows;
   			
   		  if ($totalRows_Duplicati == 0) {
   		  
   				$query_Sede = "SELECT pre_elenconomi.SedeSomm, pre_elenconomi.Cognome, pre_elenconomi.Nome  
   							   FROM pre_elenconomi 
   							   WHERE pre_elenconomi.IDnome='$IDnome'";
   				$SedeSom = $PRES_conn->query($query_Sede);
   				$row_Sede = mysqli_fetch_assoc($SedeSom);
   				$MSG .= " - " . $row_Sede['Nome'] . " " . $row_Sede['Cognome'];
   
    			if ($Sede == 0 ) {
   				$Sede = $row_Sede['SedeSomm'];
   			}
   				if($Pasto == 1){
   					$query = sprintf("INSERT INTO pre_accessi (IDnome, PASTO, GIORNO, Ora_pren, USR, Ti_R, Se, Pagamento) 
     							      SELECT pre_elenconomi.IDnome, '$Pasto', '$GIO', '$Orario', '$UserID', pre_elenconomi.TipoRazione, '$Sede', '0'  
   							      FROM pre_elenconomi 
   								  WHERE pre_elenconomi.IDnome='$IDnome';");
   				} else if ($Pasto == 2){
   					$query = sprintf("INSERT INTO pre_accessi (IDnome, PASTO, GIORNO, Ora_pren, USR, Ti_R, Se, Pagamento) 
     							      SELECT pre_elenconomi.IDnome, '$Pasto', '$GIO', '$Orario', '$UserID', pre_elenconomi.TipoRazioneCe, '$Sede', '0'  
   							      FROM pre_elenconomi 
   								  WHERE pre_elenconomi.IDnome='$IDnome';");
   				} else {
   					$query = sprintf("INSERT INTO pre_accessi (IDnome, PASTO, GIORNO, Ora_pren, USR, Ti_R, Se, Pagamento) 
     							      SELECT pre_elenconomi.IDnome, '$Pasto', '$GIO', '$Orario', '$UserID', pre_elenconomi.TipoRazioneCol, '$Sede', '0'  
   							      FROM pre_elenconomi 
   								  WHERE pre_elenconomi.IDnome='$IDnome';");
   				}
   			
   				$PRES_conn->query($query); 			
   		  } 
   		}
   
   	if (date('H') > 8 && strtotime($GIO) == strtotime (date('Y-m-d')) && $Sede == 1 || date('H') > 8 && strtotime($GIO) == strtotime (date('Y-m-d')) && $Sede == 2 ||date('H') > 8 && strtotime($GIO) == strtotime (date('Y-m-d')) && $Sede == 5){
   		if ($Pasto == 1 ) { $pasto = " pranzi "; } else if ($Pasto == 2 ) { $pasto = " cene "; } else { $pasto = " colazioni "; };
   
   			$query_Sede = "SELECT pre_sedi.SEDE 
   								FROM pre_sedi
   								WHERE pre_sedi.IDsede='$Sede'";
   			$Sede = $PRES_conn->query($query_Sede);
   			$row_Sede = mysqli_fetch_assoc($Sede);
   			$SedePren = $row_Sede['SEDE'];	
   
   		if ($Pasto == 1 ) { $pasto = " pranzi "; } else if ($Pasto == 2 ) { $pasto = " cene "; } else { $pasto = " colazioni "; };
   			$mess = "Si comunica che l'utente " . $_SESSION['UsernameExt'] . " ha prenotato n. " . $count . " " . $pasto . " presso la sede " . $SedePren . " per il giorno " . $_POST['GG_pre']."-".$_POST['MM_pre']."-".$_POST['AA_pre']." per il seguente personale: " . $MSG;
   			$e = "uadvtv@cealpi.esercito.difesa.it; suadvtv2@cealpi.esercito.difesa.it"; //Inserire l'indirizzo email a cui si vuole spedire l'email 
   			$ogg = "Prenotazione pasti dopo le ore 09:00"; //Inserire l'oggetto dell'email da spedire 
   			$mittente = "info@cealpi.esercito.difesa.it"; //Inserire l'indirizzo email che verrà visualizzato come mittente dell'email 
   			$reply = "info@cealpi.esercito.difesa.it"; //Inserire l'indirizzo email a cui verranno inviate le risposte all'email inviata 
   			$intestazioni = "From: $mittente\nReply-To: $reply\nX-Mailer: Sismail Web Email Interface\n"; 
   			$msg_body = $mess; 
   			mail($e,$ogg,$msg_body, $intestazioni);
   
   		header("Location: NewSitPrenotazione.php");
   	} 
   mysqli_free_result($Duplicati);
   }
   
   	
   //***********************************************  Query inserimento tutti i nominativi ******************************************
   
   	if ((isset($_POST["PreTutti"])) && ($_POST["PreTutti"] == "Prenota tutti")) { 
   	
   			$GIO = $_SESSION['GioCor'];
   			$_SESSION['Giorno'] = $GIO;
   			$Orario = date ("Y-m-d H:i:s");
   			$UserID = $_SESSION['UserID'];
   			$TipoRaz = $_POST['TipoRazione'];
   			$Caserm = $_POST['Caserma'];
   			$Pasto = $_POST['pasto'];
   			$_SESSION['Pasto'] = $Pasto;
   			if ($Pasto == 1) {
     				$insertSQL = sprintf("INSERT INTO pre_accessi (IDnome, PASTO, GIORNO, Ora_pren, USR, Ti_R, Se) 
     									  SELECT pre_nomiliberi.IDnome, '$Pasto', '$GIO', '$Orario', '$UserID', pre_elenconomi.TipoRazione, pre_elenconomi.SedeSomm  
   									  FROM pre_nomiliberi, pre_elenconomi
     									  WHERE pre_nomiliberi.IDnome_Utente='$UserID' AND pre_nomiliberi.IDnome=pre_elenconomi.IDnome");
   			}
   			if ($Pasto == 2) {
   				$insertSQL = sprintf("INSERT INTO pre_accessi (IDnome, PASTO, GIORNO, Ora_pren, USR, Ti_R, Se) 
     									  SELECT nomi_liberi_cena.IDnome, '$Pasto', '$GIO', '$Orario', '$UserID', pre_elenconomi.TipoRazioneCe, pre_elenconomi.SedeSomm  
   									  FROM nomi_liberi_cena, pre_elenconomi
     									  WHERE nomi_liberi_cena.ID_USERNAME='$UserID' AND nomi_liberi_cena.IDnome=pre_elenconomi.IDnome");
   			}
   			if ($Pasto == 3) {
   				$insertSQL = sprintf("INSERT INTO pre_accessi (IDnome, PASTO, GIORNO, Ora_pren, USR, Ti_R, Se) 
     									  SELECT nomi_liberi_col.IDnome, '$Pasto', '$GIO', '$Orario', '$UserID', pre_elenconomi.TipoRazioneCol, pre_elenconomi.SedeSomm  
   									  FROM nomi_liberi_col, pre_elenconomi
     									 WHERE nomi_liberi_col.ID_USERNAME='$UserID' AND nomi_liberi_col.IDnome=pre_elenconomi.IDnome");
   			}
   			
   	  	$Result1 = $PRES_conn->query($insertSQL);
   		header(sprintf("Location: NewSitPrenotazione.php"));
   	
   }
   
   //***********************************************  Query selezione UO abilitate  ********************************
   
   $parID_UO = $_SESSION['UserID'];
   $query_UO = "SELECT pre_uo.ID_UO, pre_utentixunita.ID_UO, pre_uo.DEN_UN_OPER, pre_sedi.SEDE, pre_elenconomi.IDnome 
   			 FROM pre_utentixunita, pre_uo, pre_sedi, pre_elenconomi 
   			 WHERE pre_uo.ID_UO=pre_utentixunita.ID_UO AND pre_sedi.IDsede=pre_uo.SEDE AND pre_elenconomi.IDnome='$parID_UO' 
   			 AND pre_elenconomi.IDnome=pre_utentixunita.IDnome ORDER BY pre_uo.PRE_UN_OPER";
   $UO = $PRES_conn->query($query_UO);
   $row_UO = mysqli_fetch_assoc($UO);
   $totalRows_UO = $UO->num_rows;
   
   //**********************************************   Query selezione dati utente **********************************
   $query_Username = "SELECT pre_elenconomi.IDnome, pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.Foto, pre_elenconomi.Cte 
   				FROM pre_elenconomi, pre_gradi WHERE IDnome='$Tx_cert' AND pre_elenconomi.IDgrado=pre_gradi.ID";
   $Username = $PRES_conn->query($query_Username);
   $row_Username = mysqli_fetch_assoc($Username);
   $totalRows_Username = $Username->num_rows;
   $GradoUser = $row_Username['Grado'];
   $CognomeUser = $row_Username['Cognome'];
   $NomeUser = $row_Username['Nome'];
   
   //**********************************************   Query selezione nomi amministrati **********************************
   
   $parGiorno_Nomi = "%";
   if (isset($_POST['GG_pre']) or !isset($_SESSION['GIORNO'])) {
   	 $parGiorno_Nomi =  $_POST['AA_pre']."-".$_POST['MM_pre']."-".$_POST['GG_pre'];
        $_SESSION['GIORNO'] = $parGiorno_Nomi;
   } else {
    	 $parGiorno_Nomi =  date("Y-m-d");
   }
   
   $parPasto_Nomi = "%";
   if (isset($_POST['pasto'])) {
     $parPasto_Nomi =  $_POST['pasto'];
     $_SESSION['Pasto'] = $_POST['pasto'];
   } else {
     $parPasto_Nomi =  $_SESSION['Pasto'];
   }
   
   $parUser_Nomi = "%";
   if (isset($_SESSION['UserID'])) {
     $parUser_Nomi = addslashes($_SESSION['UserID']);
   }
   if (isset($_SESSION['UO'])) {
   	$parUO = $_SESSION['UO'];
   } else {
   	$parUO = '%';
   }
   
   if ($_SESSION['ORDINAX'] == 2 ){
   	$parOrdina = 'Cognome';
   } else {
   	$parOrdina = 'Grado';
   }
   if (isset($_SESSION['UO'])) {
   	$parUO = $_SESSION['UO'];
   } else {
   	$parUO = '%';
   }
   
   
   //$query_Nomi = sprintf("SELECT pre_nomiview.Grado, pre_nomiview.Cognome, pre_nomiview.Nome, pre_nomiview.IDnome,pre_nomiview.ID_USERNAME FROM pre_nomiview WHERE pre_nomiview.ID_USERNAME='%s' ORDER BY pre_nomiview.Cognome", $parUser_Nomi);
   $query_Nomi = "SELECT pre_nomiview.Grado, pre_nomiview.Cognome, pre_nomiview.Nome, pre_nomiview.IDnome, pre_nomiview.UO 
   					   FROM (SELECT pre_nomiliberi.IDnome, pre_nomiliberi.Grado, pre_nomiliberi.Cognome, pre_nomiliberi.Nome, pre_nomiliberi.GIORNO,
   					   	     pre_nomiliberi.ID_USERNAME, pre_nomiliberi.Pasto, pre_nomiliberi.UO
   							 FROM pre_nomiliberi
   					   		 WHERE pre_nomiliberi.ID_USERNAME ='$parUser_Nomi' AND pre_nomiliberi.GIORNO='$parGiorno_Nomi' 
   							 AND pre_nomiliberi.PASTO='$parPasto_Nomi') AS Subquery	RIGHT JOIN pre_nomiview ON pre_nomiview.IDnome = Subquery.IDnome				 
   						WHERE ISNULL(Subquery.GIORNO) AND pre_nomiview.ID_USERNAME ='$parUser_Nomi' AND pre_nomiview.UO LIKE '$parUO'
   						ORDER BY $parOrdina";
   if(!$Nomi = $PRES_conn->query($query_Nomi)){
     echo $PRES_conn->error;
     exit;
   }
   $row_Nomi = mysqli_fetch_assoc($Nomi);
   $totalRows_Nomi = $Nomi->num_rows;
   // ************************************************ Query di selezione delle sedi di consumazione *******************************************
   $query_Sedi = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi ORDER BY SEDE";
   $Sedi = $PRES_conn->query($query_Sedi);
   $row_Sedi = mysqli_fetch_assoc($Sedi);
   $totalRows_Sedi = $Sedi->num_rows;
   
   // ************************************************ FINE PHP *******************************************
   ?>
<!DOCTYPE html>
<html lang="it">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Prenotazione massiva e gestione presenze</title>
      <style>
:root {
    --primary-color: rgba(32, 77, 98, 255);
    --text-color: #ffffff;
}

body {
   font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--text-color);
    background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
            background-repeat: no-repeat;
            background-attachment: fixed;
    background-size: cover;
   background-position: center;
    margin: 0;
    padding: 5px;
    height: 100vh;
    overflow: hidden;
   
}

.header-banner {
    text-align: center;
    margin-bottom: 0.3rem;
}

.header-banner img {
    border-radius: 12px;
    max-width: 1000px;
    width: 100%;
    height: 90px;
    object-fit: cover;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.user-info {
    text-align: center;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.page-content {
    display: flex;
    gap: 1.5rem;
    padding: 10px;
    justify-content: flex-start;
    position: relative;
    height: calc(100vh - 150px);
}

.nav-buttons {
    position: sticky;
    top: 20px;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    width: 250px;
}

.button {
    background: rgba(32, 77, 98, 255);
    border: none;
    color: white;
    border-radius: 1em;
    transition: all 0.3s ease;
    cursor: pointer;
    padding: 0.6em;
    width: 9em;
    font-size: 1rem;
    font-weight: 500;
}

.button[value="Home"],
.button[value="Gestione anagrafiche"],
.button[value="Gestione anagrafiche ADM"],
.button[value="Situazione prenotazioni"] {
    padding: 0.8em;
    width: 13.5em;
    transition: all 0.3s ease;
    font-size: 1.15rem;
    font-weight: 500;
    font-family: 'Roboto', 'Segoe UI', sans-serif;
}

form .button[value="Prenota"] {
    padding: 0.4em;
    width: 13em;
    font-size: 1rem;
    font-weight: 500;
}

.button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.main-container {
    flex: 1;
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 1rem;
    background: rgba(32, 77, 98, 0.4);
    padding: 0.5rem;
    border-radius: 10px;
    max-width: 1000px;
    margin-left: 165px;
    height: auto;
    max-height: calc(100vh - 200px);
    overflow: hidden;
}

.form-controls {
    display: grid;
    gap: 0.6rem;
}

.form-group {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.5rem;
    border-radius: 8px;
    margin-bottom: 0.4rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.2rem;
    color: #fff;
    font-size: 0.85rem;
}

.form-group select {
    width: 100%;
    padding: 0.3rem;
    height: 32px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    font-size: 0.9rem;
    border-radius: 4px;
}

.names-list select {
    width: 100%;
    height: 250px;
    margin-top: 0.3rem;
}

.sidebar {
    width: 220px;
}

#UO {
    height: 160px;
    font-size: 0.9rem;
}

.date-group {
    display: flex;
    gap: 0.3rem;
}

.date-group select {
    flex: 1;
    height: 32px;
}

select option {
    background-color: var(--primary-color);
    color: white;
    padding: 4px;
}

.helper-text {
    font-size: 0.75rem;
    margin-top: 0.2rem;
    color: rgba(255, 255, 255, 0.8);
}
      </style>
   </head>
   <body>
      <div class="header-banner">
         <img src="./images/BannerCealpi.jpg" alt="Banner Cealpi">
      </div>
      <div class="user-info">
         <span>Utente: <?php echo $row_Username['Grado']; ?> <?php echo $row_Username['Cognome']; ?> <?php echo $row_Username['Nome']; ?></span>
      </div>
      <div class="page-content">
         <div class="nav-buttons">
         <form action="LoginPrenota.php" method="post">
    <input class="button" name="Submit" type="submit" value="Home">
</form>
            <form action="GestAnagrafiche.php" method="post">
               <input class="button" name="Submit" type="submit" value="Gestione anagrafiche">
               <input name="IDun" type="hidden" value="<?php echo $row_UO['ID_UO']; ?>">
            </form>
            <!-- <form action="/ACCESSI/ADMIN/LoginADMIN2.php" method="post">
               <input class="button" name="Submit" type="submit" value="Gestione anagrafiche ADM">
               <input name="IDun" type="hidden" value="<?php echo $row_UO['ID_UO']; ?>">
            </form> -->
            <form action="NewSitPrenotazione.php" method="post">
               <input class="button" name="Submit" type="submit" value="Situazione prenotazioni">
               <input name="GiornoPren" type="hidden" value="<?php echo $_SESSION['AA_pre']."-".$_SESSION['MM_pre']."-".$_SESSION['GG_pre']; ?>">    
            </form>
         </div>
         <div class="main-container">
            <div class="sidebar">
               <h3>U.O. Abilitate:</h3>
               <form name="form2" method="post" action="">
                  <select name="UO" size="20" id="UO" onChange="this.form.submit()">
                     <?php do { ?>
                     <option value="<?php echo $row_UO['ID_UO']?>"><?php echo $row_UO['DEN_UN_OPER']?></option>
                     <?php } while ($row_UO = mysqli_fetch_assoc($UO)); ?>
                  </select>
                  <button class="button" type="submit" name="UO" value="">Annulla filtro</button>
               </form>
            </div>
            <div class="content-area">
               <form action="<?php echo $editFormAction; ?>" method="post" name="form3" id="form3">
                  <div class="form-controls">
                     <div class="form-group">
                        <label>Prenotazioni:</label>
                        <select name="pasto" id="pasto" onChange="this.form.submit()">
                           <option value="1" <?php if (!(strcmp(1, $_SESSION['Pasto']))) {echo "SELECTED";} ?>>PRANZO</option>
                           <option value="2" <?php if (!(strcmp(2, $_SESSION['Pasto']))) {echo "SELECTED";} ?>>CENA</option>
                           <option value="3" <?php if (!(strcmp(3, $_SESSION['Pasto']))) {echo "SELECTED";} ?>>COLAZIONE</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Data:</label>
                        <select name="GG_pre" id="GG_pre" onChange="this.form.submit()">
                           <?php for($i=1; $i<=31; $i++) { 
                              $val = sprintf("%02d", $i);
                              ?>
                           <option value="<?php echo $val?>" <?php if ($_SESSION['GG_pre']==$i) {echo "SELECTED";} ?>><?php echo $val?></option>
                           <?php } ?>
                        </select>
                        <select name="MM_pre" id="MM_pre" onChange="this.form.submit()">
                           <option value="01" <?php if ($_SESSION['MM_pre']==01) {echo "SELECTED";} ?>>gen</option>
                           <option value="02" <?php if ($_SESSION['MM_pre']==02) {echo "SELECTED";} ?>>feb</option>
                           <option value="03" <?php if ($_SESSION['MM_pre']==03) {echo "SELECTED";} ?>>mar</option>
                           <option value="04" <?php if ($_SESSION['MM_pre']==04) {echo "SELECTED";} ?>>apr</option>
                           <option value="05" <?php if ($_SESSION['MM_pre']==05) {echo "SELECTED";} ?>>mag</option>
                           <option value="06" <?php if ($_SESSION['MM_pre']==06) {echo "SELECTED";} ?>>giu</option>
                           <option value="07" <?php if ($_SESSION['MM_pre']==07) {echo "SELECTED";} ?>>lug</option>
                           <option value="08" <?php if ($_SESSION['MM_pre']==8) {echo "SELECTED";} ?>>ago</option>
                           <option value="09" <?php if ($_SESSION['MM_pre']==9) {echo "SELECTED";} ?>>set</option>
                           <option value="10" <?php if ($_SESSION['MM_pre']==10) {echo "SELECTED";} ?>>ott</option>
                           <option value="11" <?php if ($_SESSION['MM_pre']==11) {echo "SELECTED";} ?>>nov</option>
                           <option value="12" <?php if ($_SESSION['MM_pre']==12) {echo "SELECTED";} ?>>dic</option>
                        </select>
                        <select name="AA_pre" id="AA_pre" onChange="this.form.submit()">
                           <?php 
                              $currentYear = date("Y");
                              $startYear = $currentYear - 1;
                              $endYear = $currentYear + 1;
                              
                              for($year = $startYear; $year <= $endYear; $year++) {
                              ?>
                           <option value="<?php echo $year?>" <?php if ($_SESSION['AA_pre']==$year) {echo "SELECTED";} ?>><?php echo $year?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Sede di consumazione:</label>
                        <select name="SelSede" id="SelSede">
                           <option value="0">Sede di default</option>
                           <?php do { ?>
                           <option value="<?php echo $row_Sedi['IDsede']?>"><?php echo $row_Sedi['SEDE']?></option>
                           <?php } while ($row_Sedi = mysqli_fetch_assoc($Sedi)); ?>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Ordina per:</label>
                        <select name="OrdPer" onChange="this.form.submit()">
                           <option value="1" <?php if (!(strcmp(1, $_SESSION['ORDINAX']))) {echo "SELECTED";} ?>>GRADO</option>
                           <option value="2" <?php if (!(strcmp(2, $_SESSION['ORDINAX']))) {echo "SELECTED";} ?>>COGNOME</option>
                        </select>
                     </div>
                     <div class="names-list">
                        <select name="checkbox[]" size="15" multiple>
                           <?php if ($Nomi && mysqli_num_rows($Nomi) > 0) { 
                              do { ?>
                           <option value="<?php echo htmlspecialchars($row_Nomi['IDnome']); ?>">
                              <?php echo htmlspecialchars($row_Nomi['Cognome']." ".$row_Nomi['Nome']." - ".$row_Nomi['Grado']); ?>
                           </option>
                           <?php } while ($row_Nomi = mysqli_fetch_assoc($Nomi));
                              } ?>
                        </select>
                        <p class="helper-text">Tenere premuto il tasto Ctrl per selezionare più nominativi</p>
                     </div>
                     <input name="NomiLib" type="hidden" value="<?php echo ($row_Nomi ? $row_Nomi['IDnome'] : ''); ?>">
                     <input name="gg" type="hidden" value="<?php echo date("Y-m-d")?>">
                     <input name="Ora" type="hidden" value="<?php echo date ("Y-m-d H:i"); ?>">
                     <input type="hidden" name="MM_insert" value="form3">
                     <input name="USR" type="hidden" value="<?php echo $_SESSION['UserID']; ?>">
                     <input name="GioPre" type="hidden" value="<?php echo $_SESSION['GioCor']; ?>">
                     <button class="button" type="submit" name="Submit" value="Prenota">Prenota</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </body>
</html>
<?php
   mysqli_free_result($Username);
   mysqli_free_result($Nomi);
   mysqli_free_result($Sedi);
   ?>