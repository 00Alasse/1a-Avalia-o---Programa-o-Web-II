# Relatório — Sistema de Clínica

## (a) Descrição do sistema
O sistema gerencia o fluxo de consultas de uma clínica médica.
Permite cadastrar pacientes e médicos, agendar consultas vinculando
ambos, e controlar o status de cada atendimento.
O acesso é protegido por login com diferentes perfis de usuário.

## (b) Modelo de dados
Três tabelas principais:

- pacientes: id, nome, cpf, data_nascimento, telefone, email
- medicos: id, nome, crm, especialidade
- consultas: id, paciente_id (FK), medico_id (FK), data_hora, motivo, status

Justificativas:
- Separamos pacientes e médicos porque são entidades com ciclos de vida diferentes
- A tabela consultas resolve o N:N entre pacientes e médicos
- O campo status é textual para permitir futuros estados
- Cancelar muda o status, não apaga o registro, preservando o histórico

## (c) Instalação e execução
1. Inicie o MySQL no XAMPP
2. Configure configuracoes/banco.php
3. Execute: php instalar.php
4. Execute: php console.php auth:install
5. Execute o scaffold de cada entidade
6. Inicie o servidor: php -S localhost:8000 roteador.php
7. Acesse: http://localhost:8000

## (d) Funcionalidades implementadas
[Preencher após o CRUD estar pronto]
- CRUD de pacientes: criar, listar, editar, excluir
- CRUD de médicos: criar, listar, editar, excluir
- CRUD de consultas: agendar, listar, alterar status
- Validações: [listar as regras do método validar()]
-