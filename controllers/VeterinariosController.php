<?php

namespace Controllers;

use Modelos\Veterinario;
use Nucleo\Controller;
use Nucleo\RelatorioPdf;
use Nucleo\Sql;

class VeterinariosController extends Controller
{
    private Veterinario $modelo;

    public function __construct()
    {
        $this->modelo = new Veterinario();
    }

    /** GET /veterinarios */
    public function index(): void
    {
        $this->exigirAutenticacao();

        $pesquisa = [];
        $condicoes = [];
        $parametros = [];

        $termo = $this->get('nome');

        if (is_scalar($termo) && (string) $termo !== '') {
            $pesquisa['nome'] = (string) $termo;

            $condicoes[] = '`nome` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
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

        $this->view('veterinarios/index', [
            'titulo' => 'Veterinarios',
            'registros' => $registros,
            'pesquisa' => $pesquisa,
        ]);
    }

    /** GET /veterinarios/criar */
    public function criar(): void
    {
        $this->exigirAutenticacao();

        $this->view('veterinarios/formulario', [
            'titulo' => 'Novo Veterinario',
            'registro' => null,
        ]);
    }

    /** POST /veterinarios/salvar */
    public function salvar(): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        $dados = [
            'nome' => $this->post('nome'),
            'crmv' => $this->post('crmv'),
            'especialidade' => $this->post('especialidade'),
            'telefone' => $this->post('telefone'),
            'ativo' => $this->post('ativo'),
        ];

        $erros = $this->modelo->validar($dados);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'veterinarios/criar');
        }

        $id = $this->modelo->criar($dados);

        $this->mensagem('sucesso', 'Veterinario criado com sucesso.');
        $this->redirecionar('veterinarios/ver/' . $id);
    }

    /** GET /veterinarios/ver/1 */
    public function ver(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('veterinarios/ver', [
            'titulo' => 'Veterinario',
            'registro' => $registro,
        ]);
    }

    /** GET /veterinarios/editar/1 */
    public function editar(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('veterinarios/formulario', [
            'titulo' => 'Editar Veterinario',
            'registro' => $registro,
        ]);
    }

    /** POST /veterinarios/atualizar/1 */
    public function atualizar(string $id): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        if (!$this->modelo->existe($id)) {
            $this->naoEncontrado();
        }

        $dados = [
            'nome' => $this->post('nome'),
            'crmv' => $this->post('crmv'),
            'especialidade' => $this->post('especialidade'),
            'telefone' => $this->post('telefone'),
            'ativo' => $this->post('ativo'),
        ];

        $erros = $this->modelo->validar($dados, $id);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'veterinarios/editar/' . $id);
        }

        $this->modelo->atualizar($id, $dados);

        $this->mensagem('sucesso', 'Veterinario atualizado com sucesso.');
        $this->redirecionar('veterinarios/ver/' . $id);
    }

    /**
     * GET /veterinarios/relatorio
     *
     * Cada campo da query string vira um filtro:
     *     /veterinarios/relatorio?nome=teste
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

        $filtro = $this->get('crmv');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`crmv` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('especialidade');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`especialidade` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('telefone');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`telefone` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('ativo');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`ativo` = ?';
            $parametros[] = $filtro;
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY id DESC';

        $registros = $this->modelo->consultar($sql, $parametros);

        foreach ($registros as &$registro) {
            $registro['ativo'] = ((int) ($registro['ativo'] ?? 0) === 1)
                ? 'Sim'
                : 'Não';
        }
        unset($registro);

        $pdf = RelatorioPdf::conteudo(
            'Relatório de veterinários',
            ['id', 'nome', 'crmv', 'especialidade', 'telefone', 'ativo'],
            $registros
        );

        $this->pdf($pdf, 'veterinarios.pdf');
    }

    /**
     * POST /veterinarios/excluir/1
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
            // O veterinário possui atendimentos ou vacinas vinculados.
            if ($e->getCode() === '23000') {
                $this->mensagem(
                    'erro',
                    'Não é possível excluir este veterinário porque existem atendimentos ou vacinas vinculados a ele.'
                );
                $this->redirecionar('veterinarios');
            }

            // Se for outro erro do banco, deixa o framework tratar normalmente.
            throw $e;
        }

        $this->mensagem('sucesso', 'Veterinário excluído com sucesso.');
        $this->redirecionar('veterinarios');
    }
}
