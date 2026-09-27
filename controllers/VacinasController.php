<?php

namespace Controllers;

use Modelos\Vacina;
use Nucleo\Controller;
use Nucleo\RelatorioPdf;
use Nucleo\Sql;

class VacinasController extends Controller
{
    private Vacina $modelo;

    public function __construct()
    {
        $this->modelo = new Vacina();
    }

    /** GET /vacinas */
    public function index(): void
    {
        $this->exigirAutenticacao();

        $registros = $this->modelo->todos();

        $animais = $this->modelo->animais();
        $veterinarios = $this->modelo->veterinarios();

        $animaisPorId = array_column($animais, null, 'id');
        $veterinariosPorId = array_column($veterinarios, null, 'id');

        $this->view('vacinas/index', [
            'titulo' => 'Vacinas',
            'registros' => $registros,
            'animaisPorId' => $animaisPorId,
            'veterinariosPorId' => $veterinariosPorId,
        ]);
    }

    /** GET /vacinas/criar */
    public function criar(): void
    {
        $this->exigirAutenticacao();

        $this->view('vacinas/formulario', [
            'titulo' => 'Nova Vacina',
            'registro' => null,
            'animais' => $this->modelo->animais(),
            'veterinarios' => $this->modelo->veterinarios(),
        ]);
    }

    /** POST /vacinas/salvar */
    public function salvar(): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        $dados = [
            'animal_id' => $this->post('animal_id'),
            'veterinario_id' => $this->post('veterinario_id'),
            'nome_vacina' => $this->post('nome_vacina'),
            'lote' => $this->post('lote'),
            'data_aplicacao' => $this->post('data_aplicacao'),
            'data_retorno' => $this->post('data_retorno'),
            'usuario_id' => usuario_id(), // RF18: Gravado da sessao
        ];

        $erros = $this->modelo->validar($dados);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'vacinas/criar');
        }

        $id = $this->modelo->criar($dados);

        $this->mensagem('sucesso', 'Vacina criada com sucesso.');
        $this->redirecionar('vacinas/ver/' . $id);
    }

    /** GET /vacinas/ver/1 */
    public function ver(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $usuario = !empty($registro['usuario_id']) ? (new \Modelos\Usuario())->buscar($registro['usuario_id']) : null;

        $this->view('vacinas/ver', [
            'titulo' => 'Vacina',
            'registro' => $registro,
            'usuario' => $usuario,
        ]);
    }

    /** GET /vacinas/editar/1 */
    public function editar(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('vacinas/formulario', [
            'titulo' => 'Editar Vacina',
            'registro' => $registro,
            'animais' => $this->modelo->animais(),
            'veterinarios' => $this->modelo->veterinarios(),
        ]);
    }

    /** POST /vacinas/atualizar/1 */
    public function atualizar(string $id): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        if (!$this->modelo->existe($id)) {
            $this->naoEncontrado();
        }

        $dados = [
            'animal_id' => $this->post('animal_id'),
            'veterinario_id' => $this->post('veterinario_id'),
            'nome_vacina' => $this->post('nome_vacina'),
            'lote' => $this->post('lote'),
            'data_aplicacao' => $this->post('data_aplicacao'),
            'data_retorno' => $this->post('data_retorno'),
        ];

        $erros = $this->modelo->validar($dados, $id);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'vacinas/editar/' . $id);
        }

        $this->modelo->atualizar($id, $dados);

        $this->mensagem('sucesso', 'Vacina atualizada com sucesso.');
        $this->redirecionar('vacinas/ver/' . $id);
    }

    /**
     * GET /vacinas/relatorio
     *
     * Gera um relatório das vacinas com os nomes do animal
     * e do veterinário em vez de exibir apenas os IDs.
     */
    public function relatorio(): void
    {
        $this->exigirAutenticacao();

        $condicoes = [];
        $parametros = [];

        $filtro = $this->get('id');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = 'v.`id` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('animal_id');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = 'v.`animal_id` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('veterinario_id');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = 'v.`veterinario_id` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('nome_vacina');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = 'v.`nome_vacina` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('lote');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = 'v.`lote` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('data_aplicacao');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = 'v.`data_aplicacao` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('data_retorno');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = 'v.`data_retorno` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        /*
         * Busca os nomes relacionados através dos IDs.
         */
        $sql = '
        SELECT
            v.`id`,
            a.`nome` AS `animal`,
            ve.`nome` AS `veterinario`,
            v.`nome_vacina`,
            v.`lote`,
            v.`data_aplicacao`,
            v.`data_retorno`
        FROM `vacinas` v
        INNER JOIN `animais` a
            ON a.`id` = v.`animal_id`
        INNER JOIN `veterinarios` ve
            ON ve.`id` = v.`veterinario_id`
    ';

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY v.`id` DESC';

        $registros = $this->modelo->consultar($sql, $parametros);

        $pdf = RelatorioPdf::conteudo(
            'Relatório de vacinas',
            [
                'id',
                'animal',
                'veterinario',
                'nome_vacina',
                'lote',
                'data_aplicacao',
                'data_retorno'
            ],
            $registros
        );

        $this->pdf($pdf, 'vacinas.pdf');
    }

    /**
     * POST /vacinas/excluir/1
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

        $this->mensagem('sucesso', 'Vacina excluída com sucesso.');
        $this->redirecionar('vacinas');
    }
}
