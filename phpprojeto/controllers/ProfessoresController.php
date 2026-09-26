<?php
namespace Controllers;

use Modelos\Professor;
use Nucleo\Controller;
use Nucleo\Sessao;

class ProfessoresController extends Controller
{
    private Professor $professores;

    public function __construct()
    {
        $this->professores = new Professor();
    }

    // GET /professores
    public function index(): void
    {
        $this->view('professores/index', [
            'titulo' => 'Professores',
            'professores' => $this->professores->todos(),
        ]);
    }

    // GET /professores/criar
    public function criar(): void
    {
        $this->view('professores/formulario', [
            'titulo' => 'Novo professor',
            'acao' => url('professores/salvar'),
        ]);
    }

    // POST /professores/salvar
    public function salvar(): void
    {
        if (!$this->ehPost()) {
            $this->redirecionar('professores/criar');
        }

        $dados = $this->todosOsCampos();
        $erros = $this->professores->validar($dados);

        if ($erros !== []) {
            Sessao::guardarEntrada($dados);
            Sessao::guardarErros($erros);
            $this->mensagem('erro', 'Corrija os campos destacados.');
            $this->redirecionar('professores/criar');
        }

        $this->professores->criar($dados);
        $this->mensagem('sucesso', 'Professor cadastrado!');
        $this->redirecionar('professores');
    }

    // GET /professores/excluir/5
    public function excluir(string $id): void
    {
        if (!$this->professores->existe($id)) {
            $this->naoEncontrado("Professor {$id} nao existe.");
        }

        $this->professores->excluir($id);
        $this->mensagem('sucesso', 'Professor removido.');
        $this->redirecionar('professores');
    }
}