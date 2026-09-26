<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Veterinario extends Model
{
    protected string $tabela = 'veterinarios';
    protected array $preenchiveis = ['nome', 'crmv', 'especialidade', 'telefone', 'ativo'];
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
            ->maximo('crmv', 255)
            ->maximo('especialidade', 255)
            ->maximo('telefone', 255)
            ->erros();
    }
}
