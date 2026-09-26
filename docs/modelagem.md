# Modelagem de Dados — Clínica

## Entidades

### pacientes
| Campo | Tipo | Restrição |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| nome | VARCHAR(255) | NOT NULL |
| cpf | VARCHAR(14) | NOT NULL, UNIQUE |
| data_nascimento | DATE | NOT NULL |
| telefone | VARCHAR(20) | NULL |
| email | VARCHAR(255) | NULL |
| criado_em | DATETIME | DEFAULT NOW() |

### medicos
| Campo | Tipo | Restrição |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| nome | VARCHAR(255) | NOT NULL |
| crm | VARCHAR(20) | NOT NULL, UNIQUE |
| especialidade | VARCHAR(100) | NOT NULL |
| criado_em | DATETIME | DEFAULT NOW() |

### consultas
| Campo | Tipo | Restrição |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| paciente_id | INT | FK → pacientes.id |
| medico_id | INT | FK → medicos.id |
| data_hora | DATETIME | NOT NULL |
| motivo | TEXT | NULL |
| status | VARCHAR(20) | DEFAULT 'agendada' |
| criado_em | DATETIME | DEFAULT NOW() |

## Relações
- pacientes 1:N consultas
- medicos 1:N consultas
- consulta resolve o N:N entre pacientes e medicos

## Decisões de design
- CPF e CRM com UNIQUE para evitar duplicatas
- status textual (agendada/realizada/cancelada) em vez de boolean
  porque podem surgir mais estados no futuro
- Cancelar muda o status, não apaga o registro (preserva histórico) 
