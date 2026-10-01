<div class="mb-4">
    <h1 class="h3 mb-1">Como funciona a 𓃠 Pata Amiga</h1>
    <p class="text-secondary mb-0">
        Conheça a estrutura, os recursos e o funcionamento do sistema.
    </p>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5">Sobre o sistema</h2>

    <p style="text-align: justify;">
        A Pata Amiga é um sistema desenvolvido para auxiliar no
        gerenciamento de uma clínica veterinária, reunindo em um só lugar
        informações sobre tutores, animais, veterinários, espécies,
        procedimentos, atendimentos e vacinas.
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

    <p class="text-secondary mb-0" style="text-align: justify;">
        O navegador envia uma requisição para o sistema. A aplicação
        identifica a rota e encaminha a requisição para o controller
        correspondente. O controller pode utilizar um model para consultar
        ou alterar dados no banco de dados e, depois, envia as informações
        necessárias para a view. A view apresenta o resultado ao usuário
        em HTML.
    </p>
</div>

</div>

<div class="row g-4 mb-4">

<div class="col-12 col-md-4">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-4">
            <h2 class="h5">Controller</h2>

            <p class="text-secondary mb-0" style="text-align: justify;">
                Recebe as requisições do usuário e coordena as ações
                necessárias no sistema, podendo consultar modelos,
                validar dados, redirecionar o usuário e carregar views.
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
                utilizados pela aplicação, realizando operações como
                consulta, criação, atualização e exclusão de registros.
            </p>
        </div>
    </div>
</div>

<div class="col-12 col-md-4">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-4">
            <h2 class="h5">View</h2>

            <p class="text-secondary mb-0" style="text-align: justify;">
                Apresenta as informações e funcionalidades do sistema
                para o usuário por meio das páginas HTML e dos componentes
                visuais da aplicação.
            </p>
        </div>
    </div>
</div>

</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5">Principais funcionalidades</h2>

    <p class="text-secondary mb-3">
        A Pata Amiga permite o gerenciamento dos principais registros
        utilizados pela clínica veterinária:
    </p>

    <ul class="mb-0">
        <li>Cadastro e consulta de espécies;</li>
        <li>Cadastro e gerenciamento de tutores;</li>
        <li>Cadastro e gerenciamento de veterinários;</li>
        <li>Cadastro de procedimentos;</li>
        <li>Cadastro e consulta de animais;</li>
        <li>Registro e consulta de atendimentos;</li>
        <li>Cadastro e acompanhamento de vacinas;</li>
        <li>Controle de retornos de vacinação;</li>
        <li>Visualização de vacinas com retorno vencido;</li>
        <li>Visualização de vacinas com retorno previsto para os próximos 30 dias.</li>
    </ul>
</div>

</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5">Alertas de vacinação</h2>

    <p style="text-align: justify;">
        Na página inicial, o sistema apresenta informações relacionadas
        aos retornos de vacinação. Os registros com retorno vencido são
        apresentados separadamente dos retornos previstos para os próximos
        30 dias, permitindo uma visualização rápida das situações que
        precisam de atenção.
    </p>

    <p class="mb-0" style="text-align: justify;">
        A partir dos alertas, também é possível acessar diretamente o
        cadastro do animal relacionado ao registro.
    </p>
</div>

</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5">Autenticação e acesso</h2>

    <p style="text-align: justify;">
        O sistema possui autenticação de usuários e permite controlar a
        exibição de determinados itens do menu de acordo com o estado de
        autenticação e, quando configurado, com o perfil de acesso.
    </p>

    <p class="mb-0" style="text-align: justify;">
        A ocultação de um item no menu serve para facilitar a navegação,
        enquanto a proteção efetiva das rotas é realizada pelo próprio
        sistema de autenticação e pelas regras definidas nos controllers.
    </p>
</div>

</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5">Geração de recursos pelo framework</h2>

    <p style="text-align: justify;">
        O framework fornece comandos pelo terminal para acelerar a criação
        de recursos da aplicação. Por exemplo, o comando
        <code>scaffold:crud</code> pode gerar a estrutura necessária para
        um novo recurso, incluindo suas partes relacionadas ao CRUD.
    </p>

    <pre class="codigo">php console.php scaffold:crud produtos nome:string preco:decimal</pre>

    <p style="text-align: justify;">
        O projeto também possui comandos relacionados à instalação de
        autenticação e à execução dos testes automatizados.
    </p>

    <pre class="codigo">php console.php auth:install Cliente

php testes/executar.php</pre> </div>

</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h2 class="h5">Estrutura da aplicação</h2>

    <p class="text-secondary mb-3">
        Os principais elementos da aplicação são organizados em diferentes
        partes, de acordo com sua responsabilidade:
    </p>

    <ul class="mb-0">
        <li><strong>Controllers:</strong> recebem e processam as requisições;</li>
        <li><strong>Modelos:</strong> trabalham com os dados da aplicação;</li>
        <li><strong>Views:</strong> apresentam as informações ao usuário;</li>
        <li><strong>Configurações:</strong> armazenam opções como o menu da aplicação;</li>
        <li><strong>Núcleo:</strong> concentra funcionalidades reutilizáveis do framework;</li>
        <li><strong>Testes:</strong> verificam o funcionamento de partes da aplicação.</li>
    </ul>
</div>

</div>

<p class="mt-4">
    <a class="botao botao--secundario" href="<?= url() ?>">Voltar ao início</a>
</p>