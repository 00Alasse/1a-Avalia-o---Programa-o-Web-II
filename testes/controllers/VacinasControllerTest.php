<?php

namespace Testes\Controllers;

use Modelos\Vacina;
use Nucleo\Database;
use Nucleo\Sessao;
use Testes\Suporte\TesteBase;

class VacinasControllerTest extends TesteBase
{
    private Vacina $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        $this->limparSessao();

        // Cada teste monta as proprias tabelas: a ordem em que as
        // classes rodam nao interfere no resultado.
        $this->recriarTabelas([
            'animais' => 'CREATE TABLE `animais` (`id` INT AUTO_INCREMENT PRIMARY KEY, `nome` VARCHAR(255) NULL)',
            'veterinarios' => 'CREATE TABLE `veterinarios` (`id` INT AUTO_INCREMENT PRIMARY KEY, `nome` VARCHAR(255) NULL)',

            'usuarios' => 'CREATE TABLE `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL
)',

            'vacinas' => "CREATE TABLE `vacinas` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `animal_id` INT NULL,
                `veterinario_id` INT NULL,
                `nome_vacina` VARCHAR(255) NULL,
                `lote` VARCHAR(255) NULL,
                `data_aplicacao` DATE NULL,
                `data_retorno` DATE NULL,
                `usuario_id` INT NULL,
                CONSTRAINT fk_vacinas_animal_id FOREIGN KEY (`animal_id`) REFERENCES `animais`(`id`),
                CONSTRAINT fk_vacinas_veterinario_id FOREIGN KEY (`veterinario_id`) REFERENCES `veterinarios`(`id`)
            )",
        ]);

        Database::conexao()->exec("INSERT INTO `animais` (`nome`) VALUES ('Opcao 1'), ('Opcao 2')");
        $this->idsRelacoes['animal_id'] = (int) Database::conexao()->query('SELECT id FROM `animais` ORDER BY id ASC LIMIT 1')->fetchColumn();
        $this->idsRelacoesAtualizadas['animal_id'] = (int) Database::conexao()->query('SELECT id FROM `animais` ORDER BY id DESC LIMIT 1')->fetchColumn();
        Database::conexao()->exec("INSERT INTO `veterinarios` (`nome`) VALUES ('Opcao 1'), ('Opcao 2')");
        $this->idsRelacoes['veterinario_id'] = (int) Database::conexao()->query('SELECT id FROM `veterinarios` ORDER BY id ASC LIMIT 1')->fetchColumn();
        $this->idsRelacoesAtualizadas['veterinario_id'] = (int) Database::conexao()->query('SELECT id FROM `veterinarios` ORDER BY id DESC LIMIT 1')->fetchColumn();
        Database::conexao()->exec(
            "INSERT INTO `usuarios` (`nome`, `email`)
     VALUES ('Usuário Teste', 'teste@example.com')"
        );

        $this->modelo = new Vacina();
        Sessao::definir(Sessao::chaveAutenticacao(), 1);
    }

    public function testeExecutaRotasDoCrud(): void
    {
        $lista = $this->requisitar('vacinas');
        $this->assertIgual(200, $lista->status);
        $this->assertContem('Animal (ID)', $lista->html);
        $this->assertContem('vacinas/relatorio', $lista->html);

        $formulario = $this->requisitar('vacinas/criar');
        $this->assertIgual(200, $formulario->status);
        $this->assertContem('Salvar', $formulario->html);

        $salvar = $this->postar('vacinas/salvar', [
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'nome_vacina' => 'Teste',
            'lote' => 'Teste',
            'data_aplicacao' => '2026-01-01',
            'data_retorno' => '2026-01-01',
        ]);
        $this->assertVerdadeiro($salvar->redirecionouPara('vacinas/ver/1'));

        $registro = $this->modelo->todos()[0] ?? null;
        $this->assertNaoNulo($registro);

        $id = (int) $registro['id'];
        $this->assertIgual($this->idsRelacoes['animal_id'], $registro['animal_id']);

        $ver = $this->requisitar('vacinas/ver/' . $id);
        $this->assertIgual(200, $ver->status);
        $this->assertContem('vacinas/editar/' . $id, $ver->html);

        $editar = $this->requisitar('vacinas/editar/' . $id);
        $this->assertIgual(200, $editar->status);
        $this->assertContem('Editar Vacina', $editar->html);

        $atualizar = $this->postar('vacinas/atualizar/' . $id, [
            'animal_id' => $this->idsRelacoesAtualizadas['animal_id'],
            'veterinario_id' => $this->idsRelacoesAtualizadas['veterinario_id'],
            'nome_vacina' => 'Atualizado',
            'lote' => 'Atualizado',
            'data_aplicacao' => '2026-02-02',
            'data_retorno' => '2026-02-02',
        ]);
        $this->assertVerdadeiro($atualizar->redirecionouPara('vacinas/ver/' . $id));
        $this->assertIgual($this->idsRelacoesAtualizadas['animal_id'], $this->modelo->buscar($id)['animal_id']);

        $excluir = $this->postar('vacinas/excluir/' . $id);
        $this->assertVerdadeiro($excluir->redirecionouPara('vacinas'));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeRecusaDadosInvalidos(): void
    {
        $resposta = $this->postar('vacinas/salvar', [
            'animal_id' => '',
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'nome_vacina' => 'Teste',
            'lote' => 'Teste',
            'data_aplicacao' => '2026-01-01',
            'data_retorno' => '2026-01-01',
        ]);

        $this->assertVerdadeiro($resposta->redirecionouPara('vacinas/criar'));
        $this->assertIgual(0, $this->modelo->contar());
    }

    public function testeRecusaFormularioSemToken(): void
    {
        $id = $this->modelo->criar([
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'nome_vacina' => 'Teste',
            'lote' => 'Teste',
            'data_aplicacao' => '2026-01-01',
            'data_retorno' => '2026-01-01',
        ]);

        $semToken = $this->postarSemToken('vacinas/excluir/' . $id);

        $this->assertVerdadeiro($semToken->foiRedirecionado());
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeExclusaoNaoAceitaGet(): void
    {
        $id = $this->modelo->criar([
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'nome_vacina' => 'Teste',
            'lote' => 'Teste',
            'data_aplicacao' => '2026-01-01',
            'data_retorno' => '2026-01-01',
        ]);

        $porGet = $this->requisitar('vacinas/excluir/' . $id);

        $this->assertIgual(404, $porGet->status);
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeGeraRelatorioEmPdf(): void
    {
        $this->modelo->criar([
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'nome_vacina' => 'Teste',
            'lote' => 'Teste',
            'data_aplicacao' => '2026-01-01',
            'data_retorno' => '2026-01-01',
        ]);

        $relatorio = $this->requisitar('vacinas/relatorio', 'GET', [
            'animal_id' => $this->idsRelacoes['animal_id'],
        ]);

        $this->assertIgual(200, $relatorio->status);
        $this->assertContem('%PDF-1.4', $relatorio->html);
        $this->assertContem('Relatório de vacinas', $relatorio->html);
    }

    public function testeExigeLoginNasRotas(): void
    {
        $this->limparSessao();

        $semLogin = $this->requisitar('vacinas');

        $this->assertVerdadeiro($semLogin->redirecionouPara('auth/login'));
    }
}
