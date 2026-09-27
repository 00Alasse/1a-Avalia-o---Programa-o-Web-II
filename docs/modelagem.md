Set-Content -Path "docs/modelagem.md" -Value '# Modelagem de Dados — Clínica Veterinária Pata Amiga

## Entidades e Atributos

### usuarios
Controle de acesso da equipe clínica (RF16).
- id: INT (PK, AUTO_INCREMENT)
- nome: VARCHAR(100) NOT NULL
- email: VARCHAR(150) NOT NULL UNIQUE
- senha: VARCHAR(255) NOT NULL
- criado_em: TIMESTAMP DEFAULT CURRENT_TIMESTAMP

### especies
Espécies atendidas (RF01).
- id: INT (PK, AUTO_INCREMENT)
- nome: VARCHAR(255) NOT NULL UNIQUE
- observacoes: TEXT NULL

### tutores
Proprietários dos animais (RF02).
- id: INT (PK, AUTO_INCREMENT)
- nome: VARCHAR(255) NULL
- cpf: VARCHAR(255) NOT NULL UNIQUE
- telefone: VARCHAR(255) NULL
- email: VARCHAR(255) NOT NULL UNIQUE
- endereco: VARCHAR(255) NULL
- data_cliente: DATE NULL

### veterinarios
Corpo clínico da clínica (RF03).
- id: INT (PK, AUTO_INCREMENT)
- nome: VARCHAR(255) NULL
- crmv: VARCHAR(255) NULL
- especialidade: VARCHAR(255) NULL
- telefone: VARCHAR(255) NULL
- ativo: TINYINT(1) DEFAULT 1

### procedimentos
Procedimentos clínicos oferecidos (RF04).
- id: INT (PK, AUTO_INCREMENT)
- descricao: VARCHAR(255) NULL
- valor: DECIMAL(12,2) NULL
- duracao_minutos: INT NULL

### animais
Pacientes da clínica (RF05, RF06).
- id: INT (PK, AUTO_INCREMENT)
- nome: VARCHAR(255) NULL
- raca: VARCHAR(255) NULL
- data_nascimento: DATE NULL
- sexo: VARCHAR(255) NULL
- peso: DECIMAL(12,2) NULL
- castrado: TINYINT(1) NULL
- observacoes: TEXT NULL
- tutor_id: INT (FK tutores)
- especie_id: INT (FK especies)

### atendimentos
Consultas e atendimentos (RF09, RF10, RF11, RF18).
- id: INT (PK, AUTO_INCREMENT)
- animal_id: INT (FK animais)
- veterinario_id: INT (FK veterinarios)
- procedimento_id: INT (FK procedimentos)
- data_hora: DATETIME NULL
- valor_cobrado: DECIMAL(12,2) NULL
- observacoes_clinicas: TEXT NULL
- situacao: VARCHAR(255) NULL
- usuario_id: INT (FK usuarios, autoria)

### vacinas
Vacinas aplicadas e retornos (RF14, RF15, RF18).
- id: INT (PK, AUTO_INCREMENT)
- animal_id: INT (FK animais)
- veterinario_id: INT (FK veterinarios)
- nome_vacina: VARCHAR(255) NULL
- lote: VARCHAR(255) NULL
- data_aplicacao: DATE NULL
- data_retorno: DATE NULL
- usuario_id: INT (FK usuarios, autoria)

## Relacionamentos
- tutores 1:N animais
- especies 1:N animais
- animais 1:N atendimentos
- veterinarios 1:N atendimentos
- procedimentos 1:N atendimentos
- animais 1:N vacinas
- veterinarios 1:N vacinas
- usuarios 1:N atendimentos (autoria)
- usuarios 1:N vacinas (autoria)' -Encoding utf8