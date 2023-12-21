<?php
//ARQUIVO QUE FAZ A VALIDAÇÃO SE O USUÁRIO ESTÁ LOGADO NO SISTEMA
//SE TENTAR ACESSAR ALGUMA PÁGINA DIRETAMENTE, ELE DIRECIONA PARA A PÁGINA DE LOGOUT
session_start();
include_once '../bd/conexao.php';
// include('protectAdmin.php');


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
    <div>
        <?php if (isset($_SESSION['login'])) { ?>
            <label class="userLogin">
                Bem-Vindo, <?php echo $_SESSION['login']; ?>
                <span style="color: gold;"><?php echo '(' . $_SESSION['nmPerfil'] . ')'; ?></span>
            </label>
            <a href="logout.php" class="up_menu_btn"><ion-icon name="exit-outline"></ion-icon></a>
        <?php } else { ?>
            <a href="login.php" class="up_menu_btn"><ion-icon name="enter-outline"></ion-icon></a>
        <?php } ?>

        <?php if ($_SESSION['admin'] == 5) { ?>
            <a href="cadLicitacao.php" class="up_menu_btn"><ion-icon name="duplicate-outline"></ion-icon></a>
        <?php } ?>
        <a href="consultarLicitacao.php" class="up_menu_btn"><ion-icon name="search-outline"></ion-icon></a>
    </div>
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
        switch ($_SESSION['admin']) {
            case 5: ?>
                <hr>
                <hr>
                <br>
                <li>
                    <a href="cadLicitacao.php" style="color:gold">Criar Licitação</a>
                </li>
                <li>
                    <a href="cadCriterio.php">Adm Critérios</a>
                </li>
                <li>
                    <a href="cadForma.php">Adm Forma</a>
                </li>
                <li><a href="#">Adm Usuários</a>
                    <ul>
                        <li>
                            <a href="consultarUsuario.php">Adm Usuário</a>
                        </li>
                        <li>
                            <a href="cadPerfil.php">Adm Perfil</a>
                        </li>
                    </ul>
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
            <?php break;

            case 4: ?>
                <hr>
                <hr>
                <br>
                <li>
                    <a href="cadLicitacao.php" style="color:gold">Criar Licitação</a>
                </li>
        <?php break;
        } ?>

    </ul>
</div>


<!--========== MAIN JS ==========-->
<script src="../materialize/js/main.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var menuToggle = document.getElementById('menuToggle');
        var menu = document.getElementById('menu');

        menuToggle.addEventListener('click', function() {
            if (menu.style.display === 'none') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        });
    });

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

    // Fechar modal se clicar fora da área do modal
    window.onclick = function(event) {
        if (event.target == document.getElementById('modalCadastro')) {
            document.getElementById('modalCadastro').style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebarBtn = document.getElementById('sidebar_btn');
        const menu = document.getElementById('menu');
        const container = document.querySelector('.container');

        let isMenuOpen = false;

        sidebarBtn.addEventListener('click', function() {

            if (!isMenuOpen) {
                menu.style.display = 'none';
                container.style.left = '140';
                container.style.width = 'calc(95% - 200px)';
            } else {
                menu.style.display = 'block';
                container.style.left = '0';
                container.style.width = '98%';
            }

            isMenuOpen = !isMenuOpen;
        });

        sidebarBtn.addEventListener('mouseenter', function() {
            menu.style.display = 'block';
            container.style.left = '140';
            container.style.width = 'calc(95% - 200px)';

            if (isMenuOpen) {
                menu.style.display = 'none';
                container.style.left = '0';
                container.style.width = '98%';
            } else {
                menu.style.display = 'block';
                container.style.left = '140';
                container.style.width = 'calc(95% - 200px)';
            }

            isMenuOpen = !isMenuOpen;
        });

        checkbox.addEventListener('change', function() {
            menu.style.display = checkbox.checked ? 'block' : 'none';
        });
    });
</script>