<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Vacina extends Model
{
protected string $tabela = 'vacinas';
protected array $preenchiveis = ['animal_id', 'veterinario_id', 'nome_vacina', 'lote', 'data_aplicacao', 'data_retorno', 'usuario_id'];    protected string $ordemPadrao = 'id DESC';

    /**
     * Regras de validacao do formulario.
     * Devolve um array vazio quando esta tudo certo.
     */
    public function validar(array $dados, int|string|null $ignorarId = null): array
    {
        return (new Validador($dados))
            ->obrigatorio('animal_id', 'Animal')
            ->numerico('animal_id')
            ->obrigatorio('veterinario_id', 'Veterinário')
            ->numerico('veterinario_id')
            ->obrigatorio('nome_vacina', 'Nome da Vacina')
            ->maximo('nome_vacina', 255)
            ->obrigatorio('lote', 'Lote')
            ->obrigatorio('data_aplicacao', 'Data de Aplicação')
            ->erros();
    }

    /** Opcoes da tabela pai, usadas no <select> do formulario. */
    public function animais(): array
    {
        return (new \Modelos\Animal())->todos();
    }

    /** Opcoes da tabela pai, usadas no <select> do formulario. */
    public function veterinarios(): array
    {
        return (new \Modelos\Veterinario())->todos();
    }
}
