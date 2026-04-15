<!DOCTYPE HTML>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <META http-equiv="refresh" content="5;URL=Home.php">
    <title>Errore Terminale</title>
    <style>
        :root {
            --primary-color: #4051b5;
            --text-color: #FFFFFF;
            --bg-overlay: rgba(0, 0, 0, 0.5);
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: var(--text-color);
            background-image: url('images/Img00.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-overlay);
            z-index: -1;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin: 40px auto;
            max-width: 800px;
            text-align: center;
        }

        .banner {
            max-width: 800px;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
        }

        h2 {
            font-size: 24px;
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .warning-icon {
            width: 120px;
            height: auto;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="banner">
        <img src="images/BannerCealpi.jpg" width="800" height="113" alt="Banner">
    </div>
    <div class="container">
        <img src="images/attenzione.png" class="warning-icon" alt="Attenzione">
        <h2>Terminale non abilitato al servizio.</h2>
    </div>
</body>
</html>
