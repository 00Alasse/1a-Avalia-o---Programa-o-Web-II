<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Tutores</h1>
        <p class="text-secondary mb-0">Gerencie os registros cadastrados.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('tutores/relatorio') ?>">Relatório PDF</a>
        <a class="btn btn-primary" href="<?= url('tutores/criar') ?>">Novo registro</a>
    </div>
</div>

<!-- Pesquisa -->
<form class="card border-0 shadow-sm p-3 mb-3" method="get" action="<?= url('tutores') ?>">

    <div class="row g-2 align-items-end">

        <div class="col-12 col-sm-8 col-lg-5">
            <label class="form-label small text-secondary mb-1" for="pesquisa_nome">
                Nome
            </label>

            <input class="form-control" id="pesquisa_nome" type="text" name="nome"
                value="<?= e($pesquisa['nome'] ?? '') ?>">
        </div>

        <div class="col-12 col-lg-auto d-flex gap-2">
            <button class="btn btn-primary" type="submit">
                Pesquisar
            </button>

            <a class="btn btn-outline-secondary" href="<?= url('tutores') ?>">
                Limpar
            </a>
        </div>

    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>Endereço</th>
                    <th>Data de cadastro</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><a href="<?= url('tutores/ver/' . $registro['id']) ?>"><?= e($registro['id']) ?></a></td>
                        <td><?= e($registro['nome'] ?? '') ?></td>
                        <td><?= e($registro['cpf'] ?? '') ?></td>
                        <td><?= e($registro['telefone'] ?? '') ?></td>
                        <td><?= e($registro['email'] ?? '') ?></td>
                        <td><?= e($registro['endereco'] ?? '') ?></td>
                        <td><?= e(data_br($registro['data_cliente'] ?? '')) ?></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-secondary"
                                href="<?= url('tutores/editar/' . $registro['id']) ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= url('tutores/excluir/' . $registro['id']) ?>"
                                onsubmit="return confirm('Excluir este registro?')">
                                <?= campo_csrf() ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if ($registros === []): ?>
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">Nenhum registro cadastrado.</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>