<?php
//ARQUIVO QUE FAZ A VALIDAÇÃO SE O USUÁRIO ESTÁ LOGADO NO SISTEMA
//SE TENTAR ACESSAR ALGUMA PÁGINA DIRETAMENTE, ELE DIRECIONA PARA A PÁGINA DE LOGOUT
session_start();
include_once '../bd/conexao.php';
include('protectAdmin.php');

$querySelect2 = "SELECT * 
                 FROM MENU M
                 LEFT JOIN ITEMMENU IM ON IM.ID_MENU = M.ID_MENU
                 ORDER BY M.NM_MENU
                ";

$querySelect = $pdoCAT->query($querySelect2);

$menus = [];

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) :
    $idMenu = $registros['ID_MENU'];
    $nmMenu = $registros['NM_MENU'];
    $simboloMenu = $registros['SIMBOLO_MENU'];
    $idItemMenu = $registros['ID_ITEMMENU'];
    $nmItemMenu = $registros['NM_ITEMMENU'];
    $tipoItemMenu = $registros['TIPO_ITEMMENU'];
    $linkItemMenu = $registros['LINK_ITEMMENU'];

    // Cria um array associativo para cada menu
    $menus[$idMenu]['nmMenu'] = $nmMenu;
    $menus[$idMenu]['simboloMenu'] = $simboloMenu;
    // Adiciona os itens do menu ao array de itens associados a esse menu
    $menus[$idMenu]['itens'][] = [
        'idItemMenu' => $idItemMenu,
        'nmItemMenu' => $nmItemMenu,
        'tipoItemMenu' => $tipoItemMenu,
        'linkItemMenu' => $linkItemMenu
    ];
endwhile;

?>

<input type="checkbox" id="check"> <!--deixa o menu fechado-->

<header class="header2">
    <label class="label2" for="check">
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
            <label class="userLogin">Bem-Vindo, <?php echo $_SESSION['login'] ?></label>
            <!-- <a href="logout.php" class="sair_btn"><ion-icon name="exit-outline"></ion-icon></a> -->
        <?php } else { ?>
            <!-- <a href="login.php" class="sair_btn"><ion-icon name="exit-outline"></ion-icon></a> -->
        <?php } ?>
    </div>
</header>

<!--========== MENU 1 ==========-->
<div class="nav" id="navbar">

    <div class="nav__items">
        <a href="consultarLicitacao.php" class="nav__link">
            <i class='material-icons' title="Consultar Licitação">playlist_add_check</i>
            <span class="nav__name">Licitações</span>
        </a>

        <?php foreach ($menus as $menu) : ?>
            <!-- submenus -->
            <div class="nav__dropdown">
                <a href="<?php echo $menu['linkMenu']; ?>" class="nav__link">
                    <i class='material-icons' title="Administrar Perfis"><?php echo $menu['simboloMenu'] ?></i>
                    <span class="nav__name"><?php echo $menu['nmMenu']; ?></span>
                    <i class='bx bx-chevron-down nav__icon nav__dropdown-icon'></i>
                </a>
                <div class="nav__dropdown-collapse">
                    <div class="nav__dropdown-content">
                        <?php
                        // Itera sobre os itens do menu
                        foreach ($menu['itens'] as $item) : ?>
                            <?php if ($item['tipoItemMenu'] == 'link') { ?>
                                <!-- Se for um link padrão -->
                                <a href="<?php echo $item['linkItemMenu']; ?>" target="_blank" class="nav__dropdown-item"><?php echo $item['nmItemMenu']; ?></a>
                            <?php } elseif ($item['tipoItemMenu'] == 'modal') { ?>
                                <!-- Se for um item que abre um modal -->
                                <a href="javascript:void(0);" class="nav__dropdown-item" data-title="<?php echo $item['nmItemMenu']; ?>" data-content="<?php echo htmlspecialchars($item['linkItemMenu']); ?>"><?php echo $item['nmItemMenu']; ?></a>
                            <?php } ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php
        if ($_SESSION['admin'] == 5) { ?>
            <hr style="width: 20px">
            <a href="consultarUsuario.php" class="nav__link">
                <i class='material-icons' title="Administrar Usuários">manage_accounts</i>
                <span class="nav__name">Administrar Usuário</span>
            </a>

            <a href="cadPerfil.php" class="nav__link">
                <i class='material-icons' title="Administrar Perfis">people</i>
                <span class="nav__name">Administrar Perfil</span>
            </a>

        <?php } ?>

        <a href="login.php" class="nav__link nav__logout">
            <i class='bx bx-log-out nav__icon'></i>
            <span class="nav__name">Log Out</span>
        </a>
    </div>
</div>
<!--========== FIM MENU 1 ==========-->

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
      <li><a href="#">Item 1</a>
        <ul>
          <li><a href="#">Subitem 1.1</a>
            <ul>
              <li><a href="#">Subitem 1.1.1</a></li>
              <li><a href="#">Subitem 1.1.2</a></li>
            </ul>
          </li>
          <li><a href="#">Subitem 1.2</a>
            <ul>
              <li><a href="#">Subitem 1.2.1</a></li>
              <li><a href="#">Subitem 1.2.2</a></li>
            </ul>
          </li>
        </ul>
      </li>
      <li><a href="#">Item 2</a>
        <ul>
          <li><a href="#">Subitem 2.1</a>
            <ul>
              <li><a href="#">Subitem 2.1.1</a></li>
              <li><a href="#">Subitem 2.1.2</a></li>
            </ul>
          </li>
          <li><a href="#">Subitem 2.2</a>
            <ul>
              <li><a href="#">Subitem 2.2.1</a></li>
              <li><a href="#">Subitem 2.2.2</a></li>
            </ul>
          </li>
        </ul>
      </li>
      <li><a href="#">Item 3</a>
        <ul>
          <li><a href="#">Subitem 3.1</a>
            <ul>
              <li><a href="#">Subitem 3.1.1</a></li>
              <li><a href="#">Subitem 3.1.2</a></li>
            </ul>
          </li>
          <li><a href="#">Subitem 3.2</a>
            <ul>
              <li><a href="#">Subitem 3.2.1</a></li>
              <li><a href="#">Subitem 3.2.2</a></li>
            </ul>
          </li>
        </ul>
      </li>
      <!-- Adicione mais itens e subitens conforme necessário -->
    </ul>
  </div>



<!--========== MAIN JS ==========-->
<script src="../materialize/js/main.js"></script>

<script>
    // Adicionar um listener para todos os links com a classe nav__dropdown-item
    document.querySelectorAll('.nav__dropdown-item').forEach(function(link) {
        link.addEventListener('click', function() {
            // Obter os valores dos atributos data-*
            var title = this.getAttribute('data-title');
            var content = this.getAttribute('data-content');

            // Chamar a função showModal com os valores obtidos
            showModal(title, content);
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
</script>