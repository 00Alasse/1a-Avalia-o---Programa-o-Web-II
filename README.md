# Clínica Veterinária Pata Amiga 🐾

Sistema web completo para gerenciamento de atendimentos clínicos, controle de vacinação e prontuários da **Clínica Veterinária Pata Amiga**, desenvolvido em conformidade com o padrão arquitetural MVC no Framework Didático em PHP, com persistência MySQL e controle de versão rigoroso via Git.

---

## 📌 1. O que o sistema faz

O sistema atende a todas as necessidades operacionais e clínicas da equipe:

* **Cadastros de Apoio (Módulo A)**:
  * **Espécies (RF01)**: Cadastro e manutenção de espécies com controle estrito de duplicidade no Model.
  * **Tutores (RF02)**: Cadastro completo dos proprietários com validação de unicidade de CPF e e-mail.
  * **Veterinários (RF03)**: Cadastro com CRMV, telefone e indicador de status ativo/inativo — profissionais inativos são bloqueados para novos agendamentos.
  * **Procedimentos (RF04)**: Catálogo de serviços com valor de tabela e duração estimada em minutos.
* **Animais & Prontuário Completo (Módulo B)**:
  * **Registro e Vínculo (RF05 / RF06)**: Cada animal possui vínculo obrigatório a exatamente um tutor e uma espécie, selecionados exclusivamente via campos `<select>` no formulário.
  * **Prontuário Detalhado (RF07)**: Tela de visualização com os dados do tutor, idade calculada dinamicamente pelo PHP a partir da data de nascimento, histórico completo de atendimentos e histórico de vacinas aplicadas.
  * **Pesquisa Combinada (RF08)**: Filtro dinâmico na listagem que permite buscar simultaneamente por nome do animal, espécie e tutor.
* **Atendimentos Clínicos & Regras de Negócio (Módulo C)**:
  * **Registro de Consultas (RF09)**: Vínculo entre animal, veterinário e procedimento, registrando data/hora, valor efetivamente cobrado, observações e situação (`agendado`, `realizado`, `cancelado`).
  * **Validação contra Conflito de Horário (RF10)**: Recusa automática de agendamentos no mesmo dia e horário para o mesmo profissional veterinário, preservando os dados digitados e exibindo mensagem amigável de erro.
  * **Bloqueio de Agendamento Retroativo (RF11)**: Recusa agendamentos com data no passado, permitindo datas retroativas apenas se o atendimento já constar como `realizado`.
  * **Filtros e Totalizadores (RF12 / RF13)**: Pesquisa por data, veterinário e situação com exibição dinâmica da quantidade total de atendimentos e soma financeira dos valores filtrados.
* **Controle de Vacinas & Alertas (Módulo D)**:
  * **Registro Vacinal (RF14)**: Controle de lote, data de aplicação, data prevista de retorno e veterinário responsável.
  * **Painel de Alertas na Home (RF15)**: Destaque na tela inicial para retornos de vacina vencidos e doses com vencimento nos próximos 30 dias, com contagem e links diretos para a ficha do paciente.
* **Acesso, Segurança & Autoria (Módulo E)**:
  * **Controle de Acesso (RF16 / RF17)**: Autenticação obrigatória para acesso às rotas de cadastro, edição, exclusão e emissão de relatórios.
  * **Autoria Inviolável por Sessão (RF18)**: Todo atendimento e vacina registra o `usuario_id` obtido diretamente da sessão ativa (`usuario_id()`), impossibilitando a manipulação via formulário.
  * **Navegação Segura e Menus Dinâmicos (RF19 / RF20 / RF25)**: Destruição segura de sessão no logout com cabeçalhos anti-cache e alternância de navegação entre visitante institucional e equipe logada.
* **Relatórios em PDF & Apresentação (Módulo F)**:
  * **Agenda em PDF (RF21)**: Relatório formatado da agenda clínica, filtrável por período e por veterinário.
  * **Carteirinha de Vacinação em PDF (RF22)**: Emissão de carteirinha oficial com dados do animal, do tutor e histórico de doses.
  * **Formatação Brasileira e CSRF (RF23 / RF24 / RT04 / RT05)**: Datas no formato `dd/mm/aaaa`, moeda `R$ 1.234,50`, campos booleanos em formato textual (`Sim` / `Não`), proteção anti-CSRF em todos os formulários e escape de saída (`e()`) contra XSS.
* **Desafio Bônus**:
  * **Painel de Indicadores do Mês**: Cards com métricas consolidadas na tela inicial exibindo atendimentos realizados no mês, faturamento total do mês, número de pacientes cadastrados e vacinas em alerta.

---

## 🛠️ 2. Comandos Utilizados na Geração do Sistema

O sistema foi construído a partir do `console.php` do framework, com subsequente refatoração e especialização de cada camada:

```bash
# 1. Instalação do módulo de autenticação da equipe clínica
php console.php auth:install Usuario usuarios auth

# 2. Geração dos CRUDs de Apoio (Módulo A)
php console.php scaffold:crud especies nome:string observacoes:text --auth
php console.php scaffold:crud tutores nome:string cpf:string telefone:string email:string endereco:string data_cliente:date --auth
php console.php scaffold:crud veterinarios nome:string crmv:string especialidade:string telefone:string ativo:boolean --auth
php console.php scaffold:crud procedimentos descricao:string valor:decimal duracao_minutos:integer --auth

# 3. Geração do Módulo de Animais (Módulo B)
php console.php scaffold:crud animais tutor_id:belongs_to=tutores especie_id:belongs_to=especies nome:string raca:string data_nascimento:date sexo:string peso:decimal castrado:boolean observacoes:text --auth
php console.php scaffold:pesquisa animais nome especie_id tutor_id

# 4. Geração do Módulo de Atendimentos (Módulo C)
php console.php scaffold:crud atendimentos animal_id:belongs_to=animais veterinario_id:belongs_to=veterinarios procedimento_id:belongs_to=procedimentos data_hora:datetime valor_cobrado:decimal observacoes_clinicas:text situacao:string usuario_id:belongs_to=usuarios --auth
php console.php scaffold:pesquisa atendimentos data_hora veterinario_id situacao

# 5. Geração do Módulo de Vacinas (Módulo D)
php console.php scaffold:crud vacinas animal_id:belongs_to=animais veterinario_id:belongs_to=veterinarios nome_vacina:string lote:string data_aplicacao:date data_retorno:date usuario_id:belongs_to=usuarios --auth
```

---

## 🗄️ 3. Modelo de Dados

O banco de dados relacional é estruturado em 8 tabelas principais:

1. `usuarios`: Contas da equipe clínica (`id`, `nome`, `email`, `senha`, `criado_em`).
2. `especies`: Tipos de animais atendidos (`id`, `nome` UNIQUE, `observacoes`).
3. `tutores`: Responsáveis pelos pets (`id`, `nome`, `cpf` UNIQUE, `telefone`, `email` UNIQUE, `endereco`, `data_cliente`).
4. `veterinarios`: Corpo clínico (`id`, `nome`, `crmv`, `especialidade`, `telefone`, `ativo`).
5. `procedimentos`: Tabela de serviços (`id`, `descricao`, `valor`, `duracao_minutos`).
6. `animais`: Prontuários dos pets (`id`, `tutor_id` FK, `especie_id` FK, `nome`, `raca`, `data_nascimento`, `sexo`, `peso`, `castrado`, `observacoes`).
7. `atendimentos`: Consultas e procedimentos (`id`, `animal_id` FK, `veterinario_id` FK, `procedimento_id` FK, `usuario_id` FK, `data_hora`, `valor_cobrado`, `observacoes_clinicas`, `situacao`).
8. `vacinas`: Aplicações e retornos (`id`, `animal_id` FK, `veterinario_id` FK, `usuario_id` FK, `nome_vacina`, `lote`, `data_aplicacao`, `data_retorno`).

*Diagrama completo e justificativas em [`docs/modelagem.md`](docs/modelagem.md).*

---

## 🚀 4. Como Instalar e Executar

### Pré-requisitos
* PHP 8.1 ou superior (com extensões `pdo_mysql`, `mbstring`).
* MySQL / MariaDB (via XAMPP, Laragon ou nativo).

### Passo a Passo

1. **Clonar o repositório:**
   ```bash
   git clone https://github.com/00Alasse/1a-Avalia-o---Programa-o-Web-II.git
   cd 1a-Avalia-o---Programa-o-Web-II
   ```

2. **Configurar a conexão com o banco de dados:**
   Abra `configuracoes/banco.php` e confira as credenciais do MySQL (padrão XAMPP: `root` sem senha na porta `3306`).

3. **Criar as tabelas e preparar os bancos de dados:**
   ```bash
   php instalar.php
   ```

4. **Executar a bateria de testes automatizados:**
   ```bash
   php testes/executar.php
   ```
   *(Todos os 189 testes devem passar com sucesso).*

5. **Iniciar a aplicação:**
   * **Pelo Apache do XAMPP**: Coloque o diretório dentro de `htdocs/web` e acesse [http://localhost/web/](http://localhost/web/).
   * **Pelo Servidor Embutido do PHP**:
     ```bash
     php -S localhost:8000 roteador.php
     ```
     Acesse [http://localhost:8000](http://localhost:8000).

---

## 👥 5. Usuários de Teste

Para avaliação do sistema e teste das rotas protegidas:

| Perfil | E-mail | Senha |
| :--- | :--- | :--- |
| **Administrador / Coordenação** | `admin@postoctt.ufpi.br` | `admin123` |
| **Atendente Recepção** | `atendente@postoctt.ufpi.br` | `admin123` |
| **Professor / Avaliador** | `kris@aula.com` | `123456` |

*Novas contas de equipe também podem ser registradas livremente pela tela de cadastro.*

---

## 👨‍💻 6. Autores da Equipe

Projeto desenvolvido para a **1ª Avaliação de Programação Web II**:

* **Letícia** ([@00Alasse](https://github.com/00Alasse))
* **Ramon Braga** ([@R4MBR4](https://github.com/R4MBR4))