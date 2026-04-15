<?php
session_start();
require_once('Connections/MyPresenze.php'); ?>
<?php
//Controllo che il parametro Tx_cert non sia nullo
if (isset($_POST['Tx_cert'])&& !isset($_SESSION['CFCMD'])) {
	$parCF_Login = "%";
	$parCF_Login = $_POST['Tx_cert'];
	$query_Login = "SELECT pre_elenconomi.IDnome, pre_elenconomi.CF, pre_elenconomi.VTV, pre_elenconomi.Cognome FROM pre_elenconomi WHERE pre_elenconomi.CF='$parCF_Login' AND pre_elenconomi.VTV= 2";
	$Login = $PRES_conn->query($query_Login);
	$row_Login = mysqli_fetch_assoc($Login);
	$totalRows_Login = $Login->num_rows;
	$_SESSION['autorized'] = true;
	$_SESSION['VTVAUT'] = true;
	$_SESSION['UserID'] = $row_Login['IDnome'];
	$_SESSION['Nome']=$row_Login['Conome'];
	header("Location: VTV.php");
} else if (isset($_SESSION['MM_UserGroup'])) {
		$parCF_Login = "%";
		$parCF_Login = $_SESSION['MM_UserGroup'];
		$query_Login = "SELECT pre_elenconomi.IDnome, pre_elenconomi.CF, pre_elenconomi.VTV, pre_elenconomi.Cognome FROM pre_elenconomi WHERE pre_elenconomi.CF='$parCF_Login' AND pre_elenconomi.VTV=2";
		$Login = $PRES_conn->query($query_Login);
		$row_Login = mysqli_fetch_assoc($Login);
		$totalRows_Login = $Login->num_rows;
		$_SESSION['autorized'] = true;
		$_SESSION['VTVAUT'] = true;
		$_SESSION['UserID'] = $row_Login['IDnome'];
		$_SESSION['Nome']=$row_Login['Conome'];
		header("Location: VTV.php");
} else if (isset($_SESSION['CFCMD'])){
	$parCF_Login = $_SESSION['CFCMD'];
	$query_Login = "SELECT pre_elenconomi.IDnome, pre_elenconomi.CF, pre_elenconomi.VTV, pre_elenconomi.Cognome FROM pre_elenconomi WHERE MD5(pre_elenconomi.CF)='$parCF_Login' AND pre_elenconomi.VTV= 2";
	$Login = $PRES_conn->query($query_Login);
	$row_Login = mysqli_fetch_assoc($Login);
	$totalRows_Login = $Login->num_rows;
	$_SESSION['autorized'] = true;
	$_SESSION['VTVAUT'] = true;
	$_SESSION['UserID'] = $row_Login['IDnome'];
	$_SESSION['Nome']=$row_Login['Conome'];
	header("Location: VTV.php");
} else {
		unset ($_SESSION['autorized']);
		unset ($_SESSION['UserID']);
		header("Location: LoginVTV.php");
}
?>

