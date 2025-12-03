<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'CRUD Usuarios' ?></title>
    
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <i class="fa-solid fa-users-gear"></i> 
                <span>CRUD System</span>
            </div>
            <nav class="nav-links">
                <a href="/users/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'users/index.php') !== false ? 'active' : '' ?>">
                    <i class="fa-solid fa-list"></i> Lista
                </a>
                <a href="/users/crear.php" class="<?= strpos($_SERVER['PHP_SELF'], 'users/crear.php') !== false ? 'active' : '' ?>">
                    <i class="fa-solid fa-plus"></i> Crear
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/auth/logout.php" class="btn-logout-nav">
                        <i class="fa-solid fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="/auth/login.php" class="<?= strpos($_SERVER['PHP_SELF'], 'auth/login.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <div class="content">