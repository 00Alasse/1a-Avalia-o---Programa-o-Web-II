<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h3 mb-0"><?= e($titulo) ?></h1>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('veterinarios') ?>">Voltar</a>
        <a class="btn btn-primary" href="<?= url('veterinarios/editar/' . $registro['id']) ?>">Editar</a>
        <form method="post" action="<?= url('veterinarios/excluir/' . $registro['id']) ?>" onsubmit="return confirm('Excluir este registro?')">
            <?= campo_csrf() ?>
            <button class="btn btn-outline-danger" type="submit">Excluir</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <dl class="row g-0 mb-0 p-4">
        <dt class="col-sm-3">ID</dt>
        <dd class="col-sm-9"><?= e($registro['id']) ?></dd>
        <dt class="col-sm-3">Nome</dt>
        <dd class="col-sm-9"><?= e($registro['nome'] ?? '') ?></dd>
        <dt class="col-sm-3">CRMV</dt>
        <dd class="col-sm-9"><?= e($registro['crmv'] ?? '') ?></dd>
        <dt class="col-sm-3">Especialidade</dt>
        <dd class="col-sm-9"><?= e($registro['especialidade'] ?? '') ?></dd>
        <dt class="col-sm-3">Telefone</dt>
        <dd class="col-sm-9"><?= e($registro['telefone'] ?? '') ?></dd>
        <dt class="col-sm-3">Ativo</dt>
        <dd class="col-sm-9"><?= e(sim_nao($registro['ativo'] ?? null)) ?></dd>
    </dl>
</div>
