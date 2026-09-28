<?php

namespace Controllers;

use Modelos\Atendimento;
use Nucleo\Controller;
use Nucleo\RelatorioPdf;
use Nucleo\Sql;

class AtendimentosController extends Controller
{
    private Atendimento $modelo;

    public function __construct()
    {
        $this->modelo = new Atendimento();
    }

    /** GET /atendimentos */
    public function index(): void
    {
        $this->exigirAutenticacao();

        // ----- scaffold:pesquisa inicio -----
        // O formulario acima da tabela manda os campos pela query string:
        //     /atendimentos?data_hora=...
        // Campo em branco e ignorado, entao a lista completa continua
        // aparecendo enquanto ninguem pesquisar nada.
        //
        // Os VALORES vao como "?" (parametros do PDO). So os nomes de
        // coluna entram no texto do SQL, e eles sao fixos aqui.
        $pesquisa = [];
        $condicoes = [];
        $parametros = [];

        $termo = $this->get('data_hora');

        if (is_scalar($termo) && (string) $termo !== '') {
            $pesquisa['data_hora'] = (string) $termo;
            $condicoes[] = '`data_hora` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $termo, 'inicio');
        }

        $termo = $this->get('veterinario_id');

        if (is_scalar($termo) && (string) $termo !== '') {
            $pesquisa['veterinario_id'] = (string) $termo;
            $condicoes[] = '`veterinario_id` = ?';
            $parametros[] = $termo;
        }

        $termo = $this->get('situacao');

        if (is_scalar($termo) && (string) $termo !== '') {
            $pesquisa['situacao'] = (string) $termo;
            $condicoes[] = '`situacao` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $termo);
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY `id` DESC';
        // ----- scaffold:pesquisa fim -----

        $registros = $this->modelo->consultar($sql, $parametros);

        $animais = $this->modelo->animais();
        $veterinarios = $this->modelo->veterinarios();
        $procedimentos = $this->modelo->procedimentos();

        $animaisPorId = array_column($animais, null, 'id');
        $veterinariosPorId = array_column($veterinarios, null, 'id');
        $procedimentosPorId = array_column($procedimentos, null, 'id');

        // RF13: Totais calculados sobre os resultados filtrados
        $totalAtendimentos = count($registros);
        $somaValores = array_sum(array_column($registros, 'valor_cobrado'));

        $this->view('atendimentos/index', [
            'titulo' => 'Atendimentos',
            'registros' => $registros,
            'pesquisa' => $pesquisa,
            'veterinarios' => $veterinarios,
            'animaisPorId' => $animaisPorId,
            'veterinariosPorId' => $veterinariosPorId,
            'procedimentosPorId' => $procedimentosPorId,
            'totalAtendimentos' => $totalAtendimentos,
            'somaValores' => $somaValores,
        ]);
    }

    /** GET /atendimentos/criar */
    public function criar(): void
    {
        $this->exigirAutenticacao();

        $this->view('atendimentos/formulario', [
            'titulo' => 'Novo Atendimento',
            'registro' => null,
            'animais' => $this->modelo->animais(),
            'veterinarios' => $this->modelo->veterinarios(),
            'procedimentos' => $this->modelo->procedimentos(),
        ]);
    }

    /** POST /atendimentos/salvar */
    public function salvar(): void
    {
        $this->exigirAutenticacao();

        $this->exigirFormularioValido();

        $dados = [
            'animal_id' => $this->post('animal_id'),
            'veterinario_id' => $this->post('veterinario_id'),
            'procedimento_id' => $this->post('procedimento_id'),
            'data_hora' => $this->post('data_hora'),
            'valor_cobrado' => $this->post('valor_cobrado'),
            'observacoes_clinicas' => $this->post('observacoes_clinicas'),
            'situacao' => $this->post('situacao'),
            'usuario_id' => usuario_id(), // RF18: Gravado da sessao
        ];

        $erros = $this->modelo->validar($dados);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'atendimentos/criar');
        }

        $id = $this->modelo->criar($dados);

        $this->mensagem('sucesso', 'Atendimento criado com sucesso.');
        $this->redirecionar('atendimentos/ver/' . $id);
    }

    /** GET /atendimentos/ver/1 */
    public function ver(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $usuario = !empty($registro['usuario_id']) ? (new \Modelos\Usuario())->buscar($registro['usuario_id']) : null;
        $animal = !empty($registro['animal_id']) ? (new \Modelos\Animal())->buscar($registro['animal_id']) : null;
        $veterinario = !empty($registro['veterinario_id']) ? (new \Modelos\Veterinario())->buscar($registro['veterinario_id']) : null;
        $procedimento = !empty($registro['procedimento_id']) ? (new \Modelos\Procedimento())->buscar($registro['procedimento_id']) : null;

        $this->view('atendimentos/ver', [
            'titulo' => 'Atendimento',
            'registro' => $registro,
            'usuario' => $usuario,
            'animal' => $animal,
            'veterinario' => $veterinario,
            'procedimento' => $procedimento,
        ]);
    }

    /** GET /atendimentos/editar/1 */
    public function editar(string $id): void
    {
        $this->exigirAutenticacao();

        $registro = $this->modelo->buscar($id);

        if ($registro === null) {
            $this->naoEncontrado();
        }

        $this->view('atendimentos/formulario', [
            'titulo' => 'Editar Atendimento',
            'registro' => $registro,
            'animais' => $this->modelo->animais(),
            'veterinarios' => $this->modelo->veterinarios(),
            'procedimentos' => $this->modelo->procedimentos(),
        ]);
    }

    /** POST /atendimentos/atualizar/1 */
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
            'procedimento_id' => $this->post('procedimento_id'),
            'data_hora' => $this->post('data_hora'),
            'valor_cobrado' => $this->post('valor_cobrado'),
            'observacoes_clinicas' => $this->post('observacoes_clinicas'),
            'situacao' => $this->post('situacao'),
        ];

        $erros = $this->modelo->validar($dados, $id);

        if ($erros !== []) {
            $this->voltarComErros($erros, 'atendimentos/editar/' . $id);
        }

        $this->modelo->atualizar($id, $dados);

        $this->mensagem('sucesso', 'Atendimento atualizado com sucesso.');
        $this->redirecionar('atendimentos/ver/' . $id);
    }

    /**
     * GET /atendimentos/relatorio-agenda
     *
     * Relatório especial RF21:
     * agenda filtrada por período e veterinário.
     */
    public function relatorioAgenda(): void
    {
        $this->exigirAutenticacao();

        if (
            $this->get('data_inicial') === null
            && $this->get('data_final') === null
            && $this->get('veterinario_id') === null
        ) {
            $this->view('atendimentos/relatorio-agenda', [
                'titulo' => 'Relatório de Agenda',
                'todosVeterinarios' => $this->modelo->todosVeterinarios(),
            ]);

            return;
        }

        $dataInicial = $this->get('data_inicial');
        $dataFinal = $this->get('data_final');
        $veterinarioId = $this->get('veterinario_id');

        if (
            is_scalar($dataInicial)
            && is_scalar($dataFinal)
            && (string) $dataInicial !== ''
            && (string) $dataFinal !== ''
            && (string) $dataInicial > (string) $dataFinal
        ) {
            $this->mensagem(
                'erro',
                'A data inicial não pode ser posterior à data final.'
            );

            $this->redirecionar('atendimentos/relatorio-agenda');
        }

        $condicoes = [];
        $parametros = [];

        if (is_scalar($dataInicial) && (string) $dataInicial !== '') {
            $condicoes[] = '`data_hora` >= ?';
            $parametros[] = $dataInicial . ' 00:00:00';
        }

        if (is_scalar($dataFinal) && (string) $dataFinal !== '') {
            $condicoes[] = '`data_hora` <= ?';
            $parametros[] = $dataFinal . ' 23:59:59';
        }

        if (is_scalar($veterinarioId) && (string) $veterinarioId !== '') {
            $condicoes[] = '`veterinario_id` = ?';
            $parametros[] = $veterinarioId;
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY `data_hora` ASC';

        $registros = $this->modelo->consultar($sql, $parametros);

        $animais = array_column(
            $this->modelo->animais(),
            null,
            'id'
        );

        $veterinarios = array_column(
            $this->modelo->veterinarios(),
            null,
            'id'
        );

        $procedimentos = array_column(
            $this->modelo->procedimentos(),
            null,
            'id'
        );

        $linhas = [];

        foreach ($registros as $registro) {
            $animal = $animais[$registro['animal_id']] ?? null;
            $veterinario = $veterinarios[$registro['veterinario_id']] ?? null;
            $procedimento = $procedimentos[$registro['procedimento_id']] ?? null;

            $linhas[] = [
                'animal' =>
                    $animal['nome'] ?? 'Não informado',

                'veterinario' =>
                    $veterinario['nome'] ?? 'Não informado',

                'procedimento' =>
                    $procedimento['descricao'] ?? 'Não informado',

                'data_hora' => !empty($registro['data_hora'])
                    ? (new \DateTime($registro['data_hora']))->format('d/m/Y H:i')
                    : '',

                'situacao' =>
                    $registro['situacao'] ?? '',
            ];
        }

        $pdf = RelatorioPdf::conteudo(
            'Relatório de agenda',
            [
                'animal',
                'veterinario',
                'procedimento',
                'data_hora',
                'situacao',
            ],
            $linhas
        );

        $this->pdf($pdf, 'agenda-atendimentos.pdf');
    }

    /**
     * GET /atendimentos/relatorio
     *
     * Cada campo da query string vira um filtro:
     *     /atendimentos/relatorio?animal_id=teste
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

        $filtro = $this->get('animal_id');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`animal_id` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('veterinario_id');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`veterinario_id` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('procedimento_id');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`procedimento_id` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('data_hora');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`data_hora` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('valor_cobrado');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`valor_cobrado` = ?';
            $parametros[] = $filtro;
        }

        $filtro = $this->get('observacoes_clinicas');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`observacoes_clinicas` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $filtro = $this->get('situacao');
        if (is_scalar($filtro) && (string) $filtro !== '') {
            $condicoes[] = '`situacao` LIKE ? ESCAPE ' . Sql::ESCAPE_LIKE;
            $parametros[] = Sql::comoLike((string) $filtro);
        }

        $sql = 'SELECT * FROM ' . $this->modelo->tabelaProtegida();

        if ($condicoes !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $sql .= ' ORDER BY id DESC';

        $registros = $this->modelo->consultar($sql, $parametros);

        // Nomes para substituir os IDs no relatório.
        $animais = $this->modelo->animais();
        $veterinarios = $this->modelo->veterinarios();
        $procedimentos = $this->modelo->procedimentos();

        $animaisPorId = array_column($animais, null, 'id');
        $veterinariosPorId = array_column($veterinarios, null, 'id');
        $procedimentosPorId = array_column($procedimentos, null, 'id');

        foreach ($registros as &$registro) {
            $animalId = $registro['animal_id'] ?? null;
            $veterinarioId = $registro['veterinario_id'] ?? null;
            $procedimentoId = $registro['procedimento_id'] ?? null;

            $registro['animal_id'] =
                $animaisPorId[$animalId]['nome']
                ?? $animalId
                ?? '';

            $registro['veterinario_id'] =
                $veterinariosPorId[$veterinarioId]['nome']
                ?? $veterinarioId
                ?? '';

            $registro['procedimento_id'] =
                $procedimentosPorId[$procedimentoId]['descricao']
                ?? $procedimentoId
                ?? '';

            if (
                !empty($registro['data_hora'])
                && $registro['data_hora'] !== '0000-00-00 00:00:00'
            ) {
                try {
                    $data = new \DateTime($registro['data_hora']);
                    $registro['data_hora'] = $data->format('d/m/Y H:i');
                } catch (\Exception $e) {
                    // Mantém o valor original se a data for inválida.
                }
            }

            if (
                isset($registro['valor_cobrado'])
                && is_numeric($registro['valor_cobrado'])
            ) {
                $registro['valor_cobrado'] =
                    'R$ ' . number_format(
                        (float) $registro['valor_cobrado'],
                        2,
                        ',',
                        '.'
                    );
            }
        }

        unset($registro);

        $pdf = RelatorioPdf::conteudo(
            'Relatório de atendimentos',
            [
                'id',
                'animal_id',
                'veterinario_id',
                'procedimento_id',
                'data_hora',
                'valor_cobrado',
                'observacoes_clinicas',
                'situacao',
            ],
            $registros
        );

        $this->pdf($pdf, 'atendimentos.pdf');
    }

    /**
     * POST /atendimentos/excluir/1
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

        $this->mensagem('sucesso', 'Atendimento excluido com sucesso.');
        $this->redirecionar('atendimentos');
    }
}
