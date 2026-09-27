<?php

namespace Controllers;

use Modelos\Especie;
use Nucleo\Controller;
use Nucleo\RelatorioPdf;
use Nucleo\Sql;

class EspeciesController extends Controller
{
    private Especie $modelo;

    public function __construct()
    {
        $this->modelo = new Especie();
    }

    /** GET /especies */
    public function index(): void
    {
        $this->exigirAutenticacao();

        $this->view('especies/index', [
            'titulo'    => 'Especies',
            'registros' => $this->modelo->todos(),
        ]);
    }

    /** GET /especies/criar */
    public function criar(): void
    {
        $this->exigirAutenticacao();

        $this->view('especies/formulario', [
            'titulo'   => 'Novo Especie',
            'registro' => null,
        ]);
    }

    /** POST /especies/salvar */
    public function salvar(): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        $dados = [
            'nome' => $this->post('nome'),
            'observacoes' => $this->post('observacoes'),
        ];

        $erros = $this->modelo->validar($dados);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'especies/criar');
        }

        $id = $this->modelo->criar($dados);

        $this->mensagem('sucesso', 'Especie criado com sucesso.');
        $this->redirecionar('especies/ver/' . $id);
    }

    /** GET /especies/ver/1 */
    public function ver(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('especies/ver', [
            'titulo'   => 'Especie',
            'registro' => $registro,
        ]);
    }

    /** GET /especies/editar/1 */
    public function editar(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('especies/formulario', [
            'titulo'   => 'Editar Especie',
            'registro' => $registro,
        ]);
    }

    /** POST /especies/atualizar/1 */
    public function atualizar(string $id): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        if (!$this->modelo->existe($id)) {
            $this->naoEncontrado();
        }

        $dados = [
            'nome' => $this->post('nome'),
            'observacoes' => $this->post('observacoes'),
        ];

        $erros = $this->modelo->validar($dados, $id);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'especies/editar/' . $id);
        }

        $this->modelo->atualizar($id, $dados);

        $this->mensagem('sucesso', 'Especie atualizado com sucesso.');
        $this->redirecionar('especies/ver/' . $id);
    }

    /**
     * GET /especies/relatorio
     *
     * Cada campo da query string vira um filtro:
     *     /especies/relatorio?nome=teste
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

        $filtro = $this->get('observacoes');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`observacoes` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY id DESC';

        $registros = $this->modelo->consultar($sql, $parametros);
        $pdf = RelatorioPdf::conteudo('Relatório de espécies', ['id', 'nome', 'observacoes'], $registros);

        $this->pdf($pdf, 'especies.pdf');
    }

    /**
     * POST /especies/excluir/1
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
        // A espécie possui animais vinculados.
        if ($e->getCode() === '23000') {
            $this->mensagem(
                'erro',
                'Não é possível excluir esta espécie porque existem animais cadastrados para ela.'
            );
            $this->redirecionar('especies');
        }

        // Se for outro erro do banco, deixa o framework
        // tratar normalmente.
        throw $e;
    }

    $this->mensagem('sucesso', 'Espécie excluída com sucesso.');
    $this->redirecionar('especies');
}
}