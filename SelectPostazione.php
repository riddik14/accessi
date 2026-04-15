<?php require_once('Connections/MyPresenze.php'); 
//initialize the session
session_start();

$query_Sedi = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sedi = $PRES_conn->query($query_Sedi);
$row_Sedi = mysqli_fetch_assoc($Sedi);
$totalRows_Sedi = $Sedi->num_rows;

// *** Validate request to login to this site.

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($accesscheck)) {
  $GLOBALS['PrevUrl'] = $accesscheck;
  $_SESSION['PrevUrl'] = $accesscheck;
}

if (isset($_POST['Sede'])) {
  $Sede=$_POST['Sede'];
  $Pasto=$_POST['Pasto'];
  $MM_redirectLoginSuccess = "PostMensaFull.php";
      
    //declare two session variables and assign them
    $_SESSION['Sede'] = $Sede;
    $_SESSION['Pasto'] = $Pasto;	      

    //register the session variables
    // Session variables already set above
    header("Location: " . $MM_redirectLoginSuccess );
  }
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Posto distribuzione</title>
<style type="text/css">
/* Stili aggiornati da LoginMassiva.php */
:root {
  --primary-color: #07406b;
  --secondary-color: #69a6b7;
}

body {
  background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
  margin: 0;
  min-height: 100vh;
  font-family: 'Arial', sans-serif;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.login-title {
    color: white;
  font-size: 25px;
  text-align: center;
  font-weight: normal;
  margin-top: -1.5rem;
  text-transform: uppercase;
  width: 100%;
}

.login-form {
    background: rgba(255, 255, 255, 0.9);
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 320px;
  /* Aggiunto per centrare il form */
  margin: 0 auto;
}

.login-container {
 display: flex;
  flex-direction: column;
  gap: 1rem;
  /* Aggiunto per centrare il contenuto */
  align-items: center;
  width: 100%;
}

.input-group {
    width: 100%;
  margin-bottom: 5px;
  display: flex;
  justify-content: center;
}

select {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 16px;
  background-color: white;
}

select:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 2px rgba(7, 64, 107, 0.2);
}

.login-btn {
  width: 100%;
  padding: 0.8rem;
  background-color: var(--primary-color);
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.login-btn:hover {
  background-color: var(--primary-color);
}

.button-container {
  width: 100%;
  padding: 1rem;
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

.main-container {
  width: 90%;
  max-width: 450px;
  margin-top: 12em;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 8px 32px var(--shadow-color);
  /* Aggiunto per centrare il contenuto interno */
  display: flex;
  flex-direction: column;
  align-items: center;
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
  color: #ffffff;
  letter-spacing: 3px;
  text-shadow: 2px 4px 6px var(--shadow-color);
  font-family: 'Arial Black', 'Arial Bold', Gadget, sans-serif;
  animation: slideDown 1s ease-out;
}
</style>
</head>
<body onLoad="form1.Sede.focus()">
<div class="header-title">
    <span class="header-title-text">Centro Ospedaliero Militare di Milano</span>
</div>
<div class="button-container">
    <button class="back-button" onclick="location.href='LoginPrenota.php'">
        <span class="fa-arrow-left"></span> Indietro
    </button>
</div>
<div class="main-container">
<h1 class="login-title">Accesso alla postazione</h1>
<form name="form1" method="get" action="PostMensa.php" class="login-form">
    <div class="login-container">
        <div class="input-group">
            <select name="Sede" id="Sede">
                <option value="-" selected>-</option>
                <?php do { ?>
                <option value="<?php echo $row_Sedi['IDsede']?>"><?php echo $row_Sedi['SEDE']?></option>
                <?php } while ($row_Sedi = mysqli_fetch_assoc($Sedi)); ?>
            </select>
        </div>
        <button type="submit" name="Submit" class="login-btn">Invia</button>
    </div>
</form>
</div>
</body>
</html>
<?php
mysqli_free_result($Sedi);
?>
