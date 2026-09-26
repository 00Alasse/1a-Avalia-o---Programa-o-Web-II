-- O framework comeca sem tabelas da aplicacao.
-- Use: php console.php scaffold:crud Nome campo:string

CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `especies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NOT NULL UNIQUE,
    `observacoes` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
