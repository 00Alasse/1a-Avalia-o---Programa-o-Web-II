<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Especie extends Model
{
    protected string $tabela = 'especies';
    protected array $preenchiveis = ['nome', 'observacoes'];
    protected string $ordemPadrao = 'id DESC';

    /**
     * Regras de validacao do formulario.
     * Devolve um array vazio quando esta tudo certo.
     */
    public function validar(array $dados, int|string|null $ignorarId = null): array
    {
        return (new Validador($dados))
            ->obrigatorio('nome')
            ->maximo('nome', 255)
            ->erros();
    }
}
