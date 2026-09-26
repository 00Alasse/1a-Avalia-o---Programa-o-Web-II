<?php if (autenticado()): ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Bem-vindo à 𓃠 Pata Amiga</h1>
        <p class="text-secondary mb-0">Gerencie os dados da clínica de forma simples e organizada.</p>
    </div>

    <div class="d-flex gap-2">
        <a class="btn btn-primary" href="<?= url('atendimentos/criar') ?>">Novo Atendimento</a>
        <a class="btn btn-outline-primary" href="<?= url('vacinas/criar') ?>">Nova Vacina</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <p class="texto-apoio" style="text-align: justify;">
                    A Pata Amiga reúne as principais informações da clínica veterinária
                    em um só lugar, facilitando o cadastro e o gerenciamento de tutores,
                    animais, veterinários, procedimentos e espécies.
                </p>

                <h2 class="h5 mt-4">Acesso rápido</h2>

                <ol class="lista-passos">
                    <li>Acesse os cadastros pelo menu lateral.</li>
                    <li>Cadastre tutores e seus animais.</li>
                    <li>Consulte veterinários, espécies e procedimentos.</li>
                    <li>Utilize os relatórios para consultar os registros cadastrados.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="text-primary fs-2 mb-3">𓃠</div>
                <h2 class="h5">Gestão da clínica</h2>
                <p class="text-secondary mb-0">
                    Tenha acesso rápido aos principais cadastros e informações da clínica.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- RF15: Alertas de Vacinas -->
<div class="row g-4 mb-4">

    <!-- Vacinas vencidas -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Retornos de Vacina Vencidos</span>
                <span class="badge bg-light text-danger fs-6"><?= count($vacinasVencidas) ?></span>
            </div>

            <div class="card-body p-0">
                <?php if ($vacinasVencidas === []): ?>
                    <p class="text-muted p-3 mb-0">
                        Nenhuma vacina com retorno vencido no momento.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Animal</th>
                                    <th>Vacina</th>
                                    <th>Venceu em</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($vacinasVencidas as $v): ?>
                                <tr>
                                    <td>
                                        <a class="fw-bold text-decoration-none"
                                           href="<?= url('animais/ver/' . $v['animal_id']) ?>">
                                            <?= e($v['animal_nome'] ?? ('Animal #' . $v['animal_id'])) ?>
                                        </a>
                                    </td>
                                    <td><?= e($v['nome_vacina']) ?></td>
                                    <td class="text-danger fw-bold">
                                        <?= e(data_br($v['data_retorno'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Vacinas próximas -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <span class="fw-bold">Retornos nos Próximos 30 Dias</span>
                <span class="badge bg-dark text-white fs-6"><?= count($vacinasProximas) ?></span>
            </div>

            <div class="card-body p-0">
                <?php if ($vacinasProximas === []): ?>
                    <p class="text-muted p-3 mb-0">
                        Nenhum retorno previsto para os próximos 30 dias.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Animal</th>
                                    <th>Vacina</th>
                                    <th>Data de Retorno</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($vacinasProximas as $v): ?>
                                <tr>
                                    <td>
                                        <a class="fw-bold text-decoration-none"
                                           href="<?= url('animais/ver/' . $v['animal_id']) ?>">
                                            <?= e($v['animal_nome'] ?? ('Animal #' . $v['animal_id'])) ?>
                                        </a>
                                    </td>
                                    <td><?= e($v['nome_vacina']) ?></td>
                                    <td class="text-dark fw-bold">
                                        <?= e(data_br($v['data_retorno'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

</div>

<?php else: ?>

<!-- RF25: Página inicial pública -->
<div class="p-5 mb-4 bg-light rounded-3 text-center shadow-sm">
    <h1 class="display-5 fw-bold text-primary">
        Clínica Veterinária Pata Amiga
    </h1>

    <p class="col-md-8 mx-auto fs-5 text-secondary">
        Cuidando com amor, dedicação e excelência da saúde do seu melhor amigo.
        Atendimento clínico, vacinas e cuidados especiais para cães e gatos.
    </p>

    <div class="mt-4">
        <a class="btn btn-primary btn-lg px-4" href="<?= url('auth/login') ?>">
            Área da Equipe (Entrar)
        </a>
    </div>
</div>

<?php endif ?>