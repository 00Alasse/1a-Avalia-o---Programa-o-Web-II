<?php
/**
 * Tela inicial (dashboard).
 *
 * A tela inicial nao depende de nenhuma tabela da aplicacao.
 */
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Bem-vindo à 𓃠 Pata Amiga</h1>
        <p class="text-secondary mb-0">Gerencie os dados da clínica de forma simples e organizada.</p>
    </div>
    <a class="btn btn-primary" href="<?= url('home/sobre') ?>">Como funciona</a>
</div>

<div class="row g-4">
<div class="col-12 col-lg-8"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><p class="texto-apoio" style="text-align: justify;">
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

</div></div></div>
<div class="col-12 col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><div class="text-primary fs-2 mb-3">&lt;/&gt;</div><h2 class="h5">Gestão da clínica</h2><p class="text-secondary mb-0">Tenha acesso rápido aos principais cadastros e informações da clínica.</p></div></div></div>
</div>
