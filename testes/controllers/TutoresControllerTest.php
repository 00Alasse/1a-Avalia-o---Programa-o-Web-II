<?php

namespace Testes\Controllers;

use Modelos\Tutor;
use Nucleo\Database;
use Nucleo\Sessao;
use Testes\Suporte\TesteBase;

class TutoresControllerTest extends TesteBase
{
    private Tutor $modelo;
    private array $idsRelacoes = [];
    private array $idsRelacoesAtualizadas = [];

    public function preparar(): void
    {
        $this->limparSessao();

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
        Sessao::definir(Sessao::chaveAutenticacao(), 1);
    }

    public function testeExecutaRotasDoCrud(): void
    {
        $lista = $this->requisitar('tutores');
        $this->assertIgual(200, $lista->status);
        $this->assertContem('nome', $lista->html);
        $this->assertContem('tutores/relatorio', $lista->html);

        $formulario = $this->requisitar('tutores/criar');
        $this->assertIgual(200, $formulario->status);
        $this->assertContem('Salvar', $formulario->html);

        $salvar = $this->postar('tutores/salvar', [
            'nome' => 'Teste',
            'cpf' => 'Teste',
            'telefone' => 'Teste',
            'email' => 'ana@example.com',
            'endereco' => 'Teste',
            'data_cliente' => '2026-01-01',
        ]);
        $this->assertVerdadeiro($salvar->redirecionouPara('tutores/ver/1'));

        $registro = $this->modelo->todos()[0] ?? null;
        $this->assertNaoNulo($registro);

        $id = (int) $registro['id'];
        $this->assertIgual('Teste', $registro['nome']);

        $ver = $this->requisitar('tutores/ver/' . $id);
        $this->assertIgual(200, $ver->status);
        $this->assertContem('tutores/editar/' . $id, $ver->html);

        $editar = $this->requisitar('tutores/editar/' . $id);
        $this->assertIgual(200, $editar->status);
        $this->assertContem('Editar Tutor', $editar->html);

        $atualizar = $this->postar('tutores/atualizar/' . $id, [
            'nome' => 'Atualizado',
            'cpf' => 'Atualizado',
            'telefone' => 'Atualizado',
            'email' => 'maria@example.com',
            'endereco' => 'Atualizado',
            'data_cliente' => '2026-02-02',
        ]);
        $this->assertVerdadeiro($atualizar->redirecionouPara('tutores/ver/' . $id));
        $this->assertIgual('Atualizado', $this->modelo->buscar($id)['nome']);

        $excluir = $this->postar('tutores/excluir/' . $id);
        $this->assertVerdadeiro($excluir->redirecionouPara('tutores'));
        $this->assertNulo($this->modelo->buscar($id));
    }

    public function testeRecusaDadosInvalidos(): void
    {
        $resposta = $this->postar('tutores/salvar', [
            'nome' => '',
            'cpf' => 'Teste',
            'telefone' => 'Teste',
            'email' => 'ana@example.com',
            'endereco' => 'Teste',
            'data_cliente' => '2026-01-01',
        ]);

        $this->assertVerdadeiro($resposta->redirecionouPara('tutores/criar'));
        $this->assertIgual(0, $this->modelo->contar());
    }

    public function testeRecusaFormularioSemToken(): void
    {
        $id = $this->modelo->criar([
            'nome' => 'Teste',
            'cpf' => 'Teste',
            'telefone' => 'Teste',
            'email' => 'ana@example.com',
            'endereco' => 'Teste',
            'data_cliente' => '2026-01-01',
        ]);

        $semToken = $this->postarSemToken('tutores/excluir/' . $id);

        $this->assertVerdadeiro($semToken->foiRedirecionado());
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeExclusaoNaoAceitaGet(): void
    {
        $id = $this->modelo->criar([
            'nome' => 'Teste',
            'cpf' => 'Teste',
            'telefone' => 'Teste',
            'email' => 'ana@example.com',
            'endereco' => 'Teste',
            'data_cliente' => '2026-01-01',
        ]);

        $porGet = $this->requisitar('tutores/excluir/' . $id);

        $this->assertIgual(404, $porGet->status);
        $this->assertNaoNulo($this->modelo->buscar($id));
    }

    public function testeGeraRelatorioEmPdf(): void
    {
        $this->modelo->criar([
            'nome' => 'Teste',
            'cpf' => 'Teste',
            'telefone' => 'Teste',
            'email' => 'ana@example.com',
            'endereco' => 'Teste',
            'data_cliente' => '2026-01-01',
        ]);

        $relatorio = $this->requisitar('tutores/relatorio', 'GET', [
            'nome' => 'Teste',
        ]);

        $this->assertIgual(200, $relatorio->status);
        $this->assertContem('%PDF-1.4', $relatorio->html);
        $this->assertContem('Relatorio de tutores', $relatorio->html);
    }

    public function testeExigeLoginNasRotas(): void
    {
        $this->limparSessao();

        $semLogin = $this->requisitar('tutores');

        $this->assertVerdadeiro($semLogin->redirecionouPara('auth/login'));
    }
}
