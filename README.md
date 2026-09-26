# Sistema de Clínica

Sistema web para gerenciamento de consultas médicas, desenvolvido com o framework MVC didático em PHP.

## O que o sistema faz

- Cadastro e listagem de pacientes
- Cadastro e listagem de médicos
- Agendamento de consultas vinculando paciente e médico
- Controle de status da consulta (agendada / realizada / cancelada)
- Login com controle de acesso por perfil

## Como instalar

### Pré-requisitos
- PHP 8.1 ou superior
- MySQL/MariaDB (XAMPP recomendado)

### Passos
1. Clone o repositório: git clone https://github.com/00Alasse/1a-Avalia-o---Programa-o-Web-II
2. Inicie o MySQL no XAMPP
3. Configure configuracoes/banco.php com seu usuário e senha
4. Execute: php instalar.php
5. Execute: php console.php auth:install
6. Execute o scaffold de cada entidade (ver seção de comandos)
7. Inicie o servidor: php -S localhost:8000 roteador.php
8. Acesse: http://localhost:8000

## Comandos usados

php instalar.php
php console.php auth:install
php console.php scaffold:crud pacientes nome:string cpf:string data_nascimento:date telefone:string email:string --auth
php console.php scaffold:crud medicos nome:string crm:string especialidade:string --auth
php console.php scaffold:crud consultas data_hora:datetime motivo:text status:string paciente_id:belongs_to=pacientes medico_id:belongs_to=medicos --auth
php console.php db:semear pacientes 10
php console.php db:semear medicos 5

## Modelo de dados

Veja docs/modelagem.md para o diagrama completo.

- pacientes: id, nome, cpf, data_nascimento, telefone, email
- medicos: id, nome, crm, especialidade
- consultas: id, paciente_id (FK), medico_id (FK), data_hora, motivo, status

## Usuários de teste

| Usuário | Senha | Perfil |
|---|---|---|
| admin@clinica.br | 123456 | administrador |
| teste@clinica.br | 123456 | usuario |

## Autores

[Seu nome] e [Nome da Pessoa 1] — [Turma] — [Ano]