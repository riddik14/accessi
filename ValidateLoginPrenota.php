<?php
/**
 * Validazione login per prenotazione pasti
 * 
 * @version 1.0
 */
require_once('Connections/MyPresenze.php'); 
session_start();

if (isset($_POST['Tx_cert']) && trim($_POST['Tx_cert']) !== '') {
    $input = trim($_POST['Tx_cert']);
    
    // Preparazione query base
    $query_Login = "SELECT IDnome, CF FROM pre_elenconomi WHERE %s=? AND Forza = 1";
    
    // Determina se l'input è un codice fiscale (16 caratteri) o ID
    if (strlen($input) == 16) {
        $fieldName = "CF";
        $queryFinal = sprintf($query_Login, $fieldName);
    } else {
        $input = substr($input, 0, -4);
        $fieldName = "IDnome";
        $queryFinal = sprintf($query_Login, $fieldName);
    }
    
    // Esecuzione query
    $stmt = $PRES_conn->prepare($queryFinal);
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $Login = $stmt->get_result();
    $row_Login = mysqli_fetch_assoc($Login);
    $totalRows_Login = $Login->num_rows;
      // Validazione utente
    if ($totalRows_Login > 0) {
        // Login riuscito
        $_SESSION['autorized'] = true;
        $_SESSION['UserID'] = $row_Login['IDnome'];
        $_SESSION['CF'] = $row_Login['CF'];
        header("Location: PrenotazioneIndividuale.php");
        exit;
    } else {
        // Login fallito
        $_SESSION['login_error'] = "Impossibile verificare l'identità. Assicurarsi di aver inserito correttamente l'ID o il Codice Fiscale.";
        header("Location: LoginPrenota.php");
        exit;
    }
} else {
    // Nessun dato fornito o accesso diretto alla pagina
    unset($_SESSION['autorized']);
    unset($_SESSION['UserID']);
    unset($_SESSION['CF']);
    header("Location: LoginPrenota.php");
    exit;
}

// Rilascio delle risorse
if(isset($Login)) {
    mysqli_free_result($Login);
}

// Chiusura statement
if(isset($stmt)) {
    $stmt->close();
}
?>