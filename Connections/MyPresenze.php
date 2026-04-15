<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_PRES = "127.0.0.1";
$database_PRES = "presenze";
$username_PRES = "admin";
$password_PRES = "admin23253";
$PRES_conn = new mysqli($hostname_PRES, $username_PRES, $password_PRES, $database_PRES); 
if($PRES_conn->connect_error) {
	echo "Errore di connessione al DB " . $PRES_conn->connect_error;
} else {
//	echo "Connessione riuscita";
	$PRES_conn -> set_charset("utf8");
}
?>
