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

CREATE TABLE IF NOT EXISTS `tutores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NULL,
    `cpf` VARCHAR(255) NOT NULL UNIQUE,
    `telefone` VARCHAR(255) NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `endereco` VARCHAR(255) NULL,
    `data_cliente` DATE NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `veterinarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NULL,
    `crmv` VARCHAR(255) NULL,
    `especialidade` VARCHAR(255) NULL,
    `telefone` VARCHAR(255) NULL,
    `ativo` TINYINT(1) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `procedimentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `descricao` VARCHAR(255) NULL,
    `valor` DECIMAL(12,2) NULL,
    `duracao_minutos` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
