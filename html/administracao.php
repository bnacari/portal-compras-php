<?php
/**
 * Portal de Compras - Administração Unificada
 * Tela com abas: Tipos, Critérios, Formas, Estrutura de Menus, Usuários, Perfis
 * Refatorada seguindo padrão consultarLicitacao/calculoKPC
 * 
 * IMPORTANTE: A aba "Usuários" utiliza TIPO_LICITACAO como perfis de acesso
 *             A aba "Perfis" gerencia a tabela PERFIL (perfis de sistema)
 */

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

// Determina aba ativa (default: tipos)
$abaAtiva = $_GET['aba'] ?? 'tipos';

// ============================================
// Se for aba de estrutura, buscar dados hierárquicos de menus
// ============================================
if ($abaAtiva == 'estrutura') {
    // Query para buscar menus, submenus e itens em uma única consulta
    $queryMenus = $pdoCAT->query("
        SELECT 
            M.ID_MENU, M.NM_MENU, M.LINK_MENU, M.DT_EXC_MENU,
            SM.ID_SUBMENU, SM.NM_SUBMENU, SM.LINK_SUBMENU, SM.DT_EXC_SUBMENU,
            IM.ID_ITEMMENU, IM.NM_ITEMMENU, IM.LINK_ITEMMENU, IM.DT_EXC_ITEMMENU
        FROM [PortalCompras].[dbo].[MENU] M
        LEFT JOIN [PortalCompras].[dbo].[SUBMENU] SM ON SM.ID_MENU = M.ID_MENU
        LEFT JOIN [PortalCompras].[dbo].[ITEMMENU] IM ON IM.ID_SUBMENU = SM.ID_SUBMENU
        ORDER BY M.NM_MENU, SM.NM_SUBMENU, IM.NM_ITEMMENU
    ");

    // Monta estrutura hierárquica dos menus
    $menus = [];
    while ($row = $queryMenus->fetch(PDO::FETCH_ASSOC)) {
        $menuId = $row['ID_MENU'];
        $subMenuId = $row['ID_SUBMENU'];
        
        // Adiciona menu se ainda não existe
        if (!isset($menus[$menuId])) {
            $menus[$menuId] = [
                'id' => $menuId,
                'nome' => $row['NM_MENU'],
                'link' => $row['LINK_MENU'],
                'inativo' => !empty($row['DT_EXC_MENU']),
                'submenus' => []
            ];
        }
        
        // Adiciona submenu se existe e ainda não foi adicionado
        if ($subMenuId && !isset($menus[$menuId]['submenus'][$subMenuId])) {
            $menus[$menuId]['submenus'][$subMenuId] = [
                'id' => $subMenuId,
                'nome' => $row['NM_SUBMENU'],
                'link' => trim($row['LINK_SUBMENU'] ?? ''),
                'inativo' => !empty($row['DT_EXC_SUBMENU']),
                'itens' => []
            ];
        }
        
        // Adiciona item de menu se existe
        if ($row['ID_ITEMMENU']) {
            $menus[$menuId]['submenus'][$subMenuId]['itens'][] = [
                'id' => $row['ID_ITEMMENU'],
                'nome' => $row['NM_ITEMMENU'],
                'link' => trim($row['LINK_ITEMMENU'] ?? ''),
                'inativo' => !empty($row['DT_EXC_ITEMMENU'])
            ];
        }
    }
    
    // Função de ordenação: ativos primeiro, depois alfabético
    $ordenar = function($a, $b) {
        if ($a['inativo'] !== $b['inativo']) {
            return $a['inativo'] ? 1 : -1;
        }
        return strcasecmp($a['nome'], $b['nome']);
    };
    
    // Ordenar menus
    usort($menus, $ordenar);
    
    // Ordenar submenus e itens dentro de cada menu
    foreach ($menus as &$menu) {
        $menu['submenus'] = array_values($menu['submenus']);
        usort($menu['submenus'], $ordenar);
        
        foreach ($menu['submenus'] as &$submenu) {
            usort($submenu['itens'], $ordenar);
        }
    }
    unset($menu, $submenu);
    
    // Contadores para estatísticas (apenas ativos)
    $totalMenus = 0;
    $totalSubMenus = 0;
    $totalItens = 0;
    foreach ($menus as $m) {
        if (!$m['inativo']) $totalMenus++;
        foreach ($m['submenus'] as $s) {
            if (!$s['inativo']) $totalSubMenus++;
            foreach ($s['itens'] as $i) {
                if (!$i['inativo']) $totalItens++;
            }
        }
    }
}
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- CSS da página -->
<link rel="stylesheet" href="style/css/administracao.css" />

<!-- CSS adicional para radio-group estilo tabs -->
<style>
    .radio-group {
        display: inline-flex;
        align-items: center;
        gap: 0;
        background: #f1f5f9;
        border-radius: 10px;
        padding: 4px;
        border: 1px solid #e2e8f0;
    }

    .radio-group input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .radio-group-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
        user-select: none;
        border: 1px solid transparent;
        margin: 0;
    }

    .radio-group-label:hover {
        color: #475569;
    }

    .radio-group input[type="radio"]:checked + .radio-group-label {
        background: #ffffff;
        color: #1e293b;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border-color: #e2e8f0;
    }

    .radio-group input[type="radio"]:focus + .radio-group-label {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
</style>

<div class="admin-container">
   <!-- ============================================
     NOVO HEADER - administracao.php
     ============================================
     Substituir o bloco antigo:
       <div class="page-header">...</div>
       <nav class="tabs-nav">...</nav>
     
     Por este bloco abaixo:
     ============================================ -->

    <!-- Header Profissional com Abas -->
    <div class="page-header-pro">
        <div class="header-decoration">
            <div class="decoration-circle-1"></div>
            <div class="decoration-circle-2"></div>
        </div>

        <div class="header-top-row">
            <div class="header-breadcrumb">
                <a href="index.php"><ion-icon name="home-outline"></ion-icon> Início</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <span>Administração</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <ion-icon name="settings-outline"></ion-icon>
                    <div class="icon-box-pulse"></div>
                </div>
                <div class="header-title-group">
                    <h1>Administração do Sistema</h1>
                    <p class="header-subtitle">
                        <ion-icon name="business-outline"></ion-icon>
                        Gerencie cadastros, menus, usuários e configurações
                    </p>
                </div>
            </div>
        </div>

        <!-- Abas integradas -->
        <nav class="header-tabs-bar">
            <a href="?aba=tipos" class="header-tab-link <?= $abaAtiva == 'tipos' ? 'active' : '' ?>">
                <ion-icon name="pricetag-outline"></ion-icon>
                <span>Tipo Contratação</span>
            </a>
            <a href="?aba=criterios" class="header-tab-link <?= $abaAtiva == 'criterios' ? 'active' : '' ?>">
                <ion-icon name="checkmark-circle-outline"></ion-icon>
                <span>Critérios</span>
            </a>
            <a href="?aba=formas" class="header-tab-link <?= $abaAtiva == 'formas' ? 'active' : '' ?>">
                <ion-icon name="git-branch-outline"></ion-icon>
                <span>Formas</span>
            </a>
            <a href="?aba=estrutura" class="header-tab-link <?= $abaAtiva == 'estrutura' ? 'active' : '' ?>">
                <ion-icon name="git-network-outline"></ion-icon>
                <span>Estrutura de Menus</span>
            </a>
            <a href="?aba=usuarios" class="header-tab-link <?= $abaAtiva == 'usuarios' ? 'active' : '' ?>">
                <ion-icon name="people-outline"></ion-icon>
                <span>Usuários</span>
            </a>
        </nav>
    </div>

    <!-- ============================================
         ABA: TIPOS DE LICITAÇÃO
         Tabela: TIPO_LICITACAO
         ============================================ -->
    <?php if ($abaAtiva == 'tipos') : ?>
    <div class="tab-pane active" id="tab-tipos">
        <!-- Cadastro -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <h3>Novo Tipo de Licitação</h3>
                </div>
            </div>
            <div class="section-card-body">
                <form action="bd/tipo/create.php" method="post" class="inline-form">
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label">
                            <ion-icon name="text-outline"></ion-icon>
                            Nome do Tipo <span class="required">*</span>
                        </label>
                        <input type="text" name="nmTipo" class="form-control" placeholder="Digite o nome do tipo" required autofocus>
                    </div>
                    <div class="form-group" style="flex: 0 0 150px;">
                        <label class="form-label">
                            <ion-icon name="code-outline"></ion-icon>
                            Sigla <span class="required">*</span>
                        </label>
                        <input type="text" name="sglTipo" class="form-control" placeholder="Ex: PE" maxlength="10" required style="text-transform: uppercase;">
                    </div>
                    <div class="form-group auto-width">
                        <button type="submit" class="btn btn-primary">
                            <ion-icon name="add-outline"></ion-icon>
                            Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Tipos Cadastrados</h3>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nome do Tipo</th>
                                <th style="width: 120px;">Sigla</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 140px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryTipos = $pdoCAT->query("
                                SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] 
                                ORDER BY CASE WHEN DT_EXC_TIPO IS NULL THEN 0 ELSE 1 END, NM_TIPO
                            ");
                            $totalTipos = 0;
                            while ($row = $queryTipos->fetch(PDO::FETCH_ASSOC)) :
                                $totalTipos++;
                                $inativo = !empty($row['DT_EXC_TIPO']);
                            ?>
                            <tr id="rowTipo<?= $row['ID_TIPO'] ?>" class="<?= $inativo ? 'inactive' : '' ?>">
                                <td><span class="pubnmTipo"><?= htmlspecialchars($row['NM_TIPO']) ?></span></td>
                                <td><span class="badge badge-sigla pubsglTipo"><?= htmlspecialchars($row['SGL_TIPO']) ?></span></td>
                                <td>
                                    <?php if ($inativo) : ?>
                                        <span class="badge badge-inactive">Inativo</span>
                                    <?php else : ?>
                                        <span class="badge badge-active">Ativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($inativo) : ?>
                                            <a href="bd/tipo/ativa.php?idTipo=<?= $row['ID_TIPO'] ?>" class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/tipo/desativa.php?idTipo=<?= $row['ID_TIPO'] ?>" class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit edit-btn-tipo" data-id="<?= $row['ID_TIPO'] ?>" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action save save-btn-tipo" data-id="<?= $row['ID_TIPO'] ?>" title="Salvar" style="display: none;">
                                            <ion-icon name="checkmark-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action cancel cancel-btn-tipo" data-id="<?= $row['ID_TIPO'] ?>" title="Cancelar" style="display: none;">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($totalTipos == 0) : ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <ion-icon name="pricetag-outline"></ion-icon>
                                        <h3>Nenhum tipo cadastrado</h3>
                                        <p>Adicione um novo tipo acima</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: CRITÉRIOS DE JULGAMENTO
         Tabela: CRITERIO_LICITACAO
         ============================================ -->
    <?php if ($abaAtiva == 'criterios') : ?>
    <div class="tab-pane active" id="tab-criterios">
        <!-- Cadastro -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <h3>Novo Critério de Julgamento</h3>
                </div>
            </div>
            <div class="section-card-body">
                <form action="bd/criterio/create.php" method="post" class="inline-form">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="checkmark-circle-outline"></ion-icon>
                            Nome do Critério <span class="required">*</span>
                        </label>
                        <input type="text" name="nmCriterio" class="form-control" placeholder="Digite o nome do critério" required autofocus>
                    </div>
                    <div class="form-group auto-width">
                        <button type="submit" class="btn btn-primary">
                            <ion-icon name="add-outline"></ion-icon>
                            Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Critérios Cadastrados</h3>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nome do Critério</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 140px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryCriterios = $pdoCAT->query("
                                SELECT * FROM [PortalCompras].[dbo].[CRITERIO_LICITACAO] 
                                ORDER BY CASE WHEN DT_EXC_CRITERIO IS NULL THEN 0 ELSE 1 END, NM_CRITERIO
                            ");
                            $totalCriterios = 0;
                            while ($row = $queryCriterios->fetch(PDO::FETCH_ASSOC)) :
                                $totalCriterios++;
                                $inativo = !empty($row['DT_EXC_CRITERIO']);
                            ?>
                            <tr id="rowCriterio<?= $row['ID_CRITERIO'] ?>" class="<?= $inativo ? 'inactive' : '' ?>">
                                <td><span class="pubnmCriterio"><?= htmlspecialchars($row['NM_CRITERIO']) ?></span></td>
                                <td>
                                    <?php if ($inativo) : ?>
                                        <span class="badge badge-inactive">Inativo</span>
                                    <?php else : ?>
                                        <span class="badge badge-active">Ativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($inativo) : ?>
                                            <a href="bd/criterio/ativa.php?idCriterio=<?= $row['ID_CRITERIO'] ?>" class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/criterio/desativa.php?idCriterio=<?= $row['ID_CRITERIO'] ?>" class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit edit-btn-criterio" data-id="<?= $row['ID_CRITERIO'] ?>" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action save save-btn-criterio" data-id="<?= $row['ID_CRITERIO'] ?>" title="Salvar" style="display: none;">
                                            <ion-icon name="checkmark-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action cancel cancel-btn-criterio" data-id="<?= $row['ID_CRITERIO'] ?>" title="Cancelar" style="display: none;">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($totalCriterios == 0) : ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                                        <h3>Nenhum critério cadastrado</h3>
                                        <p>Adicione um novo critério acima</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: FORMAS DE ADJUDICAÇÃO
         Tabela: FORMA_ADJUDICACAO
         ============================================ -->
    <?php if ($abaAtiva == 'formas') : ?>
    <div class="tab-pane active" id="tab-formas">
        <!-- Cadastro -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <h3>Nova Forma de Adjudicação</h3>
                </div>
            </div>
            <div class="section-card-body">
                <form action="bd/forma/create.php" method="post" class="inline-form">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="git-branch-outline"></ion-icon>
                            Nome da Forma <span class="required">*</span>
                        </label>
                        <input type="text" name="nmForma" class="form-control" placeholder="Digite o nome da forma" required autofocus>
                    </div>
                    <div class="form-group auto-width">
                        <button type="submit" class="btn btn-primary">
                            <ion-icon name="add-outline"></ion-icon>
                            Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Formas Cadastradas</h3>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Nome da Forma</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 140px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Tabela: FORMA
                            $queryFormas = $pdoCAT->query("
                                SELECT * FROM [PortalCompras].[dbo].[FORMA] 
                                ORDER BY CASE WHEN DT_EXC_FORMA IS NULL THEN 0 ELSE 1 END, NM_FORMA
                            ");
                            $totalFormas = 0;
                            while ($row = $queryFormas->fetch(PDO::FETCH_ASSOC)) :
                                $totalFormas++;
                                $inativo = !empty($row['DT_EXC_FORMA']);
                            ?>
                            <tr id="rowForma<?= $row['ID_FORMA'] ?>" class="<?= $inativo ? 'inactive' : '' ?>">
                                <td><span class="pubnmForma"><?= htmlspecialchars($row['NM_FORMA']) ?></span></td>
                                <td>
                                    <?php if ($inativo) : ?>
                                        <span class="badge badge-inactive">Inativo</span>
                                    <?php else : ?>
                                        <span class="badge badge-active">Ativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($inativo) : ?>
                                            <a href="bd/forma/ativa.php?idForma=<?= $row['ID_FORMA'] ?>" class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/forma/desativa.php?idForma=<?= $row['ID_FORMA'] ?>" class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit edit-btn-forma" data-id="<?= $row['ID_FORMA'] ?>" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action save save-btn-forma" data-id="<?= $row['ID_FORMA'] ?>" title="Salvar" style="display: none;">
                                            <ion-icon name="checkmark-outline"></ion-icon>
                                        </button>
                                        <button class="btn-action cancel cancel-btn-forma" data-id="<?= $row['ID_FORMA'] ?>" title="Cancelar" style="display: none;">
                                            <ion-icon name="close-outline"></ion-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($totalFormas == 0) : ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <ion-icon name="git-branch-outline"></ion-icon>
                                        <h3>Nenhuma forma cadastrada</h3>
                                        <p>Adicione uma nova forma acima</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: ESTRUTURA DE MENUS (Acordeão)
         ============================================ -->
    <?php if ($abaAtiva == 'estrutura') : ?>
    <div class="tab-pane active" id="tab-estrutura">
        
        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-icon menu">
                    <ion-icon name="folder-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h3><?= $totalMenus ?></h3>
                    <p>Menus Ativos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon submenu">
                    <ion-icon name="folder-open-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h3><?= $totalSubMenus ?></h3>
                    <p>SubMenus Ativos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon item">
                    <ion-icon name="document-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <h3><?= $totalItens ?></h3>
                    <p>Itens de Menu</p>
                </div>
            </div>
        </div>

        <!-- Botão Adicionar Menu -->
        <button class="add-menu-btn" onclick="abrirModalMenu()">
            <ion-icon name="add-circle-outline"></ion-icon>
            Adicionar Novo Menu
        </button>

        <!-- Cards de Menus -->
        <?php if (empty($menus)) : ?>
            <div class="empty-state">
                <ion-icon name="folder-open-outline"></ion-icon>
                <h3>Nenhum menu cadastrado</h3>
                <p>Clique no botão acima para adicionar o primeiro menu</p>
            </div>
        <?php else : ?>
            <?php foreach ($menus as $menu) : ?>
            <div class="menu-card <?= $menu['inativo'] ? 'inactive' : '' ?>" id="menu-<?= $menu['id'] ?>">
                <div class="menu-card-header" onclick="toggleMenu(<?= $menu['id'] ?>)">
                    <div class="menu-card-header-left">
                        <div class="menu-icon">
                            <ion-icon name="folder-outline"></ion-icon>
                        </div>
                        <div class="menu-info">
                            <h3>
                                <?= htmlspecialchars($menu['nome']) ?>
                                <?php if ($menu['inativo']) : ?>
                                    <span class="status-inactive-badge">Inativo</span>
                                <?php endif; ?>
                            </h3>
                            <span><?= $menu['link'] ? htmlspecialchars($menu['link']) : 'Sem link direto' ?></span>
                        </div>
                    </div>
                    <div class="menu-card-header-right">
                        <?php $submenusAtivos = count(array_filter($menu['submenus'], function($s) { return !$s['inativo']; })); ?>
                        <span class="menu-badge-count"><?= $submenusAtivos ?> submenus</span>
                        <div class="header-actions" onclick="event.stopPropagation();">
                            <?php if ($menu['inativo']) : ?>
                                <a href="bd/menus/ativa.php?idMenu=<?= $menu['id'] ?>" class="btn-action activate" title="Ativar">
                                    <ion-icon name="checkmark-outline"></ion-icon>
                                </a>
                            <?php else : ?>
                                <a href="bd/menus/desativa.php?idMenu=<?= $menu['id'] ?>" class="btn-action deactivate" title="Desativar">
                                    <ion-icon name="close-outline"></ion-icon>
                                </a>
                            <?php endif; ?>
                            <button class="btn-action edit" onclick="abrirModalMenu(<?= $menu['id'] ?>, <?= htmlspecialchars(json_encode($menu['nome']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($menu['link'] ?? ''), ENT_QUOTES) ?>)" title="Editar">
                                <ion-icon name="pencil-outline"></ion-icon>
                            </button>
                        </div>
                        <div class="expand-icon">
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                </div>

                <div class="menu-card-body">
                    <?php if (empty($menu['submenus'])) : ?>
                        <div class="empty-state" style="padding: 32px;">
                            <ion-icon name="folder-open-outline"></ion-icon>
                            <h3>Nenhum submenu</h3>
                            <p>Adicione submenus a este menu</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($menu['submenus'] as $submenu) : ?>
                        <div class="submenu-card <?= $submenu['inativo'] ? 'inactive' : '' ?>" id="submenu-<?= $submenu['id'] ?>">
                            <div class="submenu-card-header" onclick="toggleSubMenu(<?= $submenu['id'] ?>)">
                                <div class="submenu-card-header-left">
                                    <div class="submenu-icon">
                                        <ion-icon name="folder-open-outline"></ion-icon>
                                    </div>
                                    <div class="submenu-info">
                                        <h4>
                                            <?= htmlspecialchars($submenu['nome']) ?>
                                            <?php if ($submenu['inativo']) : ?>
                                                <span class="status-inactive-badge">Inativo</span>
                                            <?php endif; ?>
                                        </h4>
                                        <span><?= $submenu['link'] ?: 'Sem link' ?></span>
                                    </div>
                                </div>
                                <div class="menu-card-header-right">
                                    <?php $itensAtivos = count(array_filter($submenu['itens'], function($i) { return !$i['inativo']; })); ?>
                                    <span class="menu-badge-count"><?= $itensAtivos ?> itens</span>
                                    <div class="header-actions" onclick="event.stopPropagation();">
                                        <?php if ($submenu['inativo']) : ?>
                                            <a href="bd/submenu/ativa.php?idSubMenu=<?= $submenu['id'] ?>" class="btn-action activate" title="Ativar">
                                                <ion-icon name="checkmark-outline"></ion-icon>
                                            </a>
                                        <?php else : ?>
                                            <a href="bd/submenu/desativa.php?idSubMenu=<?= $submenu['id'] ?>" class="btn-action deactivate" title="Desativar">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn-action edit" onclick="abrirModalSubMenu(<?= $submenu['id'] ?>, <?= htmlspecialchars(json_encode($submenu['nome']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($submenu['link'] ?? ''), ENT_QUOTES) ?>, <?= $menu['id'] ?>)" title="Editar">
                                            <ion-icon name="pencil-outline"></ion-icon>
                                        </button>
                                    </div>
                                    <div class="expand-icon">
                                        <ion-icon name="chevron-down-outline"></ion-icon>
                                    </div>
                                </div>
                            </div>

                            <div class="submenu-card-body">
                                <?php if (empty($submenu['itens'])) : ?>
                                    <p style="color: #94a3b8; text-align: center; padding: 16px 0;">Nenhum item cadastrado</p>
                                <?php else : ?>
                                    <?php foreach ($submenu['itens'] as $item) : ?>
                                    <div class="item-row <?= $item['inativo'] ? 'inactive' : '' ?>">
                                        <div class="item-info">
                                            <div class="item-icon">
                                                <ion-icon name="document-outline"></ion-icon>
                                            </div>
                                            <div class="item-text">
                                                <h5>
                                                    <?= htmlspecialchars($item['nome']) ?>
                                                    <?php if ($item['inativo']) : ?>
                                                        <span class="status-inactive-badge">Inativo</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <span><?= $item['link'] ?: 'Sem link' ?></span>
                                            </div>
                                        </div>
                                        <div class="header-actions">
                                            <?php if ($item['inativo']) : ?>
                                                <a href="bd/itemmenu/ativa.php?idItemMenu=<?= $item['id'] ?>" class="btn-action activate" title="Ativar">
                                                    <ion-icon name="checkmark-outline"></ion-icon>
                                                </a>
                                            <?php else : ?>
                                                <a href="bd/itemmenu/desativa.php?idItemMenu=<?= $item['id'] ?>" class="btn-action deactivate" title="Desativar">
                                                    <ion-icon name="close-outline"></ion-icon>
                                                </a>
                                            <?php endif; ?>
                                            <button class="btn-action edit" onclick="abrirModalItem(<?= $item['id'] ?>, <?= htmlspecialchars(json_encode($item['nome']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($item['link'] ?? ''), ENT_QUOTES) ?>, <?= $submenu['id'] ?>)" title="Editar">
                                                <ion-icon name="pencil-outline"></ion-icon>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <button class="add-item-btn" onclick="abrirModalItem(null, '', '', <?= $submenu['id'] ?>)">
                                    <ion-icon name="add-outline"></ion-icon>
                                    Adicionar Item
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <button class="add-submenu-btn" onclick="abrirModalSubMenu(null, '', '', <?= $menu['id'] ?>)">
                        <ion-icon name="add-outline"></ion-icon>
                        Adicionar SubMenu
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modais -->
    <div class="modal-overlay" id="modalMenu">
        <div class="modal">
            <div class="modal-header">
                <h3><ion-icon name="folder-outline"></ion-icon><span id="modalMenuTitle">Novo Menu</span></h3>
                <button class="modal-close" onclick="fecharModal('modalMenu')"><ion-icon name="close-outline"></ion-icon></button>
            </div>
            <form id="formMenu" action="bd/menus/create.php" method="post">
                <input type="hidden" name="idMenu" id="inputMenuId">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label"><ion-icon name="text-outline"></ion-icon>Nome do Menu <span class="required">*</span></label>
                        <input type="text" name="nmMenu" id="inputMenuNome" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><ion-icon name="link-outline"></ion-icon>Link (opcional)</label>
                        <input type="text" name="linkMenu" id="inputMenuLink" class="form-control" placeholder="Ex: pagina.php">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modalMenu')">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><ion-icon name="checkmark-outline"></ion-icon>Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalSubMenu">
        <div class="modal">
            <div class="modal-header">
                <h3><ion-icon name="folder-open-outline"></ion-icon><span id="modalSubMenuTitle">Novo SubMenu</span></h3>
                <button class="modal-close" onclick="fecharModal('modalSubMenu')"><ion-icon name="close-outline"></ion-icon></button>
            </div>
            <form id="formSubMenu" action="bd/submenu/create.php" method="post">
                <input type="hidden" name="idSubMenu" id="inputSubMenuId">
                <input type="hidden" name="idMenu" id="inputSubMenuPai">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label"><ion-icon name="text-outline"></ion-icon>Nome do SubMenu <span class="required">*</span></label>
                        <input type="text" name="nmSubMenu" id="inputSubMenuNome" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><ion-icon name="link-outline"></ion-icon>Link (opcional)</label>
                        <input type="text" name="linkSubMenu" id="inputSubMenuLink" class="form-control" placeholder="Ex: pagina.php">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modalSubMenu')">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><ion-icon name="checkmark-outline"></ion-icon>Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalItem">
        <div class="modal">
            <div class="modal-header">
                <h3><ion-icon name="document-outline"></ion-icon><span id="modalItemTitle">Novo Item</span></h3>
                <button class="modal-close" onclick="fecharModal('modalItem')"><ion-icon name="close-outline"></ion-icon></button>
            </div>
            <form id="formItem" action="bd/itemmenu/create.php" method="post">
                <input type="hidden" name="idItemMenu" id="inputItemId">
                <input type="hidden" name="idSubMenu" id="inputItemPai">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label"><ion-icon name="text-outline"></ion-icon>Nome do Item <span class="required">*</span></label>
                        <input type="text" name="nmItemMenu" id="inputItemNome" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><ion-icon name="link-outline"></ion-icon>Link <span class="required">*</span></label>
                        <input type="text" name="linkItemMenu" id="inputItemLink" class="form-control" placeholder="Ex: pagina.php" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modalItem')">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><ion-icon name="checkmark-outline"></ion-icon>Salvar</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================
         ABA: USUÁRIOS
         Usa TIPO_LICITACAO como perfis de acesso
         AJAX: bd/usuario/buscarUsuarios.php (GET)
         ============================================ -->
    <?php if ($abaAtiva == 'usuarios') : ?>
    <div class="tab-pane active" id="tab-usuarios">
        <!-- Filtros -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="search-outline"></ion-icon>
                    <h3>Pesquisar Usuários</h3>
                </div>
            </div>
            <div class="section-card-body">
                <div class="filter-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="person-outline"></ion-icon>
                            Nome, Login ou Matrícula
                        </label>
                        <input type="text" id="filtroNome" class="form-control" placeholder="Digite para pesquisar..." 
                            style="text-transform: uppercase;" autofocus>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="shield-outline"></ion-icon>
                            Perfil
                        </label>
                        <select id="filtroPerfil" class="form-select">
                            <option value="0">Todos os perfis</option>
                            <?php
                            // Perfis de acesso = TIPO_LICITACAO
                            $queryPerfis = $pdoCAT->query("SELECT * FROM TIPO_LICITACAO WHERE DT_EXC_TIPO IS NULL ORDER BY NM_TIPO");
                            while ($p = $queryPerfis->fetch(PDO::FETCH_ASSOC)) :
                                echo "<option value='{$p['ID_TIPO']}'>" . htmlspecialchars($p['NM_TIPO']) . "</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <ion-icon name="checkbox-outline"></ion-icon>
                            Cadastrado no sistema?
                        </label>
                        <div class="radio-group">
                            <input type="radio" name="usuSistema" id="usuTodos" value="todos" checked>
                            <label for="usuTodos" class="radio-group-label">Todos</label>
                            <input type="radio" name="usuSistema" id="usuSim" value="sim">
                            <label for="usuSim" class="radio-group-label">Sim</label>
                            <input type="radio" name="usuSistema" id="usuNao" value="nao">
                            <label for="usuNao" class="radio-group-label">Não</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listagem -->
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <ion-icon name="list-outline"></ion-icon>
                    <h3>Usuários</h3>
                    <span class="results-counter" id="resultsCounter" style="display: none;">
                        <span id="totalUsuarios">0</span> encontrados
                    </span>
                </div>
            </div>
            <div class="section-card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="modern-table" id="tabelaUsuarios">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Login</th>
                                <th>Nome</th>
                                <th>Unidade</th>
                                <th>E-mail</th>
                                <th>Perfil</th>
                                <th style="width: 120px; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyUsuarios">
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <ion-icon name="search-outline"></ion-icon>
                                        <h3>Digite para pesquisar</h3>
                                        <p>Informe nome, login ou matrícula (mín. 2 caracteres)</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    

</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
$(document).ready(function() {
    
    // Select2 com pesquisa
    $('#filtroPerfil').select2({
        placeholder: 'Todos os perfis',
        allowClear: true,
        width: '100%'
    });

    // ============================================
    // Busca AJAX de Usuários
    // Método: GET | Parâmetros: nome, perfilUsuario, usuSistema
    // ============================================
    var timeoutBusca;
    
    function buscarUsuarios() {
        var nome = $('#filtroNome').val().trim();
        var perfil = $('#filtroPerfil').val();
        var usuSistema = $('input[name="usuSistema"]:checked').val();
        
        if (nome.length < 2 && perfil == '0' && usuSistema == 'todos') {
            $('#tbodyUsuarios').html(`
                <tr><td colspan="7">
                    <div class="empty-state">
                        <ion-icon name="search-outline"></ion-icon>
                        <h3>Digite para pesquisar</h3>
                        <p>Informe nome, login ou matrícula (mín. 2 caracteres)</p>
                    </div>
                </td></tr>
            `);
            $('#resultsCounter').hide();
            return;
        }
        
        $('#tbodyUsuarios').html(`
            <tr><td colspan="7">
                <div class="table-loading">
                    <div class="loading-spinner"></div>
                    <p style="margin-top: 16px; color: #64748b;">Buscando usuários...</p>
                </div>
            </td></tr>
        `);
        $('#resultsCounter').hide();
        
        $.ajax({
            url: 'bd/usuario/buscarUsuarios.php',
            type: 'GET',
            data: {
                nome: nome,
                perfilUsuario: perfil,
                usuSistema: usuSistema
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderizarUsuarios(response.usuarios);
                    $('#totalUsuarios').text(response.total);
                    if (response.total > 0) $('#resultsCounter').show();
                } else {
                    $('#tbodyUsuarios').html(`
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <ion-icon name="alert-circle-outline"></ion-icon>
                                <h3>Erro ao buscar</h3>
                                <p>${response.error || 'Tente novamente'}</p>
                            </div>
                        </td></tr>
                    `);
                }
            },
            error: function() {
                $('#tbodyUsuarios').html(`
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <ion-icon name="alert-circle-outline"></ion-icon>
                            <h3>Erro de conexão</h3>
                            <p>Não foi possível conectar ao servidor</p>
                        </div>
                    </td></tr>
                `);
            }
        });
    }
    
    function renderizarUsuarios(usuarios) {
        if (usuarios.length === 0) {
            $('#tbodyUsuarios').html(`
                <tr><td colspan="7">
                    <div class="empty-state">
                        <ion-icon name="people-outline"></ion-icon>
                        <h3>Nenhum usuário encontrado</h3>
                        <p>Tente outros filtros</p>
                    </div>
                </td></tr>
            `);
            return;
        }
        
        var html = '';
        usuarios.forEach(function(u) {
            var perfilBadge = u.perfil 
                ? `<span class="badge badge-active">${escapeHtml(u.perfil)}</span>` 
                : '<span class="cell-secondary">-</span>';
            
            var acoes = '';
            if (!u.temMatricula) {
                if (u.status === 'A') {
                    acoes += `<a href="bd/usuario/desativa.php?email=${encodeURIComponent(u.email)}" class="btn-action deactivate" title="Desativar"><ion-icon name="close-outline"></ion-icon></a>`;
                } else {
                    acoes += `<a href="bd/usuario/ativa.php?email=${encodeURIComponent(u.email)}" class="btn-action activate" title="Ativar"><ion-icon name="checkmark-outline"></ion-icon></a>`;
                }
            }
            acoes += `<a href="usuarioForm.php?email=${encodeURIComponent(u.email)}&login=${encodeURIComponent(u.login)}" class="btn-action edit" title="Editar Perfil"><ion-icon name="pencil-outline"></ion-icon></a>`;
            
            html += `
                <tr>
                    <td class="cell-secondary">${escapeHtml(u.matricula)}</td>
                    <td><span class="cell-name">${escapeHtml(u.login)}</span></td>
                    <td>${escapeHtml(u.nome)}</td>
                    <td class="cell-secondary">${escapeHtml(u.unidade)}</td>
                    <td class="cell-secondary">${escapeHtml(u.email)}</td>
                    <td>${perfilBadge}</td>
                    <td><div class="action-buttons" style="justify-content: center;">${acoes}</div></td>
                </tr>
            `;
        });
        
        $('#tbodyUsuarios').html(html);
    }
    
    function escapeHtml(text) {
        if (!text) return '-';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
    
    $('#filtroNome').on('keyup', function() {
        clearTimeout(timeoutBusca);
        timeoutBusca = setTimeout(buscarUsuarios, 400);
    });
    
    $('#filtroPerfil').on('change', buscarUsuarios);
    $('input[name="usuSistema"]').on('change', buscarUsuarios);

    // ============================================
    // Edição Inline - TIPOS
    // ============================================
    $('.edit-btn-tipo').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowTipo' + id);
        var nmTipo = row.find('.pubnmTipo').text();
        var sglTipo = row.find('.pubsglTipo').text();
        
        row.find('.pubnmTipo').replaceWith(`<input type="text" class="inline-edit-input pubnmTipo" value="${nmTipo}" />`);
        row.find('.pubsglTipo').replaceWith(`<input type="text" class="inline-edit-input pubsglTipo" value="${sglTipo}" style="width: 80px;" />`);
        
        row.find('.edit-btn-tipo').hide();
        row.find('.save-btn-tipo, .cancel-btn-tipo').show();
        row.find('.deactivate, .activate').hide();
    });

    $('.save-btn-tipo').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowTipo' + id);
        var nmTipo = row.find('input.pubnmTipo').val().trim();
        var sglTipo = row.find('input.pubsglTipo').val().trim();
        
        if (nmTipo === '' || sglTipo === '') { alert('Preencha todos os campos'); return; }
        window.location.href = `bd/tipo/update.php?idTipo=${id}&nmTipo=${encodeURIComponent(nmTipo)}&sglTipo=${encodeURIComponent(sglTipo)}`;
    });

    $('.cancel-btn-tipo').on('click', function() { location.reload(); });

    // ============================================
    // Edição Inline - CRITÉRIOS
    // ============================================
    $('.edit-btn-criterio').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowCriterio' + id);
        var currentName = row.find('.pubnmCriterio').text();
        
        row.find('.pubnmCriterio').replaceWith(`<input type="text" class="inline-edit-input pubnmCriterio" value="${currentName}" />`);
        
        row.find('.edit-btn-criterio').hide();
        row.find('.save-btn-criterio, .cancel-btn-criterio').show();
        row.find('.deactivate, .activate').hide();
    });

    $('.save-btn-criterio').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowCriterio' + id);
        var nmCriterio = row.find('input.pubnmCriterio').val().trim();
        
        if (nmCriterio === '') { alert('O nome é obrigatório'); return; }
        window.location.href = `bd/criterio/update.php?idCriterio=${id}&nmCriterio=${encodeURIComponent(nmCriterio)}`;
    });

    $('.cancel-btn-criterio').on('click', function() { location.reload(); });

    // ============================================
    // Edição Inline - FORMAS
    // ============================================
    $('.edit-btn-forma').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowForma' + id);
        var currentName = row.find('.pubnmForma').text();
        
        row.find('.pubnmForma').replaceWith(`<input type="text" class="inline-edit-input pubnmForma" value="${currentName}" />`);
        
        row.find('.edit-btn-forma').hide();
        row.find('.save-btn-forma, .cancel-btn-forma').show();
        row.find('.deactivate, .activate').hide();
    });

    $('.save-btn-forma').on('click', function() {
        var id = $(this).data('id');
        var row = $('#rowForma' + id);
        var nmForma = row.find('input.pubnmForma').val().trim();
        
        if (nmForma === '') { alert('O nome é obrigatório'); return; }
        window.location.href = `bd/forma/update.php?idForma=${id}&nmForma=${encodeURIComponent(nmForma)}`;
    });

    $('.cancel-btn-forma').on('click', function() { location.reload(); });

    
});

// ============================================
// Acordeão de Menus
// ============================================
function toggleMenu(id) {
    document.getElementById('menu-' + id).classList.toggle('expanded');
}

function toggleSubMenu(id) {
    event.stopPropagation();
    document.getElementById('submenu-' + id).classList.toggle('expanded');
}

// ============================================
// Modais
// ============================================
function fecharModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function abrirModalMenu(id = null, nome = '', link = '') {
    document.getElementById('modalMenuTitle').textContent = id ? 'Editar Menu' : 'Novo Menu';
    document.getElementById('inputMenuId').value = id || '';
    document.getElementById('inputMenuNome').value = nome;
    document.getElementById('inputMenuLink').value = link;
    document.getElementById('formMenu').action = id ? 'bd/menus/update.php' : 'bd/menus/create.php';
    document.getElementById('modalMenu').classList.add('active');
    document.getElementById('inputMenuNome').focus();
}

function abrirModalSubMenu(id = null, nome = '', link = '', menuPai = null) {
    event.stopPropagation();
    document.getElementById('modalSubMenuTitle').textContent = id ? 'Editar SubMenu' : 'Novo SubMenu';
    document.getElementById('inputSubMenuId').value = id || '';
    document.getElementById('inputSubMenuNome').value = nome;
    document.getElementById('inputSubMenuLink').value = link;
    document.getElementById('inputSubMenuPai').value = menuPai || '';
    document.getElementById('formSubMenu').action = id ? 'bd/submenu/update.php' : 'bd/submenu/create.php';
    document.getElementById('modalSubMenu').classList.add('active');
    document.getElementById('inputSubMenuNome').focus();
}

function abrirModalItem(id = null, nome = '', link = '', subMenuPai = null) {
    event.stopPropagation();
    document.getElementById('modalItemTitle').textContent = id ? 'Editar Item' : 'Novo Item';
    document.getElementById('inputItemId').value = id || '';
    document.getElementById('inputItemNome').value = nome;
    document.getElementById('inputItemLink').value = link;
    document.getElementById('inputItemPai').value = subMenuPai || '';
    document.getElementById('formItem').action = id ? 'bd/itemmenu/update.php' : 'bd/itemmenu/create.php';
    document.getElementById('modalItem').classList.add('active');
    document.getElementById('inputItemNome').focus();
}

// Fechar modal ao clicar fora ou ESC
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => modal.classList.remove('active'));
    }
});
</script>

<?php include_once 'includes/footer.inc.php'; ?>