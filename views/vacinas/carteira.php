<div class="mb-4">
    <h1 class="h3 mb-1">Carteira de vacinação</h1>
    <p class="text-secondary mb-0">
        Selecione o animal para gerar a carteira de vacinação.
    </p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="get">
            <div class="mb-3">
                <label class="form-label" for="animal_id">
                    Animal
                </label>

                <select
                    class="form-select"
                    id="animal_id"
                    name="animal_id"
                    required
                >
                    <option value="">Selecione um animal</option>

                    <?php foreach ($animais as $animal): ?>
                        <option value="<?= e($animal['id']) ?>">
                            <?= e($animal['nome']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <button class="btn btn-primary" type="submit">
                Gerar carteira
            </button>
        </form>
    </div>
</div>