<?php
include_once 'bd/conexao.php';

$querySelect2 = "SELECT top 1 * 
                FROM [ADCache].[dbo].Users U
                LEFT JOIN [ADCache].[dbo].[OrganizationalUnits] UNI on UNI.OUName = U.department
                LEFT JOIN [ADCache].[dbo].Managers M on M.initials = U.initials
                WHERE U.sAMAccountName IS NOT NULL
                AND U.company IS NOT NULL
                AND U.IsEnabled = 1
                AND (U.accountExpires > GETDATE() OR U.accountExpires IS NULL)
                ORDER BY U.displayName";

$querySelect = $pdoCAT->query($querySelect2);

$data = array();

while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
    // Crie a estrutura de dados conforme necessário
    $ocupacao = array(
        "chaveExterna" => $registros['ID'],
        "nome" => $registros['displayName'],
        "nomeFeminino" => $registros['displayName'] 
    );

    $papel = array(
        "cpf" => $registros['ID'], 
        "nomeCidadao" => $registros['displayName'], 
        "chaveExternaOcupacao" => $registros['ID'], 
        "prioritario" => true, 
        "gestorLotacao" => true, 
        "situacao" => 0 
    );

    // Adicione os dados ao array principal
    $data[] = array(
        "idOrganograma" => $registros['ID'], 
        "unidadeGestora" => $registros['ID'], 
        "listaOcupacoes" => array($ocupacao),
        "unidadesFilhas" => array(
            array(
                "idOrganograma" => $registros['ID'], 
                "unidadesFilhas" => array($registros['ID']), 
                "papeis" => array($papel),
                "grupos" => array(
                    array(
                        "chaveExterna" => $registros['ID'], 
                        "nome" => $registros['displayName'], 
                        "papeis" => array($papel),
                        "tipo" => 0 
                    )
                )
            )
        )
    );
}

$json = json_encode($data, JSON_PRETTY_PRINT);

header('Content-Type: application/json');

echo $json;

$jsonData = json_encode($data);

// URL do endpoint
$url = 'https://api.cargarh.hom.es.gov.br/v2/Carga/organizacao/b5488dcc-3a6a-4f12-a3af-8b78e93ce9cc';

// Inicializa uma nova sessão cURL
$curl = curl_init($url);

// Configura as opções da requisição cURL
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

// Executa a requisição cURL
$response = curl_exec($curl);

// Verifica se ocorreu algum erro durante a requisição
if ($response === false) {
    $error = curl_error($curl);
    // Trate o erro conforme necessário
} else {
    // Processa a resposta
    // Você pode imprimir a resposta ou fazer qualquer outra coisa com ela
    echo $response;
}

// Fecha a sessão cURL
curl_close($curl);
?>

?>
