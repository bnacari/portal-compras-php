<?php
/**
 * Portal de Compras - CESAN
 * Tela de Consulta de Licitações
 * 
 * Layout refatorado no estilo calculoKPC
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
    $sqlTipos = $pdoCAT->query("SELECT ID_TIPO, NM_TIPO, SGL_TIPO FROM [portalcompras].[dbo].[TIPO_LICITACAO] WHERE DT_EXC_TIPO IS NULL ORDER BY NM_TIPO");
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
    <!-- Header da Página -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-header-info">
                <div class="page-header-icon">
                    <ion-icon name="document-text-outline"></ion-icon>
                </div>
                <div>
                    <h1>Consulta de Licitações</h1>
                    <p class="page-header-subtitle">Pesquise e gerencie os processos licitatórios</p>
                </div>
            </div>
            <div class="page-header-actions">
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
                    <a href="licitacaoForm.php" class="btn-novo">
                        <ion-icon name="add-outline"></ion-icon>
                        Nova Licitação
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <div class="filters-header">
            <div class="filters-title">
                <ion-icon name="filter-outline"></ion-icon>
                Filtros de Pesquisa
            </div>
            <button type="button" class="btn-clear-filters" onclick="limparFiltros()">
                <ion-icon name="refresh-outline"></ion-icon>
                Limpar Filtros
            </button>
        </div>

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
    const registrosPorPagina = 20;
    let totalRegistros = 0;
    let searchTimeout;
    const podeEditar = <?= $podeEditar ? 'true' : 'false' ?>;
    const idUsuario = <?= $_SESSION['idUsuario'] ?? 0 ?>;
    const emailUsuario = '<?= $_SESSION['email'] ?? '' ?>';
    
    // Chave para localStorage
    const STORAGE_KEY_VIEW = 'consultarLicitacao_view';
    const STORAGE_KEY_FILTROS = 'consultarLicitacao_filtros';

    // ============================================
    // Inicialização
    // ============================================
    $(document).ready(function () {
        // Inicializar Select2 para Tipo de Licitação
        $('#tipoLicitacao').select2({
            placeholder: 'Selecione o tipo...',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function () { return "Nenhum tipo encontrado"; },
                searching: function () { return "Buscando..."; }
            }
        });

        // Verificar se é mobile e forçar cards
        if (isMobile()) {
            toggleView('cards');
        } else {
            // Restaurar preferência de visualização apenas no desktop
            const savedView = localStorage.getItem(STORAGE_KEY_VIEW);
            if (savedView) {
                toggleView(savedView);
            }
        }

        // Restaurar filtros salvos
        restaurarFiltros();

        // ============================================
        // Eventos de Filtro Automático
        // ============================================
        
        // Tipo - filtra ao mudar
        $('#tipoLicitacao').on('change', function () {
            paginaAtualLic = 1;
            salvarFiltros();
            pesquisar();
        });

        // Radio buttons - Status
        $('input[name="statusLicitacao"]').on('change', function () {
            paginaAtualLic = 1;
            salvarFiltros();
            pesquisar();
        });

        // Campos de texto com debounce
        $('#tituloLicitacao').on('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                paginaAtualLic = 1;
                salvarFiltros();
                pesquisar();
            }, 500);
        });

        // Campos de data
        $('#dtIniLicitacao, #dtFimLicitacao').on('change', function () {
            paginaAtualLic = 1;
            salvarFiltros();
            pesquisar();
        });

        // Carrega dados iniciais
        pesquisar();
    });

    // ============================================
    // Detectar Mobile
    // ============================================
    function isMobile() {
        return window.innerWidth <= 768;
    }

    // ============================================
    // Persistência de Filtros
    // ============================================
    function salvarFiltros() {
        const state = {
            titulo: document.getElementById('tituloLicitacao').value || '',
            tipo: $('#tipoLicitacao').val() || '',
            status: $('input[name="statusLicitacao"]:checked').val() || '',
            dataInicial: document.getElementById('dtIniLicitacao').value || '',
            dataFinal: document.getElementById('dtFimLicitacao').value || '',
            pagina: paginaAtualLic
        };
        localStorage.setItem(STORAGE_KEY_FILTROS, JSON.stringify(state));
    }

    function restaurarFiltros() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY_FILTROS);
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
    // Toggle View (Cards/Tabela)
    // ============================================
    function toggleView(view) {
        // No mobile, sempre força cards
        if (isMobile()) {
            view = 'cards';
        }

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

        // Só salva preferência no desktop
        if (!isMobile()) {
            localStorage.setItem(STORAGE_KEY_VIEW, view);
        }
    }

    // ============================================
    // Função Limpar Filtros
    // ============================================
    function limparFiltros() {
        document.getElementById('tituloLicitacao').value = '';
        $('#tipoLicitacao').val('').trigger('change');
        $('input[name="statusLicitacao"][value="Em Andamento"]').prop('checked', true);
        document.getElementById('dtIniLicitacao').value = '';
        document.getElementById('dtFimLicitacao').value = '';
        
        paginaAtualLic = 1;
        localStorage.removeItem(STORAGE_KEY_FILTROS);
        pesquisar();
    }

    // ============================================
    // Função Principal de Pesquisa (AJAX)
    // ============================================
    function pesquisar() {
        mostrarLoading(true);

        const formData = new FormData();
        formData.append('tituloLicitacao', document.getElementById('tituloLicitacao').value);
        formData.append('tipoLicitacao', $('#tipoLicitacao').val() || 'vazio');
        formData.append('statusLicitacao', $('input[name="statusLicitacao"]:checked').val() || 'Em Andamento');
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
        dados.forEach(function (item) {
            const statusClass = getStatusClass(item.STATUS_LICITACAO);
            const tipoClass = getTipoClass(item.NM_TIPO);
            const tipoAbrev = getTipoAbreviado(item.NM_TIPO);
            const dataAbertura = item.DT_ABER_LICITACAO ? formatarDataHora(item.DT_ABER_LICITACAO) : '-';
            const codigo = (item.SGL_TIPO || '') + ' ' + (item.COD_LICITACAO || '');
            const objeto = item.OBJETO_LICITACAO ? truncarTexto(item.OBJETO_LICITACAO, 100) : '-';

            html += `
                <div class="licitacao-card">
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
                            <a href="viewLicitacao.php?idLicitacao=${item.ID_LICITACAO}" class="btn-acao visualizar" title="Visualizar">
                                <ion-icon name="eye-outline"></ion-icon>
                            </a>
                            ${podeEditar && item.STATUS_LICITACAO !== 'Encerrado' ? `
                            <a href="licitacaoForm.php?idLicitacao=${item.ID_LICITACAO}" class="btn-acao editar" title="Editar">
                                <ion-icon name="create-outline"></ion-icon>
                            </a>
                            ` : ''}
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
        dados.forEach(function (item) {
            const statusClass = getStatusClass(item.STATUS_LICITACAO);
            const rowClass = getRowClass(item.STATUS_LICITACAO);
            const tipoClass = getTipoClass(item.NM_TIPO);
            const tipoAbrev = getTipoAbreviado(item.NM_TIPO);
            const dataAbertura = item.DT_ABER_LICITACAO ? formatarDataHora(item.DT_ABER_LICITACAO) : '-';
            const codigo = (item.SGL_TIPO || '') + ' ' + (item.COD_LICITACAO || '');
            const objeto = item.OBJETO_LICITACAO ? truncarTexto(item.OBJETO_LICITACAO, 80) : '-';

            html += `
                <tr class="${rowClass}">
                    <td><strong>${codigo}</strong></td>
                    <td><span class="badge badge-tipo ${tipoClass}" title="${item.NM_TIPO || ''}">${tipoAbrev}</span></td>
                    <td title="${item.OBJETO_LICITACAO || ''}">${objeto}</td>
                    <td><span class="badge ${statusClass}">${item.STATUS_LICITACAO || '-'}</span></td>
                    <td>${dataAbertura}</td>
                    <td>${item.PREG_RESP_LICITACAO || '-'}</td>
                    <td>
                        <div class="acoes-cell">
                            <a href="viewLicitacao.php?idLicitacao=${item.ID_LICITACAO}" class="btn-acao visualizar" title="Visualizar">
                                <ion-icon name="eye-outline"></ion-icon>
                            </a>
                            ${podeEditar && item.STATUS_LICITACAO !== 'Encerrado' ? `
                            <a href="licitacaoForm.php?idLicitacao=${item.ID_LICITACAO}" class="btn-acao editar" title="Editar">
                                <ion-icon name="create-outline"></ion-icon>
                            </a>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // ============================================
    // Paginação
    // ============================================
    function atualizarPaginacao() {
        const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);
        const inicio = (paginaAtualLic - 1) * registrosPorPagina + 1;
        const fim = Math.min(paginaAtualLic * registrosPorPagina, totalRegistros);

        document.getElementById('paginacaoInicio').textContent = totalRegistros > 0 ? inicio : 0;
        document.getElementById('paginacaoFim').textContent = fim;
        document.getElementById('paginacaoTotal').textContent = totalRegistros;

        const paginacao = document.getElementById('paginacao');
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

        paginacao.innerHTML = html;
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
        if (tipoLower.includes('concorrência internacional')) return 'CONCOR. INTERN.';
        if (tipoLower.includes('procedimento de manifestação')) return 'PROC. MANIF.';
        if (tipoLower.includes('request of proposal')) return 'RFP';
        
        // Retorna abreviado se for muito longo
        if (tipo.length > 18) {
            return tipo.substring(0, 15) + '...';
        }
        return tipo;
    }

    function formatarDataHora(dataStr) {
        if (!dataStr) return '-';
        const data = new Date(dataStr);
        return data.toLocaleDateString('pt-BR') + ' ' + data.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    function truncarTexto(texto, maxLength) {
        if (texto.length <= maxLength) return texto;
        return texto.substring(0, maxLength) + '...';
    }

    // ============================================
    // Toast System
    // ============================================
    function showToast(message, type, duration) {
        type = type || 'info';
        duration = duration || 5000;

        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const icons = {
            sucesso: 'checkmark-circle',
            erro: 'close-circle',
            alerta: 'alert-circle',
            info: 'information-circle'
        };

        const toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML = `
            <div class="toast-icon">
                <ion-icon name="${icons[type] || icons.info}"></ion-icon>
            </div>
            <div class="toast-content">
                <p class="toast-message">${message}</p>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <ion-icon name="close"></ion-icon>
            </button>
        `;

        container.appendChild(toast);
        setTimeout(function() { toast.classList.add('show'); }, 10);
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() { toast.remove(); }, 300);
        }, duration);
    }
</script>

<?php include_once 'includes/footer.inc.php'; ?>