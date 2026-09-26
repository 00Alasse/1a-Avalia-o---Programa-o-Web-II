<?php

namespace Testes\Modelos;

use Modelos\Veterinario;
use Nucleo\Database;
use Testes\Suporte\TesteBase;

class VeterinarioTest extends TesteBase
{
    private Veterinario $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        // Cada teste monta as proprias tabelas: a ordem em que as
        // classes rodam nao interfere no resultado.
        $this->recriarTabelas([
            'veterinarios' => "CREATE TABLE `veterinarios` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nome` VARCHAR(255) NULL,
                `crmv` VARCHAR(255) NULL,
                `especialidade` VARCHAR(255) NULL,
                `telefone` VARCHAR(255) NULL,
                `ativo` TINYINT(1) NULL
            )",
        ]);


        $this->modelo = new Veterinario();
    }

    public function testeExecutaCrudCompleto(): void
    {
        $dados = [
            'nome' => 'Teste',
            'crmv' => 'Teste',
            'especialidade' => 'Teste',
            'telefone' => 'Teste',
            'ativo' => 1,
        ];

        $id = $this->modelo->criar($dados);
        $registro = $this->modelo->buscar($id);

        $this->assertVerdadeiro($id > 0);
        $this->assertIgual($dados['nome'], $registro['nome']);
        $this->assertIgual(1, $this->modelo->contar());

        $this->assertVerdadeiro($this->modelo->atualizar($id, ['nome' => 'Atualizado']));
        $this->assertIgual('Atualizado', $this->modelo->buscar($id)['nome']);

        $this->assertVerdadeiro($this->modelo->excluir($id));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeValidaOsCamposObrigatorios(): void
    {
        $dados = [
            'nome' => '',
            'crmv' => 'Teste',
            'especialidade' => 'Teste',
            'telefone' => 'Teste',
            'ativo' => 1,
        ];

        $erros = $this->modelo->validar($dados);

        $this->assertNaoVazio($erros);
        $this->assertTemChave('nome', $erros);
    }
}
