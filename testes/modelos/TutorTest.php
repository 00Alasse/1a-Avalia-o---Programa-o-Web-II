<?php

namespace Testes\Modelos;

use Modelos\Tutor;
use Nucleo\Database;
use Testes\Suporte\TesteBase;

class TutorTest extends TesteBase
{
    private Tutor $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        // Cada teste monta as proprias tabelas: a ordem em que as
        // classes rodam nao interfere no resultado.
        $this->recriarTabelas([
            'tutores' => "CREATE TABLE `tutores` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nome` VARCHAR(255) NULL,
                `cpf` VARCHAR(255) NULL,
                `telefone` VARCHAR(255) NULL,
                `email` VARCHAR(255) NULL,
                `endereco` VARCHAR(255) NULL,
                `data_cliente` DATE NULL
            )",
        ]);


        $this->modelo = new Tutor();
    }

    public function testeExecutaCrudCompleto(): void
    {
        $dados = [
            'nome' => 'Teste',
            'cpf' => 'Teste',
            'telefone' => 'Teste',
            'email' => 'ana@example.com',
            'endereco' => 'Teste',
            'data_cliente' => '2026-01-01',
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
            'cpf' => 'Teste',
            'telefone' => 'Teste',
            'email' => 'ana@example.com',
            'endereco' => 'Teste',
            'data_cliente' => '2026-01-01',
        ];

        $erros = $this->modelo->validar($dados);

        $this->assertNaoVazio($erros);
        $this->assertTemChave('nome', $erros);
    }
}
