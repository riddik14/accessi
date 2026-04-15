<?php
require_once('Connections/MyPresenze.php');
require_once('XlsReader/reader.php');

session_start();
$Tx_cert = $_SESSION['UserID'];

// Query di ricerca dati utente collegato


// Query di caricamento delle Sedi
$query_Sede = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
$Sede = $PRES_conn->query($query_Sede);
$row_Sede = mysqli_fetch_assoc($Sede);
$totalRows_Sede = $Sede->num_rows;
function convertiData($data, $separatoreData, $nGiorniDaSommare) {
  list($giorno, $mese, $anno) = explode($separatoreData, $data);
  return date("Y-m-d", mktime(0, 0, 0, $mese, $giorno + $nGiorniDaSommare, $anno));
}

function convertiDataSQL($data, $separatoreData, $nGiorniDaSommare) {
  list($anno, $mese, $giorno) = explode($separatoreData, $data);
  return date("Y-m-d", mktime(0, 0, 0, $mese, $giorno + $nGiorniDaSommare, $anno));
}

function convertiDataPre($dataEur) {
  $rsl = str_replace("/", "-", $dataEur);
  return $rsl;
}

function convertiDataPren($dataEur) {
  $rsl = explode('-', $dataEur);
  $rsl = array_reverse($rsl);
  return implode('-', $rsl);
}

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
  $theValue = addslashes($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  /*mysql_select_db($database_MyPresenze, $MyPresenze);
  $query_DelRecordMensa = sprintf("DELETE FROM pre_menu");
  $DelRecordMensa = mysql_query($query_DelRecordMensa, $MyPresenze) or die(mysql_error($MyPresenze));

  mysql_select_db($database_MyPresenze, $MyPresenze);
  $query_DelRecordMensa = sprintf("DELETE FROM pre_piatti");
  $DelRecordMensa = mysql_query($query_DelRecordMensa, $MyPresenze) or die(mysql_error($MyPresenze));
  */
  

  if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
    $error = "Non e stato selezionato alcun file da inviare!";
    unlink($_FILES['file']['tmp_name']);
    // assign error message, remove uploaded file, redisplay form. 
  } else { //a file was uploaded 
    $maxfilesize = 4102400;
    if ($_FILES['file']['size'] > $maxfilesize) {
      $error = "Il file selezionato e troppo grande.";
      unlink($_FILES['file']['tmp_name']); // assign error message, remove uploaded file, redisplay form..
    } else {
      if ($_FILES['file']['type'] != "application/vnd.ms-excel" && $_FILES['file']['type'] != "application/vnd.ms-excel") {
        $error = "Il file selezionato non e un file in formato Excell.";
        unlink($_FILES['file']['tmp_name']); // assign error message, remove uploaded file, redisplay form. 
      } else { //File has passed all validation, copy it to the final destination and remove the temporary file: 
        $file_tmp = $_FILES['file']['tmp_name'];
$file_name = $_FILES['file']['name'];
$file_destination = 'C:\xampp\htdocs\ACCESSI\XLSFiles\ ' . $file_name;
$file_delete = 'C:\Users\cllfba73d21z133l\Desktop\XLSFiles\ ';
move_uploaded_file($file_tmp, $file_destination);
//unlink($file_delete);
$filename = ($_FILES['file']['name']);
print ("Foto inserita correttamente.");

        // Inizio importazione dati da Excel // ExcelFile($filename, $encoding);
        $data = new Spreadsheet_Excel_Reader(); // Set output Encoding. 
        $data->setOutputEncoding('utf-8'); 
        $data->read("C:\xampp\htdocs\ACCESSI\XLSFiles" . $filename); 

        /* $data->sheets[0]['numRows'] - 
            count rows $data->sheets[0]['numCols'] - 
            count columns $data->sheets[0]['cells'][$i][$j] - 
            data from $i-row $j-column $data->sheets[0]['cellsInfo'][$i][$j] -
            extended info about cell $data->sheets[0]['cellsInfo'][$i][$j]['type'] ="date"|"number"|"unknown"if 'type' =="unknown"- use 'raw' value, because cell contain value with format '0.00'; 
            $data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format 
            $data->sheets[0]['cellsInfo'][$i][$j]['colspan'] $data->sheets[0]['cellsInfo'][$i][$j]['rowspan'] */

        error_reporting(E_ALL ^ E_NOTICE);
        $n_foglio = $_POST['n_sheet'];
        $n_riga = $_POST['nriga'];

        $Piatto = $data->sheets[$n_foglio]['cells'][2][4];
        $dal = convertiData(($data->sheets[$n_foglio]['cells'][5][6]), "/", -1);

        // Importa la data e le vivande della colazione

        for ($i = $n_riga; $i <= $data->sheets[$n_foglio]['numRows']; $i++) {  // Eseguo un ciclo sulle righe a partire dalla n.8 
          $test = $data->sheets[$n_foglio]['cells'][$i][1]; //Memorizzo il valore della prima cella della righa per vedere se il suo valore ? X
          $ComandoSQL = "";
          //echo "Test: " . $test ."<BR>";
          if ($test != "X" AND $test != "x") {
            $tempPiatto = $data->sheets[$n_foglio]['cells'][$i][2];
            if (isset($tempPiatto)) { //Se il valore della cella contenente il piatto ? valorizzata allora eseguo una query per vedere se ? gi? presente nella tabella piatti
              $tempPiatto = str_replace("*", "", $tempPiatto);
              $tempPiattoConv = str_replace("*", "", addslashes(iconv("ISO-8859-1", "UTF-8", $tempPiatto)));
              $tempPiattoConv = str_replace(",", " ", addslashes(iconv("ISO-8859-1", "UTF-8", $tempPiattoConv)));
              $Kcal = 0;
              $Perc = 0;

              $CePiatto = "SELECT ID, descrizione FROM pre_piatti WHERE descrizione LIKE ?";
              $stmt = mysqli_prepare($MyPresenze, $CePiatto);
              mysqli_stmt_bind_param($stmt, "s", $tempPiattoConv);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_bind_result($stmt, $IdPiatto, $descrizionePiatto);
              mysqli_stmt_fetch($stmt);
              mysqli_stmt_close($stmt);

              // Se il piatto non ? gi? presente nella tabella allora lo inserisco
              if (!isset($descrizionePiatto)) { //Se non trovo il piatto lo inserisco nella tabella
                $descrizionePiatto = iconv("UTF-8", "ISO-8859-1", $tempPiatto);
                $insertSQL = "INSERT INTO pre_piatti(descrizione, Kcal, Perc, Portata) VALUES (?, ?, ?, 4)";
                $stmt = mysqli_prepare($MyPresenze, $insertSQL);
                mysqli_stmt_bind_param($stmt, "sis", $tempPiattoConv, $Kcal, $Perc);
                mysqli_stmt_execute($stmt);
                $IdPiatto = mysqli_insert_id($MyPresenze);
              }

              // Inserisco data ID sede e pasto nella tabella pre_menu
              $datapranzo = $dal;
              $insertSQL = "INSERT INTO pre_menu(Pasto, IDpiatto, Giorno, Sede) VALUES (3, ?, ?, ?)";
              $stmt = mysqli_prepare($MyPresenze, $insertSQL);
              mysqli_stmt_bind_param($stmt, "isi", $IdPiatto, $datapranzo, $_POST['Sede']);
              mysqli_stmt_execute($stmt);
              $menuId = mysqli_insert_id($MyPresenze);

              if ($test == "18181818" or $test == "2018") {
                $dal = convertiDataSQL($dal, "-", 1);
              }
            }
          } else {
            break;
          }
        } //Fine ciclo next

        // Importa la data e il primo piatto del pranzo

        for ($i = $n_riga; $i <= $data->sheets[$n_foglio]['numRows']; $i++) {  // Eseguo un ciclo sulle righe a partire dalla n.8 
          $test = $data->sheets[$n_foglio]['cells'][$i][1]; //Memorizzo il valore della prima cella della righa per vedere se il suo valore ? X
          $ComandoSQL = "";
          //echo "Test: " . $test ."<BR>";
          if ($test != "X" AND $test != "x") {
            $tempPiatto = $data->sheets[$n_foglio]['cells'][$i][3];
            if (isset($tempPiatto)) { //Se il valore della cella contenente il piatto ? valorizzata allora eseguo una query per vedere se ? gi? presente nella tabella piatti
              $tempPiatto = str_replace("*", "", $tempPiatto);
              $tempPiattoConv = str_replace("*", "", addslashes(iconv("ISO-8859-1", "UTF-8", $tempPiatto)));
              $Kcal = $data->sheets[$n_foglio]['cells'][$i][4];
              $Perc = $data->sheets[$n_foglio]['cells'][$i][5];

              $CePiatto = "SELECT ID, descrizione FROM pre_piatti WHERE descrizione LIKE ?";
              $stmt = mysqli_prepare($MyPresenze, $CePiatto);
              mysqli_stmt_bind_param($stmt, "s", $tempPiattoConv);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_bind_result($stmt, $IdPiatto, $descrizionePiatto);
              mysqli_stmt_fetch($stmt);
              mysqli_stmt_close($stmt);

              // Se il piatto non ? gi? presente nella tabella allora lo inserisco
              if (!isset($descrizionePiatto) && isset($Kcal)) { //Se non trovo il piatto lo inserisco nella tabella
                $descrizionePiatto = iconv("UTF-8", "ISO-8859-1", $tempPiatto);
                $insertSQL = "INSERT INTO pre_piatti(descrizione, Kcal, Perc, Portata) VALUES (?, ?, ?, 1)";
                $stmt = mysqli_prepare($MyPresenze, $insertSQL);
                mysqli_stmt_bind_param($stmt, "sis", $tempPiattoConv, $Kcal, $Perc);
                mysqli_stmt_execute($stmt);
                $IdPiatto = mysqli_insert_id($MyPresenze);
              }

              // Inserisco data ID sede e pasto nella tabella pre_menu
              $datapranzo = $dal;
              $IdPiatto = "SELECT ID FROM pre_piatti WHERE descrizione LIKE ?";
              $stmt = mysqli_prepare($MyPresenze, $IdPiatto);
              mysqli_stmt_bind_param($stmt, "s", $tempPiattoConv);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_bind_result($stmt, $IdPiatto);
              mysqli_stmt_fetch($stmt);
              mysqli_stmt_close($stmt);

              $insertSQL = "INSERT INTO pre_menu(Pasto, IDpiatto, Giorno, Sede) VALUES (1, ?, ?, ?)";
              $stmt = mysqli_prepare($MyPresenze, $insertSQL);
              mysqli_stmt_bind_param($stmt, "isi", $IdPiatto, $datapranzo, $_POST['Sede']);
              mysqli_stmt_execute($stmt);
              $menuId = mysqli_insert_id($MyPresenze);

              if ($test == "18181818" or $test == "2018") {
                $dal = convertiDataSQL($dal, "-", 1);
              }
            }
          } else {
            break;
          }
        } //Fine ciclo next

        // Importa la data e il secondo piatto del pranzo

        $dal = convertiData(($data->sheets[$n_foglio]['cells'][5][6]), "/", -1);

        for ($i = $n_riga; $i <= $data->sheets[$n_foglio]['numRows']; $i++) {  // Eseguo un ciclo sulle righe a partire dalla n.8 
          $test = $data->sheets[$n_foglio]['cells'][$i][1]; //Memorizzo il valore della prima cella della righa per vedere se il suo valore ? X
          $ComandoSQL = "";

          if ($test != "X" AND $test != "x") {
            $tempPiatto = $data->sheets[$n_foglio]['cells'][$i][6];
            if (isset($tempPiatto)) { //Se il valore della cella contenente il piatto ? valorizzata allora eseguo una query per vedere se ? gi? presente nella tabella piatti
              $tempPiatto = str_replace("*", "", $tempPiatto);
              $tempPiattoConv = str_replace("*", "", addslashes(iconv("ISO-8859-1", "UTF-8", $tempPiatto)));
              $Kcal = $data->sheets[$n_foglio]['cells'][$i][7];
              $Perc = $data->sheets[$n_foglio]['cells'][$i][8];

              $CePiatto = "SELECT ID, descrizione FROM pre_piatti WHERE descrizione LIKE ?";
              $stmt = mysqli_prepare($MyPresenze, $CePiatto);
              mysqli_stmt_bind_param($stmt, "s", $tempPiattoConv);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_bind_result($stmt, $IdPiatto, $descrizionePiatto);
              mysqli_stmt_fetch($stmt);
              mysqli_stmt_close($stmt);

              // Se il piatto non ? gi? presente nella tabella allora lo inserisco
              if (!isset($descrizionePiatto)) { //Se non trovo il piatto lo inserisco nella tabella
                $insertSQL = "INSERT INTO pre_piatti(descrizione, Kcal, Perc, Portata) VALUES (?, ?, ?, 2)";
                $stmt = mysqli_prepare($MyPresenze, $insertSQL);
                mysqli_stmt_bind_param($stmt, "sis", $tempPiattoConv, $Kcal, $Perc);
                mysqli_stmt_execute($stmt);
                $IdPiatto = mysqli_insert_id($MyPresenze);
              }

              // Inserisco data ID sede e pasto nella tabella pre_menu
              $datapranzo = $dal;
              $insertSQL = "INSERT INTO pre_menu(Pasto, IDpiatto, Giorno, Sede) VALUES (1, ?, ?, ?)";
              $stmt = mysqli_prepare($MyPresenze, $insertSQL);
              mysqli_stmt_bind_param($stmt, "isi", $IdPiatto, $datapranzo, $_POST['Sede']);
              mysqli_stmt_execute($stmt);
              $menuId = mysqli_insert_id($MyPresenze);

              if ($test == "18181818" or $test == "2018") {
                $dal = convertiDataSQL($dal, "-", 1);
              }
            }
          } else {
            break;
          }
        } //Fine ciclo next

        // Importa la data e il primo piatto della cena

        $dal = convertiData(($data->sheets[$n_foglio]['cells'][5][6]), "/", -1);

        for ($i = $n_riga; $i <= $data->sheets[$n_foglio]['numRows']; $i++) {  // Eseguo un ciclo sulle righe a partire dalla n.8 
          $test = $data->sheets[$n_foglio]['cells'][$i][1]; //Memorizzo il valore della prima cella della righa per vedere se il suo valore ? X
          $ComandoSQL = "";

          if ($test != "X" AND $test != "x") {
            $tempPiatto = $data->sheets[$n_foglio]['cells'][$i][9];
            if (isset($tempPiatto)) { //Se il valore della cella contenente il piatto ? valorizzata allora eseguo una query per vedere se ? gi? presente nella tabella piatti
              $tempPiatto = str_replace("*", "", $tempPiatto);
              $tempPiattoConv = str_replace("*", "", addslashes(iconv("ISO-8859-1", "UTF-8", $tempPiatto)));
              $Kcal = $data->sheets[$n_foglio]['cells'][$i][10];
              $Perc = $data->sheets[$n_foglio]['cells'][$i][11];

              $CePiatto = "SELECT ID, descrizione FROM pre_piatti WHERE descrizione LIKE ?";
              $stmt = mysqli_prepare($MyPresenze, $CePiatto);
              mysqli_stmt_bind_param($stmt, "s", $tempPiattoConv);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_bind_result($stmt, $IdPiatto, $descrizionePiatto);
              mysqli_stmt_fetch($stmt);
              mysqli_stmt_close($stmt);

              // Se il piatto non ? gi? presente nella tabella allora lo inserisco
              if (!isset($descrizionePiatto)) { //Se non trovo il piatto lo inserisco nella tabella
                $insertSQL = "INSERT INTO pre_piatti(descrizione, Kcal, Perc, Portata) VALUES (?, ?, ?, 1)";
                $stmt = mysqli_prepare($MyPresenze, $insertSQL);
                mysqli_stmt_bind_param($stmt, "sis", $tempPiattoConv, $Kcal, $Perc);
                mysqli_stmt_execute($stmt);
                $IdPiatto = mysqli_insert_id($MyPresenze);
              }

              // Inserisco data ID sede e pasto nella tabella pre_menu
              $datapranzo = $dal;
              $insertSQL = "INSERT INTO pre_menu(Pasto, IDpiatto, Giorno, Sede) VALUES (2, ?, ?, ?)";
              $stmt = mysqli_prepare($MyPresenze, $insertSQL);
              mysqli_stmt_bind_param($stmt, "isi", $IdPiatto, $datapranzo, $_POST['Sede']);
              mysqli_stmt_execute($stmt);
              $menuId = mysqli_insert_id($MyPresenze);

              if ($test == "18181818" or $test == "2018") {
                $dal = convertiDataSQL($dal, "-", 1);
              }
            }
          } else {
            break;
          }
        } //Fine ciclo next

        // Importa la data e il secondo piatto della cena

        $dal = convertiData(($data->sheets[$n_foglio]['cells'][5][6]), "/", -1);

        for ($i = $n_riga; $i <= $data->sheets[$n_foglio]['numRows']; $i++) {  // Eseguo un ciclo sulle righe a partire dalla n.8 
          $test = $data->sheets[$n_foglio]['cells'][$i][1]; //Memorizzo il valore della prima cella della righa per vedere se il suo valore ? X
          $ComandoSQL = "";

          if ($test != "X" AND $test != "x") {
            $tempPiatto = $data->sheets[$n_foglio]['cells'][$i][12];
            if (isset($tempPiatto)) { //Se il valore della cella contenente il piatto ? valorizzata allora eseguo una query per vedere se ? gi? presente nella tabella piatti
              $tempPiatto = str_replace("*", "", $tempPiatto);
              $tempPiattoConv = str_replace("*", "", addslashes(iconv("ISO-8859-1", "UTF-8", $tempPiatto)));
              $Kcal = $data->sheets[$n_foglio]['cells'][$i][13];
              $Perc = $data->sheets[$n_foglio]['cells'][$i][14];

              $CePiatto = "SELECT ID, descrizione FROM pre_piatti WHERE descrizione LIKE ?";
              $stmt = mysqli_prepare($MyPresenze, $CePiatto);
              mysqli_stmt_bind_param($stmt, "s", $tempPiattoConv);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_bind_result($stmt, $IdPiatto, $descrizionePiatto);
              mysqli_stmt_fetch($stmt);
              mysqli_stmt_close($stmt);

              // Se il piatto non ? gi? presente nella tabella allora lo inserisco
              if (!isset($descrizionePiatto)) { //Se non trovo il piatto lo inserisco nella tabella
                $insertSQL = "INSERT INTO pre_piatti(descrizione, Kcal, Perc, Portata) VALUES (?, ?, ?, 2)";
                $stmt = mysqli_prepare($MyPresenze, $insertSQL);
                mysqli_stmt_bind_param($stmt, "sis", $tempPiattoConv, $Kcal, $Perc);
                mysqli_stmt_execute($stmt);
                $IdPiatto = mysqli_insert_id($MyPresenze);
              }
// Inserisco data ID sede e pasto nella tabella pre_menu
              $datapranzo = $dal;
              $insertSQL = "INSERT INTO pre_menu(Pasto, IDpiatto, Giorno, Sede) VALUES (2, ?, ?, ?)";
              $stmt = mysqli_prepare($MyPresenze, $insertSQL);
              mysqli_stmt_bind_param($stmt, "isi", $IdPiatto, $datapranzo, $_POST['Sede']);
              mysqli_stmt_execute($stmt);
              $menuId = mysqli_insert_id($MyPresenze);

              if ($test == "18181818" or $test == "2018") {
                $dal = convertiDataSQL($dal, "-", 1);
              }
            }
          } else {
            break;
          }
        } //Fine ciclo next
      }
    }
  }

  echo ('<script> alert("Operazione completata."); </script>');
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Documento senza titolo</title>
<script language="JavaScript" src="calendar1.js"></script>
<style type="text/css">
<!--
.Stile1 {font-size: x-small}
.Stile2 {font-size: 9px}
.Stile3 {font-size: 10px}
.Stile4 {font-size: 10}
.Stile5 {font-size: 12px}
.Stile7 {font-size: large}
-->
</style>
</head>

<body>
<p align="center"><span class="Stile1"><img src="images/BannerCealpi.jpg" width="800" height="92" border="1"><a href="PrenotazioneMassiva.php"><img src="images/xbox360power.jpg" width="92" height="92" border="1"></a></span></p>
<p align="center"></p>
<p align="center">&nbsp;</p>
<p align="center" class="Stile4">Importazione del men&ugrave; sul sistema <a href="PRENOTAZIONI_OFFLINE.xls" target="_blank"></a></p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" name="form1" class="Stile3">

<p class="Stile7">Seleziona il file xls da caricare <strong>(ATTENZIONE: cambiare nel file excell, il formato della data di inizio periodo in dd/mm/yyyy e inserire una riga alla fine dell'ultima giornata e digitare una X nella pima casella)</strong>:
</p>
<p class="Stile7"><img src="img/FormatoDate.jpg" width="400" height="320"> <img src="img/Xfinegiornata.jpg" width="300" height="150"></p>
<p>
  <input name="file" type="file" size="60">
</p>
<p>Il menu inizia alla riga 
  <input name="nriga" type="text" id="nriga" size="2" maxlength="2"> 
  del foglio xls</p>
<p>Sede caserma 
  <select name="Sede" id="Sede">
    <?php do { ?>
    <option value="<?php echo htmlspecialchars($row_Sede['IDsede']);?>"><?php echo htmlspecialchars($row_Sede['SEDE']);?></option>
    <?php } while ($row_Sede = mysqli_fetch_assoc($Sede));
  $rows = mysqli_num_rows($Sede);
  if($rows > 0) {
      mysqli_data_seek($Sede, 0);
	  $row_Sede = mysqli_fetch_assoc($Sede);
  }
?>
  </select> 
  
</p>
<p>&nbsp;</p>
<p>
  <input type="submit" name="Submit" value="Elabora file">
  <input type="hidden" name="MM_update" value="form1">
</p>
</form>
<?php if ($_SESSION['Visualizza'] == 1) { ?>
<p>Dati da elaborare </p>
<table width="416" border="1" align="left">
  <tr>
    <td width="74" nowrap><span class="Stile5">Data</span></td>
    <td width="88" nowrap><span class="Stile5">Piatto</span></td>
    <td width="103" nowrap><span class="Stile5">Kcal</span></td>
    <td width="123" nowrap class="Stile5">%</td>
  </tr>
  <tr class="Stile2">
    <td nowrap><div align="center"><span class="Stile5"><?php echo htmlspecialchars($SISME);?></span></div></td>
    <td nowrap><div align="center"><span class="Stile5"><?php echo htmlspecialchars($DataPre);?></span></div></td>
    <td nowrap><span class="Stile5"><?php echo htmlspecialchars($motivo);?></span></td>
    <td nowrap><span class="Stile5"><a href="mailto:<?php echo htmlspecialchars($PDC);?>"><?php echo htmlspecialchars($PDC);?></a></span></td>
  </tr>
</table>
<p align="left"></p>
<p align="left"></p>
<p align="left">Elenco del personale segnalato </p>
<table width="480" border="1" align="left">
  <tr class="Stile2">
    <td width="51" nowrap><span class="Stile5">CF</span></td>
    <td width="65" nowrap><span class="Stile5">Nome</span></td>
    <td width="81" nowrap><span class="Stile5">Cognome</span></td>
    <td width="63" nowrap><span class="Stile5">Pasto prenotato </span></td>
    <td width="64" nowrap><div align="center">In forza </div></td>
    <td width="116" nowrap><div align="center"><span class="Stile5">CF gi&agrave; presente</span></div></td>
  </tr>
  <?php do { ?>
  <tr class="Stile2">
    <td height="31" nowrap><span class="Stile5"><?php echo htmlspecialchars($row_Dati['CF']); ?></span></td>
    <td nowrap><span class="Stile5"><?php echo htmlspecialchars($row_Dati['Nome']); ?></span></td>
    <td nowrap><span class="Stile5"><?php echo htmlspecialchars($row_Dati['Cognome']); ?></span></td>
    <td nowrap><span class="Stile5"><?php echo htmlspecialchars($row_Dati['Pasto']); ?></span></td>
    <td nowrap><div align="center"><span class="Stile5"><?php echo htmlspecialchars($row_Dati['Forza']); ?></span></div></td>
    <td nowrap><form action="" method="post" name="form2" class="Stile5">
      <div align="center">
        <?php if (isset($row_Dati['IDnome'])){ ?>
          <img src="images/attenzione.gif"width="26" height="24">
        <?php } ?>
      </div>
    </form>
    </td>
  </tr>
  <?php } while ($row_Dati = mysqli_fetch_assoc($Dati)); ?>

</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>Impostare i seguenti parametri di importazione e cliccare sul pulsante &lt;Carica anagrafiche e conferma prenotazione&gt;</p>
<p>Se il personale dovr&agrave; essere inserito in una nuova unit&agrave; operativa o in una unit&agrave; operativa cui l'utente non ha accesso, sar&agrave; necessario contattare l'amministratore del sistema per creare una nuova unit&agrave; operativa o per ottenere l'accesso ad una gi&agrave; presente. </p>
<form action="" method="post" name="form3" class="Stile5">
  Pasto:
  <select name="Pasto" id="Pasto">
    <option value="1" selected>PRANZO</option>
    <option value="2">CENA</option>
    <option value="3">COLAZIONE</option>
  </select> 
  data
  <input name="datapre" type="text" id="datapre" value="<?php echo htmlspecialchars($DataPre);?>" size="10" maxlength="10"> 
  <a href="javascript:cal1.popup();"><img src="img/cal.gif" width="25" height="25" border="0" alt="Click qui per cambiare data"></a>  sede
  <select name="Sede" id="Sede">
    <?php
do {  
?>
    <option value="<?php echo htmlspecialchars($row_Sede['IDsede']);?>"><?php echo htmlspecialchars($row_Sede['SEDE']);?></option>
    <?php
} while ($row_Sede = mysqli_fetch_assoc($Sede));
  $rows = mysqli_num_rows($Sede);
  if($rows > 0) {
      mysqli_data_seek($Sede, 0);
	  $row_Sede = mysqli_fetch_assoc($Sede);
  }
?>
  </select>   
  unit&agrave; operativa
  <select name="UO" id="UO">
    <?php
do {  
?>
    <option value="<?php echo htmlspecialchars($row_UO['ID_UO']);?>"><?php echo htmlspecialchars($row_UO['DEN_UN_OPER']);?></option>
    <?php
} while ($row_UO = mysqli_fetch_assoc($UO));
  $rows = mysqli_num_rows($UO);
  if($rows > 0) {
      mysqli_data_seek($UO, 0);
	  $row_UO = mysqli_fetch_assoc($UO);
  }
?>
  </select>
  razione 
  <select name="TipoRazione" id="TipoRazione">
    <option value="1" selected>Ordinaria</option>
    <option value="2">Media</option>
    <option value="3">Pesante</option>
  </select>
  a pagamento 
  <select name="Pagamento" id="Pagamento">
    <option value="0" selected>No</option>
    <option value="1">Si</option>
  </select>
  <input type="submit" name="Submit" value="Carica anagrafiche e conferma prenotazione">
  <input name="mmupdate" type="hidden" id="mmupdate" value="form3">
</form>
<?php } ?>
<p align="center">&nbsp; </p>
</body>
</html>
<?php
mysqli_free_result($Sede);

?>