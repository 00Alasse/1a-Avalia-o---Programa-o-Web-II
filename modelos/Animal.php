<?php

namespace Modelos;

use Nucleo\Model;
use Nucleo\Validador;

class Animal extends Model
{
    protected string $tabela = 'animais';
    protected array $preenchiveis = ['nome', 'raca', 'data_nascimento', 'sexo', 'peso', 'castrado', 'observacoes', 'tutor_id', 'especie_id', 'foto'];
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
            ->maximo('raca', 255)
            ->maximo('sexo', 255)
            ->numerico('peso')
            ->obrigatorio('tutor_id')
            ->numerico('tutor_id')
            ->obrigatorio('especie_id')
            ->numerico('especie_id');

        $dataNascimento = $dados['data_nascimento'] ?? '';

        if ($dataNascimento !== '') {
            $data = \DateTime::createFromFormat('Y-m-d', $dataNascimento);
            $hoje = new \DateTime('today');

            if ($data === false || $data > $hoje) {
                $v->personalizada(
                    'data_nascimento',
                    false,
                    'A data de nascimento não pode ser futura.'
                );
            }
        }

        return $v->erros();
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
