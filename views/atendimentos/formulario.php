<div class="mb-4">
    <h1 class="h3 mb-1"><?= e($titulo) ?></h1>
    <p class="text-secondary mb-0">Preencha os dados abaixo.</p>
</div>

<form class="card border-0 shadow-sm p-4" method="post" action="<?= url('atendimentos/' . ($registro ? 'atualizar/' . $registro['id'] : 'salvar')) ?>">
    <?= campo_csrf() ?>
    <div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="animal_id">animal_id</label>
        <?php $selecionado = antigo('animal_id', $registro['animal_id'] ?? ''); ?>
        <select class="form-select <?= tem_erro('animal_id') ? 'is-invalid' : '' ?>" id="animal_id" name="animal_id">
            <option value="">Selecione...</option>
            <?php foreach (($animais ?? []) as $opcao): ?>
                <option value="<?= e($opcao['id']) ?>" <?= (string) $selecionado === (string) $opcao['id'] ? 'selected' : '' ?>><?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
            <?php endforeach ?>
        </select>
        <?php if ($mensagem = erro_de('animal_id')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="veterinario_id">veterinario_id</label>
        <?php $selecionado = antigo('veterinario_id', $registro['veterinario_id'] ?? ''); ?>
        <select class="form-select <?= tem_erro('veterinario_id') ? 'is-invalid' : '' ?>" id="veterinario_id" name="veterinario_id">
            <option value="">Selecione...</option>
            <?php foreach (($veterinarios ?? []) as $opcao): ?>
                <option value="<?= e($opcao['id']) ?>" <?= (string) $selecionado === (string) $opcao['id'] ? 'selected' : '' ?>><?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
            <?php endforeach ?>
        </select>
        <?php if ($mensagem = erro_de('veterinario_id')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="procedimento_id">procedimento_id</label>
        <?php $selecionado = antigo('procedimento_id', $registro['procedimento_id'] ?? ''); ?>
        <select class="form-select <?= tem_erro('procedimento_id') ? 'is-invalid' : '' ?>" id="procedimento_id" name="procedimento_id">
            <option value="">Selecione...</option>
            <?php foreach (($procedimentos ?? []) as $opcao): ?>
                <option value="<?= e($opcao['id']) ?>" <?= (string) $selecionado === (string) $opcao['id'] ? 'selected' : '' ?>><?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
            <?php endforeach ?>
        </select>
        <?php if ($mensagem = erro_de('procedimento_id')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="data_hora">data_hora</label>
        <input class="form-control <?= tem_erro('data_hora') ? 'is-invalid' : '' ?>" id="data_hora" type="datetime-local" name="data_hora" value="<?= e(antigo('data_hora', $registro['data_hora'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('data_hora')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="valor_cobrado">valor_cobrado</label>
        <input class="form-control <?= tem_erro('valor_cobrado') ? 'is-invalid' : '' ?>" id="valor_cobrado" type="number" step="0.01" name="valor_cobrado" value="<?= e(antigo('valor_cobrado', $registro['valor_cobrado'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('valor_cobrado')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-12">
        <label class="form-label" for="observacoes_clinicas">observacoes_clinicas</label>
        <textarea class="form-control <?= tem_erro('observacoes_clinicas') ? 'is-invalid' : '' ?>" id="observacoes_clinicas" name="observacoes_clinicas" rows="4"><?= e(antigo('observacoes_clinicas', $registro['observacoes_clinicas'] ?? '')) ?></textarea>
        <?php if ($mensagem = erro_de('observacoes_clinicas')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="situacao">situacao</label>
        <input class="form-control <?= tem_erro('situacao') ? 'is-invalid' : '' ?>" id="situacao" type="text" name="situacao" value="<?= e(antigo('situacao', $registro['situacao'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('situacao')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">Salvar</button>
        <a class="btn btn-outline-secondary" href="<?= url('atendimentos') ?>">Cancelar</a>
    </div>
</form>
