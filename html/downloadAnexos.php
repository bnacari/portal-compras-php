<?php
/**
 * Download de todos os anexos de uma licitação em formato ZIP
 */

session_start();
include_once 'bd/conexao.php';

$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

if (!$idLicitacao) {
    die('ID da licitação não informado.');
}

// Buscar informações da licitação para nome do arquivo
$queryLicitacao = $pdoCAT->query("
    SELECT L.ID_LICITACAO, TIPO.SGL_TIPO, DET.COD_LICITACAO 
    FROM [PortalCompras].[dbo].[LICITACAO] L
    LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
    LEFT JOIN TIPO_LICITACAO TIPO ON TIPO.ID_TIPO = DET.TIPO_LICITACAO
    WHERE L.ID_LICITACAO = $idLicitacao
");
$dadosLicitacao = $queryLicitacao->fetch(PDO::FETCH_ASSOC);

// Nome do arquivo ZIP
$nomeArquivo = 'Anexos';
if ($dadosLicitacao) {
    $sigla = $dadosLicitacao['SGL_TIPO'] ?? '';
    $codigo = $dadosLicitacao['COD_LICITACAO'] ?? '';
    if ($sigla && $codigo) {
        $nomeArquivo = $sigla . '_' . $codigo . '_Anexos';
    }
}
$nomeArquivo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nomeArquivo);

// Coletar todos os anexos
$anexos = array();
$directory = "uploads/" . $idLicitacao;

// Anexos do banco de dados (licitações 13.303)
if ($idLicitacao > 2000) {
    $queryAnexo = "WITH RankedAnexos AS (
                        SELECT
                            ID_LICITACAO,
                            NM_ANEXO,
                            LINK_ANEXO,
                            ROW_NUMBER() OVER (PARTITION BY ID_LICITACAO, CASE WHEN NM_ANEXO LIKE '%_descricao' THEN 1 ELSE 2 END ORDER BY NM_ANEXO) AS rn
                        FROM ANEXO
                        WHERE ID_LICITACAO = $idLicitacao
                        AND DT_EXC_ANEXO IS NULL
                    )
                    SELECT
                        ID_LICITACAO,
                        MAX(CASE WHEN NM_ANEXO like '%_descricao' THEN LINK_ANEXO END) AS NM_ANEXO,
                        MAX(CASE WHEN NM_ANEXO like '%_arquivo' THEN LINK_ANEXO END) AS LINK_ANEXO
                    FROM RankedAnexos
                    GROUP BY ID_LICITACAO, rn;";
} else {
    $queryAnexo = "SELECT ID_LICITACAO, NM_ANEXO, LINK_ANEXO FROM ANEXO WHERE ID_LICITACAO = $idLicitacao AND DT_EXC_ANEXO IS NULL";
}

$queryAnexo2 = $pdoCAT->query($queryAnexo);

while ($registros = $queryAnexo2->fetch(PDO::FETCH_ASSOC)) {
    if (!empty($registros['LINK_ANEXO'])) {
        $anexos[] = array(
            'nome' => $registros['NM_ANEXO'] ?? basename($registros['LINK_ANEXO']),
            'caminho' => $registros['LINK_ANEXO']
        );
    }
}

// Anexos do diretório físico
if (is_dir($directory)) {
    $files = scandir($directory);
    $files = array_diff($files, array('.', '..'));

    foreach ($files as $file) {
        $caminhoCompleto = $directory . '/' . $file;
        if (is_file($caminhoCompleto)) {
            $anexos[] = array(
                'nome' => $file,
                'caminho' => $caminhoCompleto
            );
        }
    }
}

// Verificar se há anexos
if (empty($anexos)) {
    die('Nenhum anexo encontrado para esta licitação.');
}

// Criar arquivo ZIP
$zipFileName = sys_get_temp_dir() . '/' . $nomeArquivo . '_' . time() . '.zip';
$zip = new ZipArchive();

if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die('Não foi possível criar o arquivo ZIP.');
}

$arquivosAdicionados = 0;
$nomesUsados = array();

foreach ($anexos as $anexo) {
    $caminhoArquivo = $anexo['caminho'];
    $nomeNoZip = $anexo['nome'];
    
    // Verificar se é URL externa ou arquivo local
    if (filter_var($caminhoArquivo, FILTER_VALIDATE_URL)) {
        // É uma URL - tentar baixar o conteúdo
        $conteudo = @file_get_contents($caminhoArquivo);
        if ($conteudo !== false) {
            // Evitar nomes duplicados
            $nomeOriginal = $nomeNoZip ?: basename(parse_url($caminhoArquivo, PHP_URL_PATH));
            $nomeNoZip = $nomeOriginal;
            $contador = 1;
            while (in_array($nomeNoZip, $nomesUsados)) {
                $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                $nomeBase = pathinfo($nomeOriginal, PATHINFO_FILENAME);
                $nomeNoZip = $nomeBase . '_' . $contador . '.' . $extensao;
                $contador++;
            }
            $nomesUsados[] = $nomeNoZip;
            
            $zip->addFromString($nomeNoZip, $conteudo);
            $arquivosAdicionados++;
        }
    } else {
        // É um arquivo local
        if (file_exists($caminhoArquivo) && is_file($caminhoArquivo)) {
            // Evitar nomes duplicados
            $nomeOriginal = $nomeNoZip ?: basename($caminhoArquivo);
            $nomeNoZip = $nomeOriginal;
            $contador = 1;
            while (in_array($nomeNoZip, $nomesUsados)) {
                $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                $nomeBase = pathinfo($nomeOriginal, PATHINFO_FILENAME);
                $nomeNoZip = $nomeBase . '_' . $contador . '.' . $extensao;
                $contador++;
            }
            $nomesUsados[] = $nomeNoZip;
            
            $zip->addFile($caminhoArquivo, $nomeNoZip);
            $arquivosAdicionados++;
        }
    }
}

$zip->close();

// Verificar se algum arquivo foi adicionado
if ($arquivosAdicionados == 0) {
    unlink($zipFileName);
    die('Não foi possível adicionar nenhum arquivo ao ZIP.');
}

// Enviar o arquivo para download
if (file_exists($zipFileName)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $nomeArquivo . '.zip"');
    header('Content-Length: ' . filesize($zipFileName));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    readfile($zipFileName);
    
    // Remover arquivo temporário
    unlink($zipFileName);
    exit;
} else {
    die('Erro ao gerar o arquivo ZIP.');
}