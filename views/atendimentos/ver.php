<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h3 mb-0"><?= e($titulo) ?> #<?= e($registro['id']) ?></h1>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('atendimentos') ?>">Voltar</a>
        <a class="btn btn-primary" href="<?= url('atendimentos/editar/' . $registro['id']) ?>">Editar</a>
        <form method="post" action="<?= url('atendimentos/excluir/' . $registro['id']) ?>"
            onsubmit="return confirm('Excluir este atendimento?')">
            <?= campo_csrf() ?>
            <button class="btn btn-outline-danger" type="submit">Excluir</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <dl class="row g-0 mb-0 p-4">
        <dt class="col-sm-3">Identificador</dt>
        <dd class="col-sm-9">#<?= e($registro['id']) ?></dd>

        <dt class="col-sm-3">Animal</dt>
        <dd class="col-sm-9">
            <?= e($animal['nome'] ?? 'Animal não encontrado') ?>
        </dd>

        <dt class="col-sm-3">Veterinário</dt>
        <dd class="col-sm-9">
            <?= e($veterinario['nome'] ?? 'Veterinário não encontrado') ?>
        </dd>

        <dt class="col-sm-3">Procedimento</dt>
        <dd class="col-sm-9">
            <?= e($procedimento['descricao'] ?? 'Procedimento não encontrado') ?>
        </dd>

        <dt class="col-sm-3">Data e Horário</dt>
        <dd class="col-sm-9"><?= e(data_br($registro['data_hora'] ?? '', true)) ?></dd>

        <dt class="col-sm-3">Valor Cobrado</dt>
        <dd class="col-sm-9">R$ <?= e(moeda_br($registro['valor_cobrado'] ?? 0)) ?></dd>

        <dt class="col-sm-3">Situação</dt>
        <dd class="col-sm-9"><span class="badge bg-primary"><?= e($registro['situacao'] ?? '') ?></span></dd>

        <dt class="col-sm-3">Observações Clínicas</dt>
        <dd class="col-sm-9"><?= nl2br(e($registro['observacoes_clinicas'] ?? 'Nenhuma observação informada.')) ?></dd>

        <!-- RF18: Exibicao da autoria do lancamento pela equipe -->
        <dt class="col-sm-3 text-primary">Registrado por</dt>
        <dd class="col-sm-9 text-primary font-weight-bold">
            <?= e($usuario['nome'] ?? 'Equipe') ?>
            <?= !empty($usuario['email']) ? '(' . e($usuario['email']) . ')' : '' ?>
        </dd>
    </dl>
</div>