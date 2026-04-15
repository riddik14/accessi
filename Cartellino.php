<?php
   require_once('Connections/MyPresenze.php');
   session_start();
   
   // Query ottimizzata usando JOIN invece di WHERE
   $query_Nomi = sprintf("SELECT n.IDnome, n.Cognome, n.Nome, g.Grado 
                         FROM pre_elenconomi n 
                         INNER JOIN pre_utentixunita u ON n.UO = u.ID_UO 
                         INNER JOIN pre_gradi g ON n.IDgrado = g.ID 
                         WHERE u.IDnome = '%s'
                         ORDER BY n.Cognome", $_SESSION['UserID']);
   
   $Reparto = $PRES_conn->query("SELECT Reparto FROM pre_setup")->fetch_assoc()['Reparto'];
   $Nomi = $PRES_conn->query($query_Nomi);
   ?>
<!DOCTYPE html>
<html lang="it">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Cartellini</title>
      <style>
         :root {
         --primary-color: rgba(32, 77, 98, 255);
         --text-color: #ffffff;
         }
         body {
         font-family: 'Segoe UI', Arial, sans-serif;
         color: var(--text-color);
         background: linear-gradient(to bottom, 
         rgba(88, 140, 164, 1), /* Blu chiaro */
         rgba(17, 56, 78, 1)   /* Blu scuro */
         );
         background-size: cover;
         background-position: center;
         background-repeat: no-repeat;
         background-attachment: fixed;
         margin: 0;
         padding: 5px;
         min-height: 100vh;
         overflow-x: hidden;
         }
         .header-banner {
         text-align: center;
         margin-bottom: 1rem;
         }
         .header-banner img {
         border-radius: 12px;
         width: 1020px;
         height: 100px;
         object-fit: cover;
         }
         .back-button {
         background: rgba(32, 77, 98, 255);
         border: none;
         padding: 1em 1em;
         color: white;
         border-radius: 0.7em;
         font-size: 1.2rem;
         transition: all 0.3s ease;
         cursor: pointer;
         text-align: center;
         display: inline-block; /* Modificato per allineamento orizzontale */
         white-space: nowrap;
         margin: 0 5px; /* Margini laterali ridotti */
         }
         .back-button:hover {
         background: rgba(32, 77, 98, 0.8);
         }
         .print-btn {
         width: 200px; /* Stessa larghezza del bottone sopra */
         margin-left: 20px;
         }
         .table-container {
         max-height: 500px;
         overflow-y: auto;
         margin: 20px auto;
         width: 90%;
         max-width: 900px;
         }
         .search-box {
         margin: 20px auto;
         width: 90%;
         max-width: 900px;
         text-align: center;
         }
         .search-box input {
         padding: 8px;
         width: 200px;
         border-radius: 4px;
         border: 1px solid white;
         background: rgba(255,255,255,0.1);
         color: white;            /* Colore del testo */
         font-size: 16px;        /* Dimensione del testo */
         font-weight: 500;       /* Peso del font */
         outline: none;          /* Rimuove il contorno quando selezionato */
         }
         .search-box input::placeholder {
         color: rgba(255,255,255,0.8);  /* Colore del placeholder */
         opacity: 1;                     /* Opacità del placeholder */
         }
         .data-table {
         width: 100%;
         border-collapse: collapse;
         background: rgba(255, 255, 255, 0.1);
         border-radius: 8px;
         overflow: hidden;
         }
         .data-table th {
         background: rgba(32, 77, 98, 0.9);
         color: white;
         padding: 1rem;
         text-align: center;
         position: sticky;
         top: 0;
         }
         .data-table td {
         padding: 0.8rem;
         text-align: center;
         border-bottom: 1px solid rgba(255, 255, 255, 0.1);
         color: white;
         }
         .button {
         background: rgba(32, 77, 98, 255);
         border: none;
         color: white;
         border-radius: 1em;
         padding: 0.8em 1.2em;
         font-size: 1rem;
         cursor: pointer;
         transition: all 0.3s ease;
         margin: 10px;
         }
         .pagination {
         margin: 20px auto;
         text-align: center;
         }
         .pagination button {
         background: rgba(32, 77, 98, 255);
         border: none;
         color: white;
         padding: 8px 12px;
         margin: 0 3px;
         border-radius: 4px;
         cursor: pointer;
         font-size: 16px;
         min-width: 40px;
         transition: background-color 0.3s ease;
         }
         .pagination button:hover {
         background: rgba(32, 77, 98, 0.8);
         }
         .pagination span {
         display: inline-block;
         padding: 8px 12px;
         font-size: 16px;
         }
         .qr-container {
         display: grid;
         grid-template-columns: repeat(2, 1fr);
         grid-template-rows: repeat(4, 1fr);
         gap: 2mm;
         padding: 3mm;
         width: 194mm;
         margin: 0 auto; /* Mantiene il margine auto */
         justify-content: center; /* Centra la griglia orizzontalmente */
         align-items: center; /* Centra la griglia verticalmente */
         max-width: 90%; /* Limita la larghezza massima */
         position: absolute; /* Posizionamento assoluto */
         left: 50%; /* Sposta al centro orizzontalmente */
         transform: translateX(-50%); /* Aggiusta la posizione al centro esatto */
         }
         .qrcode {
         width: 90mm; /* Ridotto per evitare overflow */
         height: 65mm;
         padding: 3mm;
         margin: 1mm auto;
         border: 1px solid #ccc;
         box-sizing: border-box;
         background: white;
         color: black;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: space-between;
         break-inside: avoid;
         }
         .qrcode h3 {
         font-size: 11pt;
         margin: 1mm 0;
         }
         .qrcode h4 {
         font-size: 10pt;
         margin: 1mm 0;
         }
         .qrcode h5 {
         font-size: 9pt;
         margin: 1mm 0;
         }
         .qrcode img {
         width: 32mm;
         height: 32mm;
         margin: 2mm 0;
         }
         @media print {
         /* Nascondi tutti gli elementi con classe no-print */
         .no-print { 
         display: none !important; 
         }
         /* Rimuovi lo sfondo e i margini del body */
         body { 
         background: none;
         margin: 0;
         padding: 0;
         }
         /* Ottimizza il container dei QR per la stampa */
         .qr-container {
         page-break-after: always;
         padding: 4mm;
         margin: 0 auto;
         }
         /* Imposta i margini della pagina */
         @page {
         margin: 8mm;
         size: A4 portrait;
         }
         }
      </style>
   </head>
   <body>
      <div class="header-banner no-print">
         <img src="./images/BannerCealpi.jpg" alt="Banner">
      </div>
      <div style="margin: 20px;">
         <button class="back-button no-print" onclick="location.href='GestAnagrafiche.php'" style="margin-bottom: 10px;">
         ← Indietro
         </button>
      </div>
      <?php if (!isset($_POST['MM_form1'])): ?>
      <form name="form1" method="post">
         <h2 style="text-align:center;color:white">Seleziona nominativi</h2>
         <div class="search-box no-print">
            <input type="text" id="searchInput" placeholder="Cerca..." onkeyup="filterTable()">
         </div>
         <div class="table-container">
            <table class="data-table" id="nominativiTable">
               <tr>
                  <th>Grado</th>
                  <th>Cognome</th>
                  <th>Nome</th>
                  <th><input type="checkbox" id="selectAll" onclick="toggleAll()"></th>
               </tr>
               <?php while ($row = $Nomi->fetch_assoc()): ?>
               <tr>
                  <td><?= $row['Grado'] ?></td>
                  <td><?= $row['Cognome'] ?></td>
                  <td><?= $row['Nome'] ?></td>
                  <td>
                     <input type="checkbox" name="checkbox[]" value="<?= $row['IDnome'] ?>">
                  </td>
               </tr>
               <?php endwhile; ?>
            </table>
         </div>
         <div style="text-align:center">
            <input type="submit" class="button" name="Submit" value="Visualizza">
            <input type="hidden" name="MM_form1" value="Stampa">
         </div>
      </form>
      <?php else: ?>
      <?php
         if (!empty($_POST['checkbox'])) {
             $ids = array_map(function($id) { return "'$id'"; }, $_POST['checkbox']);
             $idList = implode(',', $ids);
             
             $query_BarCode = "SELECT n.IDnome, n.CF, n.Cognome, n.Nome, g.Grado 
                              FROM pre_elenconomi n 
                              INNER JOIN pre_gradi g ON g.ID = n.IDgrado 
                              WHERE n.IDnome IN ($idList)";
             $BarCode = $PRES_conn->query($query_BarCode);
         ?>
      <div style="text-align: center; margin: 20px auto 30px auto;">
         <form method="post" style="display: inline-block; margin-right: 10px;">
            <button type="submit" class="back-button no-print">⮌ Torna all'elenco</button>
         </form>
         <button class="back-button no-print" onclick="window.print()">🖨️ Stampa</button>
      </div>
      <div class="qr-container">
         <?php
            include('phpqrcode/qrlib.php');
            while ($row = $BarCode->fetch_assoc()):
                $pin = $row['IDnome'] . substr($row['CF'], 6, 2) . substr($row['CF'], 9, 2);
                $qrFile = "phpqrcode/images/{$row['IDnome']}.png";
                QRcode::png($row['CF'], $qrFile, QR_ECLEVEL_L, 3);
            ?>
         <div class="qrcode">
            <h3><?= $Reparto ?></h3>
            <h4>Badge Personale Per Servizio Mensa</h4>
            <h5><?= "{$row['Grado']} {$row['Cognome']} {$row['Nome']}" ?></h5>
            <h5>PIN UTENTE <?= $pin ?></h5>
            <img width="125" src="<?= $qrFile ?>" />
         </div>
         <?php endwhile; ?>
      </div>
      <?php } ?>
      <?php endif; ?>
      <script>
         const rowsPerPage = 10;
         let currentPage = 1;
         
         function filterTable() {
         let input = document.getElementById("searchInput");
         let filter = input.value.toLowerCase();
         let table = document.getElementById("nominativiTable");
         let tr = table.getElementsByTagName("tr");
         
         for (let i = 1; i < tr.length; i++) {
             let visible = false;
             let td = tr[i].getElementsByTagName("td");
             for (let j = 0; j < td.length - 1; j++) {
                 let cell = td[j];
                 if (cell) {
                     let text = cell.textContent || cell.innerText;
                     if (text.toLowerCase().indexOf(filter) > -1) {
                         visible = true;
                         break;
                     }
                 }
             }
             tr[i].style.display = visible ? "" : "none";
         }
         }
         
         function toggleAll() {
         // Ottiene il checkbox principale
         const selectAllCheckbox = document.getElementById('selectAll');
         
         // Ottiene tutti i checkbox della tabella
         const checkboxes = document.querySelectorAll('input[name="checkbox[]"]');
         
         // Imposta lo stato di tutti i checkbox visibili in base allo stato del checkbox principale
         checkboxes.forEach(checkbox => {
             // Controlla se la riga del checkbox è visibile
             if (checkbox.closest('tr').style.display !== 'none') {
                 checkbox.checked = selectAllCheckbox.checked;
             }
         });
         }
      </script>
   </body>
</html>
</body>
</html>