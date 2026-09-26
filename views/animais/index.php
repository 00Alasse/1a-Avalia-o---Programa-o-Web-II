<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">animais</h1>
        <p class="text-secondary mb-0">Gerencie os registros cadastrados.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('animais/relatorio') ?>">Relatorio PDF</a>
        <a class="btn btn-primary" href="<?= url('animais/criar') ?>">Novo registro</a>
    </div>
</div>

<!-- scaffold:pesquisa inicio -->
<form class="card border-0 shadow-sm p-3 mb-3" method="get" action="<?= url('animais') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_nome">nome</label>
            <input class="form-control" id="pesquisa_nome" type="text" name="nome" value="<?= e($pesquisa['nome'] ?? '') ?>">
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_especie_id">especie_id</label>
            <?php $escolhido = (string) ($pesquisa['especie_id'] ?? ''); ?>
            <select class="form-select" id="pesquisa_especie_id" name="especie_id">
                <option value="">Todos</option>
                <?php foreach (($especies ?? []) as $opcao): ?>
                    <option value="<?= e($opcao['id']) ?>" <?= $escolhido === (string) $opcao['id'] ? 'selected' : '' ?>><?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_tutor_id">tutor_id</label>
            <?php $escolhido = (string) ($pesquisa['tutor_id'] ?? ''); ?>
            <select class="form-select" id="pesquisa_tutor_id" name="tutor_id">
                <option value="">Todos</option>
                <?php foreach (($tutores ?? []) as $opcao): ?>
                    <option value="<?= e($opcao['id']) ?>" <?= $escolhido === (string) $opcao['id'] ? 'selected' : '' ?>><?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-12 col-lg-auto d-flex gap-2">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
            <a class="btn btn-outline-secondary" href="<?= url('animais') ?>">Limpar</a>
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
                <th>nome</th>
                <th>raca</th>
                <th>data_nascimento</th>
                <th>sexo</th>
                <th>peso</th>
                <th>castrado</th>
                <th>observacoes</th>
                <th>tutor_id</th>
                <th>especie_id</th>
                <th class="text-end">Acoes</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($registros as $registro): ?>
            <tr>
                <td><a href="<?= url('animais/ver/' . $registro['id']) ?>"><?= e($registro['id']) ?></a></td>
                <td><?= e($registro['nome'] ?? '') ?></td>
                <td><?= e($registro['raca'] ?? '') ?></td>
                <td><?= e($registro['data_nascimento'] ?? '') ?></td>
                <td><?= e($registro['sexo'] ?? '') ?></td>
                <td><?= e($registro['peso'] ?? '') ?></td>
                <td><?= e(sim_nao($registro['castrado'] ?? null)) ?></td>
                <td><?= e($registro['observacoes'] ?? '') ?></td>
                <td><?= e($registro['tutor_id'] ?? '') ?></td>
                <td><?= e($registro['especie_id'] ?? '') ?></td>
                <td class="text-end text-nowrap">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= url('animais/editar/' . $registro['id']) ?>">Editar</a>
                    <form class="d-inline" method="post" action="<?= url('animais/excluir/' . $registro['id']) ?>" onsubmit="return confirm('Excluir este registro?')">
                        <?= campo_csrf() ?>
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach ?>
            <?php if ($registros === []): ?>
            <tr><td colspan="11" class="text-center text-secondary py-4"><?= ($pesquisa ?? []) === [] ? 'Nenhum registro cadastrado.' : 'Nenhum registro encontrado para a pesquisa.' ?></td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
