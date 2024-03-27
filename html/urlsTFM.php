<?php

// Caminho do arquivo
$arquivoEdoc = "https://tfm.sistemas.cesan.com.br/files/e-doc/portal.txt";
$arquivoAdcs = "https://tfm.sistemas.cesan.com.br/files/a-dcs/portal.txt";

// Lê o conteúdo do arquivo
$conteudoEdoc = file_get_contents($arquivoEdoc);
$conteudoAdcs = file_get_contents($arquivoAdcs);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Encontra a posição da string "tabela_precos="
$tabela_precos_pos = strpos($conteudoEdoc, "tabela_precos=");
// Encontra a posição do próximo caractere após "tabela_precos="
$tabela_precos_valor_pos = $tabela_precos_pos + strlen("tabela_precos=");
// Encontra a posição da quebra de linha ou final do arquivo a partir do valor de "tabela_precos="
$tabela_precos_end_pos = strpos($conteudoEdoc, "\n", $tabela_precos_valor_pos);
if ($tabela_precos_end_pos === false) {
    $tabela_precos_end_pos = strlen($conteudoEdoc);
}
// Obtém o valor de "tabela_precos"
$tabelaPrecos = substr($conteudoEdoc, $tabela_precos_valor_pos, $tabela_precos_end_pos - $tabela_precos_valor_pos);
// Remove possíveis caracteres indesejados
$tabelaPrecos = trim($tabelaPrecos);
$_SESSION['tabelaPrecos'] = 'https://tfm.sistemas.cesan.com.br/files/e-doc/' . $tabelaPrecos;

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Encontra a posição da string "prescricoes_tecnicas="
$prescricoes_tecnicas_pos = strpos($conteudoEdoc, "prescricoes_tecnicas=");
// Encontra a posição do próximo caractere após "prescricoes_tecnicas="
$prescricoes_tecnicas_valor_pos = $prescricoes_tecnicas_pos + strlen("prescricoes_tecnicas=");
// Encontra a posição da quebra de linha ou final do arquivo a partir do valor de "prescricoes_tecnicas="
$prescricoes_tecnicas_end_pos = strpos($conteudoEdoc, "\n", $prescricoes_tecnicas_valor_pos);
if ($prescricoes_tecnicas_end_pos === false) {
    $prescricoes_tecnicas_end_pos = strlen($conteudoEdoc);
}
// Obtém o valor de "prescricoes_tecnicas"
$prescricoesTecnicas = substr($conteudoEdoc, $prescricoes_tecnicas_valor_pos, $prescricoes_tecnicas_end_pos - $prescricoes_tecnicas_valor_pos);
// Remove possíveis caracteres indesejados
$prescricoesTecnicas = trim($prescricoesTecnicas);
$_SESSION['prescricoesTecnicas'] = 'https://tfm.sistemas.cesan.com.br/files/e-doc/' . $prescricoesTecnicas;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Encontra a posição da string "tabela_precos="
$dispensas_pos = strpos($conteudoAdcs, "dispensas_licitacao=");
// Encontra a posição do próximo caractere após "tabela_precos="
$dispensas_valor_pos = $dispensas_pos + strlen("dispensas_licitacao=");
// Encontra a posição da quebra de linha ou final do arquivo a partir do valor de "tabela_precos="
$dispensas_end_pos = strpos($conteudoAdcs, "\n", $dispensas_valor_pos);
if ($dispensas_end_pos === false) {
    $dispensas_end_pos = strlen($conteudoAdcs);
}
// Obtém o valor de "tabela_precos"
$dispensasLicitacao = substr($conteudoAdcs, $dispensas_valor_pos, $dispensas_end_pos - $dispensas_valor_pos);
// Remove possíveis caracteres indesejados
$dispensasLicitacao = trim($dispensasLicitacao);
$_SESSION['dispensasLicitacao'] = 'https://tfm.sistemas.cesan.com.br/files/a-dcs/' . $dispensasLicitacao;

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Encontra a posição da string "tabela_precos="
$inexigibilidades_pos = strpos($conteudoAdcs, "inexigibilidades=");
// Encontra a posição do próximo caractere após "tabela_precos="
$inexigibilidades_valor_pos = $inexigibilidades_pos + strlen("inexigibilidades=");
// Encontra a posição da quebra de linha ou final do arquivo a partir do valor de "tabela_precos="
$inexigibilidades_end_pos = strpos($conteudoAdcs, "\n", $inexigibilidades_valor_pos);
if ($inexigibilidades_end_pos === false) {
    $inexigibilidades_end_pos = strlen($conteudoAdcs);
}
// Obtém o valor de "tabela_precos"
$inexigibilidades = substr($conteudoAdcs, $inexigibilidades_valor_pos, $inexigibilidades_end_pos - $inexigibilidades_valor_pos);
// Remove possíveis caracteres indesejados
$inexigibilidades = trim($inexigibilidades);
$_SESSION['inexigibilidades'] = 'https://tfm.sistemas.cesan.com.br/files/a-dcs/' . $inexigibilidades;

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Exibe os valores obtidos
echo "Valor de tabela_precos: $tabelaPrecos\n";
echo "Valor de prescricoes_tecnicas: $prescricoesTecnicas\n";

echo "Dispensa: $dispensasLicitacao\n";
echo "inexigibilidades: $inexigibilidades\n";


?>
