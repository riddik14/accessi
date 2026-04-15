<?php require_once('../Connections/MyPresenze.php'); ?>
<?php

if (isset($_GET['IDpren'])){
	$IDpren = $_GET['IDpren'];
};
// Lettura intestazione
mysql_select_db($database_MyPresenze, $MyPresenze);
			$query_Intestazione = "SELECT pre_setup.Reparto FROM pre_setup";
			$Intestazione = mysql_query($query_Intestazione, $MyPresenze) or die(mysql_error());
			$row_Intestazione = mysql_fetch_assoc($Intestazione);
			$Reparto = $row_Intestazione['Reparto'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Stampa codici a barre personale di passaggio</title>

<style type="text/css">
<!--
.Stile2 {font-size: xx-small}
.Stile3 {font-size: small}
-->
</style>
</head>

<body>

  <p align="center"><img src="./images/BannerCealpi.jpg" width="671" height="74"></p>
  <p align="center">&nbsp;</p>


<p align="center"><?php 
			mysql_select_db($database_MyPresenze, $MyPresenze);
			mysql_select_db($database_MyPresenze, $MyPresenze);
			$query_BarCode = "SELECT pre_elenconomi.IDnome, pre_elenconomi.ID_PERS_MTR, pre_elenconomi.Cognome, pre_elenconomi.Nome, 
							  pre_elenconomi.CF, pre_gradi.Grado 
							  FROM pre_elenconomi, pre_gradi, pre_offlinedata 
				  	    	  WHERE pre_gradi.ID=pre_elenconomi.IDgrado AND pre_elenconomi.IDnome=pre_offlinedata.IDnome AND
							  pre_offlinedata.IDmaster = '$IDpren'";
			$BarCode = mysql_query($query_BarCode, $MyPresenze) or die(mysql_error());
			$row_BarCode = mysql_fetch_assoc($BarCode);
			$totalRows_BarCode = mysql_num_rows($BarCode);
		?></p>
		  <?php do { ?>
			<table width="363" border="2" align="center">
	 	 	<tr>
    		<th width="353" height="178" scope="col"><h3 align="center"><?php echo $Reparto; ?></h3>
      			<h5>BADGE  PERSONALE PER SERVIZIO MENSA </h5>
		        <h5><?php echo $row_BarCode['Grado']; ?> <?php echo $row_BarCode['Cognome']; ?> <?php echo $row_BarCode['Nome']; ?> </h5>
			    <p>PIN UTENTE <?php echo $row_BarCode['IDnome'] . substr($row_BarCode['CF'], 6, 2) .substr($row_BarCode['CF'], 9, 2) ;  ?></p>
			    <p class="Stile2">
		        <?php 
	  				$encode="CODE39"; 
			        $bdata=$row_BarCode['CF']; 
			        $height=90; 
			        $scale=1; 
			        $bgcolor="#FFFFFF"; 
			        $color="#000000"; 
			        $file=""; 
			        $type="png"; 
					$qstr = "encode=CODE39&bdata=".urlencode($bdata)."&height=60&scale=1,5&bgcolor=".urlencode("#FFFFFF")."&color=".urlencode("#000000")."&file=&type=gif"; 
					echo "<img src='barcode.php?$qstr'>";  ?>
              </p>
		      </th>
		  </tr>
	  </table>
   <?php } while ($row_BarCode = mysql_fetch_assoc($BarCode)); ?>
   <p>Questi badge possono essere utilizzati per la registrazione delle consumazioni dei pasti presso le mense del <?php echo $Reparto;?> per tutta la durata della missione. </p>
</body>
</html>
<?php

mysql_free_result($BarCode);
?>