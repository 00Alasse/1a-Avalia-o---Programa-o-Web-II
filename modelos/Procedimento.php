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
        return (new Validador($dados))
            ->obrigatorio('descricao')
            ->maximo('descricao', 255)
            ->numerico('valor')
            ->numerico('duracao_minutos')
            ->erros();
    }
}
