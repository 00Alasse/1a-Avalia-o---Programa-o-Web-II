<?php

namespace Controllers;

use Modelos\Tutor;
use Nucleo\Controller;
use Nucleo\RelatorioPdf;
use Nucleo\Sql;

class TutoresController extends Controller
{
    private Tutor $modelo;

    public function __construct()
    {
        $this->modelo = new Tutor();
    }

    /** GET /tutores */
    public function index(): void
    {
        $this->exigirAutenticacao();

        $this->view('tutores/index', [
            'titulo' => 'Tutores',
            'registros' => $this->modelo->todos(),
        ]);
    }

    /** GET /tutores/criar */
    public function criar(): void
    {
        $this->exigirAutenticacao();

        $this->view('tutores/formulario', [
            'titulo' => 'Novo Tutor',
            'registro' => null,
        ]);
    }

    /** POST /tutores/salvar */
    public function salvar(): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        $dados = [
            'nome' => $this->post('nome'),
            'cpf' => $this->post('cpf'),
            'telefone' => $this->post('telefone'),
            'email' => $this->post('email'),
            'endereco' => $this->post('endereco'),
            'data_cliente' => date('Y-m-d'),
        ];

        $erros = $this->modelo->validar($dados);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'tutores/criar');
        }

        $id = $this->modelo->criar($dados);

        $this->mensagem('sucesso', 'Tutor criado com sucesso.');
        $this->redirecionar('tutores/ver/' . $id);
    }

    /** GET /tutores/ver/1 */
    public function ver(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('tutores/ver', [
            'titulo' => 'Tutor',
            'registro' => $registro,
        ]);
    }

    /** GET /tutores/editar/1 */
    public function editar(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('tutores/formulario', [
            'titulo' => 'Editar Tutor',
            'registro' => $registro,
        ]);
    }

    /** POST /tutores/atualizar/1 */
    public function atualizar(string $id): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        if (!$this->modelo->existe($id)) {
            $this->naoEncontrado();
        }

        $dados = [
            'nome' => $this->post('nome'),
            'cpf' => $this->post('cpf'),
            'telefone' => $this->post('telefone'),
            'email' => $this->post('email'),
            'endereco' => $this->post('endereco'),
        ];

        $erros = $this->modelo->validar($dados, $id);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'tutores/editar/' . $id);
        }

        $this->modelo->atualizar($id, $dados);

        $this->mensagem('sucesso', 'Tutor atualizado com sucesso.');
        $this->redirecionar('tutores/ver/' . $id);
    }

    /**
     * GET /tutores/relatorio
     *
     * Cada campo da query string vira um filtro:
     *     /tutores/relatorio?nome=teste
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

        $filtro = $this->get('cpf');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`cpf` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('telefone');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`telefone` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('email');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`email` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('endereco');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`endereco` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('data_cliente');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`data_cliente` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY id DESC';

        $registros = $this->modelo->consultar($sql, $parametros);
        $pdf = RelatorioPdf::conteudo(
            'Relatório de tutores',
            ['id', 'nome', 'cpf', 'telefone', 'email', 'endereco', 'data_cliente'],
            $registros
        );

        $this->pdf($pdf, 'tutores.pdf');
    }

    /**
     * POST /tutores/excluir/1
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
            // O tutor possui animais vinculados.
            if ($e->getCode() === '23000') {
                $this->mensagem(
                    'erro',
                    'Não é possível excluir este tutor porque existem animais cadastrados para ele.'
                );
                $this->redirecionar('tutores');
            }

            // Se for outro erro do banco, deixa o framework
            // tratar normalmente.
            throw $e;
        }

        $this->mensagem('sucesso', 'Tutor excluído com sucesso.');
        $this->redirecionar('tutores');
    }
}
