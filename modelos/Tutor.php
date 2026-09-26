<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Tutor extends Model
{
    protected string $tabela = 'tutores';
    protected array $preenchiveis = ['nome', 'cpf', 'telefone', 'email', 'endereco', 'data_cliente'];
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
        ->obrigatorio('cpf')
        ->maximo('cpf', 14)
        ->maximo('telefone', 20)
        ->obrigatorio('email')
        ->email('email')
        ->maximo('email', 255)
        ->maximo('endereco', 255);

    $cpf = $dados['cpf'] ?? '';

    if ($cpf !== '') {
        $registro = $this->primeiroOnde('cpf', $cpf);

        if ($registro !== null && ($ignorarId === null || (string) $registro['id'] !== (string) $ignorarId)) {
            $v->personalizada('cpf', false, 'Este CPF já está cadastrado.');
        }
    }

    $email = $dados['email'] ?? '';

    if ($email !== '') {
        $registro = $this->primeiroOnde('email', $email);

        if ($registro !== null && ($ignorarId === null || (string) $registro['id'] !== (string) $ignorarId)) {
            $v->personalizada('email', false, 'Este e-mail já está cadastrado.');
        }
    }

    return $v->erros();
    }
}