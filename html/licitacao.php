<?php

/**
 * Portal de Compras - CESAN
 * Tela de Consulta de Licitações
 * 
 * Layout refatorado - Header + Filtros integrados
 */

$paginaAtual = 'licitacao';

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/menu.inc.php';

// Filtros
$tituloLicitacaoFilter = filter_input(INPUT_POST, 'tituloLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$statusLicitacaoFilter = filter_input(INPUT_POST, 'statusLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'Em Andamento';
$dtIniLicitacaoFilter = filter_input(INPUT_POST, 'dtIniLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$dtFimLicitacaoFilter = filter_input(INPUT_POST, 'dtFimLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$tipoLicitacaoFilter = filter_input(INPUT_POST, 'tipoLicitacao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

// Verificar se usuário pode editar
$podeEditar = !empty($_SESSION['idPerfilFinal']);

// Buscar tipos de licitação para o filtro
try {
    $sqlTipos = $pdoCAT->query("SELECT ID_TIPO, NM_TIPO, SGL_TIPO FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL AND NM_TIPO NOT LIKE 'ADMINISTRADOR' ORDER BY NM_TIPO");
    $tipos = $sqlTipos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $tipos = [];
}

// Situações disponíveis
$situacoes = [
    '' => 'Todos',
    'Em Andamento' => 'Em Andamento',
    'Suspenso' => 'Suspenso',
    'Encerrado' => 'Encerrado'
];

if ($podeEditar) {
    $situacoes['Rascunho'] = 'Rascunho';
}
?>

<!-- jQuery (necessário para Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- CSS da página -->
<link rel="stylesheet" href="style/css/licitacao.css" />

<div class="page-container">

    <!-- ============================================
         BLOCO UNIFICADO: Header + Stats + Filtros
         ============================================ -->
    <div class="page-header-unified">

        <!-- Elementos decorativos -->
        <div class="header-decoration">
            <div class="decoration-circle decoration-circle-1"></div>
            <div class="decoration-circle decoration-circle-2"></div>
            <div class="decoration-line"></div>
        </div>

        <!-- Breadcrumb + Data -->
        <div class="header-top-row">
            <div class="header-breadcrumb">
                <a href="index.php"><ion-icon name="home-outline"></ion-icon> Início</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <span>Licitações</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <!-- Título + Ações -->
        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <ion-icon name="document-text-outline"></ion-icon>
                    <div class="icon-box-pulse"></div>
                </div>
                <div class="header-title-group">
                    <h1>Consulta de Licitações</h1>
                    <p class="header-subtitle">
                        <ion-icon name="business-outline"></ion-icon>
                        Portal de Compras — CESAN
                    </p>
                </div>
            </div>

            <div class="header-right">
                <!-- Toggle de Visualização -->
                <div class="view-toggle">
                    <button type="button" onclick="toggleView('cards')" id="btnViewCards" class="active" title="Visualização em Cards">
                        <ion-icon name="grid-outline"></ion-icon>
                    </button>
                    <button type="button" onclick="toggleView('table')" id="btnViewTable" title="Visualização em Tabela">
                        <ion-icon name="list-outline"></ion-icon>
                    </button>
                </div>
                <?php if ($podeEditar): ?>
                    <a href="licitacaoForm.php" class="btn-novo-pro">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        <span>Nova Licitação</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Barra de Estatísticas -->
        <div class="header-stats-bar">
            <div class="stat-item" id="statTotal">
                <div class="stat-icon stat-icon-total">
                    <ion-icon name="albums-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <span class="stat-number" id="statTotalNum">—</span>
                    <span class="stat-label">Total</span>
                </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item" id="statAndamento">
                <div class="stat-icon stat-icon-andamento">
                    <ion-icon name="play-circle-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <span class="stat-number" id="statAndamentoNum">—</span>
                    <span class="stat-label">Em Andamento</span>
                </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item" id="statEncerrado">
                <div class="stat-icon stat-icon-encerrado">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <span class="stat-number" id="statEncerradoNum">—</span>
                    <span class="stat-label">Encerrados</span>
                </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item" id="statSuspenso">
                <div class="stat-icon stat-icon-suspenso">
                    <ion-icon name="pause-circle-outline"></ion-icon>
                </div>
                <div class="stat-info">
                    <span class="stat-number" id="statSuspensoNum">—</span>
                    <span class="stat-label">Suspensos</span>
                </div>
            </div>
        </div>

        <!-- ============================================
             Filtros integrados (colapsável)
             ============================================ -->
        <div class="filters-section" id="filtersSection">
            <div class="filters-section-header">
                <div class="filters-title">
                    <ion-icon name="filter-outline"></ion-icon>
                    Filtros de Pesquisa
                </div>
                <div class="filters-actions">
                    <button type="button" class="btn-clear-filters" onclick="limparFiltros()">
                        <ion-icon name="refresh-outline"></ion-icon>
                        Limpar Filtros
                    </button>
                    <button type="button" class="btn-toggle-filters" id="btnToggleFilters" onclick="toggleFilters()" title="Recolher filtros">
                        <ion-icon name="chevron-up-outline" id="iconToggleFilters"></ion-icon>
                    </button>
                </div>
            </div>

            <div class="filters-body" id="filtersBody">
                <form id="formFiltros">
                    <div class="filters-grid">
                        <!-- Busca por Código/Objeto -->
                        <div class="form-group">
                            <label class="form-label">
                                <ion-icon name="search-outline"></ion-icon>
                                Buscar por Código ou Objeto
                            </label>
                            <input type="text" class="form-control" id="tituloLicitacao" name="tituloLicitacao"
                                placeholder="Digite o código ou objeto da licitação..."
                                value="<?php echo htmlspecialchars($tituloLicitacaoFilter); ?>">
                        </div>

                        <!-- Tipo de Licitação (Select2) -->
                        <div class="form-group">
                            <label class="form-label">
                                <ion-icon name="pricetag-outline"></ion-icon>
                                Tipo
                            </label>
                            <select id="tipoLicitacao" name="tipoLicitacao" class="select2-tipo" style="width: 100%;">
                                <option value="">Todos os Tipos</option>
                                <?php foreach ($tipos as $tipo): ?>
                                    <option value="<?= $tipo['ID_TIPO'] ?>" <?= $tipoLicitacaoFilter == $tipo['ID_TIPO'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['NM_TIPO']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Situação (Radio Button) -->
                        <div class="form-group">
                            <label class="form-label">
                                <ion-icon name="checkmark-circle-outline"></ion-icon>
                                Situação
                            </label>
                            <div class="radio-group">
                                <?php foreach ($situacoes as $value => $label): ?>
                                    <label class="radio-item">
                                        <input type="radio" name="statusLicitacao" value="<?= $value ?>"
                                            <?= $statusLicitacaoFilter == $value ? 'checked' : '' ?>>
                                        <span class="radio-label"><?= $label ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Segunda linha: Datas -->
                    <div class="filters-grid-row2">
                        <!-- Data Inicial -->
                        <div class="form-group">
                            <label class="form-label">
                                <ion-icon name="calendar-outline"></ion-icon>
                                Data Inicial
                            </label>
                            <input type="date" class="form-control" id="dtIniLicitacao" name="dtIniLicitacao"
                                value="<?php echo htmlspecialchars($dtIniLicitacaoFilter); ?>">
                        </div>

                        <!-- Data Final -->
                        <div class="form-group">
                            <label class="form-label">
                                <ion-icon name="calendar-outline"></ion-icon>
                                Data Final
                            </label>
                            <input type="date" class="form-control" id="dtFimLicitacao" name="dtFimLicitacao"
                                value="<?php echo htmlspecialchars($dtFimLicitacaoFilter); ?>">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Resumo dos filtros ativos (visível quando recolhido) -->
            <div class="filters-summary" id="filtersSummary">
                <span class="filters-summary-text" id="filtersSummaryText"></span>
            </div>
        </div>

    </div>
    <!-- FIM do bloco unificado -->

    <!-- Container de Resultados -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-info">
                <strong id="totalRegistros">0</strong> licitação(ões) encontrada(s)
            </div>
            <div class="table-actions">
                <!-- Ações podem ser adicionadas aqui -->
            </div>
        </div>

        <!-- View: Cards -->
        <div class="view-cards active" id="viewCards">
            <div class="cards-container" id="cardsContainer">
                <!-- Preenchido via JavaScript -->
            </div>
            <div class="pagination-container">
                <div class="pagination-info">
                    Mostrando <span id="paginacaoInicioCards">0</span> a <span id="paginacaoFimCards">0</span> de <span id="paginacaoTotalCards">0</span>
                </div>
                <div class="pagination" id="paginacaoCards"></div>
            </div>
        </div>

        <!-- View: Tabela -->
        <div class="view-table" id="viewTable">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Objeto</th>
                            <th>Status</th>
                            <th>Data Abertura</th>
                            <th>Responsável</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaBody">
                        <!-- Preenchido via JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="pagination-container">
                <div class="pagination-info">
                    Mostrando <span id="paginacaoInicio">0</span> a <span id="paginacaoFim">0</span> de <span id="paginacaoTotal">0</span>
                </div>
                <div class="pagination" id="paginacao"></div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<script>
    // ============================================
    // Variáveis Globais
    // ============================================
    let paginaAtualLic = 1;
    let totalRegistros = 0;
    let registrosPorPagina = 20;
    const podeEditar = <?php echo $podeEditar ? 'true' : 'false'; ?>;
    const STORAGE_KEY_VIEW = 'licitacao_view_preference';
    const STORAGE_KEY_FILTERS = 'licitacao_filters';
    const STORAGE_KEY_FILTERS_COLLAPSED = 'licitacao_filters_collapsed';

    // ============================================
    // Inicialização
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2
        $('.select2-tipo').select2({
            placeholder: 'Selecione o tipo...',
            allowClear: true,
            width: '100%'
        });

        // Listeners para auto-pesquisa
        document.getElementById('tituloLicitacao').addEventListener('input', debounce(function() {
            paginaAtualLic = 1;
            salvarFiltros();
            pesquisar();
        }, 400));

        $('input[name="statusLicitacao"]').on('change', function() {
            paginaAtualLic = 1;
            salvarFiltros();
            pesquisar();
        });

        $('#tipoLicitacao').on('change', function() {
            paginaAtualLic = 1;
            salvarFiltros();
            pesquisar();
        });

        document.getElementById('dtIniLicitacao').addEventListener('change', function() {
            paginaAtualLic = 1;
            salvarFiltros();
            pesquisar();
        });

        document.getElementById('dtFimLicitacao').addEventListener('change', function() {
            paginaAtualLic = 1;
            salvarFiltros();
            pesquisar();
        });

        // Restaurar filtros e estado dos filtros
        restaurarFiltros();
        restaurarEstadoFiltros();

        // Restaurar preferência de view
        const savedView = localStorage.getItem(STORAGE_KEY_VIEW);
        if (savedView && !isMobile()) {
            toggleView(savedView);
        }

        // Atualizar data no header
        atualizarDataHeader();

        // Pesquisar
        pesquisar();
    });

    // ============================================
    // Toggle Filtros (Minimizar/Expandir)
    // ============================================
    function toggleFilters() {
        const section = document.getElementById('filtersSection');
        const body = document.getElementById('filtersBody');
        const summary = document.getElementById('filtersSummary');
        const icon = document.getElementById('iconToggleFilters');
        const btn = document.getElementById('btnToggleFilters');

        const isCollapsed = section.classList.toggle('collapsed');

        if (isCollapsed) {
            icon.setAttribute('name', 'chevron-down-outline');
            btn.title = 'Expandir filtros';
            atualizarResumoFiltros();
        } else {
            icon.setAttribute('name', 'chevron-up-outline');
            btn.title = 'Recolher filtros';
        }

        // Salvar estado
        localStorage.setItem(STORAGE_KEY_FILTERS_COLLAPSED, isCollapsed ? '1' : '0');
    }

    function restaurarEstadoFiltros() {
        const collapsed = localStorage.getItem(STORAGE_KEY_FILTERS_COLLAPSED);
        if (collapsed === '1') {
            const section = document.getElementById('filtersSection');
            const icon = document.getElementById('iconToggleFilters');
            const btn = document.getElementById('btnToggleFilters');

            section.classList.add('collapsed');
            icon.setAttribute('name', 'chevron-down-outline');
            btn.title = 'Expandir filtros';
            atualizarResumoFiltros();
        }
    }

    function atualizarResumoFiltros() {
        const filtrosAtivos = [];
        
        const titulo = document.getElementById('tituloLicitacao').value.trim();
        if (titulo) filtrosAtivos.push('Busca: "' + titulo + '"');

        const tipoText = $('#tipoLicitacao option:selected').text();
        const tipoVal = $('#tipoLicitacao').val();
        if (tipoVal) filtrosAtivos.push('Tipo: ' + tipoText);

        const status = $('input[name="statusLicitacao"]:checked').val();
        if (status) filtrosAtivos.push('Situação: ' + status);
        else filtrosAtivos.push('Situação: Todos');

        const dtIni = document.getElementById('dtIniLicitacao').value;
        if (dtIni) filtrosAtivos.push('De: ' + formatarDataSimples(dtIni));

        const dtFim = document.getElementById('dtFimLicitacao').value;
        if (dtFim) filtrosAtivos.push('Até: ' + formatarDataSimples(dtFim));

        const summaryText = document.getElementById('filtersSummaryText');
        if (filtrosAtivos.length > 0) {
            summaryText.innerHTML = '<ion-icon name="funnel-outline"></ion-icon> ' + filtrosAtivos.join(' <span class="summary-separator">•</span> ');
        } else {
            summaryText.innerHTML = '<ion-icon name="funnel-outline"></ion-icon> Nenhum filtro ativo';
        }
    }

    function formatarDataSimples(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    // ============================================
    // Debounce
    // ============================================
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ============================================
    // Detectar Mobile
    // ============================================
    function isMobile() {
        return window.innerWidth <= 768;
    }

    // ============================================
    // Toast Notifications
    // ============================================
    function showToast(mensagem, tipo = 'info') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = 'toast ' + tipo;

        const icons = {
            sucesso: 'checkmark-circle',
            erro: 'close-circle',
            alerta: 'alert-circle',
            info: 'information-circle'
        };

        toast.innerHTML = `
            <div class="toast-icon"><ion-icon name="${icons[tipo] || icons.info}"></ion-icon></div>
            <div class="toast-content"><p class="toast-message">${mensagem}</p></div>
            <button class="toast-close" onclick="this.parentElement.remove()"><ion-icon name="close-outline"></ion-icon></button>
        `;

        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ============================================
    // Salvar / Restaurar Filtros
    // ============================================
    function salvarFiltros() {
        const state = {
            titulo: document.getElementById('tituloLicitacao').value,
            tipo: $('#tipoLicitacao').val(),
            status: $('input[name="statusLicitacao"]:checked').val(),
            dataInicial: document.getElementById('dtIniLicitacao').value,
            dataFinal: document.getElementById('dtFimLicitacao').value,
            pagina: paginaAtualLic
        };
        try {
            localStorage.setItem(STORAGE_KEY_FILTERS, JSON.stringify(state));
        } catch (e) {}

        // Atualizar resumo se filtros estão recolhidos
        if (document.getElementById('filtersSection').classList.contains('collapsed')) {
            atualizarResumoFiltros();
        }
    }

    function restaurarFiltros() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY_FILTERS);
            if (!saved) return;
            const state = JSON.parse(saved);
            if (state.titulo) document.getElementById('tituloLicitacao').value = state.titulo;
            if (state.tipo) {
                $('#tipoLicitacao').val(state.tipo).trigger('change');
            }
            if (state.status !== undefined) {
                $('input[name="statusLicitacao"][value="' + state.status + '"]').prop('checked', true);
            }
            if (state.dataInicial) document.getElementById('dtIniLicitacao').value = state.dataInicial;
            if (state.dataFinal) document.getElementById('dtFimLicitacao').value = state.dataFinal;
            if (state.pagina) paginaAtualLic = parseInt(state.pagina);
        } catch (e) {
            console.error('Erro ao restaurar filtros:', e);
        }
    }

    // ============================================
    // Limpar Filtros
    // ============================================
    function limparFiltros() {
        document.getElementById('tituloLicitacao').value = '';
        $('#tipoLicitacao').val('').trigger('change');
        $('input[name="statusLicitacao"][value="Em Andamento"]').prop('checked', true);
        document.getElementById('dtIniLicitacao').value = '';
        document.getElementById('dtFimLicitacao').value = '';
        paginaAtualLic = 1;
        localStorage.removeItem(STORAGE_KEY_FILTERS);
        pesquisar();

        // Atualizar resumo
        if (document.getElementById('filtersSection').classList.contains('collapsed')) {
            atualizarResumoFiltros();
        }
    }

    // ============================================
    // Toggle View (Cards/Tabela)
    // ============================================
    function toggleView(view) {
        if (isMobile()) view = 'cards';

        const cardsView = document.getElementById('viewCards');
        const tableView = document.getElementById('viewTable');
        const btnCards = document.getElementById('btnViewCards');
        const btnTable = document.getElementById('btnViewTable');

        if (view === 'cards') {
            cardsView.classList.add('active');
            tableView.classList.remove('active');
            btnCards.classList.add('active');
            btnTable.classList.remove('active');
        } else {
            cardsView.classList.remove('active');
            tableView.classList.add('active');
            btnCards.classList.remove('active');
            btnTable.classList.add('active');
        }

        if (!isMobile()) {
            localStorage.setItem(STORAGE_KEY_VIEW, view);
        }
    }

    // ============================================
    // Pesquisar (AJAX)
    // ============================================
    function pesquisar() {
        mostrarLoading(true);

        const formData = new FormData();
        formData.append('tituloLicitacao', document.getElementById('tituloLicitacao').value);
        formData.append('tipoLicitacao', $('#tipoLicitacao').val() || 'vazio');
        formData.append('statusLicitacao', $('input[name="statusLicitacao"]:checked').val() || 'vazio');
        formData.append('dtIniLicitacao', document.getElementById('dtIniLicitacao').value);
        formData.append('dtFimLicitacao', document.getElementById('dtFimLicitacao').value);
        formData.append('pagina', paginaAtualLic);
        formData.append('limite', registrosPorPagina);

        fetch('bd/licitacao/readAjax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                mostrarLoading(false);

                if (data.success) {
                    totalRegistros = data.total;
                    renderizarCards(data.data);
                    renderizarTabela(data.data);
                    atualizarPaginacao();
                    document.getElementById('totalRegistros').textContent = totalRegistros;

                    // Atualizar estatísticas do header
                    if (data.stats) {
                        atualizarEstatisticasHeaderDireto(
                            data.stats.total_geral,
                            data.stats.total_andamento,
                            data.stats.total_encerrado,
                            data.stats.total_suspenso
                        );
                    }
                } else {
                    showToast('Erro ao carregar dados: ' + data.message, 'erro');
                }
            })
            .catch(error => {
                mostrarLoading(false);
                console.error('Erro:', error);
                showToast('Erro ao carregar dados', 'erro');
            });
    }

    // ============================================
    // Função auxiliar para truncar texto
    // ============================================
    function truncarTexto(texto, limite) {
        if (!texto) return '-';
        return texto.length > limite ? texto.substring(0, limite) + '...' : texto;
    }

    // ============================================
    // Função auxiliar para gerar o ícone de notificação
    // ============================================
    function getNotificationIcon(item) {
        if (!item.ENVIO_ATUALIZACAO_LICITACAO || item.ENVIO_ATUALIZACAO_LICITACAO == 0) {
            return '';
        }

        if (item.USUARIO_INSCRITO && item.USUARIO_INSCRITO > 0) {
            return `
            <span class="notification-indicator subscribed" title="Você receberá notificações desta licitação">
                <ion-icon name="notifications"></ion-icon>
            </span>
        `;
        }

        return `
        <span class="notification-indicator available" title="Notificações disponíveis">
            <ion-icon name="notifications-outline"></ion-icon>
        </span>
    `;
    }

    // ============================================
    // Função auxiliar para gerar o botão de notificação
    // ============================================
    function getNotificationButton(item) {
        if (!item.ENVIO_ATUALIZACAO_LICITACAO || item.ENVIO_ATUALIZACAO_LICITACAO == 0) {
            return '<span></span>';
        }

        if (item.USUARIO_INSCRITO && item.USUARIO_INSCRITO > 0) {
            return `
            <button type="button" 
                    class="btn-acao notificacao subscribed" 
                    title="Você está recebendo notificações - Clique para cancelar"
                    onclick="toggleNotificacao(${item.ID_LICITACAO}, 'cancelar', this)">
                <ion-icon name="notifications"></ion-icon>
            </button>
        `;
        }

        return `
        <button type="button" 
                class="btn-acao notificacao available" 
                title="Clique para receber notificações"
                onclick="toggleNotificacao(${item.ID_LICITACAO}, 'inscrever', this)">
            <ion-icon name="notifications-outline"></ion-icon>
        </button>
    `;
    }

    // ============================================
    // Função para toggle de notificação via AJAX
    // ============================================
    function toggleNotificacao(idLicitacao, acao, button) {
        button.disabled = true;
        button.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('idLicitacao', idLicitacao);
        formData.append('acao', acao);

        fetch('bd/licitacao/toggleNotificacao.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                button.disabled = false;
                button.style.opacity = '1';

                if (data.success) {
                    if (data.inscrito) {
                        button.classList.remove('available');
                        button.classList.add('subscribed');
                        button.title = 'Você está recebendo notificações - Clique para cancelar';
                        button.onclick = function() {
                            toggleNotificacao(idLicitacao, 'cancelar', this);
                        };
                        button.innerHTML = '<ion-icon name="notifications"></ion-icon>';
                    } else {
                        button.classList.remove('subscribed');
                        button.classList.add('available');
                        button.title = 'Clique para receber notificações';
                        button.onclick = function() {
                            toggleNotificacao(idLicitacao, 'inscrever', this);
                        };
                        button.innerHTML = '<ion-icon name="notifications-outline"></ion-icon>';
                    }

                    showToast(data.message, 'sucesso');
                } else {
                    showToast(data.message || 'Erro ao processar solicitação', 'erro');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.style.opacity = '1';
                console.error('Erro:', error);
                showToast('Erro ao processar solicitação', 'erro');
            });
    }

    // ============================================
    // Renderização de Cards
    // ============================================
    function renderizarCards(dados) {
        const container = document.getElementById('cardsContainer');

        if (!dados || dados.length === 0) {
            container.innerHTML = `
            <div class="empty-state" style="grid-column: 1 / -1;">
                <ion-icon name="document-outline"></ion-icon>
                <h3>Nenhuma licitação encontrada</h3>
                <p>Ajuste os filtros ou cadastre uma nova licitação.</p>
            </div>
        `;
            return;
        }

        let html = '';
        dados.forEach(function(item) {
            const statusClass = getStatusClass(item.STATUS_LICITACAO);
            const tipoClass = getTipoClass(item.NM_TIPO);
            const tipoAbrev = getTipoAbreviado(item.NM_TIPO);
            const dataAbertura = item.DT_ABER_LICITACAO ? formatarDataHora(item.DT_ABER_LICITACAO) : '-';
            const codigo = (item.SGL_TIPO || '') + ' ' + (item.COD_LICITACAO || '');
            const objeto = item.OBJETO_LICITACAO ? truncarTexto(item.OBJETO_LICITACAO, 100) : '-';
            const notificationButton = getNotificationButton(item);

            html += `
            <div class="licitacao-card" data-id="${item.ID_LICITACAO}" onclick="abrirVisualizacao(${item.ID_LICITACAO}, event)">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-icon">
                            <ion-icon name="document-text-outline"></ion-icon>
                        </div>
                        <div class="card-title-group">
                            <h3 class="card-title">${codigo}</h3>
                        </div>
                    </div>
                    <span class="badge ${statusClass}">${item.STATUS_LICITACAO || '-'}</span>
                </div>
                <div class="card-body">
                    <div class="card-info-row">
                        <ion-icon name="document-outline"></ion-icon>
                        <span>${objeto}</span>
                    </div>
                    <div class="card-info-row">
                        <ion-icon name="calendar-outline"></ion-icon>
                        <span class="card-info-label">Abertura:</span>
                        <span>${dataAbertura}</span>
                    </div>
                    <div class="card-info-row">
                        <ion-icon name="person-outline"></ion-icon>
                        <span class="card-info-label">Responsável:</span>
                        <span>${item.PREG_RESP_LICITACAO || '-'}</span>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="badge badge-tipo ${tipoClass}" title="${item.NM_TIPO || ''}">${tipoAbrev}</span>
                    <div class="card-footer-actions">
                        ${notificationButton}
                        <a href="licitacaoView.php?idLicitacao=${item.ID_LICITACAO}" class="btn-acao visualizar" title="Visualizar">
                            <ion-icon name="eye-outline"></ion-icon>
                        </a>
                        ${podeEditar && item.STATUS_LICITACAO !== 'Encerrado' ?
                            `<a href="licitacaoForm.php?idLicitacao=${item.ID_LICITACAO}" class="btn-acao editar" title="Editar">
                                <ion-icon name="create-outline"></ion-icon>
                            </a>` : ''}
                    </div>
                </div>
            </div>
        `;
        });

        container.innerHTML = html;
    }

    // ============================================
    // Renderização da Tabela
    // ============================================
    function renderizarTabela(dados) {
        const tbody = document.getElementById('tabelaBody');

        if (!dados || dados.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <ion-icon name="document-outline"></ion-icon>
                        <h3>Nenhuma licitação encontrada</h3>
                        <p>Ajuste os filtros ou cadastre uma nova licitação.</p>
                    </div>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';
        dados.forEach(function(item) {
            const statusClass = getStatusClass(item.STATUS_LICITACAO);
            const rowClass = getRowClass(item.STATUS_LICITACAO);
            const tipoClass = getTipoClass(item.NM_TIPO);
            const tipoAbrev = getTipoAbreviado(item.NM_TIPO);
            const dataAbertura = item.DT_ABER_LICITACAO ? formatarDataHora(item.DT_ABER_LICITACAO) : '-';
            const codigo = (item.SGL_TIPO || '') + ' ' + (item.COD_LICITACAO || '');
            const objeto = item.OBJETO_LICITACAO ? truncarTexto(item.OBJETO_LICITACAO, 80) : '-';
            const notificationButton = getNotificationButton(item);

            html += `
            <tr class="${rowClass}" data-id="${item.ID_LICITACAO}" onclick="abrirVisualizacao(${item.ID_LICITACAO}, event)">
                <td><strong>${codigo}</strong></td>
                <td><span class="badge badge-tipo ${tipoClass}" title="${item.NM_TIPO || ''}">${tipoAbrev}</span></td>
                <td title="${item.OBJETO_LICITACAO || ''}">${objeto}</td>
                <td><span class="badge ${statusClass}">${item.STATUS_LICITACAO || '-'}</span></td>
                <td>${dataAbertura}</td>
                <td>${item.PREG_RESP_LICITACAO || '-'}</td>
                <td>
                    <div class="acoes-cell">
                        ${notificationButton}
                        <a href="licitacaoView.php?idLicitacao=${item.ID_LICITACAO}" class="btn-acao visualizar" title="Visualizar">
                            <ion-icon name="eye-outline"></ion-icon>
                        </a>
                        ${podeEditar && item.STATUS_LICITACAO !== 'Encerrado' ?
                            `<a href="licitacaoForm.php?idLicitacao=${item.ID_LICITACAO}" class="btn-acao editar" title="Editar">
                                <ion-icon name="create-outline"></ion-icon>
                            </a>` : '<span></span>'}
                    </div>
                </td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    // ============================================
    // Abrir Visualização ao Clicar na Linha
    // ============================================
    function abrirVisualizacao(idLicitacao, event) {
        // Não abrir se clicar em um botão ou link dentro da célula de ações
        if (event.target.closest('.acoes-cell') || event.target.closest('.btn-acao')) {
            return;
        }
        window.location.href = `licitacaoView.php?idLicitacao=${idLicitacao}`;
    }

    // ============================================
    // Paginação
    // ============================================
    function atualizarPaginacao() {
        const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);
        const inicio = (paginaAtualLic - 1) * registrosPorPagina + 1;
        const fim = Math.min(paginaAtualLic * registrosPorPagina, totalRegistros);

        // Atualizar paginação da Tabela
        document.getElementById('paginacaoInicio').textContent = totalRegistros > 0 ? inicio : 0;
        document.getElementById('paginacaoFim').textContent = fim;
        document.getElementById('paginacaoTotal').textContent = totalRegistros;

        // Atualizar paginação dos Cards
        document.getElementById('paginacaoInicioCards').textContent = totalRegistros > 0 ? inicio : 0;
        document.getElementById('paginacaoFimCards').textContent = fim;
        document.getElementById('paginacaoTotalCards').textContent = totalRegistros;

        // Gerar botões de paginação
        let html = '';

        html += `<button onclick="irParaPagina(${paginaAtualLic - 1})" ${paginaAtualLic === 1 ? 'disabled' : ''}>
            <ion-icon name="chevron-back-outline"></ion-icon></button>`;

        const maxPaginas = 5;
        let startPage = Math.max(1, paginaAtualLic - Math.floor(maxPaginas / 2));
        let endPage = Math.min(totalPaginas, startPage + maxPaginas - 1);

        if (endPage - startPage < maxPaginas - 1) {
            startPage = Math.max(1, endPage - maxPaginas + 1);
        }

        if (startPage > 1) {
            html += `<button onclick="irParaPagina(1)">1</button>`;
            if (startPage > 2) html += `<button disabled>...</button>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<button onclick="irParaPagina(${i})" class="${i === paginaAtualLic ? 'active' : ''}">${i}</button>`;
        }

        if (endPage < totalPaginas) {
            if (endPage < totalPaginas - 1) html += `<button disabled>...</button>`;
            html += `<button onclick="irParaPagina(${totalPaginas})">${totalPaginas}</button>`;
        }

        html += `<button onclick="irParaPagina(${paginaAtualLic + 1})" ${paginaAtualLic === totalPaginas || totalPaginas === 0 ? 'disabled' : ''}>
            <ion-icon name="chevron-forward-outline"></ion-icon></button>`;

        // Aplicar em ambas as paginações
        document.getElementById('paginacao').innerHTML = html;
        document.getElementById('paginacaoCards').innerHTML = html;
    }

    function irParaPagina(pagina) {
        const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);
        if (pagina >= 1 && pagina <= totalPaginas) {
            paginaAtualLic = pagina;
            salvarFiltros();
            pesquisar();
        }
    }

    // ============================================
    // Utilitários
    // ============================================
    function mostrarLoading(show) {
        document.getElementById('loadingOverlay').classList.toggle('active', show);
    }

    function getStatusClass(status) {
        switch (status) {
            case 'Em Andamento': return 'badge-andamento';
            case 'Encerrado': return 'badge-encerrado';
            case 'Suspenso': return 'badge-suspenso';
            case 'Rascunho': return 'badge-rascunho';
            default: return 'badge-andamento';
        }
    }

    function getRowClass(status) {
        switch (status) {
            case 'Encerrado': return 'encerrado';
            case 'Suspenso': return 'suspenso';
            default: return '';
        }
    }

    function getTipoClass(tipo) {
        if (!tipo) return 'default';
        const tipoLower = tipo.toLowerCase();
        if (tipoLower.includes('pregão') || tipoLower.includes('pregao')) return 'pregao';
        if (tipoLower.includes('licitação') || tipoLower.includes('licitacao')) return 'licitacao';
        if (tipoLower.includes('dispensa')) return 'dispensa';
        if (tipoLower.includes('concorrência') || tipoLower.includes('concorrencia')) return 'concorrencia';
        if (tipoLower.includes('credenciamento')) return 'credenciamento';
        if (tipoLower.includes('leilão') || tipoLower.includes('leilao')) return 'leilao';
        return 'default';
    }

    function getTipoAbreviado(tipo) {
        if (!tipo) return '-';
        const tipoLower = tipo.toLowerCase();
        if (tipoLower.includes('pregão eletrônico') || tipoLower.includes('pregao eletronico')) return 'PREGÃO ELETR.';
        if (tipoLower.includes('licitação cesan')) return 'LICIT. CESAN';
        if (tipoLower.includes('licitação internacional')) return 'LICIT. INTERN.';
        if (tipoLower.includes('dispensa eletrônica')) return 'DISPENSA ELETR.';
        if (tipoLower.includes('dispensa')) return 'DISPENSA';
        if (tipoLower.includes('concorrência')) return 'CONCORRÊNCIA';
        if (tipoLower.includes('credenciamento')) return 'CREDENC.';
        if (tipoLower.includes('leilão')) return 'LEILÃO';
        return tipo.length > 18 ? tipo.substring(0, 18) + '.' : tipo;
    }

    function formatarDataHora(data) {
        if (!data) return '-';
        try {
            const d = new Date(data);
            return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            return data;
        }
    }

    // ============================================
    // Header: Data e Estatísticas
    // ============================================
    function atualizarDataHeader() {
        const el = document.getElementById('headerDate');
        if (!el) return;
        const agora = new Date();
        const opcoes = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
        const dataFormatada = agora.toLocaleDateString('pt-BR', opcoes);
        el.textContent = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);
    }

    function atualizarEstatisticasHeaderDireto(total, andamento, encerrado, suspenso) {
        animarNumero('statTotalNum', total || 0);
        animarNumero('statAndamentoNum', andamento || 0);
        animarNumero('statEncerradoNum', encerrado || 0);
        animarNumero('statSuspensoNum', suspenso || 0);
    }

    function animarNumero(elementId, valorFinal) {
        const el = document.getElementById(elementId);
        if (!el) return;

        const textoAtual = el.textContent;
        const valorAtual = parseInt(textoAtual.replace(/\D/g, '')) || 0;

        if (valorAtual === valorFinal) return;

        const duracao = 600;
        const inicio = performance.now();

        function animar(timestamp) {
            const progresso = Math.min((timestamp - inicio) / duracao, 1);
            const eased = 1 - Math.pow(1 - progresso, 3);
            const valor = Math.round(valorAtual + (valorFinal - valorAtual) * eased);
            el.textContent = valor.toLocaleString('pt-BR');

            if (progresso < 1) {
                requestAnimationFrame(animar);
            } else {
                el.classList.add('loaded');
            }
        }

        requestAnimationFrame(animar);
    }
</script>

<?php include_once 'includes/footer.inc.php'; ?>