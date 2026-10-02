# Relatório Técnico — Sistema Clínica Veterinária Pata Amiga 🐾

**Disciplina:** Programação Web II — 1ª Avaliação  
**Framework:** Framework MVC Didático em PHP (PHP 8.2 & MySQL)  
**Equipe:**  
* Letícia ([@00Alasse](https://github.com/00Alasse))  
* Ramon Braga ([@R4MBR4](https://github.com/R4MBR4))  
**Repositório:** [https://github.com/00Alasse/1a-Avalia-o---Programa-o-Web-II](https://github.com/00Alasse/1a-Avalia-o---Programa-o-Web-II)  
**Versão / Tag de Entrega:** `v1.0`

---

## 1. Descrição do Sistema

A **Clínica Veterinária Pata Amiga** atende cães, gatos e pequenos animais. O sistema substitui o antigo controle descentralizado e vulnerável (cadernos físicos e planilhas isoladas) por uma solução web centralizada, segura e aderente ao padrão arquitetural **Model-View-Controller (MVC)**.

### Principais Funcionalidades Implementadas:
* **Módulo A (Cadastros de Apoio)**: Gestão de espécies (RF01), tutores com validações únicas (RF02), veterinários com controle de profissionais ativos (RF03) e catálogo de procedimentos com valores e tempo estimado (RF04).
* **Módulo B (Animais & Prontuário)**: Vínculo estrito com tutor e espécie via dropdown (RF05/RF06), busca combinada dinâmica (RF08) e prontuário médico completo com cálculo dinâmico da idade do animal pelo PHP e histórico unificado de atendimentos e vacinas (RF07).
* **Módulo C (Atendimentos Clínicos)**: Agendamento e registro de consultas (RF09), recusa de choque de horário do profissional (RF10), bloqueio de agendamento no passado (RF11), filtro com totalizador e somatório financeiro (RF12/RF13).
* **Módulo D (Vacinas & Alertas)**: Controle vacinal por lote e data de retorno (RF14) e painel inteligente na tela inicial destacando vacinas vencidas e a vencer em até 30 dias com links rápidos (RF15).
* **Módulo E (Acesso & Segurança)**: Controle de autenticação da equipe clínica (RF16), proteção integral das rotas contra visitantes não autenticados (RF17), gravação inviolável da autoria das operações via sessão (RF18) e menus contextuais (RF19/RF20).
* **Módulo F (Relatórios & Apresentação)**: Emissão de relatórios em PDF para a agenda clínica (RF21) e para a carteirinha de vacinação do paciente (RF22), formatação numérica e de datas brasileira (RF23/RF24) e página pública institucional (RF25).
* **Desafio Bônus Realizado**: Painel de indicadores gerenciais na home da equipe (atendimentos realizados no mês, receita financeira mensal, total de pacientes e doses pendentes).

---

## 2. Modelo de Dados Final e Relacionamentos

### (a) Estrutura das Tabelas

1. **`usuarios`**: Contas de acesso da equipe clínica.
   * `id` (INT PK AI), `nome` (VARCHAR 100), `email` (VARCHAR 150 UNIQUE), `senha` (VARCHAR 255), `criado_em` (TIMESTAMP).
2. **`especies`**: Categorias de animais atendidos.
   * `id` (INT PK AI), `nome` (VARCHAR 255 UNIQUE), `observacoes` (TEXT NULL).
3. **`tutores`**: Responsáveis pelos animais.
   * `id` (INT PK AI), `nome` (VARCHAR 255), `cpf` (VARCHAR 255 UNIQUE), `telefone` (VARCHAR 255), `email` (VARCHAR 255 UNIQUE), `endereco` (VARCHAR 255), `data_cliente` (DATE).
4. **`veterinarios`**: Corpo médico veterinário.
   * `id` (INT PK AI), `nome` (VARCHAR 255), `crmv` (VARCHAR 255), `especialidade` (VARCHAR 255), `telefone` (VARCHAR 255), `ativo` (TINYINT(1) DEFAULT 1).
5. **`procedimentos`**: Catálogo de serviços clínicos.
   * `id` (INT PK AI), `descricao` (VARCHAR 255), `valor` (DECIMAL 12,2), `duracao_minutos` (INT).
6. **`animais`**: Pacientes da clínica.
   * `id` (INT PK AI), `tutor_id` (INT FK `tutores`), `especie_id` (INT FK `especies`), `nome` (VARCHAR 255), `raca` (VARCHAR 255), `data_nascimento` (DATE), `sexo` (VARCHAR 255), `peso` (DECIMAL 12,2), `castrado` (TINYINT(1)), `observacoes` (TEXT).
7. **`atendimentos`**: Consultas e intervenções médicas.
   * `id` (INT PK AI), `animal_id` (INT FK `animais`), `veterinario_id` (INT FK `veterinarios`), `procedimento_id` (INT FK `procedimentos`), `usuario_id` (INT FK `usuarios`), `data_hora` (DATETIME), `valor_cobrado` (DECIMAL 12,2), `observacoes_clinicas` (TEXT), `situacao` (VARCHAR 255).
8. **`vacinas`**: Aplicação de imunizantes e retornos.
   * `id` (INT PK AI), `animal_id` (INT FK `animais`), `veterinario_id` (INT FK `veterinarios`), `usuario_id` (INT FK `usuarios`), `nome_vacina` (VARCHAR 255), `lote` (VARCHAR 255), `data_aplicacao` (DATE), `data_retorno` (DATE).

### (b) Justificativas da Modelagem
* **Separação entre Tutores e Veterinários**: Tutores são clientes externos atendidos pela recepção; veterinários são profissionais com número de conselho (CRMV) e flag de disponibilidade ativa.
* **Tabela Associativa/Transacional `atendimentos`**: Modela a relação N:N entre animais e veterinários ao longo do tempo, registrando o procedimento realizado e permitindo que o `valor_cobrado` reflita o preço praticado na data, independente de reajustes futuros na tabela `procedimentos`.
* **Chave Estrangeira `usuario_id`**: Vincula auditoria e autoria (quem logou e registrou a consulta/vacina) aos dados clínicos, atendendo ao RF18.
* **Integridade Referencial**: Todas as chaves estrangeiras (`FOREIGN KEY`) utilizam `ENGINE=InnoDB` garantindo integridade das relações e suporte a transações.

---

## 3. Comandos Utilizados na Construção do Projeto

Seguindo a recomendação do roteiro didático, o scaffold de autenticação foi instalado prioritariamente, garantindo que os scaffolds subsequentes já nascessem com rotas protegidas pelo parâmetro `--auth`:

```bash
# 1. Preparação do banco de dados e ambiente inicial
php instalar.php

# 2. Instalação da autenticação da equipe (Módulo E)
php console.php auth:install Usuario usuarios auth

# 3. Geração dos cadastros de apoio (Módulo A)
php console.php scaffold:crud especies nome:string observacoes:text --auth
php console.php scaffold:crud tutores nome:string cpf:string telefone:string email:string endereco:string data_cliente:date --auth
php console.php scaffold:crud veterinarios nome:string crmv:string especialidade:string telefone:string ativo:boolean --auth
php console.php scaffold:crud procedimentos descricao:string valor:decimal duracao_minutos:integer --auth

# 4. Geração do recurso de Animais com chaves estrangeiras (Módulo B)
php console.php scaffold:crud animais tutor_id:belongs_to=tutores especie_id:belongs_to=especies nome:string raca:string data_nascimento:date sexo:string peso:decimal castrado:boolean observacoes:text --auth
php console.php scaffold:pesquisa animais nome especie_id tutor_id

# 5. Geração de Atendimentos e Vacinas com autoria (Módulos C e D)
php console.php scaffold:crud atendimentos animal_id:belongs_to=animais veterinario_id:belongs_to=veterinarios procedimento_id:belongs_to=procedimentos data_hora:datetime valor_cobrado:decimal observacoes_clinicas:text situacao:string usuario_id:belongs_to=usuarios --auth
php console.php scaffold:pesquisa atendimentos data_hora veterinario_id situacao

php console.php scaffold:crud vacinas animal_id:belongs_to=animais veterinario_id:belongs_to=veterinarios nome_vacina:string lote:string data_aplicacao:date data_retorno:date usuario_id:belongs_to=usuarios --auth

# 6. Execução contínua dos testes automatizados
php testes/executar.php
```

---

## 4. Decisões Arquiteturais e Separação de Camadas

Em estrito cumprimento ao requisito **RT03**:
1. **Model (`Modelos\`)**:
   * Concentra **100% da lógica de negócio e validações**, especialmente nos métodos `validar($dados, $ignorarId)`.
   * Nenhuma view ou controller realiza consultas diretas em SQL para validar consistência de dados.
   * Regras como unicidade de CPF/e-mail, verificação de choques de horário, bloqueio de datas passadas e checagem de castração duplicada ficam encapsuladas nos respectivos models.
2. **Controller (`Controllers\`)**:
   * Responsável apenas pelo fluxo: receber requisições HTTP, invocar `exigirAutenticacao()`, sanitizar parâmetros via token anti-CSRF (`exigirFormularioValido()`), delegar persistência ao Model e redirecionar com mensagens flash amigáveis.
3. **View (`Views\`)**:
   * Exclusivamente apresentação. Todos os dados são escapados com a função nativa `e()` para prevenir vulnerabilidades de Cross-Site Scripting (XSS).
   * Formatação visual centralizada com os helpers `data_br()`, `moeda_br()` e `sim_nao()`.

---

## 5. Implementação das Regras de Negócio Chave

### 5.1. RF10: Recusa de Conflito de Horário para o Mesmo Veterinário
Implementada no método `validar()` da classe `Modelos\Atendimento`:

```php
// RF10: Recusar agendamento duplicado para o mesmo veterinario no mesmo horario
if (!empty($dados['veterinario_id']) && !empty($dados['data_hora'])) {
    $sql = "SELECT id FROM atendimentos WHERE veterinario_id = ? AND data_hora = ? AND situacao != 'cancelado'";
    $parametros = [$dados['veterinario_id'], $dados['data_hora']];

    // Se for edicao de um atendimento existente, ignora o proprio id
    if ($ignorarId !== null) {
        $sql .= " AND id != ?";
        $parametros[] = $ignorarId;
    }

    $conflitos = $this->consultar($sql, $parametros);
    if (!empty($conflitos)) {
        $v->personalizada('data_hora', false, 'O veterinário selecionado já possui um atendimento marcado para este mesmo horário.');
    }
}
```

### 5.2. RF11: Recusa de Agendamento com Data no Passado
Permite data retroativa exclusivamente se o atendimento for registrado com situação `realizado`:

```php
// RF11: Validar data de acordo com a situação
if (!empty($dados['data_hora'])) {
    $situacao = $dados['situacao'] ?? '';
    $dataHora = strtotime($dados['data_hora']);

    if ($situacao === 'realizado' && $dataHora > time()) {
        $v->personalizada(
            'data_hora',
            false,
            'Um atendimento realizado não pode ter data e horário no futuro.'
        );
    }

    if ($situacao !== 'realizado' && $dataHora < time()) {
        $v->personalizada(
            'data_hora',
            false,
            'Atendimentos agendados ou cancelados não podem ter data e horário no passado.'
        );
    }
}
```

### 5.3. RF18: Gravação Segura de Autoria a Partir da Sessão
Implementada em `Controllers\AtendimentosController` e `Controllers\VacinasController`. O `usuario_id` é injetado diretamente da sessão autenticada, nunca vindo do corpo da requisição POST:

```php
// No método salvar() de AtendimentosController.php:
$dados = [
    'animal_id'            => $this->post('animal_id'),
    'veterinario_id'       => $this->post('veterinario_id'),
    'procedimento_id'      => $this->post('procedimento_id'),
    'data_hora'            => $this->post('data_hora'),
    'valor_cobrado'        => $this->post('valor_cobrado'),
    'observacoes_clinicas' => $this->post('observacoes_clinicas'),
    'situacao'             => $this->post('situacao'),
    'usuario_id'           => usuario_id(), // RF18: Inviolável da sessão do usuário logado
];
```

E na view [`views/atendimentos/ver.php`](file:///c:/xampp/htdocs/web/views/atendimentos/ver.php):
```php
<dt class="col-sm-3 text-primary">Registrado por</dt>
<dd class="col-sm-9 text-primary font-weight-bold">
    <?= e($usuario['nome'] ?? 'Equipe') ?>
    <?= !empty($usuario['email']) ? '(' . e($usuario['email']) . ')' : '' ?>
</dd>
```

---

## 6. Gestão do Git e Resolução de Problemas

### (a) Revisão do `.gitignore` (RG03)
O arquivo `.gitignore` foi configurado e mantido para proteger o repositório contra:
* Arquivos de configuração local (`configuracoes/banco.local.php`): evita vazamento de credenciais locais de cada membro da equipe.
* Uploads dinâmicos (`views/uploads/*`, exceto o `.htaccess` protetor): evita versionar arquivos binários enviados por testes.
* Metadados e lixo de sistema operacional/IDEs (`.DS_Store`, `Thumbs.db`, `.vscode/`, `.idea/`).

### (b) Resolução de Conflito de Merge Real (RG08)
No commit `c40dafc`, ocorreu um conflito real de merge ao integrar a branch remota com alterações locais em `views/template/cabecalho.php` e arquivos de estilo. Ambos os desenvolvedores haviam customizado a navegação (um incluindo os links de relatórios e outro os links do módulo de vacinas). O conflito foi resolvido manualmente preservando ambas as rotas no menu lateral e unificando o CSS.

### (c) Comando de Correção de Histórico Justificado (RG09)
No commit `00199c0`, foi utilizado o comando `git revert` para desfazer o commit `ef39a89` (*"docs: adicionar nota temporaria de homologacao"*), que continha anotações de teste que não deveriam permanecer no branch de entrega principal. O revert manteve o histórico auditável e seguro.

---

## 7. Resultados dos Testes Automatizados (RT08 / E3)

A suíte completa de testes automatizados do sistema foi executada com **100% de sucesso**:

```text
==========================================================
Executando os testes do framework
...
Controllers\AnimaisControllerTest          PASSOU (todas as rotas)
Controllers\AtendimentosControllerTest     PASSOU (todas as rotas)
Controllers\AuthControllerTest             PASSOU (todas as rotas)
Controllers\EspeciesControllerTest         PASSOU (todas as rotas)
Controllers\ProcedimentosControllerTest    PASSOU (todas as rotas)
Controllers\TutoresControllerTest          PASSOU (todas as rotas)
Controllers\VacinasControllerTest          PASSOU (todas as rotas)
Controllers\VeterinariosControllerTest     PASSOU (todas as rotas)
Modelos\AtendimentoTest                    PASSOU (RF10, RF11, RF18 cobertos)
...
----------------------------------------------------------
Testes: 189 | Passaram: 189 | Falharam: 0 | Erros: 0 | Asserções: 619
Tempo: 4.281s

TUDO CERTO! O sistema está funcionando.
==========================================================
```

---

## 8. Declaração do Uso de Inteligência Artificial Generativa

Conforme estabelecido na **Seção 9 (Regras de Conduta)** do documento da atividade:
* A ferramenta de IA Generativa foi empregada exclusivamente como suporte no diagnóstico de falhas nos testes automatizados gerados pelo scaffold (identificação de discrepâncias de rótulos maiúsculos/minúsculos nas views e dependências de fixtures de banco em testes de visualização) e na estruturação da documentação de entrega.
* Todo o código, regras de negócio e arquitetura foram integralmente compreendidos, validados e conferidos pelos integrantes da equipe, estando ambos preparados para a defesa técnica oral.