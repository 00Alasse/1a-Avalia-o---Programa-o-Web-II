<?php

namespace Testes\Modelos;

use Modelos\Animal;
use Nucleo\Database;
use Testes\Suporte\TesteBase;

class AnimalTest extends TesteBase
{
    private Animal $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        // Cada teste monta as proprias tabelas: a ordem em que as
        // classes rodam nao interfere no resultado.
        $this->recriarTabelas([
            'tutores' => 'CREATE TABLE `tutores` (`id` INT AUTO_INCREMENT PRIMARY KEY, `nome` VARCHAR(255) NULL)',
            'especies' => 'CREATE TABLE `especies` (`id` INT AUTO_INCREMENT PRIMARY KEY, `nome` VARCHAR(255) NULL)',
            'animais' => "CREATE TABLE `animais` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nome` VARCHAR(255) NULL,
                `raca` VARCHAR(255) NULL,
                `data_nascimento` DATE NULL,
                `sexo` VARCHAR(255) NULL,
                `peso` DECIMAL(12,2) NULL,
                `castrado` TINYINT(1) NULL,
                `observacoes` TEXT NULL,
                `tutor_id` INT NULL,
                `especie_id` INT NULL,
                CONSTRAINT fk_animais_tutor_id FOREIGN KEY (`tutor_id`) REFERENCES `tutores`(`id`),
                CONSTRAINT fk_animais_especie_id FOREIGN KEY (`especie_id`) REFERENCES `especies`(`id`)
            )",
        ]);

        Database::conexao()->exec("INSERT INTO `tutores` (`nome`) VALUES ('Opcao 1'), ('Opcao 2')");
        $this->idsRelacoes['tutor_id'] = (int) Database::conexao()->query('SELECT id FROM `tutores` ORDER BY id ASC LIMIT 1')->fetchColumn();
        $this->idsRelacoesAtualizadas['tutor_id'] = (int) Database::conexao()->query('SELECT id FROM `tutores` ORDER BY id DESC LIMIT 1')->fetchColumn();
        Database::conexao()->exec("INSERT INTO `especies` (`nome`) VALUES ('Opcao 1'), ('Opcao 2')");
        $this->idsRelacoes['especie_id'] = (int) Database::conexao()->query('SELECT id FROM `especies` ORDER BY id ASC LIMIT 1')->fetchColumn();
        $this->idsRelacoesAtualizadas['especie_id'] = (int) Database::conexao()->query('SELECT id FROM `especies` ORDER BY id DESC LIMIT 1')->fetchColumn();

        $this->modelo = new Animal();
    }

    public function testeExecutaCrudCompleto(): void
    {
        $dados = [
            'nome' => 'Teste',
            'raca' => 'Teste',
            'data_nascimento' => '2026-01-01',
            'sexo' => 'Teste',
            'peso' => 10.5,
            'castrado' => 1,
            'observacoes' => 'Teste',
            'tutor_id' => $this->idsRelacoes['tutor_id'],
            'especie_id' => $this->idsRelacoes['especie_id'],
        ];

        $opcoes = $this->modelo->tutores();
        $this->assertTotal(2, $opcoes);
        $this->assertVerdadeiro(in_array($this->idsRelacoes['tutor_id'], array_column($opcoes, 'id'), true));

        $opcoes = $this->modelo->especies();
        $this->assertTotal(2, $opcoes);
        $this->assertVerdadeiro(in_array($this->idsRelacoes['especie_id'], array_column($opcoes, 'id'), true));

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
            'raca' => 'Teste',
            'data_nascimento' => '2026-01-01',
            'sexo' => 'Teste',
            'peso' => 10.5,
            'castrado' => 1,
            'observacoes' => 'Teste',
            'tutor_id' => $this->idsRelacoes['tutor_id'],
            'especie_id' => $this->idsRelacoes['especie_id'],
        ];

        $erros = $this->modelo->validar($dados);

        $this->assertNaoVazio($erros);
        $this->assertTemChave('nome', $erros);
    }
}
