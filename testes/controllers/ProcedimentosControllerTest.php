<?php

namespace Testes\Controllers;

use Modelos\Procedimento;
use Nucleo\Database;
use Nucleo\Sessao;
use Testes\Suporte\TesteBase;

class ProcedimentosControllerTest extends TesteBase
{
    private Procedimento $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        $this->limparSessao();

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
        Sessao::definir(Sessao::chaveAutenticacao(), 1);
    }

    public function testeExecutaRotasDoCrud(): void
    {
        $lista = $this->requisitar('procedimentos');
        $this->assertIgual(200, $lista->status);
        $this->assertContem('descricao', $lista->html);
        $this->assertContem('procedimentos/relatorio', $lista->html);

        $formulario = $this->requisitar('procedimentos/criar');
        $this->assertIgual(200, $formulario->status);
        $this->assertContem('Salvar', $formulario->html);

        $salvar = $this->postar('procedimentos/salvar', [
            'descricao' => 'Teste',
            'valor' => 10.5,
            'duracao_minutos' => 1,
        ]);
        $this->assertVerdadeiro($salvar->redirecionouPara('procedimentos/ver/1'));

        $registro = $this->modelo->todos()[0] ?? null;
        $this->assertNaoNulo($registro);

        $id = (int) $registro['id'];
        $this->assertIgual('Teste', $registro['descricao']);

        $ver = $this->requisitar('procedimentos/ver/' . $id);
        $this->assertIgual(200, $ver->status);
        $this->assertContem('procedimentos/editar/' . $id, $ver->html);

        $editar = $this->requisitar('procedimentos/editar/' . $id);
        $this->assertIgual(200, $editar->status);
        $this->assertContem('Editar Procedimento', $editar->html);

        $atualizar = $this->postar('procedimentos/atualizar/' . $id, [
            'descricao' => 'Atualizado',
            'valor' => 20.5,
            'duracao_minutos' => 2,
        ]);
        $this->assertVerdadeiro($atualizar->redirecionouPara('procedimentos/ver/' . $id));
        $this->assertIgual('Atualizado', $this->modelo->buscar($id)['descricao']);

        $excluir = $this->postar('procedimentos/excluir/' . $id);
        $this->assertVerdadeiro($excluir->redirecionouPara('procedimentos'));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeRecusaDadosInvalidos(): void
    {
        $resposta = $this->postar('procedimentos/salvar', [
            'descricao' => '',
            'valor' => 10.5,
            'duracao_minutos' => 1,
        ]);

        $this->assertVerdadeiro($resposta->redirecionouPara('procedimentos/criar'));
        $this->assertIgual(0, $this->modelo->contar());
    }

    public function testeRecusaFormularioSemToken(): void
    {
        $id = $this->modelo->criar([
            'descricao' => 'Teste',
            'valor' => 10.5,
            'duracao_minutos' => 1,
        ]);

        $semToken = $this->postarSemToken('procedimentos/excluir/' . $id);

        $this->assertVerdadeiro($semToken->foiRedirecionado());
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeExclusaoNaoAceitaGet(): void
    {
        $id = $this->modelo->criar([
            'descricao' => 'Teste',
            'valor' => 10.5,
            'duracao_minutos' => 1,
        ]);

        $porGet = $this->requisitar('procedimentos/excluir/' . $id);

        $this->assertIgual(404, $porGet->status);
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeGeraRelatorioEmPdf(): void
    {
        $this->modelo->criar([
            'descricao' => 'Teste',
            'valor' => 10.5,
            'duracao_minutos' => 1,
        ]);

        $relatorio = $this->requisitar('procedimentos/relatorio', 'GET', [
            'descricao' => 'Teste',
        ]);

        $this->assertIgual(200, $relatorio->status);
        $this->assertContem('%PDF-1.4', $relatorio->html);
        $this->assertContem('Relatorio de procedimentos', $relatorio->html);
    }

    public function testeExigeLoginNasRotas(): void
    {
        $this->limparSessao();

        $semLogin = $this->requisitar('procedimentos');

        $this->assertVerdadeiro($semLogin->redirecionouPara('auth/login'));
    }
}
