<?php
//ARQUIVO QUE FAZ A VALIDAÇÃO SE O USUÁRIO ESTÁ LOGADO NO SISTEMA
//SE TENTAR ACESSAR ALGUMA PÁGINA DIRETAMENTE, ELE DIRECIONA PARA A PÁGINA DE LOGOUT
session_start();
include_once '../bd/conexao.php';
// include('protectAdmin.php');

$login = $_SESSION['login'];

foreach ($_SESSION['perfil'] as $perfil) {
    $idPerfil[] = $perfil['idPerfil'];

    if ($perfil['idPerfil'] == 9) {
        $isAdmin = 1;
    }
}

$_SESSION['idPerfilFinal'] = implode(',', $idPerfil);

// Função para obter os menus principais
function obterMenusPrincipais($pdoCAT)
{
    $query = "SELECT * FROM MENU WHERE DT_EXC_MENU IS NULL ORDER BY NM_MENU";
    $stmt = $pdoCAT->query($query);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para obter os submenus de um menu principal
function obterSubMenus($pdoCAT, $menuID)
{
    $query = "SELECT * FROM SUBMENU WHERE ID_MENU = ? AND DT_EXC_SUBMENU IS NULL ORDER BY NM_SUBMENU";
    $stmt = $pdoCAT->prepare($query);
    $stmt->execute([$menuID]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para obter os itens de um submenu
function obterItensMenu($pdoCAT, $submenuID)
{
    $query = "SELECT * FROM ITEMMENU WHERE ID_SUBMENU = ? AND DT_EXC_ITEMMENU IS NULL ORDER BY NM_ITEMMENU";
    $stmt = $pdoCAT->prepare($query);
    $stmt->execute([$submenuID]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para construir o menu em HTML
function construirMenuHTML($pdoCAT)
{
    $resultadoMenusPrincipais = obterMenusPrincipais($pdoCAT);

    foreach ($resultadoMenusPrincipais as $menuPrincipal) {
        $submenus = obterSubMenus($pdoCAT, $menuPrincipal['ID_MENU']);
        construirMenuHTMLRecursivo($pdoCAT, $menuPrincipal, $submenus);
    }
}

// Função auxiliar para construir o menu de forma recursiva
function construirMenuHTMLRecursivo($pdoCAT, $menuPrincipal, $submenus)
{
    echo '<li><a href="' . $menuPrincipal['LINK_MENU'] . '" target="_blank">' . $menuPrincipal['NM_MENU'] . '</a>';
    if (!empty($submenus)) {
        echo '<ul>';
        foreach ($submenus as $submenu) {
            echo '<li><a href="' . $submenu['LINK_SUBMENU'] . '" target="_blank">' . $submenu['NM_SUBMENU'] . '</a>';
            $itensmenu = obterItensMenu($pdoCAT, $submenu['ID_SUBMENU']);
            if (!empty($itensmenu)) {
                echo '<ul>';
                foreach ($itensmenu as $itemmenu) {
                    echo '<li><a href="' . $itemmenu['LINK_ITEMMENU'] . '" target="_blank">' . $itemmenu['NM_ITEMMENU'] . '</a></li>';
                }
                echo '</ul>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }
    echo '</li>';
}

?>
<div class="header3">
    <div>
        <?php if (isset($_SESSION['login'])) { ?>
            <label class="userLogin">Bem-Vindo, <?php echo $_SESSION['login']; ?></label>

            <a href="logout.php" class="up_menu_btn" title="Sair"><i class="bi bi-box-arrow-up-right"></i></a>
        <?php } else { ?>
            <a href="login.php" class="up_menu_btn" title="Entrar"><i class="bi bi-box-arrow-in-up-right"></i></ion-icon></a>
        <?php }

        if (strpos($login, '@') !== false) { ?>
            <a href="trocaSenhaUsuario.php" class="up_menu_btn" title="Trocar Senha"><i class="bi bi-key"></i></a>

        <?php } ?>

        <?php if (!empty($_SESSION['perfil'])) {
        ?>
            <a href="cadLicitacao.php" class="up_menu_btn" title="Cadastrar Licitação"><i class="bi bi-plus-circle"></i></a>
        <?php } ?>
        <a href="consultarLicitacao.php" class="up_menu_btn" title="Pesquisar Licitações"><i class="bi bi-search"></i></a>
        <a href="#contatoModal" class="up_menu_btn" data-toggle="modal" title="Contatos"><i class="bi bi-chat-right-dots"></i></a>

        <?php
        if (isMobile()) {
            if (isset($_SESSION['msg']) && $_SESSION['msg'] !== '') { ?>
                <fieldset class="fieldset-msg" id="fieldsetMsg">
                    <label class="msg">
                        <?php echo $_SESSION['msg'];
                        $_SESSION['msg'] = ''; ?>
                    </label>
                </fieldset>
            <?php }
        } else {
            ?>
            <label class="msg">
                <?php echo $_SESSION['msg'];
                $_SESSION['msg'] = ''; ?>
            </label>
        <?php
        } ?>

    </div>
</div>

<header class="header2">
    <input type="checkbox" id="check" checked>

    <label class="label2" for="check" id="menuToggle">
        <ion-icon name="menu-outline" id="sidebar_btn"></ion-icon>
    </label>
    <div class="left2">
        <img src="imagens/logo_icon.png" class="imageMenu" alt="">
    </div>
    <div class="left2">
        <a href="index.php">
            <h3>Portal de Compras</h3>
        </a>
    </div>


    <?php

    function isMobile()
    {
        // Lista de palavras-chave que podem indicar um dispositivo móvel
        $mobileKeywords = array('Android', 'iPhone', 'iPad', 'Windows Phone', 'BlackBerry', 'Opera Mini', 'Symbian', 'Mobile');

        // Obtém o cabeçalho "User-Agent" do navegador
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        // Verifica se o User-Agent contém alguma palavra-chave móvel
        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true; // É um dispositivo móvel
            }
        }

        return false; // Não é um dispositivo móvel
    }

    ?>

</header>

<div id="modalCadastro" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h4 id="modalTitle"></h4>
        <p id="modalContent"></p>
    </div>
</div>

<div id="menu">
    <!-- Conteúdo do menu -->
    <h2></h2>
    <ul>
        <li>
            <a href="consultarLicitacao.php" style="color:gold">Licitações</a>
        </li>

        <!-- MENU CRIADO RECURSIVAMENTE BASEADO NO CADASTRO DO BANCO DE DADOS -->
        <?php
        construirMenuHTML($pdoCAT); ?>


        <?php
        if (!empty($_SESSION['idPerfilFinal'])) { ?>
            <hr>
            <hr>
            <br>
            <li>
                <a href="cadLicitacao.php" style="color:gold">Criar Licitação</a>
            </li>
            <?php }
        foreach ($_SESSION['perfil'] as $perfil) {
            // Verifica se o ID do perfil é igual a 9
            if ($perfil['idPerfil'] == 9) { ?>
                <li>
                    <a href="cadCriterio.php">Adm Critérios</a>
                </li>
                <li>
                    <a href="cadForma.php">Adm Forma</a>
                </li>
                <li>
                    <a href="cadTipo.php">Adm Tipo Contratação</a>
                </li>
                <li>
                    <a href="consultarUsuario.php">Adm Usuários</a>
                </li>
                <li><a href="#">Adm Menus</a>
                    <ul>
                        <li>
                            <a href="cadMenu.php">Adm Menu</a>
                        </li>
                        <li>
                            <a href="cadSubMenu.php">Adm SubMenu</a>
                        </li>
                        <li>
                            <a href="cadItemMenu.php">Adm ItemMenu</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="cadAnexos.php">Adm Anexos</a>
                </li>
        <?php // Se encontrou o ID 9, você pode sair do loop
                break;
            }
        }
        ?>


        <?php if (strpos($login, '@') !== false) { ?>
            <hr>
            <hr>
            <br>
            <li>
                <a href="trocaSenhaUsuario.php">Trocar Senha</a>
            </li>

        <?php }

        if (isset($_SESSION['login'])) { ?>
            <li>
                <a href="consultarAtualizacao.php">Adm Envio de E-mail</a>
            </li>
        <?php }

        ?>

    </ul>
</div>

<!-- contatoModal -->
<div class="modal fade" id="contatoModal" tabindex="-1" role="dialog" aria-labelledby="contatoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contatoModalLabel">Contatos</h5>
            </div>
            <div class="modal-body">
                <p class="descricao1">Informações, dúvidas e esclarecimentos sobre licitação Cesan</p>
                <p class="descricao2">Comissão Permanente de Licitações (CPL)</p>
                <p class="descricao3">Rua Nelcy Lopes Vieira, S/N, Jardim Limoeiro, Serra, ES, CEP: 29164-018</p>
                <ul class="list-group">
                    <li class="list-group-item"><ion-icon name="call-outline"></ion-icon> (27) 2127-5119</li>
                    <li class="list-group-item"><ion-icon name="chatbox-ellipses-outline"></ion-icon> <a href="mailto:licitacoes@cesan.com.br">licitacoes@cesan.com.br</a></li>
                </ul>
            </div>
            <div class="modal-body">
                <p class="descricao1">Informações, dúvidas e esclarecimentos sobre pregões, dispensas eletrônicas e cadastro de fornecedores</p>
                <p class="descricao2">Divisão de Compras e Suprimentos (A-DCS)</p>
                <p class="descricao3">Rua Nelcy Lopes Vieira, S/N, Jardim Limoeiro, Serra, ES. CEP: 29.164-018</p>
                <br>
                <ul class="list-group">
                    <p class="descricao2">Pregoeiros</p>
                    <li class="list-group-item"><ion-icon name="chatbox-ellipses-outline"></ion-icon> <a href="mailto:pregao@cesan.com.br">pregao@cesan.com.br</a></li>
                    <li class="list-group-item"><ion-icon name="call-outline"></ion-icon> Luciana Spinassé - (27) 2127-5299</li>
                    <li class="list-group-item"><ion-icon name="call-outline"></ion-icon> Fernando Cordeiro - (27) 2127-5418</li>
                    <li class="list-group-item"><ion-icon name="call-outline"></ion-icon> Mirelle Ino - (27) 2127-5429</li>
                </ul>
                <br>
                <ul class="list-group">
                    <p class="descricao2">Cadastro de Fornecedores</p>
                    <li class="list-group-item"><ion-icon name="chatbox-ellipses-outline"></ion-icon> <a href="mailto:cadastrofornecedor@cesan.com.br">cadastrofornecedor@cesan.com.br</a></li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!--========== MAIN JS ==========-->
<script src="../materialize/js/main.js"></script>

<script>
    function openModal() {
        document.getElementById("contatoModal").style.display = "block";
    }

    // Função para fechar o modal
    function closeModal() {
        document.getElementById("contatoModal").style.display = "none";
    }

    function isMobile() {
        return /Mobi|Android/i.test(navigator.userAgent);
    }

    document.addEventListener('DOMContentLoaded', function() {
        var menuToggle = document.getElementById('menuToggle');
        var menu = document.getElementById('menu');
        const container = document.querySelector('.container');
        const sidebarBtn = document.getElementById('sidebar_btn');

        //mantém o MENU aberto ao carregar a página
        if (!isMobile()) {
            // Mantém o MENU aberto ao carregar a página
            menu.style.display = 'block';
            container.style.left = '100';
            container.style.top = '60';

            if (!isMobile()) {
                container.style.width = 'calc(93% - 110px)';
            } else {
                container.style.width = 'calc(93% - 170px)';
            }

        } else {
            container.style.top = '60';
            container.style.left = '0';
            container.style.width = '100%';
        }

        let isMenuOpen = false;

        //posicionamento do menu após click no botão
        sidebarBtn.addEventListener('click', function() {
            if (!isMenuOpen) {
                menu.style.display = 'block';
                container.style.left = '100';

                if (!isMobile()) {
                    container.style.width = 'calc(93% - 110px)';
                } else {
                    container.style.width = 'calc(93% - 170px)';
                }
            } else {
                menu.style.display = 'none';
                container.style.left = '0';
                container.style.width = '100%';
            }

            isMenuOpen = !isMenuOpen;
        });

        // checkbox.addEventListener('change', function() {
        //     menu.style.display = checkbox.checked ? 'block' : 'none';
        // });
    });

    // ===================================================================================================================
    // CONFIGURA O CLIQUE PARA ABRIR OS SUBMENUS
    $(document).ready(function() {
        // Adiciona um manipulador de eventos de clique para os itens de menu com submenus
        $('#menu li:has(ul)').click(function(e) {
            e.preventDefault();
            e.stopPropagation(); // Impede a propagação do evento de clique para evitar a ativação do clique no documento
        });

        // Impede que o clique no submenu propague para o documento
        $('#menu ul ul').click(function(e) {
            e.stopPropagation();
        });

        // Fecha o FIELDSET-MSG ao clicar em qq local da página
        $(document).click(function(e) {
            if (!$(e.target).closest('#fieldsetMsg').length) {
                $('#fieldsetMsg').hide();
            }
        });
    });

    // MODAL ============================================================================================================
    // Mostrar modal ao clicar no botão ou link desejado
    function showModal(title, content) {
        var modal = document.getElementById('modalCadastro');
        var modalTitle = modal.querySelector('.modal-content h4');
        var modalContent = modal.querySelector('.modal-content p');

        modalTitle.innerHTML = title;
        modalContent.innerHTML = content;

        modal.style.display = 'block';
    }

    // Fechar modal ao clicar no botão de fechar
    document.querySelector('#modalCadastro .close').onclick = function() {
        document.getElementById('modalCadastro').style.display = 'none';
    }

    // Fechar modal ao clicar fora da área do modal
    window.onclick = function(event) {
        if (event.target == document.getElementById('modalCadastro')) {
            document.getElementById('modalCadastro').style.display = 'none';
        }
    }
</script>