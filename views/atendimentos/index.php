<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Atendimentos</h1>
        <p class="text-secondary mb-0">Gerencie as consultas e procedimentos da clínica.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('atendimentos/relatorio') ?>">Relatório PDF</a>
        <a class="btn btn-primary" href="<?= url('atendimentos/criar') ?>">Novo Atendimento</a>
    </div>
</div>

<!-- scaffold:pesquisa inicio -->
<form class="card border-0 shadow-sm p-3 mb-3" method="get" action="<?= url('atendimentos') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_data_hora">Data do Atendimento</label>
            <input class="form-control" id="pesquisa_data_hora" type="date" name="data_hora"
                value="<?= e($pesquisa['data_hora'] ?? '') ?>">
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_veterinario_id">Veterinário</label>
            <?php $escolhido = (string) ($pesquisa['veterinario_id'] ?? ''); ?>
            <select class="form-select" id="pesquisa_veterinario_id" name="veterinario_id">
                <option value="">Todos</option>
                <?php foreach (($veterinarios ?? []) as $opcao): ?>
                    <option value="<?= e($opcao['id']) ?>" <?= $escolhido === (string) $opcao['id'] ? 'selected' : '' ?>>
                        <?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label small text-secondary mb-1" for="pesquisa_situacao">Situação</label>
            <input class="form-control" id="pesquisa_situacao" type="text" name="situacao"
                placeholder="agendado, realizado..." value="<?= e($pesquisa['situacao'] ?? '') ?>">
        </div>
        <div class="col-12 col-lg-auto d-flex gap-2">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
            <a class="btn btn-outline-secondary" href="<?= url('atendimentos') ?>">Limpar</a>
        </div>
    </div>
</form>
<!-- scaffold:pesquisa fim -->

<!-- RF13: Resumo com contagem e soma dos valores filtrados -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <span class="text-secondary small">Total de Atendimentos</span>
            <strong class="fs-4 text-primary"><?= e($totalAtendimentos ?? count($registros)) ?></strong>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <span class="text-secondary small">Valor Total</span>
            <strong class="fs-4 text-success">R$ <?= e(moeda_br($somaValores ?? 0)) ?></strong>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Animal</th>
                    <th>Veterinário</th>
                    <th>Procedimento</th>
                    <th>Data e Horário</th>
                    <th>Valor Cobrado</th>
                    <th>Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><a href="<?= url('atendimentos/ver/' . $registro['id']) ?>">#<?= e($registro['id']) ?></a></td>
                        <td><?= e($animaisPorId[$registro['animal_id']]['nome'] ?? 'Animal não encontrado') ?></td>
                        <td><?= e($veterinariosPorId[$registro['veterinario_id']]['nome'] ?? 'Veterinário não encontrado') ?></td>
                        <td><?= e($procedimentosPorId[$registro['procedimento_id']]['descricao'] ?? 'Procedimento não encontrado') ?></td>
                        <td><?= e(data_br($registro['data_hora'] ?? '', true)) ?></td>
                        <td>R$ <?= e(moeda_br($registro['valor_cobrado'] ?? 0)) ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= e($registro['situacao'] ?? '') ?></span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-secondary"
                                href="<?= url('atendimentos/editar/' . $registro['id']) ?>">Editar</a>
                            <form class="d-inline" method="post"
                                action="<?= url('atendimentos/excluir/' . $registro['id']) ?>"
                                onsubmit="return confirm('Excluir este registro?')">
                                <?= campo_csrf() ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if ($registros === []): ?>
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">
                            <?= ($pesquisa ?? []) === [] ? 'Nenhum atendimento cadastrado.' : 'Nenhum atendimento encontrado para a pesquisa.' ?>
                        </td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>