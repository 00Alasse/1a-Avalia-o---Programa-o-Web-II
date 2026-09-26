<div class="mb-4">
    <h1 class="h3 mb-1"><?= e($titulo) ?></h1>
    <p class="text-secondary mb-0">Preencha os dados abaixo.</p>
</div>

<form class="card border-0 shadow-sm p-4" method="post" action="<?= url('vacinas/' . ($registro ? 'atualizar/' . $registro['id'] : 'salvar')) ?>">
    <?= campo_csrf() ?>
    <div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="animal_id">Animal</label>
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
        <label class="form-label" for="veterinario_id">Veterinário</label>
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
        <label class="form-label" for="nome_vacina">Nome da vacina</label>
        <input class="form-control <?= tem_erro('nome_vacina') ? 'is-invalid' : '' ?>" id="nome_vacina" type="text" name="nome_vacina" value="<?= e(antigo('nome_vacina', $registro['nome_vacina'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('nome_vacina')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="lote">Lote</label>
        <input class="form-control <?= tem_erro('lote') ? 'is-invalid' : '' ?>" id="lote" type="text" name="lote" value="<?= e(antigo('lote', $registro['lote'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('lote')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="data_aplicacao">Data de aplicação</label>
        <input class="form-control <?= tem_erro('data_aplicacao') ? 'is-invalid' : '' ?>" id="data_aplicacao" type="date" name="data_aplicacao" value="<?= e(antigo('data_aplicacao', $registro['data_aplicacao'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('data_aplicacao')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="data_retorno">Data de retorno</label>
        <input class="form-control <?= tem_erro('data_retorno') ? 'is-invalid' : '' ?>" id="data_retorno" type="date" name="data_retorno" value="<?= e(antigo('data_retorno', $registro['data_retorno'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('data_retorno')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">Salvar</button>
        <a class="btn btn-outline-secondary" href="<?= url('vacinas') ?>">Cancelar</a>
    </div>
</form>
