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

CREATE TABLE IF NOT EXISTS `animais` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NULL,
    `raca` VARCHAR(255) NULL,
    `data_nascimento` DATE NULL,
    `sexo` VARCHAR(255) NULL,
    `peso` DECIMAL(12,2) NULL,
    `castrado` TINYINT(1) NULL,
    `observacoes` TEXT NULL,
    `tutor_id` INT NULL,
    `especie_id` INT NULL,
    CONSTRAINT fk_animais_tutor_id FOREIGN KEY (`tutor_id`) REFERENCES `tutores`(`id`),
    CONSTRAINT fk_animais_especie_id FOREIGN KEY (`especie_id`) REFERENCES `especies`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `atendimentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `animal_id` INT NULL,
    `veterinario_id` INT NULL,
    `procedimento_id` INT NULL,
    `data_hora` DATETIME NULL,
    `valor_cobrado` DECIMAL(12,2) NULL,
    `observacoes_clinicas` TEXT NULL,
    `situacao` VARCHAR(255) NULL,
    `usuario_id` INT NULL,
    CONSTRAINT fk_atendimentos_animal_id FOREIGN KEY (`animal_id`) REFERENCES `animais`(`id`),
    CONSTRAINT fk_atendimentos_veterinario_id FOREIGN KEY (`veterinario_id`) REFERENCES `veterinarios`(`id`),
    CONSTRAINT fk_atendimentos_procedimento_id FOREIGN KEY (`procedimento_id`) REFERENCES `procedimentos`(`id`),
    CONSTRAINT fk_atendimentos_usuario_id FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `vacinas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `animal_id` INT NULL,
    `veterinario_id` INT NULL,
    `nome_vacina` VARCHAR(255) NULL,
    `lote` VARCHAR(255) NULL,
    `data_aplicacao` DATE NULL,
    `data_retorno` DATE NULL,
    `usuario_id` INT NULL,
    CONSTRAINT fk_vacinas_animal_id FOREIGN KEY (`animal_id`) REFERENCES `animais`(`id`),
    CONSTRAINT fk_vacinas_veterinario_id FOREIGN KEY (`veterinario_id`) REFERENCES `veterinarios`(`id`),
    CONSTRAINT fk_vacinas_usuario_id FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;