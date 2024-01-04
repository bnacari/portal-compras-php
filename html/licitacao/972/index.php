

<?php
    // GAMBIARRA FEITA PARA REDIRECIONAR PARA A 
    // PÁGINA ABAIXO CASO O USUÁRIO TENTE ACESSAR "http://portal-de-compras.sistemas.cesan.com.br/licitacao/972/"
    
    $url = "https://compras.cesan.com.br/viewLicitacao.php?idLicitacao=39783";
    header("Location: $url");
    exit();
?>