<div class="mb-4">
    <h1 class="h3 mb-1"><?= e($titulo) ?></h1>
    <p class="text-secondary mb-0">Preencha os dados abaixo.</p>
</div>

<form class="card border-0 shadow-sm p-4" method="post" action="<?= url('tutores/' . ($registro ? 'atualizar/' . $registro['id'] : 'salvar')) ?>">
    <?= campo_csrf() ?>
    <div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="nome">nome</label>
        <input class="form-control <?= tem_erro('nome') ? 'is-invalid' : '' ?>" id="nome" type="text" name="nome" value="<?= e(antigo('nome', $registro['nome'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('nome')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="cpf">cpf</label>
        <input class="form-control <?= tem_erro('cpf') ? 'is-invalid' : '' ?>" id="cpf" type="text" name="cpf" value="<?= e(antigo('cpf', $registro['cpf'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('cpf')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="telefone">telefone</label>
        <input class="form-control <?= tem_erro('telefone') ? 'is-invalid' : '' ?>" id="telefone" type="text" name="telefone" value="<?= e(antigo('telefone', $registro['telefone'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('telefone')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="email">email</label>
        <input class="form-control <?= tem_erro('email') ? 'is-invalid' : '' ?>" id="email" type="text" name="email" value="<?= e(antigo('email', $registro['email'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('email')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="endereco">endereco</label>
        <input class="form-control <?= tem_erro('endereco') ? 'is-invalid' : '' ?>" id="endereco" type="text" name="endereco" value="<?= e(antigo('endereco', $registro['endereco'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('endereco')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="data_cliente">data_cliente</label>
        <input class="form-control <?= tem_erro('data_cliente') ? 'is-invalid' : '' ?>" id="data_cliente" type="date" name="data_cliente" value="<?= e(antigo('data_cliente', $registro['data_cliente'] ?? '')) ?>">
        <?php if ($mensagem = erro_de('data_cliente')): ?><div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
    </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">Salvar</button>
        <a class="btn btn-outline-secondary" href="<?= url('tutores') ?>">Cancelar</a>
    </div>
</form>
