<?php
/**
 * Login per la prenotazione pasti
 * 
 * @version 1.0
 */
session_start();
?>
<!DOCTYPE html>
<html lang="it">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <title>Login Prenotazione Pasti</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="style/fonta/css/all.css">      <style>
         :root {
            --primary-color: #4a8799;
            --primary-light: #69a6b7;
            --primary-dark: #17364e;
            --accent-color: #cfe6ee;
            --text-light: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.2);
            --background-gradient: linear-gradient(to bottom, rgba(88, 140, 164, 1), rgba(17, 56, 78, 1));
            --transition-speed: 0.3s;
            --border-radius: 12px;
         }
         
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
         }
         
         body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; 
            padding-top: 5vh; 
            align-items: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background-gradient);
            background-repeat: no-repeat;
            background-attachment: fixed;
         }         .header-title {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 2rem;
            text-align: center;
            z-index: 1000;
            background: rgba(32, 77, 98, 0.3);
            box-shadow: 0 4px 6px var(--shadow-color);
            backdrop-filter: blur(5px);
         }
         
         .header-title-text {
            font-size: clamp(1.5rem, 5vw, 3rem);
            font-weight: 900;
            color: var(--text-light);
            letter-spacing: 3px;
            text-shadow: 2px 4px 6px var(--shadow-color);
            font-family: system-ui, -apple-system, 'Arial Black', sans-serif;
            animation: fadeInDown 1s ease-out;
         }         .main-content {
            background: rgba(32, 77, 98, 0.3);
            width: 90%;
            max-width: 800px;
            margin: 8.5rem auto; 
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 32px var(--shadow-color);
            backdrop-filter: blur(10px);
         }
         
         .logo-container { 
            margin-bottom: 2rem; 
         }
         
         .main-logo {
            font-size: 4rem;
            color: var(--accent-color);
            text-shadow: 2px 2px 4px var(--shadow-color);
            animation: float 3s ease-in-out infinite;
         }         .testo-3d {
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            text-transform: uppercase;
            font-weight: 700;
            color: var(--accent-color);
            text-shadow: 2px 2px 4px var(--shadow-color);
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
         }
         
         .form-subtitle {
            font-size: 1rem;
            color: var(--text-light);
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.8rem;
         }         .input-wrapper {
            position: relative;
            margin: 1.5rem auto;
            width: 90%;
            max-width: 300px;
         }
         
         .input-group {
            width: 100%;
            max-width: 300px;
         }
         
         input[type="text"],
         input[type="password"] {
            width: 100%;
            padding: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius);
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            transition: all var(--transition-speed) ease;
         }
         
         input[type="text"]:focus,
         input[type="password"]:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 8px rgba(207, 230, 238, 0.4);
         }
         
         input[type="text"]::placeholder,
         input[type="password"]::placeholder {
            color: rgba(255, 255, 255, 0.7);
         }         /* Stile unificato per i pulsanti */
         .btn {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            border: none;
            padding: 1rem 2rem;
            color: var(--text-light);
            border-radius: var(--border-radius);
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            min-width: 200px;
            margin: 1.5rem auto;
            box-shadow: 0 4px 15px rgba(74, 135, 153, 0.3);
            transition: all var(--transition-speed) ease;
            display: block;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
         }
         
         .btn:hover {
            transform: translateY(-3px);
            background: linear-gradient(135deg, #7ab8ca, #5a97a9);
            box-shadow: 0 8px 25px rgba(74, 135, 153, 0.5);
         }
         
         .btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 10px rgba(74, 135, 153, 0.3);
         }
         
         .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
         }
         
         .btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
         }
         
         @keyframes ripple {
            0% {
               transform: scale(0, 0);
               opacity: 0.5;
            }
            20% {
               transform: scale(25, 25);
               opacity: 0.3;
            }
            100% {
               transform: scale(40, 40);
               opacity: 0;
            }
         }         .sidebar {
            position: fixed;
            bottom: -150px;
            left: 0;
            width: 100%;
            height: 150px;
            background: rgba(32, 77, 98, 0.3);
            backdrop-filter: blur(10px);
            padding: 1rem;
            transition: var(--transition-speed) ease;
            z-index: 1001;
            box-shadow: 0 -5px 15px var(--shadow-color);
         }
         
         .sidebar.active {
            bottom: 0;
         }
         
         .toggle-btn {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            z-index: 1002;
            transition: var(--transition-speed) ease;
            box-shadow: 0 4px 15px var(--shadow-color);
            display: flex;
            align-items: center;
            justify-content: center;
         }
         
         .toggle-btn:hover {
            transform: translateX(-50%) scale(1.1);
         }         .sidebar-menu {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            height: 100%;
         }
         
         .menu-item { 
            flex: 1; 
            text-align: center; 
         }
         
         .menu-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--accent-color);
            text-decoration: none;
            padding: 0.8rem;
            border-radius: var(--border-radius);
            transition: var(--transition-speed) ease;
         }
         
         .menu-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
         }
         
         .menu-icon {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
         }
         
         .menu-text {
            font-size: 0.9rem;
         }         @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0); }
         }
         
         @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
         }
         
         .error-message {
            background-color: rgba(220, 53, 69, 0.2);
            color: var(--text-light);
            padding: 12px;
            border-radius: var(--border-radius);
            margin: 1.3em auto 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.2);
            animation: fadeIn 0.5s ease-out;
         }
         
         @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
         }         @media (max-width: 768px) {
            .main-content {
               width: 95%;
               padding: 2rem 1rem;
               margin: 1rem auto;
            }
            
            .testo-3d {
               font-size: 1.5rem;
               letter-spacing: 0.5px;
            }
            
            .form-subtitle { font-size: 0.9rem; }
            .input-group { max-width: 280px; }
            .menu-text { font-size: 0.8rem; }
            
            .toggle-btn {
               bottom: 1.5rem;
               width: 45px;
               height: 45px;
            }
            
            .sidebar {
               height: 180px;
               bottom: -180px;
            }
         }
           @media (prefers-reduced-motion: reduce) {
            .btn:hover, .menu-link:hover {
               transform: none;
            }
            
            .main-logo {
               animation: none;
            }
            
            html body * {
               transition: none;
               animation: none;
            }
         }
      </style>
   </head>
   <body>      <button class="toggle-btn" title="Apri menu di navigazione">
         <i class="fas fa-bars"></i>
      </button>
      <div class="sidebar">
         <div class="sidebar-menu">
            <div class="menu-item">
               <a href="LoginMassiva.php" class="menu-link">
               <i class="fas fa-users menu-icon"></i>
               <span class="menu-text">Accesso Operatori</span>
               </a>
            </div>
            <div class="menu-item">
               <a href="LoginVTV.php" class="menu-link">
               <i class="fas fa-id-card menu-icon"></i>
               <span class="menu-text">Ufficio VTV</span>
               </a>
            </div>
            <div class="menu-item">
               <a href="SelectPostazione.php" class="menu-link">
               <i class="fas fa-qrcode menu-icon"></i>
               <span class="menu-text">Posto Distribuzione</span>
               </a>
            </div>
            <div class="menu-item">
               <a href="ADMIN/LoginADMIN.php" class="menu-link">
               <i class="fas fa-user-shield menu-icon"></i>
               <span class="menu-text">Admin</span>
               </a>
            </div>
         </div>
      </div>
      <div class="header-title">
         <span class="header-title-text">Centro Ospedaliero Militare di Milano</span>
      </div>
      <div class="main-content">
         <div>
            <div class="logo-container">
               <i class="fas fa-utensils main-logo"></i>
            </div>
            <h1 class="testo-3d">Servizio di prenotazione pasti</h1>
            <p class="form-subtitle">PER EFFETTUARE LA PRENOTAZIONE INSERIRE L'ID O IL CODICE FISCALE</p>            <form action="ValidateLoginPrenota.php" method="POST" name="form1">
               <div class="input-wrapper">
                  <input class="input-group" placeholder="ID o C.F." name="Tx_cert" type="password" id="Tx_cert" autofocus>
               </div>
               <button type="submit" id="btn_prenota" class="btn">
                  <span>Prenota</span>
               </button>
            </form>
         </div>
         <?php if(isset($_SESSION['login_error'])): ?>
            <div class="error-message">
               <?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
            </div>
         <?php endif; ?>
      </div>      <script>
         document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.toggle-btn');
            
            // Gestione menu mobile
            toggleBtn.addEventListener('click', function() {
               sidebar.classList.toggle('active');
               this.classList.toggle('active');
               
               // Effetto ripple sul pulsante
               const ripple = document.createElement('span');
               ripple.classList.add('ripple');
               this.appendChild(ripple);
               
               setTimeout(() => {
                  ripple.remove();
               }, 600);
            });
            
            // Chiusura menu quando si clicca fuori
            document.addEventListener('click', function(event) {
               if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target) && sidebar.classList.contains('active')) {
                  sidebar.classList.remove('active');
                  toggleBtn.classList.remove('active');
               }
            });

            // Focus automatico sul campo di input
            const inputField = document.getElementById('Tx_cert');
            if (inputField) {
               inputField.focus();
            }
            
            // Aggiunta animazione ai link del menu
            document.querySelectorAll('.menu-link').forEach((link, index) => {
               link.style.animationDelay = `${index * 0.1}s`;
            });
         });
      </script>
   </body>
</html>