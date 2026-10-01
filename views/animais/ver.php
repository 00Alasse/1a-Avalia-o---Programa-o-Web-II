<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-0"><?= e($registro['nome']) ?></h1>
        <p class="text-secondary mb-0">Prontuário completo do paciente veterinário.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('animais') ?>">Voltar</a>
        <a class="btn btn-primary" href="<?= url('animais/editar/' . $registro['id']) ?>">Editar</a>

        <a class="btn btn-outline-info" href="<?= url('animais/carteira-vacinacao/' . $registro['id']) ?>">
            Carteira de vacinação
        </a>

        <form method="post" action="<?= url('animais/excluir/' . $registro['id']) ?>"
            onsubmit="return confirm('Excluir este animal?')">
            <?= campo_csrf() ?>
            <button class="btn btn-outline-danger" type="submit">Excluir</button>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Dados do Animal -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Informações do Animal</h5>
            </div>
            <dl class="row g-0 mb-0 p-3">
                <dt class="col-sm-4 text-secondary">Identificador</dt>
                <dd class="col-sm-8">#<?= e($registro['id']) ?></dd>

                <dt class="col-sm-4 text-secondary">Nome</dt>
                <dd class="col-sm-8 fw-bold"><?= e($registro['nome']) ?></dd>

                <dt class="col-sm-4 text-secondary">Espécie</dt>
                <dd class="col-sm-8"><?= e($especie['nome'] ?? 'Não informada') ?></dd>

                <dt class="col-sm-4 text-secondary">Raça</dt>
                <dd class="col-sm-8"><?= e($registro['raca'] ?? 'Não informada') ?></dd>

                <dt class="col-sm-4 text-secondary">Data de Nascimento</dt>
                <dd class="col-sm-8">
                    <?= !empty($registro['data_nascimento']) ? e(data_br($registro['data_nascimento'])) : '—' ?>
                </dd>

                <!-- RF07: Idade calculada a partir de data_nascimento -->
                <dt class="col-sm-4 text-primary">Idade Calculada</dt>
                <dd class="col-sm-8 fw-bold text-primary"><?= e($idadeTexto) ?></dd>

                <dt class="col-sm-4 text-secondary">Sexo</dt>
                <dd class="col-sm-8"><?= e($registro['sexo'] ?? '') ?></dd>

                <dt class="col-sm-4 text-secondary">Peso</dt>
                <dd class="col-sm-8"><?= e(moeda_br($registro['peso'])) ?> kg</dd>

                <dt class="col-sm-4 text-secondary">Castrado</dt>
                <dd class="col-sm-8"><?= e(sim_nao($registro['castrado'])) ?></dd>

                <dt class="col-sm-4 text-secondary">Observações</dt>
                <dd class="col-sm-8"><?= nl2br(e($registro['observacoes'] ?? 'Nenhuma observação.')) ?></dd>
            </dl>
        </div>
    </div>

    <!-- RF07: Dados do Tutor -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Dados do Tutor (Responsável)</h5>
            </div>
            <div class="card-body p-3">
                <?php if ($tutor): ?>
                    <dl class="row g-0 mb-0">
                        <dt class="col-sm-4 text-secondary">Nome</dt>
                        <dd class="col-sm-8 fw-bold"><?= e($tutor['nome']) ?></dd>

                        <dt class="col-sm-4 text-secondary">CPF</dt>
                        <dd class="col-sm-8"><?= e($tutor['cpf'] ?? 'Não informado') ?></dd>

                        <dt class="col-sm-4 text-secondary">Telefone</dt>
                        <dd class="col-sm-8"><?= e($tutor['telefone'] ?? 'Não informado') ?></dd>

                        <dt class="col-sm-4 text-secondary">E-mail</dt>
                        <dd class="col-sm-8"><?= e($tutor['email'] ?? 'Não informado') ?></dd>

                        <dt class="col-sm-4 text-secondary">Endereço</dt>
                        <dd class="col-sm-8"><?= e($tutor['endereco'] ?? 'Não informado') ?></dd>
                    </dl>
                <?php else: ?>
                    <p class="text-muted mb-0">Nenhum tutor vinculado.</p>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!-- RF07: Histórico de Atendimentos -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Histórico de Atendimentos</h5>
        <span class="badge bg-primary"><?= count($atendimentos) ?> registro(s)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Data e Hora</th>
                    <th>Procedimento</th>
                    <th>Veterinário</th>
                    <th>Valor</th>
                    <th>Situação</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($atendimentos as $at): ?>
                    <tr>
                        <td><?= e(data_br($at['data_hora'], true)) ?></td>
                        <td><?= e($at['procedimento_nome'] ?? ('#' . $at['procedimento_id'])) ?></td>
                        <td><?= e($at['veterinario_nome'] ?? ('#' . $at['veterinario_id'])) ?></td>
                        <td>R$ <?= e(moeda_br($at['valor_cobrado'])) ?></td>
                        <td><span class="badge bg-secondary"><?= e($at['situacao']) ?></span></td>
                        <td class="small text-secondary"><?= e($at['observacoes_clinicas']) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if ($atendimentos === []): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-3">Nenhum atendimento realizado para este
                            animal.</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<!-- RF07: Histórico de Vacinas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Histórico de Vacinas</h5>
        <span class="badge bg-info text-dark"><?= count($vacinas) ?> vacina(s)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Vacina</th>
                    <th>Lote</th>
                    <th>Data de Aplicação</th>
                    <th>Retorno Previsto</th>
                    <th>Veterinário</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vacinas as $vac): ?>
                    <tr>
                        <td class="fw-bold"><?= e($vac['nome_vacina']) ?></td>
                        <td><?= e($vac['lote']) ?></td>
                        <td><?= e(data_br($vac['data_aplicacao'])) ?></td>
                        <td>
                            <?php if (!empty($vac['data_retorno'])): ?>
                                <span class="badge bg-warning text-dark"><?= e(data_br($vac['data_retorno'])) ?></span>
                            <?php else: ?>
                                <span class="text-secondary">-</span>
                            <?php endif ?>
                        </td>
                        <td><?= e($vac['veterinario_nome'] ?? ('#' . $vac['veterinario_id'])) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if ($vacinas === []): ?>
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-3">Nenhuma vacina registrada para este animal.
                        </td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>