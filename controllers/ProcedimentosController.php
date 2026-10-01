<?php

namespace Controllers;

use Modelos\Procedimento;
use Nucleo\Controller;
use Nucleo\RelatorioPdf;
use Nucleo\Sql;

class ProcedimentosController extends Controller
{
    private Procedimento $modelo;

    public function __construct()
    {
        $this->modelo = new Procedimento();
    }

    /** GET /procedimentos */
    public function index(): void
    {
        $this->exigirAutenticacao();

        $pesquisa = [];
        $condicoes = [];
        $parametros = [];

        $termo = $this->get('descricao');

        if (is_scalar($termo) && (string) $termo !== '') {
            $pesquisa['descricao'] = (string) $termo;

            $condicoes[] = '`descricao` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $termo);
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY `id` DESC';

        $registros = $this->modelo->consultar(
            $sql,
            $parametros
        );

        $this->view('procedimentos/index', [
            'titulo' => 'Procedimentos',
            'registros' => $registros,
            'pesquisa' => $pesquisa,
        ]);
    }

    /** GET /procedimentos/criar */
    public function criar(): void
    {
        $this->exigirAutenticacao();

        $this->view('procedimentos/formulario', [
            'titulo' => 'Novo Procedimento',
            'registro' => null,
        ]);
    }

    /** POST /procedimentos/salvar */
    public function salvar(): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        $dados = [
            'descricao' => $this->post('descricao'),
            'valor' => $this->post('valor'),
            'duracao_minutos' => $this->post('duracao_minutos'),
        ];

        $erros = $this->modelo->validar($dados);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'procedimentos/criar');
        }

        $id = $this->modelo->criar($dados);

        $this->mensagem('sucesso', 'Procedimento criado com sucesso.');
        $this->redirecionar('procedimentos/ver/' . $id);
    }

    /** GET /procedimentos/ver/1 */
    public function ver(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('procedimentos/ver', [
            'titulo' => 'Procedimento',
            'registro' => $registro,
        ]);
    }

    /** GET /procedimentos/editar/1 */
    public function editar(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('procedimentos/formulario', [
            'titulo' => 'Editar Procedimento',
            'registro' => $registro,
        ]);
    }

    /** POST /procedimentos/atualizar/1 */
    public function atualizar(string $id): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        if (!$this->modelo->existe($id)) {
            $this->naoEncontrado();
        }

        $dados = [
            'descricao' => $this->post('descricao'),
            'valor' => $this->post('valor'),
            'duracao_minutos' => $this->post('duracao_minutos'),
        ];

        $erros = $this->modelo->validar($dados, $id);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'procedimentos/editar/' . $id);
        }

        $this->modelo->atualizar($id, $dados);

        $this->mensagem('sucesso', 'Procedimento atualizado com sucesso.');
        $this->redirecionar('procedimentos/ver/' . $id);
    }

    /**
     * GET /procedimentos/relatorio
     *
     * Cada campo da query string vira um filtro:
     *     /procedimentos/relatorio?descricao=teste
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

        $filtro = $this->get('descricao');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`descricao` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('valor');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`valor` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('duracao_minutos');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`duracao_minutos` = ?';
            $parametros[] = $filtro;
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY id DESC';

        $registros = $this->modelo->consultar($sql, $parametros);

        foreach ($registros as &$registro) {
            if (
                isset($registro['valor'])
                && is_numeric($registro['valor'])
            ) {
                $registro['valor'] =
                    'R$ ' . number_format(
                        (float) $registro['valor'],
                        2,
                        ',',
                        '.'
                    );
            }
        }

        unset($registro);

        $pdf = RelatorioPdf::conteudo(
            'Relatório de procedimentos',
            ['id', 'descricao', 'valor', 'duracao_minutos'],
            $registros
        );

        $this->pdf($pdf, 'procedimentos.pdf');
    }

    /**
     * POST /procedimentos/excluir/1
     *
     * So aceita POST com token: um link ou um <img> em outro site
     * nao conseguem apagar registros.
     */
    /**
     * POST /procedimentos/excluir/1
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
            // O procedimento possui atendimentos vinculados.
            if ($e->getCode() === '23000') {
                $this->mensagem(
                    'erro',
                    'Não é possível excluir este procedimento porque existem atendimentos cadastrados para ele.'
                );
                $this->redirecionar('procedimentos');
            }

            // Se for outro erro do banco, deixa o framework
            // tratar normalmente.
            throw $e;
        }

        $this->mensagem('sucesso', 'Procedimento excluído com sucesso.');
        $this->redirecionar('procedimentos');
    }
}
