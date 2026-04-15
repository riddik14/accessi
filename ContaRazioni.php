<?php session_start();
require_once('Connections/MyPresenze.php');

$Sede = $_SESSION['Sede'];
$Pasto =  $_SESSION['Pasto'];
$Giorno = date("Y-m-d");

$query_Conteggio = "SELECT riepilogomensa.Se, riepilogomensa.GIORNO, riepilogomensa.PASTO, riepilogomensa.TipoRazione, 
					Sum(riepilogomensa.Prenotati) AS Prenotati, Sum(riepilogomensa.Consumati) as Consumati
					FROM riepilogomensa
					GROUP BY riepilogomensa.Se, riepilogomensa.GIORNO, riepilogomensa.PASTO, riepilogomensa.TipoRazione
					HAVING (((riepilogomensa.Se)='$Sede') AND ((riepilogomensa.GIORNO)='$Giorno') AND ((riepilogomensa.PASTO)='$Pasto'))";
if(!$Conteggio = $PRES_conn->query($query_Conteggio)){
  echo $PRES_conn->error;
  //exit;
};
$row_Conteggio = mysqli_fetch_assoc($Conteggio);
$totalRows_Conteggio = $Conteggio->num_rows;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Documento senza titolo</title>
<style type="text/css">
<style type="text/css">
<!--
.Stile23 {
	color: #FFFFFF;
	font-size: 32px;
}
.Stile40 {color: #333333}
.Stile52 {color: #FFFFFF}
.Stile54 {
	color: #3399FF;
	font-weight: bold;
	font-size:38px;
}
.Stile56 {
	color: #FFFF00;
	font-weight: bold;
	font-size:36px;
}
.Stile74 {font-size: x-large}
.Stile77 {font-size: 55px; color: #FFFFFF;}
.Stile78 {font-size: 55px}
.Stile79 {font-size: 55px; color: #FFFFFF; }
-->
</style>
</style>
</head>
<div align="center" class="Stile40">
      <table width="100%" border="1" align="center" cellspacing="0" bgcolor="#333333">
      <tr>
        <td nowrap scope="col"><p align="center" class="Stile52 Stile1 Stile74"> RAZIONE</p></td>
        <td nowrap scope="col"><p align="center" class="Stile52 Stile1 Stile74">PRENOTATI</p></td>
        <td nowrap scope="col"><p align="center" class="Stile52 Stile1 Stile74">CONSUMATI</p></td>
        <td nowrap scope="col"><p align="center" class="Stile52 Stile1 Stile74">DA CONSUMARE</p></td>
      </tr>
      <?php do { ?>
      <tr align="center" valign="middle" class="Stile23">
        <td height="86" class="Stile56" scope="col"><?php if($row_Conteggio['TipoRazione'] == "ORDINARIA") { ?>
            <span class="Stile56">
            <?php } else { ?>
            <span class="Stile54">
        <?php } echo $row_Conteggio['TipoRazione']; ?>
            </span></td>
        <td scope="col"><h1 align="center" class="Stile78"><?php echo $row_Conteggio['Prenotati']; ?></h1></td>
        <td scope="col"><h1 align="center" class="Stile79"><?php echo $row_Conteggio['Consumati']; ?></h1></td>
        <td scope="col"><h1 align="center" class="Stile77"><?php echo ($row_Conteggio['Prenotati'] - $row_Conteggio['Consumati']); ?> </h1></td>
      </tr>
      <?php } while ($row_Conteggio = mysqli_fetch_assoc($Conteggio)); ?>
  </table>
    
</div>
</body>
<?php mysqli_free_result($Conteggio); ?>
</html>
