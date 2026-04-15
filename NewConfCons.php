<?php 
session_start();
require_once('Connections/MyPresenze.php'); 
$IPadd = $_SERVER['REMOTE_ADDR'];

$parCF_Prova = "%";
if (isset($_GET['CF'])) {
    //<?php echo $row_BarCode['IDnome'] . substr($row_BarCode['CF'], 6, 2) .substr($row_BarCode['CF'], 9, 2) ;12152
  $parCF_Prova = addslashes($_GET['CF']);
}
	$InizioCF = substr ($parCF_Prova, 0, -3);
	if (!(is_numeric($InizioCF))){
		
		$query_Prova = sprintf("SELECT pre_elenconomi.IDnome, pre_elenconomi.CF FROM pre_elenconomi WHERE pre_elenconomi.CF='%s'", $parCF_Prova);
		$Prova = $PRES_conn->query($query_Prova);
		$row_Prova = mysqli_fetch_assoc($Prova);
	} else {
		$lung = strlen($parCF_Prova);
		$parCF_Prova = substr ($parCF_Prova, 0, $lung -4);
		
		$query_Prova = sprintf("SELECT pre_elenconomi.IDnome, pre_elenconomi.CF FROM pre_elenconomi WHERE pre_elenconomi.IDnome='%s'", $parCF_Prova);
		$Prova = $PRES_conn->query($query_Prova);
		$row_Prova = mysqli_fetch_assoc($Prova);
	}
		
//                            Query di controllo se il pasto � gi� stato consumato

    
	$par_IDuser = isset($row_Prova['IDnome']) ? $row_Prova['IDnome'] : null;

	if($par_IDuser) {
		$par_GG = date("Y-m-d");
		$ora_cons = date("Y-m-d H:i:s");
		$parSe = $_SESSION['Sede'];
		$Pasto = $_SESSION['Pasto'];
		$query_Consumato = sprintf("SELECT pre_accessi.IDrecord, pre_accessi.Ora_cons_pr 
									FROM pre_accessi
									WHERE pre_accessi.IDnome='$par_IDuser' 
									AND PASTO='$Pasto' AND GIORNO='$par_GG' AND pre_accessi.Se='$parSe'");
		$Consumato = $PRES_conn->query($query_Consumato);
		$row_Consumato = mysqli_fetch_assoc($Consumato);
		
		if(!isset($row_Consumato['IDrecord'])) {
			$_SESSION['CF'] = $_GET['CF'];
			$_SESSION['CONS'] = 2;
		} else {
			if($row_Consumato['Ora_cons_pr'] <>'') {
				$_SESSION['CF'] = $_GET['CF'];
				$_SESSION['CONS'] = 1;
			} else {
				// Query di inserimento dell'orario di consumazione del pasto
				$updateSQL = "UPDATE pre_accessi SET Ora_cons_pr='$ora_cons', IP='$IPadd'  
							  WHERE IDnome='$par_IDuser' AND PASTO='$Pasto' AND GIORNO='$par_GG' AND Se='$parSe'";
				
				$Result1 = $PRES_conn->query($updateSQL);
				$_SESSION['CF'] = $_GET['CF'];
				$_SESSION['CONS'] = 0;
			}
		}
	} else {
		$_SESSION['CF'] = $_GET['CF'];
		$_SESSION['CONS'] = 2; // Nessuna prenotazione trovata
	}
	
$parGG_Prenotazioni = date("Y-m-d");
$parPasto_Prenotazioni = $_SESSION['Pasto'];
$parCF_Prenotazioni = $_SESSION['CF'];
$InizioCF = substr ($parCF_Prenotazioni, 0, 3);

	if (!(is_numeric($InizioCF))){
		
		$query_Prenotazioni = sprintf("SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ti_R, 
								pre_accessi.Se, pre_accessi.Cons, pre_elenconomi.CF, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.Foto,
								pre_gradi.Grado, pre_accessi.Ora_cons_pr, pre_uo.DEN_UN_OPER, pre_uo.IDturno
								FROM pre_accessi, pre_elenconomi, pre_gradi, pre_uo
								WHERE pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_elenconomi.CF='%s' AND pre_accessi.PASTO='%s' AND pre_elenconomi.IDgrado=pre_gradi.ID
								AND pre_accessi.GIORNO='%s' AND pre_uo.ID_UO=pre_elenconomi.UO", $parCF_Prenotazioni,$parPasto_Prenotazioni,$parGG_Prenotazioni);
		$Prenotazioni = $PRES_conn->query($query_Prenotazioni);
		$row_Prenotazioni = mysqli_fetch_assoc($Prenotazioni);
		$totalRows_Prenotazioni = $Prenotazioni->num_rows;
	} else {
		$lung = strlen($parCF_Prenotazioni);
		$parCF_Prenotazioni = substr ($parCF_Prenotazioni, 0, $lung -4);
		
		$query_Prenotazioni = sprintf("SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ti_R, 
								pre_accessi.Se, pre_accessi.Cons, pre_elenconomi.CF, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.Foto,
								pre_gradi.Grado, pre_accessi.Ora_cons_pr, pre_uo.DEN_UN_OPER, pre_uo.IDturno
								FROM pre_accessi, pre_elenconomi, pre_gradi, pre_uo
								WHERE pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_elenconomi.IDnome='%s' AND pre_accessi.PASTO='%s' AND pre_elenconomi.IDgrado=pre_gradi.ID
								AND pre_accessi.GIORNO='%s' AND pre_uo.ID_UO=pre_elenconomi.UO", $parCF_Prenotazioni,$parPasto_Prenotazioni,$parGG_Prenotazioni);
		$Prenotazioni = $PRES_conn->query($query_Prenotazioni);
		$row_Prenotazioni = mysqli_fetch_assoc($Prenotazioni);
		$totalRows_Prenotazioni = $Prenotazioni->num_rows;
	
	}
if($totalRows_Prenotazioni > 0){
	$_SESSION['Turno'] = $row_Prenotazioni['IDturno'];
}

if (isset($row_Prenotazioni['IDrecord']) && $row_Prenotazioni['Ti_R'] == 1 && $_SESSION['CONS'] == 0) { ?>
    <div class="success-confirmation">
            <?php if ($row_Prenotazioni['Foto']): ?>
                <img class="user-photo" src="<?php echo $row_Prenotazioni['Foto']; ?>" alt="Foto utente">
            <?php else: ?>
                <div class="user-photo default-user-icon">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
            <div class="check-icon">
                <i class="fas fa-check"></i>
            </div>
        <div class="confirmation-details">
            <h2>CONFERMA CONSUMAZIONE</h2>
            <p class="user-name"><?php echo $row_Prenotazioni['Grado'] ." " .$row_Prenotazioni['Cognome']." ".$row_Prenotazioni['Nome']; ?></p>
            <p class="department">REPARTO: <?php echo $row_Prenotazioni['DEN_UN_OPER']; ?></p>
        </div>
    </div>

<?php } elseif (isset($row_Prenotazioni['IDrecord']) && $row_Prenotazioni['Ti_R'] == 3 && $_SESSION['CONS'] == 0) { ?>
    <div class="success-confirmation heavy-ration">
        <div class="photo-container">
            <?php if ($row_Prenotazioni['Foto']): ?>
                <img class="user-photo" src="<?php echo $row_Prenotazioni['Foto']; ?>" alt="Foto utente">
            <?php else: ?>
                <div class="user-photo default-user-icon">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
            <div class="check-icon">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        <div class="confirmation-details">
            <h2>CONFERMA CONSUMAZIONE - RAZIONE PESANTE</h2>
            <p class="user-name"><?php echo $row_Prenotazioni['Grado'] ." " .$row_Prenotazioni['Cognome']." ".$row_Prenotazioni['Nome']; ?></p>
            <p class="department">REPARTO: <?php echo $row_Prenotazioni['DEN_UN_OPER']; ?></p>
        </div>
    </div>

<?php } elseif ($_SESSION['CONS'] == 2) { ?>
    <div class="message-box error">
        <div class="message-icon-container">
            <i class="fas fa-user-slash message-icon"></i>
        </div>
        <div class="message-content">
            <h2>Accesso Negato</h2>
            <p>Nessuna prenotazione trovata</p>
        </div>
    </div>
<?php } elseif ($_SESSION['CONS'] == 1) { ?>
    <div class="message-box warning">
        <div class="message-icon-container">
            <i class="fas fa-circle-exclamation message-icon"></i>
        </div>
        <div class="message-content">
            <h2>Attenzione!</h2>
            <p>Consumazione già registrata</p>
        </div>
    </div>
<?php } 

// Libera i risultati solo se le query hanno prodotto risultati
if (isset($Prenotazioni) && $Prenotazioni instanceof mysqli_result) {
    mysqli_free_result($Prenotazioni);
}

if (isset($Consumato) && $Consumato instanceof mysqli_result) {
    mysqli_free_result($Consumato);
}

if (isset($Prova) && $Prova instanceof mysqli_result) {
    mysqli_free_result($Prova);
}
?>

<style>
/* Stili base ridimensionati */
.success-confirmation, .message-box {
    max-width: 600px;  /* Aumentato da 400px */
    margin: 30px auto;
    border-radius: 30px; /* Aumentato da 20px */
}

/* Modifica dimensioni foto e contenitore */
.user-photo, .default-user-icon {
    max-width: 375px;  /* Aumentato da 250px */
    height: auto;
    border-radius: 22px;
}

.default-user-icon i {
    font-size: 180px;  /* Aumentato da 120px */
}

/* Dimensioni testo aumentate */
.confirmation-details h2 {
    font-size: 3.3rem;  /* Aumentato da 2.2rem */
}

.user-name {
    font-size: 2.6rem;  /* Aumentato da 1.7rem */
}

.department {
    font-size: 2.1rem;  /* Aumentato da 1.4rem */
}

/* Icona di conferma ridimensionata */
.check-icon {
    width: 90px;     /* Aumentato da 60px */
    height: 90px;    /* Aumentato da 60px */
}

.check-icon i {
    font-size: 42px;  /* Aumentato da 28px */
}

/* Message box ridimensionato */
.message-box {
    padding: 60px 45px 75px;
    min-height: 450px;  /* Aumentato da 300px */
}

.message-icon-container {
    width: 180px;   /* Aumentato da 120px */
    height: 180px;  /* Aumentato da 120px */
}

.message-icon {
    font-size: 5.2rem;  /* Aumentato da 3.5rem */
}

/* Responsive per schermi più piccoli con zoom 150% */
@media (max-width: 768px) {
    .success-confirmation {
        margin: 20px;
        padding: 25px;
    }
    
    .user-photo, .default-user-icon {
        max-width: 300px;
    }
    
    .confirmation-details h2 {
        font-size: 2.7rem;
    }
    
    .user-name {
        font-size: 2.1rem;
    }
    
    .department {
        font-size: 1.8rem;
    }
    
    .message-box {
        min-height: 350px;
        padding: 40px 30px;
    }
    
    .message-icon-container {
        width: 150px;
        height: 150px;
    }
}

/* Stili base condivisi */
.success-confirmation, .message-box {
    max-width: 400px;
    margin: 20px auto;
    border-radius: 20px;
    padding: 30px 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    text-align: center;
}

/* Stile conferma consumazione */
.success-confirmation {
    background: linear-gradient(135deg, rgba(32, 77, 98, 0.95), rgba(41, 98, 125, 0.95));
    padding: 35px 60px 35px 35px;  /* Aumentato padding destro a 60px */
    border: 2px solid rgba(255, 255, 255, 0.15);
    transform: translateY(0);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.success-confirmation:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
}

.success-confirmation::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
    pointer-events: none;
}

.user-photo {
    max-width: 250px;
    height: auto;
    border: 4px solid rgba(255, 255, 255, 0.25);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    margin: 0 auto 15px;  /* Ridotto il margin bottom da 20px a 15px */
    transition: all 0.4s ease;
}

.user-photo:hover {
    transform: scale(1.03);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
}

.check-icon {
    position: absolute;
    bottom: 0;           /* Manteniamo la nuova posizione */
    right: 0;           /* Manteniamo la nuova posizione */
    width: 60px;        /* Ripristinato a 60px */
    height: 60px;       /* Ripristinato a 60px */
    background: linear-gradient(145deg, #4CAF50, #45a049);
    border-radius: 20px 0 20px 0;  /* Modificato per adattarsi all'angolo mantenendo lo stile */
    border: 3px solid rgba(255, 255, 255, 0.3);  /* Ripristinato il bordo originale */
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: checkPulse 2s infinite;
    z-index: 2;  /* Assicura che l'icona sia sempre sopra altri elementi */
}

.check-icon i {
    font-size: 28px;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

@keyframes checkPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4); }
    100% { transform: scale(1); }
}

.confirmation-details {
    padding: 0 0 20px;    /* Rimosso padding top */
    margin-top: -20px;    /* Aumentato margin negativo da -10px a -20px */
}

.confirmation-details h2 {
    font-size: 2.2rem;     /* Aumentato da 2rem */
    font-weight: 800;      /* Aumentato da 700 */
    color: white;
    margin-bottom: 8px;  /* Ridotto da 12px */
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3),
                 0 0 20px rgba(255, 255, 255, 0.1);
    letter-spacing: 1.2px; /* Aumentato da 1px */
    text-transform: uppercase;
    line-height: 1.2;      /* Aggiunto per compattare il titolo */
}

.user-name {
    font-size: 1.7rem;    /* Aumentato da 1.5rem */
    color: white;         /* Cambiato da rgba a white per maggiore contrasto */
    margin-top: -5px;     /* Aggiunto margin negativo */
    margin-bottom: 5px;   /* Ridotto da 8px */
    font-weight: 700;     /* Aumentato da 600 */
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    line-height: 1.2;     /* Ridotto da 1.4 */
    padding: 0 10px;      /* Aggiunto padding laterale */
}

.department {
    font-size: 1.4rem;    /* Aumentato da 1.2rem */
    color: rgba(255, 255, 255, 0.95);  /* Aumentata opacità */
    font-weight: 600;     /* Aumentato da 500 */
    text-shadow: 0 2px 3px rgba(0, 0, 0, 0.25);
    letter-spacing: 0.5px;  /* Aggiunto letter-spacing */
    line-height: 1.3;    /* Ridotto da 1.5 */
    margin-top: -2px;     /* Aggiunto margin negativo */
}

/* Stile specifico per razione pesante */
.heavy-ration .check-icon {
    background: linear-gradient(145deg, #2196F3, #1976D2);
}

@keyframes checkPulse {
    0% { transform: scale(1) rotate(0deg); }
    50% { transform: scale(1.1) rotate(5deg); }
    100% { transform: scale(1) rotate(0deg); }
}

/* Messaggi di errore e warning */
.message-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 25px;
    padding: 40px 30px 50px;
    max-width: 500px;  /* Aumentato da 400px */
    min-height: 300px; /* Aggiunto min-height */
    justify-content: center;
}

.message-box.error {
    background: linear-gradient(145deg, #dc3545 0%, #8b1520 100%);  /* Scurito il colore finale */
}

.message-box.warning {
    background: linear-gradient(145deg, #ff9800 0%, #d84315 100%);  /* Scurito il colore finale */
}

.message-icon-container {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    width: 120px;      /* Aumentato da 80px */
    height: 120px;     /* Aumentato da 80px */
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 5px;  /* Ridotto da 10px a 5px */
    border: 4px solid rgba(255, 255, 255, 0.3);  /* Aumentato spessore e opacità del bordo */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.message-icon {
    font-size: 3.5rem; /* Aumentato da 2.5rem */
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.message-content {
    color: white;
    margin-top: 0;  /* Spostato leggermente verso l'alto */
    padding: 0 20px;
}

.message-content h2 {
    font-size: 2.2rem;    /* Aumentato da 1.8rem */
    font-weight: 700;   /* Aumentato da 600 */
    margin-bottom: 20px;  /* Aumentato da 12px */
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3),   /* Migliorato il testo shadow per maggiore leggibilità */
                 0 0 15px rgba(0, 0, 0, 0.2);
    letter-spacing: 0.5px;
}

.message-content p {
    font-size: 1.4rem;    /* Aumentato da 1.2rem */
    opacity: 1;         /* Aumentato da 0.9 */
    margin: 0;
    line-height: 1.5;   /* Aumentato da 1.4 */
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);  /* Aggiunto text shadow al sottotitolo */
    font-weight: 500;   /* Aggiunto peso del font */
    padding: 0 15px;
}

/* Animazioni */
.message-icon-container {
    animation: messageIconEnter 0.5s ease-out;
}

@keyframes messageIconEnter {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Hover effects */
.message-box:hover .message-icon-container {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .message-box {
        margin: 15px;
        padding: 30px 20px;
        min-height: 250px;
    }

    .message-icon-container {
        width: 90px;     /* Aumentato da 60px */
        height: 90px;    /* Aumentato da 60px */
    }

    .message-icon {
        font-size: 2.8rem; /* Aumentato da 2rem */
    }

    .message-content h2 {
        font-size: 1.8rem; /* Aumentato da 1.4rem */
    }

    .message-content p {
        font-size: 1.2rem; /* Aumentato da 1rem */
    }
}

/* Responsive per la conferma */
@media (max-width: 480px) {
    .success-confirmation {
        padding: 25px 50px 25px 25px;  /* Mantenuto più padding a destra anche in mobile */
    }
    
    .user-photo {
        max-width: 200px;
        margin-bottom: 10px;  /* Ridotto ulteriormente in mobile */
    }
    
    .confirmation-details h2 {
        font-size: 1.8rem;
        margin-bottom: 6px;    /* Ridotto ulteriormente */
    }
    
    .user-name {
        font-size: 1.4rem;
        margin-top: -3px;      /* Leggermente ridotto in mobile */
        margin-bottom: 4px;    /* Ridotto in mobile */
    }
    
    .department {
        font-size: 1.2rem;
        margin-top: -1px;      /* Leggermente ridotto in mobile */
    }
    
    .check-icon {
        width: 50px;    /* Ripristinato a 50px */
        height: 50px;   /* Ripristinato a 50px */
        right: 0;
        bottom: 0;
    }
    
    .check-icon i {
        font-size: 22px;
    }
    
    .confirmation-details {
        margin-top: -15px;    /* Aggiustato per mobile */
    }
}

.default-user-icon {
    width: 250px;            /* Larghezza uguale alla foto utente */
    height: 250px;           /* Altezza proporzionata */
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;     /* Mantiene lo stesso border-radius della foto */
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.default-user-icon i {
    font-size: 120px;        /* Icona più grande */
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Aggiustamenti responsive */
@media (max-width: 480px) {
    .default-user-icon {
        width: 200px;        /* Ridotto per mobile come la foto */
        height: 200px;
    }
    
    .default-user-icon i {
        font-size: 90px;     /* Icona proporzionalmente più piccola su mobile */
    }
}
</style>
