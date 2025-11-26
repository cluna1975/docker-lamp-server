<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página no encontrada (404)</title>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@1.5.7/dist/lottie-player.js"></script>

    <style>
        /* (Tus estilos anteriores estaban bien, déjalos igual) */
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f0f2f5;
            color: #343a40;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
        }
        .container {
            background: white;
            max-width: 500px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        lottie-player {
            width: 100%;
            max-width: 300px; /* Ajustado */
            margin: 0 auto 20px auto;
        }
        h1 { font-size: 3rem; margin: 0; color: #dc3545; }
        h2 { font-size: 1.5rem; margin-top: 10px; }
        p { font-size: 1.1rem; color: #6c757d; margin-bottom: 30px; }
        a {
            display: inline-block; padding: 12px 24px;
            background-color: #0d6efd; color: white;
            text-decoration: none; border-radius: 8px; font-weight: 600;
        }
        a:hover { background-color: #0b5ed7; }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes titleMove {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
            100% {
                transform: translateY(0);
            }
        }

        .container {
            animation: fadeIn 1s ease-out;
        }

        h1 {
            animation: titleMove 3s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <lottie-player
            src="https://assets2.lottiefiles.com/packages/lf20_u1xuufn3.json"
            background="transparent"
            speed="1"
            style="width: 300px; height: 300px;"
            loop
            autoplay>
        </lottie-player>

        <h1>404</h1>
        <h2>¡Ups! Página no encontrada.</h2>
        <p>Parece que el enlace que seguiste está roto o la página ha sido movida a otro lugar.</p>
        <a href="/">Regresar al Inicio</a>
    </div>
</body>
</html>