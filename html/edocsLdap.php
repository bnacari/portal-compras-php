<?php

$ldap_host = 'cesan.com.br';
$dominio = '@cesan.com.br'; //Dominio local ou global
$ldap_user = 'bruno.nacari' . $dominio;
$ldap_port = '389';
$ldap_pass = 'Cesan2020@'; // Senha do usuário

// Conectando ao servidor LDAP
$ldap_conn = ldap_connect($ldap_host, $ldap_port);

// Verificando se a conexão foi estabelecida com sucesso
if (!$ldap_conn) {
    die("Falha ao conectar ao servidor LDAP.");
}

// Configurando opções de LDAP
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

// Realizando a autenticação no servidor LDAP
$ldap_bind = ldap_bind($ldap_conn, $ldap_user, $ldap_pass);

// Verificando se a autenticação foi bem-sucedida
if (!$ldap_bind) {
    die("Falha ao autenticar no servidor LDAP.");
}

// Consulta LDAP para buscar todos os usuários dentro da unidade organizacional "OU=Organograma" e suas sub-OUs
$ldap_base_dn = "OU=Organograma,OU=CESAN,DC=cesan,DC=com,DC=br";
$ldap_filter = "(|(objectClass=user)(objectClass=organizationalUnit))"; // Filtra tanto usuários quanto grupos
$ldap_search = ldap_search($ldap_conn, $ldap_base_dn, $ldap_filter, ["cn", "mail"]);
$ldap_entries = ldap_get_entries($ldap_conn, $ldap_search);

// Verificando se há resultados
if ($ldap_entries['count'] == 0) {
    die("Nenhum usuário encontrado na unidade organizacional 'OU=Organograma'.");
}

// Iterando sobre os resultados e exibindo informações dos usuários
for ($i = 0; $i < $ldap_entries['count']; $i++) {
    echo "Usuário " . ($i + 1) . "<br>";
    foreach ($ldap_entries[$i] as $key => $value) {
        // Ignora atributos especiais retornados pelo LDAP
        if (!is_numeric($key)) {
            echo "$key: ";
            // Se o atributo tiver vários valores, os exibe em uma lista
            if (is_array($value)) {
                echo implode(", ", $value);
            } else {
                echo $value;
            }
            echo "<br>";
        }
    }
    if ($ldap_entries[$i]['useraccountcontrol'][0] & 2) {
        echo "Status da conta: Desabilitado<br>";
    } else {
        echo "Status da conta: Habilitado<br>";
    }
    echo "<br>";
}

$resultados = array();

// Iterando sobre os resultados e construindo o array no formato desejado
for ($i = 0; $i < $ldap_entries['count']; $i++) {
    $usuario = array();
    
    // Adiciona os campos relevantes para cada usuário
    $usuario["chaveExterna"] = $ldap_entries[$i]['cn'][0]; // Usando 'cn' como chave externa, você pode ajustar conforme necessário
    $usuario["sigla"] = ""; // Coloque a sigla aqui
    $usuario["chaveExternaUnidadeGestora"] = ""; // Coloque a chave externa da unidade gestora aqui
    
    // Verifica se é uma unidade organizacional
    if ($ldap_entries[$i]['objectclass'][0] == 'organizationalUnit') {
        // Configura os dados da unidade organizacional
        $usuario["nome"] = $ldap_entries[$i]['cn'][0]; // Usando 'cn' como nome da unidade organizacional, você pode ajustar conforme necessário
        // Adicione outros campos da unidade organizacional conforme necessário
    } else {
        // Configura os dados do usuário
        $usuario["listaOcupacoes"] = array(); // Inicializa a lista de ocupações (vazia para usuários)
        // Adicione outros campos do usuário conforme necessário
    }

    // Adiciona o usuário ao array de resultados
    $resultados[] = $usuario;
}

// Converte o array para JSON
$json_resultados = json_encode($resultados, JSON_PRETTY_PRINT);

// Imprime o JSON resultante
echo $json_resultados;

// Fechando a conexão LDAP
ldap_close($ldap_conn);

?>