-- 1. DESACTIVAR VERIFICACIÓN DE LLAVES (Truco Pro para evitar errores de borrado)
SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================
-- DEFINICIÓN DE ESTRUCTURA (DDL)
-- ==========================================

-- 1. Tabla ROLES
DROP TABLE IF EXISTS roles; -- Limpia si existe vieja
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabla USUARIOS
DROP TABLE IF EXISTS usuarios;
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabla USUARIO_ROLES
DROP TABLE IF EXISTS usuario_roles;
CREATE TABLE IF NOT EXISTS usuario_roles (
    usuario_id INT NOT NULL,
    rol_id INT NOT NULL,
    PRIMARY KEY (usuario_id, rol_id),
    CONSTRAINT fk_user_role_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_role_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- 4. Tabla MENUS
DROP TABLE IF EXISTS menus;
CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    ruta VARCHAR(100),
    icono VARCHAR(50),
    padre_id INT NULL,
    orden INT DEFAULT 0,
    CONSTRAINT fk_menu_padre FOREIGN KEY (padre_id) REFERENCES menus(id) ON DELETE SET NULL
);

-- 5. Tabla MENU_ACCIONES
DROP TABLE IF EXISTS menu_acciones;
CREATE TABLE IF NOT EXISTS menu_acciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    CONSTRAINT fk_accion_menu FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);

-- 6. Tabla ROL_PERMISOS
DROP TABLE IF EXISTS rol_permisos;
CREATE TABLE IF NOT EXISTS rol_permisos (
    rol_id INT NOT NULL,
    menu_accion_id INT NOT NULL,
    PRIMARY KEY (rol_id, menu_accion_id),
    CONSTRAINT fk_permiso_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_permiso_accion FOREIGN KEY (menu_accion_id) REFERENCES menu_acciones(id) ON DELETE CASCADE
);

-- ==========================================
-- REACTIVAR VERIFICACIÓN DE LLAVES
-- ==========================================
SET FOREIGN_KEY_CHECKS = 1;