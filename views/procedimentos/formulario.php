<div class="mb-4">
    <h1 class="h3 mb-1"><?= e($titulo) ?></h1>
    <p class="text-secondary mb-0">Preencha os dados abaixo.</p>
</div>

<form class="card border-0 shadow-sm p-4" method="post" action="<?= url('procedimentos/' . ($registro ? 'atualizar/' . $registro['id'] : 'salvar')) ?>">
    <?= campo_csrf() ?>
    <div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="descricao">Descrição</label>
        <input class="form-control <?= tem_erro('descricao') ? 'is-invalid' : '' ?>" id="descricao" type="text" name="descricao" value="<?= e(antigo('descricao', $registro['descricao'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('descricao')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="valor">Valor</label>
        <input class="form-control <?= tem_erro('valor') ? 'is-invalid' : '' ?>" id="valor" type="number" step="0.01" name="valor" value="<?= e(antigo('valor', $registro['valor'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('valor')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="duracao_minutos">Duração (minutos)</label>
        <input class="form-control <?= tem_erro('duracao_minutos') ? 'is-invalid' : '' ?>" id="duracao_minutos" type="number" name="duracao_minutos" value="<?= e(antigo('duracao_minutos', $registro['duracao_minutos'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('duracao_minutos')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">Salvar</button>
        <a class="btn btn-outline-secondary" href="<?= url('procedimentos') ?>">Cancelar</a>
    </div>
</form>
