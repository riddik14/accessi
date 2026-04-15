<?php require_once('../Connections/MyPresenze.php'); 
	session_start();
//Controllo che il parametro Tx_cert non sia nullo
if (isset($_POST['Tx_cert'])) {
	$parCF_Login = "%";
	if (isset($_POST['Tx_cert'])) {
	  $parCF_Login = $_POST['Tx_cert'];
	}
	
$query_Login = "SELECT pre_elenconomi.IDnome, pre_elenconomi.CF , pre_elenconomi.ADMIN FROM pre_elenconomi WHERE pre_elenconomi.CF='$parCF_Login'";
$Login = $PRES_conn->query($query_Login);
$row_Login = mysqli_fetch_assoc($Login);
$totalRows_Login = $Login->num_rows;
	if ($row_Login['ADMIN']==1) {
	
		$_SESSION['autorized'] = true;
		$_SESSION['UserID'] = $row_Login['IDnome'];
		//echo $row_Login['IDnome'];
		header("Location:Master.php");
			} else {
				header("Location:LoginADMIN.php");
			}
} else {
	unset ($_SESSION['autorized']);
	unset ($_SESSION['UserID']);
	header("Location: LoginADMIN.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento senza titolo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<p>&nbsp;
</p>
</body>
</html>
<?php
mysqli_free_result($Login);
?>
