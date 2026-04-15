<?php require_once('../Connections/MyPresenze.php'); 

$query_Setup = "SELECT pre_setup.ID, pre_setup.Reparto, pre_setup.Ditta_Rist, pre_setup.LoginMode FROM pre_setup";
$Setup = $PRES_conn->query($query_Setup);
$row_Setup = mysqli_fetch_assoc($Setup);
$totalRows_Setup = $Setup->num_rows;

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
// ************************** Memorizza i dati nella tabella Setup  **********************************
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE pre_setup SET Reparto=%s, Ditta_Rist=%s WHERE ID=%s",
                       GetSQLValueString($_POST['Reparto'], "text"),
                       GetSQLValueString($_POST['Ditta'], "text"),
                       GetSQLValueString($_POST['ID_REC'], "int"));

  $Result1 = $PRES_conn->query($updateSQL);

  $updateGoTo = "Setup.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE pre_setup SET LoginMode=%s WHERE ID=%s",
                       GetSQLValueString($_POST['select'], "int"),
                       GetSQLValueString($_POST['IDrecord'], "int"));

  $Result1 = $PRES_conn->query($updateSQL);
}
if ((isset($_POST['Setup'])) && ($_POST['Setup'] == "1")) {
// Modifica il file tnsnames.ora
	$IP = $_POST['IP_SIGE'];
		$nomefile = "C:\\oracle\\ora92\\network\\admin\\tnsnames.ora";
		$p_file = fopen($nomefile,"r");
		$line = file($nomefile);
		fclose($p_file);
			
		$p_file = fopen($nomefile,"w+");
		$j = 0;
		$x = count($line);
	    while($j < $x){	
						if (strstr($line[$j],'(ADDRESS = (PROTOCOL = TCP)')){
								$line[$j] = "        (ADDRESS = (PROTOCOL = TCP)(HOST = " . $IP .")(PORT = 1521))"."\r\n";
						}
					fwrite($p_file, $line[$j]);
					$j= $j+1;
		}	

	fclose($p_file);
		
	
// Modifica il file SIGE.asp e memorizza la password di SIETABELLE
$PW = $_POST['PW'];
		$nomefile = "E:\\www\\cealpi_php\\Connections\\SIGE.asp";
		$p_file = fopen($nomefile,"r");
		$line = file($nomefile);
		fclose($p_file);
			
		$p_file = fopen($nomefile,"w+");
		$j = 0;
		$x = count($line);
	    while($j < $x){	
						if (strstr($line[$j],'MM_SIGE_STRING =')){
								$line[$j] = "MM_SIGE_STRING = \"dsn=SIGE;uid=sietabelle;pwd=" . $PW .";\""."\r\n";
						}
					fwrite($p_file, $line[$j]);
					$j= $j+1;
		}	

	fclose($p_file);

// Modifica il file PHP.ini e memorizza il server smtp e la mail

$SMTP = $_POST['SMTP'];
$mail = $_POST['email'];
		$nomefile = "c:\\php\\php.ini";
		$p_file = fopen($nomefile,"r");
		$line = file($nomefile);
		fclose($p_file);
			
		$p_file = fopen($nomefile,"w+");
		$j = 0;
		$x = count($line);
	    while($j < $x){	
						if (strstr($line[$j],'SMTP =')){
								$line[$j] = "SMTP = " . $SMTP ."\r\n";
						} else {
								if (strstr($line[$j],'sendmail_from =')){		
									$line[$j] = "sendmail_from = " . $mail ."\r\n";
								}
					}
					fwrite($p_file, $line[$j]);
					$j= $j+1;
		}	
		
		fclose($p_file);
header("Location:Setup.php");
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Impostazioni admin</title>
    <link rel="stylesheet" href="\ACCESSI\style\fonta\css\all.css">
    <style>
        :root {
            --primary: #07406b;
            --primary-light: #0a5185;
            --white: #ffffff;
            --text-light: #e0e0e0;
            --warning-bg: #1a4d73;
            --warning-text: #ffd700;
            --border: #2c5f8a;
            --shadow: rgba(0, 0, 0, 0.2);
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: var(--white);
            background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .form-section {
            background-color: var(--primary);
            border-radius: 12px;
            box-shadow: 0 4px 16px var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            width: 100%; 
            box-sizing: border-box; 
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-bottom: 1.5rem;
            align-items: flex-start; 
        }

        .form-group {
            flex: 1 1 400px; 
            max-width: 100%; 
            margin-bottom: 1rem; 
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--text-light);
            font-size: 0.95rem; 
            line-height: 1.4;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            box-sizing: border-box; 
            border: 2px solid var(--border);
            border-radius: 6px;
            background-color: var(--white);
            color: var(--primary);
            transition: all 0.3s ease;
            margin-top: 0.25rem; 
            font-size: 1.1rem; 
            min-height: 48px; 
        }

        select {
            height: 48px; 
            padding: 0 0.85rem; 
        }

        .form-actions {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            gap: 1rem;
        }

        button,
        input[type="submit"] {
            background-color: var(--warning-bg);
            color: var(--text-light);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        button:hover,
        input[type="submit"]:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
        }

        .section-title {
            color: var(--warning-text);
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
    <!-- Mantenere lo script JavaScript esistente invariato -->
    <script type="text/javascript">
		Script controllo validit� fcampi prima dell'inserimento
		function Modulo() {
     	// Variabili associate ai campi del modulo
	    var IPSIGE = document.form3.IP_SIGE.value;
	    var PW = document.form3.PW.value;
	    var email = document.form3.email.value;
		var SMTP = document.form3.SMTP.value;
		//Effettua il controllo sul campo IP
   		 if ((IPSIGE == "") || (IPSIGE == "undefined")) {
       		alert("Inserire indirizzo IP del server Oracle contenente il DB SIGE.");
        	document.form3.IP_SIGE.focus();
            return false;
    	} else if ((PW == "") || (PW == "undefined")) {
        	alert("Inserire la password dell'utente Oracle SIETABELLE.");
        	document.form3.PW.focus();
        	return false;
		} else if ((email == "") || (email == "undefined")) {
        	alert("Inserire l'indirizzo email da utilizzare per il servizio rilevazione presenze.");
        	document.form3.email.focus();
	        return false;
		} else if ((SMTP == "") || (SMTP == "undefined")) {
        	alert("Inserire l'indirizzo del server SMTP da utilizzare per l'invio delle email.");
        	document.form3.SMTP.focus();
	        return false;
		} else {
     	   document.form3.submit();
    	}
	} 
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
</head>
<body>
    <div class="container">
        <div align="center">
  <form name="form1" method="POST" action="<?php echo $editFormAction; ?>" class="form-section">
    <input name="ID_REC" type="hidden" id="ID_REC" value="<?php echo $row_Setup['ID']; ?>">
    <div class="form-row">
        <div class="form-group">
            <label>Reparto</label>
            <input name="Reparto" type="text" id="Reparto" value="<?php echo $row_Setup['Reparto']; ?>">
        </div>
        <div class="form-group">
            <label>Intestazione ditta CATERING</label>
            <input name="Ditta" type="text" id="Ditta" value="<?php echo $row_Setup['Ditta_Rist']; ?>">
        </div>
    </div>
    <div class="form-actions">
        <input type="submit" name="Submit" value="Salva">
    </div>
    <input type="hidden" name="MM_update" value="form1">
  </form>
  <form method="POST" name="form3" id="form3" class="form-section">
    <h3 class="section-title">Inizializzazione del sistema</h3>
    <div class="form-row">
        <div class="form-group">
            <label>Indirizzo IP del server SIGE (es. 10.22.100.1)</label>
            <input name="IP_SIGE" type="text" id="IP_SIGE">
        </div>
        <div class="form-group">
            <label>Password utente Oracle SIETABELLE</label>
            <input name="PW" type="password" id="PW">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Indirizzo email legato al servizio presenze</label>
            <input name="email" type="text" id="email">
        </div>
        <div class="form-group">
            <label>SERVER SMTP EI NET di bacino</label>
            <input name="SMTP" type="text" id="SMTP">
        </div>
    </div>
    <div class="form-actions">
        <input type="submit" name="Salva" value="Salva" onClick="return Modulo();">
        <input name="Setup" type="hidden" id="Setup" value="1">
    </div>
  </form>
  <form name="form2" method="POST" action="<?php echo $editFormAction; ?>" class="form-section">
    <div class="form-row">
        <div class="form-group">
            <label class="section-title">Modalità di autenticazione utente per la rilevazione presenze</label>
            <input name="IDrecord" type="hidden" id="IDrecord" value="<?php echo $row_Setup['ID']; ?>">
            <select name="select">
                <option value="0" <?php if (!(strcmp(0, $row_Setup['LoginMode']))) {echo "SELECTED";} ?>>Solo CMD</option>
                <option value="1" <?php if (!(strcmp(1, $row_Setup['LoginMode']))) {echo "SELECTED";} ?>>CMD e inserimento CF/PIN</option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <input type="hidden" name="MM_update" value="form2">
        <input type="submit" name="Submit" value="Salva">
    </div>
  </form>
</div>
    </div>
</body>
</html>
<?php
mysqli_free_result($Setup);
?>
