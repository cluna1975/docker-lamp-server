CREATE TABLE users (
    -- Identificadores
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE, -- Identificador público (seguridad por oscuridad)

    -- Credenciales y Datos Básicos
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- NUNCA guardar en texto plano
    username VARCHAR(50) UNIQUE NULL, -- Opcional, dependiendo del negocio

    -- Información Personal (Separar si crece mucho a una tabla 'profiles')
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    
    -- Estados y Roles (Control de acceso simple)
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    
    -- Verificaciones
    email_verified_at TIMESTAMP NULL,

    -- Auditoría y Timestamps (Cruciales)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL, -- Para Soft Deletes (borrado lógico)

    -- Índices para optimizar búsquedas frecuentes
    INDEX idx_email (email),
    INDEX idx_uuid (uuid),
    INDEX idx_status (status)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;