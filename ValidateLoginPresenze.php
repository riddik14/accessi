<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
session_start();
$parCF_Login = "%";

//Controllo che il parametro Tx_cert non sia nullo
unset ($_SESSION['UserID']);
	if (isset($_POST['Tx_cert'])) {
		if (strlen($_POST['Tx_cert']) == 16) {
	  		$parCF_Login = $_POST['Tx_cert'];
	 		$_SESSION['autorized'] = true;
	 		mysql_select_db($database_MyPresenze, $MyPresenze);
	  		$query_Login = "SELECT pre_elenconomi.IDnome, pre_elenconomi.CF FROM pre_elenconomi WHERE pre_elenconomi.CF='$parCF_Login'";
	 		$Login = mysql_query($query_Login, $MyPresenze) or die(mysql_error());
	  		$row_Login = mysql_fetch_assoc($Login);
	  		$totalRows_Login = mysql_num_rows($Login);
	 		$_SESSION['UserID'] = $row_Login['IDnome'];
	 		header("Location: Master.php");
		} else {
			$parCF_Login = substr($_POST['Tx_cert'], 0, -4);
			$_SESSION['autorized'] = true;
	 		mysql_select_db($database_MyPresenze, $MyPresenze);
	  		$query_Login = "SELECT pre_elenconomi.IDnome, pre_elenconomi.CF FROM pre_elenconomi WHERE pre_elenconomi.IDnome='$parCF_Login'";
	 		$Login = mysql_query($query_Login, $MyPresenze) or die(mysql_error());
	  		$row_Login = mysql_fetch_assoc($Login);
	  		$totalRows_Login = mysql_num_rows($Login);
	 		$_SESSION['UserID'] = $row_Login['IDnome'];
	 		header("Location: Master.php");
		}
} else if (isset($_POST['CardCode'])){
		if (strlen($_POST['CardCode']) == 16) {
			$parCF_Login = $_POST['CardCode'];		
		} else {
			$parCF_Login = substr($_POST['CardCode'], 1, 16);
		}
			//echo($parCF_Login);
	 		$_SESSION['autorized'] = true;
	 		mysql_select_db($database_MyPresenze, $MyPresenze);
	  		$query_Login = "SELECT pre_elenconomi.IDnome, pre_elenconomi.CF FROM pre_elenconomi WHERE pre_elenconomi.CF='$parCF_Login'";
	 		$Login = mysql_query($query_Login, $MyPresenze) or die(mysql_error());
	  		$row_Login = mysql_fetch_assoc($Login);
	  		$totalRows_Login = mysql_num_rows($Login);
	 		$_SESSION['UserID'] = $row_Login['IDnome'];
	 		header("Location: Master.php");
} else {
	unset ($_SESSION['UserID']);
	$_SESSION['autorized'] = false;
	header("Location: LoginError.php");
}

mysql_free_result($Login);
?>
