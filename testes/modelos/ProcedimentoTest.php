<?php

namespace Testes\Modelos;

use Modelos\Procedimento;
use Nucleo\Database;
use Testes\Suporte\TesteBase;

class ProcedimentoTest extends TesteBase
{
    private Procedimento $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        // Cada teste monta as proprias tabelas: a ordem em que as
        // classes rodam nao interfere no resultado.
        $this->recriarTabelas([
            'procedimentos' => "CREATE TABLE `procedimentos` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `descricao` VARCHAR(255) NULL,
                `valor` DECIMAL(12,2) NULL,
                `duracao_minutos` INT NULL
            )",
        ]);


        $this->modelo = new Procedimento();
    }

    public function testeExecutaCrudCompleto(): void
    {
        $dados = [
            'descricao' => 'Teste',
            'valor' => 10.5,
            'duracao_minutos' => 1,
        ];

        $id = $this->modelo->criar($dados);
        $registro = $this->modelo->buscar($id);

        $this->assertVerdadeiro($id > 0);
        $this->assertIgual($dados['descricao'], $registro['descricao']);
        $this->assertIgual(1, $this->modelo->contar());

        $this->assertVerdadeiro($this->modelo->atualizar($id, ['descricao' => 'Atualizado']));
        $this->assertIgual('Atualizado', $this->modelo->buscar($id)['descricao']);

        $this->assertVerdadeiro($this->modelo->excluir($id));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeValidaOsCamposObrigatorios(): void
    {
        $dados = [
            'descricao' => '',
            'valor' => 10.5,
            'duracao_minutos' => 1,
        ];

        $erros = $this->modelo->validar($dados);

        $this->assertNaoVazio($erros);
        $this->assertTemChave('descricao', $erros);
    }
}
