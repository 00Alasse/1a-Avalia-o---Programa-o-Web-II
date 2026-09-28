<div class="mb-4">
    <h1 class="h3 mb-1">Relatório de Agenda</h1>
    <p class="text-secondary mb-0">
        Selecione um período e, opcionalmente, um veterinário. Os campos podem ser deixados em branco para consultar
        toda a agenda.
    </p>
</div>

<form class="card border-0 shadow-sm p-4" method="get" action="<?= url('atendimentos/relatorio-agenda') ?>">

    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label" for="data_inicial">
                Data inicial
            </label>
            <input class="form-control" id="data_inicial" type="date" name="data_inicial"
                max="<?= e($dataFinal ?? '') ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="data_final">
                Data final
            </label>
            <input class="form-control" id="data_final" type="date" name="data_final"
                min="<?= e($dataInicial ?? '') ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="veterinario_id">
                Veterinário
            </label>

            <select class="form-select" id="veterinario_id" name="veterinario_id">
                <option value="">Todos</option>

                <?php foreach (($todosVeterinarios ?? []) as $veterinario): ?>
                    <option value="<?= e($veterinario['id']) ?>">
                        <?= e($veterinario['nome']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

    </div>

    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">
            Gerar relatório PDF
        </button>

        <a class="btn btn-outline-secondary" href="<?= url('atendimentos') ?>">
            Cancelar
        </a>
    </div>
</form>

<script>
    const dataInicial = document.getElementById('data_inicial');
    const dataFinal = document.getElementById('data_final');

    dataInicial.addEventListener('change', function () {
        dataFinal.min = this.value;
    });

    dataFinal.addEventListener('change', function () {
        dataInicial.max = this.value;
    });
</script>