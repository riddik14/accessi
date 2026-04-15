<?php require_once('../Connections/MyPresenze.php'); 
// *** Validate request to login to this site.
session_start();

$loginFormAction = $_SERVER['PHP_SELF'];

if (isset($accesscheck)) {
  $GLOBALS['PrevUrl'] = $accesscheck;
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "CF";
  $MM_redirectLoginSuccess = "Master.php";
  $MM_redirectLoginFailed = "LoginADMIN.php";
  $MM_redirecttoReferrer = false;
  	
      $stmt = $PRES_conn->prepare("SELECT Username, Password, CF, Nome, Cognome, IDnome FROM pre_elenconomi WHERE Username=? AND Password=? AND ADMIN=1");
      $stmt->bind_param("ss", $loginUsername, $password);
      $stmt->execute();
      $Login = $stmt->get_result();
        $row_Login = mysqli_fetch_assoc($Login);
        $loginFoundUser = $Login->num_rows;
      

  if ($loginFoundUser > 0) {
    $loginStrGroup  = $row_Login['CF'];
	  $_SESSION['autorized'] = true;
  	$_SESSION['UserID'] = $row_Login['IDnome'];
  	$_SESSION['UsernameExt'] =  $row_Login['Nome'] . " " . $row_Login['Cognome'];
  
    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }

    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Admin</title>
<script>
function jnlp(str) {
			window.location.href='LoginCMD.jnlp?key=20';
}

var browsers = ["Opera", "Edg", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];
var userbrowser, useragent = navigator.userAgent;
for (var i = 0; i < browsers.length; i++) {
    if( useragent.indexOf(browsers[i]) > -1 ) {
        userbrowser = browsers[i];
        break;
    }
};
  
switch(userbrowser) {
    case 'MSIE':
        userbrowser = 'Internet Explorer';
        break;
  
    case 'Trident':
        userbrowser = 'Internet Explorer';
        break;
  
    case 'Edg':
        userbrowser = 'Microsoft Edge';
        break;
}
 
if(userbrowser == 'Internet Explorer'){
	alert('ATTENZIONE!! Per un corretto funzionamento del sistema utilizzare il browser Chrome o Firefox. Internet Explorer non garantisce la corretta visualizzazione delle pagine ed il funzionamento delle procedure');
}
</script>

<style type="text/css">
:root {
  --primary-color: #07406b;
  --secondary-color: #69a6b7;
  --text-light: #ffffff;
  --shadow-color: rgba(0, 0, 0, 0.2);
  --glass-bg: rgba(255, 255, 255, 0.1);
  --gradient-primary: linear-gradient(to bottom, #588ca4, #11384e);
}

body {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 5vh;
  margin: 0;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: var(--gradient-primary);
  background-repeat: no-repeat;
  background-attachment: fixed;
}

.header-title {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  padding: 2rem;
  text-align: center;
  background: rgba(32, 77, 98, 0.3);
  box-shadow: 0 4px 6px var(--shadow-color);
  z-index: 1000;
}

.header-title-text {
  font-size: 3rem;
  font-weight: 900;
  color: var(--text-light);
  letter-spacing: 3px;
  text-shadow: 2px 4px 6px var(--shadow-color);
  font-family: 'Arial Black', 'Arial Bold', Gadget, sans-serif;
  animation: slideDown 1s ease-out;
}

.main-container {
  width: 90%;
  max-width: 450px;
  margin-top: 10em;
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 8px 32px var(--shadow-color);
  padding: 2.5rem;
}

.login-title {
  color: #ffffff;
  font-size: 30px;
  text-align: center;
  text-transform: uppercase;
  margin-bottom: 35px;
}


.login-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.input-group {
  width: 100%;
  margin-bottom: 5px;
  display: flex;
  justify-content: center;
}

input[type="text"],
input[type="password"] {
  width: 80%; /* Ridotto dal calc(80% - 10px) */
  height: 45px;
  padding: 15px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 8px;
  background: var(--glass-bg);
  font-size: 15px;
  transition: all 0.3s ease;
  box-sizing: border-box;
  letter-spacing: 0.5px;
  color: white; /* Aggiunto per rendere il testo bianco */
}

input::placeholder {
  color: rgb(255, 255, 255);
}


.login-btn {
  width: 80%; /* Allineato con la larghezza degli input */
  margin: 0 auto; /* Centra orizzontalmente */
  padding: 12px;
  background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
  color: var(--text-light);
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: block; /* Necessario per margin: 0 auto */
  margin-top: 20px;
}

.login-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px var(--shadow-color);
}

.back-button {
  position: fixed;
  top: 150px;
  left: 20px;
  background: linear-gradient(to bottom, var(--primary-color), #052c4a);
  border: none;
  padding: 12px 22px;
  color: #ffffff;
  border-radius: 0.7em;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-button:hover {
  background: linear-gradient(to bottom, #052c4a, var(--primary-color));
  transform: translateY(-2px);
}
.fa-arrow-left::before {
            content: "←";
            margin-right: 8px;
        }

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
</head>
<?php //effetuo il controllo sull'indirizzo IP per la visualizzazione dello sfondo della Home
$ip = $_SERVER["REMOTE_ADDR"];
if (substr($ip, 0 , 9) <> "10.31.191") {
?>
<body onLoad="form1.Submit.focus()">
<?php } else { ?>
<body onLoad="form1.Submit.focus()">
<?php }?>
<div class="header-title">
    <span class="header-title-text">Centro Ospedaliero Militare di Milano</span>
</div>
<div class="button-container">
        <button class="back-button" onclick="location.href='/ACCESSI/LoginPrenota.php'">
            <span class="fa-arrow-left"></span> Indietro
        </button>
    </div>
<div class="main-container">
<h1 class="login-title">Login ADM</h1>
<form name="form1" method="POST" action="<?php echo $loginFormAction; ?>" class="login-form">
  <div class="login-container">   
    <div class="input-group">
      <input placeholder="Username" name="username" type="text" id="username" required>
    </div>
    <div class="input-group">
      <input placeholder="Password" name="password" type="password" id="password" required>
    </div>
    <button type="submit" name="Submit" class="login-btn">Accedi</button>
    <input name="Tx_cert" type="hidden" id="Tx_cert">
  </div>
  </div>
</form>
</body>
</html>

