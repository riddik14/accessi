<?php
   session_start();
   require_once('Connections/MyPresenze.php');
   $_SESSION['CONS']=0;
   // Inizializza le variabili di ambiente Sede Pasto 
   if (!(isset($_SESSION['Sede'])) && (!(isset($_GET['Sede'])))) {
     header("Location: SelectPostazione.php");
   }
   
   if (!(isset($_SESSION['Sede'])) && (isset($_GET['Sede']))) {
   	$_SESSION['Sede'] = $_GET['Sede'];
   }
    
   if (isset($_SESSION['Sede'])){
   	 $Sede = $_SESSION['Sede'];
   }
   
   
   if (date("H") >= 9 && date("H") <= 15) {
   	$Pasto = 1;
   	$_SESSION['Pasto'] = $Pasto;
   } else {
   	if (date("H") >= 5 && date("H")< 10) {
   		$Pasto = 3;
   		$_SESSION['Pasto'] = $Pasto;
   	} else {
   		$Pasto = 2;
   		$_SESSION['Pasto'] = $Pasto;
   	}	
   };
   
   If ($_SESSION['Pasto'] == 1 ){
   	$_SESSION['NPasto'] = "PRANZO";
   } else if ($_SESSION['Pasto'] == 2) {
   	$_SESSION['NPasto'] = "CENA";
   } else {
   	$_SESSION['NPasto'] ="COLAZIONE";
   }
   
   $Giorno = date("Y-m-d");
   
   // ** Logout the current user. **
   $logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
   if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
     $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
   }
   
   if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
     // Pulizia delle variabili di sessione
     unset($_SESSION['Sede']);
     unset($_SESSION['Pasto']);
   	
     $logoutGoTo = "Home.php";
     if ($logoutGoTo) {
       header("Location: $logoutGoTo");
       exit;
     }
   }
   
   // ********************************* Controllo se il terminale è abilitato
   $IPadd = $_SERVER['REMOTE_ADDR'];
$query_IP = "SELECT pre_ip.IP FROM pre_ip WHERE pre_ip.IP='$IPadd'";
$IP = $PRES_conn->query($query_IP);
$row_IP = mysqli_fetch_assoc($IP);
$totalRows_IP = $IP->num_rows;
if($row_IP['IP'] <> $IPadd) {
	 header("Location: ErroreTerminale.php");
}
   
   
   function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
   {
     global $PRES_conn;
     switch ($theType) {
       case "text":
         $theValue = ($theValue != "") ? "'" . mysqli_real_escape_string($PRES_conn, $theValue) . "'" : "NULL";
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
   
   $maxRows_Prenotazioni = 8;
   $pageNum_Prenotazioni = 0;
   if (isset($_GET['pageNum_Prenotazioni'])) {
     $pageNum_Prenotazioni = $_GET['pageNum_Prenotazioni'];
   }
   $startRow_Prenotazioni = $pageNum_Prenotazioni * $maxRows_Prenotazioni;
   
   
   $query_Prenotazioni = "SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, 
   					   pre_accessi.Ti_R, pre_accessi.Se, pre_accessi.Cons, date_format(pre_accessi.Ora_cons_pr,'%H:%i') AS OraCons, 
   					   pre_tiporazione.TipoRazione, pre_sedi.SEDE, pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, 
   					   pre_elenconomi.CF FROM pre_accessi, pre_tiporazione, pre_sedi, pre_elenconomi, pre_gradi 
   					   WHERE pre_sedi.IDsede=pre_accessi.Se AND pre_tiporazione.ID=pre_accessi.Ti_R AND pre_elenconomi.IDnome=pre_accessi.IDnome 
   					   AND pre_accessi.PASTO='$Pasto' AND pre_accessi.Se='$Sede' AND pre_accessi.GIORNO='$Giorno' AND pre_accessi.Ora_cons_pr> 0
   					   AND pre_gradi.ID=pre_elenconomi.IDgrado ORDER BY pre_accessi.Ora_cons_pr DESC";
   $query_limit_Prenotazioni = sprintf("%s LIMIT %d, %d", $query_Prenotazioni, $startRow_Prenotazioni, $maxRows_Prenotazioni);
   $Prenotazioni = $PRES_conn->query($query_limit_Prenotazioni);
   $row_Prenotazioni = mysqli_fetch_assoc($Prenotazioni);
   
   //Recordset menu pranzo 
   $parSede = $_SESSION['Sede'];
   $query_MenuPranzo = "SELECT pre_piatti.descrizione, pre_piatti.Kcal, pre_piatti.Perc 
   					FROM pre_menu, pre_piatti
   					WHERE pre_piatti.ID=pre_menu.IDpiatto AND pre_menu.Giorno = '$Giorno' AND pre_menu.Pasto = '$Pasto' AND pre_menu.Sede = '$parSede'";
   $MenuPranzo = $PRES_conn->query($query_MenuPranzo);
   $row_MenuPranzo = mysqli_fetch_assoc($MenuPranzo);
   $totalRows_MenuPranzo = $MenuPranzo->num_rows;
   
   //Recordset selezione del turno
   
   $parSede = $_SESSION['Sede'];
   $parOrario = date("H:i");
   $parPasto = $_SESSION['Pasto'];
   
   $query_Turno = "SELECT pre_turni.ID, pre_turni.Descr
   				From pre_turni Inner Join pre_sedi On pre_turni.sede = pre_sedi.IDsede
   				WHERE pre_sedi.IDsede = '$parSede' AND pre_turni.dalle < '$parOrario' AND pre_turni.alle > '$parOrario' AND pre_turni.pasto='$parPasto'";
   $Turno = $PRES_conn->query($query_Turno);
   $row_Turno = mysqli_fetch_assoc($Turno);
   $totalRows_Turno = $Turno->num_rows;
   if (isset($row_Turno['ID'])) {
   	$_SESSION['Turno'] = $row_Turno['ID'];
   }
   
   if (isset($_GET['totalRows_Prenotazioni'])) {
     $totalRows_Prenotazioni = $_GET['totalRows_Prenotazioni'];
   } else {
     $all_Prenotazioni = $PRES_conn->query($query_Prenotazioni);
     $totalRows_Prenotazioni = $all_Prenotazioni->num_rows;
   }
   $totalPages_Prenotazioni = ceil($totalRows_Prenotazioni/$maxRows_Prenotazioni)-1;
   
   
   $query_Sede = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi WHERE pre_sedi.IDsede='$Sede'";
   $Sede = $PRES_conn->query($query_Sede);
   $row_Sede = mysqli_fetch_assoc($Sede);
   $totalRows_Sede = $Sede->num_rows;
   
   
   $query_Cons = "SELECT pre_accessi.IDrecord, pre_accessi.Ora_cons_pr FROM pre_accessi";
   $Cons = $PRES_conn->query($query_Cons);
   $row_Cons = mysqli_fetch_assoc($Cons);
   $totalRows_Cons = $Cons->num_rows;
   
   // Modifica del blocco per gestire le richieste AJAX
   if (isset($_GET['action'])) {
       if ($_GET['action'] == 'updateConteggi') {
           $conteggi = renderContaRazioni();
           
           $html = '<div class="table-title">Riepilogo Distribuzione Pasti</div>';
           $html .= '<div class="table-container"><table>';
           
           $html .= '<tr>';
           $html .= '<th>RAZIONE</th>';
           if (!isset($conteggi['error'])) {
               foreach ($conteggi as $row) {
                   $html .= '<td>' . $row['TipoRazione'] . '</td>';
               }
           }
           $html .= '</tr><tr>';
           $html .= '<th>PRENOTATI</th>';
           if (!isset($conteggi['error'])) {
               foreach ($conteggi as $row) {
                   $html .= '<td>' . ($row['Prenotati'] ?: '0') . '</td>';
               }
           }
           $html .= '</tr><tr>';
           $html .= '<th>CONSUMATI</th>';
           if (!isset($conteggi['error'])) {
               foreach ($conteggi as $row) {
                   $html .= '<td>' . ($row['Consumati'] ?: '0') . '</td>';
               }
           }
           $html .= '</tr><tr>';
           $html .= '<th>DA CONSUMARE</th>';
           if (!isset($conteggi['error'])) {
               foreach ($conteggi as $row) {
                   $html .= '<td>' . (($row['Prenotati'] - $row['Consumati']) ?: '0') . '</td>';
               }
           }
           $html .= '</tr>';
           
           $html .= '</table></div>';
           echo $html;
           
       } elseif ($_GET['action'] == 'updatePassaggi') {
           $passaggi = renderPassaggi();
           
           $html = '<div class="table-title">Ultimi Passaggi</div>';
           $html .= '<div class="table-container"><table>';
           $html .= '<thead><tr>';
           $html .= '<th class="col-nome">Nome</th>';
           $html .= '<th class="col-razione">Razione</th>';
           $html .= '<th class="col-orario">Orario</th>';
           $html .= '</tr></thead><tbody>';
           
           if (!isset($passaggi['error'])) {
               foreach ($passaggi as $row) {
                   $html .= '<tr>';
                   $html .= '<td class="nome-cell"><span class="grado">' . $row['Grado'] . '</span> ' . $row['Cognome'] . ' ' . $row['Nome'] . '</td>';
                   $html .= '<td class="razione-cell">' . $row['TipoRazione'] . '</td>';
                   $html .= '<td class="orario-cell">' . $row['OraCons'] . '</td>';
                   $html .= '</tr>';
               }
           }         
           $html .= '</tbody></table></div>';
           echo $html;
       }
       // Aggiungi qui il nuovo caso per updateTitolo
    elseif ($_GET['action'] == 'updateTitolo') {
      echo "DISTRIBUZIONE " . $_SESSION['NPasto'] . " DEL " . date('d/m/Y');
      exit;
  }
       exit;
   }
   
   // Modifica della funzione renderContaRazioni() per restituire solo i dati
   function renderContaRazioni() {
       global $PRES_conn;
       $Sede = $_SESSION['Sede'];
       $Pasto = $_SESSION['Pasto'];
       $Giorno = date("Y-m-d");
   
       $query_Conteggio = "SELECT riepilogomensa.Se, riepilogomensa.GIORNO, riepilogomensa.PASTO, 
                           riepilogomensa.TipoRazione, Sum(riepilogomensa.Prenotati) AS Prenotati, 
                           Sum(riepilogomensa.Consumati) as Consumati
                           FROM riepilogomensa
                           GROUP BY riepilogomensa.Se, riepilogomensa.GIORNO, riepilogomensa.PASTO, 
                           riepilogomensa.TipoRazione
                           HAVING (((riepilogomensa.Se)='$Sede') AND ((riepilogomensa.GIORNO)='$Giorno') 
                           AND ((riepilogomensa.PASTO)='$Pasto'))";
       
       $Conteggio = $PRES_conn->query($query_Conteggio);
       if (!$Conteggio) {
           return ['error' => $PRES_conn->error];
       }
   
       $data = [];
       while ($row = mysqli_fetch_assoc($Conteggio)) {
           $data[] = $row;
       }
       mysqli_free_result($Conteggio);
       return $data;
   }
   
   // Modifica della funzione renderPassaggi() per restituire solo i dati
   function renderPassaggi() {
       global $PRES_conn;
       $maxRows_Prenotazioni = 10;
       $Sede = $_SESSION['Sede'];
       $Pasto = $_SESSION['Pasto'];
       $Giorno = date("Y-m-d");
   
       $query_Prenotazioni = "SELECT pre_accessi.IDrecord, pre_accessi.IDnome, 
                             date_format(pre_accessi.Ora_cons_pr,'%H:%i') AS OraCons, 
                             pre_tiporazione.TipoRazione, pre_gradi.Grado, 
                             pre_elenconomi.Cognome, pre_elenconomi.Nome 
                             FROM pre_accessi, pre_tiporazione, pre_sedi, pre_elenconomi, pre_gradi 
                             WHERE pre_sedi.IDsede=pre_accessi.Se AND pre_tiporazione.ID=pre_accessi.Ti_R 
                             AND pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_accessi.PASTO='$Pasto' 
                             AND pre_accessi.Se='$Sede' AND pre_accessi.GIORNO='$Giorno' 
                             AND pre_accessi.Ora_cons_pr > 0 AND pre_gradi.ID=pre_elenconomi.IDgrado 
                             ORDER BY pre_accessi.Ora_cons_pr DESC LIMIT $maxRows_Prenotazioni";
   
       $Prenotazioni = $PRES_conn->query($query_Prenotazioni);
       if (!$Prenotazioni) {
           return ['error' => $PRES_conn->error];
       }
   
       $data = [];
       while ($row = mysqli_fetch_assoc($Prenotazioni)) {
           $data[] = $row;
       }
       mysqli_free_result($Prenotazioni);
       return $data;
   }
   ?>
<!DOCTYPE HTML>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title><?php echo date('d/m/Y') . " " . $row_Sede['SEDE']; ?> - DISTRIBUZIONE <?php echo $_SESSION['NPasto'];?></title>
      <!-- Aggiungi questo link per Font Awesome -->
      <link rel="stylesheet" href="style\fonta\css\all.css">
      <style>
         :root {
            --primary-color: rgba(32, 77, 98, 1);
            --primary-transparent: rgba(32, 77, 98, 0.7);
            --white: #ffffff;
            --white-transparent: rgba(255, 255, 255, 0.1);
            --shadow: rgba(0, 0, 0, 0.2);
            --font-size-base: 14px; /* Ridotto da 16px */
         }
         body {
            font-family: 'Roboto', sans-serif;
            color: var(--white);
            background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
            background-size: cover;
            margin: 0;
            padding: 10px; /* Ridotto da 20px */
            min-height: 100vh;
            font-size: var(--font-size-base);
         }
         .header {
            text-align: center;
            margin: 20px auto; /* Aumentato il margine */
            width: 100%;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
         }
         .header h1, .header i {
            font-size: 4.2em;
            margin: 0;
            padding: 0;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px var(--shadow);
            text-align: center;
            width: 100%;
         }
         .header i {
            margin-bottom: 20px; /* Aumentato lo spazio tra l'icona e il titolo */
            font-size: 4em;
            display: block;
         }
         .container {
            max-width: 1650px;
            margin: 5px auto 0;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            padding: 5px;
            justify-content: center;
            margin-top: 80px; /* Modificato da 40px a 80px per spostare i container più in basso */
         }
         .panel {
            flex: 1;
            min-width: 300px; /* Ridotto da 400px */
            max-width: 900px;
            background: var(--white-transparent);
            border-radius: 15px;
            padding: 10px; /* Ridotto da 20px */
            box-shadow: 0 8px 32px var(--shadow);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            flex-grow: 1;
            height: auto;
         }
         .table-container {
            width: 100%;
            margin: 0;
            padding: 0;
         }
         .table-container table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
         }
         .table-container th, .table-container td {
            padding: 5px; /* Ridotto da 8px */
            text-align: center;
            color: var(--white);
            text-shadow: 1px 1px 2px var(--shadow);
            font-size: 1.3em; /* Aggiunto per ridurre dimensione testo */
         }
         .table-container th {
            background: var(--primary-color);
            font-size: 1em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
         }
         .table-title {
            width: 100%;
            color: var(--white);
            font-size: 1.4em; /* Ridotto da 1.4em */
            font-weight: 600;
            text-align: center;
            margin: 0 0 5px 0; /* Ridotto da 10px */
            padding: 5px 0; /* Ridotto da 10px */
            background: var(--primary-transparent);
            border-radius: 8px;
            letter-spacing: 2px;
         }
         @media screen and (max-width: 1400px) {
            .container {
               flex-direction: column;
               align-items: center;
            }
            .panel {
               min-width: 95%;
               margin-bottom: 15px;
            }
         }
         .input-container {
            width: 100%;
            margin: 0;
            padding: 0;
            text-align: center;
         }
         .input-container h2 {
            color: var(--white);
            font-size: 1.5em;
            font-weight: 600;
            text-shadow: 0 2px 4px var(--shadow);
            margin: 0 auto 20px;  /* Modificato da "10px auto" a "0 auto 15px" per spostare il titolo più in alto */
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            max-width: 80%;
            line-height: 1.4;
            width: fit-content;  /* Added to ensure proper centering */
            display: block;      /* Added to ensure proper centering */
         }
         .input-container input[type="text"] {
            width: 200px;  /* Reduced from 300px */
            height: 40px;  /* Added explicit height */
            margin: 1px 0;
            padding: 8px 50px;  /* Reduced padding and made it equal on both sides */
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            background: var(--white-transparent);
            color: var(--white);
            font-size: 1.2em;
            font-weight: 500;
            text-align: center;
            letter-spacing: 1.5px;
            transition: all 0.3s ease;
            outline: none;
            font-family: 'Roboto', sans-serif;
         }
         
         .input-container input[type="text"]:focus {
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
         }
         
         .input-container button {
            margin: 10px 0 5px; /* Aggiunto margine verticale */
            padding: 12px 40px; /* Aumentato il padding */
            border: none;
            border-radius: 50px;
            background: var(--primary-color);
            color: var(--white);
            font-size: 1.2em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px var(--shadow);
         }
         .overlay-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
         }
         .message-overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            min-height: 200px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
         }
         .show {
            opacity: 1;
            visibility: visible;
         }
         .page-content.blur {
            filter: blur(5px);
            opacity: 0.7;
            pointer-events: none;
         }
         .container.enlarged {
            align-items: stretch;
         }
         .left-panel {
            height: 100%;
         }
         /* Stili specifici per la tabella di riepilogo */
         .left-panel .table-container table {
            background: rgba(255, 255, 255, 0.1);
            margin-top: 10px;
         }
         .left-panel .table-container th {
            background: var(--primary-color);
            font-size: 1.3em;
            padding: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            text-align: center; /* Aggiunto per centrare */
         }
         .left-panel .table-container td {
            font-size: 1.2em;
            font-weight: 600;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            text-align: center; /* Aggiunto per centrare */
         }
         /* Rimuovo l'allineamento a sinistra precedente */
         .left-panel .table-container th:first-child,
         .left-panel .table-container td:first-child {
            text-align: center; /* Modificato da left a center */
            padding-left: 20px;
         }
         /* Stile per il contenitore dell'input e dell'icona */
         .scan-container {
            width: 500px;  /* Reduced from 600px to match new input size */
            position: relative;
            width: 600px;
            margin: 70px auto 0;
            padding: 30px;
            background: var(--white-transparent);
            border-radius: 25px;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.15);
         }
         .input-group {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0 15px;    /* Modificato da "15px 0" per compensare lo spazio */
         }
         .scan-icon {
            position: absolute;
            right: 40px;  /* Adjusted position */
            top: 50%;
            transform: translateY(-50%);
            font-size: 2em;
            color: var(--white);
            transition: all 0.3s ease;
            text-shadow: 0 0 10px rgba(0,0,0,0.3);
            padding: 10px;
            background: var(--primary-color);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px var(--shadow);
            border: 2px solid rgba(255, 255, 255, 0.2);
         }
         .scan-icon:hover {
            transform: translateY(-50%) scale(1.1);
            background: var(--primary-transparent);
         }
         .scan-icon.success {
            background: #4CAF50;
            animation: successPulse 0.5s ease;
         }
         .scan-icon.error {
            background: #F44336;
            animation: errorShake 0.5s ease;
         }
         .scan-icon.warning {
            background: #FFA500;
            animation: warningPulse 0.5s ease;
         }
         @keyframes successPulse {
            0% { transform: translateY(-50%) scale(1); }
            50% { transform: translateY(-50%) scale(1.2); }
            100% { transform: translateY(-50%) scale(1); }
         }
         @keyframes errorShake {
            0%, 100% { transform: translateY(-50%) translateX(0); }
            25% { transform: translateY(-50%) translateX(-10px); }
            75% { transform: translateY(-50%) translateX(10px); }
         }
         @keyframes warningPulse {
            0% { transform: translateY(-50%) scale(1); }
            50% { transform: translateY(-50%) scale(1.2); }
            100% { transform: translateY(-50%) scale(1); }
         }
      </style>
   </head>
   <body>
      <div id="overlay-backdrop" class="overlay-backdrop"></div>
      <div id="messageOverlay" class="message-overlay"></div>
      <div class="page-content">
         <div class="header">
            <i class="fa fa-utensils"></i>
            <h1> DISTRIBUZIONE <?php echo $_SESSION['NPasto'];?> DEL <?php echo date('d/m/Y') ; ?></h1>
         </div>
         <div class="scan-container">
            <div class="input-container">
               <h2>Inserire il C.F. o il PIN utente:</h2>
               <div class="input-group">
                  <input type="text" id="CF" onKeyPress="return runScript(event);" autocomplete="off" />
                  <i class="fas fa-qrcode scan-icon" id="scanIcon"></i>
               </div>
               <button type="button" onclick="submitCF()">Invia</button>
            </div>
         </div>
         <!-- Assicurati che il container abbia la classe "enlarged" -->
         <div class="container enlarged">
            <div class="left-panel panel">
               <div id="Conteggi">
                  <div class="table-title">Riepilogo Distribuzione Pasti</div>
                  <div class="table-container">
                     <table>
                        <tr>
                           <th>RAZIONE</th>
                           <?php 
                              $conteggi = renderContaRazioni();
                              if (!isset($conteggi['error'])) {
                                  foreach ($conteggi as $row) { 
                                      echo "<td>" . $row['TipoRazione'] . "</td>";
                                  }
                              } else {
                                  echo "<td>-</td>";
                              }
                              ?>
                        </tr>
                        <tr>
                           <th>PRENOTATI</th>
                           <?php 
                              if (!isset($conteggi['error'])) {
                                  foreach ($conteggi as $row) { 
                                      echo "<td>" . ($row['Prenotati'] ?: '0') . "</td>";
                                  }
                              } else {
                                  echo "<td>0</td>";
                              }
                              ?>
                        </tr>
                        <tr>
                           <th>CONSUMATI</th>
                           <?php 
                              if (!isset($conteggi['error'])) {
                                  foreach ($conteggi as $row) { 
                                      echo "<td>" . ($row['Consumati'] ?: '0') . "</td>";
                                  }
                              } else {
                                  echo "<td>0</td>";
                              }
                              ?>
                        </tr>
                        <tr>
                           <th>DA CONSUMARE</th>
                           <?php 
                              if (!isset($conteggi['error'])) {
                                  foreach ($conteggi as $row) { 
                                      echo "<td>" . (($row['Prenotati'] - $row['Consumati']) ?: '0') . "</td>";
                                  }
                              } else {
                                  echo "<td>0</td>";
                              }
                              ?>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>
            <div class="right-panel panel">
               <div id="Passaggi">
                  <div class="table-title">Ultimi Passaggi</div>
                  <div class="table-container">
                     <table>
                        <thead>
                           <tr>
                              <th>Nome</th>
                              <th>Razione</th>
                              <th>Orario</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php 
                              $passaggi = renderPassaggi();
                              if (!isset($passaggi['error'])) {
                                  foreach ($passaggi as $row) { 
                                      echo "<tr>";
                                      echo "<td>" . $row['Grado'] . " " . $row['Cognome'] . " " . $row['Nome'] . "</td>";
                                      echo "<td>" . $row['TipoRazione'] . "</td>";
                                      echo "<td>" . $row['OraCons'] . "</td>";
                                      echo "</tr>";
                                  }
                              }
                              ?>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script>
         function runScript(e) {
    if (e.keyCode == 13) {
        submitCF();
        return false;
    }
    return true;
}

let refreshInterval;  // Variabile per gestire l'intervallo

function updateTitle() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.querySelector('.header h1').innerHTML = xhr.responseText;
        }
    };
    xhr.open("GET", "PostMensaFull.php?action=updateTitolo", true);
    xhr.send();
}

function startAutoRefresh() {
    // Esegui subito il primo aggiornamento
    showConteggi();
    showPassaggi();
    updateTitle();
    
    // Imposta l'aggiornamento automatico ogni 2 secondi
    refreshInterval = setInterval(function() {
        showConteggi();
        showPassaggi();
        updateTitle();
    }, 2000);
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

function submitCF() {
    var cf = document.getElementById('CF').value;
    if(cf.trim() !== '') {
        stopAutoRefresh();
        
        var scanIcon = document.getElementById('scanIcon');
        var messageOverlay = document.getElementById('messageOverlay');
        var backdrop = document.getElementById('overlay-backdrop');
        var pageContent = document.querySelector('.page-content');
        
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log("Risposta ricevuta:", xhr.responseText);
                
                // Rimuovi tutti gli stati precedenti
                scanIcon.classList.remove('success', 'error', 'warning');
                
                // Controllo più specifico del contenuto della risposta
                if(xhr.responseText.includes('CONFERMA CONSUMAZIONE')) {
                    scanIcon.classList.add('success');
                } else if(xhr.responseText.includes('Consumazione già registrata')) {
                    scanIcon.classList.add('warning');
                } else if(xhr.responseText.includes('Nessuna prenotazione trovata')) {
                    scanIcon.classList.add('error');
                }
                
                setTimeout(function() {
                    messageOverlay.innerHTML = xhr.responseText;
                    backdrop.classList.add('show');
                    messageOverlay.classList.add('show');
                    pageContent.classList.add('blur');
                    
                    playSound(messageOverlay);
                    
                    setTimeout(function() {
                        scanIcon.classList.remove('success', 'error', 'warning');
                        backdrop.classList.remove('show');
                        messageOverlay.classList.remove('show');
                        pageContent.classList.remove('blur');
                        startAutoRefresh();
                    }, 3000);
                    
                }, 500);
                
                document.getElementById('CF').value = "";
                document.getElementById('CF').focus();
            }
        };
        xhr.open("GET", "NewConfCons.php?CF=" + encodeURIComponent(cf), true);
        xhr.send();
    }
}

function playSound(messageElement) {
    var soundFile = '';
    if(messageElement.querySelector('.success-message')) {
        soundFile = 'Si.mp3';
    } else if(messageElement.querySelector('.error-message')) {
        soundFile = 'No.mp3';
    }
    if(soundFile) {
        new Audio(soundFile).play();
    }
}

function showConteggi() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("Conteggi").innerHTML = xhr.responseText;
        }
    };
    xhr.open("GET", "PostMensaFull.php?action=updateConteggi", true);
    xhr.send();
}

function showPassaggi() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("Passaggi").innerHTML = xhr.responseText;
        }
    };
    xhr.open("GET", "PostMensaFull.php?action=updatePassaggi", true);
    xhr.send();
}

// Inizializzazione al caricamento della pagina
window.onload = function() {
    startAutoRefresh();
    document.getElementById('CF').focus();
};
      </script>
   </body>
</html>
<?php
   mysqli_free_result($Prenotazioni);
   mysqli_free_result($Sede);
   mysqli_free_result($Cons);
   mysqli_free_result($MenuPranzo);
   mysqli_free_result($Turno);
   ?>

