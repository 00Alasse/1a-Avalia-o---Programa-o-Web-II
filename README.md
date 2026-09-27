# Clínica Veterinária Pata Amiga 🐾

Sistema web completo para gerenciamento de atendimentos, vacinas e prontuários da Clínica Veterinária Pata Amiga, desenvolvido com o Framework Didático MVC em PHP.

## O que o sistema faz

- **Cadastros de Apoio**: Gerenciamento de espécies com controle de duplicidade (RF01), tutores com validação de CPF e e-mail únicos (RF02), veterinários com indicação de ativos/inativos (RF03) e catálogo de procedimentos clínicos com valores e duração (RF04).
- **Animais & Prontuário**: Registro de animais com vínculo obrigatório a tutor e espécie (RF05/RF06), pesquisa combinada (RF08) e prontuário completo exibindo dados do tutor, idade calculada em PHP e histórico de atendimentos e vacinas (RF07).
- **Atendimentos Clínicos**: Agendamento de consultas e procedimentos com validação contra conflito de horário do mesmo profissional (RF10), recusa de agendamento retroativo no passado (RF11), filtro com contagem e soma dinâmica de valores (RF13) e registro seguro de autoria pela sessão (RF18).
- **Controle de Vacinas**: Registro de aplicações e previsões de retorno (RF14) com painel na tela inicial destacando vacinas vencidas e a vencer nos próximos 30 dias com links para o animal (RF15).
- **Controle de Acesso**: Tela de login restrita à equipe clínica com proteção de rotas (RF16/RF17) e apresentação institucional pública da clínica para visitantes (RF25).

## Como instalar

### Pré-requisitos
- PHP 8.1 ou superior
- MySQL / MariaDB (XAMPP recomendado)

### Passos
1. Clone o repositório:
   ```bash
   git clone https://github.com/00Alasse/1a-Avalia-o---Programa-o-Web-II.git