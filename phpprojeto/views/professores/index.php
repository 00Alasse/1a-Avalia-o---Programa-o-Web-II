<h1>Professores</h1>
<a class="botao" href="<?= url('professores/criar') ?>">Novo professor</a>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Disciplina</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($professores as $p): ?>
        <tr>
            <td><?= e($p['nome']) ?></td>
            <td><?= e($p['disciplina']) ?></td>
            <td>
                <a href="<?= url('professores/excluir/' . $p['id']) ?>">Excluir</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>