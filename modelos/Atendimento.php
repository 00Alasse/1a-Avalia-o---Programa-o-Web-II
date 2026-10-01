<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Atendimento extends Model
{
    protected string $tabela = 'atendimentos';
    protected array $preenchiveis = ['animal_id', 'veterinario_id', 'procedimento_id', 'data_hora', 'valor_cobrado', 'observacoes_clinicas', 'situacao', 'usuario_id'];
    protected string $ordemPadrao = 'id DESC';

    /**
     * Regras de validacao do formulario.
     * Devolve um array vazio quando esta tudo certo.
     */
    public function validar(array $dados, int|string|null $ignorarId = null): array
    {
        $v = new Validador($dados);

        // Validacoes basicas dos campos obrigatorios
        $v->obrigatorio('animal_id', 'Animal')
            ->numerico('animal_id')
            ->obrigatorio('veterinario_id', 'Veterinário')
            ->numerico('veterinario_id')
            ->obrigatorio('procedimento_id', 'Procedimento')
            ->numerico('procedimento_id')
            ->obrigatorio('data_hora', 'Data e Horário')
            ->numerico('valor_cobrado', 'Valor Cobrado')
            ->obrigatorio('situacao', 'Situação');

        $v->personalizada(
            'situacao',
            in_array($dados['situacao'] ?? '', ['agendado', 'realizado', 'cancelado'], true),
            'A situação deve ser Agendado, Realizado ou Cancelado.'
        );

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

        // RF12: Impedir castração de animal que já está castrado
        if (!empty($dados['animal_id']) && !empty($dados['procedimento_id'])) {
            $animal = (new \Modelos\Animal())->buscar($dados['animal_id']);
            $procedimento = (new \Modelos\Procedimento())->buscar($dados['procedimento_id']);

            if ($animal !== null && $procedimento !== null) {
                $descricaoProcedimento = mb_strtolower(trim($procedimento['descricao']));

                if (
                    $descricaoProcedimento === 'castração'
                    && isset($animal['castrado'])
                    && (string) $animal['castrado'] === '1'
                ) {
                    $v->personalizada(
                        'procedimento_id',
                        false,
                        'Este animal já está cadastrado como castrado e não pode receber um procedimento de castração.'
                    );
                }
            }
        }

        return $v->erros();
    }
    /** Opcoes da tabela pai, usadas no <select> do formulario. */
    public function animais(): array
    {
        return (new \Modelos\Animal())->consultar(
            'SELECT * FROM animais ORDER BY nome ASC'
        );
    }

    /** Opcoes da tabela pai, usadas no <select> do formulario. */
    public function veterinarios(): array
    {
        return (new \Modelos\Veterinario())->consultar(
        'SELECT * FROM veterinarios WHERE ativo = 1 ORDER BY nome ASC'
        );
    }

    public function todosVeterinarios(): array
    {
        return (new \Modelos\Veterinario())->consultar(
            'SELECT * FROM veterinarios ORDER BY nome ASC'
        );
    }

    /** Opcoes da tabela pai, usadas no <select> do formulario. */
    public function procedimentos(): array
    {
        return (new \Modelos\Procedimento())->consultar(
            'SELECT * FROM procedimentos ORDER BY descricao ASC'
        );
    }
}
