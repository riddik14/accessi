<?php
/**
 * Sistema di Prenotazione Pasti
 * 
 * Gestione prenotazione individuale pasti.
 * 
 * @version 1.2
 * @author Servizio Mensa
 */

require_once('Connections/MyPresenze.php');
session_start();

define('MAX_BOOKING_DAYS', 8);

// --- AUTENTICAZIONE E LOGOUT ---
function gestisciAutenticazione() {
    if (!isset($_SESSION['UserID'])) {
        header("Location: LoginPrenota.php");
        exit;
    }
    if (!empty($_GET['doLogout'])) {
        foreach (['MM_Username', 'MM_UserGroup', 'GioPre', 'UserID'] as $var) unset($_SESSION[$var]);
        header("Location: LoginPrenota.php");
        exit;
    }
}

// --- DATA PRENOTAZIONE ---
function gestisciDataPrenotazione() {
    if (!isset($_SESSION['GioPre'])) $_SESSION['GioPre'] = date("d-m-Y");
    if (!empty($_POST['Giorno'])) {
        $today = strtotime(date("Y-m-d"));
        $selected = strtotime($_POST['Giorno']);
        $max = strtotime("+".MAX_BOOKING_DAYS." days", $today);
        if ($selected < $today || $selected > $max) {
            $_SESSION['GioPre'] = date("d-m-Y");
            header("Location: ErroreData.php");
            exit;
        }
        $_SESSION['GioPre'] = $_POST['Giorno'];
    }
}

// --- PRENOTAZIONI ---
function gestisciPrenotazioni($conn) {
    for ($i = 1; $i <= 3; $i++) {
        $formKey = "MM_insert$i";
        if (!empty($_POST[$formKey]) && $_POST[$formKey] === "form$i") {
            $fields = ['IDUSR', 'GIORNO', 'Pasto', 'Ora_pren', 'TipoRazione', 'sede', 'Pagamento'];
            foreach ($fields as $f) if (!isset($_POST[$f])) return;
            inserisciPrenotazione($conn);
        }
    }
    if (!empty($_POST["MM_delete"]) && in_array($_POST["MM_delete"], ["form5", "form6", "form7"]) && !empty($_POST['IDrecord'])) {
        $stmt = $conn->prepare("DELETE FROM pre_accessi WHERE IDrecord = ?");
        if ($stmt) {
            $stmt->bind_param("i", $_POST['IDrecord']);
            $stmt->execute();
            $stmt->close();
        }
    }
}

function inserisciPrenotazione($conn) {
    $sql = "INSERT INTO pre_accessi (IDnome, GIORNO, PASTO, Ora_pren, USR, Ti_R, Se, Pagamento, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("isisiiiss",
        $_POST['IDUSR'],
        $_POST['GIORNO'],
        $_POST['Pasto'],
        $_POST['Ora_pren'],
        $_POST['IDUSR'],
        $_POST['TipoRazione'],
        $_POST['sede'],
        $_POST['Pagamento'],
        $_SERVER['REMOTE_ADDR']
    );
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}

// --- DATI UTENTE E OPZIONI ---
function getUserDataAndOptions($conn, $userId) {
    $data = ['user' => null, 'sedi' => [], 'tipi_razione' => []];
    $userQuery = "SELECT en.IDnome, g.Grado, en.Cognome, en.Nome, en.UO, en.SedeSomm, en.TipoRazione, en.Foto, en.TipoRazioneCe, en.TipoRazioneCol, uo.DEN_UN_OPER, uo.ObbligoCMD
        FROM pre_elenconomi AS en
        INNER JOIN pre_uo AS uo ON en.UO = uo.ID_UO
        INNER JOIN pre_gradi AS g ON en.IDgrado = g.ID
        WHERE en.IDnome = ?";
    $stmt = $conn->prepare($userQuery);
    if (!$stmt) return $data;
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) $data['user'] = $result->fetch_assoc();
    $stmt->close();

    $optionsQuery = "(SELECT 'sedi' AS tipo, CAST(IDsede AS CHAR) AS id, SEDE AS nome FROM pre_sedi ORDER BY SEDE)
                     UNION ALL
                     (SELECT 'razioni' AS tipo, CAST(ID AS CHAR) AS id, TipoRazione AS nome FROM pre_tiporazione ORDER BY ID)";
    $result = $conn->query($optionsQuery);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['tipo'] === 'sedi') $data['sedi'][$row['id']] = $row['nome'];
            else $data['tipi_razione'][$row['id']] = $row['nome'];
        }
    }
    return $data;
}

function getUserPrenotazioni($conn, $userId, $sessionDate) {
    $gg = date("Y-m-d", strtotime($sessionDate));
    $ggcol = date("Y-m-d", strtotime($sessionDate . ' +1 day'));
    $out = ['pranzo' => null, 'cena' => null, 'colazione' => null, 'ParGG' => $gg, 'ParGGcol' => $ggcol];
    $q = "SELECT a.IDrecord, a.IDnome, a.GIORNO, a.PASTO, a.Ora_pren, a.USR, a.Ora_cons_pr, a.Ti_R AS TipoRazione, s.SEDE
        FROM pre_accessi a
        JOIN pre_sedi s ON s.IDsede = a.Se
        WHERE a.IDnome = ? AND a.GIORNO IN (?, ?) AND a.PASTO IN (1,2,3)";
    $stmt = $conn->prepare($q);
    if (!$stmt) return $out;
    $stmt->bind_param("sss", $userId, $gg, $ggcol);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['PASTO'] == 1 && $row['GIORNO'] == $gg) $out['pranzo'] = $row;
            elseif ($row['PASTO'] == 2 && $row['GIORNO'] == $gg) $out['cena'] = $row;
            elseif ($row['PASTO'] == 3 && $row['GIORNO'] == $ggcol) $out['colazione'] = $row;
        }
    }
    $stmt->close();
    return $out;
}

// --- UTILITY ---
function isPastoPrenotabile($data_pasto, $ora_limite) {
    return time() <= strtotime($data_pasto . " " . $ora_limite);
}

// --- HTML GENERATOR ---
function generaHtml($sedi, $tipi_razione, $editFormAction) {
    $generaDropdown = function($options, $selected) {
        $out = "";
        foreach ($options as $id => $nome) {
            $sel = ($id == $selected) ? " selected" : "";
            $out .= "<option value=\"{$id}\"{$sel}>{$nome}</option>\n";
        }
        return $out;
    };
    $generaFormPrenotazione = function($form_name, $pasto_id, $giorno, $row_User, $tipo_razione_default) use ($generaDropdown, $sedi, $tipi_razione, $editFormAction) {
        if (empty($tipi_razione) || empty($sedi)) return '<div class="reservation-status"><p>Impossibile generare il form di prenotazione. Dati mancanti.</p></div>';
        $select_id_base = ($pasto_id * 2) + 5;
        $user_id = $_SESSION['UserID'] ?? '';
        return '<form action="'.$editFormAction.'" method="POST" name="'.$form_name.'" class="meal-form">
            <div class="form-group">
                <label for="select'.$select_id_base.'">Tipo di razione</label>
                <select name="TipoRazione" id="select'.$select_id_base.'" class="meal-select form-control">
                    '.$generaDropdown($tipi_razione, $tipo_razione_default).'
                </select>
            </div>
            <div class="form-group">
                <label for="select'.($select_id_base + 1).'">Sede mensa</label>
                <select name="sede" id="select'.($select_id_base + 1).'" class="meal-select form-control">
                    '.$generaDropdown($sedi, $row_User['SedeSomm']).'
                </select>
            </div>
            <div class="payment-field">
                <label class="payment-label">A pagamento</label>
                <select name="Pagamento" class="payment-select form-control">
                    <option value="1">Si</option>
                    <option value="0" selected>No</option>
                </select>
            </div>
            <button type="submit" class="submit-button">Prenota</button>
            <input name="IDUSR" type="hidden" value="'.$user_id.'">
            <input name="Ora_pren" type="hidden" value="'.date("Y-m-d H:i").'">
            <input name="Pasto" type="hidden" value="'.$pasto_id.'">
            <input name="GIORNO" type="hidden" value="'.$giorno.'">
            <input name="MM_insert'.$pasto_id.'" type="hidden" value="'.$form_name.'">
        </form>';
    };
    $generaStatoPrenotazione = function($prenotazione, $form_name, $orario_limite, $session_giorno) use ($editFormAction) {
        if (empty($prenotazione)) return '<div class="reservation-status"><p>Nessuna prenotazione trovata</p></div>';
        $html = '<div class="reservation-status">';
        if (isset($prenotazione['Ora_cons_pr'])) {
            $html .= '<div class="reservation-card consumed">
                <div class="card-icon-wrapper"><i class="fas fa-utensils card-icon"></i></div>
                <div class="card-content">
                    <h3 class="card-title"><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Pasto Consumato</h3>
                    <div class="reservation-timestamp"><i class="fas fa-clock"></i> '.$prenotazione['Ora_cons_pr'].'</div>
                </div>
                <div class="card-status-badge consumed"><i class="fas fa-check"></i></div>
            </div>';
        } else {
            $sede = htmlspecialchars($prenotazione['SEDE']);
            $tipoRazione = htmlspecialchars($prenotazione['TipoRazione'] ?? 'Standard');
            $html .= '<div class="reservation-card booked">
                <div class="card-icon-wrapper"><i class="fas fa-clipboard-check card-icon"></i></div>
                <div class="card-content">
                    <h3 class="card-title"><i class="fas fa-bookmark" style="color: var(--accent-color);"></i> Prenotazione Confermata</h3>
                    <div class="reservation-details">
                        <div class="detail-row"><i class="fas fa-map-marker-alt"></i><span>'.$sede.'</span></div>
                        <div class="detail-row"><i class="fas fa-utensils"></i><span>'.$tipoRazione.'</span></div>
                    </div>
                </div>
            </div>';
            if (isPastoPrenotabile($session_giorno, $orario_limite)) {
                $idrecord = $prenotazione['IDrecord'];
                $html .= '<form action="'.$editFormAction.'" method="post" name="'.$form_name.'" class="cancel-form">
                    <button type="submit" class="cancel-button">
                        <i class="fas fa-times-circle"></i>
                        <span>Annulla prenotazione</span>
                    </button>
                    <input name="IDrecord" type="hidden" value="'.$idrecord.'">
                    <input name="MM_delete" type="hidden" value="'.$form_name.'">
                </form>';
            }
        }
        $html .= '</div>';
        return $html;
    };
    return ['generaFormPrenotazione' => $generaFormPrenotazione, 'generaStatoPrenotazione' => $generaStatoPrenotazione];
}

// --- INIZIALIZZAZIONE ---
gestisciAutenticazione();
gestisciDataPrenotazione();
$editFormAction = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? "?" . htmlentities($_SERVER['QUERY_STRING']) : "");
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true" . (isset($_SERVER['QUERY_STRING']) ? "&" . htmlentities($_SERVER['QUERY_STRING']) : "");
gestisciPrenotazioni($PRES_conn);

$user_id = $_SESSION['UserID'];
$dataResult = getUserDataAndOptions($PRES_conn, $user_id);
$userData = $dataResult['user'];
$sedi = $dataResult['sedi'];
$tipi_razione = $dataResult['tipi_razione'];
if (!$userData) {
    header("Location: ErroreTerminale.php");
    exit;
}
$row_User = $userData;
$orari_limite = [1 => "09:00", 2 => "09:00", 3 => "23:59"];
$prenotationData = getUserPrenotazioni($PRES_conn, $user_id, $_SESSION['GioPre']);
$row_Prenotazione_Pr = $prenotationData['pranzo'];
$row_Prenotazione_Ce = $prenotationData['cena'];
$row_Prenotazione_Col = $prenotationData['colazione'];
$dataFormattataGiorno = $prenotationData['ParGG'];
$dataFormattataGiornoSuccessivo = $prenotationData['ParGGcol'];
$htmlGenerators = generaHtml($sedi, $tipi_razione, $editFormAction);
$generaFormPrenotazione = $htmlGenerators['generaFormPrenotazione'];
$generaStatoPrenotazione = $htmlGenerators['generaStatoPrenotazione'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome e Google Fonts con preload per migliorare il caricamento -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" 
          as="style" onload="this.onload=null;this.rel='stylesheet'" 
          integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" 
          as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    </noscript>
    <title>Sistema di Prenotazione Pasti - <?php echo ($row_User['Cognome']." ". $row_User['Nome']);?></title>    <style>
        /* --- MODERN PROFESSIONAL THEME 2024 - ENHANCED --- */
        :root {
            --primary: #1a457a;
            --primary-light: #2d6eb6;
            --primary-dark: #0b2b4d;
            --accent: #4caf50;
            --accent-light: #6abf6e;
            --danger: #e53935;
            --warning: #ff9800;
            --success: #00897b;
            --surface: #fff;
            --surface-dark: #f4f7fd;
            --text-main: #1a2138;
            --text-muted: #5a6882;
            --radius: 16px;
            --radius-lg: 24px;
            --radius-sm: 8px;
            --shadow: 0 10px 30px -5px rgba(17, 24, 39, 0.1), 0 8px 10px -6px rgba(17, 24, 39, 0.05);
            --shadow-hover: 0 20px 40px -5px rgba(17, 24, 39, 0.15), 0 10px 20px -5px rgba(17, 24, 39, 0.1);
            --transition: 0.25s cubic-bezier(.4,0,.2,1);
            --transition-bounce: 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            --header-height: 70px;
            --container-width: 1200px;
            --font-sans: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }        html, body {
            font-family: var(--font-sans);
            font-size: 16px;
            color: var(--text-main);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 60%, var(--primary-light) 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='400' viewBox='0 0 800 800'%3E%3Cg fill='none' stroke='%23FFFFFF' stroke-opacity='0.03' stroke-width='1'%3E%3Cpath d='M769 229L1037 260.9M927 880L731 737 520 660 309 538 40 599 295 764 126.5 879.5 40 599-197 493 102 382-31 229 126.5 79.5-69-63'/%3E%3Cpath d='M-31 229L237 261 390 382 603 493 308.5 537.5 101.5 381.5M370 905L295 764'/%3E%3Cpath d='M520 660L578 842 731 737 840 599 603 493 520 660 295 764 309 538 390 382 539 269 769 229 577.5 41.5 370 105 295 -36 126.5 79.5 237 261 102 382 40 599 -69 737 127 880'/%3E%3Cpath d='M520-140L578.5 42.5 731-63M603 493L539 269 237 261 370 105M902 382L539 269M390 382L102 382'/%3E%3Cpath d='M-222 42L126.5 79.5 370 105 539 269 577.5 41.5 927 80 769 229 902 382 603 493 731 737M295-36L577.5 41.5M578 842L295 764M40-201L127 80M102 382L-261 269'/%3E%3C/g%3E%3Cg fill='%23FFFFFF' fill-opacity='0.02'%3E%3Ccircle cx='769' cy='229' r='5'/%3E%3Ccircle cx='539' cy='269' r='5'/%3E%3Ccircle cx='603' cy='493' r='5'/%3E%3Ccircle cx='731' cy='737' r='5'/%3E%3Ccircle cx='520' cy='660' r='5'/%3E%3Ccircle cx='309' cy='538' r='5'/%3E%3Ccircle cx='295' cy='764' r='5'/%3E%3Ccircle cx='40' cy='599' r='5'/%3E%3Ccircle cx='102' cy='382' r='5'/%3E%3Ccircle cx='127' cy='80' r='5'/%3E%3Ccircle cx='370' cy='105' r='5'/%3E%3Ccircle cx='578' cy='42' r='5'/%3E%3Ccircle cx='237' cy='261' r='5'/%3E%3Ccircle cx='390' cy='382' r='5'/%3E%3C/g%3E%3C/svg%3E"),
                radial-gradient(circle at 30% 20%, rgba(255,255,255,0.08) 0, transparent 60%),
                radial-gradient(circle at 70% 80%, rgba(255,255,255,0.06) 0, transparent 60%);
            z-index: -1;
            background-size: 120% 120%, 100% 100%, 100% 100%;
            animation: bg-subtle-move 120s linear infinite;
        }
        @keyframes bg-subtle-move {
            0% { background-position: 0% 0%, 0% 0%, 0% 0%; }
            50% { background-position: 100% 100%, 20% 20%, -20% -20%; }
            100% { background-position: 0% 0%, 0% 0%, 0% 0%; }
        }
        header {
            background: rgba(13, 36, 62, 0.95);
            box-shadow: var(--shadow);
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: var(--container-width);
            margin: 0 auto;
        }
        .navbar-brand img {
            height: 48px;
            border-radius: var(--radius-sm);
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }
        .navbar-brand img:hover {
            transform: scale(1.05);
        }
        .navbar-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 14px;
            letter-spacing: 0.01em;
            text-shadow: 0 2px 8px rgba(30,73,118,0.12);
            position: relative;
        }
        .navbar-title-icon {
            color: var(--accent-light);
            font-size: 1.5rem;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
        }
        .navbar-title:hover .navbar-title-icon {
            transform: rotate(15deg);
        }
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 18px;
        }        .user-dropdown-simple {
            position: relative;
        }
        .dropdown-toggle-simple {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 20px;
            border-radius: 999px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .dropdown-toggle-simple:hover, .dropdown-toggle-simple:focus {
            background: rgba(255,255,255,0.15);
            border-color: var(--accent-light);
            transform: translateY(-2px);
        }
        .dropdown-toggle-simple:active {
            transform: translateY(0);
        }
        .default-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--accent-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: 2px solid rgba(255,255,255,0.18);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .user-name {
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
            color: #fff;
        }
        .user-surname { font-weight: 700; }
        .dropdown-menu-simple {
            position: absolute;
            right: 0;
            top: calc(100% + 12px);
            min-width: 280px;
            background: rgba(16,41,66,0.98);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.98);
            transition: var(--transition-bounce);
            overflow: hidden;
            z-index: 1050;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .dropdown-menu-simple.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        .dropdown-menu-simple::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 20px;
            width: 16px;
            height: 16px;
            background: rgba(16,41,66,0.98);
            transform: rotate(45deg);
            border-left: 1px solid rgba(255,255,255,0.12);
            border-top: 1px solid rgba(255,255,255,0.12);
        }
        .dropdown-menu-simple.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        .dropdown-header {
            padding: 18px 18px 12px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.10);
            background: linear-gradient(180deg, rgba(255,255,255,0.06), transparent);
        }
        .user-info-extended {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .user-photo-container {
            flex-shrink: 0;
        }
        .user-avatar, .default-user-photo {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.22);
            box-shadow: 0 3px 10px rgba(0,0,0,0.13);
            font-size: 1.3rem;
        }
        .user-details {
            display: flex;
            flex-direction: column;
        }
        .user-role {
            font-size: 0.93rem;
            color: var(--accent-light);
            margin-top: 2px;
        }
        .dropdown-body {
            padding: 10px 0;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 13px;
            text-decoration: none;
            color: #fff;
            padding: 10px 22px;
            transition: var(--transition);
            border-radius: var(--radius);
            font-weight: 500;
        }
        .dropdown-item:hover, .dropdown-item:focus {
            background: rgba(91,184,95,0.13);
            color: var(--accent-light);
        }
        .dropdown-item i {
            font-size: 1.1rem;
            color: var(--accent-light);
        }
        .dropdown-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.13), transparent);
            margin: 8px 0;
        }
        .dropdown-item[href*="doLogout"] {
            color: var(--danger);
        }
        .dropdown-item[href*="doLogout"]:hover {
            background: rgba(230,57,70,0.13);
            color: var(--danger);
        }        /* --- MAIN CONTAINER --- */
        .main-container {
            max-width: var(--container-width);
            margin: 42px auto;
            padding: 0 24px;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - var(--header-height) - 60px);
        }
        .content-area {
            background: rgba(255,255,255,0.09);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            overflow: hidden;
            flex: 1;
            border: 1px solid rgba(255,255,255,0.15);
            position: relative;
            z-index: 1;
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.7s cubic-bezier(.16,1,.3,1), transform 0.7s cubic-bezier(.16,1,.3,1);
        }
        .content-area.initialized {
            opacity: 1;
            transform: translateY(0);
        }
        .content-area::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-light), var(--accent-light), var(--accent));
            background-size: 200% 100%;
            opacity: 0.9;
            z-index: 2;
            animation: shimmer 14s infinite linear;
        }
        @keyframes shimmer {
            0% { background-position: 0% 0; }
            100% { background-position: 200% 0; }
        }
        /* --- DATE SELECTOR --- */
        .date-selector {
            padding: 24px 34px;
            background: linear-gradient(to bottom, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.07) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            position: relative;
            z-index: 3;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }
        .date-form {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            width: 100%;
        }
        .date-selector label {
            font-weight: 600;
            color: #fff;
            font-size: 1.1rem;
            padding-left: 34px;
            margin-right: 14px;
            white-space: nowrap;
            position: relative;
        }
        .date-selector label:before {
            content: '\f073';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            font-size: 20px;
            filter: drop-shadow(0 2px 3px rgba(0,0,0,0.2));
        }
        .date-wrapper {
            flex: 1;
            max-width: 270px;
            position: relative;
        }
        .date-selector input[type="date"] {
            width: 100%;
            padding: 14px 18px;
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.22);
            background: rgba(0,0,0,0.15);
            color: #fff;
            font-size: 1.05rem;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            appearance: none;
        }
        .date-selector input[type="date"]:hover {
            border-color: var(--accent-light);
            background: rgba(0,0,0,0.18);
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.15);
        }
        .date-selector input[type="date"]:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(91,184,95,0.2);
            background: rgba(0,0,0,0.20);
            transform: translateY(-2px);
        }
        .date-selector input[type="date"].date-highlight {
            border-color: var(--accent);
            background: rgba(91,184,95,0.15);
            animation: pulseBorder 2s infinite;
        }
        @keyframes pulseBorder {
            0% { border-color: rgba(91,184,95,0.5); }
            50% { border-color: rgba(91,184,95,1); box-shadow: 0 0 12px rgba(91,184,95,0.3);}
            100% { border-color: rgba(91,184,95,0.5);}
        }
        .date-helper {
            font-size: 0.95rem;
            margin-left: auto;
            color: var(--accent-light);
            background: rgba(0,0,0,0.15);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
        }        /* --- MEALS CONTAINER --- */
        .meals-container {
            padding: 42px;
            display: grid;
            gap: 42px;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }
        .meal-card {
            background: rgba(255,255,255,0.12);
            border-radius: var(--radius-lg);
            padding: 40px 30px 30px 30px;
            border: 1px solid rgba(255,255,255,0.15);
            transition: var(--transition-bounce);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 24px;
            isolation: isolate;
        }
        .meal-card:before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: var(--accent);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 2;
        }
        .meal-card:after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.15), transparent 60%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .meal-card.pranzo:before { background: linear-gradient(to right, var(--warning), var(--accent)); }
        .meal-card.cena:before { background: linear-gradient(to right, var(--primary-light), var(--primary)); }
        .meal-card.colazione:before { background: linear-gradient(to right, var(--success), var(--accent-light)); }
        .meal-card:hover, .meal-card.active {
            background: rgba(255,255,255,0.16);
            box-shadow: var(--shadow-hover);
            border-color: rgba(255,255,255,0.25);
            transform: translateY(-5px) scale(1.02);
        }
        .meal-card:hover:before, .meal-card.active:before { 
            opacity: 1; 
        }
        .meal-card:hover:after, .meal-card.active:after { 
            opacity: 1; 
        }
        .meal-title {
            font-size: 1.22rem;
            font-weight: 700;
            color: #fff;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.02em;
            position: relative;
        }
        .meal-title.pranzo::before {
            content: '\f2e7';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--warning);
            font-size: 1.3rem;
            margin-right: 10px;
            filter: drop-shadow(0 2px 3px rgba(0,0,0,0.2));
        }
        .meal-title.cena::before {
            content: '\f2e7';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--primary-light);
            font-size: 1.3rem;
            margin-right: 10px;
            filter: drop-shadow(0 2px 3px rgba(0,0,0,0.2));
        }
        .meal-title.colazione::before {
            content: '\f0f4';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--success);
            font-size: 1.3rem;
            margin-right: 10px;
            filter: drop-shadow(0 2px 3px rgba(0,0,0,0.2));
        }
        .status-indicator {
            position: absolute;
            top: 20px; right: 20px;
            width: 14px; height: 14px;
            border-radius: 50%;
            background: var(--text-muted);
            transition: var(--transition);
            border: 2px solid rgba(255,255,255,0.2);
        }
        .meal-card .status-indicator.booked {
            background: var(--accent);
            box-shadow: 0 0 12px var(--accent);
            border-color: rgba(255,255,255,0.4);
            animation: statusPulse 2s infinite;
        }
        @keyframes statusPulse {
            0% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(76, 175, 80, 0); }
            100% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0); }
        }        /* --- FORM --- */
        .meal-form {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr 1fr;
            margin-top: 20px;
            position: relative;
            padding-top: 16px;
        }
        .meal-form::before {
            content: 'Dettagli prenotazione';
            position: absolute;
            top: -12px; left: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--accent-light);
            background: rgba(0,0,0,0.2);
            padding: 4px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            letter-spacing: 0.02em;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group label {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--accent-light);
            padding-left: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .form-group label::before {
            content: '•';
            font-size: 1.5em;
            line-height: 0;
            color: var(--accent);
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(0,0,0,0.18);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: var(--radius);
            transition: var(--transition);
            font-size: 1.05rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .form-control:hover {
            border-color: var(--accent-light);
            background: rgba(0,0,0,0.22);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.12);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(91,184,95,0.18), 0 6px 12px rgba(0,0,0,0.1);
            background: rgba(0,0,0,0.25);
            transform: translateY(-2px);
        }
        .form-control.field-changed {
            animation: highlightField 1.2s cubic-bezier(.19,1,.22,1);
            border-color: var(--accent-light);
        }
        @keyframes highlightField {
            0% { background: rgba(91,184,95,0.25);}
            100% { background: rgba(0,0,0,0.18);}
        }
        select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.8)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }        /* --- BUTTONS --- */
        .btn, .submit-button, .cancel-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            font-weight: 600;
            border-radius: var(--radius);
            transition: var(--transition-bounce);
            border: none;
            cursor: pointer;
            font-size: 1.05rem;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.02em;
            z-index: 1;
        }
        .btn::after, .submit-button::after, .cancel-button::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255,255,255,0.1);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.5s ease;
            z-index: -1;
        }
        .btn:hover::after, .submit-button:hover::after, .cancel-button:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        .submit-button {
            grid-column: span 2;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: #fff;
            margin-top: 20px;
            min-height: 54px;
            text-transform: uppercase;
            font-size: 1rem;
        }
        .submit-button:hover {
            background: linear-gradient(135deg, var(--accent-light) 10%, var(--accent) 90%);
            box-shadow: 0 8px 20px rgba(65,150,69,0.25);
            transform: translateY(-3px);
        }
        .submit-button:active {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(65,150,69,0.2);
        }
        .submit-button i {
            font-size: 1.1em;
            margin-right: 2px;
        }
        .cancel-button {
            background: linear-gradient(135deg, var(--danger), #c62828);
            color: #fff;
            margin-top: 16px;
            width: 100%;
            text-transform: uppercase;
            font-size: 1rem;
        }
        .cancel-button:hover {
            background: linear-gradient(135deg, #c62828 10%, var(--danger) 90%);
            box-shadow: 0 8px 20px rgba(230,57,70,0.25);
            transform: translateY(-3px);
        }
        .cancel-button:active {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(230,57,70,0.2);
        }
        /* --- RESERVATION STATUS --- */
        .reservation-status {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .reservation-card {
            display: flex;
            background: rgba(255,255,255,0.10);
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.13);
            transition: var(--transition);
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .reservation-card.booked {
            background: rgba(91,184,95,0.13);
            border-color: rgba(91,184,95,0.18);
        }
        .reservation-card.consumed {
            background: rgba(42,157,143,0.13);
            border-color: rgba(42,157,143,0.18);
        }
        .card-icon-wrapper {
            background: rgba(255,255,255,0.13);
            padding: 28px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 68px;
            border-right: 1px solid rgba(255,255,255,0.07);
        }
        .card-icon {
            font-size: 1.7rem;
            color: var(--accent);
            filter: drop-shadow(0 2px 3px rgba(0,0,0,0.13));
        }
        .reservation-card.consumed .card-icon { color: var(--success);}
        .meal-card.pranzo .card-icon { color: var(--warning);}
        .meal-card.cena .card-icon { color: var(--primary-light);}
        .meal-card.colazione .card-icon { color: var(--success);}
        .card-content {
            flex: 1;
            padding: 18px 22px;
        }
        .card-title {
            font-size: 1.08rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .reservation-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: rgba(0,0,0,0.10);
            padding: 8px 12px;
            border-radius: 10px;
            margin-top: 8px;
            border: 1px solid rgba(255,255,255,0.07);
        }
        .detail-row {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--accent-light);
            font-size: 0.98rem;
        }
        .reservation-timestamp {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.93rem;
            color: var(--accent-light);
            margin-top: 4px;
        }
        .card-status-badge {
            position: absolute;
            top: 12px; right: 12px;
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            z-index: 2;
            border: 2px solid rgba(255,255,255,0.18);
            background: var(--success);
            color: #fff;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(42,157,143,0.18);}
            70% { box-shadow: 0 0 0 6px rgba(42,157,143,0);}
            100% { box-shadow: 0 0 0 0 rgba(42,157,143,0);}
        }
        /* --- MESSAGES --- */
        .reservation-status p {
            background: rgba(249,168,37,0.13);
            border: 1px solid rgba(249,168,37,0.18);
            border-radius: 10px;
            padding: 12px 18px;
            color: var(--warning);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }
        .reservation-status p::before {
            content: '\f017';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 1.2rem;
            color: var(--warning);
            margin-right: 8px;
        }
        .reservation-status p.error-message {
            background: rgba(230,57,70,0.13);
            border: 1px solid rgba(230,57,70,0.18);
            color: var(--danger);
        }
        .reservation-status p.error-message::before {
            content: '\f071';
            color: var(--danger);
        }
        /* --- ACCESSIBILITY & RESPONSIVE --- */
        a:focus, button:focus, input:focus, select:focus {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }
        .skip-link {
            position: absolute;
            top: -40px; left: 0;
            background: var(--accent);
            color: #fff;
            padding: 8px;
            z-index: 9999;
            transition: top 0.3s;
        }
        .skip-link:focus { top: 0; }
        @media (max-width: 992px) {
            .meals-container { grid-template-columns: 1fr; padding: 18px; gap: 18px;}
            .main-container { padding: 0 8px; margin: 18px auto;}
        }
        @media (max-width: 768px) {
            .date-form { flex-direction: column; align-items: flex-start;}
            .date-wrapper { width: 100%; max-width: 100%;}
            .navbar-title { font-size: 1.05rem;}
            .user-name { max-width: 90px;}
            .meal-form { grid-template-columns: 1fr;}
            .payment-field { grid-column: 1;}
            .date-selector { padding: 14px;}
            header { padding: 0 8px;}
        }
        @media (max-width: 576px) {
            .navbar-brand { display: none;}
            .user-name { display: none;}
            .meal-card { padding: 14px;}
            .reservation-card { flex-direction: column;}
            .card-icon-wrapper { width: 100%; padding: 14px;}
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>
    <!-- Header con navbar -->
    <header role="banner">
        <div class="navbar">
            <div class="navbar-brand">
                <img src="./images/BannerCealpi.jpg" alt="Banner Cealpi" width="100%" height="100%">
            </div>
            <h1 class="navbar-title"><i class="fas fa-utensils navbar-title-icon" aria-hidden="true"></i>Sistema Prenotazione Pasti</h1>
            <div class="navbar-actions" role="navigation"><!-- START User Dropdown Menu -->
            <?php
            // Prepare user display variables to avoid repetition
            $user_grado = $row_User['Grado'];
            $user_cognome_upper = strtoupper($row_User['Cognome']);
            $user_nome_ucwords = ucwords(strtolower($row_User['Nome']));
            $user_display_name = $user_grado . ' <span class="user-surname">' . $user_cognome_upper . '</span> ' . $user_nome_ucwords;
            $user_foto_src = !empty($row_User['Foto']) ? $row_User['Foto'] : null;
            $user_role = $row_User['DEN_UN_OPER'];
            ?>
            <div class="user-dropdown-simple">
                <!-- Toggle Button -->
                <div id="userMenuToggle" class="dropdown-toggle-simple" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="default-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?php echo $user_display_name; ?></div>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <!-- Dropdown Menu -->
                <div id="userDropdownMenu" class="dropdown-menu-simple" role="menu">
                    <div class="dropdown-header">
                        <div class="user-info-extended">
                            <div class="user-photo-container">
                                <?php if ($user_foto_src) { ?>
                                    <img src="<?php echo $user_foto_src; ?>" alt="Foto utente" class="user-avatar">
                                <?php } else { ?>
                                    <div class="default-user-photo">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="user-details">
                                <div class="user-name"><?php echo $user_display_name; ?></div>
                                <div class="user-role"><?php echo $user_role; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-body">
                        <a href="/ACCESSI/Files/menu_maggio.pdf#zoom=140" target="_blank" class="dropdown-item">
                            <i class="fas fa-clipboard-list"></i>
                            Menu Maggio
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-history"></i>
                            Storico prenotazioni
                        </a>
                         <div class="dropdown-divider"></div>
                         <a href="<?php echo $logoutAction; ?>" class="dropdown-item">
                             <i class="fas fa-sign-out-alt"></i> Logout
                         </a>
                    </div>                </div>
            </div>
            <!-- END User Dropdown Menu -->
        </div>
    </header>
    
    <div class="main-container">
        <div id="main-content" class="content-area" role="main">
            <div class="content-area-overlay"></div>
            <div class="date-selector" role="region" aria-label="Selettore data prenotazione">
                <form name="form8" method="post" class="date-form">
                    <label for="Giorno">Seleziona data:</label>
                    <div class="date-wrapper">
                        <input type="date" id="Giorno" name="Giorno" onchange="this.form.submit()" 
                            value="<?php echo isset($_POST['Giorno']) ? $_POST['Giorno'] : date('Y-m-d'); ?>"
                            title="Seleziona la data per la prenotazione pasti" 
                            aria-label="Data prenotazione pasti"
                            min="<?php echo date('Y-m-d'); ?>" 
                            max="<?php echo date('Y-m-d', strtotime('+' . MAX_BOOKING_DAYS . ' days')); ?>"
                            class="date-input <?php echo (isset($_POST['Giorno']) && $_POST['Giorno'] == date('Y-m-d')) ? 'date-highlight' : ''; ?>">                    </div>
                </form>
                <div class="date-helper">
                    <small>Puoi prenotare fino a <?php echo MAX_BOOKING_DAYS; ?> giorni in anticipo</small>
                </div>
            </div>
            <div class="meals-container" role="region" aria-label="Prenotazione pasti">
                <!-- Card Pranzo -->
                <div class="meal-card pranzo">
                    <div class="meal-title pranzo">PRANZO DEL <?php echo date("d-m-Y", strtotime($_SESSION['GioPre'])); ?></div>
                    <span class="status-indicator <?php echo isset($row_Prenotazione_Pr['IDnome']) ? 'booked' : ''; ?>"></span>                    <?php 
                    // Verifica se esiste una prenotazione per il pranzo
                    if (!isset($row_Prenotazione_Pr['IDnome'])) { 
                        if (isPastoPrenotabile($_SESSION['GioPre'], $orari_limite[1])) {
                            echo $generaFormPrenotazione('form1', 1, date("Y-m-d", strtotime($_SESSION['GioPre'])), 
                                $row_User, $row_User['TipoRazione']);
                        } else { ?>
                            <div class="reservation-status">
                                <p>Non è possibile effettuare la prenotazione dopo le ore <?php echo $orari_limite[1]; ?></p>
                            </div>
                        <?php 
                        }
                    } else { 
                        echo $generaStatoPrenotazione($row_Prenotazione_Pr, 'form5', $orari_limite[1], $_SESSION['GioPre']);
                    } 
                    ?>
                </div>
                
                <!-- Card Cena -->
                <div class="meal-card cena">
                    <div class="meal-title cena">CENA DEL <?php echo date("d-m-Y", strtotime($_SESSION['GioPre'])); ?></div>
                    <span class="status-indicator <?php echo isset($row_Prenotazione_Ce['IDnome']) ? 'booked' : ''; ?>"></span>                    <?php 
                    // Verifica se esiste una prenotazione per la cena
                    if (!isset($row_Prenotazione_Ce['IDnome'])) {                        
                        if (isPastoPrenotabile($_SESSION['GioPre'], $orari_limite[2])) {
                            echo $generaFormPrenotazione('form2', 2, date("Y-m-d", strtotime($_SESSION['GioPre'])), 
                                $row_User, $row_User['TipoRazioneCe']);
                        } else { ?>
                            <div class="reservation-status">
                                <p>Non è possibile effettuare la prenotazione dopo le ore <?php echo $orari_limite[2]; ?></p>
                            </div>
                        <?php 
                        }
                    } else { 
                        echo $generaStatoPrenotazione($row_Prenotazione_Ce, 'form6', $orari_limite[2], $_SESSION['GioPre']);
                    } 
                    ?>
                </div>
                
                <!-- Card Colazione -->
                <div class="meal-card colazione">
                    <div class="meal-title colazione">COLAZIONE DEL <?php echo date("d-m-Y", strtotime($_SESSION['GioPre'] . ' +1 day')); ?></div>
                    <span class="status-indicator <?php echo isset($row_Prenotazione_Col['IDnome']) ? 'booked' : ''; ?>"></span>
                    <?php
                    // Verifica se esiste una prenotazione per la colazione
                    if (!isset($row_Prenotazione_Col['IDnome'])) {
                        echo $generaFormPrenotazione('form3', 3, $dataFormattataGiornoSuccessivo, 
                            $row_User, $row_User['TipoRazioneCol']);
                    } else { 
                        echo $generaStatoPrenotazione($row_Prenotazione_Col, 'form7', $orari_limite[3], $_SESSION['GioPre']);
                    } ?>
                </div>            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        'use strict';
        // Effetti iniziali
        const contentArea = document.querySelector('.content-area');
        if (contentArea) requestAnimationFrame(() => contentArea.classList.add('initialized'));
        // Cache DOM
        const domCache = {};
        const getElement = id => domCache[id] || (domCache[id] = document.getElementById(id));
        // Date selector
        const dateInput = getElement('Giorno');
        if (dateInput) {
            const today = new Date(); today.setHours(0,0,0,0);
            const selectedDate = new Date(dateInput.value); selectedDate.setHours(0,0,0,0);
            if (selectedDate.getTime() === today.getTime()) dateInput.classList.add('date-highlight');
            dateInput.addEventListener('change', () => {
                const form = dateInput.closest('form');
                if (form && !form.querySelector('.loading-indicator')) {
                    const loadingMsg = document.createElement('div');
                    loadingMsg.className = 'loading-indicator';
                    loadingMsg.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Aggiornamento prenotazioni...';
                    loadingMsg.style.cssText = 'position:absolute;right:10px;bottom:10px;background:rgba(0,0,0,0.7);padding:8px 16px;border-radius:20px;font-size:0.9rem;color:white;z-index:1000;box-shadow:0 4px 12px rgba(0,0,0,0.2);';
                    form.appendChild(loadingMsg);
                }
            });
        }
        // User menu dropdown
        const toggleButton = getElement('userMenuToggle');
        const dropdownMenu = getElement('userDropdownMenu');
        if (toggleButton && dropdownMenu) {
            let isOpen = false, menuItems = dropdownMenu.querySelectorAll('a, button'), focusIndex = -1;
            const toggleDropdown = show => {
                if (isOpen === show) return;
                isOpen = show;
                dropdownMenu.classList.toggle('show', show);
                toggleButton.setAttribute('aria-expanded', show ? 'true' : 'false');
                toggleButton.classList.toggle('active', show);
                if (show && menuItems.length > 0) {
                    focusIndex = 0;
                    setTimeout(() => menuItems[0].focus(), 100);
                }
            };
            toggleButton.addEventListener('click', e => { e.stopPropagation(); toggleDropdown(!isOpen); });
            toggleButton.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleDropdown(!isOpen); }
                else if (e.key === 'ArrowDown' && !isOpen) { e.preventDefault(); toggleDropdown(true); }
            });
            dropdownMenu.addEventListener('keydown', e => {
                if (!isOpen || !menuItems.length) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); focusIndex = (focusIndex + 1) % menuItems.length; menuItems[focusIndex].focus(); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); focusIndex = (focusIndex - 1 + menuItems.length) % menuItems.length; menuItems[focusIndex].focus(); }
                else if (e.key === 'Escape') { e.preventDefault(); toggleDropdown(false); toggleButton.focus(); }
            });
            document.addEventListener('click', e => {
                if (isOpen && !dropdownMenu.contains(e.target) && !toggleButton.contains(e.target)) toggleDropdown(false);
            }, { passive: true });
        }
        // Card hover effects
        const mealsContainer = document.querySelector('.meals-container');
        if (mealsContainer && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            mealsContainer.addEventListener('mouseover', e => {
                const mealCard = e.target.closest('.meal-card');
                const reservationCard = e.target.closest('.reservation-card');
                if (mealCard) mealCard.classList.add('active');
                if (reservationCard) {
                    reservationCard.style.transform = 'translateY(-5px)';
                    reservationCard.style.boxShadow = '0 15px 30px rgba(0,0,0,0.2)';
                }
            }, { passive: true });
            mealsContainer.addEventListener('mouseout', e => {
                const mealCard = e.target.closest('.meal-card');
                const reservationCard = e.target.closest('.reservation-card');
                if (mealCard) mealCard.classList.remove('active');
                if (reservationCard) {
                    reservationCard.style.transform = '';
                    reservationCard.style.boxShadow = '';
                }
            }, { passive: true });
        }
        // Lazy loading immagini
        if ('loading' in HTMLImageElement.prototype) {
            document.querySelectorAll('img[data-src]').forEach(img => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            });
        }
        // Gestione form
        document.addEventListener('change', e => {
            if (e.target.matches('.meal-form select')) {
                e.target.classList.add('field-changed');
                setTimeout(() => e.target.classList.remove('field-changed'), 1000);
            }
        });
        document.querySelectorAll('.meal-form .submit-button').forEach(btn => {
            if (!btn) return;
            btn.addEventListener('click', () => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Prenotazione in corso...';
                btn.disabled = true;
                btn.style.opacity = '0.8';
                setTimeout(() => {
                    if (btn.disabled) {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    }
                }, 8000);
            });
        });
        document.addEventListener('click', e => {
            if (e.target.matches('.cancel-form .cancel-button')) {
                const cancelButton = e.target;
                if (!confirm('Sei sicuro di voler annullare questa prenotazione?')) {
                    e.preventDefault();
                    return false;
                }
                const originalText = cancelButton.innerHTML;
                cancelButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Cancellazione in corso...';
                cancelButton.disabled = true;
                cancelButton.style.opacity = '0.8';
                setTimeout(() => {
                    if (cancelButton.disabled) {
                        cancelButton.innerHTML = originalText;
                        cancelButton.disabled = false;
                        cancelButton.style.opacity = '1';
                    }
                }, 8000);
            }
        });
    });
    </script>
</body>
</html>
<?php
if (isset($PRES_conn) && $PRES_conn instanceof mysqli) $PRES_conn->close();
?>