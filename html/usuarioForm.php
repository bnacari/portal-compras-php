<?php
/**
 * ============================================
 * Portal de Compras - CESAN
 * Tela de Edição de Usuário (usuarioForm.php)
 * 
 * Refatorada de editarUsuario.php
 * Layout baseado nos padrões do sistema
 * ============================================
 */

include_once 'bd/conexao.php';
include_once 'includes/header.inc.php';
include_once 'includes/footer.inc.php';
include_once 'includes/menu.inc.php';

include('protectAdmin.php');

// ============================================
// Captura de parâmetros da URL
// ============================================
$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
$nomeBusca = filter_input(INPUT_GET, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$perfilBusca = filter_input(INPUT_GET, 'perfil', FILTER_SANITIZE_NUMBER_INT);

// ============================================
// Busca dados do usuário pelo e-mail
// ============================================
$queryAdmin = "SELECT * FROM USUARIO WHERE EMAIL_ADM like '$email'";
$querySelect = $pdoCAT->query($queryAdmin);

$isExterno = false;
while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['EMAIL_ADM'];
    $idUsuario = $registros['ID_ADM'];
    $isExterno = (strtolower(trim($registros['LGN_CRIADOR'] ?? '')) === 'externo');
endwhile;

// ============================================
// Se usuário existe, busca dados completos
// ============================================
if (isset($existeUsuario)) {
    $queryLE = "SELECT U.*, TL.*
    FROM USUARIO U 
    LEFT JOIN PERFIL_USUARIO PU ON U.ID_ADM = PU.ID_USUARIO
    LEFT JOIN TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO 
    WHERE U.ID_ADM = $idUsuario";

    $querySelectLE = $pdoCAT->query($queryLE);

    $perfilUsuario = array();
    while ($registrosLE = $querySelectLE->fetch(PDO::FETCH_ASSOC)) :
        $nmPerfil = $registrosLE['NM_TIPO'];
        $idPerfil = $registrosLE['ID_TIPO'];
        $nmUsuario = $registrosLE['NM_ADM'];
        $registroPU = array(
            'NM_TIPO' => $nmPerfil,
            'ID_TIPO' => $idPerfil
        );
        $perfilUsuario[] = $registroPU;
    endwhile;
} else {
    // ============================================
    // Se não existe, busca no AD e cria
    // ============================================
    $queryInsert = "SELECT [ID]
                    ,[sAMAccountName]
                    ,[initials]
                    ,[department]
                    ,[physicalDeliveryOfficeName]
                    ,[displayName]
                    ,[telephoneNumber]
                    ,[mobile]
                    ,[mail]
                    ,[accountExpires]
                    ,[IsEnabled]
                    ,[objectCategory]
                FROM [ADCache].[dbo].[Users]
                where mail like '$email'";

    $queryInsert2 = $pdoCAT->query($queryInsert);

    while ($registros = $queryInsert2->fetch(PDO::FETCH_ASSOC)) :
        $matricula = $registros['initials'];
        $nmUsuario = $registros['displayName'];
        $mail = $registros['mail'];
        $login = $registros['sAMAccountName'];
    endwhile;

    $loginCriador = $_SESSION['login'];
    $querySelect2 = "INSERT INTO USUARIO VALUES ($matricula, '$nmUsuario', '$mail', GETDATE(), 'A', '$loginCriador', '$login', NULL, NULL)";
    $querySelect = $pdoCAT->query($querySelect2);

    // Registro de LOG
    $login = $_SESSION['login'];
    $tela = 'Usuário';
    $acao = 'Perfil CRIADO para ' . $nmUsuario;
    $idEvento = $matricula;
    $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");

    // Busca ID do usuário recém criado
    $queryAdmin = "SELECT * FROM USUARIO WHERE EMAIL_ADM like '$email'";
    $querySelect = $pdoCAT->query($queryAdmin);
    while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
        $idUsuario = $registros['ID_ADM'];
    endwhile;
}
?>

<!-- ============================================
     CSS e JS externos
     ============================================ -->
<link rel="stylesheet" href="style/css/usuarioForm.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="page-container">
    
    <!-- ============================================
         Header da Página
         ============================================ -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-header-info">
                <div class="page-header-icon">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <div>
                    <h1>Editar Usuário</h1>
                    <p class="page-header-subtitle"><?php echo htmlspecialchars($nmUsuario); ?></p>
                </div>
            </div>
            
            <!-- Botão Voltar -->
            <button type="button" class="btn-voltar" id="btnVoltar">
                <ion-icon name="arrow-back-outline"></ion-icon>
                Voltar
            </button>
        </div>
    </div>

    <!-- ============================================
         Card Principal - Dados do Usuário
         ============================================ -->
    <div class="section-card">
        <div class="section-header">
            <ion-icon name="person-circle-outline"></ion-icon>
            <h2>Dados do Usuário e Perfis de Acesso</h2>
        </div>
        
        <div class="section-body">
            <form action="bd/usuario/update.php" method="post" id="formUsuario">
                
                <!-- Campo oculto com ID do usuário -->
                <input type="hidden" id="idUsuario" name="idUsuario" value="<?php echo $idUsuario ?>">
                
                <div class="form-grid">
                    
                    <!-- E-mail -->
                    <div class="form-group">
                        <label>
                            <ion-icon name="mail-outline"></ion-icon>
                            E-mail
                        </label>
                        <input type="text" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($email) ?>" 
                               class="form-control" 
                               readonly>
                    </div>

                    <!-- Nome Completo -->
                    <div class="form-group">
                        <label>
                            <ion-icon name="person-outline"></ion-icon>
                            Nome Completo
                        </label>
                        <input type="text" 
                               value="<?php echo htmlspecialchars($nmUsuario) ?>" 
                               class="form-control" 
                               readonly>
                    </div>

                    <!-- Perfis de Acesso -->
                    <div class="form-group">
                        <label>
                            <ion-icon name="shield-checkmark-outline"></ion-icon>
                            Perfis de Acesso
                        </label>
                        <div class="select-wrapper" <?php echo $isExterno ? 'title="Usuários externos não podem ter perfis de acesso alterados"' : ''; ?>>
                            <select name="perfilUsuario[]" 
                                    id="perfilUsuario" 
                                    multiple 
                                    <?php echo $isExterno ? 'disabled' : ''; ?>>
                                <?php
                                // Busca todos os tipos de licitação ativos
                                $querySelect2 = "SELECT * FROM TIPO_LICITACAO WHERE DT_EXC_TIPO IS NULL ORDER BY NM_TIPO";
                                $querySelect = $pdoCAT->query($querySelect2);

                                // Busca perfis atuais do usuário
                                $queryPerfilUsuario = "SELECT TL.*
                                                        FROM USUARIO U 
                                                        LEFT JOIN PERFIL_USUARIO PU ON U.ID_ADM = PU.ID_USUARIO
                                                        LEFT JOIN TIPO_LICITACAO TL ON TL.ID_TIPO = PU.ID_TIPO_LICITACAO 
                                                        WHERE U.ID_ADM = $idUsuario";
                                $queryPerfisUsuario = $pdoCAT->query($queryPerfilUsuario);

                                // Monta array de perfis do usuário
                                $perfisUsuario = array();
                                while ($row = $queryPerfisUsuario->fetch(PDO::FETCH_ASSOC)) {
                                    $perfisUsuario[] = $row["ID_TIPO"];
                                }

                                // Lista todas as opções
                                while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
                                    $valorLE = $registros["ID_TIPO"];
                                    $descricaoLE = $registros["NM_TIPO"];
                                    $selecionadoLE = in_array($valorLE, $perfisUsuario) ? 'selected' : '';
                                    echo "<option value='" . $valorLE . "' $selecionadoLE>" . htmlspecialchars($descricaoLE) . "</option>";
                                endwhile;
                                ?>
                            </select>
                        </div>
                        <?php if ($isExterno): ?>
                            <span class="help-text">
                                <ion-icon name="information-circle-outline"></ion-icon>
                                Usuários externos não podem ter perfis alterados
                            </span>
                        <?php endif; ?>
                    </div>
                    
                </div>

                <!-- ============================================
                     Botões de Ação
                     ============================================ -->
                <div class="form-actions">
                    <button type="button" class="btn-cancelar" id="btnCancelar">
                        <ion-icon name="close-outline"></ion-icon>
                        Cancelar
                    </button>
                    <button type="submit" class="btn-salvar">
                        <ion-icon name="save-outline"></ion-icon>
                        Salvar Alterações
                    </button>
                </div>

            </form>
            
            <!-- Form oculto para voltar com parâmetros de busca -->
            <form id="formVoltar" action="administracao.php?aba=usuarios" method="post" style="display: none;">
                <input type="hidden" name="nome" value="<?php echo htmlspecialchars($nomeBusca ?? ''); ?>">
                <input type="hidden" name="perfilUsuario" value="<?php echo htmlspecialchars($perfilBusca ?? '0'); ?>">
            </form>
        </div>
    </div>
    
</div>

<!-- ============================================
     Scripts JavaScript
     ============================================ -->
<script>
$(document).ready(function() {
    
    // ============================================
    // Inicializa Select2 com pesquisa
    // ============================================
    $('#perfilUsuario').select2({
        placeholder: 'Selecione os perfis de acesso',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Nenhum resultado encontrado";
            },
            searching: function() {
                return "Pesquisando...";
            }
        }
    });
    
    // ============================================
    // Botão Cancelar - volta para busca
    // ============================================
    $('#btnCancelar, #btnVoltar').on('click', function() {
        $('#formVoltar').submit();
    });
    
});
</script>

<?php include_once 'includes/footer.inc.php'; ?>