<?php
//consultarLicitacao.php
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';

// Filtros
$tituloLicitacaoFilter = filter_input(INPUT_POST, 'tituloLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$statusLicitacaoFilter = filter_input(INPUT_POST, 'statusLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'Em Andamento';
$dtIniLicitacaoFilter = filter_input(INPUT_POST, 'dtIniLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$dtFimLicitacaoFilter = filter_input(INPUT_POST, 'dtFimLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$tipoLicitacaoFilter = filter_input(INPUT_POST, 'tipoLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
?>

<style>
    /* ============================================
   PÁGINA DE LICITAÇÕES - Estilo Rede de Ideias
   ============================================ */

    /* =============================================
   RESET MATERIALIZE - FORÇA SELECT NATIVO
   ============================================= */

    /* A classe browser-default do Materialize impede a inicialização customizada */
    .filters-section select.browser-default {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        position: relative !important;
        z-index: 1 !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 14px center !important;
        background-color: #f8fafc !important;
        padding: 12px 40px 12px 16px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        font-size: 14px !important;
        color: #334155 !important;
        font-family: 'Inter', sans-serif !important;
        cursor: pointer !important;
        box-sizing: border-box !important;
        height: 46px !important;
        width: 100% !important;
        transition: all 0.2s ease !important;
    }

    .filters-section select.browser-default:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    /* Esconde elementos do Materialize que possam aparecer */
    .filters-section .select-wrapper,
    .filters-section .dropdown-content,
    .filters-section ul.dropdown-content,
    .filters-section input.select-dropdown,
    .filters-section .caret {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
        position: absolute !important;
        z-index: -9999 !important;
    }

    /* Custom Select Container */
    .filters-section .custom-select {
        position: relative;
        width: 100%;
    }

    .page-container {
        padding: 32px;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Hero Section */
    .page-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 20px;
        padding: 40px 48px;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 24px;
    }

    .page-hero-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .page-hero-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 28px;
    }

    .page-hero-text h1 {
        color: #ffffff;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        letter-spacing: -0.02em;
    }

    .page-hero-text p {
        color: #94a3b8;
        font-size: 15px;
        margin: 0;
    }

    /* View Toggle */
    .view-toggle {
        display: flex;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 4px;
        gap: 4px;
    }

    .view-toggle-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        background: transparent;
        color: #94a3b8;
    }

    .view-toggle-btn:hover {
        color: #ffffff;
    }

    .view-toggle-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .view-toggle-btn ion-icon {
        font-size: 18px;
    }

    /* Filters Section */
    .filters-section {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 16px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-group label ion-icon {
        font-size: 14px;
    }

    .filter-group input[type="text"],
    .filter-group input[type="date"],
    .filter-group select {
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        color: #334155;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        width: 100%;
        font-family: 'Inter', sans-serif;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
        box-sizing: border-box;
        height: 46px;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-group input::placeholder {
        color: #94a3b8;
    }

    .filter-dates {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .filter-dates input {
        flex: 1;
    }

    .filter-dates span {
        color: #94a3b8;
        font-size: 13px;
        flex-shrink: 0;
    }

    .btn-filter {
        padding: 12px 24px;
        background: #0f172a;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        height: 46px;
    }

    .btn-filter:hover {
        background: #1e293b;
        transform: translateY(-1px);
    }

    .btn-clear {
        padding: 12px;
        background: transparent;
        color: #64748b;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 46px;
        width: 46px;
    }

    .btn-clear:hover {
        background: #f1f5f9;
        color: #334155;
    }

    /* Loading indicator */
    .loading-indicator {
        display: none;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-size: 14px;
        padding: 12px 0;
    }

    .loading-indicator.active {
        display: flex;
    }

    .loading-indicator ion-icon {
        font-size: 20px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Results Counter */
    .results-info {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 0;
        color: #64748b;
        font-size: 14px;
    }

    .results-info ion-icon {
        font-size: 18px;
    }

    .results-count {
        font-weight: 600;
        color: #0f172a;
    }

    /* Cards Grid */
    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 24px;
    }

    .licitacao-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .licitacao-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .card-codigo {
        display: inline-flex;
        padding: 6px 12px;
        background: #dbeafe;
        color: #1d4ed8;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
    }

    .card-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .card-status.em-andamento {
        background: #dcfce7;
        color: #166534;
    }

    .card-status.suspenso {
        background: #fef3c7;
        color: #92400e;
    }

    .card-status.encerrado {
        background: #fee2e2;
        color: #991b1b;
    }

    .card-status.rascunho {
        background: #f1f5f9;
        color: #64748b;
    }

    .card-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }

    .card-title a:hover {
        color: #3b82f6;
    }

    .card-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .card-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #64748b;
    }

    .card-meta-item ion-icon {
        font-size: 16px;
        color: #94a3b8;
    }

    .card-meta-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: #f1f5f9;
        border-radius: 6px;
        font-size: 12px;
        color: #475569;
    }

    .card-objeto {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        margin-top: auto;
    }

    .card-date {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #64748b;
    }

    .card-date ion-icon {
        font-size: 16px;
    }

    .card-actions {
        display: flex;
        gap: 8px;
    }

    .card-action-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #64748b;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .card-action-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .card-action-btn.notify {
        color: #ef4444;
    }

    .card-action-btn.notify:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }

    .card-action-btn.notify.active {
        background: #fef2f2;
        border-color: #fca5a5;
    }

    /* Table View */
    .table-container {
        display: none;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
    }

    .table-container.active {
        display: block;
    }

    .cards-container.hidden {
        display: none;
    }

    /* Table Scroll Wrapper */
    .table-scroll-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Custom Scrollbar */
    .table-scroll-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .licitacoes-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .licitacoes-table thead {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .licitacoes-table th {
        padding: 16px 20px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .licitacoes-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s ease;
    }

    .licitacoes-table tbody tr:hover {
        background: #f8fafc;
    }

    .licitacoes-table tbody tr:last-child {
        border-bottom: none;
    }

    .licitacoes-table td {
        padding: 16px 20px;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
    }

    .table-codigo {
        font-weight: 600;
        color: #1d4ed8;
        white-space: nowrap;
    }

    .table-codigo a {
        color: inherit;
        text-decoration: none;
    }

    .table-codigo a:hover {
        text-decoration: underline;
    }

    .table-objeto {
        max-width: 400px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #64748b;
    }

    .table-tipo {
        display: inline-flex;
        padding: 4px 10px;
        background: #f1f5f9;
        border-radius: 6px;
        font-size: 12px;
        color: #475569;
        white-space: nowrap;
    }

    .table-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .table-actions {
        display: flex;
        gap: 8px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 40px;
        color: #64748b;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 600;
        color: #334155;
        margin: 0 0 8px 0;
    }

    .empty-state p {
        font-size: 14px;
        margin: 0;
    }

    /* Results Container */
    #resultsContainer {
        min-height: 200px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .filters-grid {
            grid-template-columns: 1fr 1fr;
        }

        .filter-group.dates-group {
            grid-column: span 2;
        }
    }

    @media (max-width: 768px) {
        .page-container {
            padding: 16px;
        }

        .page-hero {
            padding: 24px;
            flex-direction: column;
            text-align: center;
        }

        .page-hero-content {
            flex-direction: column;
        }

        .page-hero-text h1 {
            font-size: 22px;
        }

        .view-toggle {
            width: 100%;
            justify-content: center;
        }

        .filters-section {
            padding: 16px;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .filter-group.dates-group {
            grid-column: span 1;
        }

        .filter-dates {
            flex-direction: column;
        }

        .filter-dates span {
            display: none;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        .btn-filter {
            flex: 1;
            justify-content: center;
        }

        .cards-container {
            grid-template-columns: 1fr;
        }

        /* ============================================
       FORÇAR MODO CARDS EM MOBILE
       ============================================ */
        .view-toggle-btn[data-view="table"] {
            display: none !important;
        }

        .table-container {
            display: none !important;
        }

        .table-container.active {
            display: none !important;
        }

        .cards-container {
            display: grid !important;
        }

        .cards-container.hidden {
            display: grid !important;
        }

        /* ============================================ */
    }

    @media (max-width: 480px) {
        .card-header {
            flex-direction: column;
            gap: 8px;
        }

        .card-footer {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }

        .card-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<div class="page-container">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="page-hero-icon">
                <ion-icon name="document-text-outline"></ion-icon>
            </div>
            <div class="page-hero-text">
                <h1>Licitações</h1>
                <p>Consulte e gerencie todas as licitações do sistema</p>
            </div>
        </div>

        <div class="view-toggle">
            <button type="button" class="view-toggle-btn active" data-view="cards" onclick="toggleView('cards')">
                <ion-icon name="grid-outline"></ion-icon>
                Cards
            </button>
            <button type="button" class="view-toggle-btn" data-view="table" onclick="toggleView('table')">
                <ion-icon name="list-outline"></ion-icon>
                Tabela
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section" data-no-materialize="true">
        <form id="formFiltrar" onsubmit="return false;">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>
                        <ion-icon name="search-outline"></ion-icon>
                        Buscar
                    </label>
                    <input type="text" name="tituloLicitacao" id="tituloLicitacao"
                        placeholder="Título ou objeto da licitação..."
                        value="<?php echo htmlspecialchars($tituloLicitacaoFilter); ?>">
                </div>

                <div class="filter-group">
                    <label>
                        <ion-icon name="flag-outline"></ion-icon>
                        Status
                    </label>
                    <div class="custom-select">
                        <select name="statusLicitacao" id="statusLicitacao" class="browser-default">
                            <option value="vazio" <?php echo $statusLicitacaoFilter == 'vazio' ? 'selected' : ''; ?>>Todos
                                os Status</option>
                            <option value="Em Andamento" <?php echo $statusLicitacaoFilter == 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                            <option value="Suspenso" <?php echo $statusLicitacaoFilter == 'Suspenso' ? 'selected' : ''; ?>>Suspenso</option>
                            <option value="Encerrado" <?php echo $statusLicitacaoFilter == 'Encerrado' ? 'selected' : ''; ?>>Encerrado</option>
                            <?php if (!empty($_SESSION['idPerfilFinal'])) { ?>
                                <option value="Rascunho" <?php echo $statusLicitacaoFilter == 'Rascunho' ? 'selected' : ''; ?>>Rascunho</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <label>
                        <ion-icon name="pricetag-outline"></ion-icon>
                        Tipo
                    </label>
                    <div class="custom-select">
                        <select name="tipoLicitacao" id="tipoLicitacao" class="browser-default">
                            <option value="vazio">Todos os Tipos</option>
                            <?php
                            $queryTipos = "SELECT * FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE ID_TIPO <> 9 AND DT_EXC_TIPO IS NULL";
                            $resultTipos = $pdoCAT->query($queryTipos);
                            while ($tipo = $resultTipos->fetch(PDO::FETCH_ASSOC)):
                                $selected = ($tipoLicitacaoFilter == $tipo['ID_TIPO']) ? 'selected' : '';
                                echo "<option value='" . $tipo["ID_TIPO"] . "' $selected>" . $tipo["NM_TIPO"] . "</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>
                </div>

                <div class="filter-group dates-group">
                    <label>
                        <ion-icon name="calendar-outline"></ion-icon>
                        Período
                    </label>
                    <div class="filter-dates">
                        <input type="date" name="dtIniLicitacao" id="dtIniLicitacao"
                            value="<?php echo htmlspecialchars($dtIniLicitacaoFilter); ?>">
                        <span>até</span>
                        <input type="date" name="dtFimLicitacao" id="dtFimLicitacao"
                            value="<?php echo $dtFimLicitacaoFilter ? htmlspecialchars($dtFimLicitacaoFilter) : date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                </div>

                <div class="filter-actions" style="display: flex; gap: 8px; align-items: end;">
                    <button type="button" class="btn-clear" onclick="limparFiltros()" title="Limpar filtros">
                        <ion-icon name="close-outline"></ion-icon>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Loading Indicator -->
    <div class="loading-indicator" id="loadingIndicator">
        <ion-icon name="sync-outline"></ion-icon>
        Carregando...
    </div>

    <!-- Results Container -->
    <div id="resultsContainer">
        <?php include_once 'bd/licitacao/read.php'; ?>
    </div>
</div>

<script>
    let searchTimeout;

    // ============================================
    // VERIFICA SE ESTÁ EM MOBILE
    // ============================================
    function isMobile() {
        return window.matchMedia('(max-width: 768px)').matches;
    }
    // Destruir Materialize Select e restaurar select nativo
    function destroyMaterializeSelects() {
        const selects = document.querySelectorAll('.filters-section select');

        selects.forEach(select => {
            // Destruir instância do Materialize se existir
            if (typeof M !== 'undefined' && M.FormSelect) {
                const instance = M.FormSelect.getInstance(select);
                if (instance) {
                    instance.destroy();
                }
            }

            // Verificar se o Materialize criou um wrapper
            const wrapper = select.closest('.select-wrapper');
            if (wrapper && !wrapper.classList.contains('custom-select')) {
                // Remover elementos criados pelo Materialize
                const dropdown = wrapper.querySelector('input.select-dropdown');
                const caret = wrapper.querySelector('.caret, svg');
                const dropdownContent = wrapper.querySelector('ul.dropdown-content');

                if (dropdown) dropdown.remove();
                if (caret) caret.remove();
                if (dropdownContent) dropdownContent.remove();

                // Mover o select para fora do wrapper e remover o wrapper
                const parent = wrapper.parentNode;
                const customSelect = document.createElement('div');
                customSelect.className = 'custom-select';

                parent.insertBefore(customSelect, wrapper);
                customSelect.appendChild(select);
                wrapper.remove();
            }

            // Forçar display do select
            select.style.cssText = `
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            position: relative !important;
        `;
        });

        // Remover dropdowns órfãos em qualquer lugar
        document.querySelectorAll('.filters-section .dropdown-content, .filters-section ul.dropdown-content').forEach(el => {
            el.style.display = 'none';
            el.remove();
        });

        // Esconder inputs do Materialize
        document.querySelectorAll('.filters-section input.select-dropdown').forEach(el => {
            el.style.display = 'none';
            el.remove();
        });
    }

    // Toggle entre Cards e Tabela
    // ============================================
    // TOGGLE ENTRE CARDS E TABELA
    // ============================================
    function toggleView(view) {
        // CRÍTICO: Em mobile, sempre forçar cards
        if (isMobile()) {
            view = 'cards';
        }

        const cardsContainer = document.getElementById('cardsContainer');
        const tableContainer = document.getElementById('tableContainer');
        const buttons = document.querySelectorAll('.view-toggle-btn');

        if (!cardsContainer || !tableContainer) return;

        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });

        if (view === 'cards') {
            cardsContainer.classList.remove('hidden');
            tableContainer.classList.remove('active');
        } else {
            cardsContainer.classList.add('hidden');
            tableContainer.classList.add('active');
        }

        // Salvar preferência apenas se não for mobile
        if (!isMobile()) {
            localStorage.setItem('licitacaoView', view);
        }
    }

    // Pesquisar via AJAX
    function pesquisar() {
        const formData = new FormData(document.getElementById('formFiltrar'));
        const loadingIndicator = document.getElementById('loadingIndicator');
        const resultsContainer = document.getElementById('resultsContainer');

        // Mostrar loading
        loadingIndicator.classList.add('active');

        fetch('bd/licitacao/read.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(html => {
                resultsContainer.innerHTML = html;
                loadingIndicator.classList.remove('active');

                // ============================================
                // Restaurar preferência apenas se não for mobile
                // ============================================
                if (!isMobile()) {
                    const savedView = localStorage.getItem('licitacaoView');
                    if (savedView) {
                        toggleView(savedView);
                    }
                } else {
                    // Forçar cards em mobile
                    toggleView('cards');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                loadingIndicator.classList.remove('active');
            });
    }

    // Pesquisa automática com debounce
    function pesquisaAutomatica() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(pesquisar, 500);
    }

    // Limpar filtros
    function limparFiltros() {
        document.getElementById('tituloLicitacao').value = '';
        document.getElementById('statusLicitacao').value = 'Em Andamento';
        document.getElementById('tipoLicitacao').value = 'vazio';
        document.getElementById('dtIniLicitacao').value = '';
        document.getElementById('dtFimLicitacao').value = '';
        pesquisar();
    }

    // Confirmar exclusão
    function confirmDelete(idLicitacao) {
        if (confirm('Tem certeza que deseja excluir esta licitação?')) {
            window.location.href = 'bd/licitacao/delete.php?idLicitacao=' + idLicitacao;
        }
    }

    // Enviar atualização
    function enviarAtualizacao(idLicitacao) {
        window.location.href = 'bd/licitacao/enviarAtualizacao.php?idLicitacao=' + idLicitacao;
    }

    // Desativar atualização
    function desativarAtualizacao(idAtualizacao) {
        window.location.href = 'bd/atualizacao/desativar.php?idAtualizacao=' + idAtualizacao;
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function () {
        // IMPORTANTE: Destruir Materialize Select para usar select nativo
        destroyMaterializeSelects();
        setTimeout(destroyMaterializeSelects, 100);
        setTimeout(destroyMaterializeSelects, 300);
        setTimeout(destroyMaterializeSelects, 500);
        setTimeout(destroyMaterializeSelects, 1000);

        // Impedir que o Materialize inicialize os selects do filtro
        if (typeof M !== 'undefined') {
            // Observar mudanças no DOM para destruir Materialize quando ele tentar inicializar
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.addedNodes.length) {
                        const hasDropdown = document.querySelector('.filters-section .dropdown-content, .filters-section input.select-dropdown');
                        if (hasDropdown) {
                            destroyMaterializeSelects();
                        }
                    }
                });
            });

            const filtersSection = document.querySelector('.filters-section');
            if (filtersSection) {
                observer.observe(filtersSection, { childList: true, subtree: true });
            }
        }

        // Restaurar preferência de visualização
        // ============================================
        // CRÍTICO: Forçar cards em mobile
        // ============================================
        if (isMobile()) {
            toggleView('cards');
        } else {
            // Restaurar preferência apenas em desktop
            const savedView = localStorage.getItem('licitacaoView');
            if (savedView) {
                toggleView(savedView);
            }
        }

        // ============================================
        // Listener para resize - forçar cards se mudar para mobile
        // ============================================
        let resizeTimeout;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function () {
                if (isMobile()) {
                    toggleView('cards');
                }
            }, 250);
        });

        // Pesquisa automática ao digitar
        document.getElementById('tituloLicitacao').addEventListener('input', pesquisaAutomatica);

        // Pesquisa automática ao mudar selects
        document.getElementById('statusLicitacao').addEventListener('change', pesquisar);
        document.getElementById('tipoLicitacao').addEventListener('change', pesquisar);

        // Pesquisa automática ao mudar datas
        document.getElementById('dtIniLicitacao').addEventListener('change', pesquisar);
        document.getElementById('dtFimLicitacao').addEventListener('change', pesquisar);
    });
</script>

<?php include_once 'includes/footer.inc.php'; ?>