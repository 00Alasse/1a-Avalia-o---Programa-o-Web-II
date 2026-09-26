<div class="mb-4">
    <h1 class="h3 mb-1">Como funciona a 𓃠 Pata Amiga</h1>
    <p class="text-secondary mb-0">
        Conheça a estrutura e o funcionamento do sistema.
    </p>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5">Sobre o sistema</h2>

        <p style="text-align: justify;">
            A Pata Amiga é um sistema desenvolvido para auxiliar no
            gerenciamento de uma clínica veterinária, reunindo em um só lugar
            informações sobre tutores, animais, veterinários, espécies e
            procedimentos.
        </p>

        <p style="text-align: justify;" class="mb-0">
            O sistema utiliza uma estrutura baseada no padrão MVC
            (Model-View-Controller), organizando as responsabilidades da
            aplicação e facilitando sua manutenção e evolução.
        </p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5">Como funciona o sistema</h2>

        <pre class="codigo">Navegador
   |
   v
index.php -> bootstrap.php -> Aplicação
                              |
                              v
                    Controller -> Model -> Banco de dados
                              |
                              v
                           View -> HTML</pre>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h5">Controller</h2>
                <p class="text-secondary mb-0" style="text-align: justify;">
                    Recebe as requisições do usuário e coordena as ações
                    necessárias no sistema.
                </p>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h5">Model</h2>
                <p class="text-secondary mb-0" style="text-align: justify;">
                    Responsável pelo acesso e gerenciamento dos dados
                    utilizados pela aplicação.
                </p>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h5">View</h2>
                <p class="text-secondary mb-0" style="text-align: justify;">
                    Apresenta as informações e as funcionalidades do sistema
                    para o usuário por meio das páginas HTML.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body p-4">
        <h2 class="h5">Principais cadastros</h2>

        <p class="text-secondary mb-3">
            A Pata Amiga permite o gerenciamento dos principais registros
            utilizados pela clínica:
        </p>

        <ul class="mb-0">
            <li>Tutores e seus dados de cadastro;</li>
            <li>Animais e suas informações;</li>
            <li>Espécies cadastradas;</li>
            <li>Veterinários e suas especialidades;</li>
            <li>Procedimentos e seus valores e durações.</li>
        </ul>
    </div>
</div>

<p class="mt-4">
    <a class="botao botao--secundario" href="<?= url() ?>">Voltar ao início</a>
</p>