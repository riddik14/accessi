<?php
session_start();
require_once('Connections/MyPresenze.php');

 $maxRows_Prenotazioni = 10;
 $Sede = $_SESSION['Sede'];
 $Pasto = $_SESSION['Pasto'];
 $Giorno = date("Y-m-d");

$query_Prenotazioni = "SELECT pre_accessi.IDrecord, pre_accessi.IDnome, pre_accessi.GIORNO, pre_accessi.PASTO, pre_accessi.Ora_pren, 
					   pre_accessi.Ti_R, pre_accessi.Se, pre_accessi.Cons, date_format(pre_accessi.Ora_cons_pr,'%H:%i') AS OraCons, 
					   pre_tiporazione.TipoRazione, pre_sedi.SEDE, pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, 
					   pre_elenconomi.CF FROM pre_accessi, pre_tiporazione, pre_sedi, pre_elenconomi, pre_gradi 
					   WHERE pre_sedi.IDsede=pre_accessi.Se AND pre_tiporazione.ID=pre_accessi.Ti_R AND pre_elenconomi.IDnome=pre_accessi.IDnome 
					   AND pre_accessi.PASTO='$Pasto' AND pre_accessi.Se='$Sede' AND pre_accessi.GIORNO='$Giorno' AND pre_accessi.Ora_cons_pr> 0
					   AND pre_gradi.ID=pre_elenconomi.IDgrado ORDER BY pre_accessi.Ora_cons_pr DESC";
$query_limit_Prenotazioni = sprintf("%s LIMIT %d", $query_Prenotazioni, $maxRows_Prenotazioni);
$Prenotazioni = $PRES_conn->query($query_limit_Prenotazioni);
$row_Prenotazioni = mysqli_fetch_assoc($Prenotazioni);
$totalRows_Prenotazioni = $Prenotazioni->num_rows;
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Documento senza titolo</title>
<style type="text/css">

.Stile52 {color: #FFFFFF}
.Stile53 {	color: #0099FF;
	font-weight: bold;
}
.Stile64 {color: #FFFFFF}
.Stile76 {font-size: small; color: #FFFFFF}
.Stile73 {
	font-family: Arial, Helvetica, sans-serif;
	color: #FFFFFF;
}

</style>
</head>

<body>
  <p class="Stile52" align="center"><strong>ULTIMI PASSAGGI</strong></p>
<?php if($totalRows_Prenotazioni>0) { ?>
<table border="2" align="center" cellspacing="0" bgcolor="#333333" width="95%">

  <tr class="Stile52">
    <th scope="col"><span class="Stile64">Nome</span></th>
    <th scope="col"><div align="center" class="Stile64">Razione </div></th>
    <th scope="col"><div align="center" class="Stile64">Consumato</div></th>
  </tr>
  <?php do { ?>
  <tr class="Stile52">
    <td height="27" nowrap><div align="center" class="Stile76"><?php echo $row_Prenotazioni['Grado'] ." " . $row_Prenotazioni['Cognome']." ".$row_Prenotazioni['Nome']; ?> </div></td>
    <td height="27" nowrap><div align="center" class="Stile76">
        <?php if($row_Prenotazioni['TipoRazione'] == "ORDINARIA") {; ?>
        <span class="Stile64">
        <?php } else { ?>
        <span class="Stile53">
        <?php } ?>
        <?php echo $row_Prenotazioni['TipoRazione']; ?></span></span></div></td>
    <td nowrap><div align="center" class="Stile76"><img src="images/spunta.gif" width="23" height="23"></div></td>
  </tr>
  <?php } while ($row_Prenotazioni = mysqli_fetch_assoc($Prenotazioni)); 
	  }?>
</table>

</body>
</html>
<?php
mysqli_free_result($Prenotazioni);

?>