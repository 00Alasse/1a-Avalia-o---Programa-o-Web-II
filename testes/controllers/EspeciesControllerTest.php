<?php

namespace Testes\Controllers;

use Modelos\Especie;
use Nucleo\Database;
use Nucleo\Sessao;
use Testes\Suporte\TesteBase;

class EspeciesControllerTest extends TesteBase
{
    private Especie $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        $this->limparSessao();

        // Cada teste monta as proprias tabelas: a ordem em que as
        // classes rodam nao interfere no resultado.
        $this->recriarTabelas([
            'especies' => "CREATE TABLE `especies` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nome` VARCHAR(255) NULL,
                `observacoes` TEXT NULL
            )",
        ]);


        $this->modelo = new Especie();
        Sessao::definir(Sessao::chaveAutenticacao(), 1);
    }

    public function testeExecutaRotasDoCrud(): void
    {
        $lista = $this->requisitar('especies');
        $this->assertIgual(200, $lista->status);
        $this->assertContem('nome', $lista->html);
        $this->assertContem('especies/relatorio', $lista->html);

        $formulario = $this->requisitar('especies/criar');
        $this->assertIgual(200, $formulario->status);
        $this->assertContem('Salvar', $formulario->html);

        $salvar = $this->postar('especies/salvar', [
            'nome' => 'Teste',
            'observacoes' => 'Teste',
        ]);
        $this->assertVerdadeiro($salvar->redirecionouPara('especies/ver/1'));

        $registro = $this->modelo->todos()[0] ?? null;
        $this->assertNaoNulo($registro);

        $id = (int) $registro['id'];
        $this->assertIgual('Teste', $registro['nome']);

        $ver = $this->requisitar('especies/ver/' . $id);
        $this->assertIgual(200, $ver->status);
        $this->assertContem('especies/editar/' . $id, $ver->html);

        $editar = $this->requisitar('especies/editar/' . $id);
        $this->assertIgual(200, $editar->status);
        $this->assertContem('Editar Especie', $editar->html);

        $atualizar = $this->postar('especies/atualizar/' . $id, [
            'nome' => 'Atualizado',
            'observacoes' => 'Atualizado',
        ]);
        $this->assertVerdadeiro($atualizar->redirecionouPara('especies/ver/' . $id));
        $this->assertIgual('Atualizado', $this->modelo->buscar($id)['nome']);

        $excluir = $this->postar('especies/excluir/' . $id);
        $this->assertVerdadeiro($excluir->redirecionouPara('especies'));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeRecusaDadosInvalidos(): void
    {
        $resposta = $this->postar('especies/salvar', [
            'nome' => '',
            'observacoes' => 'Teste',
        ]);

        $this->assertVerdadeiro($resposta->redirecionouPara('especies/criar'));
        $this->assertIgual(0, $this->modelo->contar());
    }

    public function testeRecusaFormularioSemToken(): void
    {
        $id = $this->modelo->criar([
            'nome' => 'Teste',
            'observacoes' => 'Teste',
        ]);

        $semToken = $this->postarSemToken('especies/excluir/' . $id);

        $this->assertVerdadeiro($semToken->foiRedirecionado());
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeExclusaoNaoAceitaGet(): void
    {
        $id = $this->modelo->criar([
            'nome' => 'Teste',
            'observacoes' => 'Teste',
        ]);

        $porGet = $this->requisitar('especies/excluir/' . $id);

        $this->assertIgual(404, $porGet->status);
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeGeraRelatorioEmPdf(): void
    {
        $this->modelo->criar([
            'nome' => 'Teste',
            'observacoes' => 'Teste',
        ]);

        $relatorio = $this->requisitar('especies/relatorio', 'GET', [
            'nome' => 'Teste',
        ]);

        $this->assertIgual(200, $relatorio->status);
        $this->assertContem('%PDF-1.4', $relatorio->html);
        $this->assertContem('Relatorio de especies', $relatorio->html);
    }

    public function testeExigeLoginNasRotas(): void
    {
        $this->limparSessao();

        $semLogin = $this->requisitar('especies');

        $this->assertVerdadeiro($semLogin->redirecionouPara('auth/login'));
    }
}
