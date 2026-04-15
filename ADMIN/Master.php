<?php require_once('../Connections/MyPresenze.php'); ?>
<?php
session_start();

if (!($_SESSION['UserID'])) {
    unset($_SESSION['UserID']);
    header("Location: ErrorLoginADMIN.php");
}

$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
    $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
    session_destroy();
    $logoutGoTo = "../Home.php";
    if ($logoutGoTo) {
        header("Location: $logoutGoTo");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master</title>
    <link rel="stylesheet" href="\ACCESSI\style\fonta\css\all.css">
    <style>
       /* Reset e stili base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(to bottom, 
                rgba(88, 140, 164, 1), /* Blu chiaro */
                rgba(17, 56, 78, 1)   /* Blu scuro */
            );
    background-size: cover;
    min-height: 100vh;
    display: flex;
    overflow: hidden;
}

/* Sidebar */
.sidebar {
    width: 300px;
    background: rgba(7, 64, 107, 0.95);
    height: 100vh;
    padding: 20px;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

/* Menu Items */
.menu-item {
    padding: 15px;
    margin: 8px 0;
    color: white;
    cursor: pointer;
    border-radius: 10px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid transparent;
}

.menu-item:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateX(5px);
}

.menu-item.active {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
}

.menu-item i {
    font-size: 1.5rem;
    width: 30px;
    text-align: center;
}

.menu-item span {
    font-size: 1.1rem;
    font-weight: 500;
}

/* Home menu item specifico */
.menu-item:first-child {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 20px;
    padding-bottom: 20px;
}

.menu-item:first-child:hover {
    background: rgba(255, 255, 255, 0.15);
}

/* Area contenuti principale */
.main-content {
    flex: 1;
    padding: 2px;
    height: 100vh;
    display: flex;
    flex-direction: column;
    background: transparent;
    overflow: auto; /* Aggiunto overflow auto */
}

#content-container {
    flex: 1;
    margin: 0;
    padding: 0;
    height: calc(100vh - 2px);
    width: 100%;
    overflow: visible; /* Modificato da hidden a visible */
    background: transparent;
}

/* iframe */
#contentFrame {
    width: 100%;
    height: 100%;
    border: none;
    background: transparent !important;
    overflow-y: scroll; /* Aggiunto scroll verticale */
    overflow-x: hidden; /* Nasconde scroll orizzontale */
    transform: scale(1.06);
    transform-origin: top left;
    margin: -2px;
}

/* Pannelli contenuto */
.content-panel {
    display: none;
    height: 100%;
    margin: 0;
    padding: 0;
    background: transparent;
}

.content-panel.active {
    display: block;
    background: transparent;
}

/* Media Queries */
@media (max-width: 768px) {
    body {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        max-height: 300px;
    }

    .main-content {
        height: calc(100vh - 300px);
    }

    #content-container {
        height: 100%;
    }
}

/* Stile della scrollbar per l'iframe */
#contentFrame::-webkit-scrollbar {
    width: 8px;
}

#contentFrame::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

#contentFrame::-webkit-scrollbar-thumb {
    background: rgba(7, 64, 107, 0.6);
    border-radius: 4px;
}

#contentFrame::-webkit-scrollbar-thumb:hover {
    background: rgba(7, 64, 107, 0.8);
}
    </style>
</head>
<body>
    <div class="sidebar">
    <div class="menu-item" onclick="location.href='/ACCESSI/LoginPrenota.php'">
        <i class="fas fa-home"></i>
        <span>Torna alla Home</span>
    </div>
        <div class="menu-item" data-panel="anagrafiche">
            <i class="fas fa-users"></i>
            <span>Gestione anagrafiche</span>
        </div>
        <div class="menu-item" data-panel="db">
            <i class="fas fa-database"></i>
            <span>Gestione DB</span>
        </div>
        <div class="menu-item" data-panel="ip">
            <i class="fas fa-network-wired"></i>
            <span>Gestione IP client</span>
        </div>
        <div class="menu-item" data-panel="uo">
            <i class="fas fa-building"></i>
            <span>Gestione U.O.</span>
        </div>
        <div class="menu-item" data-panel="setup">
            <i class="fas fa-cogs"></i>
            <span>Impostazioni</span>
        </div>
        <div class="menu-item" data-panel="moduo">
            <i class="fas fa-edit"></i>
            <span>Modifica U.O.</span>
        </div>
        <div class="menu-item" data-panel="turni">
            <i class="fas fa-clock"></i>
            <span>Area Operatori</span>
        </div> 
    </div>

    <div class="main-content">
    <div id="content-container" class="content-panel active">
    <iframe id="contentFrame" 
        name="contentFrame" 
        frameborder="0"
        sandbox="allow-same-origin allow-scripts allow-forms allow-modals"
        onload="this.style.height = this.contentWindow.document.documentElement.scrollHeight + 'px';">
</iframe>
    </div>
</div>      
    <script>
        // Aggiorna lo script esistente
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function() {
        // Rimuovi active da tutti i menu items
        document.querySelectorAll('.menu-item').forEach(i => {
            i.classList.remove('active');
        });
        
        // Aggiungi active al menu item cliccato
        this.classList.add('active');
        
        // Ottieni il pannello ID e carica la pagina corrispondente
        const panelId = this.getAttribute('data-panel');
        const frame = document.getElementById('contentFrame');
        
        // Mappa degli URL per ogni pannello
        const urlMap = {
            'anagrafiche': 'GestAnagrafiche.php',
            'db': 'GestDB.php',
            'ip': 'GestIP.php',
            'uo': 'GestUO.php',
            'setup': 'Setup.php',
            'moduo': 'ModUO.php',
            'turni': 'GestAnagrafiche2.php'
        };
        
        // Carica la pagina nell'iframe
        if (urlMap[panelId]) {
            frame.src = urlMap[panelId];
            
            // Aggiungi un gestore per l'evento load dell'iframe
            frame.onload = function() {
                // Intercetta tutti i link nella pagina caricata
                try {
                    const links = frame.contentDocument.getElementsByTagName('a');
                    Array.from(links).forEach(link => {
                        link.target = 'contentFrame';
                    });
                    
                    // Intercetta tutti i form
                    const forms = frame.contentDocument.getElementsByTagName('form');
                    Array.from(forms).forEach(form => {
                        form.target = 'contentFrame';
                    });
                } catch (e) {
                    console.log('Cross-origin restrictions prevented modification');
                }
            };
        }
    });
});

// Funzione per impedire l'apertura in nuove schede
function preventNewWindow(event) {
    if (event.target.tagName === 'A' && event.target.target === '_blank') {
        event.preventDefault();
        document.getElementById('contentFrame').src = event.target.href;
    }
}

// Aggiungi il listener al documento
document.addEventListener('click', preventNewWindow, true);
    </script>
</body>
</html>