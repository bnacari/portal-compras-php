<?php
/**
 * Download de todos os anexos de uma licitação em formato ZIP
 */

// Iniciar sessão e includes
session_start();
include_once 'bd/conexao.php';

// Função auxiliar para log de erros
function logError($message) {
    error_log("[DownloadAnexos] " . $message);
}

// Capturar ID da licitação
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

if (!$idLicitacao) {
    logError("ID da licitação não informado");
    die('Erro: ID da licitação não informado.');
}

logError("Iniciando download para licitação ID: " . $idLicitacao);

try {
    // Buscar informações da licitação
    $queryLicitacao = $pdoCAT->query("
        SELECT L.ID_LICITACAO, TIPO.SGL_TIPO, DET.COD_LICITACAO 
        FROM [PortalCompras].[dbo].[LICITACAO] L
        LEFT JOIN DETALHE_LICITACAO DET ON DET.ID_LICITACAO = L.ID_LICITACAO
        LEFT JOIN TIPO_LICITACAO TIPO ON TIPO.ID_TIPO = DET.TIPO_LICITACAO
        WHERE L.ID_LICITACAO = $idLicitacao
    ");
    $dadosLicitacao = $queryLicitacao->fetch(PDO::FETCH_ASSOC);

    // Nome do arquivo ZIP
    $nomeArquivo = 'Anexos_Licitacao_' . $idLicitacao;
    if ($dadosLicitacao && !empty($dadosLicitacao['SGL_TIPO']) && !empty($dadosLicitacao['COD_LICITACAO'])) {
        $nomeArquivo = $dadosLicitacao['SGL_TIPO'] . '_' . $dadosLicitacao['COD_LICITACAO'] . '_Anexos';
    }
    $nomeArquivo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nomeArquivo);
    
    logError("Nome do arquivo ZIP: " . $nomeArquivo);

    // Coletar todos os anexos
    $anexos = array();
    $directory = "uploads/" . $idLicitacao;

    // 1. Anexos do banco de dados
    logError("Buscando anexos do banco de dados...");
    
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
            $nome = $registros['NM_ANEXO'] ?? basename(parse_url($registros['LINK_ANEXO'], PHP_URL_PATH));
            $anexos[] = array(
                'nome' => $nome,
                'caminho' => $registros['LINK_ANEXO'],
                'tipo' => 'banco'
            );
            logError("Anexo do banco: " . $nome . " -> " . $registros['LINK_ANEXO']);
        }
    }

    // 2. Anexos do diretório físico
    logError("Buscando anexos no diretório: " . $directory);
    
    if (is_dir($directory)) {
        $files = scandir($directory);
        $files = array_diff($files, array('.', '..'));

        foreach ($files as $file) {
            $caminhoCompleto = $directory . '/' . $file;
            if (is_file($caminhoCompleto)) {
                $anexos[] = array(
                    'nome' => $file,
                    'caminho' => $caminhoCompleto,
                    'tipo' => 'diretorio'
                );
                logError("Anexo do diretório: " . $file);
            }
        }
    } else {
        logError("Diretório não existe: " . $directory);
    }

    // Verificar se há anexos
    if (empty($anexos)) {
        logError("Nenhum anexo encontrado");
        die('Nenhum anexo encontrado para esta licitação.');
    }

    logError("Total de anexos encontrados: " . count($anexos));

    // Criar arquivo ZIP temporário
    $zipFileName = sys_get_temp_dir() . '/' . $nomeArquivo . '_' . time() . '.zip';
    logError("Criando arquivo ZIP: " . $zipFileName);
    
    $zip = new ZipArchive();

    if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        logError("Não foi possível criar o arquivo ZIP");
        die('Não foi possível criar o arquivo ZIP.');
    }

    $arquivosAdicionados = 0;
    $nomesUsados = array();
    $erros = array();

    foreach ($anexos as $anexo) {
        $caminhoArquivo = $anexo['caminho'];
        $nomeOriginal = $anexo['nome'];
        
        logError("Processando: " . $nomeOriginal . " (tipo: " . $anexo['tipo'] . ")");
        
        try {
            // Verificar se é URL externa ou arquivo local
            if (filter_var($caminhoArquivo, FILTER_VALIDATE_URL)) {
                logError("URL detectada: " . $caminhoArquivo);
                
                // Configurar contexto para permitir SSL inseguro (apenas para desenvolvimento)
                $context = stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ]);
                
                // Tentar baixar o conteúdo
                $conteudo = @file_get_contents($caminhoArquivo, false, $context);
                
                if ($conteudo === false) {
                    $erro = "Falha ao baixar URL: " . $caminhoArquivo;
                    logError($erro);
                    $erros[] = $erro;
                    continue;
                }
                
                // Garantir nome único
                $nomeNoZip = $nomeOriginal;
                $contador = 1;
                while (in_array($nomeNoZip, $nomesUsados)) {
                    $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                    $nomeBase = pathinfo($nomeOriginal, PATHINFO_FILENAME);
                    $nomeNoZip = $nomeBase . '_' . $contador . ($extensao ? '.' . $extensao : '');
                    $contador++;
                }
                $nomesUsados[] = $nomeNoZip;
                
                $zip->addFromString($nomeNoZip, $conteudo);
                $arquivosAdicionados++;
                logError("URL adicionada com sucesso: " . $nomeNoZip);
                
            } else {
                // É um arquivo local
                logError("Arquivo local: " . $caminhoArquivo);
                
                if (!file_exists($caminhoArquivo)) {
                    $erro = "Arquivo não existe: " . $caminhoArquivo;
                    logError($erro);
                    $erros[] = $erro;
                    continue;
                }
                
                if (!is_file($caminhoArquivo)) {
                    $erro = "Não é um arquivo: " . $caminhoArquivo;
                    logError($erro);
                    $erros[] = $erro;
                    continue;
                }
                
                // Garantir nome único
                $nomeNoZip = $nomeOriginal;
                $contador = 1;
                while (in_array($nomeNoZip, $nomesUsados)) {
                    $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                    $nomeBase = pathinfo($nomeOriginal, PATHINFO_FILENAME);
                    $nomeNoZip = $nomeBase . '_' . $contador . ($extensao ? '.' . $extensao : '');
                    $contador++;
                }
                $nomesUsados[] = $nomeNoZip;
                
                if ($zip->addFile($caminhoArquivo, $nomeNoZip)) {
                    $arquivosAdicionados++;
                    logError("Arquivo local adicionado com sucesso: " . $nomeNoZip);
                } else {
                    $erro = "Falha ao adicionar arquivo: " . $caminhoArquivo;
                    logError($erro);
                    $erros[] = $erro;
                }
            }
            
        } catch (Exception $e) {
            $erro = "Exceção ao processar " . $nomeOriginal . ": " . $e->getMessage();
            logError($erro);
            $erros[] = $erro;
        }
    }

    $zip->close();
    
    logError("Arquivos adicionados ao ZIP: " . $arquivosAdicionados);

    // Verificar se algum arquivo foi adicionado
    if ($arquivosAdicionados == 0) {
        @unlink($zipFileName);
        logError("Nenhum arquivo foi adicionado ao ZIP");
        
        $mensagemErro = "Não foi possível adicionar nenhum arquivo ao ZIP.";
        if (!empty($erros)) {
            $mensagemErro .= "\n\nErros encontrados:\n" . implode("\n", $erros);
        }
        die($mensagemErro);
    }

    // Verificar se o arquivo ZIP foi criado
    if (!file_exists($zipFileName)) {
        logError("Arquivo ZIP não foi criado: " . $zipFileName);
        die('Erro ao gerar o arquivo ZIP.');
    }

    $tamanhoZip = filesize($zipFileName);
    logError("Arquivo ZIP criado com sucesso. Tamanho: " . $tamanhoZip . " bytes");

    // Limpar qualquer saída anterior
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Enviar o arquivo para download
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $nomeArquivo . '.zip"');
    header('Content-Length: ' . $tamanhoZip);
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Ler e enviar o arquivo
    readfile($zipFileName);
    
    // Remover arquivo temporário
    @unlink($zipFileName);
    
    logError("Download concluído com sucesso");
    exit;

} catch (Exception $e) {
    logError("Exceção geral: " . $e->getMessage());
    die('Erro ao processar download: ' . $e->getMessage());
}
?>