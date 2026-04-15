<?php require_once('Connections/MyPresenze.php'); 

session_start();
$IDuser = $_SESSION['UserID'];

$query_Intestazione = "SELECT pre_setup.Reparto, pre_setup.Ditta_Rist FROM pre_setup";
$Intestazione = $PRES_conn->query($query_Intestazione);
$row_Intestazione =  mysqli_fetch_assoc($Intestazione);	
	
$Reparto =  iconv("UTF-8", "ISO-8859-1//IGNORE", $row_Intestazione['Reparto']);
$Ditta = iconv("UTF-8", "ISO-8859-1//IGNORE", $row_Intestazione['Ditta_Rist']);
mysqli_free_result($Intestazione);

if (isset($_POST['report']) && $_POST['report'] == 1) {

// INIZIO REPORT REGISTRO PRESENZE

//error_reporting(E_ALL);
include('class.ezpdf.php');
$pdf = new Cezpdf('a4','portrait');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezText($Reparto,20, array('justification'=>'center'));
$pdf->ezText('    ',18, 'center');
$pdf->ezText('Elenco degli ingressi e delle uscite registrate con la procedura automatizzata dal ',10, array('justification'=>'center'));
$pdf->ezText(' ',12, 'center');
//--------------------------------------------------

$mese = $_POST['mese'];
$anno = $_POST['anno'];
$Nome = $_POST['IDnome'];

$query_Nome = "SELECT  pre_gradi.Grado, pre_elenconomi.Nome, pre_elenconomi.Cognome
				FROM pre_elenconomi, pre_gradi
				WHERE pre_elenconomi.IDnome='$Nome' AND pre_gradi.ID=pre_elenconomi.IDgrado";
				
$data_Nome = $PRES_conn->query($query_Nome);	
$row_Nome =  mysqli_fetch_assoc($data_Nome);		
$Persona = $row_Nome['Grado']." ". $row_Nome['Nome']." ". $row_Nome['Cognome'];

$query = "SELECT pre_giornisett.WEEKDAY AS GIORNO, date_format(pre_orari_gg.GIORNO,'%d/%m/%Y') AS DATE, 
				 pre_orari_gg.ORA_IN AS ENTRATA, pre_orari_gg.ORA_OUT AS USCITA,
				 timediff(`pre_orari_gg`.`ORA_OUT`,`pre_orari_gg`.`ORA_IN`) AS PERIODO, 
				 pre_orari_gg.CAUSA AS CAUS, pre_orari_gg.NOTE
				 FROM pre_orari_gg, pre_giornisett 
				 WHERE pre_orari_gg.IDnome='$Nome' AND year(pre_orari_gg.GIORNO)='$anno' AND 
				 month(pre_orari_gg.GIORNO)='$mese' AND dayofweek(pre_orari_gg.GIORNO)=pre_giornisett.ID
				 ORDER BY pre_orari_gg.GIORNO";
$result = $PRES_conn->query($query);

$data = array();
while($row = mysqli_fetch_assoc($result)) {
	$data[] = $row;
};

$pdf->ezText($Persona, 10, array('justification'=>'center'));
$pdf->ezText(' ',12, 'center');
$pdf->ezTable($data,'','', array('shaded'=>0, 'showLines'=>2));
$pdf->ezText('',10, 'center');
$pdf->ezText('CAUSALI: NOL normale orario di lavoro; COL comando e logistica; SER: servizi di caserma; REC recupero compensativo; SI1 servizio isolato.',6, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Il sottoscritto, '. $Persona . ', dichiara che gli orari sopra elencati acquisisti attraverso procedura informatica, corrispondono ai servizi effettivamente prestati.',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Il presente statino costituisce il registro presenze del dichiarante per il mese selezionato.',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Aosta, l� '.date('d-m-Y'),10);
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Firma _____________________________',10, array('justification'=>'center'));
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Il Comandante/Capo Ufficio',10, array('justification'=>'center'));
$pdf->ezText('',10, 'center');
$pdf->ezText('____________________________________',10, array('justification'=>'center'));

if (isset($d) && $d){
	$pdfcode = $pdf->output(1);
	$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
	echo '<html><body>';
	echo trim($pdfcode);
	echo '</body></html>';
} else {
	$pdf->stream();
}

// FINE REPORT REGISTRO PRESENZE
mysqli_free_result($result);
unset($data);
}
// INIZIO REPORT PRENOTAZIONE PASTI

if (isset($_POST['report']) && $_POST['report'] == 2) {
	$giorno = $_POST['gio'];
	$Nome = $_SESSION['UserID'];
	$Pasto = $_POST['Pasto'];
	$Sede = $_POST['Sede'];
	$Formato_data =  date('d-m-Y', strtotime($giorno));
	
	if ($Pasto == 1) $Pranzo = " dei PRANZI prenotati ";
	if ($Pasto == 2) $Pranzo = " delle CENE prenotate ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI prenotate ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Elenco' . $Pranzo .'per il giorno ' . $Formato_data,12, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');

	
//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_uo.DEN_UN_OPER AS UO, pre_tiporazione.TipoRazione AS Razione, pre_sedi.Sede, IF(pre_accessi.Pagamento=1,'S','N')
			  AS Pag
			  FROM pre_accessi, pre_elenconomi, pre_uo, pre_tiporazione, pre_sedi, pre_utentixunita, pre_gradi
			  WHERE pre_accessi.GIORNO='$giorno' AND pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_uo.ID_UO=pre_elenconomi.UO AND 
			  pre_tiporazione.ID=pre_accessi.Ti_R AND pre_accessi.Se=pre_sedi.IDsede AND pre_accessi.PASTO='$Pasto' AND pre_utentixunita.ID_UO=pre_elenconomi.UO 
			  AND pre_utentixunita.IDnome='$Nome' AND pre_accessi.Se='$Sede' AND pre_gradi.ID=pre_elenconomi.IDgrado
			  ORDER BY pre_elenconomi.Cognome";

	
//--------------------------------------------------

//Query del conteggio pasti

	$query3 = "SELECT Count(pre_elenconomi.Categoria) AS NUMERO, pre_categorie.Categoria, 
			pre_tiporazione.TipoRazione AS Razione, pre_sedi.Sede, IF(pre_accessi.Pagamento=1,'S','N') AS Pag
			FROM ((((pre_accessi INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome) INNER JOIN
			pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID) INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede)
			INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat) INNER JOIN pre_utentixunita ON pre_elenconomi.UO = pre_utentixunita.ID_UO
			WHERE pre_accessi.GIORNO='$giorno' AND pre_accessi.PASTO='$Pasto' AND pre_utentixunita.IDnome='$Nome' AND pre_accessi.Se='$Sede'
			GROUP BY pre_categorie.Categoria, pre_accessi.GIORNO, pre_accessi.PASTO, pre_tiporazione.TipoRazione, pre_sedi.SEDE, pre_accessi.Pagamento, pre_categorie.IDcat
			ORDER BY pre_categorie.IDcat;"; 
//----------------------------------------------------
	

// do the SQL query
$result = $PRES_conn->query($query);
if ($result === false) {
    die("Error: " . mysqli_error($PRES_conn));
}
$row_result = mysqli_fetch_assoc($result);
$nrow_result = $result->num_rows;

$result3 = $PRES_conn->query($query3);
if ($result3 === false) {
    die("Error: " . mysqli_error($PRES_conn));
}
$row_result3 = mysqli_fetch_assoc($result3);
$nrow_result3 = $result3->num_rows;
	
	
// intestazione

//	$pdf->ezText($Reparto,18, array('justification'=>'center'));
//	$pdf->ezText('    ',18, 'center');
//	$pdf->ezText('Elenco' . $Pranzo .'per il giorno ' . $Formato_data, 16, array('justification'=>'center'));
//	$pdf->ezText(' ',18, 'center');
	

// Crea la tabella per i pranzi
if($nrow_result > 0){
$y = 700;
	$i = 0;
	$pdf->addText(20, $y+15, 8, '<b>N.</b>');
	$pdf->addText(45, $y+15, 8, '<b>Grado</b>');
	$pdf->addText(140, $y+15, 8, '<b>Cognome</b>');
	$pdf->addText(245, $y+15, 8, '<b>Nome</b>');
	$pdf->addText(355, $y+15, 8, '<b>UO</b>');
	$pdf->addText(455, $y+15, 8, '<b>Razione</b>');
	$pdf->addText(515, $y+15, 8, '<b>Sede</b>');
	$n = 1;
do {
	$pdf->addText(20, $y,  8, utf8_decode($n));
	$pdf->addText(45, $y,  8, utf8_decode($row_result['Grado']));
	$pdf->addText(140, $y,  8, utf8_decode($row_result['Cognome']));
	$pdf->addText(245, $y,  8, utf8_decode($row_result['Nome']));
	$pdf->addText(355, $y, 8, utf8_decode($row_result['UO']));
	$pdf->addText(455, $y, 8, utf8_decode($row_result['Razione']));
	$pdf->addText(515, $y, 8, utf8_decode($row_result['Sede']));
	$n++;
	$pdf->addText(20, $y + 14, 10, '___________________________________________________________________________________________________');

	$y=$y-15;
	if ($y < 40) {
		$y = 800;
		$pdf -> ezNewPage();
	}
		$i = $i + 1;

	} while($row_result = mysqli_fetch_assoc($result));

	
	//Crea la tabelle dei conteggi pranzo
	$y = $y - 25;
	$pdf->addText(152, $y, 12, '<b>DIMOSTRAZIONE NUMERICA PER CATEGORIA</b>');
	$y = $y - 15;
	$pdf->addText(150, $y, 8, '<b>Numero</b>');
	$pdf->addText(200, $y, 8, '<b>Categoria</b>');
	$pdf->addText(280, $y, 8, '<b>Tipo razione</b>');
	$pdf->addText(370, $y, 8, '<b>Sede</b>');


	$y = $y - 15;
	do {
		$pdf->addText(150, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['NUMERO']));
		$pdf->addText(200, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Categoria']));
		$pdf->addText(280, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Razione']));
		$pdf->addText(370, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Sede']));
		$pdf->addText(150, $y + 14, 10, '__________________________________________________');

		$y=$y-15;
		
		if ($y < 25) {
			$pdf->addText(450, 20, 8, "Pagina " .$i . ' data ' . $Formato_data);
			$i = $i+1;
			$y = 750;
			$pdf -> ezNewPage();
		}

	} while($row_result3 = mysqli_fetch_assoc($result3));

}	else {
	$pdf->addText(152, $y, 12, 'NESSUN DATO DA VISUALIZZARE');
}
		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
	
mysqli_free_result($result);
mysqli_free_result($result3);

}

// FINE STAMPE PRENOTAZIONE PASTI


if (isset($_POST['report']) && $_POST['report'] == 3) {

// INIZIO STAMPA CARTELLINO


//error_reporting(E_ALL);
include('class.ezpdf.php');
$pdf =new Cezpdf();
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezText($Reparto,12, array('justification'=>'center'));
$pdf->ezText('',10, 'center');
//--------------------------------------------------

$Nome = $_POST['IDnome'];
$query = "SELECT Grado, Cognome, Nome, CF
		  FROM pre_elenconomi
		  where IDnome='$Nome'";
//--------------------------------------------------

$data = array();
$result = $PRES_conn->query($query);
$Qry = mysqli_fetch_assoc($result);
$CF = "*" . $Qry['CF'] . "*";
$Nome = $Qry['Grado']." ".$Qry['Nome']." ".$Qry['Cognome'];
	$pdf->ezText($Nome,12, array('justification'=>'center'));
	$pdf->ezText('',12, 'center');
	$pdf->selectFont('../fonts/IDAutomationHC39M.afm');
	$pdf->ezText($CF,18, array('justification'=>'center'));
if (isset($d) && $d){
	$pdfcode = $pdf->output(1);
	$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
	echo '<html><body>';
	echo trim($pdfcode);
	echo '</body></html>';
} else {
	$pdf->stream();
}
unset($data);
mysqli_free_result($result);

}
// STAMPA ELENCO NOMINATIVO AMMINISTRATI
if (isset($_POST['report']) && $_POST['report'] == 4) {

//error_reporting(E_ALL);
include('class.ezpdf.php');
$pdf =new Cezpdf('A4','landscape');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezText($Reparto,12, array('justification'=>'center'));
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Elenco del personale amministrato',10, array('justification'=>'center'));
$pdf->ezText('',8, 'center');
//--------------------------------------------------

$Nome = $_POST['IDnome'];
$query = "SELECT pre_nomiview.Grado, pre_nomiview.Cognome, pre_nomiview.Nome, pre_uo.DEN_UN_OPER AS Un_operativa, pre_tiporazione.TipoRazione, pre_sedi.SEDE
FROM ((pre_nomiview INNER JOIN pre_uo ON pre_nomiview.UO = pre_uo.ID_UO) INNER JOIN pre_tiporazione ON pre_nomiview.TipoRaz = pre_tiporazione.ID) INNER JOIN pre_sedi ON pre_nomiview.Sede = pre_sedi.IDsede
WHERE (((pre_nomiview.ID_USERNAME)='$Nome'))
ORDER BY pre_nomiview.Cognome;";
//--------------------------------------------------

// initialize the array
	$data = array();
	
// do the SQL query
	$result = $PRES_conn->query($query);

// step through the result set, populating the array, note that this could
// while($data[] = mysqli_fetch_assoc($result)) {}
while($row = mysqli_fetch_assoc($result)) {
	$data[] = $row;
};

$pdf->ezTable($data,'' ,'', array('fontSize'=>'7', 'shaded'=>0, 'showLines'=>2));


if (isset($d) && $d){
	$pdfcode = $pdf->output(1);
	$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
	echo '<html><body>';
	echo trim($pdfcode);
	echo '</body></html>';
} else {
	$pdf->stream();
}
mysqli_free_result($result);
unset($data);
}

//Stampa liberatoria e richiesta invio busta paga per email.

if (isset($_POST['report']) && $_POST['report'] == 5) {

//error_reporting(E_ALL);
include('class.ezpdf.php');
$Nome = $_POST['IDnome'];
$query = "SELECT statini_invio.ID, statini_invio.COGNOME, statini_invio.NOME, statini_invio.CF, statini_invio.email
FROM statini_invio
WHERE statini_invio.ID='$Nome';";
//--------------------------------------------------

$pdf =new Cezpdf('a4','portrait');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezText('A: CENTRO ADDESTRAMENTO ALPINO',12, array('justification'=>'right'));
$pdf->ezText('   Servizio Amministrativo',12, array('justification'=>'right'));
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Oggetto: richieta di invio telematico dello statino stipendiale mensile.',10, array('justification'=>'left'));
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('',8, 'center');

//--------------------------------------------------

// initialize the array
	$data = array();
	
// do the SQL query
	$result = $PRES_conn->query($query);

// step through the result set, populating the array, note that this could
// while($data[] = mysqli_fetch_assoc($result)) {}
while($row = mysqli_fetch_assoc($result)) {
	$data[] = $row;
};

$pdf->ezTable($data,'','',array('shaded'=>0, 'showLines'=>2));

if (isset($d) && $d){
	$pdfcode = $pdf->output(1);
	$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
	echo '<html><body>';
	echo trim($pdfcode);
	echo '</body></html>';
} else {
	$pdf->stream();
}
mysqli_free_result($result);
unset($data);
}

if (isset($_POST['report']) && $_POST['report'] == 6) {

// INIZIO REPORT PRESENTI ASSENTI

//error_reporting(E_ALL);
include('class.ezpdf.php');
$pdf =new Cezpdf('a4','landscape');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezText($Reparto,20, array('justification'=>'center'));
$pdf->ezText('    ',18, 'center');
$pdf->ezText('Elenco degli orari di ingresso e uscita registrate nella giornata del ',10, array('justification'=>'center'));
$pdf->ezText(' ',10, 'center');
//--------------------------------------------------

$giorno = $_POST['dd'];
$mese = $_POST['mm'];
$anno = $_POST['aa'];
$Nome = $_POST['IDnome'];

$Data = $_POST['dd']."/". $_POST['mm'] ."/". $_POST['aa'];
$parGiorno = $_POST['aa']."-". $_POST['mm'] ."/". $_POST['dd'];

$query = "SELECT pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, date_format(pre_orari_gg.ORA_IN,'%H:%i') AS ING, 
		  date_format(pre_orari_gg.ORA_OUT,'%H:%i')AS USC, pre_orari_gg.CAUSA, date_format(pre_orari_gg.ORA_REG_IN,'%d/%m/%Y %H:%i') as ORA_REG_ING, 
		  date_format(pre_orari_gg.ORA_REG_OUT,'%d/%m/%Y %H:%i') as ORA_REG_USC, pre_elenconomi_1.Cognome AS MOD_INGR,
		  date_format(pre_orari_gg.ORA_MOD_IN,'%d/%m/%Y %H:%i') AS DATA_MOD_ING, pre_elenconomi_2.Cognome AS MOD_USC,
		  date_format(pre_orari_gg.ORA_MOD_OUT,'%d/%m/%Y %H:%i') AS DATA_MOD_USC
          FROM (((((pre_orari_gg INNER JOIN pre_elenconomi ON pre_orari_gg.IDnome = pre_elenconomi.IDnome) JOIN pre_gradi ON 
		  pre_elenconomi.IDgrado = pre_gradi.ID) LEFT JOIN pre_elenconomi AS pre_elenconomi_1 ON 
		  pre_orari_gg.ID_MOD_IN = pre_elenconomi_1.IDnome) LEFT JOIN pre_elenconomi AS pre_elenconomi_2 ON 
		  pre_orari_gg.ID_MOD_OUT = pre_elenconomi_2.IDnome) JOIN pre_utentixunita ON pre_elenconomi.UO = pre_utentixunita.ID_UO)
		  WHERE pre_orari_gg.GIORNO='$parGiorno' AND pre_utentixunita.IDnome='$Nome'
		  ORDER BY pre_elenconomi.Cognome, pre_orari_gg.ID;";
 
$data = array();
$result = $PRES_conn->query($query);
while($row = mysqli_fetch_assoc($result)) {
	$data[] = $row;
};

$pdf->ezText($Data, 10, array('justification'=>'center'));
$pdf->ezText(' ',10, 'center');

$pdf->ezTable($data,'' ,'', array('fontSize'=>'8', 'shaded'=>0, 'showLines'=>2));
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Aosta, l� '.date('d-m-Y'),10);
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('',10, 'center');
$pdf->ezText('Firma _____________________________',10, array('justification'=>'center'));

if (isset($d) && $d){
	$pdfcode = $pdf->output(1);
	$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
	echo '<html><body>';
	echo trim($pdfcode);
	echo '</body></html>';
} else {
	$pdf->stream();
}
mysqli_free_result($result);
unset($data);
}

// FINE REPORT PRESENTI E ASSENTI

// ******************************************* STAMPA REPORT CONSUMAZIONI ****************************************

if (isset($_POST['report']) && $_POST['report'] == "Report consumazioni") {
	$giorno = $_POST['gio'];
	$Nome = $IDuser;
	$Pasto = $_POST['Pasto'];
	$Sede = $_POST['Sede'];
	$Formato_data = date('d-m-Y', strtotime($giorno));
		
	if ($Pasto == 1) $Pranzo = " dei PRANZI consumati ";
	if ($Pasto == 2) $Pranzo = " delle CENE consumate ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI consumate ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');

	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('OGGETTO: Lista del personale fruitore' . $Pranzo .'il giorno ' . $Formato_data,10, array('justification'=>'left'));
	$pdf->ezText(' ',14, 'center');
	$pdf->ezText(iconv("UTF-8", "ISO-8859-1//IGNORE",'Il personale riportato di seguito rientra nella categoria dei militari aventi diritto al pasto gratuito a carico della A.D. e risulta essere presente nei documenti contabili relativi alla forza vettovagliata del Reparto.'), 10, array('justification'=>'left'));
	
	$pdf->ezText(' ',14, 'center');


//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT  pre_gradi.Grado,  pre_elenconomi.Cognome,  pre_elenconomi.Nome,  pre_uo.DEN_UN_OPER AS UO,  pre_tiporazione.TipoRazione AS Tipo_Razione,
  			  pre_sedi.SEDE AS Sede
			  FROM pre_accessi, pre_elenconomi, pre_uo, pre_tiporazione, pre_sedi, pre_gradi
			  WHERE pre_elenconomi.IDnome = pre_accessi.IDnome AND pre_uo.ID_UO = pre_elenconomi.UO AND pre_tiporazione.ID = pre_accessi.Ti_R AND
  			  pre_accessi.Se = pre_sedi.IDsede AND pre_gradi.ID = pre_elenconomi.IDgrado AND pre_accessi.GIORNO = '$giorno' AND pre_accessi.PASTO = '$Pasto' AND
  			  pre_accessi.Se = '$Sede' AND (pre_accessi.Pagamento = '0' OR  ISNULL(pre_accessi.Pagamento)) AND pre_accessi.Ora_cons_pr IS NOT NULL
			  ORDER BY UO, pre_tiporazione.ID, pre_elenconomi.Cognome";

	
//--------------------------------------------------
 
//Query del conteggio pasti

	$query3 = "SELECT  Count(pre_elenconomi.Categoria) AS NUMERO, pre_categorie.Categoria, pre_tiporazione.TipoRazione AS Razione, pre_sedi.SEDE, pre_elenconomi.FA
			   FROM (((pre_accessi INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome) INNER JOIN pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID)
  			   INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede) INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat
			   WHERE pre_accessi.GIORNO = '$giorno' AND pre_accessi.PASTO = '$Pasto' AND pre_accessi.Ora_cons_pr IS NOT NULL AND pre_accessi.Se = '$Sede' AND
  			   (pre_accessi.Pagamento = '0' OR ISNULL(pre_accessi.Pagamento))
			   GROUP BY pre_categorie.Categoria, pre_tiporazione.TipoRazione, pre_sedi.SEDE, pre_elenconomi.FA, pre_accessi.GIORNO, pre_accessi.PASTO, pre_categorie.IDcat
			   ORDER BY pre_categorie.IDcat"; 
//----------------------------------------------------

//Query del conteggio pasti per razione

	$query4 = "SELECT Count(pre_elenconomi.Categoria) AS N, pre_tiporazione.TipoRazione AS Razione
			   FROM (((pre_accessi INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome) INNER JOIN pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID)
			   INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede) INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat
			   WHERE pre_accessi.GIORNO = '$giorno' AND pre_accessi.PASTO = '$Pasto' AND pre_accessi.Se = '$Sede' AND (pre_accessi.Pagamento = '0' OR  ISNULL(pre_accessi.Pagamento)) AND
  pre_accessi.Ora_cons_pr IS NOT NULL
			   GROUP BY pre_tiporazione.TipoRazione, pre_accessi.GIORNO, pre_accessi.PASTO"; 
//----------------------------------------------------

// do the SQL query
$result = $PRES_conn->query($query);
$result3 = $PRES_conn->query($query3);
$result4 = $PRES_conn->query($query4);

$row_result = mysqli_fetch_assoc($result);
$row_result3 = mysqli_fetch_assoc($result3);
$row_result4 = mysqli_fetch_assoc($result4);

$nrow=$result->num_rows;
$nrow3=$result3->num_rows;
$nrow4=$result4->num_rows;
$i = 1;

if($nrow >0){

$pdf->ezText('Elenco nominativo ' . $Pranzo . ' per sede', 12, array('justification'=>'center'));
$pdf->ezText('',8, 'center');

// Crea la tabella per i pranzi
$y = 630;
$n = 1;

$pdf->addText(20, $y+15, 8, '<b>N.</b>');
$pdf->addText(45, $y+15, 8, '<b>Grado</b>');
$pdf->addText(140, $y+15, 8, '<b>Cognome</b>');
$pdf->addText(245, $y+15, 8, '<b>Nome</b>');
$pdf->addText(355, $y+15, 8, '<b>UO</b>');
$pdf->addText(455, $y+15, 8, '<b>Razione</b>');
$pdf->addText(515, $y+15, 8, '<b>Sede</b>');

do {
$pdf->addText(20, $y,  8, utf8_decode($n));	
$pdf->addText(45, $y,  8, utf8_decode($row_result['Grado']));
$pdf->addText(140, $y,  8, utf8_decode($row_result['Cognome']));
$pdf->addText(245, $y,  8, utf8_decode($row_result['Nome']));
$pdf->addText(355, $y, 8, utf8_decode($row_result['UO']));
$pdf->addText(455, $y, 8, utf8_decode($row_result['Tipo_Razione']));
$pdf->addText(515, $y, 8, utf8_decode($row_result['Sede']));
$pdf->addText(20, $y + 14, 10, '___________________________________________________________________________________________________');
$n = $n +1;
$y = $y - 15;

if ($y < 35) {
	$pdf->addText(470, 20, 8, "Pagina " .$i . ' data ' . $Formato_data);
	$i = $i + 1;
	$y = 800;
	$pdf -> ezNewPage();
}

} while($row_result = mysqli_fetch_assoc($result));

//Crea la tabelle dei conteggi pranzo
$y = $y - 15;
if ($y < 100) {
	$pdf->addText(450, 20, 8, "Pagina " .$i . ' data ' . $Formato_data);
	$i = $i+1;
	$y = 750;
	$pdf -> ezNewPage();
}

$pdf->addText(152, $y, 12, '<b>DIMOSTRAZIONE NUMERICA PER CATEGORIA</b>');
$y = $y - 15;
$pdf->addText(150, $y, 8, '<b>Numero</b>');
$pdf->addText(200, $y, 8, '<b>Categoria</b>');
$pdf->addText(280, $y, 8, '<b>Tipo razione</b>');
$pdf->addText(370, $y, 8, '<b>Sede</b>');


$y = $y - 15;
do {
$pdf->addText(150, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['NUMERO']));
$pdf->addText(200, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Categoria']));
$pdf->addText(280, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Razione']));
$pdf->addText(370, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['SEDE']));
$pdf->addText(150, $y + 14, 10, '__________________________________________________');

$y=$y-15;
if ($y < 25) {
	$pdf->addText(450, 20, 8, "Pagina " .$i . ' data ' . $Formato_data);
	$i = $i + 1;
	$y = 750;
	$pdf -> ezNewPage();
}

} while($row_result3 = mysqli_fetch_assoc($result3));

// Tabella totali per razione

$y = $y - 15;

$pdf->addText(200, $y, 12, '<b>CONTEGGIO PER TIPO RAZIONE</b>');
$y = $y - 15;

$pdf->addText(250, $y, 8, '<b>Numero</b>');
$pdf->addText(300, $y, 8, '<b>Tipo razione</b>');
$y = $y - 15;

do {
$pdf->addText(255, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result4['N']));
$pdf->addText(305, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result4['Razione']));

$pdf->addText(250, $y + 14, 10, '__________________');

$y=$y-45;
if ($y < 25) {
$y = 750;
$pdf -> ezNewPage();
}

} while($row_result4 = mysqli_fetch_assoc($result4));

}


$y = $pdf->ezText('', 10, array('justification'=>'left')); // Ottiene la posizione verticale corrente

$pdf->addText(50, $y, 10, iconv("UTF-8", "ISO-8859-1//IGNORE", "Per verifica e accettazione" ));
$pdf->addText(30, $y-15,  10, iconv("UTF-8", "ISO-8859-1//IGNORE", "Il rappresentante della ditta " . $Ditta));
$pdf->addText(320, $y,  10, iconv("UTF-8", "ISO-8859-1//IGNORE", "Il rappresentante della Amministrazione Militare"));
 $y=$y-25;
 $pdf->addText(30, $y,  10, "__________________________________");
$pdf->addText(320, $y,  10, "______________________________________");

if (isset($d) && $d){
$pdfcode = $pdf->output(1);
$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
echo '<html><body>';
echo trim($pdfcode);
echo '</body></html>';
} else {
$pdf->stream();
}

mysqli_free_result($result);
mysqli_free_result($result3);
mysqli_free_result($result4);

}

// ************************** FINE STAMPA PASTI CONSUMATI  ************************************

// ************************* REPORT STAMPE PASTI A PAGAMENTO **********************************

if (isset($_POST['report2']) && $_POST['report2'] == "Razioni a pagamento") {
	$giorno = $_POST['gio'];
	$Nome = $IDuser;
	$Pasto = $_POST['Pasto'];
	if (!isset($_POST['Sede'])) {
		die('The "Sede" index is not set in $_POST array.');
	}
	$Formato_data = date('d-m-Y', strtotime($giorno));
		
	if ($Pasto == 1) $Pranzo = " dei PRANZI a pagamento ";
	if ($Pasto == 2) $Pranzo = " delle CENE a pagamento ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI a pagamento ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('OGGETTO: Lista del personale che ha usufruito' . $Pranzo . 'il giorno ' . $Formato_data ,10, array('justification'=>'left'));
	$pdf->ezText(' ',18, 'center');
	
	$pdf->ezText(' ',18, 'center');
	
	
//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

$query = "SELECT
pre_gradi.Grado,
pre_elenconomi.Cognome,
pre_elenconomi.Nome,
pre_uo.DEN_UN_OPER AS UO,
pre_tiporazione.TipoRazione AS Razione,
pre_sedi.SEDE AS Sede,
IF(pre_accessi.Pagamento=1,'S','N') AS Pag
FROM
pre_accessi,
pre_elenconomi,
pre_uo,
pre_tiporazione,
pre_sedi,
pre_gradi
WHERE
pre_elenconomi.IDnome = pre_accessi.IDnome AND
pre_uo.ID_UO = pre_elenconomi.UO AND
pre_tiporazione.ID = pre_accessi.Ti_R AND
pre_accessi.Se = pre_sedi.IDsede AND
pre_gradi.ID = pre_elenconomi.IDgrado AND
pre_accessi.GIORNO = '$giorno' AND
pre_accessi.PASTO = '$Pasto' AND
pre_accessi.Se = '$Sede' AND
pre_accessi.Ora_cons_pr IS NOT NULL AND
pre_accessi.Pagamento = 1
ORDER BY
UO,
pre_elenconomi.Cognome";

//--------------------------------------------------
 
//Query del conteggio pasti

$query3 = "SELECT
Count(pre_elenconomi.Categoria) AS NUMERO,
pre_categorie.Categoria,
pre_tiporazione.TipoRazione AS Tipo_Razione,
pre_sedi.SEDE
FROM
(((pre_accessi
INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome)
INNER JOIN pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID)
INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede)
INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat
WHERE
pre_accessi.GIORNO = '$giorno' AND
pre_accessi.PASTO = '$Pasto' AND
pre_accessi.Ora_cons_pr IS NOT NULL AND
pre_accessi.Se = '$Sede' AND
pre_accessi.Pagamento = 1
GROUP BY
pre_categorie.Categoria,
pre_tiporazione.TipoRazione,
pre_sedi.SEDE,
pre_accessi.GIORNO,
pre_accessi.PASTO,
pre_categorie.IDcat
ORDER BY
pre_categorie.IDcat"; 
//----------------------------------------------------


	// do the SQL query
	$result = $PRES_conn->query($query);
if ($result === false) {
    die("Error: " . mysqli_error($PRES_conn));
}
	$result3 = $PRES_conn->query($query3);
	if ($result3 === false) {
		die("Error: " . mysqli_error($PRES_conn));
	}

	$row_result = mysqli_fetch_assoc($result);
	$nrow_result = $result->num_rows;
	$row_result3 = mysqli_fetch_assoc($result3);
	$nrow_result3 = $result3->num_rows;


//Crea la tabelle dei conteggi pranzo

	$pdf->ezText('Elenco nominativo' . $Pranzo . 'per sede', 12, array('justification'=>'center'));
	$pdf->ezText('',8, 'center');

	// Crea la tabella per i pranzi
$y = 620;
$n = 1;
if ($nrow_result > 0){

	$pdf->addText(20, $y+15, 8, '<b>N.</b>');
	$pdf->addText(45, $y+15, 8, '<b>Grado</b>');
	$pdf->addText(150, $y+15, 8, '<b>Cognome</b>');
	$pdf->addText(255, $y+15, 8, '<b>Nome</b>');
	$pdf->addText(375, $y+15, 8, '<b>UO</b>');
	$pdf->addText(475, $y+15, 8, '<b>Razione</b>');
	$pdf->addText(520, $y+15, 8, '<b>Sede</b>');

	
do {
	$pdf->addText(20, $y,  8, utf8_decode($n));
	$pdf->addText(45, $y,  8, utf8_decode($row_result['Grado']));
	$pdf->addText(150, $y,  8, utf8_decode($row_result['Cognome']));
	$pdf->addText(255, $y,  8, utf8_decode($row_result['Nome']));
	$pdf->addText(370, $y, 8, utf8_decode($row_result['UO']));
	$pdf->addText(480, $y, 8, utf8_decode($row_result['Razione']));
	$pdf->addText(900, $y, 8, utf8_decode($row_result['Sede']));
	$pdf->addText(20, $y + 14, 10, '___________________________________________________________________________________________________');
$y=$y-15;
$n = $n +1;
if ($y < 25) {
$y = 750;
$pdf -> ezNewPage();
}

} while($row_result = mysqli_fetch_assoc($result));

//Crea la tabelle dei conteggi pranzo
$y = $y - 15;
if ($y < 100) {
$y = 750;
$pdf -> ezNewPage();
}

$pdf->addText(220, $y, 12, '<b>DIMOSTRAZIONE NUMERICA</b>');
$y = $y - 15;
$pdf->addText(100, $y, 8, '<b>Numero</b>');
$pdf->addText(150, $y, 8, '<b>Categoria</b>');
$pdf->addText(230, $y, 8, '<b>Tipo razione</b>');
$pdf->addText(320, $y, 8, '<b>Sede</b>');
$pdf->addText(430, $y, 8, '<b>Pagamento</b>');

$y = $y - 15;
do {
$pdf->addText(110, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['NUMERO']));
$pdf->addText(160, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Categoria']));
$pdf->addText(230, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Tipo_Razione']));
$pdf->addText(310, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Sede']));
$pdf->addText(435, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Pag']));
$pdf->addText(100, $y + 14, 10, '_________________________________________________________________________');

$y=$y-15;
if ($y < 25) {
$y = 750;
$pdf -> ezNewPage();
}

} while($row_result3 = mysqli_fetch_assoc($result3));

// Tabella totali per razione

mysqli_free_result($result);
mysqli_free_result($result3);

$pdf->addText(50, $y, 10, iconv("UTF-8", "ISO-8859-1//IGNORE", "Per verifica e accettazione" ));
$pdf->addText(30, $y-15,  10, iconv("UTF-8", "ISO-8859-1//IGNORE", "Il rappresentante della ditta " . $Ditta));
$pdf->addText(320, $y,  10, iconv("UTF-8", "ISO-8859-1//IGNORE", "Il rappresentante della Amministrazione Militare"));
}
  		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}

}

//  ************************** FINE STAMPA CONSUMAZIONI A PAGAMENTO ****************************

// ******************************************* STAMPA REPORT PRENOTATI NON CONSUMATI ****************************************

if (isset($_POST['report1']) && $_POST['report1'] == "Prenotati non consumati") {
	$giorno = $_POST['gio'];
	$Nome = $IDuser;
	$Pasto = $_POST['Pasto'];
	$Sede = $_POST['Sede'];
	$Formato_data = date('d-m-Y', strtotime($giorno));
		
	if ($Pasto == 1) $Pranzo = " dei PRANZI prenotati e non consumati ";
	if ($Pasto == 2) $Pranzo = " delle CENE prenotate e non consumate ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI prenotate e non consumate ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('OGGETTO: Lista ' . $Pranzo .'il giorno ' . $Formato_data,10, array('justification'=>'left'));
	$pdf->ezText(' ',18, 'center');
	
//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT pre_gradi.Grado,pre_elenconomi.Cognome, pre_elenconomi.IDnome, pre_elenconomi.Nome, pre_uo.DEN_UN_OPER AS UO, pre_tiporazione.TipoRazione AS Razione, pre_sedi.Sede AS SEDE, IF(pre_accessi.Pagamento=1,'S','N') AS Pag
			  FROM pre_accessi, pre_elenconomi, pre_uo, pre_tiporazione, pre_sedi, pre_gradi
			  WHERE pre_accessi.GIORNO='$giorno' AND pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_uo.ID_UO=pre_elenconomi.UO AND 
			  pre_tiporazione.ID=pre_accessi.Ti_R AND pre_accessi.Se=pre_sedi.IDsede AND pre_accessi.PASTO='$Pasto' AND pre_gradi.ID=pre_elenconomi.IDgrado
			  AND pre_accessi.Se='$Sede' AND isnull(pre_accessi.Ora_cons_pr)
			  ORDER BY pre_uo.DEN_UN_OPER, pre_elenconomi.Cognome";

	
//--------------------------------------------------
 

//Query del conteggio pasti

	$query3 = "SELECT Count(pre_elenconomi.Categoria) AS NUMERO, pre_categorie.Categoria, 
			pre_tiporazione.TipoRazione AS Tipo_Razione, pre_sedi.Sede
			FROM (((pre_accessi INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome) INNER JOIN
			pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID) INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede)
			INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat
			WHERE pre_accessi.GIORNO='$giorno' AND pre_accessi.PASTO='$Pasto' AND ISNULL(pre_accessi.Ora_cons_pr) AND pre_accessi.Se='$Sede'
			GROUP BY pre_categorie.Categoria, pre_accessi.GIORNO, pre_accessi.PASTO, pre_tiporazione.TipoRazione, pre_sedi.SEDE"; 
//----------------------------------------------------


// do the SQL query
$result = $PRES_conn->query($query);
$result3 = $PRES_conn->query($query3);


$row_result = mysqli_fetch_assoc($result);
$row_result3 = mysqli_fetch_assoc($result3);

$nrow = $result->num_rows;
$nrow3 = $result3->num_rows;

	
$pdf->ezText('Elenco nominativo ' . $Pranzo . ' per sede', 12, array('justification'=>'center'));
$pdf->ezText('',8, 'center');

// Crea la tabella per i pranzi
$y = 680;

if($nrow>0){

	$pdf->addText(20, $y+15, 8, '<b>N.</b>');
	$pdf->addText(45, $y+15, 8, '<b>Grado</b>');
	$pdf->addText(140, $y+15, 8, '<b>Cognome</b>');
	$pdf->addText(245, $y+15, 8, '<b>Nome</b>');
	$pdf->addText(355, $y+15, 8, '<b>UO</b>');
	$pdf->addText(455, $y+15, 8, '<b>Razione</b>');
	$pdf->addText(515, $y+15, 8, '<b>IDnome</b>');
	$n = 1;
do {
	$pdf->addText(20, $y,  8, utf8_decode($n));
	$pdf->addText(45, $y,  8, utf8_decode($row_result['Grado']));
	$pdf->addText(140, $y,  8, utf8_decode($row_result['Cognome']));
	$pdf->addText(245, $y,  8, utf8_decode($row_result['Nome']));
	$pdf->addText(355, $y, 8, utf8_decode($row_result['UO']));
	$pdf->addText(455, $y, 8, utf8_decode($row_result['Razione']));
	$pdf->addText(515, $y, 8, utf8_decode($row_result['IDnome']));
	$n++;
$pdf->addText(20, $y + 14, 10, '___________________________________________________________________________________________________');
$y=$y-15;
if ($y < 25) {
$y = 750;
$pdf -> ezNewPage();
}

} while($row_result = mysqli_fetch_assoc($result));
}

//Crea la tabelle dei conteggi pranzo
$y = $y - 15;
if ($y < 100) {
$y = 750;
$pdf -> ezNewPage();
}
if($nrow3 > 0){
$pdf->addText(220, $y, 12, '<b>DIMOSTRAZIONE NUMERICA</b>');
$y = $y - 15;
$pdf->addText(100, $y, 8, '<b>Numero</b>');
$pdf->addText(150, $y, 8, '<b>Categoria</b>');
$pdf->addText(300, $y, 8, '<b>Razione</b>');
$pdf->addText(450, $y, 8, '<b>Sede</b>');


$y = $y - 15;
do {
$pdf->addText(110, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['NUMERO']));
$pdf->addText(160, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Categoria']));
$pdf->addText(300, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Tipo_Razione']));
$pdf->addText(440, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Sede']));

$pdf->addText(100, $y + 14, 10, '_________________________________________________________________________');

$y=$y-15;
if ($y < 25) {
	$y = 750;
	$pdf -> ezNewPage();
}

} while($row_result3 = mysqli_fetch_assoc($result3));
} else {

	$pdf->addText(220, $y, 12, '<b>NON CI SONO DATI DA STAMPARE</b>');
}		
		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
		mysqli_free_result($result);
		mysqli_free_result($result3);
		unset($data);
		unset($data3);
} 


// *************************** FINE STAMPA PASTI CONSUMATI  ************************************

// *************************** INIZIO REPORT PRENOTAZIONE PASTI  *******************************

if (isset($_POST['report']) && $_POST['report'] == 8) {
	$giorno = $_POST['gio'];
	$Pasto = $_POST['Pasto'];
	$Sede = $_POST['Sede'];
	$Formato_data =  date('d-m-Y', strtotime($giorno));
	
	if ($Pasto == 1) $Pranzo = " dei PRANZI prenotati ";
	if ($Pasto == 2) $Pranzo = " delle CENE prenotate ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI prenotate ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Dettagli delle prenotazioni ' . $Pranzo .'per il giorno ' . $Formato_data,12, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');
//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_uo.DEN_UN_OPER AS UO, pre_tiporazione.TipoRazione AS Razione, 
			  pre_sedi.Sede, IF(pre_accessi.Pagamento=1,'S','N') AS Pag
			  FROM pre_accessi, pre_elenconomi, pre_uo, pre_tiporazione, pre_sedi, pre_gradi
			  WHERE pre_accessi.GIORNO='$giorno' AND pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_uo.ID_UO=pre_elenconomi.UO AND 
			  pre_tiporazione.ID=pre_accessi.Ti_R AND pre_accessi.Se=pre_sedi.IDsede AND pre_accessi.PASTO='$Pasto' 
			  AND pre_accessi.Se='$Sede' AND pre_gradi.ID=pre_elenconomi.IDgrado
			  ORDER BY pre_elenconomi.Cognome";

	
//--------------------------------------------------
 
//Query del conteggio pasti per razione

	$query4 = "SELECT Count(pre_elenconomi.Categoria) AS N, pre_tiporazione.TipoRazione AS Razione
			   FROM (((pre_accessi INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome) INNER JOIN pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID)
			   INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede) INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat
			   WHERE pre_accessi.GIORNO = '$giorno' AND pre_accessi.PASTO = '$Pasto' AND pre_accessi.Se = '$Sede' AND (pre_accessi.Pagamento = '0' OR  ISNULL(pre_accessi.Pagamento)) AND
 			   pre_accessi.Ora_cons_pr IS NOT NULL
			   GROUP BY pre_tiporazione.TipoRazione, pre_accessi.GIORNO, pre_accessi.PASTO"; 
//----------------------------------------------------

//Query del conteggio pasti

	$query3 = "SELECT Count(pre_elenconomi.Categoria) AS NUMERO, pre_categorie.Categoria, 
			pre_tiporazione.TipoRazione AS Tipo_Razione, pre_sedi.Sede, IF(pre_accessi.Pagamento=1,'S','N') AS Pag
			FROM (((pre_accessi INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome) INNER JOIN
			pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID) INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede)
			INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat
			WHERE pre_accessi.GIORNO='$giorno' AND pre_accessi.PASTO='$Pasto' AND pre_accessi.Se='$Sede'
			GROUP BY pre_categorie.Categoria, pre_accessi.GIORNO, pre_accessi.PASTO, pre_tiporazione.TipoRazione, pre_sedi.SEDE, pre_accessi.Pagamento, pre_categorie.IDcat
			ORDER BY pre_categorie.IDcat"; 
//----------------------------------------------------


	// do the SQL query
	$result = $PRES_conn->query($query);
	if ($result === false) {
		die('Error: '. mysqli_error($PRES_conn));
	}
	$result3 = $PRES_conn->query($query3);
	if ($result3 === false) {
		die('Error: '. mysqli_error($PRES_conn));
	}
	$result4 = $PRES_conn->query($query4);

	$row_result = mysqli_fetch_assoc($result);
	$row_result3 = mysqli_fetch_assoc($result3);
	$row_result4 = mysqli_fetch_assoc($result4);

	$nrow= $result->num_rows;
	$nrow3= $result3->num_rows;
	$nrow4= $result4->num_rows;
	
	// Intestazione report

	//$pdf->ezText($Reparto,18, array('justification'=>'center', 'shaded'=>0, 'showLines'=>2));
	//$pdf->ezText('    ',18, 'center');


//Crea la tabelle dei conteggi pranzo

	//$pdf->ezText('Elenco nominativo ' . $Pranzo . ' per sede', 12, array('justification'=>'center'));
	//$pdf->ezText('',8, 'center');
if($nrow > 0) {
	// Crea la tabella per i pranzi
$y = 700;

$pdf->addText(20, $y+15, 8, '<b>Grado</b>');
$pdf->addText(110, $y+15, 8, '<b>Cognome</b>');
$pdf->addText(210, $y+15, 8, '<b>Nome</b>');
$pdf->addText(300, $y+15, 8, '<b>UO</b>');
$pdf->addText(440, $y+15, 8, '<b>Razione</b>');
$pdf->addText(475, $y+15, 8, '<b>Pag.</b>');
$pdf->addText(515, $y+15, 8, '<b>Sede</b>');

do {
	$pdf->addText(20, $y,  8, utf8_decode($row_result['Grado']));
	$pdf->addText(110, $y,  8, utf8_decode($row_result['Cognome']));
	$pdf->addText(210, $y,  8, utf8_decode($row_result['Nome']));
	$pdf->addText(300, $y, 8, utf8_decode($row_result['UO']));
	$pdf->addText(440, $y, 8, utf8_decode($row_result['Razione']));
	$pdf->addText(475, $y, 8, utf8_decode($row_result['Pag']));
	$pdf->addText(515, $y, 8, utf8_decode($row_result['Sede']));
	$pdf->addText(20, $y + 14, 10, '___________________________________________________________________________________________________');
$y=$y-15;
if ($y < 25) {
$y = 750;
$pdf -> ezNewPage();
}

} while($row_result = mysqli_fetch_assoc($result));
}

if($nrow3 > 0){
//Crea la tabelle dei conteggi pranzo
$y = $y - 15;
if ($y < 100) {
$y = 750;
$pdf -> ezNewPage();
}

$pdf->addText(220, $y, 12, '<b>DIMOSTRAZIONE NUMERICA</b>');
$y = $y - 15;
$pdf->addText(100, $y, 8, '<b>Numero</b>');
$pdf->addText(150, $y, 8, '<b>Categoria</b>');
$pdf->addText(230, $y, 8, '<b>Tipo razione</b>');
$pdf->addText(320, $y, 8, '<b>Sede</b>');
$pdf->addText(440, $y, 8, '<b>Pagamento</b>');

$y = $y - 15;
do {
$pdf->addText(110, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['NUMERO']));
$pdf->addText(160, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Categoria']));
$pdf->addText(230, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Tipo_Razione']));
$pdf->addText(310, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Sede']));
$pdf->addText(435, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Pag']));
$pdf->addText(100, $y + 14, 10, '_________________________________________________________________________');

$y=$y-15;
if ($y < 25) {
$y = 750;
$pdf -> ezNewPage();
}

} while($row_result3 = mysqli_fetch_assoc($result3));

}
// Tabella totali per razione
if($nrow4 > 0){
$y = $y - 15;

$pdf->addText(220, $y, 12, '<b>CONTEGGIO PER RAZIONI</b>');
$y = $y - 15;

$pdf->addText(200, $y, 8, '<b>Numero</b>');
$pdf->addText(330, $y, 8, '<b>Tipo razione</b>');
$y = $y - 15;

do {
$pdf->addText(210, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result4['N']));
$pdf->addText(300, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result4['Razione']));

$pdf->addText(200, $y + 14, 10, '_________________________________');

$y=$y-15;
if ($y < 25) {
$y = 750;
$pdf -> ezNewPage();
}

} while($row_result4 = mysqli_fetch_assoc($result4));
}
if (isset($d) && $d){
	$pdfcode = $pdf->output(1);
	$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
	echo '<html><body>';
	echo trim($pdfcode);
	echo '</body></html>';
} else {
	$pdf->stream();
}

mysqli_free_result($result);
mysqli_free_result($result3);

	
}

// ****************************      INIZIO REPORT DIMOSTRAZIONE NUMERICA PASTI   *********************************

if (isset($_POST['report3']) && $_POST['report3'] == "Dimostrazione numerica") {
	$giorno = $_POST['gio'];
	$Pasto = $_POST['Pasto'];
	$Sede = $_POST['Sede'];
	$Formato_data =  date('d-m-Y', strtotime($giorno));
	
	if ($Pasto == 1) $Pranzo = " dei PRANZI prenotati e consumati ";
	if ($Pasto == 2) $Pranzo = " delle CENE prenotate e consumate ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI prenotate e consumate ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Dimostrazione numerica ' . $Pranzo .'nel giorno ' . $Formato_data . ' presso le mense di servizio.',12, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');

//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

$query = "SELECT riepilogomensa.SEDE, riepilogomensa.DEN_UN_OPER as REPARTO, riepilogomensa.TipoRazione AS RAZIONE, riepilogomensa.FA AS FA, 
			  riepilogomensa.Categoria AS CAT, riepilogomensa.Prenotati AS PREN, riepilogomensa.Consumati AS CONS, IF(riepilogomensa.Pagamento=1,'S','N') AS Pag 
			  FROM riepilogomensa 
			  WHERE riepilogomensa.GIORNO='$giorno' AND riepilogomensa.PASTO='$Pasto'
			  GROUP BY riepilogomensa.SEDE, riepilogomensa.DEN_UN_OPER, riepilogomensa.FA, riepilogomensa.TipoRazione, riepilogomensa.Categoria, riepilogomensa.Prenotati, riepilogomensa.Consumati, riepilogomensa.Pagamento, riepilogomensa.GIORNO, riepilogomensa.PASTO, riepilogomensa.IDcat
			  ORDER BY riepilogomensa.SEDE, riepilogomensa.DEN_UN_OPER, riepilogomensa.IDcat;";

//-----------------------------------------------------


// initialize the array
	$data = array();
// do the SQL query
$result = $PRES_conn->query($query);
if ($result === false) {
    die("Error: " . mysqli_error($PRES_conn));
}

// step through the result set, populating the array, note that this could
// while($data[] = mysqli_fetch_assoc($result)) {}
while($row = mysqli_fetch_assoc($result)) {
	$data[] = $row;
};
		
// Crea la tabella per i pranzi
	$pdf->ezTable($data,'' ,'', array('fontSize'=>'10', 'shaded'=>0, 'showLines'=>2));
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');

		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
mysqli_free_result($result);
unset($data);
}

// *************************** INIZIO REPORT RAZIONI K  *******************************

if (isset($_POST['report']) && $_POST['report'] == 11) {
	$giorno = $_POST['gio'];
	$Pasto = $_POST['Pasto'];
	$Sede = $_POST['Sede'];
	$UO = $_POST['UO'];
	$Formato_data =  date('d-m-Y', strtotime($giorno));
	
	if ($Pasto == 1) $Pranzo = " dei PRANZI prenotati ";
	if ($Pasto == 2) $Pranzo = " delle CENE prenotate ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI prenotate ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','landscape');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Dettaglio ' . $Pranzo .'per il giorno ' . $Formato_data,12, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');

//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_uo.DEN_UN_OPER AS UO, pre_tiporazione.TipoRazione AS Razione, pre_sedi.Sede, IF(pre_accessi.Pagamento=1,'S','N') AS Pag, Users.Cognome as Utente_pren, date_format(pre_accessi.Ora_pren,'%d/%m/%Y %H:%i') as GG_Ora_pren
			  FROM pre_accessi, pre_elenconomi, pre_uo, pre_tiporazione, pre_sedi, pre_gradi, pre_elenconomi as Users
			  WHERE pre_accessi.GIORNO='$giorno' AND pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_uo.ID_UO=pre_elenconomi.UO AND 
			  pre_tiporazione.ID=pre_accessi.Ti_R AND pre_accessi.Se=pre_sedi.IDsede AND pre_accessi.PASTO='$Pasto' AND pre_elenconomi.UO = '$UO'
			  AND pre_accessi.Se='$Sede' AND pre_gradi.ID=pre_elenconomi.IDgrado AND pre_accessi.USR = Users.IDnome
			  ORDER BY pre_elenconomi.Cognome";
	
//--------------------------------------------------
 
//Query del conteggio pasti

	$query3 = "SELECT Count(pre_elenconomi.Categoria) AS NUMERO, pre_categorie.Categoria, 
			pre_tiporazione.TipoRazione AS Tipo_Razione, pre_sedi.Sede, IF(pre_accessi.Pagamento=1,'S','N') AS Pag
			FROM (((pre_accessi INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome) INNER JOIN
			pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID) INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede)
			INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat
			WHERE pre_accessi.GIORNO='$giorno' AND pre_accessi.PASTO='$Pasto' AND pre_accessi.Se='$Sede' AND pre_elenconomi.UO = '$UO'
			GROUP BY pre_categorie.Categoria, pre_accessi.GIORNO, pre_accessi.PASTO, pre_tiporazione.TipoRazione, pre_sedi.SEDE, pre_accessi.Pagamento
			ORDER BY pre_categorie.IDcat;"; 
//----------------------------------------------------

	
// initialize the array
	$result = $PRES_conn->query($query);
	if ($result === false) {
		die('Error: '. mysqli_error($PRES_conn));
	}
	$result3 = $PRES_conn->query($query3);
	if ($result3 === false) {
		die('Error: '. mysqli_error($PRES_conn));
	}

	$row_result = mysqli_fetch_assoc($result);
	$row_result3 = mysqli_fetch_assoc($result3);
	

// Crea la tabella per i pranzi
$y = 470;

$pdf->addText(20, $y+15, 8, '<b>Grado</b>');
$pdf->addText(110, $y+15, 8, '<b>Cognome</b>');
$pdf->addText(210, $y+15, 8, '<b>Nome</b>');
		$pdf->addText(250, $y+15, 8, '<b>UO</b>');
		$pdf->addText(400, $y+15, 8, '<b>Razione</b>');
		$pdf->addText(500, $y+15, 8, '<b>Sede</b>');
		$pdf->addText(600, $y+15, 8, '<b>Prenotato da</b>');
		$pdf->addText(710, $y+15, 8, '<b>data/hh</b>');
		
	do {
		$pdf->addText(45, $y,  8, utf8_decode($row_result['Grado']));
		$pdf->addText(150, $y,  8, utf8_decode($row_result['Cognome']));
		$pdf->addText(255, $y,  8, utf8_decode($row_result['Nome']));
			$pdf->addText(250, $y, 8, utf8_decode($row_result['UO']));
			$pdf->addText(400, $y, 8, utf8_decode($row_result['Razione']));
			$pdf->addText(500, $y, 8, utf8_decode($row_result['Sede']));
			$pdf->addText(590, $y, 8, utf8_decode($row_result['User']));
			$pdf->addText(690, $y, 8, $row_result['OraPren']);
			$pdf->addText(20, $y + 14, 10, '________________________________________________________________________________________________________________________________________');
		$y=$y-15;
	if ($y < 25) {
		$y = 580;
		$pdf -> ezNewPage();
	}

	} while($row_result = mysqli_fetch_assoc($result));
	
//Crea la tabelle dei conteggi pranzo
$y = $y - 15;
if ($y < 100) {
	$y = 550;
	$pdf -> ezNewPage();
}

	$pdf->addText(310, $y, 12, '<b>DIMOSTRAZIONE NUMERICA</b>');
	$y = $y - 15;
	$pdf->addText(200, $y, 8, '<b>Numero</b>');
	$pdf->addText(250, $y, 8, '<b>Categoria</b>');
	$pdf->addText(350, $y, 8, '<b>Tipo razione</b>');
	$pdf->addText(440, $y, 8, '<b>Sede</b>');
	$pdf->addText(550, $y, 8, '<b>Pagamento</b>');
	
	$y = $y - 15;
do {
		$pdf->addText(200, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['NUMERO']));
		$pdf->addText(250, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Categoria']));
		$pdf->addText(350, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Tipo_Razione']));
		$pdf->addText(440, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Sede']));
		$pdf->addText(550, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Pag']));
		$pdf->addText(200, $y + 14, 10, '_________________________________________________________________________');
	
	$y=$y-15;
	if ($y < 25) {
		$y = 580;
		$pdf -> ezNewPage();
	}
	
} while($row_result3 = mysqli_fetch_assoc($result3));
	
		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
		
		mysqli_free_result($result);
		mysqli_free_result($result3);
}

// *************************** INIZIO REPORT PRENOTAZIONE PASTI  *******************************

if (isset($_POST['report']) && $_POST['report'] == 10) {
	$giorno = $_POST['gio'];
	$Pasto = $_POST['Pasto'];
	$Sede = $_POST['Sede'];
	$Formato_data =  date('d-m-Y', strtotime($giorno));
	
	if ($Pasto == 1) $Pranzo = " dei PRANZI prenotati ";
	if ($Pasto == 2) $Pranzo = " delle CENE prenotate ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI prenotate ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','landscape');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Dettaglio ' . $Pranzo .'per il giorno ' . $Formato_data,12, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');
	

//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_uo.DEN_UN_OPER AS UO, pre_tiporazione.TipoRazione AS Razione,
			  pre_sedi.Sede, IF(pre_accessi.Pagamento=1,'S','N') AS Pag, Users.Cognome AS User, 
			  date_format(pre_accessi.Ora_pren,'%d/%m/%Y %H:%i') as OraPren
			  FROM pre_accessi, pre_elenconomi, pre_uo, pre_tiporazione, pre_sedi, pre_gradi, pre_elenconomi as Users
			  WHERE pre_accessi.GIORNO='$giorno' AND pre_elenconomi.IDnome=pre_accessi.IDnome AND pre_uo.ID_UO=pre_elenconomi.UO AND 
			  pre_tiporazione.ID=pre_accessi.Ti_R AND pre_accessi.Se=pre_sedi.IDsede AND pre_accessi.PASTO='$Pasto' 
			  AND pre_accessi.Se='$Sede' AND pre_gradi.ID=pre_elenconomi.IDgrado AND pre_accessi.USR = Users.IDnome
			  ORDER BY pre_elenconomi.Cognome";
	
//--------------------------------------------------
 
//Query del conteggio pasti

	$query3 = "SELECT Count(pre_elenconomi.Categoria) AS NUMERO, pre_categorie.Categoria, 
			pre_tiporazione.TipoRazione AS Tipo_Razione, pre_sedi.Sede, IF(pre_accessi.Pagamento=1,'S','N') AS Pag
			FROM (((pre_accessi INNER JOIN pre_elenconomi ON pre_accessi.IDnome = pre_elenconomi.IDnome) INNER JOIN
			pre_tiporazione ON pre_accessi.Ti_R = pre_tiporazione.ID) INNER JOIN pre_sedi ON pre_accessi.Se = pre_sedi.IDsede)
			INNER JOIN pre_categorie ON pre_elenconomi.Categoria = pre_categorie.IDcat
			WHERE pre_accessi.GIORNO='$giorno' AND pre_accessi.PASTO='$Pasto' AND pre_accessi.Se='$Sede'
			GROUP BY pre_categorie.Categoria, pre_accessi.GIORNO, pre_accessi.PASTO, pre_tiporazione.TipoRazione, pre_sedi.SEDE, pre_accessi.Pagamento, pre_categorie.IDcat
			ORDER BY pre_categorie.IDcat;"; 
//----------------------------------------------------


	// do the SQL query
	$result = $PRES_conn->query($query);
	if ($result === false) {
		die('Error: '. mysqli_error($PRES_conn));
	}
	
	$result3 = $PRES_conn->query($query3);
	if ($result3 === false) {
		die('Error: '. mysqli_error($PRES_conn));
	}
	

	$row_result = mysqli_fetch_assoc($result);
	$row_result3 = mysqli_fetch_assoc($result3);

	$nrow = $result->num_rows;
	$nrow3 = $result3->num_rows;


	if($nrow > 0){
// Crea la tabella per i pranzi
$y = 470;
        
		$pdf->addText(20, $y+15, 8, '<b>Grado</b>');
		$pdf->addText(100, $y+15, 8, '<b>Cognome</b>');
		$pdf->addText(210, $y+15, 8, '<b>Nome</b>');
		$pdf->addText(280, $y+15, 8, '<b>UO</b>');
		$pdf->addText(450, $y+15, 8, '<b>Razione</b>');
		$pdf->addText(500, $y+15, 8, '<b>Sede</b>');
		$pdf->addText(580, $y+15, 8, '<b>Pag.</b>');
		$pdf->addText(620, $y+15, 8, '<b>Prenotato da</b>');
		$pdf->addText(720, $y+15, 8, '<b>data/hh</b>');
		
	do {
		    
			$pdf->addText(20, $y,  8, utf8_decode($row_result['Grado']));
			$pdf->addText(100, $y,  8, utf8_decode($row_result['Cognome']));
			$pdf->addText(210, $y,  8, utf8_decode($row_result['Nome']));
			$pdf->addText(280, $y, 8, utf8_decode($row_result['UO']));
			$pdf->addText(450, $y, 8, utf8_decode($row_result['Razione']));
			$pdf->addText(500, $y, 8, utf8_decode($row_result['Sede']));
			$pdf->addText(580, $y, 8, utf8_decode($row_result['Pag']));
			$pdf->addText(620, $y, 8, utf8_decode($row_result['User']));
			$pdf->addText(710, $y, 8, $row_result['OraPren']);
			$pdf->addText(20, $y + 14, 10, '________________________________________________________________________________________________________________________________________');
		$y=$y-15;
	if ($y < 25) {
		$y = 580;
		$pdf -> ezNewPage();
	}

	} while($row_result = mysqli_fetch_assoc($result));
	
}

if($nrow3 > 0){
//Crea la tabelle dei conteggi pranzo
$y = $y - 15;
if ($y < 100) {
	$y = 550;
	$pdf -> ezNewPage();
}

	$pdf->addText(310, $y, 12, '<b>DIMOSTRAZIONE NUMERICA</b>');
	$y = $y - 15;
	$pdf->addText(200, $y, 8, '<b>Numero</b>');
	$pdf->addText(250, $y, 8, '<b>Categoria</b>');
	$pdf->addText(350, $y, 8, '<b>Tipo razione</b>');
	$pdf->addText(440, $y, 8, '<b>Sede</b>');
	$pdf->addText(550, $y, 8, '<b>Pagamento</b>');
	
	$y = $y - 15;
do {
		$pdf->addText(200, $y,  8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['NUMERO']));
		$pdf->addText(250, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Categoria']));
		$pdf->addText(350, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Tipo_Razione']));
		$pdf->addText(440, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Sede']));
		$pdf->addText(550, $y, 8, iconv("UTF-8", "ISO-8859-1//IGNORE", $row_result3['Pag']));
		$pdf->addText(200, $y + 14, 10, '_________________________________________________________________________');
	
	$y=$y-15;
	if ($y < 25) {
		$y = 580;
		$pdf -> ezNewPage();
	}
	
} while($row_result3 = mysqli_fetch_assoc($result3));
}
		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
		
		mysqli_free_result($result);
		mysqli_free_result($result3);
}

// ****************************      INIZIO REPORT DIMOSTRAZIONE NUMERICA PASTI PER PERIODO   *********************************

if (isset($_POST['ReportRic1']) && $_POST['ReportRic1'] == "Conteggio razioni distribuite") {
	if (isset($_POST['sto_dal'])) {$giornoda = $_POST['sto_dal']; $Formato_data_da =  date('d-m-Y', strtotime($giornoda));};
	if (isset($_POST['sto_al'])) {$giornoa = $_POST['sto_al']; $Formato_data_a =  date('d-m-Y', strtotime($giornoa)); };	
	if (isset($_POST['Pasto'])) {$Pasto = $_POST['Pasto']; };	
	if (isset($_POST['Sede'])) {$Sede = $_POST['Sede']; };	
	
	
	if ($Pasto == 1) $Pranzo = " dei PRANZI prenotati e consumati ";
	if ($Pasto == 2) $Pranzo = " delle CENE prenotate e consumate ";
	if ($Pasto == 3) $Pranzo = " delle COLAZIONI prenotate e consumate ";
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Dimostrazione numerica ' . $Pranzo .'nel periodo',12, array('justification'=>'center'));
	$pdf->ezText('dal ' . $Formato_data_da . " al " . $Formato_data_a . ' presso le mense di servizio.',12, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');

//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT riepilogomensafa.SEDE,riepilogomensafa.DEN_UN_OPER as REPARTO, riepilogomensafa.TipoRazione AS RAZIONE, 
			  riepilogomensafa.Categoria AS CAT, SUM(riepilogomensafa.Prenotati) AS PREN, SUM(riepilogomensafa.Consumati) AS CONS, 
			  IF(riepilogomensafa.Pagamento=1,'S','N') AS Pag FROM riepilogomensafa			   
			  WHERE riepilogomensafa.GIORNO >= '$giornoda' AND riepilogomensafa.GIORNO <= '$giornoa' AND riepilogomensafa.PASTO='$Pasto'
			  GROUP BY riepilogomensafa.SEDE,riepilogomensafa.DEN_UN_OPER, riepilogomensafa.TipoRazione, riepilogomensafa.Pagamento, riepilogomensafa.PASTO, riepilogomensafa.Categoria
			  ORDER BY riepilogomensafa.SEDE, riepilogomensafa.DEN_UN_OPER";
			  
// initialize the array
	$data = [];
// do the SQL query
$result = $PRES_conn->query($query);
if ($result === false) {
    die("Error: " . mysqli_error($PRES_conn));
}
while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
		
// Crea la tabella per i pranzi
	$pdf->ezTable($data,'' ,'', array('fontSize'=>'10', 'shaded'=>0, 'showLines'=>2));
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');

		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
		if (is_object($result) && get_class($result) == 'mysqli_result') {
			mysqli_free_result($result);
		}
unset($data);
}

// ****************************  INIZIO REPORT DIMOSTRAZIONE NUMERICA PASTI PER PERIODO IN BASE ALLA FFAA  *********************************

if (isset($_POST['ConteggioFFAA']) && $_POST['ConteggioFFAA'] == "Conteggio consumazioni per FFAA") {
if (isset($_POST['sto_dal'])) {$giornoda = $_POST['sto_dal']; };
if (isset($_POST['sto_al'])) {$giornoa = $_POST['sto_al']; };	
if (isset($_POST['FA'])) {$FA = $_POST['FA']; };	
	
	$Formato_data_da =  date('d-m-Y', strtotime($giornoda));
	$Formato_data_a =  date('d-m-Y', strtotime($giornoa));
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Dimostrazione numerica per FF.AA. nel periodo',12, array('justification'=>'center'));
	$pdf->ezText('dal ' . $Formato_data_da . " al " . $Formato_data_a . ' presso le mense di servizio.',12, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');

//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT conteggioconsumazionifa.SEDE, conteggioconsumazionifa.DEN_UN_OPER AS REPARTO, conteggioconsumazionifa.TipoRazione AS RAZIONE, conteggioconsumazionifa.Categoria AS CAT,
			  Sum(conteggioconsumazionifa.ConteggioDiConteggioDiIDrecord) AS CONS, conteggioconsumazionifa.FA, If(conteggioconsumazionifa.PASTO = 1, 'PRANZO', If(conteggioconsumazionifa.PASTO = 2, 'CENA', 'COLAZIONE')) AS PASTO
			  FROM conteggioconsumazionifa
			  WHERE conteggioconsumazionifa.GIORNO >= '$giornoda' AND conteggioconsumazionifa.GIORNO <= '$giornoa' AND conteggioconsumazionifa.FA = '$FA'
			  GROUP BY conteggioconsumazionifa.SEDE, conteggioconsumazionifa.DEN_UN_OPER, conteggioconsumazionifa.TipoRazione, conteggioconsumazionifa.FA, conteggioconsumazionifa.PASTO, conteggioconsumazionifa.Categoria
			  ORDER BY conteggioconsumazionifa.SEDE";
	
//-----------------------------------------------------


// initialize the array
	$data = array();
// do the SQL query
$result = $PRES_conn->query($query);
if ($result === false) {
    die("Error: ". mysqli_error($PRES_conn));
}

while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// step through the result set, populating the array, note that this could
// while($data[] = mysqli_fetch_assoc($result)) {}


   mysqli_free_result($result);
		
// Crea la tabella per i pranzi
	$pdf->ezTable($data,'' ,'', array('fontSize'=>'10', 'shaded'=>0, 'showLines'=>2));
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');

		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}

unset($data);
}

// ****************************  INIZIO REPORT DIMOSTRAZIONE NUMERICA PASTI PER PERIODO IN BASE ALLA CATEGORIA  *********************************

if (isset($_POST['ConteggioCAT']) && $_POST['ConteggioCAT'] == "Conteggio consumazioni per categoria") {
if (isset($_POST['sto_dal'])) {$giornoda = $_POST['sto_dal']; };
if (isset($_POST['sto_al'])) {$giornoa = $_POST['sto_al']; };	
if (isset($_POST['Categoria'])) {$Cate = $_POST['Categoria']; };	
	
	$Formato_data_da =  date('d-m-Y', strtotime($giornoda));
	$Formato_data_a =  date('d-m-Y', strtotime($giornoa));
	
	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Dimostrazione numerica per Categoria nel periodo',12, array('justification'=>'center'));
	$pdf->ezText('dal ' . $Formato_data_da . " al " . $Formato_data_a . ' presso le mense di servizio.',12, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');

//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT conteggioconsumazionifa.SEDE, conteggioconsumazionifa.DEN_UN_OPER as REPARTO, conteggioconsumazionifa.TipoRazione AS RAZIONE, 
			  conteggioconsumazionifa.Categoria AS CAT, SUM(conteggioconsumazionifa.ConteggioDiConteggioDiIDrecord) AS CONS, conteggioconsumazionifa.FA, IF(conteggioconsumazionifa.PASTO=1,'PRANZO', IF(conteggioconsumazionifa.PASTO=2,'CENA', 'COLAZIONE')) AS PASTO
			  FROM conteggioconsumazionifa 
			  WHERE conteggioconsumazionifa.GIORNO >= '$giornoda' AND  conteggioconsumazionifa.GIORNO <= '$giornoa' AND conteggioconsumazionifa.Categoria like '$Cate'
			  GROUP BY conteggioconsumazionifa.SEDE, conteggioconsumazionifa.DEN_UN_OPER, conteggioconsumazionifa.TipoRazione, conteggioconsumazionifa.FA, conteggioconsumazionifa.PASTO, conteggioconsumazionifa.Categoria
			  ORDER BY conteggioconsumazionifa.SEDE;";
	
//-----------------------------------------------------

// initialize the array
	$data = array();
// do the SQL query
	$result = $PRES_conn->query($query);

// step through the result set, populating the array, note that this could
	// while($data[] = mysqli_fetch_assoc($result)) {}
	while($row = mysqli_fetch_assoc($result)) {
		$data[] = $row;
	};
	mysqli_free_result($result);

// Crea la tabella per i pranzi
	$pdf->ezTable($data,'' ,'', array('fontSize'=>'10', 'shaded'=>0, 'showLines'=>2));
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');

		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
unset($data);
}

// Report dati INPS caricati per CF

if (isset($_GET['Stampa']) && $_GET['Stampa'] == "INPS") {
if (isset($_GET['CF'])) {$parCF = $_GET['CF']; };


	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','landscape');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center'));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Elenco dati INPS per BDC',10, array('justification'=>'center'));
	$pdf->ezText('CF: ' . $parCF,10, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');

//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT inps.CF, inps.Qual, inps.CodQual, inps.CodCont AS Co8, Date_Format(DAL, '%d/%m/%Y') As DAL, Date_Format(AL, '%d/%m/%Y') As AL, inps.GG, inps.R41,
			 inps.R42, inps.R43, inps.R45, inps.R46, inps.R47, inps.R48, inps.R53
			 FROM inps WHERE CF LIKE '$parCF';";
	
//-----------------------------------------------------


// initialize the array
	$data = array();
// do the SQL query
	$result = $PRES_conn->query($query);

// step through the result set, populating the array, note that this could
// while($data[] = mysqli_fetch_assoc($result)) {}
while($row = mysqli_fetch_assoc($result)) {
	$data[] = $row;
};
	mysqli_free_result($result);
		
// Crea la tabella per i pranzi
	$pdf->ezTable($data,'' ,'', array('fontSize'=>'9', 'justification'=>'right', 'shaded'=>0, 'showLines'=>2));
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('',8, 'center');
	$pdf->ezText('___________________________________________________',8, 'center');
	$pdf->ezText('Note:',8, 'center');
	$pdf->ezText('Co8: codice tipo impiego (1-tempo indeterminato, 17-tempo determinato)',8, 'center');
	$pdf->ezText('R41: maggiorazione base pensionabile',8, 'center');
	$pdf->ezText('R42: indennit� aeronavigazione/volo',8, 'center');
	$pdf->ezText('R43: imponibile TFS',8, 'center');
	$pdf->ezText('R45: imponibile credito',8, 'center');
	$pdf->ezText('R46: valore mensile stipendio tabellare',8, 'center');
	$pdf->ezText('R47: retribuzione individuale anzianit�',8, 'center');
	$pdf->ezText('R48: tot. imponibile pensionistico per il calcolo della contribuzione',8, 'center');
	$pdf->ezText('R53: retribuzione individuale ai fini pensionistici',8, 'center');


		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
unset($data);
}

// Report dati INPS caricati per data

if (isset($_GET['Stampa']) && $_GET['Stampa'] == "XDATA") {
if (isset($_GET['giorno'])) {$pargiorno = $_GET['giorno']; };


	//error_reporting(E_ALL);
	include('class.ezpdf.php');
	$pdf =new Cezpdf('a4','landscape');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezText($Reparto,18, array('justification'=>'center', 'shaded'=>0, 'showLines'=>2));
	$pdf->ezText('    ',18, 'center');
	$pdf->ezText('Elenco dati INPS per BDC inseriti in data '. $pargiorno,10, array('justification'=>'center'));
	$pdf->ezText('',10, array('justification'=>'center'));
	$pdf->ezText(' ',18, 'center');

//--------------------------------------------------
// Query selezione dei pasti in base al parametro pasto

	$query = "SELECT inps.File, inps.CF,inps.DataCar AS DATA, Count(inps.ID) AS N_record
			  FROM inps
			  WHERE inps.DataCar = '$pargiorno'
			  GROUP BY inps.CF,inps.DataCar, inps.File;";
	
//-----------------------------------------------------

// initialize the array
	$data = array();
// do the SQL query
	$result = $PRES_conn->query($query);

// step through the result set, populating the array, note that this could
// while($data[] = mysqli_fetch_assoc($result)) {}
while($row = mysqli_fetch_assoc($result)) {
	$data[] = $row;
};
	mysqli_free_result($result);
		
// Crea la tabella per i pranzi
	$pdf->ezTable($data,'' ,'', array('fontSize'=>'10', 'justification'=>'right', 'shaded'=>0, 'showLines'=>2));
	$pdf->ezText('',8, 'center');
$pdf->ezText('',8, 'center');
$pdf->ezText('',8, 'center');
	$pdf->ezText('___________________________________________________',8, 'center');

		if (isset($d) && $d){
			$pdfcode = $pdf->output(1);
			$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
			echo '<html><body>';
			echo trim($pdfcode);
			echo '</body></html>';
		} else {
			$pdf->stream();
		}
unset($data);
}
?>