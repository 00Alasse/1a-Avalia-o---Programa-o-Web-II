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
    $v = new Validador($dados);

    $v->obrigatorio('nome')
        ->maximo('nome', 255);

    $nome = $dados['nome'] ?? '';

    if ($nome !== '') {
        $registro = $this->primeiroOnde('nome', $nome);

        if ($registro !== null && ($ignorarId === null || (string) $registro['id'] !== (string) $ignorarId)) {
            $v->personalizada('nome', false, 'Esta espécie já está cadastrada.');
        }
    }

    return $v->erros();
}
}
