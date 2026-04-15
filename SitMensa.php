<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
mysql_select_db($database_MyPresenze, $MyPresenze);
$query_Recordset1 = "SELECT riepilogomensa.Categoria, riepilogomensa.DEN_UN_OPER, riepilogomensa.GIORNO, riepilogomensa.PASTO, riepilogomensa.TipoRazione, riepilogomensa.SEDE, riepilogomensa.Prenotati, riepilogomensa.Consumati, riepilogomensa.Pagamento FROM riepilogomensa WHERE riepilogomensa.GIORNO='2009-11-17'";
$Recordset1 = mysql_query($query_Recordset1, $MyPresenze) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento senza titolo</title>
</head>

<body>
<table width="200" border="1">
  <tr>
    <td>Reparto</td>
    <td>Categoria</td>
    <td>Razione</td>
    <td>Numero pren</td>
    <td>Numero cons </td>
    <td>Diff</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php do { ?>
  <tr>
    <td><?php echo $row_Recordset1['DEN_UN_OPER']; ?></td>
    <td><?php echo $row_Recordset1['Categoria']; ?></td>
    <td><?php echo $row_Recordset1['TipoRazione']; ?></td>
    <td><?php echo $row_Recordset1['Prenotati']; ?></td>
    <td><?php echo $row_Recordset1['Consumati']; ?></td>
    <td><?php echo ($row_Recordset1['Prenotati'] - $row_Recordset1['Consumati']); ?></td>
    <td><?php echo $row_Recordset1['PASTO']; ?></td>
    <td><?php echo $row_Recordset1['SEDE']; ?></td>
  </tr>
  <?php } while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
