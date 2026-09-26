<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h3 mb-0"><?= e($titulo) ?></h1>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('atendimentos') ?>">Voltar</a>
        <a class="btn btn-primary" href="<?= url('atendimentos/editar/' . $registro['id']) ?>">Editar</a>
        <form method="post" action="<?= url('atendimentos/excluir/' . $registro['id']) ?>" onsubmit="return confirm('Excluir este registro?')">
            <?= campo_csrf() ?>
            <button class="btn btn-outline-danger" type="submit">Excluir</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <dl class="row g-0 mb-0 p-4">
        <dt class="col-sm-3">id</dt>
        <dd class="col-sm-9"><?= e($registro['id']) ?></dd>
        <dt class="col-sm-3">animal_id</dt>
        <dd class="col-sm-9"><?= e($registro['animal_id'] ?? '') ?></dd>
        <dt class="col-sm-3">veterinario_id</dt>
        <dd class="col-sm-9"><?= e($registro['veterinario_id'] ?? '') ?></dd>
        <dt class="col-sm-3">procedimento_id</dt>
        <dd class="col-sm-9"><?= e($registro['procedimento_id'] ?? '') ?></dd>
        <dt class="col-sm-3">data_hora</dt>
        <dd class="col-sm-9"><?= e($registro['data_hora'] ?? '') ?></dd>
        <dt class="col-sm-3">valor_cobrado</dt>
        <dd class="col-sm-9"><?= e($registro['valor_cobrado'] ?? '') ?></dd>
        <dt class="col-sm-3">observacoes_clinicas</dt>
        <dd class="col-sm-9"><?= e($registro['observacoes_clinicas'] ?? '') ?></dd>
        <dt class="col-sm-3">situacao</dt>
        <dd class="col-sm-9"><?= e($registro['situacao'] ?? '') ?></dd>
    </dl>
</div>
