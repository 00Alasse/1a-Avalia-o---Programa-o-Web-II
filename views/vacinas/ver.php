<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h3 mb-0"><?= e($titulo) ?> #<?= e($registro['id']) ?></h1>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('vacinas') ?>">Voltar</a>
        <a class="btn btn-primary" href="<?= url('vacinas/editar/' . $registro['id']) ?>">Editar</a>
        <form method="post" action="<?= url('vacinas/excluir/' . $registro['id']) ?>"
            onsubmit="return confirm('Excluir este registro?')">
            <?= campo_csrf() ?>
            <button class="btn btn-outline-danger" type="submit">Excluir</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <dl class="row g-0 mb-0 p-4">
        <dt class="col-sm-3">Identificador</dt>
        <dd class="col-sm-9">#<?= e($registro['id']) ?></dd>

        <dt class="col-sm-3">Animal (ID)</dt>
        <dd class="col-sm-9"><?= e($registro['animal_id'] ?? '') ?></dd>

        <dt class="col-sm-3">Veterinário (ID)</dt>
        <dd class="col-sm-9"><?= e($registro['veterinario_id'] ?? '') ?></dd>

        <dt class="col-sm-3">Nome da Vacina</dt>
        <dd class="col-sm-9"><strong><?= e($registro['nome_vacina'] ?? '') ?></strong></dd>

        <dt class="col-sm-3">Lote</dt>
        <dd class="col-sm-9"><?= e($registro['lote'] ?? '') ?></dd>

        <dt class="col-sm-3">Data de Aplicação</dt>
        <dd class="col-sm-9"><?= e(data_br($registro['data_aplicacao'] ?? '')) ?></dd>

        <dt class="col-sm-3">Previsão de Retorno</dt>
        <dd class="col-sm-9">
            <?php
            $dataRetorno = $registro['data_retorno'] ?? null;
            $dataRetornoValida = !empty($dataRetorno)
                && $dataRetorno !== '0000-00-00'
                && $dataRetorno !== '0000-00-00 00:00:00';
            ?>

            <?php if ($dataRetornoValida): ?>
                <span class="badge bg-info text-dark"><?= e(data_br($dataRetorno)) ?></span>
            <?php else: ?>
                <span class="text-secondary">Não informado</span>
            <?php endif ?>
        </dd>

        <!-- RF18: Autoria do lancamento da vacina -->
        <dt class="col-sm-3 text-primary">Registrado por</dt>
        <dd class="col-sm-9 text-primary font-weight-bold">
            <?= e($usuario['nome'] ?? 'Equipe') ?>
            <?= !empty($usuario['email']) ? '(' . e($usuario['email']) . ')' : '' ?>
        </dd>
    </dl>
</div>