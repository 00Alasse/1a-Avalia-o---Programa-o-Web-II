<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Procedimentos</h1>
        <p class="text-secondary mb-0">Gerencie os registros cadastrados.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('procedimentos/relatorio') ?>">Relatório PDF</a>
        <a class="btn btn-primary" href="<?= url('procedimentos/criar') ?>">Novo registro</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Duração (minutos)</th>
                <th class="text-end">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($registros as $registro): ?>
            <tr>
                <td><a href="<?= url('procedimentos/ver/' . $registro['id']) ?>"><?= e($registro['id']) ?></a></td>
                <td><?= e($registro['descricao'] ?? '') ?></td>
                <td><?= e(moeda_br($registro['valor'] ?? '')) ?></td>
                <td><?= e($registro['duracao_minutos'] ?? '') ?></td>
                <td class="text-end text-nowrap">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= url('procedimentos/editar/' . $registro['id']) ?>">Editar</a>
                    <form class="d-inline" method="post" action="<?= url('procedimentos/excluir/' . $registro['id']) ?>" onsubmit="return confirm('Excluir este registro?')">
                        <?= campo_csrf() ?>
                        <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach ?>
            <?php if ($registros === []): ?>
            <tr><td colspan="5" class="text-center text-secondary py-4">Nenhum registro cadastrado.</td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
