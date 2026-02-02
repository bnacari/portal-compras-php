<?php
/**
 * ============================================
 * Portal de Compras - CESAN
 * Tela de Visualização de Licitação
 * 
 * Layout refatorado com funcionalidade de notificação
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
    $permitirAtualizacao = $registros['ENVIO_ATUALIZACAO_LICITACAO'] ?? 0;
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
        $nmTipo = $registros['NM_TIPO'];
    endwhile;
}

// Busca nome do critério
if ($criterioLicitacao && $criterioLicitacao != '0') {
    $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[CRITERIO_LICITACAO] WHERE ID_CRITERIO = $criterioLicitacao";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
        $nmCriterio = $registros['NM_CRITERIO'];
    endwhile;
}

// Busca nome da forma
if ($formaLicitacao && $formaLicitacao != '0') {
    $querySelect2 = "SELECT * FROM [PortalCompras].[dbo].[FORMA] WHERE ID_FORMA = $formaLicitacao";
    $querySelect = $pdoCAT->query($querySelect2);

    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)):
        $nmForma = $registros['NM_FORMA'];
    endwhile;
}

// Corrige datas inválidas (1969)
if (strpos($dtAberLicitacao, '1969') !== false || strpos($dtAberLicitacao, '31/12') !== false) {
    $dtAberLicitacao = '';
}
if (strpos($dtIniSessLicitacao, '1969') !== false || strpos($dtIniSessLicitacao, '31/12') !== false) {
    $dtIniSessLicitacao = '';
}

// ============================================
// Verifica se usuário está cadastrado para notificações
// ============================================
$usuarioJaCadastrado = false;
$idAtualizacaoUsuario = null;

if (isset($_SESSION['idUsuario']) && $_SESSION['sucesso'] == 1 && $permitirAtualizacao == 1) {
    $idUsuarioLogado = $_SESSION['idUsuario'];
    $queryVerificaAtualizacao = "SELECT ID_ATUALIZACAO FROM ATUALIZACAO 
                                  WHERE ID_LICITACAO = $idLicitacao 
                                  AND ID_ADM = $idUsuarioLogado 
                                  AND DT_EXC_ATUALIZACAO IS NULL";
    $stmtVerifica = $pdoCAT->query($queryVerificaAtualizacao);
    $registroAtualizacao = $stmtVerifica->fetch(PDO::FETCH_ASSOC);

    if ($registroAtualizacao) {
        $usuarioJaCadastrado = true;
        $idAtualizacaoUsuario = $registroAtualizacao['ID_ATUALIZACAO'];
    }
}

// Determina classe do status
$statusClass = 'andamento';
if ($statusLicitacao == 'Suspenso')
    $statusClass = 'suspenso';
elseif ($statusLicitacao == 'Rascunho')
    $statusClass = 'rascunho';
elseif ($statusLicitacao == 'Encerrado')
    $statusClass = 'encerrado';

// Verifica se usuário pode editar
$podeEditar = false;
if (isset($_SESSION['perfil'])) {
    foreach ($_SESSION['perfil'] as $perfil) {
        if ($perfil['idPerfil'] == $tipoLicitacao || $perfil['idPerfil'] == 9) {
            $podeEditar = true;
            break;
        }
    }
}

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
    ];
    return $icons[$ext] ?? ['icon' => 'document', 'class' => 'default'];
}
?>

<!-- CSS da página -->
<link rel="stylesheet" href="style/css/licitacaoView.css" />

<div class="page-container">

    <!-- ============================================
         Header Profissional - Padrão Administração
         ============================================ -->
    <div class="page-header-pro">
        <div class="header-decoration">
            <div class="decoration-circle-1"></div>
            <div class="decoration-circle-2"></div>
        </div>

        <div class="header-top-row">
            <div class="header-breadcrumb">
                <a href="index.php"><ion-icon name="home-outline"></ion-icon> Início</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <a href="licitacao.php">Licitações</a>
                <ion-icon name="chevron-forward-outline" class="breadcrumb-sep"></ion-icon>
                <span>Visualizar</span>
            </div>
            <div class="header-date" id="headerDate"></div>
        </div>

        <div class="header-main-row">
            <div class="header-left">
                <div class="header-icon-box">
                    <ion-icon name="document-text-outline"></ion-icon>
                    <div class="icon-box-pulse"></div>
                </div>
                <div class="header-title-group">
                    <h1><?php echo htmlspecialchars($codLicitacao); ?></h1>
                    <p class="header-subtitle">
                        <ion-icon name="pricetag-outline"></ion-icon>
                        <?php echo htmlspecialchars($nmTipo ?? ''); ?>
                    </p>
                </div>
            </div>
            <div class="header-right">
                <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusLicitacao); ?></span>
                <?php if ($podeEditar): ?>
                <a href="licitacaoForm.php?idLicitacao=<?php echo $idLicitacao; ?>" class="btn-header-action">
                    <ion-icon name="pencil-outline"></ion-icon>
                    Editar
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ============================================
         Card de Notificação (se permitir atualizações)
         ============================================ -->
    <?php if ($permitirAtualizacao == 1 && isset($_SESSION['sucesso']) && $_SESSION['sucesso'] == 1): ?>
        <div class="notification-card" id="notificationCard">
            <div class="notification-content">
                <div class="notification-icon">
                    <ion-icon
                        name="<?php echo $usuarioJaCadastrado ? 'notifications' : 'notifications-outline'; ?>"></ion-icon>
                </div>
                <div class="notification-info">
                    <h3>Notificações por E-mail</h3>
                    <p id="notificationText">
                        <?php if ($usuarioJaCadastrado): ?>
                            Você receberá e-mails quando esta licitação for atualizada.
                        <?php else: ?>
                            Deseja receber e-mails quando houver atualizações nesta licitação?
                        <?php endif; ?>
                    </p>
                </div>
                <div class="notification-action">
                    <?php if ($usuarioJaCadastrado): ?>
                        <button type="button" class="btn btn-notification btn-unsubscribe" id="btnNotificacao" data-inscrito="1"
                            onclick="toggleNotificacaoView(<?php echo $idLicitacao; ?>, 'cancelar')">
                            <ion-icon name="notifications-off-outline"></ion-icon>
                            <span>Cancelar Notificações</span>
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-notification btn-subscribe" id="btnNotificacao" data-inscrito="0"
                            onclick="toggleNotificacaoView(<?php echo $idLicitacao; ?>, 'inscrever')">
                            <ion-icon name="notifications-outline"></ion-icon>
                            <span>Receber Notificações</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ============================================
         Seção: Informações da Licitação
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="information-circle-outline"></ion-icon>
            <h2>Informações da Licitação</h2>
        </div>
        <div class="section-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Código</span>
                    <span class="info-value"><?php echo htmlspecialchars($codLicitacao); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo</span>
                    <span class="info-value"><?php echo htmlspecialchars($nmTipo ?? '-'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Responsável</span>
                    <span class="info-value"><?php echo htmlspecialchars($respLicitacao ?: '-'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span
                            class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusLicitacao); ?></span>
                    </span>
                </div>
            </div>

            <div class="info-full">
                <span class="info-label">Objeto</span>
                <span class="info-value info-object"><?php echo nl2br(htmlspecialchars($objLicitacao ?: '-')); ?></span>
            </div>

            <?php if ($identificadorLicitacao): ?>
                <div class="info-full">
                    <span class="info-label">Identificador</span>
                    <span class="info-value"><?php echo htmlspecialchars($identificadorLicitacao); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($vlLicitacao): ?>
                <div class="info-full">
                    <span class="info-label">Valor Estimado</span>
                    <span class="info-value"><?php echo htmlspecialchars($vlLicitacao); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================
         Seção: Datas e Horários
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="calendar-outline"></ion-icon>
            <h2>Datas e Horários</h2>
        </div>
        <div class="section-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Data de Abertura</span>
                    <span class="info-value"><?php echo $dtAberLicitacao ?: '-'; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data da Sessão de Disputa</span>
                    <span class="info-value"><?php echo $dtIniSessLicitacao ?: '-'; ?></span>
                </div>
            </div>

            <?php if ($localLicitacao): ?>
                <div class="info-full">
                    <span class="info-label">Local de Abertura</span>
                    <span class="info-value">
                        <?php if (filter_var($localLicitacao, FILTER_VALIDATE_URL)): ?>
                            <a href="<?php echo htmlspecialchars($localLicitacao); ?>" target="_blank" class="info-link">
                                <ion-icon name="open-outline"></ion-icon>
                                <?php echo htmlspecialchars($localLicitacao); ?>
                            </a>
                        <?php else: ?>
                            <?php echo htmlspecialchars($localLicitacao); ?>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================
         Seção: Detalhes da Licitação
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="settings-outline"></ion-icon>
            <h2>Detalhes da Licitação</h2>
        </div>
        <div class="section-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Modo de Disputa</span>
                    <span class="info-value"><?php echo htmlspecialchars($modoLicitacao ?: '-'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Critério de Julgamento</span>
                    <span class="info-value"><?php echo htmlspecialchars($nmCriterio ?? '-'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Forma</span>
                    <span class="info-value"><?php echo htmlspecialchars($nmForma ?? '-'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Regime de Execução</span>
                    <span class="info-value"><?php echo htmlspecialchars($regimeLicitacao ?: '-'); ?></span>
                </div>
            </div>

            <?php if ($obsLicitacao): ?>
                <div class="info-full">
                    <span class="info-label">Observação</span>
                    <span class="info-value info-obs"><?php echo nl2br(htmlspecialchars($obsLicitacao)); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================
         Seção: Documentos Anexados
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="attach-outline"></ion-icon>
            <h2>Documentos Anexados</h2>
        </div>
        <div class="section-content">
            <?php
            $directory = "uploads" . '/' . $idLicitacao;
            $anexos = array();

            // Arquivos do diretório físico
            if (is_dir($directory)) {
                $files = scandir($directory);
                $files = array_diff($files, array('.', '..', '_order.json'));

                foreach ($files as $file) {
                    $anexos[] = array(
                        'nmAnexo' => $file,
                        'linkAnexo' => $directory . '/' . $file,
                        'timestamp' => filemtime($directory . '/' . $file),
                    );
                }
            }

            // Verifica se existe ordem personalizada salva
            $orderFile = $directory . '/_order.json';
            if (file_exists($orderFile)) {
                $savedOrder = json_decode(file_get_contents($orderFile), true);
                if (is_array($savedOrder)) {
                    $anexosByName = [];
                    foreach ($anexos as $anexo) {
                        $anexosByName[$anexo['nmAnexo']] = $anexo;
                    }
                    $orderedAnexos = [];
                    foreach ($savedOrder as $filename) {
                        if (isset($anexosByName[$filename])) {
                            $orderedAnexos[] = $anexosByName[$filename];
                            unset($anexosByName[$filename]);
                        }
                    }
                    foreach ($anexosByName as $anexo) {
                        $orderedAnexos[] = $anexo;
                    }
                    $anexos = $orderedAnexos;
                }
            } else {
                usort($anexos, function ($a, $b) {
                    return $b['timestamp'] - $a['timestamp'];
                });
            }

            if (!empty($anexos)) {
                // Header com toggle de visualização
                echo '<div class="files-section-header">';
                echo '<div class="files-section-title">';
                echo '<ion-icon name="folder-open-outline"></ion-icon>';
                echo '<span>Arquivos (' . count($anexos) . ')</span>';
                echo '</div>';
                echo '<div class="files-view-toggle">';
                echo '<button type="button" class="files-view-btn active" data-view="grid" onclick="toggleFilesView(\'grid\')">';
                echo '<ion-icon name="grid-outline"></ion-icon>';
                echo '</button>';
                echo '<button type="button" class="files-view-btn" data-view="list" onclick="toggleFilesView(\'list\')">';
                echo '<ion-icon name="list-outline"></ion-icon>';
                echo '</button>';
                echo '</div>';
                echo '</div>';

                // GRID VIEW
                echo '<div class="files-grid" id="filesGrid">';
                foreach ($anexos as $anexo) {
                    $fileInfo = getFileIcon($anexo['nmAnexo']);
                    $nomeArquivo = htmlspecialchars($anexo['nmAnexo']);
                    $linkArquivo = htmlspecialchars($anexo['linkAnexo']);
                    $dataArquivo = $anexo['timestamp'] ? date("d/m/Y H:i", $anexo['timestamp']) : '';

                    echo '<a href="' . $linkArquivo . '" target="_blank" class="file-card">';
                    echo '<div class="file-card-icon ' . $fileInfo['class'] . '">';
                    echo '<ion-icon name="' . $fileInfo['icon'] . '-outline"></ion-icon>';
                    echo '</div>';
                    echo '<div class="file-card-info">';
                    echo '<span class="file-card-name">' . $nomeArquivo . '</span>';
                    echo '<span class="file-card-date">' . $dataArquivo . '</span>';
                    echo '</div>';
                    echo '</a>';
                }
                echo '</div>';

                // LIST VIEW
                echo '<div class="files-list hidden" id="filesList">';
                echo '<div class="files-table-wrapper">';
                echo '<table class="files-table">';
                echo '<thead><tr><th>Arquivo</th><th>Data</th></tr></thead>';
                echo '<tbody>';

                foreach ($anexos as $anexo) {
                    $nomeArquivo = htmlspecialchars($anexo['nmAnexo']);
                    $linkArquivo = htmlspecialchars($anexo['linkAnexo']);
                    $dataArquivo = $anexo['timestamp'] ? date("d/m/Y H:i", $anexo['timestamp']) : '-';

                    echo '<tr>';
                    echo '<td>';
                    echo '<a href="' . $linkArquivo . '" target="_blank">';
                    echo '<ion-icon name="document-outline"></ion-icon> ' . $nomeArquivo;
                    echo '</a>';
                    echo '</td>';
                    echo '<td class="file-date">' . $dataArquivo . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
                echo '</div>';
                echo '</div>';
            } else {
                // Estado vazio
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
        <a href="licitacao.php" class="btn btn-primary">
            <ion-icon name="arrow-back-outline"></ion-icon>
            Voltar para Licitações
        </a>
        <?php if ($podeEditar): ?>
            <a href="licitacaoForm.php?idLicitacao=<?php echo $idLicitacao; ?>" class="btn btn-outline">
                <ion-icon name="create-outline"></ion-icon>
                Editar Licitação
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
    /**
     * ============================================
     * JavaScript - Funcionalidades da Página
     * ============================================
     */

    /**
     * Toggle de notificação via AJAX
     */
    function toggleNotificacaoView(idLicitacao, acao) {
        const button = document.getElementById('btnNotificacao');
        const card = document.getElementById('notificationCard');
        const icon = card.querySelector('.notification-icon ion-icon');
        const text = document.getElementById('notificationText');

        // Desabilitar botão temporariamente
        button.disabled = true;
        button.style.opacity = '0.5';

        // Criar FormData
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
                    // Atualizar visual do card
                    if (data.inscrito) {
                        // Usuário agora está inscrito
                        button.classList.remove('btn-subscribe');
                        button.classList.add('btn-unsubscribe');
                        button.onclick = function () { toggleNotificacaoView(idLicitacao, 'cancelar'); };
                        button.innerHTML = '<ion-icon name="notifications-off-outline"></ion-icon><span>Cancelar Notificações</span>';
                        icon.setAttribute('name', 'notifications');
                        text.textContent = 'Você receberá e-mails quando esta licitação for atualizada.';
                    } else {
                        // Usuário cancelou inscrição
                        button.classList.remove('btn-unsubscribe');
                        button.classList.add('btn-subscribe');
                        button.onclick = function () { toggleNotificacaoView(idLicitacao, 'inscrever'); };
                        button.innerHTML = '<ion-icon name="notifications-outline"></ion-icon><span>Receber Notificações</span>';
                        icon.setAttribute('name', 'notifications-outline');
                        text.textContent = 'Deseja receber e-mails quando houver atualizações nesta licitação?';
                    }

                    // Mostrar mensagem de sucesso
                    alert(data.message);
                } else {
                    alert(data.message || 'Erro ao processar solicitação');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.style.opacity = '1';
                console.error('Erro:', error);
                alert('Erro ao processar solicitação');
            });
    }

    /**
     * Alterna entre visualização em cards e lista
     */
    function toggleFilesView(view) {
        const grid = document.getElementById('filesGrid');
        const list = document.getElementById('filesList');
        const buttons = document.querySelectorAll('.files-view-btn');

        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });

        if (view === 'grid') {
            if (grid) grid.classList.remove('hidden');
            if (list) list.classList.add('hidden');
        } else {
            if (grid) grid.classList.add('hidden');
            if (list) list.classList.remove('hidden');
        }

        localStorage.setItem('filesViewLicitacao', view);
    }

    // Exibe data e hora atual no header
    document.addEventListener('DOMContentLoaded', function() {
        const hoje = new Date();
        const opcoes = { weekday: "long", day: "numeric", month: "long", year: "numeric" };
        const opcoesHora = { hour: "2-digit", minute: "2-digit", second: "2-digit" };
        const dataFormatada = hoje.toLocaleDateString("pt-BR", opcoes);
        const horaFormatada = hoje.toLocaleTimeString("pt-BR", opcoesHora);
        const dataHora = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1) + " - " + horaFormatada;
        const headerDate = document.getElementById("headerDate");
        if (headerDate) {
            headerDate.textContent = dataHora;
        }
    });

    // Restaura preferência de visualização
    document.addEventListener('DOMContentLoaded', function () {
        const savedView = localStorage.getItem('filesViewLicitacao');
        if (savedView && (savedView === 'grid' || savedView === 'list')) {
            toggleFilesView(savedView);
        }
    });
</script>

<?php include_once 'includes/footer.inc.php'; ?>