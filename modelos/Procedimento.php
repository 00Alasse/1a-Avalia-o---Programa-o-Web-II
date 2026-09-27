<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Procedimento extends Model
{
    protected string $tabela = 'procedimentos';
    protected array $preenchiveis = ['descricao', 'valor', 'duracao_minutos'];
    protected string $ordemPadrao = 'id DESC';

    /**
     * Regras de validacao do formulario.
     * Devolve um array vazio quando esta tudo certo.
     */
    public function validar(array $dados, int|string|null $ignorarId = null): array
    {
        $v = new Validador($dados);

        $v->obrigatorio('descricao')
            ->maximo('descricao', 255)
            ->obrigatorio('valor')
            ->numerico('valor')
            ->obrigatorio('duracao_minutos')
            ->numerico('duracao_minutos');

        $valor = $dados['valor'] ?? '';

        if ($valor !== '' && is_numeric($valor)) {
            $v->personalizada(
                'valor',
                (float) $valor >= 0,
                'O valor não pode ser negativo.'
            );
        }

        $duracao = $dados['duracao_minutos'] ?? '';

        if ($duracao !== '' && is_numeric($duracao)) {
            $v->personalizada(
                'duracao_minutos',
                (int) $duracao > 0,
                'A duração deve ser maior que zero.'
            );
        }

        return $v->erros();
    }
}