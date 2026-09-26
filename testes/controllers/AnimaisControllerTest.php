<?php

namespace Testes\Controllers;

use Modelos\Animal;
use Nucleo\Database;
use Nucleo\Sessao;
use Testes\Suporte\TesteBase;

class AnimaisControllerTest extends TesteBase
{
    private Animal $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        $this->limparSessao();

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
        Sessao::definir(Sessao::chaveAutenticacao(), 1);
    }

    public function testeExecutaRotasDoCrud(): void
    {
        $lista = $this->requisitar('animais');
        $this->assertIgual(200, $lista->status);
        $this->assertContem('nome', $lista->html);
        $this->assertContem('animais/relatorio', $lista->html);

        $formulario = $this->requisitar('animais/criar');
        $this->assertIgual(200, $formulario->status);
        $this->assertContem('Salvar', $formulario->html);

        $salvar = $this->postar('animais/salvar', [
            'nome' => 'Teste',
            'raca' => 'Teste',
            'data_nascimento' => '2026-01-01',
            'sexo' => 'Teste',
            'peso' => 10.5,
            'castrado' => 1,
            'observacoes' => 'Teste',
            'tutor_id' => $this->idsRelacoes['tutor_id'],
            'especie_id' => $this->idsRelacoes['especie_id'],
        ]);
        $this->assertVerdadeiro($salvar->redirecionouPara('animais/ver/1'));

        $registro = $this->modelo->todos()[0] ?? null;
        $this->assertNaoNulo($registro);

        $id = (int) $registro['id'];
        $this->assertIgual('Teste', $registro['nome']);

        $ver = $this->requisitar('animais/ver/' . $id);
        $this->assertIgual(200, $ver->status);
        $this->assertContem('animais/editar/' . $id, $ver->html);

        $editar = $this->requisitar('animais/editar/' . $id);
        $this->assertIgual(200, $editar->status);
        $this->assertContem('Editar Animal', $editar->html);

        $atualizar = $this->postar('animais/atualizar/' . $id, [
            'nome' => 'Atualizado',
            'raca' => 'Atualizado',
            'data_nascimento' => '2026-02-02',
            'sexo' => 'Atualizado',
            'peso' => 20.5,
            'castrado' => 0,
            'observacoes' => 'Atualizado',
            'tutor_id' => $this->idsRelacoesAtualizadas['tutor_id'],
            'especie_id' => $this->idsRelacoesAtualizadas['especie_id'],
        ]);
        $this->assertVerdadeiro($atualizar->redirecionouPara('animais/ver/' . $id));
        $this->assertIgual('Atualizado', $this->modelo->buscar($id)['nome']);

        $excluir = $this->postar('animais/excluir/' . $id);
        $this->assertVerdadeiro($excluir->redirecionouPara('animais'));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeRecusaDadosInvalidos(): void
    {
        $resposta = $this->postar('animais/salvar', [
            'nome' => '',
            'raca' => 'Teste',
            'data_nascimento' => '2026-01-01',
            'sexo' => 'Teste',
            'peso' => 10.5,
            'castrado' => 1,
            'observacoes' => 'Teste',
            'tutor_id' => $this->idsRelacoes['tutor_id'],
            'especie_id' => $this->idsRelacoes['especie_id'],
        ]);

        $this->assertVerdadeiro($resposta->redirecionouPara('animais/criar'));
        $this->assertIgual(0, $this->modelo->contar());
    }

    public function testeRecusaFormularioSemToken(): void
    {
        $id = $this->modelo->criar([
            'nome' => 'Teste',
            'raca' => 'Teste',
            'data_nascimento' => '2026-01-01',
            'sexo' => 'Teste',
            'peso' => 10.5,
            'castrado' => 1,
            'observacoes' => 'Teste',
            'tutor_id' => $this->idsRelacoes['tutor_id'],
            'especie_id' => $this->idsRelacoes['especie_id'],
        ]);

        $semToken = $this->postarSemToken('animais/excluir/' . $id);

        $this->assertVerdadeiro($semToken->foiRedirecionado());
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeExclusaoNaoAceitaGet(): void
    {
        $id = $this->modelo->criar([
            'nome' => 'Teste',
            'raca' => 'Teste',
            'data_nascimento' => '2026-01-01',
            'sexo' => 'Teste',
            'peso' => 10.5,
            'castrado' => 1,
            'observacoes' => 'Teste',
            'tutor_id' => $this->idsRelacoes['tutor_id'],
            'especie_id' => $this->idsRelacoes['especie_id'],
        ]);

        $porGet = $this->requisitar('animais/excluir/' . $id);

        $this->assertIgual(404, $porGet->status);
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeGeraRelatorioEmPdf(): void
    {
        $this->modelo->criar([
            'nome' => 'Teste',
            'raca' => 'Teste',
            'data_nascimento' => '2026-01-01',
            'sexo' => 'Teste',
            'peso' => 10.5,
            'castrado' => 1,
            'observacoes' => 'Teste',
            'tutor_id' => $this->idsRelacoes['tutor_id'],
            'especie_id' => $this->idsRelacoes['especie_id'],
        ]);

        $relatorio = $this->requisitar('animais/relatorio', 'GET', [
            'nome' => 'Teste',
        ]);

        $this->assertIgual(200, $relatorio->status);
        $this->assertContem('%PDF-1.4', $relatorio->html);
        $this->assertContem('Relatorio de animais', $relatorio->html);
    }

    public function testeExigeLoginNasRotas(): void
    {
        $this->limparSessao();

        $semLogin = $this->requisitar('animais');

        $this->assertVerdadeiro($semLogin->redirecionouPara('auth/login'));
    }
}
