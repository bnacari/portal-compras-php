<?php

session_start();

include_once 'bd/conexao.php';
include_once 'redirecionar.php';

$telaLogin = 0;
$testemunha = 0;

if(isset($_POST["loginEnvolvido"])) {
    //vem da tela de cadastro de OCORRENCIA
    $loginEnvolvido = $_POST["loginEnvolvido"];

} else if (isset($_POST["loginTestemunha"])){
    $loginEnvolvido = $_POST["loginTestemunha"];
    $testemunha = 1;

} else {
    //vem da tela de LOGIN
    $loginEnvolvido = $_SESSION['login'];
    $telaLogin = 1;
}

$apiUrl = 'https://api-estorg.sistemas.cesan.com.br:8443/v1/employees/findByLogon?logon='.$loginEnvolvido;

$ch = curl_init();

// Configuração da requisição cURL
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Adicione opções de autenticação ou cabeçalhos, se necessário

$response = curl_exec($ch);

if ($response === false) {
    echo 'Erro na requisição cURL: ' . curl_error($ch);
} else {
    // $response conterá os dados da resposta da API
    $data = json_decode($response);

    //pego informações da UNIDADE do usuário ///////////////////////////////////////////////////////////////////////////////////////
    $apiUrlUnidade = 'https://api-estorg.sistemas.cesan.com.br:8443/v1/org-units/'.$data->orgUnitId;
    
    $chUnidade = curl_init();

    // Configuração da requisição cURL
    curl_setopt($chUnidade, CURLOPT_URL, $apiUrlUnidade);
    curl_setopt($chUnidade, CURLOPT_RETURNTRANSFER, true);
    // Adicione opções de autenticação ou cabeçalhos, se necessário

    $responseUnidade = curl_exec($chUnidade);
    $dataUnidade = json_decode($responseUnidade);
    //FIM informações da UNIDADE do usuário ///////////////////////////////////////////////////////////////////////////////////////

    if($telaLogin == 1){
        $_SESSION['cargoLogin'] = $data->position;;
    }

    if(!isset($data->name)){
        if($testemunha == 1){
            $_SESSION['matTestemunha'] = '';
        } else {
            $_SESSION['emailEnvolvido'] = '';
            $_SESSION['loginEnvolvido'] = '';
            $_SESSION['loginTestemunha'] = '';
            $_SESSION['matriculaEnvolvido'] = '';
            $_SESSION['cargoEnvolvido'] = '';
            $_SESSION['unidadeEnvolvido'] = '';
        }
        
        http_response_code(500); // Define o código de erro HTTP (500 - Internal Server Error)
        exit();
    }

    echo "<table class='rTable'>";
    echo "<thead><tr><th>Nome</th><th>Unidade</th><th>Matrícula</th><th>E-mail</th><th>Cargo</th></tr></thead><tbody><tr>";
    echo "<td>$data->name</td>";
    echo "<td>$dataUnidade->acronym - $dataUnidade->name</td>";
    echo "<td>$data->personnelNumber</td>";
    echo "<td>$data->email</td>";
    echo "<td>$data->position</td>";
    echo "</tr></tbody></table>";

    // echo "Nome: " . $data->name . "<br>";
    // echo "Unidade: " . $dataUnidade->acronym . " - " . $dataUnidade->name ."<br>";
    // echo "Matrícula: " . $data->personnelNumber . "<br>";
    // // echo "Login: " . $data->logon . "<br>";
    // echo "E-mail: " . $data->email . "<br>";
    // echo "Cargo: " . $data->position . "<br>";


    if ($testemunha == 1){

        $_SESSION['matTestemunha'] = $data->personnelNumber;
        // echo ($_SESSION['matTestemunha']);

    } else {
        
        $_SESSION['emailEnvolvido'] = $data->email;
        $_SESSION['loginEnvolvido'] = $data->name;
        $_SESSION['matriculaEnvolvido'] = $data->personnelNumber;
        $_SESSION['cargoEnvolvido'] = $data->position;
        $_SESSION['unidadeEnvolvido'] = $dataUnidade->acronym;
        // echo ($_SESSION['matriculaEnvolvido']);

    }
    
    $_SESSION['redirecionar'] = '../consultarLicitacao.php';

    $tela = 'login';
    $acao = 'Sucesso';
    $evento = 0;

    if($telaLogin == 1){
        redirecionar($_SESSION['redirecionar']);
    }

    // $dataPhoto = json_decode($response, true);

    // if ($dataPhoto && isset($dataPhoto['photo']['data'])) {
    //     $photoData = $dataPhoto['photo']['data'];

    //     // Exibe a imagem usando os dados da imagem em base64
    //     echo "<img src='data:image/jpeg;base64,$photoData' alt='Foto do funcionário'>";
    // } else {
    //     echo "Dados inválidos ou foto não encontrada.";
    // }
}

curl_close($ch);
curl_close($chUnidade);


?>
