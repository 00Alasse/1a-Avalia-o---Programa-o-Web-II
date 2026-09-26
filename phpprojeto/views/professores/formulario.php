<h1>Novo professor</h1>

<form method="POST" action="<?= e($acao) ?>" class="formulario">
    <div class="campo <?= tem_erro('nome') ? 'campo--erro' : '' ?>">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" value="<?= e(antigo('nome')) ?>" required>
        <?php if ($msg = erro_de('nome')): ?>
            <span class="campo__erro"><?= e($msg) ?></span>
        <?php endif ?>
    </div>

    <div class="campo <?= tem_erro('disciplina') ? 'campo--erro' : '' ?>">
        <label for="disciplina">Disciplina</label>
        <input type="text" id="disciplina" name="disciplina" value="<?= e(antigo('disciplina')) ?>" required>
        <?php if ($msg = erro_de('disciplina')): ?>
            <span class="campo__erro"><?= e($msg) ?></span>
        <?php endif ?>
    </div>

    <button type="submit" class="botao">Cadastrar</button>
</form>