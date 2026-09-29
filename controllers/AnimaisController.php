<?php

namespace Controllers;

use Modelos\Animal;
use Nucleo\Controller;
use Nucleo\RelatorioPdf;
use Nucleo\Sql;

class AnimaisController extends Controller
{
    private Animal $modelo;

    public function __construct()
    {
        $this->modelo = new Animal();
    }

    /** GET /animais */
    public function index(): void
    {
        $this->exigirAutenticacao();

        // ----- scaffold:pesquisa inicio -----
        // O formulario acima da tabela manda os campos pela query string:
        //     /animais?nome=...
        // Campo em branco e ignorado, entao a lista completa continua
        // aparecendo enquanto ninguem pesquisar nada.
        //
        // Os VALORES vao como "?" (parametros do PDO). So os nomes de
        // coluna entram no texto do SQL, e eles sao fixos aqui.
        $pesquisa = [];
        $condicoes = [];
        $parametros = [];

        $termo = $this->get('nome');

        if (is_scalar($termo) && (string) $termo !== '') {
            $pesquisa['nome'] = (string) $termo;
            $condicoes[] = '`nome` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $termo);
        }

        $termo = $this->get('especie_id');

        if (is_scalar($termo) && (string) $termo !== '') {
            $pesquisa['especie_id'] = (string) $termo;
            $condicoes[] = '`especie_id` = ?';
            $parametros[] = $termo;
        }

        $termo = $this->get('tutor_id');

        if (is_scalar($termo) && (string) $termo !== '') {
            $pesquisa['tutor_id'] = (string) $termo;
            $condicoes[] = '`tutor_id` = ?';
            $parametros[] = $termo;
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY `id` DESC';
        // ----- scaffold:pesquisa fim -----

        $registros = $this->modelo->consultar($sql, $parametros);

        $especies = $this->modelo->especies();
        $tutores = $this->modelo->tutores();

        $especiesPorId = array_column($especies, null, 'id');
        $tutoresPorId = array_column($tutores, null, 'id');

        $this->view('animais/index', [
            'titulo' => 'Animais',
            'registros' => $registros,
            'pesquisa' => $pesquisa,
            'especies' => $especies,
            'tutores' => $tutores,
            'especiesPorId' => $especiesPorId,
            'tutoresPorId' => $tutoresPorId,
        ]);
    }

    /** GET /animais/criar */
    public function criar(): void
    {
        $this->exigirAutenticacao();

        $this->view('animais/formulario', [
            'titulo' => 'Novo Animal',
            'registro' => null,
            'tutores' => $this->modelo->tutores(),
            'especies' => $this->modelo->especies(),
        ]);
    }

    /** POST /animais/salvar */
    public function salvar(): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        $dados = [
            'nome' => $this->post('nome'),
            'raca' => $this->post('raca'),
            'data_nascimento' => $this->post('data_nascimento') ?: null,
            'sexo' => $this->post('sexo'),
            'peso' => $this->post('peso'),
            'castrado' => $this->post('castrado'),
            'observacoes' => $this->post('observacoes'),
            'tutor_id' => $this->post('tutor_id'),
            'especie_id' => $this->post('especie_id'),
        ];

        $erros = $this->modelo->validar($dados);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'animais/criar');
        }

        $id = $this->modelo->criar($dados);

        $this->mensagem('sucesso', 'Animal criado com sucesso.');
        $this->redirecionar('animais/ver/' . $id);
    }

    /** GET /animais/ver/1 */
    public function ver(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        // RF07: Dados do tutor e da especie
        $tutor = !empty($registro['tutor_id'])
            ? (new \Modelos\Tutor())->buscar($registro['tutor_id'])
            : null;

        $especie = !empty($registro['especie_id'])
            ? (new \Modelos\Especie())->buscar($registro['especie_id'])
            : null;

        // RF07: Idade calculada a partir da data de nascimento
        $idadeTexto = 'Não informada';

        $dataNascimento = $registro['data_nascimento'] ?? null;

        if (
            !empty($dataNascimento)
            && $dataNascimento !== '0000-00-00'
            && $dataNascimento !== '0000-00-00 00:00:00'
        ) {
            try {
                $nasc = new \DateTime($dataNascimento);
                $hoje = new \DateTime();

                if ($nasc <= $hoje) {
                    $diff = $nasc->diff($hoje);

                    if ($diff->y > 0) {
                        $idadeTexto = $diff->y . ' ano(s)' .
                            ($diff->m > 0 ? ' e ' . $diff->m . ' mês(es)' : '');
                    } else {
                        $idadeTexto = $diff->m . ' mês(es) e ' . $diff->d . ' dia(s)';
                    }
                }
            } catch (\Exception $e) {
                $idadeTexto = 'Não informada';
            }
        }

        // RF07: Historico de atendimentos deste animal
        $sqlAtendimentos = "SELECT a.*, v.nome AS veterinario_nome, p.descricao AS procedimento_nome 
                            FROM atendimentos a 
                            LEFT JOIN veterinarios v ON v.id = a.veterinario_id 
                            LEFT JOIN procedimentos p ON p.id = a.procedimento_id 
                            WHERE a.animal_id = ? 
                            ORDER BY a.data_hora DESC";

        $atendimentos = (new \Modelos\Atendimento())->consultar(
            $sqlAtendimentos,
            [$id]
        );

        // RF07: Historico de vacinas deste animal
        $sqlVacinas = "SELECT vac.*, v.nome AS veterinario_nome 
                       FROM vacinas vac 
                       LEFT JOIN veterinarios v ON v.id = vac.veterinario_id 
                       WHERE vac.animal_id = ? 
                       ORDER BY vac.data_aplicacao DESC";

        $vacinas = (new \Modelos\Vacina())->consultar(
            $sqlVacinas,
            [$id]
        );

        $this->view('animais/ver', [
            'titulo' => 'Detalhes do Animal',
            'registro' => $registro,
            'tutor' => $tutor,
            'especie' => $especie,
            'idadeTexto' => $idadeTexto,
            'atendimentos' => $atendimentos,
            'vacinas' => $vacinas,
        ]);
    }

    /** GET /animais/editar/1 */
    public function editar(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('animais/formulario', [
            'titulo' => 'Editar Animal',
            'registro' => $registro,
            'tutores' => $this->modelo->tutores(),
            'especies' => $this->modelo->especies(),
        ]);
    }

    /** POST /animais/atualizar/1 */
    public function atualizar(string $id): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        if (!$this->modelo->existe($id)) {
            $this->naoEncontrado();
        }

        $dados = [
            'nome' => $this->post('nome'),
            'raca' => $this->post('raca'),
            'data_nascimento' => $this->post('data_nascimento') ?: null,
            'sexo' => $this->post('sexo'),
            'peso' => $this->post('peso'),
            'castrado' => $this->post('castrado'),
            'observacoes' => $this->post('observacoes'),
            'tutor_id' => $this->post('tutor_id'),
            'especie_id' => $this->post('especie_id'),
        ];

        $erros = $this->modelo->validar($dados, $id);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'animais/editar/' . $id);
        }

        $this->modelo->atualizar($id, $dados);

        $this->mensagem('sucesso', 'Animal atualizado com sucesso.');
        $this->redirecionar('animais/ver/' . $id);
    }

    /**
     * GET /animais/carteira-vacinacao/1
     *
     * Gera a carteira de vacinação de um animal,
     * contendo os dados do animal, do tutor e todas
     * as vacinas aplicadas.
     */
    public function carteiraVacinacao(string $id): void
    {
        $this->exigirAutenticacao();

        $animal = $this->modelo->buscar($id);

        if ($animal === null) {
            $this->naoEncontrado();
        }

        // Busca o tutor do animal.
        $tutor = !empty($animal['tutor_id'])
            ? (new \Modelos\Tutor())->buscar($animal['tutor_id'])
            : null;

        // Busca todas as vacinas aplicadas neste animal.
        $sqlVacinas = "
            SELECT
                vac.nome_vacina,
                vac.lote,
                vac.data_aplicacao,
                vac.data_retorno,
                v.nome AS veterinario
            FROM vacinas vac
            LEFT JOIN veterinarios v
                ON v.id = vac.veterinario_id
            WHERE vac.animal_id = ?
            ORDER BY vac.data_aplicacao ASC
        ";

        $vacinas = (new \Modelos\Vacina())->consultar(
            $sqlVacinas,
            [$id]
        );

        /*
         * A carteira usa uma única tabela:
         *
         * Campo | Informação | Vacina | Lote | Aplicação | Retorno | Veterinário
         *
         * As primeiras linhas apresentam os dados do animal/tutor.
         * As linhas seguintes apresentam as vacinas.
         */
        $pdf = RelatorioPdf::carteiraVacinacao(
            'Carteira de vacinação - ' . ($animal['nome'] ?? 'Animal'),
            $animal,
            $tutor,
            null,
            $vacinas
        );

        $this->pdf(
            $pdf,
            'carteira-vacinacao-' . $id . '.pdf'
        );

    }

    /**
     * GET /animais/relatorio
     *
     * Cada campo da query string vira um filtro:
     *     /animais/relatorio?nome=teste
     */
    public function relatorio(): void
    {
        $this->exigirAutenticacao();

        $condicoes = [];
        $parametros = [];

        $filtro = $this->get('id');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`id` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('nome');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`nome` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('raca');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`raca` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('data_nascimento');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`data_nascimento` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('sexo');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`sexo` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('peso');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`peso` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('castrado');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`castrado` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('observacoes');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`observacoes` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('tutor_id');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`tutor_id` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('especie_id');

        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`especie_id` = ?';
            $parametros[] = $filtro;
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY id DESC';

        $registros = $this->modelo->consultar($sql, $parametros);

        // Busca os dados das tabelas relacionadas.
        $tutores = $this->modelo->tutores();
        $especies = $this->modelo->especies();

        $tutoresPorId = array_column($tutores, null, 'id');
        $especiesPorId = array_column($especies, null, 'id');

        // Prepara os dados para o relatório.
        $linhasRelatorio = [];

        foreach ($registros as $registro) {
            $linhasRelatorio[] = [
                'id' => $registro['id'] ?? '',
                'nome' => $registro['nome'] ?? '',
                'raca' => $registro['raca'] ?? '',
                'data_nascimento' => $registro['data_nascimento'] ?? '',
                'sexo' => $registro['sexo'] ?? '',
                'peso' => $registro['peso'] ?? '',
                'castrado' => !empty($registro['castrado']) ? 'Sim' : 'Não',
                'observacoes' => $registro['observacoes'] ?? '',
                'tutor' => $tutoresPorId[$registro['tutor_id']]['nome'] ?? '-',
                'especie' => $especiesPorId[$registro['especie_id']]['nome'] ?? '-',
            ];
        }

        $colunas = [
            'ID',
            'Nome',
            'Raça',
            'Data de nascimento',
            'Sexo',
            'Peso',
            'Castrado',
            'Observações',
            'Tutor',
            'Espécie',
        ];

        $dados = [];

        foreach ($linhasRelatorio as $linha) {
            $dados[] = [
                'ID' => $linha['id'],
                'Nome' => $linha['nome'],
                'Raça' => $linha['raca'],
                'Data de nascimento' => $linha['data_nascimento'],
                'Sexo' => $linha['sexo'],
                'Peso' => $linha['peso'],
                'Castrado' => $linha['castrado'],
                'Observações' => $linha['observacoes'],
                'Tutor' => $linha['tutor'],
                'Espécie' => $linha['especie'],
            ];
        }

        $pdf = RelatorioPdf::conteudo(
            'Relatório de animais',
            $colunas,
            $dados
        );

        $this->pdf($pdf, 'animais.pdf');
    }

    /**
     * POST /animais/excluir/1
     *
     * So aceita POST com token: um link ou um <img> em outro site
     * nao conseguem apagar registros.
     */
    public function excluir(string $id): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        if (!$this->modelo->existe($id)) {
            $this->naoEncontrado();
        }

        try {
            if (!$this->modelo->excluir($id)) {
                $this->naoEncontrado();
            }
        } catch (\PDOException $e) {
            // O animal possui atendimentos ou vacinas vinculados.
            if ($e->getCode() === '23000') {
                $this->mensagem(
                    'erro',
                    'Não é possível excluir este animal porque existem atendimentos ou vacinas cadastrados para ele.'
                );
                $this->redirecionar('animais');
            }

            throw $e;
        }

        $this->mensagem('sucesso', 'Animal excluído com sucesso.');
        $this->redirecionar('animais');
    }
}