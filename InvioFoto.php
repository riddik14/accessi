<?php require_once('Connections/MyPresenze.php'); ?>
<?php
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
     $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
   }
   
   if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    
     $updateSQL = sprintf("UPDATE pre_elenconomi SET Foto=%s WHERE IDnome=%s",
                          GetSQLValueString('\ACCESSI\Foto\ '. $_FILES['file']['name'], "text"),
                          GetSQLValueString($_POST['hiddenField'], "int"));
     $Result1 = $PRES_conn->query($updateSQL);
   }
   
   $IDap_Apparati = "%";
   if (isset($_POST['IDnome'])) {
     $IDap_Apparati = addslashes($_POST['IDnome']);
   }
   
   $query_Apparati = sprintf("SELECT pre_elenconomi.IDnome, pre_elenconomi.Foto FROM  pre_elenconomi WHERE pre_elenconomi.IDnome='%s'", $IDap_Apparati);
   $Apparati = $PRES_conn->query($query_Apparati);
   $row_Apparati = mysqli_fetch_assoc($Apparati);
   $totalRows_Apparati = $Apparati->num_rows;
   
   if (isset($_POST['submit'])) {
   //print_r($HTTP_POST_FILES);
   if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
   $error = "Non è stato selezionato alcun file da inviare!";
   unlink($_FILES['file']['tmp_name']);
   // assign error message, remove uploaded file, redisplay form.
   } else {
   //a file was uploaded
   $maxfilesize=4102400;
   if ($_FILES['file']['size'] > $maxfilesize) {
   $error = "Il file selezionato è troppo grande.";
   unlink($_FILES['file']['tmp_name']);
   
   
   // assign error message, remove uploaded file, redisplay form.
   } else 
   
   //File has passed all validation, copy it to the final destination and remove the temporary file:
   $file_tmp = $_FILES['file']['tmp_name'];
   $file_name = $_FILES['file']['name'];
   $file_destination = 'C:\xampp\htdocs\ACCESSI\Foto\ ' . $file_name;
   $file_delete = 'C:\Users\cllfba73d21z133l\Desktop\Foto\ ';
   move_uploaded_file($file_tmp, $file_destination);
   //unlink($file_delete);
   $filename = ($_FILES['file']['name']);
   $_SESSION['redirect_id'] = $_POST['hiddenField'];
   echo "<script type='text/javascript'>
           alert('Foto inserita correttamente.');
           window.location.href = 'ModAnagrafiche.php?IDrecord=" . $_POST['hiddenField'] . "';
         </script>";
   }
   }
   
   ?>
<html>
   <head>
      <style type="text/css">
         body { font-family: Arial, sans-serif; margin: 20px; }
        .container { width: 800px; 
                     margin: 0 auto;
                     background-color: #ffffff; }
        .header { background-color: #07406b; color: white; padding: 10px; font-weight: bold; }
        .form-content { padding: 20px; border: 1px solid #ccc; }
        .form-group { margin: 15px 0;
                      color: #07406b; }
        .btn-submit { 
            background-color: #07406b;
            color: white;
            padding: 8px 20px;
            border: none;
            cursor: pointer;
            margin: 1em;
        }
        .error { color: red; margin: 10px 0; }
      </style>
   </head>
   <body>
   <div class="container">
        <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data">
            <div class="header">Inserimento foto</div>
            <div class="form-content">
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Selezionare il file da inviare:</label>
                    <input name="file" type="file" size="40">
                </div>
                
                <input name="hiddenField" type="hidden" value="<?php echo isset($row_Apparati['IDnome']) ? $row_Apparati['IDnome'] : ''; ?>">
                <input type="hidden" name="MM_update" value="form1">
                
                <div class="form-group">
                    <input name="submit" type="submit" class="btn-submit" value="Salva">
                </div>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_free_result($Apparati); ?>