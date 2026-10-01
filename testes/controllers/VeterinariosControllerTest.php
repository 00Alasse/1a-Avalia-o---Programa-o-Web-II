<?php

namespace Testes\Controllers;

use Modelos\Veterinario;
use Nucleo\Database;
use Nucleo\Sessao;
use Testes\Suporte\TesteBase;

class VeterinariosControllerTest extends TesteBase
{
    private Veterinario $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        $this->limparSessao();

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
        Sessao::definir(Sessao::chaveAutenticacao(), 1);
    }

    public function testeExecutaRotasDoCrud(): void
    {
        $lista = $this->requisitar('veterinarios');
        $this->assertIgual(200, $lista->status);
        $this->assertContem('nome', $lista->html);
        $this->assertContem('veterinarios/relatorio', $lista->html);

        $formulario = $this->requisitar('veterinarios/criar');
        $this->assertIgual(200, $formulario->status);
        $this->assertContem('Salvar', $formulario->html);

        $salvar = $this->postar('veterinarios/salvar', [
            'nome' => 'Teste',
            'crmv' => 'Teste',
            'especialidade' => 'Teste',
            'telefone' => 'Teste',
            'ativo' => 1,
        ]);
        $this->assertVerdadeiro($salvar->redirecionouPara('veterinarios/ver/1'));

        $registro = $this->modelo->todos()[0] ?? null;
        $this->assertNaoNulo($registro);

        $id = (int) $registro['id'];
        $this->assertIgual('Teste', $registro['nome']);

        $ver = $this->requisitar('veterinarios/ver/' . $id);
        $this->assertIgual(200, $ver->status);
        $this->assertContem('veterinarios/editar/' . $id, $ver->html);

        $editar = $this->requisitar('veterinarios/editar/' . $id);
        $this->assertIgual(200, $editar->status);
        $this->assertContem('Editar Veterinario', $editar->html);

        $atualizar = $this->postar('veterinarios/atualizar/' . $id, [
            'nome' => 'Atualizado',
            'crmv' => 'Atualizado',
            'especialidade' => 'Atualizado',
            'telefone' => 'Atualizado',
            'ativo' => 0,
        ]);
        $this->assertVerdadeiro($atualizar->redirecionouPara('veterinarios/ver/' . $id));
        $this->assertIgual('Atualizado', $this->modelo->buscar($id)['nome']);

        $excluir = $this->postar('veterinarios/excluir/' . $id);
        $this->assertVerdadeiro($excluir->redirecionouPara('veterinarios'));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeRecusaDadosInvalidos(): void
    {
        $resposta = $this->postar('veterinarios/salvar', [
            'nome' => '',
            'crmv' => 'Teste',
            'especialidade' => 'Teste',
            'telefone' => 'Teste',
            'ativo' => 1,
        ]);

        $this->assertVerdadeiro($resposta->redirecionouPara('veterinarios/criar'));
        $this->assertIgual(0, $this->modelo->contar());
    }

    public function testeRecusaFormularioSemToken(): void
    {
        $id = $this->modelo->criar([
            'nome' => 'Teste',
            'crmv' => 'Teste',
            'especialidade' => 'Teste',
            'telefone' => 'Teste',
            'ativo' => 1,
        ]);

        $semToken = $this->postarSemToken('veterinarios/excluir/' . $id);

        $this->assertVerdadeiro($semToken->foiRedirecionado());
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeExclusaoNaoAceitaGet(): void
    {
        $id = $this->modelo->criar([
            'nome' => 'Teste',
            'crmv' => 'Teste',
            'especialidade' => 'Teste',
            'telefone' => 'Teste',
            'ativo' => 1,
        ]);

        $porGet = $this->requisitar('veterinarios/excluir/' . $id);

        $this->assertIgual(404, $porGet->status);
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeGeraRelatorioEmPdf(): void
    {
        $this->modelo->criar([
            'nome' => 'Teste',
            'crmv' => 'Teste',
            'especialidade' => 'Teste',
            'telefone' => 'Teste',
            'ativo' => 1,
        ]);

        $relatorio = $this->requisitar('veterinarios/relatorio', 'GET', [
            'nome' => 'Teste',
        ]);

        $this->assertIgual(200, $relatorio->status);
        $this->assertContem('%PDF-1.4', $relatorio->html);
        $this->assertContem('Relatório de veterinários', $relatorio->html);
    }

    public function testeExigeLoginNasRotas(): void
    {
        $this->limparSessao();

        $semLogin = $this->requisitar('veterinarios');

        $this->assertVerdadeiro($semLogin->redirecionouPara('auth/login'));
    }
}
