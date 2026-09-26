<?php

namespace Testes\Controllers;

use Modelos\Atendimento;
use Nucleo\Database;
use Nucleo\Sessao;
use Testes\Suporte\TesteBase;

class AtendimentosControllerTest extends TesteBase
{
    private Atendimento $modelo;
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
        Sessao::definir(Sessao::chaveAutenticacao(), 1);
    }

    public function testeExecutaRotasDoCrud(): void
    {
        $lista = $this->requisitar('atendimentos');
        $this->assertIgual(200, $lista->status);
        $this->assertContem('animal_id', $lista->html);
        $this->assertContem('atendimentos/relatorio', $lista->html);

        $formulario = $this->requisitar('atendimentos/criar');
        $this->assertIgual(200, $formulario->status);
        $this->assertContem('Salvar', $formulario->html);

        $salvar = $this->postar('atendimentos/salvar', [
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'procedimento_id' => $this->idsRelacoes['procedimento_id'],
            'data_hora' => '2026-01-01 10:00:00',
            'valor_cobrado' => 10.5,
            'observacoes_clinicas' => 'Teste',
            'situacao' => 'Teste',
        ]);
        $this->assertVerdadeiro($salvar->redirecionouPara('atendimentos/ver/1'));

        $registro = $this->modelo->todos()[0] ?? null;
        $this->assertNaoNulo($registro);

        $id = (int) $registro['id'];
        $this->assertIgual($this->idsRelacoes['animal_id'], $registro['animal_id']);

        $ver = $this->requisitar('atendimentos/ver/' . $id);
        $this->assertIgual(200, $ver->status);
        $this->assertContem('atendimentos/editar/' . $id, $ver->html);

        $editar = $this->requisitar('atendimentos/editar/' . $id);
        $this->assertIgual(200, $editar->status);
        $this->assertContem('Editar Atendimento', $editar->html);

        $atualizar = $this->postar('atendimentos/atualizar/' . $id, [
            'animal_id' => $this->idsRelacoesAtualizadas['animal_id'],
            'veterinario_id' => $this->idsRelacoesAtualizadas['veterinario_id'],
            'procedimento_id' => $this->idsRelacoesAtualizadas['procedimento_id'],
            'data_hora' => '2026-02-02 12:00:00',
            'valor_cobrado' => 20.5,
            'observacoes_clinicas' => 'Atualizado',
            'situacao' => 'Atualizado',
        ]);
        $this->assertVerdadeiro($atualizar->redirecionouPara('atendimentos/ver/' . $id));
        $this->assertIgual($this->idsRelacoesAtualizadas['animal_id'], $this->modelo->buscar($id)['animal_id']);

        $excluir = $this->postar('atendimentos/excluir/' . $id);
        $this->assertVerdadeiro($excluir->redirecionouPara('atendimentos'));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeRecusaDadosInvalidos(): void
    {
        $resposta = $this->postar('atendimentos/salvar', [
            'animal_id' => '',
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'procedimento_id' => $this->idsRelacoes['procedimento_id'],
            'data_hora' => '2026-01-01 10:00:00',
            'valor_cobrado' => 10.5,
            'observacoes_clinicas' => 'Teste',
            'situacao' => 'Teste',
        ]);

        $this->assertVerdadeiro($resposta->redirecionouPara('atendimentos/criar'));
        $this->assertIgual(0, $this->modelo->contar());
    }

    public function testeRecusaFormularioSemToken(): void
    {
        $id = $this->modelo->criar([
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'procedimento_id' => $this->idsRelacoes['procedimento_id'],
            'data_hora' => '2026-01-01 10:00:00',
            'valor_cobrado' => 10.5,
            'observacoes_clinicas' => 'Teste',
            'situacao' => 'Teste',
        ]);

        $semToken = $this->postarSemToken('atendimentos/excluir/' . $id);

        $this->assertVerdadeiro($semToken->foiRedirecionado());
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeExclusaoNaoAceitaGet(): void
    {
        $id = $this->modelo->criar([
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'procedimento_id' => $this->idsRelacoes['procedimento_id'],
            'data_hora' => '2026-01-01 10:00:00',
            'valor_cobrado' => 10.5,
            'observacoes_clinicas' => 'Teste',
            'situacao' => 'Teste',
        ]);

        $porGet = $this->requisitar('atendimentos/excluir/' . $id);

        $this->assertIgual(404, $porGet->status);
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeGeraRelatorioEmPdf(): void
    {
        $this->modelo->criar([
            'animal_id' => $this->idsRelacoes['animal_id'],
            'veterinario_id' => $this->idsRelacoes['veterinario_id'],
            'procedimento_id' => $this->idsRelacoes['procedimento_id'],
            'data_hora' => '2026-01-01 10:00:00',
            'valor_cobrado' => 10.5,
            'observacoes_clinicas' => 'Teste',
            'situacao' => 'Teste',
        ]);

        $relatorio = $this->requisitar('atendimentos/relatorio', 'GET', [
            'animal_id' => $this->idsRelacoes['animal_id'],
        ]);

        $this->assertIgual(200, $relatorio->status);
        $this->assertContem('%PDF-1.4', $relatorio->html);
        $this->assertContem('Relatorio de atendimentos', $relatorio->html);
    }

    public function testeExigeLoginNasRotas(): void
    {
        $this->limparSessao();

        $semLogin = $this->requisitar('atendimentos');

        $this->assertVerdadeiro($semLogin->redirecionouPara('auth/login'));
    }
}
