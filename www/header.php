<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'CRUD Usuarios' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 0; display: flex; flex-direction: column; min-height: 100vh; }
        .main-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header-content { max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; }
        .logo { font-size: 24px; font-weight: bold; }
        .nav-links a { color: white; text-decoration: none; margin: 0 15px; padding: 8px 16px; border-radius: 20px; transition: background 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,0.2); }
        .content { padding: 20px; flex: 1; }
        @media (max-width: 768px) {
            .header-content { flex-direction: column; gap: 15px; text-align: center; }
            .nav-links a { margin: 0 8px; font-size: 14px; }
        }

        .container { background: white; padding: 30px 40px; border-radius: 10px; width: 100%; max-width: 800px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin: 20px auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header h2 { margin: 0; color: #333; }
        .header .btn-crear {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .header .btn-crear:hover { background: #0056b3; }
        .header .btn-crear i { margin-right: 8px; }
        
        .msg { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .msg-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:hover { background-color: #f1f1f1; }
        .acciones a, .acciones button {
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 4px;
            color: white;
            font-size: 0.9em;
            border: none;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn-editar { background-color: #ffc107; }
        .btn-eliminar { background-color: #dc3545; }
        .no-usuarios { text-align: center; color: #777; padding: 20px; }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .container { padding: 15px; margin: 10px; }
            .header { flex-direction: column; gap: 15px; text-align: center; }
            table { font-size: 14px; }
            th, td { padding: 8px 5px; }
            .acciones form { margin: 2px 0; }
            .acciones button { padding: 4px 6px; font-size: 12px; }
            th:nth-child(4), td:nth-child(4) { display: none; } /* Ocultar fecha en móvil */
        }
        
        @media (min-width: 1200px) {
            .container { max-width: 1000px; }
            th, td { padding: 15px; }
        }
        .header-right {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .dt-buttons .btn-export {
            padding: 10px 15px;
            background: #6c757d;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
        }
        .dt-buttons .btn-export:hover {
            background: #5a6268;
        }
        .dt-buttons .btn-export i {
            margin-right: 8px;
        }
        .dt-buttons {
            margin-bottom: 15px;
        }

        /* Estilos para el nuevo footer */
        .main-footer {
            background-color: #343a40;
            color: white;
            padding: 30px 20px;
            text-align: center;
            margin-top: 40px;
        }
        .footer-content {
            max-width: 1000px;
            margin: 0 auto;
        }
        .social-icons {
            margin-top: 15px;
        }
        .social-icons a {
            color: white;
            margin: 0 10px;
            font-size: 20px;
            text-decoration: none;
            transition: color 0.3s;
        }
        .social-icons a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <i class="fa-solid fa-users"></i> CRUD Usuarios
            </div>
            <nav class="nav-links">
                <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-list"></i> Lista
                </a>
                <a href="crear.php" class="<?= basename($_SERVER['PHP_SELF']) == 'crear.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-plus"></i> Crear
                </a>
            </nav>
        </div>
    </header>
    <div class="content">