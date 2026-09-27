<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Vacinas Aplicadas</h1>
        <p class="text-secondary mb-0">Gerencie o histórico de vacinas e retornos dos animais.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('vacinas/relatorio') ?>">Relatório PDF</a>
        <a class="btn btn-primary" href="<?= url('vacinas/criar') ?>">Nova Vacina</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Animal (ID)</th>
                    <th>Veterinário (ID)</th>
                    <th>Nome da Vacina</th>
                    <th>Lote</th>
                    <th>Data da Aplicação</th>
                    <th>Previsão de Retorno</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><a href="<?= url('vacinas/ver/' . $registro['id']) ?>">#<?= e($registro['id']) ?></a></td>
                        <td><?= e($registro['animal_id'] ?? '') ?></td>
                        <td><?= e($registro['veterinario_id'] ?? '') ?></td>
                        <td><strong><?= e($registro['nome_vacina'] ?? '') ?></strong></td>
                        <td><?= e($registro['lote'] ?? '') ?></td>
                        <td><?= e(data_br($registro['data_aplicacao'] ?? '')) ?></td>
                        <td>
                            <?php
                            $dataRetorno = $registro['data_retorno'] ?? null;

                            $dataRetornoValida =
                                !empty($dataRetorno) &&
                                $dataRetorno !== '0000-00-00' &&
                                $dataRetorno !== '0000-00-00 00:00:00';
                            ?>

                            <?php if ($dataRetornoValida): ?>
                                <span class="badge bg-info text-dark"><?= e(data_br($dataRetorno)) ?></span>
                            <?php else: ?>
                                <span class="text-secondary">-</span>
                            <?php endif ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-secondary"
                                href="<?= url('vacinas/editar/' . $registro['id']) ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= url('vacinas/excluir/' . $registro['id']) ?>"
                                onsubmit="return confirm('Excluir esta vacina?')">
                                <?= campo_csrf() ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if ($registros === []): ?>
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">Nenhuma vacina registrada.</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>