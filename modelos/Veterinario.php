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
    $v = new Validador($dados);

    $v->obrigatorio('nome')
        ->maximo('nome', 255)
        ->obrigatorio('crmv')
        ->maximo('crmv', 255)
        ->obrigatorio('especialidade')
        ->maximo('especialidade', 255)
        ->maximo('telefone', 255);

    $crmv = $dados['crmv'] ?? '';

    if ($crmv !== '') {
        $registro = $this->primeiroOnde('crmv', $crmv);

        if (
            $registro !== null &&
            ($ignorarId === null || (string) $registro['id'] !== (string) $ignorarId)
        ) {
            $v->personalizada('crmv', false, 'Este CRMV já está cadastrado.');
        }
    }

    return $v->erros();
}
}
