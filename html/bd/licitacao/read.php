<?php
//read.php - Visualização de Licitações (Cards e Tabela)

// Iniciar sessão se não estiver iniciada (para chamadas AJAX)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexão se não estiver incluída
if (!isset($pdoCAT)) {
    include_once __DIR__ . '/../conexao.php';
}

$lgnCriador = $_SESSION['login'] ?? '';

// Filtros recebidos
$tituloLicitacaoFilter = filter_input(INPUT_POST, 'tituloLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$statusLicitacaoFilter = filter_input(INPUT_POST, 'statusLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$dtIniLicitacaoFilter = filter_input(INPUT_POST, 'dtIniLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$dtFimLicitacaoFilter = filter_input(INPUT_POST, 'dtFimLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);
$tipoLicitacao = filter_input(INPUT_POST, 'tipoLicitacao', FILTER_SANITIZE_SPECIAL_CHARS);

// Processar filtro de tipo
if (!isset($tipoLicitacao) || empty($tipoLicitacao) || $tipoLicitacao == 'vazio') {
    $tipoLicitacao = " IS NOT NULL";
} else {
    $tipoLicitacao = " = " . $tipoLicitacao;
}

// Processar filtro de status
if (!isset($statusLicitacaoFilter) || empty($statusLicitacaoFilter)) {
    $statusLicitacaoFilter = 'Em Andamento';
}

if (isset($tituloLicitacaoFilter)) {
    $tituloLicitacaoFilterSQL = " like '%$tituloLicitacaoFilter%'";
} else {
    $tituloLicitacaoFilterSQL = " like '%%'";
}

if ($statusLicitacaoFilter !== 'vazio') {
    if ($statusLicitacaoFilter == 'Em Andamento') {
        $statusLicitacaoFilterSQL = "like 'Em Andamento'";
    } else if ($statusLicitacaoFilter == 'Suspenso') {
        $statusLicitacaoFilterSQL = "like 'Suspenso'";
    } else if ($statusLicitacaoFilter == 'Encerrado') {
        $statusLicitacaoFilterSQL = "like 'Encerrado'";
    } else if ($statusLicitacaoFilter == 'Rascunho') {
        $statusLicitacaoFilterSQL = "like 'Rascunho'";
    } else {
        $statusLicitacaoFilterSQL = "NOT LIKE 'Rascunho'";
    }
} else {
    $statusLicitacaoFilterSQL = "NOT LIKE 'Rascunho'";
}

if (isset($dtIniLicitacaoFilter) && !empty($dtIniLicitacaoFilter)) {
    $dtIniLicitacaoFilterSQL = " between '$dtIniLicitacaoFilter' and '$dtFimLicitacaoFilter'";
} else {
    $dtIniLicitacaoFilterSQL = " IS NOT NULL";
}

// Query principal
$querySelect2 = "SELECT  
                    DISTINCT D.*, L.ID_LICITACAO, L.DT_LICITACAO, TIPO.NM_TIPO AS NM_TIPO, TIPO.SGL_TIPO
                FROM
                    LICITACAO L
                    LEFT JOIN ANEXO A ON L.ID_LICITACAO = A.ID_LICITACAO
                    LEFT JOIN DETALHE_LICITACAO D ON D.ID_LICITACAO = L.ID_LICITACAO
                    LEFT JOIN TIPO_LICITACAO TIPO ON D.TIPO_LICITACAO = TIPO.ID_TIPO
                WHERE
                    D.STATUS_LICITACAO $statusLicitacaoFilterSQL
                    AND L.DT_EXC_LICITACAO IS NULL";

$querySelect2 .= " AND (D.COD_LICITACAO $tituloLicitacaoFilterSQL OR D.OBJETO_LICITACAO $tituloLicitacaoFilterSQL)";
$querySelect2 .= " AND L.DT_LICITACAO $dtIniLicitacaoFilterSQL ";
$querySelect2 .= " AND D.TIPO_LICITACAO $tipoLicitacao";
$querySelect2 .= " ORDER BY L.[DT_LICITACAO] DESC";

// Executar consulta
$querySelect = $pdoCAT->query($querySelect2);
$licitacoes = $querySelect->fetchAll(PDO::FETCH_ASSOC);
$totalLicitacoes = count($licitacoes);

// Email do usuário para verificar notificações
$email = $_SESSION['email'] ?? '';
?>

<!-- Contador de Resultados -->
<div class="results-info">
    <ion-icon name="information-circle-outline"></ion-icon>
    <span class="results-count"><?php echo $totalLicitacoes; ?></span> 
    licitaç<?php echo $totalLicitacoes == 1 ? 'ão encontrada' : 'ões encontradas'; ?>
</div>

<?php if ($totalLicitacoes > 0): ?>

<!-- Visualização em Cards -->
<div class="cards-container" id="cardsContainer">
    <?php foreach ($licitacoes as $registros):
        $idLicitacao = $registros['ID_LICITACAO'];
        $idTipoLicitacao = $registros['TIPO_LICITACAO'];
        $tipoLicitacaoNome = $registros['NM_TIPO'];
        $codLicitacao = $registros['SGL_TIPO'] . ' ' . $registros['COD_LICITACAO'];
        $statusLicitacao = $registros['STATUS_LICITACAO'];
        $dtLicitacao = date('d/m/Y', strtotime($registros['DT_LICITACAO']));
        $objLicitacao = $registros['OBJETO_LICITACAO'];
        $permitirAtualizacao = $registros['ENVIO_ATUALIZACAO_LICITACAO'];
        
        $tituloLicitacao = $tipoLicitacaoNome ? $tipoLicitacaoNome . ' - ' . $codLicitacao : $codLicitacao;
        
        // Verificar se usuário está cadastrado para notificações
        $idAtualizacao = null;
        if (!empty($email)) {
            $queryUpdateLicitacao = "SELECT ID_ATUALIZACAO FROM ATUALIZACAO 
                                    WHERE ID_LICITACAO = $idLicitacao 
                                    AND EMAIL_ADM LIKE '$email' 
                                    AND DT_EXC_ATUALIZACAO IS NULL";
            $queryUpdateLici2 = $pdoCAT->query($queryUpdateLicitacao);
            $resultAtualizacao = $queryUpdateLici2->fetch(PDO::FETCH_ASSOC);
            if ($resultAtualizacao) {
                $idAtualizacao = $resultAtualizacao['ID_ATUALIZACAO'];
            }
        }
        
        // Classe do status
        $statusClass = '';
        switch ($statusLicitacao) {
            case 'Em Andamento': $statusClass = 'em-andamento'; break;
            case 'Suspenso': $statusClass = 'suspenso'; break;
            case 'Encerrado': $statusClass = 'encerrado'; break;
            case 'Rascunho': $statusClass = 'rascunho'; break;
        }
    ?>
    <div class="licitacao-card">
        <div class="card-header">
            <span class="card-codigo"><?php echo htmlspecialchars($codLicitacao); ?></span>
            <span class="card-status <?php echo $statusClass; ?>">
                <span class="card-status-dot"></span>
                <?php echo htmlspecialchars($statusLicitacao); ?>
            </span>
        </div>
        
        <h3 class="card-title">
            <a href="viewLicitacao.php?idLicitacao=<?php echo $idLicitacao; ?>">
                <?php echo htmlspecialchars($tituloLicitacao); ?>
            </a>
        </h3>
        
        <div class="card-meta">
            <?php if ($tipoLicitacaoNome): ?>
            <div class="card-meta-item">
                <ion-icon name="pricetag-outline"></ion-icon>
                <span class="card-meta-tag"><?php echo htmlspecialchars($tipoLicitacaoNome); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <p class="card-objeto">
            <?php echo htmlspecialchars($objLicitacao); ?>
        </p>
        
        <div class="card-footer">
            <div class="card-date">
                <ion-icon name="calendar-outline"></ion-icon>
                <?php echo $dtLicitacao; ?>
            </div>
            
            <div class="card-actions">
                <?php
                // Botões de ação para administradores
                if (isset($_SESSION['perfil'])) {
                    foreach ($_SESSION['perfil'] as $perfil) {
                        if ($perfil['idPerfil'] == 9 || $perfil['idPerfil'] == $idTipoLicitacao) {
                            echo '<a href="editarLicitacao.php?idLicitacao=' . $idLicitacao . '" class="card-action-btn" title="Editar Licitação">';
                            echo '<ion-icon name="create-outline"></ion-icon>';
                            echo '</a>';
                            
                            echo '<button type="button" onclick="confirmDelete(' . $idLicitacao . ')" class="card-action-btn" title="Excluir Licitação">';
                            echo '<ion-icon name="trash-outline"></ion-icon>';
                            echo '</button>';
                            break;
                        }
                    }
                }
                
                // Botão de notificação
                if ($permitirAtualizacao == 1 && !empty($email)) {
                    if (!$idAtualizacao) {
                        echo '<button type="button" onclick="enviarAtualizacao(' . $idLicitacao . ')" class="card-action-btn notify" title="Receber notificações desta licitação">';
                        echo '<ion-icon name="notifications-outline"></ion-icon>';
                        echo '</button>';
                    } else {
                        echo '<button type="button" onclick="desativarAtualizacao(' . $idAtualizacao . ')" class="card-action-btn notify active" title="Desativar notificações">';
                        echo '<ion-icon name="notifications"></ion-icon>';
                        echo '</button>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Visualização em Tabela -->
<div class="table-container" id="tableContainer">
    <div class="table-scroll-wrapper">
        <table class="licitacoes-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Objeto</th>
                <th>Tipo</th>
                <th>Data</th>
                <th>Status</th>
                <th style="width: 120px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($licitacoes as $registros):
                $idLicitacao = $registros['ID_LICITACAO'];
                $idTipoLicitacao = $registros['TIPO_LICITACAO'];
                $tipoLicitacaoNome = $registros['NM_TIPO'];
                $codLicitacao = $registros['SGL_TIPO'] . ' ' . $registros['COD_LICITACAO'];
                $statusLicitacao = $registros['STATUS_LICITACAO'];
                $dtLicitacao = date('d/m/Y', strtotime($registros['DT_LICITACAO']));
                $objLicitacao = $registros['OBJETO_LICITACAO'];
                $permitirAtualizacao = $registros['ENVIO_ATUALIZACAO_LICITACAO'];
                
                // Verificar notificações
                $idAtualizacao = null;
                if (!empty($email)) {
                    $queryUpdateLicitacao = "SELECT ID_ATUALIZACAO FROM ATUALIZACAO 
                                            WHERE ID_LICITACAO = $idLicitacao 
                                            AND EMAIL_ADM LIKE '$email' 
                                            AND DT_EXC_ATUALIZACAO IS NULL";
                    $queryUpdateLici2 = $pdoCAT->query($queryUpdateLicitacao);
                    $resultAtualizacao = $queryUpdateLici2->fetch(PDO::FETCH_ASSOC);
                    if ($resultAtualizacao) {
                        $idAtualizacao = $resultAtualizacao['ID_ATUALIZACAO'];
                    }
                }
                
                // Classe do status
                $statusClass = '';
                switch ($statusLicitacao) {
                    case 'Em Andamento': $statusClass = 'em-andamento'; break;
                    case 'Suspenso': $statusClass = 'suspenso'; break;
                    case 'Encerrado': $statusClass = 'encerrado'; break;
                    case 'Rascunho': $statusClass = 'rascunho'; break;
                }
            ?>
            <tr>
                <td class="table-codigo">
                    <a href="viewLicitacao.php?idLicitacao=<?php echo $idLicitacao; ?>">
                        <?php echo htmlspecialchars($codLicitacao); ?>
                    </a>
                </td>
                <td class="table-objeto" title="<?php echo htmlspecialchars($objLicitacao); ?>">
                    <?php echo htmlspecialchars($objLicitacao); ?>
                </td>
                <td>
                    <?php if ($tipoLicitacaoNome): ?>
                    <span class="table-tipo"><?php echo htmlspecialchars($tipoLicitacaoNome); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo $dtLicitacao; ?></td>
                <td>
                    <span class="table-status card-status <?php echo $statusClass; ?>">
                        <span class="card-status-dot"></span>
                        <?php echo htmlspecialchars($statusLicitacao); ?>
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <?php
                        // Botões de ação para administradores
                        if (isset($_SESSION['perfil'])) {
                            foreach ($_SESSION['perfil'] as $perfil) {
                                if ($perfil['idPerfil'] == 9 || $perfil['idPerfil'] == $idTipoLicitacao) {
                                    echo '<a href="editarLicitacao.php?idLicitacao=' . $idLicitacao . '" class="card-action-btn" title="Editar">';
                                    echo '<ion-icon name="create-outline"></ion-icon>';
                                    echo '</a>';
                                    
                                    echo '<button type="button" onclick="confirmDelete(' . $idLicitacao . ')" class="card-action-btn" title="Excluir">';
                                    echo '<ion-icon name="trash-outline"></ion-icon>';
                                    echo '</button>';
                                    break;
                                }
                            }
                        }
                        
                        // Botão de notificação
                        if ($permitirAtualizacao == 1 && !empty($email)) {
                            if (!$idAtualizacao) {
                                echo '<button type="button" onclick="enviarAtualizacao(' . $idLicitacao . ')" class="card-action-btn notify" title="Receber notificações">';
                                echo '<ion-icon name="notifications-outline"></ion-icon>';
                                echo '</button>';
                            } else {
                                echo '<button type="button" onclick="desativarAtualizacao(' . $idAtualizacao . ')" class="card-action-btn notify active" title="Desativar notificações">';
                                echo '<ion-icon name="notifications"></ion-icon>';
                                echo '</button>';
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div><!-- /.table-scroll-wrapper -->
</div>

<?php else: ?>

<!-- Empty State -->
<div class="empty-state">
    <div class="empty-state-icon">📋</div>
    <h3>Nenhuma licitação encontrada</h3>
    <p>Tente ajustar os filtros de busca ou limpar a pesquisa.</p>
</div>

<?php endif; ?>