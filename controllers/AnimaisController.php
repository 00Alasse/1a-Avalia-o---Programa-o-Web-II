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
        $pesquisa   = [];
        $condicoes  = [];
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

        $this->view('animais/index', [
            'titulo'    => 'Animais',
            'registros' => $this->modelo->consultar($sql, $parametros),
            'pesquisa'  => $pesquisa,
            'tutores'   => $this->modelo->tutores(),
            'especies'  => $this->modelo->especies(),
        ]);
    }

    /** GET /animais/criar */
    public function criar(): void
    {
        $this->exigirAutenticacao();

        $this->view('animais/formulario', [
            'titulo'   => 'Novo Animal',
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
            'data_nascimento' => $this->post('data_nascimento'),
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

        $this->view('animais/ver', [
            'titulo'   => 'Animal',
            'registro' => $registro,
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
            'titulo'   => 'Editar Animal',
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
            'data_nascimento' => $this->post('data_nascimento'),
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
     * GET /animais/relatorio
     *
     * Cada campo da query string vira um filtro:
     *     /animais/relatorio?nome=teste
     */
    public function relatorio(): void
    {
        $this->exigirAutenticacao();

        $condicoes  = [];
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
        $pdf = RelatorioPdf::conteudo('Relatorio de animais', ['id', 'nome', 'raca', 'data_nascimento', 'sexo', 'peso', 'castrado', 'observacoes', 'tutor_id', 'especie_id'], $registros);

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

        if (!$this->modelo->excluir($id)) {
            $this->naoEncontrado();
        }

        $this->mensagem('sucesso', 'Animal excluido com sucesso.');
        $this->redirecionar('animais');
    }
}
