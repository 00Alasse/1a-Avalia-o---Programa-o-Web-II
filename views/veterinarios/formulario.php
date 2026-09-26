<div class="mb-4">
    <h1 class="h3 mb-1"><?= e($titulo) ?></h1>
    <p class="text-secondary mb-0">Preencha os dados abaixo.</p>
</div>

<form class="card border-0 shadow-sm p-4" method="post" action="<?= url('veterinarios/' . ($registro ? 'atualizar/' . $registro['id'] : 'salvar')) ?>">
    <?= campo_csrf() ?>
    <div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="nome">nome</label>
        <input class="form-control <?= tem_erro('nome') ? 'is-invalid' : '' ?>" id="nome" type="text" name="nome" value="<?= e(antigo('nome', $registro['nome'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('nome')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="crmv">crmv</label>
        <input class="form-control <?= tem_erro('crmv') ? 'is-invalid' : '' ?>" id="crmv" type="text" name="crmv" value="<?= e(antigo('crmv', $registro['crmv'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('crmv')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="especialidade">especialidade</label>
        <input class="form-control <?= tem_erro('especialidade') ? 'is-invalid' : '' ?>" id="especialidade" type="text" name="especialidade" value="<?= e(antigo('especialidade', $registro['especialidade'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('especialidade')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="telefone">telefone</label>
        <input class="form-control <?= tem_erro('telefone') ? 'is-invalid' : '' ?>" id="telefone" type="text" name="telefone" value="<?= e(antigo('telefone', $registro['telefone'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('telefone')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="ativo">ativo</label>
        <div class="form-check">
            <input type="hidden" name="ativo" value="0">
            <input class="form-check-input" id="ativo" type="checkbox" name="ativo" value="1" <?= antigo('ativo', $registro['ativo'] ?? '') ? 'checked' : '' ?>>
            <label class="form-check-label" for="ativo">Sim</label>
        </div>
        <?php if ($mensagem = erro_de('ativo')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">Salvar</button>
        <a class="btn btn-outline-secondary" href="<?= url('veterinarios') ?>">Cancelar</a>
    </div>
</form>
