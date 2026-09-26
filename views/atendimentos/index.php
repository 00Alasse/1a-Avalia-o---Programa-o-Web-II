<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">atendimentos</h1>
        <p class="text-secondary mb-0">Gerencie os registros cadastrados.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('atendimentos/relatorio') ?>">Relatorio PDF</a>
        <a class="btn btn-primary" href="<?= url('atendimentos/criar') ?>">Novo registro</a>
    </div>
</div>

<!-- scaffold:pesquisa inicio -->
<form class="card border-0 shadow-sm p-3 mb-3" method="get" action="<?= url('atendimentos') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_data_hora">data_hora</label>
            <input class="form-control" id="pesquisa_data_hora" type="date" name="data_hora" value="<?= e($pesquisa['data_hora'] ?? '') ?>">
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_veterinario_id">veterinario_id</label>
            <?php $escolhido = (string) ($pesquisa['veterinario_id'] ?? ''); ?>
            <select class="form-select" id="pesquisa_veterinario_id" name="veterinario_id">
                <option value="">Todos</option>
                <?php foreach (($veterinarios ?? []) as $opcao): ?>
                    <option value="<?= e($opcao['id']) ?>" <?= $escolhido === (string) $opcao['id'] ? 'selected' : '' ?>><?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_situacao">situacao</label>
            <input class="form-control" id="pesquisa_situacao" type="text" name="situacao" value="<?= e($pesquisa['situacao'] ?? '') ?>">
        </div>
        <div class="col-12 col-lg-auto d-flex gap-2">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
            <a class="btn btn-outline-secondary" href="<?= url('atendimentos') ?>">Limpar</a>
        </div>
    </div>
</form>
<!-- scaffold:pesquisa fim -->

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>animal_id</th>
                <th>veterinario_id</th>
                <th>procedimento_id</th>
                <th>data_hora</th>
                <th>valor_cobrado</th>
                <th>observacoes_clinicas</th>
                <th>situacao</th>
                <th class="text-end">Acoes</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($registros as $registro): ?>
            <tr>
                <td><a href="<?= url('atendimentos/ver/' . $registro['id']) ?>"><?= e($registro['id']) ?></a></td>
                <td><?= e($registro['animal_id'] ?? '') ?></td>
                <td><?= e($registro['veterinario_id'] ?? '') ?></td>
                <td><?= e($registro['procedimento_id'] ?? '') ?></td>
                <td><?= e($registro['data_hora'] ?? '') ?></td>
                <td><?= e($registro['valor_cobrado'] ?? '') ?></td>
                <td><?= e($registro['observacoes_clinicas'] ?? '') ?></td>
                <td><?= e($registro['situacao'] ?? '') ?></td>
                <td class="text-end text-nowrap">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= url('atendimentos/editar/' . $registro['id']) ?>">Editar</a>
                    <form class="d-inline" method="post" action="<?= url('atendimentos/excluir/' . $registro['id']) ?>" onsubmit="return confirm('Excluir este registro?')">
                        <?= campo_csrf() ?>
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach ?>
            <?php if ($registros === []): ?>
            <tr><td colspan="9" class="text-center text-secondary py-4"><?= ($pesquisa ?? []) === [] ? 'Nenhum registro cadastrado.' : 'Nenhum registro encontrado para a pesquisa.' ?></td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
