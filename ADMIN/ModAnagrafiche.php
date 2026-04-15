<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
   //initialize the session
   session_start();
   
   // ** Logout the current user. **
   $logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
   if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
     $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
   }
   
   if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
     //to fully log out a visitor we need to clear the session varialbles
     unset($_SESSION['MM_Username']);
     unset($_SESSION['MM_UserGroup']);
      
     $logoutGoTo = "../Home.php";
     if ($logoutGoTo) {
       header("Location: $logoutGoTo");
       exit;
     }
   }
   ?>
<?php
   function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
   {
     // Magic quotes was removed in PHP 7.0, so we just apply addslashes directly
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
     $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
   }
   if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
      $insertSQL = sprintf("INSERT INTO pre_utentixunita (IDnome, ID_UO) 
          SELECT %s, %s 
          WHERE NOT EXISTS (
              SELECT 1 FROM pre_utentixunita 
              WHERE IDnome = %s AND ID_UO = %s
          )",
          GetSQLValueString($_POST['IDnome'], "text"),
          GetSQLValueString($_POST['select'], "int"),
          GetSQLValueString($_POST['IDnome'], "text"),
          GetSQLValueString($_POST['select'], "int"));
  
      $Result1 = $PRES_conn->query($insertSQL);
  }
   
   if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
     $US = $_POST['IDnome'];
     
     // Modifica la query per inserire solo le UO non già associate
     $insertSQL = sprintf("INSERT INTO pre_utentixunita (IDnome, ID_UO) 
       SELECT '$US', ID_UO 
       FROM pre_uo 
       WHERE ID_UO NOT IN (
         SELECT ID_UO 
         FROM pre_utentixunita 
         WHERE IDnome = '$US'
       )");
       
     $Result1 = $PRES_conn->query($insertSQL);
   }
   
   
   
   if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
     $updateSQL = sprintf("UPDATE pre_elenconomi SET Cognome=%s, Nome=%s, UO=%s, VTV=%s, `ADMIN`=%s, CF=%s, Categoria=%s, IDgrado=%s, Cte=%s, email=%s, FA=%s, Username=%s, Password=%s WHERE IDnome=%s",
                          GetSQLValueString($_POST['cognome'], "text"),
                          GetSQLValueString($_POST['nome'], "text"),
                          GetSQLValueString($_POST['UO'], "int"),
                          GetSQLValueString($_POST['VTV'], "text"),
                          GetSQLValueString($_POST['ADM'], "text"),
                          GetSQLValueString($_POST['CF'], "text"),
                          GetSQLValueString($_POST['Categoria'], "int"),
                          GetSQLValueString($_POST['grado'], "text"),
                         
                          GetSQLValueString($_POST['Cte'], "int"),
                          GetSQLValueString($_POST['email'], "text"),
   					   GetSQLValueString($_POST['FA'], "text"),
   					   GetSQLValueString($_POST['username'], "text"),
   					   GetSQLValueString($_POST['password'], "text"),
                          GetSQLValueString($_POST['ID'], "int"));
   
     $Result1 = $PRES_conn->query($updateSQL);
   
     $updateGoTo = "GestAnagrafiche.php";
     if (isset($_SERVER['QUERY_STRING'])) {
       $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
       $updateGoTo .= $_SERVER['QUERY_STRING'];
     }
     header(sprintf("Location: %s", $updateGoTo));
   }
   
   if ((isset($_POST['IDUO'])) && ($_POST['IDUO'] != "")) {
     $deleteSQL = sprintf("DELETE FROM pre_utentixunita WHERE ID=%s",
                          GetSQLValueString($_POST['IDUO'], "int"));
   
     $Result1 = $PRES_conn->query($deleteSQL);
   }
   
   if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
     $US = $_POST['IDnome'];
     
     // Modifica la query per inserire solo le UO non già associate
     $insertSQL = sprintf("INSERT INTO pre_utentixunita (IDnome, ID_UO) 
       SELECT '$US', ID_UO 
       FROM pre_uo 
       WHERE ID_UO NOT IN (
         SELECT ID_UO 
         FROM pre_utentixunita 
         WHERE IDnome = '$US'
       )");
       
     $Result1 = $PRES_conn->query($insertSQL);
   }
   if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "form5")) {
     $US = $_POST['IDnome'];
     $deleteSQL = sprintf("DELETE FROM pre_utentixunita WHERE IDnome = '%s'", $US);
     $Result1 = $PRES_conn->query($deleteSQL);
   }
   
   $parID_Nomi = "%";
   if (isset($_GET['ID_pers'])) {
     $parID_Nomi = addslashes($_GET['ID_pers']);
   }
   
   $query_Nomi = sprintf("Select
     pre_elenconomi.IDnome,
     pre_elenconomi.IDgrado,
     pre_elenconomi.Cognome,
     pre_elenconomi.Nome,
     pre_elenconomi.UO,
     pre_elenconomi.Username,
     pre_elenconomi.Password,
     pre_elenconomi.TipoOrario,
     pre_uo.DEN_UN_OPER,
     pre_elenconomi.ID_PERS_MTR,
     pre_elenconomi.Username,
     pre_elenconomi.Password,
     pre_elenconomi.VTV,
     pre_elenconomi.ADMIN,
     pre_elenconomi.CF,
     pre_elenconomi.Categoria,
     pre_elenconomi.Cte,
     pre_elenconomi.email,
     pre_elenconomi.Stip,
     pre_elenconomi.FA
   From
     pre_elenconomi Left Join
     pre_uo On pre_elenconomi.UO = pre_uo.ID_UO
   Where
     pre_elenconomi.IDnome = '%s'", $parID_Nomi);
     
   $Nomi = $PRES_conn->query($query_Nomi);
   $row_Nomi = mysqli_fetch_assoc($Nomi);
   $totalRows_Nomi = $Nomi->num_rows;
   
   $query_UO = "SELECT pre_uo.ID_UO, pre_uo.DEN_UN_OPER FROM pre_uo ORDER BY pre_uo.DEN_UN_OPER";
   $UO = $PRES_conn->query($query_UO);
   $row_UO = mysqli_fetch_assoc($UO);
   $totalRows_UO = $UO->num_rows;
   
   $parID_UO_abilitate = "%";
   if (isset($_GET['ID_pers'])) {
     $parID_UO_abilitate = addslashes($_GET['ID_pers']);
   }
   
   $query_UO_abilitate = sprintf("SELECT pre_utentixunita.ID, pre_utentixunita.IDnome, pre_utentixunita.ID_UO, pre_uo.DEN_UN_OPER, pre_uo.SEDE, pre_sedi.SEDE FROM pre_utentixunita, pre_uo, pre_sedi WHERE pre_uo.ID_UO=pre_utentixunita.ID_UO AND pre_sedi.IDsede=pre_uo.SEDE AND pre_utentixunita.IDnome='%s' ORDER BY pre_uo.PRE_UN_OPER", $parID_UO_abilitate);
   $UO_abilitate = $PRES_conn->query($query_UO_abilitate);
   $row_UO_abilitate = mysqli_fetch_assoc($UO_abilitate);
   $totalRows_UO_abilitate = $UO_abilitate->num_rows;
   
   
   $query_Sedi = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
   $Sedi = $PRES_conn->query($query_Sedi);
   $row_Sedi = mysqli_fetch_assoc($Sedi);
   $totalRows_Sedi = $Sedi->num_rows;
   
   
   $query_Categorie = "SELECT pre_categorie.IDcat, pre_categorie.Categoria FROM pre_categorie";
   $Categorie = $PRES_conn->query($query_Categorie);
   $row_Categorie = mysqli_fetch_assoc($Categorie);
   $totalRows_Categorie = $Categorie->num_rows;
   
   
   $query_Gradi = "SELECT pre_gradi.ID, pre_gradi.Grado FROM pre_gradi ORDER BY
     pre_gradi.Ordinamento";
   $Gradi = $PRES_conn->query($query_Gradi);
   $row_Gradi = mysqli_fetch_assoc($Gradi);
   $totalRows_Gradi = $Gradi->num_rows;
   ?>
<?php
   // Mantenere il codice PHP iniziale invariato
   ?>
<!DOCTYPE HTML>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <title>Modifica Anagrafica</title>
      <link rel="stylesheet" href="\ACCESSI\style\fonta\css\all.css">
      <style>
         body {
         margin: 0;
         padding: 0;
         background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
         background-size: cover;
         font-family: Arial, sans-serif;
         min-height: 100vh;
         color: #ffffff;
         }
         .container {
         width: 90%;
         max-width: 1200px;
         margin: 0 auto;
         padding: 20px;
         }
         .data-table {
         background-image: linear-gradient(to bottom, #07406b 40%, #07406b);
         border-radius: 0.7em;
         padding: 15px;
         margin: 15px auto;
         width: 100%;
         max-width: 800px;
         }
         .form-group {
         margin: 10px 0;
         }
         .btn {
         background-color: #07406b;
         color: #ffffff;
         padding: 10px 20px;
         border: 1px solid #ffffff;
         border-radius: 0.7em;
         cursor: pointer;
         }
         select, input[type="text"], input[type="password"] {
         padding: 5px;
         border-radius: 4px;
         width: 100%;
         max-width: 200px;
         }
         .data-table {
         background: linear-gradient(to bottom, #07406b 40%, #07406b);
         border-radius: 12px;
         padding: 20px;
         margin: 20px auto;
         width: 90%;
         max-width: 800px;
         box-shadow: 0 4px 6px rgba(0,0,0,0.1);
         }
         .section-title {
         text-align: center;
         color: white;
         font-size: 20px;
         font-weight: bold;
         margin: 0 0 30px 0; /* Ridotto il margin-bottom e rimosso padding */
         border-bottom: 2px solid rgba(255,255,255,0.2);
         padding-bottom: 10px; /* Aggiunto padding-bottom per il bordo */
         }
         /* Aggiustamento spaziatura contenuto */
         .form-row {
         display: flex;
         justify-content: space-between;
         margin-top: 10px; /* Ridotto il margine superiore */
         gap: 20px;
         }
         .form-group {
         flex: 1;
         margin-top: 5px; /* Ridotto il margine superiore */
         }
         .form-label {
         display: block;
         margin-bottom: 5px;
         color: white;
         font-weight: bold;
         }
         .form-control {
         width: 100%;
         padding: 8px;
         border-radius: 6px;
         border: 1px solid #ddd;
         }
         .submit-container {
         text-align: center;
         margin: 30px 0;
         }
         .submit-button {
         background: #07406b;
         color: white;
         padding: 15px 40px;
         font-size: 18px;
         border: 2px solid white;
         border-radius: 8px;
         cursor: pointer;
         transition: all 0.3s ease;
         }
         .submit-button:hover {
         background: #0a5289;
         transform: scale(1.05);
         }
         .uo-list {
         background: rgba(255,255,255,0.1);
         border-radius: 8px;
         padding: 15px;
         margin-top: 20px;
         }
         .uo-item {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 10px;
         border-bottom: 1px solid rgba(255,255,255,0.1);
         }
         .uo-info {
         flex: 1;
         color: white;
         }
         .uo-actions {
         margin-left: 15px;
         }
         .uo-item:first-child {
    margin-top: 20px; /* Aggiunto spazio prima del primo elemento della lista */
}
         h3 {
         text-align: center;
         margin-bottom: 20px;
         color: white;
         }
         .uo-item {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 10px;
         border-bottom: 1px solid rgba(255,255,255,0.1);
         }
         .uo-info {
         display: flex;
         justify-content: space-between;
         width: 70%;
         }
         .uo-sede {
         text-align: left;
         min-width: 150px;
         }
         .uo-nome {
         flex-grow: 1;
         padding-right: 20px;
         }
         .buttons-container {
         display: flex;
         justify-content: flex-end;
         gap: 10px;
         margin-left: auto;
         }
         .right-buttons {
         display: flex;
         gap: 10px;
         }
         .uo-selection-container {
         display: flex;
         align-items: center;
         gap: 10px;
         margin-bottom: 15px;
         margin: 40px 0;
         }
         .uo-selection-container select {
         flex: 1;
         max-width: 300px;
         }
         .back-button-container {
         position: fixed;
         top: 40px; /* Aumentato da 20px a 30px per abbassare leggermente */
         left: 20px;
         z-index: 1000;
         }
         .back-button {
         background-color: #07406b;
         color: #ffffff;
         padding: 8px 10px;
         border: 2px solid #ffffff;
         border-radius: 50%;
         cursor: pointer;
         display: flex;
         align-items: center;
         justify-content: center;
         transition: all 0.3s ease;
         width: 35px;
         height: 35px;
         text-decoration: none;
         }
         .back-button:hover {
         background-color: #0a5289;
         transform: scale(1.1);
         box-shadow: 0 0 10px rgba(255,255,255,0.3);
         }
         .back-button i {
         font-size: 16px;
         }
         h4 {
    margin-top: 40px; /* Aggiunto margine sopra "UO abilitate" */
    margin-bottom: 20px; /* Aggiunto margine sotto */
    color: white;
}
      </style>
   </head>
   <body>
      <div class="back-button-container">
         <a href="GestAnagrafiche.php" class="back-button">
         <i class="fas fa-arrow-left"></i>
         </a>
      </div>
      <div class="container">
         <form name="form1" method="POST" action="<?php echo $editFormAction; ?>">
            <div class="data-table">
               <div class="form-group">
                  <table width="100%">
                     <tr>
                        <td>Grado</td>
                        <td>Categoria</td>
                        <td>F.A.</td>
                        <td>C.F.</td>
                        <td>Cognome</td>
                        <td>Nome</td>
                     </tr>
                     <tr>
                        <td>
                           <select name="grado" id="grado">
                              <?php do { ?>
                              <option value="<?php echo $row_Gradi['ID']?>" <?php if (!(strcmp($row_Gradi['ID'], $row_Nomi['IDgrado']))) {echo "SELECTED";} ?>><?php echo $row_Gradi['Grado']?></option>
                              <?php } while ($row_Gradi = mysqli_fetch_assoc($Gradi)); ?>
                           </select>
                        </td>
                        <td>
                           <select name="Categoria" id="Categoria">
                              <?php do { ?>
                              <option value="<?php echo $row_Categorie['IDcat']?>" <?php if (!(strcmp($row_Categorie['IDcat'], $row_Nomi['Categoria']))) {echo "SELECTED";} ?>><?php echo $row_Categorie['Categoria']?></option>
                              <?php } while ($row_Categorie = mysqli_fetch_assoc($Categorie)); ?>
                           </select>
                        </td>
                        <td>
                           <select name="FA" id="FA">
                              <option value="EI" <?php if (!(strcmp("EI", $row_Nomi['FA']))) {echo "SELECTED";} ?>>EI</option>
                              <option value="AM" <?php if (!(strcmp("AM", $row_Nomi['FA']))) {echo "SELECTED";} ?>>AM</option>
                              <option value="MM" <?php if (!(strcmp("MM", $row_Nomi['FA']))) {echo "SELECTED";} ?>>MM</option>
                              <option value="CC" <?php if (!(strcmp("CC", $row_Nomi['FA']))) {echo "SELECTED";} ?>>CC</option>
                           </select>
                        </td>
                        <td><input name="CF" type="text" id="CF" value="<?php echo $row_Nomi['CF']; ?>"></td>
                        <td><input name="cognome" type="text" id="cognome" value="<?php echo $row_Nomi['Cognome']; ?>"></td>
                        <td><input name="nome" type="text" id="nome" value="<?php echo $row_Nomi['Nome']; ?>"></td>
                     </tr>
                  </table>
               </div>
            </div>
            <div class="data-table">
               <div class="form-group">
                  <table width="100%">
                     <tr>
                        <td>Tipo utente</td>
                        <td>U.O. di appartenenza</td>
                     </tr>
                     <tr>
                        <td>
                           <select name="Cte" id="Cte">
                              <option value="0" <?php if (!(strcmp(0, $row_Nomi['Cte']))) {echo "SELECTED";} ?>>Utente standard</option>
                              <option value="1" <?php if (!(strcmp(1, $row_Nomi['Cte']))) {echo "SELECTED";} ?>>Comandante/Capo Ufficio</option>
                              <option value="2" <?php if (!(strcmp(2, $row_Nomi['Cte']))) {echo "SELECTED";} ?>>Gestione prenotazioni esterne</option>
                           </select>
                        </td>
                        <td>
                           <select name="UO" id="UO">
                              <?php 
                                 // Reset del puntatore dell'array
                                 mysqli_data_seek($UO, 0);
                                 while ($row_UO = mysqli_fetch_assoc($UO)) { 
                                    ?>
                                      <option value="<?php echo $row_UO['ID_UO']?>" 
                                        <?php if (!(strcmp($row_UO['ID_UO'], $row_Nomi['UO']))) {
                                          echo "SELECTED";
                                        } ?>>
                                        <?php echo $row_UO['DEN_UN_OPER']?>
                                      </option>
                                    <?php 
                                    } 
                                    ?>
                                  </select>
                        </td>
                     </tr>
                  </table>
               </div>
            </div>
            <div class="data-table">
               <h2 class="section-title">Ruoli Utente</h2>
               <div class="form-row">
                  <div class="form-group">
                     <label class="form-label">Admin</label>
                     <select name="ADM" class="form-control">
                        <option value="1" <?php if (!(strcmp(1, $row_Nomi['ADMIN']))) {echo "SELECTED";} ?>>Si</option>
                        <option value="0" <?php if (!(strcmp(0, $row_Nomi['ADMIN']))) {echo "SELECTED";} ?>>No</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label class="form-label">add.VTV</label>
                     <select name="VTV" class="form-control">
                        <option value="2" <?php if (!(strcmp(2, $row_Nomi['VTV']))) {echo "SELECTED";} ?>>Si</option>
                        <option value="0" <?php if (!(strcmp(0, $row_Nomi['VTV']))) {echo "SELECTED";} ?>>No</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="data-table">
               <h2 class="section-title">Credenziali Accesso</h2>
               <div class="form-row">
                  <div class="form-group">
                     <label class="form-label">Username</label>
                     <input name="username" type="text" class="form-control" value="<?php echo $row_Nomi['Username']; ?>">
                  </div>
                  <div class="form-group">
                     <label class="form-label">Password</label>
                     <input name="password" type="password" class="form-control" value="<?php echo $row_Nomi['Password']; ?>">
                  </div>
               </div>
            </div>
            <div class="submit-container">
               <input name="ID" type="hidden" id="ID" value="<?php echo $row_Nomi['IDnome']; ?>">
               <input type="submit" name="Submit" value="Salva" class="submit-button">
               <input type="hidden" name="MM_update" value="form1">
            </div>
         </form>
         <div class="data-table">
            <h3>Associazione Unità Organizzativa</h3>
            <div class="uo-selection-container">
               <form name="form2" method="POST" action="<?php echo $editFormAction; ?>" style="display: flex; align-items: center; gap: 10px;">
                  <input name="IDnome" type="hidden" value="<?php echo $row_Nomi['IDnome']; ?>">
                  <select name="select">
                     <?php 
                        mysqli_data_seek($UO, 0);
                        while ($row_UO = mysqli_fetch_assoc($UO)) { ?>
                     <option value="<?php echo $row_UO['ID_UO']?>"><?php echo $row_UO['DEN_UN_OPER']?></option>
                     <?php } ?>
                  </select>
                  <input type="submit" name="Submit" value="Salva" class="btn">
                  <input type="hidden" name="MM_insert" value="form2">
               </form>
               <div class="buttons-container">
                  <form name="form3" method="POST" action="<?php echo $editFormAction; ?>" style="display: inline-block;">
                     <input name="IDnome" type="hidden" value="<?php echo $row_Nomi['IDnome']; ?>">
                     <input type="submit" name="Submit" value="Abilita tutte" class="btn">
                     <input type="hidden" name="MM_insert" value="form3">
                  </form>
                  <form name="form5" method="POST" action="<?php echo $editFormAction; ?>" style="display: inline-block;">
                     <input name="IDnome" type="hidden" value="<?php echo $row_Nomi['IDnome']; ?>">
                     <input type="submit" name="Submit" value="Disabilita tutte" class="btn" 
                        onclick="return confirm('Sei sicuro di voler rimuovere tutte le UO abilitate?');">
                     <input type="hidden" name="MM_delete" value="form5">
                  </form>
               </div>
            </div>
            <?php if ($totalRows_UO_abilitate > 0) { ?>
            <h4>UO abilitate:</h4>
            <?php do { ?>
            <div class="uo-item">
               <div class="uo-info">
                  <span class="uo-nome"><?php echo $row_UO_abilitate['DEN_UN_OPER']; ?></span>
                  <!-- <span class="uo-sede"><?php echo $row_UO_abilitate['SEDE']; ?></span> -->
               </div>
               <form name="form4" method="post" action="">
                  <input name="IDUO" type="hidden" value="<?php echo $row_UO_abilitate['ID']; ?>">
                  <input type="submit" name="Submit" value="Disabilita" class="btn">
               </form>
            </div>
            <?php } while ($row_UO_abilitate = mysqli_fetch_assoc($UO_abilitate)); ?>
            <?php } ?>
         </div>
      </div>
      <?php
         mysqli_free_result($Nomi);
         mysqli_free_result($UO);
         mysqli_free_result($UO_abilitate); 
         mysqli_free_result($Sedi);
         mysqli_free_result($Categorie);
         mysqli_free_result($Gradi);
         ?>
   </body>
</html>