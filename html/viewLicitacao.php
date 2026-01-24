<?php
/**
 * ============================================
 * Portal de Compras - CESAN
 * Tela de Visualização de Licitação
 * 
 * Layout refatorado baseado em consultarLicitacao.php
 * ============================================
 */

// Includes necessários
include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';
include_once 'redirecionar.php';

// Obtém o ID da licitação via GET
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

// Query principal para buscar dados da licitação
$querySelect2 = "SELECT TIPO.SGL_TIPO AS SGL_TIPO, L.*, DET.COD_LICITACAO AS COD_LIC, DET.*
                    FROM [PortalCompras].[dbo].[LICITACAO] L
                    LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN ANEXO A ON A.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN TIPO_LICITACAO TIPO ON TIPO.ID_TIPO = DET.TIPO_LICITACAO
                    WHERE L.ID_LICITACAO = $idLicitacao
                ";

$querySelect = $pdoCAT->query($querySelect2);

// Extrai os dados da licitação
while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
    $idLicitacao = $registros['ID_LICITACAO'];
    $dtLicitacao = $registros['DT_LICITACAO'];
    $codLicitacao = $registros['SGL_TIPO'] . ' ' . $registros['COD_LIC'];
    $statusLicitacao = $registros['STATUS_LICITACAO'];
    $objLicitacao = $registros['OBJETO_LICITACAO'];
    $respLicitacao = $registros['PREG_RESP_LICITACAO'];
    $dtAberLicitacao = date('d/m/Y H:i', strtotime($registros['DT_ABER_LICITACAO']));
    $dtIniSessLicitacao = date('d/m/Y H:i', strtotime($registros['DT_INI_SESS_LICITACAO']));
    $modoLicitacao = $registros['MODO_LICITACAO'];
    $criterioLicitacao = $registros['CRITERIO_LICITACAO'];
    $tipoLicitacao = $registros['TIPO_LICITACAO'];
    $regimeLicitacao = $registros['REGIME_LICITACAO'];
    $formaLicitacao = $registros['FORMA_LICITACAO'];
    $vlLicitacao = $registros['VL_LICITACAO'];
    $localLicitacao = $registros['LOCAL_ABER_LICITACAO'];
    $identificadorLicitacao = $registros['IDENTIFICADOR_LICITACAO'];
    $obsLicitacao = $registros['OBS_LICITACAO'];
    $dtExcLicitacao = $registros['DT_EXC_LICITACAO'];
endwhile;

// Evita acesso de usuários não logados a licitações com status "Rascunho" ou excluídas
if (($_SESSION['sucesso'] != 1 && $statusLicitacao == 'Rascunho') || ($_SESSION['sucesso'] != 1 && $dtExcLicitacao != null)) {
    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
}

// Busca nome do tipo de licitação
if (isset($tipoLicitacao)) {
    $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[TIPO_LICITACAO] WHERE ID_TIPO = $tipoLicitacao";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
        $idTipo = $registros['ID_TIPO'];
        $nmTipo = $registros['NM_TIPO'];
    endwhile;

    $tituloLicitacao = $nmTipo . ' - ' . $codLicitacao;
} else {
    $tituloLicitacao = $codLicitacao;
}

// Busca nome do critério
$nmCriterio = '';
if (isset($criterioLicitacao) && $criterioLicitacao != '0') {
    $queryCriterio = $pdoCAT->query("SELECT NM_CRITERIO FROM [portalcompras].[dbo].[CRITERIO_LICITACAO] WHERE ID_CRITERIO = $criterioLicitacao");
    $regCriterio = $queryCriterio->fetch(PDO::FETCH_ASSOC);
    $nmCriterio = $regCriterio['NM_CRITERIO'] ?? '';
}

// Busca nome da forma
$nmForma = '';
if (isset($formaLicitacao) && $formaLicitacao != 0) {
    $queryForma = $pdoCAT->query("SELECT NM_FORMA FROM [portalcompras].[dbo].[FORMA] WHERE ID_FORMA = $formaLicitacao");
    $regForma = $queryForma->fetch(PDO::FETCH_ASSOC);
    $nmForma = $regForma['NM_FORMA'] ?? '';
}

// Registra auditoria
$_SESSION['redirecionar'] = 'viewLicitacao.php?idLicitacao=' . $idLicitacao;
$login = $_SESSION['login'];
$tela = 'Licitação';
$acao = 'Visualizada';
$idEvento = $idLicitacao;
$queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
?>

<!-- CSS da página -->
<link rel="stylesheet" href="style/css/viewLicitacao.css" />

<div class="page-container">

    <!-- ============================================
         Header da Página - Estilo consultarLicitacao
         ============================================ -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-header-info">
                <div class="page-header-icon">
                    <ion-icon name="document-text-outline"></ion-icon>
                </div>
                <div>
                    <h1><?php echo htmlspecialchars($tituloLicitacao); ?></h1>
                    <div class="page-header-subtitle">
                        <p>Visualização detalhada da licitação</p>
                        <?php if (isset($statusLicitacao) && $statusLicitacao !== '') {
                            // Define classe CSS do status
                            $statusClass = '';
                            switch ($statusLicitacao) {
                                case 'Em Andamento':
                                    $statusClass = 'em-andamento';
                                    break;
                                case 'Suspenso':
                                    $statusClass = 'suspenso';
                                    break;
                                case 'Encerrado':
                                    $statusClass = 'encerrado';
                                    break;
                                case 'Rascunho':
                                    $statusClass = 'rascunho';
                                    break;
                            }
                        ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <span class="status-dot"></span>
                                <?php echo htmlspecialchars($statusLicitacao); ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         Dados Principais
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="document-text-outline"></ion-icon>
            <h2>Dados Principais</h2>
        </div>
        <div class="section-content">
            <div class="info-grid">
                <?php if (isset($nmTipo) && $nmTipo !== '') { ?>
                    <div class="info-group">
                        <label><ion-icon name="pricetag-outline"></ion-icon> Tipo de Contratação</label>
                        <div class="info-value"><?php echo htmlspecialchars($nmTipo); ?></div>
                    </div>
                <?php } ?>

                <?php if (isset($codLicitacao) && $codLicitacao !== '') { ?>
                    <div class="info-group">
                        <label><ion-icon name="barcode-outline"></ion-icon> Código</label>
                        <div class="info-value"><?php echo htmlspecialchars($codLicitacao); ?></div>
                    </div>
                <?php } ?>

                <?php if (isset($respLicitacao) && $respLicitacao !== '') { ?>
                    <div class="info-group">
                        <label><ion-icon name="person-outline"></ion-icon> Responsável</label>
                        <div class="info-value"><?php echo htmlspecialchars($respLicitacao); ?></div>
                    </div>
                <?php } ?>

                <?php if (isset($objLicitacao) && $objLicitacao !== '') { ?>
                    <div class="info-group col-3">
                        <label><ion-icon name="document-outline"></ion-icon> Objeto</label>
                        <div class="info-value textarea"><?php echo htmlspecialchars(trim($objLicitacao)); ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- ============================================
         Datas e Horários
         ============================================ -->
    <?php if ((isset($dtAberLicitacao) && !strpos($dtAberLicitacao, '/1969')) || (isset($dtIniSessLicitacao) && !strpos($dtIniSessLicitacao, '/1969'))) { ?>
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="calendar-outline"></ion-icon>
                <h2>Datas e Horários</h2>
            </div>
            <div class="section-content">
                <div class="info-grid">
                    <?php if (isset($dtAberLicitacao) && !strpos($dtAberLicitacao, '/1969')) { ?>
                        <div class="info-group">
                            <label><ion-icon name="time-outline"></ion-icon> Data e Horário de Abertura</label>
                            <div class="info-value"><?php echo $dtAberLicitacao; ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($dtIniSessLicitacao) && !strpos($dtIniSessLicitacao, '/1969')) { ?>
                        <div class="info-group">
                            <label><ion-icon name="play-circle-outline"></ion-icon> Início da Sessão de Disputa</label>
                            <div class="info-value"><?php echo $dtIniSessLicitacao; ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- ============================================
         Detalhes da Licitação
         ============================================ -->
    <?php
    $hasDetails = (isset($modoLicitacao) && $modoLicitacao != '0') ||
        (isset($criterioLicitacao) && $criterioLicitacao != '0') ||
        (isset($regimeLicitacao) && $regimeLicitacao !== '') ||
        (isset($formaLicitacao) && $formaLicitacao != 0) ||
        (isset($vlLicitacao) && $vlLicitacao !== '') ||
        (isset($identificadorLicitacao) && $identificadorLicitacao !== '') ||
        (isset($localLicitacao) && $localLicitacao !== '');

    if ($hasDetails) { ?>
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="list-outline"></ion-icon>
                <h2>Detalhes da Licitação</h2>
            </div>
            <div class="section-content">
                <div class="info-grid">
                    <?php if (isset($modoLicitacao) && $modoLicitacao != '0') { ?>
                        <div class="info-group">
                            <label><ion-icon name="options-outline"></ion-icon> Modo de Disputa</label>
                            <div class="info-value"><?php echo htmlspecialchars($modoLicitacao); ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($criterioLicitacao) && $criterioLicitacao != '0' && $nmCriterio !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="checkmark-circle-outline"></ion-icon> Critério de Julgamento</label>
                            <div class="info-value"><?php echo htmlspecialchars($nmCriterio); ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($regimeLicitacao) && $regimeLicitacao !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="construct-outline"></ion-icon> Regime de Execução</label>
                            <div class="info-value"><?php echo htmlspecialchars($regimeLicitacao); ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($formaLicitacao) && $formaLicitacao != 0 && $nmForma !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="layers-outline"></ion-icon> Forma</label>
                            <div class="info-value"><?php echo htmlspecialchars($nmForma); ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($vlLicitacao) && $vlLicitacao !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="cash-outline"></ion-icon> Valor Estimado</label>
                            <div class="info-value"><?php echo htmlspecialchars($vlLicitacao); ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($identificadorLicitacao) && $identificadorLicitacao !== '') { ?>
                        <div class="info-group">
                            <label><ion-icon name="finger-print-outline"></ion-icon> Identificador</label>
                            <div class="info-value"><?php echo htmlspecialchars($identificadorLicitacao); ?></div>
                        </div>
                    <?php } ?>

                    <?php if (isset($localLicitacao) && $localLicitacao !== '') { ?>
                        <div class="info-group col-3">
                            <label><ion-icon name="location-outline"></ion-icon> Local de Abertura</label>
                            <div class="info-value">
                                <a href="<?php echo htmlspecialchars($localLicitacao); ?>" target="_blank">
                                    <?php echo htmlspecialchars($localLicitacao); ?>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- ============================================
         Observação
         ============================================ -->
    <?php if (isset($obsLicitacao) && trim($obsLicitacao) !== '') { ?>
        <div class="section-card">
            <div class="section-header">
                <ion-icon name="chatbubble-outline"></ion-icon>
                <h2>Observação</h2>
            </div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-group col-3">
                        <div class="info-value textarea"><?php echo htmlspecialchars(trim($obsLicitacao)); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- ============================================
         Documentos Anexados
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="attach-outline"></ion-icon>
            <h2>Documentos Anexados</h2>
        </div>
        <div class="section-content">
            <?php
            // Diretório de uploads
            $directory = "uploads" . '/' . $idLicitacao;
            $isDirectory = is_dir($directory);
            $anexos = array();

            // Query de anexos conforme versão da licitação
            if ($idLicitacao > 2000) {
                // Licitações 13.303
                $queryAnexo = "WITH RankedAnexos AS (
                                    SELECT
                                        ID_LICITACAO,
                                        NM_ANEXO,
                                        LINK_ANEXO,
                                        ROW_NUMBER() OVER (PARTITION BY ID_LICITACAO, CASE WHEN NM_ANEXO LIKE '%_descricao' THEN 1 ELSE 2 END ORDER BY NM_ANEXO) AS rn
                                    FROM ANEXO
                                    WHERE ID_LICITACAO = $idLicitacao
                                    AND DT_EXC_ANEXO IS NULL
                                )
                                SELECT
                                    ID_LICITACAO,
                                    MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN LINK_ANEXO END) AS NM_ANEXO,
                                    MAX(CASE WHEN NM_ANEXO like '%_arquivo' THEN LINK_ANEXO END) AS LINK_ANEXO
                                FROM RankedAnexos
                                GROUP BY ID_LICITACAO, rn;";
            } else {
                $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO FROM ANEXO WHERE ID_LICITACAO = $idLicitacao AND DT_EXC_ANEXO IS NULL";
            }

            $queryAnexo2 = $pdoCAT->query($queryAnexo);

            // Anexos do banco de dados
            while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
                $anexos[] = array(
                    'nmAnexo' => $registros['NM_ANEXO'],
                    'linkAnexo' => $registros['LINK_ANEXO'],
                    'timestamp' => null
                );
            }

            // Anexos do diretório físico
            if ($isDirectory) {
                $files = scandir($directory);
                $files = array_diff($files, array('.', '..'));

                foreach ($files as $file) {
                    $anexos[] = array(
                        'nmAnexo' => $file,
                        'linkAnexo' => $directory . '/' . $file,
                        'timestamp' => filemtime($directory . '/' . $file),
                    );
                }
            }

            // Ordenação padrão por timestamp (mais recentes primeiro)
            usort($anexos, function ($a, $b) {
                $aTime = isset($a['timestamp']) ? $a['timestamp'] : 0;
                $bTime = isset($b['timestamp']) ? $b['timestamp'] : 0;
                return $bTime - $aTime;
            });

            // Renderiza anexos se existirem
            if (!empty($anexos)) {
                /**
                 * Função auxiliar para determinar ícone do arquivo
                 */
                function getFileIcon($filename)
                {
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $icons = [
                        'pdf' => ['icon' => 'document-text', 'class' => 'pdf'],
                        'doc' => ['icon' => 'document', 'class' => 'doc'],
                        'docx' => ['icon' => 'document', 'class' => 'doc'],
                        'xls' => ['icon' => 'grid', 'class' => 'xls'],
                        'xlsx' => ['icon' => 'grid', 'class' => 'xls'],
                        'zip' => ['icon' => 'archive', 'class' => 'zip'],
                        'rar' => ['icon' => 'archive', 'class' => 'zip'],
                        'jpg' => ['icon' => 'image', 'class' => 'img'],
                        'jpeg' => ['icon' => 'image', 'class' => 'img'],
                        'png' => ['icon' => 'image', 'class' => 'img'],
                        'txt' => ['icon' => 'document-text', 'class' => 'default'],
                    ];
                    return $icons[$ext] ?? ['icon' => 'document', 'class' => 'default'];
                }

                // Header com contador e toggle de visualização
                echo '<div class="files-header">';
                echo '<div class="files-count">';
                echo '<ion-icon name="folder-open-outline"></ion-icon>';
                echo '<span><strong>' . count($anexos) . '</strong> arquivo' . (count($anexos) > 1 ? 's' : '') . '</span>';
                echo '</div>';
                echo '<div class="files-view-toggle">';
                echo '<button class="files-view-btn active" data-view="grid" onclick="toggleFilesView(\'grid\')">';
                echo '<ion-icon name="grid-outline"></ion-icon>';
                echo '<span>Cards</span>';
                echo '</button>';
                echo '<button class="files-view-btn" data-view="list" onclick="toggleFilesView(\'list\')">';
                echo '<ion-icon name="list-outline"></ion-icon>';
                echo '<span>Lista</span>';
                echo '</button>';
                echo '</div>';
                echo '</div>';

                // GRID VIEW (Cards)
                echo '<div class="files-grid" id="filesGrid">';
                foreach ($anexos as $anexo) {
                    if (!empty($anexo['nmAnexo'])) {
                        $fileInfo = getFileIcon($anexo['nmAnexo']);
                        echo '<a href="' . htmlspecialchars($anexo['linkAnexo']) . '" target="_blank" class="file-card-link">';
                        echo '<div class="file-card" data-timestamp="' . ($anexo['timestamp'] ?? 0) . '" data-nome="' . htmlspecialchars($anexo['nmAnexo']) . '">';
                        echo '<div class="file-card-icon ' . $fileInfo['class'] . '">';
                        echo '<ion-icon name="' . $fileInfo['icon'] . '-outline"></ion-icon>';
                        echo '</div>';
                        echo '<div class="file-card-info">';
                        echo '<p class="file-card-name">' . htmlspecialchars($anexo['nmAnexo']) . '</p>';
                        echo '<div class="file-card-date">';
                        echo '<ion-icon name="calendar-outline"></ion-icon>';
                        if ($anexo['timestamp']) {
                            echo date("d/m/Y H:i", $anexo['timestamp']);
                        } else {
                            echo '<span style="color: #94a3b8;">Não disponível</span>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</a>';
                    }
                }
                echo '</div>';

                // LIST VIEW (Tabela)
                echo '<div class="files-list" id="filesList">';
                echo '<div class="file-table-wrapper">';
                echo '<table class="file-table" id="anexosTable">';
                echo '<thead>';
                echo '<tr>';
                echo '<th class="sortable" data-column="nome" data-order="asc">Arquivo</th>';
                echo '<th class="sortable" data-column="data" data-order="desc">Data Inclusão</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($anexos as $anexo) {
                    if (!empty($anexo['nmAnexo'])) {
                        echo '<tr data-timestamp="' . ($anexo['timestamp'] ?? 0) . '" data-nome="' . htmlspecialchars($anexo['nmAnexo']) . '">';
                        echo '<td>';
                        echo '<a href="' . htmlspecialchars($anexo['linkAnexo']) . '" target="_blank">';
                        echo '<ion-icon name="document-outline"></ion-icon> ';
                        echo htmlspecialchars($anexo['nmAnexo']);
                        echo '</a>';
                        echo '</td>';
                        echo '<td class="file-date">';
                        if ($anexo['timestamp']) {
                            echo date("d/m/Y H:i:s", $anexo['timestamp']);
                        } else {
                            echo '<span style="color: #94a3b8;">Não disponível</span>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                }

                echo '</tbody></table>';
                echo '</div>';
                echo '</div>';
            } else {
                // Estado vazio - sem anexos
                echo '<div class="empty-state">';
                echo '<ion-icon name="folder-open-outline"></ion-icon>';
                echo '<p>Nenhum documento anexado</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- ============================================
         Ações da Página
         ============================================ -->
    <div class="page-actions">
        <a href="consultarLicitacao.php" class="btn btn-primary">
            <ion-icon name="arrow-back-outline"></ion-icon>
            Voltar para Licitações
        </a>
        <?php
        // Botão de edição apenas para administradores (idPerfil == 9)
        if (isset($perfil['idPerfil']) && $perfil['idPerfil'] == 9) {
        ?>
            <a href="licitacaoForm.php?idLicitacao=<?php echo $idLicitacao; ?>" class="btn btn-outline">
                <ion-icon name="create-outline"></ion-icon>
                Editar Licitação
            </a>
        <?php } ?>
    </div>
</div>

<script>
    /**
     * ============================================
     * JavaScript - Funcionalidades da Página
     * ============================================
     */

    /**
     * Alterna entre visualização em cards e lista
     * @param {string} view - Tipo de visualização ('grid' ou 'list')
     */
    function toggleFilesView(view) {
        const grid = document.getElementById('filesGrid');
        const list = document.getElementById('filesList');
        const buttons = document.querySelectorAll('.files-view-btn');

        // Atualiza botões
        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });

        // Alterna visualização
        if (view === 'grid') {
            grid.classList.remove('hidden');
            list.classList.remove('active');
        } else {
            grid.classList.add('hidden');
            list.classList.add('active');
        }
    }

    /**
     * Sistema de ordenação da tabela de anexos
     */
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('anexosTable');

        if (!table) return;

        const headers = table.querySelectorAll('th.sortable');

        headers.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.getAttribute('data-column');
                const currentOrder = this.getAttribute('data-order');
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                // Atualiza indicador visual
                headers.forEach(h => h.removeAttribute('data-order'));
                this.setAttribute('data-order', newOrder);

                // Ordena as linhas
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));

                rows.sort((a, b) => {
                    let aVal, bVal;

                    if (column === 'nome') {
                        aVal = a.getAttribute('data-nome').toLowerCase();
                        bVal = b.getAttribute('data-nome').toLowerCase();
                    } else {
                        aVal = parseInt(a.getAttribute('data-timestamp')) || 0;
                        bVal = parseInt(b.getAttribute('data-timestamp')) || 0;
                    }

                    if (newOrder === 'asc') {
                        return aVal > bVal ? 1 : -1;
                    } else {
                        return aVal < bVal ? 1 : -1;
                    }
                });

                // Reinsere as linhas ordenadas
                rows.forEach(row => tbody.appendChild(row));

                // Ordena também os cards
                sortCards(column, newOrder);
            });
        });
    });

    /**
     * Ordena os cards de arquivos
     * @param {string} column - Coluna para ordenar
     * @param {string} order - Ordem ('asc' ou 'desc')
     */
    function sortCards(column, order) {
        const container = document.getElementById('filesGrid');
        if (!container) return;

        const cards = Array.from(container.querySelectorAll('.file-card-link'));

        cards.sort((a, b) => {
            const cardA = a.querySelector('.file-card');
            const cardB = b.querySelector('.file-card');
            let aVal, bVal;

            if (column === 'nome') {
                aVal = cardA.getAttribute('data-nome').toLowerCase();
                bVal = cardB.getAttribute('data-nome').toLowerCase();
            } else {
                aVal = parseInt(cardA.getAttribute('data-timestamp')) || 0;
                bVal = parseInt(cardB.getAttribute('data-timestamp')) || 0;
            }

            if (order === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });

        cards.forEach(card => container.appendChild(card));
    }
</script>

<?php include_once 'includes/footer.inc.php'; ?>