<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Atendimento extends Model
{
    protected string $tabela = 'atendimentos';
    protected array $preenchiveis = ['animal_id', 'veterinario_id', 'procedimento_id', 'data_hora', 'valor_cobrado', 'observacoes_clinicas', 'situacao'];
    protected string $ordemPadrao = 'id DESC';

    /**
     * Regras de validacao do formulario.
     * Devolve um array vazio quando esta tudo certo.
     */
    public function validar(array $dados, int|string|null $ignorarId = null): array
    {
        return (new Validador($dados))
            ->obrigatorio('animal_id')
            ->numerico('animal_id')
            ->obrigatorio('veterinario_id')
            ->numerico('veterinario_id')
            ->obrigatorio('procedimento_id')
            ->numerico('procedimento_id')
            ->numerico('valor_cobrado')
            ->maximo('situacao', 255)
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

    /** Opcoes da tabela pai, usadas no <select> do formulario. */
    public function procedimentos(): array
    {
        return (new \Modelos\Procedimento())->todos();
    }
}
