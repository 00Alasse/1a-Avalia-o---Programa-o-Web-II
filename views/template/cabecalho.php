<?php
/**
 * Cabecalho com o menu de navegacao.
 * Incluido pelo template/layout.php.
 *
 * Os itens vem de configuracoes/menu.php, que o comando scaffold:crud
 * atualiza a cada CRUD gerado. As telas de login vem dos providers
 * instalados por auth:install.
 */

use Nucleo\Autenticacao;
use Nucleo\Config;

// Descobre a rota atual para destacar o item do menu.
$rotaAtual = trim((string) ($_GET['url'] ?? ''), '/');
$secao = explode('/', $rotaAtual)[0] ?: '';

$itens = array_values(array_filter(
    (array) Config::obter('menu', [['rota' => '', 'texto' => 'Início']]),
    function (array $item): bool {
        $regra = $item['auth'] ?? null;

        $podeVer = match ($regra) {
            'sim' => Autenticacao::conectados() !== [],
            'nao' => Autenticacao::conectados() === [],
            default => true,
        };

        // 'perfil' => 'admin' (ou uma lista) esconde o item de quem nao tem
        // esse perfil. E so o menu: quem protege a rota e o exigirPerfil()
        // no controller.
        if ($podeVer && isset($item['perfil'])) {
            $podeVer = tem_perfil($item['perfil'], $item['provider'] ?? null);
        }

        return $podeVer;
    }
));

$conectados = Autenticacao::conectados();
$providers = Autenticacao::providers();
?>

<?php
$iconesMenu = [
    '' => 'bi-house',
    'especies' => 'bi-grid-3x3-gap',
    'tutores' => 'bi-person',
    'veterinarios' => 'bi-heart-pulse',
    'procedimentos' => 'bi-clipboard2-pulse',
    'animais' => 'bi-heart',
    'atendimentos' => 'bi-clipboard2',
    'vacinas' => 'bi-eyedropper',
];
?>

<header class="cabecalho">

    <aside class="sidebar" id="menuPrincipal">

        <a class="sidebar__marca" href="<?= url() ?>">
            <span class="sidebar__logo">𓃠</span>

            <span class="sidebar__texto-marca">
                <?= e($nomeDoSite ?? 'Pata Amiga') ?>
            </span>
        </a>

        <div class="sidebar__rotulo">
            Navegação
        </div>

        <nav class="sidebar__menu">

            <?php foreach ($itens as $item): ?>
                <?php $rota = trim((string) ($item['rota'] ?? ''), '/'); ?>

                <a class="sidebar__item <?= $secao === $rota ? 'sidebar__item--ativo' : '' ?>" href="<?= url($rota) ?>"
                    title="<?= e((string) ($item['texto'] ?? $rota)) ?>">
                    <span class="sidebar__icone">
                        <i class="bi <?= e($iconesMenu[$rota] ?? 'bi-circle') ?>"></i>
                    </span>

                    <span class="sidebar__texto">
                        <?= e((string) ($item['texto'] ?? $rota)) ?>
                    </span>
                </a>
            <?php endforeach ?>

            <?php if ($providers !== []): ?>
                <div class="sidebar__rotulo sidebar__rotulo-conta">
                    Conta
                </div>
            <?php endif ?>

            <?php foreach ($conectados as $provider): ?>
                <a class="sidebar__item" href="<?= url(Autenticacao::rotaSair($provider)) ?>" title="Sair">
                    <span class="sidebar__icone">&#8594;</span>

                    <span class="sidebar__texto">
                        Sair<?= $provider === '' ? '' : ' (' . e($provider) . ')' ?>
                    </span>
                </a>
            <?php endforeach ?>

            <?php foreach ($providers as $provider): ?>
                <?php if (in_array($provider, $conectados, true)) {
                    continue;
                } ?>

                <a class="sidebar__item" href="<?= url(Autenticacao::rotaLogin($provider)) ?>" title="Entrar">
                    <span class="sidebar__icone">&#8594;</span>

                    <span class="sidebar__texto">
                        Entrar<?= $provider === '' ? '' : ' (' . e($provider) . ')' ?>
                    </span>
                </a>
            <?php endforeach ?>

            <div class="sidebar__rotulo sidebar__rotulo-conta">
                Sistema
            </div>

            <a class="sidebar__item <?= $secao === 'home' ? 'sidebar__item--ativo' : '' ?>"
                href="<?= url('home/sobre') ?>" title="Sobre o sistema">
                <span class="sidebar__icone">
                    <i class="bi bi-info-circle"></i>
                </span>

                <span class="sidebar__texto">
                    Sobre o sistema
                </span>
            </a>

        </nav>
    </aside>

    <div class="topbar">

        <!-- BOTÃO NOVO: recolher/abrir menu -->
        <button class="btn btn-outline-primary botao-menu" type="button" id="botaoMenu"
            aria-label="Recolher ou abrir menu" title="Recolher ou abrir menu">
            ☰
        </button>

        <div class="topbar__titulo">
            <?= e($titulo ?? 'Painel') ?>
        </div>

        <div class="topbar__status">
            <span class="status-ponto"></span>
            Sistema online
        </div>

    </div>

</header>