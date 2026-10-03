<div class="mb-4">
    <h1 class="h3 mb-1"><?= e($titulo) ?></h1>
    <p class="text-secondary mb-0">Preencha os dados abaixo.</p>
</div>

<form class="card border-0 shadow-sm p-4" method="post" enctype="multipart/form-data"
    action="<?= url('animais/' . ($registro ? 'atualizar/' . $registro['id'] : 'salvar')) ?>">
    <?= campo_csrf() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="nome">Nome</label>
            <input class="form-control <?= tem_erro('nome') ? 'is-invalid' : '' ?>" id="nome" type="text" name="nome"
                value="<?= e(antigo('nome', $registro['nome'] ?? '')) ?>">
            <?php if ($mensagem = erro_de('nome')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="raca">Raça</label>
            <input class="form-control <?= tem_erro('raca') ? 'is-invalid' : '' ?>" id="raca" type="text" name="raca"
                value="<?= e(antigo('raca', $registro['raca'] ?? '')) ?>">
            <?php if ($mensagem = erro_de('raca')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="data_nascimento">Data de nascimento</label>
            <input class="form-control <?= tem_erro('data_nascimento') ? 'is-invalid' : '' ?>" id="data_nascimento"
                type="date" name="data_nascimento"
                value="<?= e(antigo('data_nascimento', $registro['data_nascimento'] ?? '')) ?>">
            <?php if ($mensagem = erro_de('data_nascimento')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="sexo">Sexo</label>
            <input class="form-control <?= tem_erro('sexo') ? 'is-invalid' : '' ?>" id="sexo" type="text" name="sexo"
                value="<?= e(antigo('sexo', $registro['sexo'] ?? '')) ?>">
            <?php if ($mensagem = erro_de('sexo')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="peso">Peso</label>
            <input class="form-control <?= tem_erro('peso') ? 'is-invalid' : '' ?>" id="peso" type="number" step="0.01"
                name="peso" value="<?= e(antigo('peso', $registro['peso'] ?? '')) ?>">
            <?php if ($mensagem = erro_de('peso')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="castrado">Castrado</label>
            <div class="form-check">
                <input type="hidden" name="castrado" value="0">
                <input class="form-check-input" id="castrado" type="checkbox" name="castrado" value="1"
                    <?= antigo('castrado', $registro['castrado'] ?? '') ? 'checked' : '' ?>>
                <label class="form-check-label" for="castrado">Sim</label>
            </div>
            <?php if ($mensagem = erro_de('castrado')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>
        <div class="col-12">
            <label class="form-label" for="observacoes">Observações</label>
            <textarea class="form-control <?= tem_erro('observacoes') ? 'is-invalid' : '' ?>" id="observacoes"
                name="observacoes" rows="4"><?= e(antigo('observacoes', $registro['observacoes'] ?? '')) ?></textarea>
            <?php if ($mensagem = erro_de('observacoes')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">Foto do animal</label>
            <input type="file" class="form-control" id="foto" name="foto" accept=".jpg,.jpeg">
            <div class="form-text">
                Formatos aceitos: JPG e JPEG. Tamanho máximo: 4 MB.
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="tutor_id">Tutor</label>
            <?php $selecionado = antigo('tutor_id', $registro['tutor_id'] ?? ''); ?>
            <select class="form-select <?= tem_erro('tutor_id') ? 'is-invalid' : '' ?>" id="tutor_id" name="tutor_id">
                <option value="">Selecione...</option>
                <?php foreach (($tutores ?? []) as $opcao): ?>
                    <option value="<?= e($opcao['id']) ?>" <?= (string) $selecionado === (string) $opcao['id'] ? 'selected' : '' ?>><?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
                <?php endforeach ?>
            </select>
            <?php if ($mensagem = erro_de('tutor_id')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="especie_id">Espécie</label>
            <?php $selecionado = antigo('especie_id', $registro['especie_id'] ?? ''); ?>
            <select class="form-select <?= tem_erro('especie_id') ? 'is-invalid' : '' ?>" id="especie_id"
                name="especie_id">
                <option value="">Selecione...</option>
                <?php foreach (($especies ?? []) as $opcao): ?>
                    <option value="<?= e($opcao['id']) ?>" <?= (string) $selecionado === (string) $opcao['id'] ? 'selected' : '' ?>><?= e($opcao['nome'] ?? $opcao['descricao'] ?? ('#' . $opcao['id'])) ?></option>
                <?php endforeach ?>
            </select>
            <?php if ($mensagem = erro_de('especie_id')): ?>
                <div class="invalid-feedback d-block"><?= e($mensagem) ?></div><?php endif ?>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">Salvar</button>
        <a class="btn btn-outline-secondary" href="<?= url('animais') ?>">Cancelar</a>
    </div>
</form>