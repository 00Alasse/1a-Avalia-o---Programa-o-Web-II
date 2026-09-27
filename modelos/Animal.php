<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Animal extends Model
{
    protected string $tabela = 'animais';
    protected array $preenchiveis = ['nome', 'raca', 'data_nascimento', 'sexo', 'peso', 'castrado', 'observacoes', 'tutor_id', 'especie_id'];
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
            ->maximo('raca', 255)
            ->maximo('sexo', 255)
            ->numerico('peso')
            ->obrigatorio('tutor_id')
            ->numerico('tutor_id')
            ->obrigatorio('especie_id')
            ->numerico('especie_id')
            ->erros();
    }

    /** Opcoes da tabela pai, usadas no <select> do formulario. */
    public function tutores(): array
    {
        return (new \Modelos\Tutor())->consultar(
            'SELECT * FROM tutores ORDER BY nome ASC'
        );
    }


    /** Opcoes da tabela pai, usadas no <select> do formulario. */
    public function especies(): array
    {
        return (new \Modelos\Especie())->consultar(
            'SELECT * FROM especies ORDER BY nome ASC'
        );
    }
}
