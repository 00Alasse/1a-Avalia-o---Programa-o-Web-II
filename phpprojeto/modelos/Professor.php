<?php

namespace Modelos;

use Nucleo\Model;

use Nucleo\Validador;

class Professor extends Model
{
    protected string $tabela = 'professores';
    protected array $preenchiveis = ['nome','disciplina'];
    protected string $ordemPadrao = 'nome ASC';
    public function validar(array $dados, int|string|null $ignorarId = null): array
    {
        $v = new Validador($dados);
        
        $v -> obrigatorio('nome','Nome')
        -> minimo('nome',3,'Nome')
        -> obrigatorio('disciplina','Disciplina');
        
        return $v-> erros();
    }
}
