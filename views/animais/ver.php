<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h3 mb-0"><?= e($titulo) ?></h1>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('animais') ?>">Voltar</a>
        <a class="btn btn-primary" href="<?= url('animais/editar/' . $registro['id']) ?>">Editar</a>
        <form method="post" action="<?= url('animais/excluir/' . $registro['id']) ?>" onsubmit="return confirm('Excluir este registro?')">
            <?= campo_csrf() ?>
            <button class="btn btn-outline-danger" type="submit">Excluir</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <dl class="row g-0 mb-0 p-4">
        <dt class="col-sm-3">id</dt>
        <dd class="col-sm-9"><?= e($registro['id']) ?></dd>
        <dt class="col-sm-3">nome</dt>
        <dd class="col-sm-9"><?= e($registro['nome'] ?? '') ?></dd>
        <dt class="col-sm-3">raca</dt>
        <dd class="col-sm-9"><?= e($registro['raca'] ?? '') ?></dd>
        <dt class="col-sm-3">data_nascimento</dt>
        <dd class="col-sm-9"><?= e($registro['data_nascimento'] ?? '') ?></dd>
        <dt class="col-sm-3">sexo</dt>
        <dd class="col-sm-9"><?= e($registro['sexo'] ?? '') ?></dd>
        <dt class="col-sm-3">peso</dt>
        <dd class="col-sm-9"><?= e($registro['peso'] ?? '') ?></dd>
        <dt class="col-sm-3">castrado</dt>
        <dd class="col-sm-9"><?= e(sim_nao($registro['castrado'] ?? null)) ?></dd>
        <dt class="col-sm-3">observacoes</dt>
        <dd class="col-sm-9"><?= e($registro['observacoes'] ?? '') ?></dd>
        <dt class="col-sm-3">tutor_id</dt>
        <dd class="col-sm-9"><?= e($registro['tutor_id'] ?? '') ?></dd>
        <dt class="col-sm-3">especie_id</dt>
        <dd class="col-sm-9"><?= e($registro['especie_id'] ?? '') ?></dd>
    </dl>
</div>
