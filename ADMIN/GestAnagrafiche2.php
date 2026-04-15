<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
   session_start();
   $Tx_cert = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
   
   function checkExistingCF($cf) {
       global $PRES_conn;
       $query = "SELECT COUNT(*) as count FROM pre_elenconomi WHERE CF = ?";
       $stmt = $PRES_conn->prepare($query);
       $stmt->bind_param("s", $cf);
       $stmt->execute();
       $result = $stmt->get_result();
       $row = $result->fetch_assoc();
       return $row['count'] > 0;
   }
   
   function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
   {
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
   
   $editFormAction = $_SERVER['PHP_SELF'];
   if (isset($_SERVER['QUERY_STRING'])) {
     $editFormAction .= htmlentities($_SERVER['QUERY_STRING']);
   }
   
   if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
     $insertSQL = sprintf("INSERT INTO pre_elenconomi (Cognome, Nome, UO, Forza, TipoRazione, TipoRazioneCe, TipoRazioneCol, CF, Categoria, IDgrado, FA, SedeSomm) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                          GetSQLValueString($_POST['cognome'], "text"),
                          GetSQLValueString($_POST['nome'], "text"),
                          GetSQLValueString($_POST['UO'], "int"),
                          GetSQLValueString($_POST['Forza'], "int"),
                          GetSQLValueString($_POST['select'], "int"),
   					   GetSQLValueString($_POST['selectcena'], "int"),
   					   GetSQLValueString($_POST['selectcol'], "int"),
                          GetSQLValueString($_POST['CF'], "text"),
                          GetSQLValueString($_POST['Categoria'], "int"),
                          GetSQLValueString($_POST['Grado'], "int"),
   					   GetSQLValueString($_POST['FA'], "text"),
   					   GetSQLValueString($_POST['sede'], "int"));
   
     $Result1 = $PRES_conn->query($insertSQL);
     $insertGoTo = "/ACCESSI/ADMIN/GestAnagrafiche2.php";
   
     if (isset($_SERVER['QUERY_STRING'])) {
       $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
       $insertGoTo .= $_SERVER['QUERY_STRING'];
     }
     header(sprintf("Location: %s", $insertGoTo));
   }
   
   
   $parUO_Nomi = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : null;
   $parNome_Nomi = "%";
   if (isset($_POST['Nome']) || isset($_GET['Nome'])) {
      if(isset($_POST['Nome'])){
         $parNome_Nomi = addslashes($_POST['Nome']);
      }
      if(isset($_GET['Nome'])){
         $parNome_Nomi = addslashes($_GET['Nome']);
      }
   
   	$query_Nomi = sprintf("Select pre_elenconomi.IDnome, pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome,
   						  pre_elenconomi.UO, pre_elenconomi.TipoOrario, pre_uo.DEN_UN_OPER, pre_elenconomi.ID_PERS_MTR,
   						  pre_elenconomi.Forza, pre_elenconomi.CF, pre_elenconomi.FA
   		     			  From  pre_elenconomi Left Join pre_gradi On pre_elenconomi.IDgrado = pre_gradi.ID Inner Join
   						  pre_utentixunita On pre_elenconomi.UO = pre_utentixunita.ID_UO, pre_uo
   						  Where pre_uo.ID_UO = pre_elenconomi.UO And  (pre_elenconomi.Cognome = '%s' And
   						  pre_utentixunita.IDnome = '%s')
   						  Group By pre_elenconomi.IDnome, pre_gradi.Grado, pre_elenconomi.Cognome, pre_elenconomi.Nome, pre_elenconomi.UO, pre_elenconomi.TipoOrario,
   						  pre_uo.DEN_UN_OPER, pre_elenconomi.ID_PERS_MTR, pre_elenconomi.Forza,
   						  pre_elenconomi.CF, pre_elenconomi.FA, pre_utentixunita.IDnome
   						  Order By pre_elenconomi.Cognome", $parNome_Nomi, $parUO_Nomi);
   	$Nomi = $PRES_conn->query($query_Nomi);
   	$row_Nomi = mysqli_fetch_assoc($Nomi);
   	$totalRows_Nomi = $Nomi->num_rows;
   }
   
   $Tx_cert = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : null;
   
   $query_UO = "SELECT pre_uo.ID_UO, pre_uo.DEN_UN_OPER FROM pre_uo, pre_utentixunita WHERE pre_uo.ID_UO=pre_utentixunita.ID_UO AND pre_utentixunita.IDnome='$Tx_cert' ORDER BY pre_uo.DEN_UN_OPER";
   $UO = $PRES_conn->query($query_UO);
   $row_UO = mysqli_fetch_assoc($UO);
   $totalRows_UO = $UO->num_rows;
   
   
   $query_TipoRazione = "SELECT pre_tiporazione.ID, pre_tiporazione.TipoRazione FROM pre_tiporazione";
   $TipoRazione = $PRES_conn->query($query_TipoRazione);
   $row_TipoRazione = mysqli_fetch_assoc($TipoRazione);
   $totalRows_TipoRazione = $TipoRazione->num_rows;
   
   
   $query_Sede = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
   $Sede = $PRES_conn->query($query_Sede);
   $row_Sede = mysqli_fetch_assoc($Sede);
   $totalRows_Sede = $Sede->num_rows;
   
   
   $query_TipoRaz = "SELECT pre_tiporazione.ID, pre_tiporazione.TipoRazione FROM pre_tiporazione";
   $TipoRaz = $PRES_conn->query($query_TipoRaz);
   $row_TipoRaz = mysqli_fetch_assoc($TipoRaz);
   $totalRows_TipoRaz = $TipoRaz->num_rows;
   
   
   $query_Gradi = "SELECT pre_gradi.ID, pre_gradi.Grado, pre_gradi.Cat FROM pre_gradi ORDER BY pre_gradi.Ordinamento";
   $Gradi = $PRES_conn->query($query_Gradi);
   $row_Gradi = mysqli_fetch_assoc($Gradi);
   $totalRows_Gradi = $Gradi->num_rows;
   
   
   $query_Categoria = "SELECT pre_categorie.IDcat, pre_categorie.Categoria FROM pre_categorie";
   $Categoria = $PRES_conn->query($query_Categoria);
   $row_Categoria = mysqli_fetch_assoc($Categoria);
   $totalRows_Categoria = $Categoria->num_rows;
   
   ?>
<?php
   // Gestione della richiesta AJAX
   if (isset($_POST['check_cf'])) {
       $cf = $_POST['cf'];
       echo checkExistingCF($cf) ? "exists" : "ok";
       exit;
   } ?>
<!DOCTYPE html>
<html lang="it">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Gestione anagrafiche</title>
      <script>
         function controllo() {
             var CF = document.formControllo.CF.value;
             var cognome = document.formControllo.cognome.value;
             var nome = document.formControllo.nome.value; 
             var grado = document.formControllo.grado.value;
         
             // Validazioni di base
             if (!CF) {
                 alert("Digitare Codice Fiscale.");
                 document.formControllo.CF.focus();
                 return false;
             }
             if (!cognome) {
                 alert("Digitare Cognome.");
                 document.formControllo.cognome.focus();
                 return false;
             }
             if (!nome) {
                 alert("Digitare Nome");
                 document.formControllo.nome.focus();
                 return false;
             }
             if (!grado) {
                 alert("Selezionare il grado");
                 document.formControllo.grado.focus();
                 return false;
             }
         
             // Verifica CF con AJAX
             var xhr = new XMLHttpRequest();
             xhr.open("POST", window.location.href, true);
             xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
             xhr.onreadystatechange = function() {
                 if (xhr.readyState === 4 && xhr.status === 200) {
                     if (xhr.responseText === "exists") {
                         alert("Codice Fiscale già presente in anagrafica!");
                         document.formControllo.CF.focus();
                     } else {
                         // Procedi con l'iframe
                         var frame = document.getElementById('operationsFrame');
                         var iframe = document.getElementById('operationsIframe');
                         
                         frame.style.display = 'block';
                         iframe.src = "operations2.php?CF=" + encodeURIComponent(CF) + 
                                    "&nome=" + encodeURIComponent(nome) + 
                                    "&cognome=" + encodeURIComponent(cognome) + 
                                    "&grado=" + encodeURIComponent(grado);
         
                         iframe.onload = function() {
                             var height = Math.max(
                                 iframe.contentWindow.document.body.scrollHeight,
                                 200
                             );
                             iframe.style.height = height + 'px';
                             frame.style.height = height + 'px';
                             frame.scrollIntoView({
                                 behavior: 'smooth',
                                 block: 'start'
                             });
                         };
                     }
                 }
             };
             xhr.send("check_cf=1&cf=" + encodeURIComponent(CF));
             return false;
         }
         
      </script>
      <style>
         :root {
  --white: #ffffff;
  --primary-color: rgba(32, 77, 98, 1);
  --white-transparent: rgba(255, 255, 255, 0.1);
  --table-header: rgba(32, 77, 98, 1);
}

/* Stili base */
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  color: var(--white);
  background: linear-gradient(to bottom, rgba(88, 140, 164, 1), rgba(17, 56, 78, 1));
  background-size: cover;
  background-attachment: fixed;
  margin: 0;
  padding: 5px;
  min-height: 100vh;
  max-width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
}

/* Contenitori */
.form-container {
  width: 95%;
  max-width: 1200px;
  margin: 1rem auto;
  padding: 1rem;
  border-radius: 12px;
}

/* Tabella dati */
.data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin: 0 auto;
  border-radius: 10px;
  background-color: rgba(255, 255, 255, 0.15);
  font-size: 0.9rem;
}

.data-table th {
  background: var(--table-header);
  padding: 1.2rem;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid var(--white-transparent);
}

.data-table td {
  padding: 1.2rem;
  text-align: center;
  border-bottom: 1px solid var(--white-transparent);
}

.data-table tr:hover td {
  background: rgba(255, 255, 255, 0.05);
}

/* Form e input */
input[type="text"],
select {
  padding: 0.8em;
  border-radius: 6px;
  border: 1px solid var(--white);
  background: rgba(255, 255, 255, 0.2);
  color: var(--white);
  width: 95%;
  max-width: 300px;
  font-size: 1.1em;
}

.button {
  background: var(--primary-color);
  border: none;
  color: var(--white);
  border-radius: 0.6em;
  padding: 0.8em 1.5em;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  background-color: rgba(42, 97, 118, 1);
}

/* Menu e barra di ricerca */
.nav-menu {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin: 1.5rem auto;
  width: 95%;
  max-width: 1200px;
  flex-wrap: wrap;
  padding: 1rem;
}

.search-bar {
  width: 85%;
  max-width: 1000px;
  margin: 5rem auto;
  padding: 1rem;
}

.search-bar form {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

/* Frame operazioni */
#operationsFrame {
  margin: 20px auto;
  width: 90%;
  max-width: 1000px;
}

#operationsIframe {
  width: 100%;
  border: none;
  min-height: 200px;
  border-radius: 8px;
}

/* Media query */
@media screen and (max-width: 768px) {
  .data-table {
    font-size: 0.8rem;
  }
  
  .data-table th, 
  .data-table td {
    padding: 0.6rem 0.3rem;
  }
  
  input[type="text"],
  select {
    width: 100%;
    max-width: 250px;
  }
  
  .button {
    padding: 0.6em 1em;
    font-size: 0.9rem;
  }
}
      </style>
   </head>
   <body>
      <div class="nav-menu">
         <form method="post" action="">
            <input type="hidden" name="MM_form5" value="form5">
            <input type="submit" class="button" value="Inserisci/trova nominativi">
         </form>
         <form method="post" action="Cartellino2.php">
            <input type="submit" class="button" value="Stampa Qr Code">
         </form>
      </div>
      <div class="search-bar">
         <form name="form2" method="post" action="/ACCESSI/Admin/GestAnagrafiche2.php">
            <span>Inserire Cognome:</span>
            <input name="Nome" type="text" id="Nome">
            <input type="submit" class="button" name="Submit" value="Trova">
         </form>
      </div>
      <div align="center">
         <?php if (isset($_POST['MM_form5']) && $_POST['MM_form5'] == 'form5') { ?>
         <div class="form-container">
            <h3 style="color: white; text-align: center; margin-bottom: 1rem;">Inserimento/ricerca anagrafica</h3>
            <form name="formControllo" id="formControllo" method="GET" action="<?php echo $editFormAction; ?>">
               <table class="data-table">
                  <tr>
                     <th width="25%">Cod.Fiscale</th>
                     <th width="25%">Grado</th>
                     <th width="25%">Cognome</th>
                     <th width="25%">Nome</th>
                  </tr>
                  <tr>
                     <td>
                        <input name="CF" type="text" maxlength="16" style="width: 90%; padding: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid white; border-radius: 4px;">
                     </td>
                     <td>
                        <select name="grado" style="width: 90%; padding: 8px; background: rgba(32,77,98,0.9); color: white; border: 1px solid white; border-radius: 4px;">
                           <?php do { ?>
                           <option value="<?php echo $row_Gradi['ID']?>"><?php echo $row_Gradi['Grado']?></option>
                           <?php } while ($row_Gradi = mysqli_fetch_assoc($Gradi)); ?>
                        </select>
                     </td>
                     <td>
                        <input type="text" name="cognome" style="width: 90%; padding: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid white; border-radius: 4px;">
                     </td>
                     <td>
                        <input type="text" name="nome" style="width: 90%; padding: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid white; border-radius: 4px;">
                     </td>
                  </tr>
               </table>
               <div style="text-align: center; margin-top: 2rem;">
                  <input type="button" class="button" value="Avanti" onClick="controllo();" style="width: 200px; padding: 12px;">
               </div>
            </form>
            <div id="operationsFrame" style="display: none;">
               <iframe id="operationsIframe" 
                  frameborder="0"
                  scrolling="no"
                  style="width: 100%; 
                  min-height: 200px;
                  border: none; 
                  border-radius: 8px; 
                  background: trasparent">
               </iframe>
            </div>
         </div>
         <?php } else {
            if (isset($totalRows_Nomi) && $totalRows_Nomi > 0) { ?>
         <div class="form-container">
            <table class="data-table">
               <thead>
                  <tr>
                     <th>Grado</th>
                     <th>F.A.</th>
                     <th>Cognome</th>
                     <th>Nome</th>
                     <th>Comando/Ufficio</th>
                     <th>In forza</th>
                     <th>Modifica</th>
                  </tr>
               </thead>
               <tbody>
                  <?php do { ?>
                  <tr>
                     <td><?php echo $row_Nomi['Grado']; ?></td>
                     <td><?php echo $row_Nomi['FA']; ?></td>
                     <td><?php echo $row_Nomi['Cognome']; ?></td>
                     <td><?php echo $row_Nomi['Nome']; ?></td>
                     <td><?php echo $row_Nomi['DEN_UN_OPER']; ?></td>
                     <td><?php echo (!(strcmp($row_Nomi['Forza'],1))) ? "SI" : "NO"; ?></td>
                     <td>
                        <form action="/ACCESSI/ModAnagrafiche.php" method="post">
                           <input type="hidden" name="IDrecord" value="<?php echo $row_Nomi['IDnome']; ?>">
                           <input type="submit" class="button" value="Modifica">
                        </form>
                     </td>
                  </tr>
                  <?php } while ($row_Nomi = mysqli_fetch_assoc($Nomi)); ?>
               </tbody>
            </table>
         </div>
         <?php }
            } ?>
      </div>
   </body>
</html>
<?php
   mysqli_free_result($UO);
   mysqli_free_result($TipoRazione);
   mysqli_free_result($Sede);
   mysqli_free_result($TipoRaz);
   mysqli_free_result($Gradi);
   mysqli_free_result($Categoria);
   ?>