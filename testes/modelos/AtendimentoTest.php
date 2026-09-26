<?php

namespace Testes\Modelos;

use Modelos\Atendimento;
use Nucleo\Database;
use Testes\Suporte\TesteBase;

class AtendimentoTest extends TesteBase
{
    private Atendimento $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        // Cada teste monta as proprias tabelas: a ordem em que as
        // classes rodam nao interfere no resultado.
        $this->recriarTabelas([
            'animais' => 'CREATE TABLE `animais` (`id` INT AUTO_INCREMENT PRIMARY KEY, `nome` VARCHAR(255) NULL)',
            'veterinarios' => 'CREATE TABLE `veterinarios` (`id` INT AUTO_INCREMENT PRIMARY KEY, `nome` VARCHAR(255) NULL)',
            'procedimentos' => 'CREATE TABLE `procedimentos` (`id` INT AUTO_INCREMENT PRIMARY KEY, `nome` VARCHAR(255) NULL)',
            'atendimentos' => "CREATE TABLE `atendimentos` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `animal_id` INT NULL,
                `veterinario_id` INT NULL,
                `procedimento_id` INT NULL,
                `data_hora` DATETIME NULL,
                `valor_cobrado` DECIMAL(12,2) NULL,
                `observacoes_clinicas` TEXT NULL,
                `situacao` VARCHAR(255) NULL,
                CONSTRAINT fk_atendimentos_animal_id FOREIGN KEY (`animal_id`) REFERENCES `animais`(`id`),
                CONSTRAINT fk_atendimentos_veterinario_id FOREIGN KEY (`veterinario_id`) REFERENCES `veterinarios`(`id`),
                CONSTRAINT fk_atendimentos_procedimento_id FOREIGN KEY (`procedimento_id`) REFERENCES `procedimentos`(`id`)
            )",
        ]);

        Database::conexao()->exec("INSERT INTO `animais` (`nome`) VALUES ('Opcao 1'), ('Opcao 2')");
        $this->idsRelacoes['animal_id'] = (int) Database::conexao()->query('SELECT id FROM `animais` ORDER BY id ASC LIMIT 1')->fetchColumn();
        $this->idsRelacoesAtualizadas['animal_id'] = (int) Database::conexao()->query('SELECT id FROM `animais` ORDER BY id DESC LIMIT 1')->fetchColumn();
        Database::conexao()->exec("INSERT INTO `veterinarios` (`nome`) VALUES ('Opcao 1'), ('Opcao 2')");
        $this->idsRelacoes['veterinario_id'] = (int) Database::conexao()->query('SELECT id FROM `veterinarios` ORDER BY id ASC LIMIT 1')->fetchColumn();
        $this->idsRelacoesAtualizadas['veterinario_id'] = (int) Database::conexao()->query('SELECT id FROM `veterinarios` ORDER BY id DESC LIMIT 1')->fetchColumn();
        Database::conexao()->exec("INSERT INTO `procedimentos` (`nome`) VALUES ('Opcao 1'), ('Opcao 2')");
        $this->idsRelacoes['procedimento_id'] = (int) Database::conexao()->query('SELECT id FROM `procedimentos` ORDER BY id ASC LIMIT 1')->fetchColumn();
        $this->idsRelacoesAtualizadas['procedimento_id'] = (int) Database::conexao()->query('SELECT id FROM `procedimentos` ORDER BY id DESC LIMIT 1')->fetchColumn();

        $this->modelo = new Atendimento();
    }

    public function testeExecutaCrudCompleto(): void
    {
        $dados = [
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'procedimento_id' => $this->idsRelacoes['procedimento_id'],
            'data_hora' => '2026-01-01 10:00:00',
            'valor_cobrado' => 10.5,
            'observacoes_clinicas' => 'Teste',
            'situacao' => 'Teste',
        ];

        $opcoes = $this->modelo->animais();
        $this->assertTotal(2, $opcoes);
        $this->assertVerdadeiro(in_array($this->idsRelacoes['animal_id'], array_column($opcoes, 'id'), true));

        $opcoes = $this->modelo->veterinarios();
        $this->assertTotal(2, $opcoes);
        $this->assertVerdadeiro(in_array($this->idsRelacoes['veterinario_id'], array_column($opcoes, 'id'), true));

        $opcoes = $this->modelo->procedimentos();
        $this->assertTotal(2, $opcoes);
        $this->assertVerdadeiro(in_array($this->idsRelacoes['procedimento_id'], array_column($opcoes, 'id'), true));

        $id = $this->modelo->criar($dados);
        $registro = $this->modelo->buscar($id);

        $this->assertVerdadeiro($id > 0);
        $this->assertIgual($dados['animal_id'], $registro['animal_id']);
        $this->assertIgual(1, $this->modelo->contar());

        $this->assertVerdadeiro($this->modelo->atualizar($id, ['animal_id' => $this->idsRelacoesAtualizadas['animal_id']]));
        $this->assertIgual($this->idsRelacoesAtualizadas['animal_id'], $this->modelo->buscar($id)['animal_id']);

        $this->assertVerdadeiro($this->modelo->excluir($id));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeValidaOsCamposObrigatorios(): void
    {
        $dados = [
            'animal_id' => '',
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'procedimento_id' => $this->idsRelacoes['procedimento_id'],
            'data_hora' => '2026-01-01 10:00:00',
            'valor_cobrado' => 10.5,
            'observacoes_clinicas' => 'Teste',
            'situacao' => 'Teste',
        ];

        $erros = $this->modelo->validar($dados);

        $this->assertNaoVazio($erros);
        $this->assertTemChave('animal_id', $erros);
    }
}
