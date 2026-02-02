<?php

session_start();
include_once 'bd/conexao.php';

// Função de envio de e-mail via socket SMTP
function sendEmail($to, $subject, $message, $from, $host, $port = 25)
{
    // Conectar ao servidor SMTP
    $socket = fsockopen($host, $port, $errno, $errstr, 30);
    if (!$socket) {
        return false;
    }

    // Leitura da resposta inicial do servidor
    fgets($socket, 512);

    // Inicia a comunicação SMTP
    fputs($socket, "HELO $host\r\n");
    fgets($socket, 512);

    // Define o remetente
    fputs($socket, "MAIL FROM:<$from>\r\n");
    fgets($socket, 512);

    // Define o destinatário
    $recipients = explode(',', $to);
    foreach ($recipients as $recipient) {
        $recipient = trim($recipient);
        if (!empty($recipient)) {
            fputs($socket, "RCPT TO:<$recipient>\r\n");
            fgets($socket, 512);
        }
    }

    // Inicia o envio de dados
    fputs($socket, "DATA\r\n");
    fgets($socket, 512);

    // Define o cabeçalho e o corpo do e-mail
    $headers = "From: $from\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    fputs($socket, $headers . "\r\n" . $message . "\r\n.\r\n");
    fgets($socket, 512);

    // Finaliza a conexão
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    
    return true;
}

// Configurações SMTP
$smtpHost = 'app-mail.sistemas.cesan.com.br';
$smtpPort = 25;
$emailFrom = 'compras@cesan.com.br';

// Parâmetros da requisição
$emailUsuario = filter_input(INPUT_GET, 'emailUsuario', FILTER_SANITIZE_SPECIAL_CHARS);
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

// Links do sistema
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

$linkLicitacao = "$protocolo://$host/licitacaoView.php?idLicitacao=" . $idLicitacao;
$linkLogin = "$protocolo://$host/login.php";
$linkTrocaSenha = "$protocolo://$host/trocaSenhaUsuario.php";

// =============================================================================================================
// SE FOR RECUPERAÇÃO DE SENHA
// =============================================================================================================
if (isset($emailUsuario) && !empty($emailUsuario)) {

    // Gerar uma senha temporária aleatória (10 caracteres)
    $novaSenha = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10);

    // Criptografar a nova senha temporária
    $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

    // Atualizar a senha no banco de dados
    $queryUpdateSenha = "UPDATE USUARIO SET SENHA = :senha WHERE EMAIL_ADM = :email";
    $stmt = $pdoCAT->prepare($queryUpdateSenha);
    $stmt->bindParam(':senha', $senhaHash, PDO::PARAM_STR);
    $stmt->bindParam(':email', $emailUsuario, PDO::PARAM_STR);
    $stmt->execute();

    // Buscar dados do usuário - Prepared Statement para evitar SQL Injection
    $querySelectPerfil = "SELECT * FROM USUARIO WHERE EMAIL_ADM = :email";
    $querySelectPerfil2 = $pdoCAT->prepare($querySelectPerfil);
    $querySelectPerfil2->bindParam(':email', $emailUsuario, PDO::PARAM_STR);
    $querySelectPerfil2->execute();
    $emailEnviado = false;
    
    while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
        $nmUsuario = $registros['NM_ADM'];
        $email = $registros['EMAIL_ADM'];

        $subject = '=?UTF-8?B?' . base64_encode('Recuperacao de Senha - Portal de Compras CESAN') . '?=';
        
        // Email com visual moderno
        $message = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 24px; font-weight: 700; margin: 0;">
                                Recuperacao de Senha
                            </h1>
                            <p style="color: #94a3b8; font-size: 14px; margin: 8px 0 0 0;">
                                Portal de Compras CESAN
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #334155; font-size: 16px; margin: 0 0 24px 0; line-height: 1.6;">
                                Olá <strong style="color: #0f172a;">' . htmlspecialchars($nmUsuario) . '</strong>,
                            </p>
                            
                            <p style="color: #64748b; font-size: 15px; margin: 0 0 24px 0; line-height: 1.6;">
                                Recebemos uma solicitação de recuperação de senha para sua conta no Portal de Compras da CESAN.
                            </p>
                            
                            <!-- Info Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
                                        <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0;">Nome do Solicitante</p>
                                        <p style="color: #0f172a; font-size: 15px; font-weight: 600; margin: 0 0 16px 0;">' . htmlspecialchars($nmUsuario) . '</p>
                                        <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0;">E-mail</p>
                                        <p style="color: #0f172a; font-size: 15px; font-weight: 600; margin: 0;">' . htmlspecialchars($email) . '</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Password Box -->
                            <p style="color: #64748b; font-size: 14px; margin: 0 0 12px 0; text-align: center;">
                                Sua nova senha temporária:
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="background-color: #dbeafe; border: 2px solid #3b82f6; border-radius: 12px; padding: 20px 32px;">
                                                    <span style="font-size: 28px; font-weight: 700; color: #1d4ed8; letter-spacing: 4px; font-family: Courier New, monospace;">' . $novaSenha . '</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Warning -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 32px;">
                                <tr>
                                    <td style="background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 16px;">
                                        <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.5;">
                                            <strong>Importante:</strong> Apos realizar o login, acesse o menu <a href="' . $linkTrocaSenha . '" style="color: #92400e;">Trocar Senha</a> para definir uma nova senha.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="background-color: #0f172a; border-radius: 10px;">
                                                    <a href="' . $linkLogin . '" style="display: block; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; padding: 14px 32px;">
                                                        Acessar o Portal de Compras
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #94a3b8; font-size: 13px; margin: 24px 0 0 0; text-align: center;">
                                Ou acesse: <a href="' . $linkLogin . '" style="color: #3b82f6;">' . $linkLogin . '</a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 40px; text-align: center;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0 0 8px 0;">
                                Este e um e-mail automatico. Por favor, nao responda.
                            </p>
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                ' . date('Y') . ' CESAN - Companhia Espirito Santense de Saneamento
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        $emailEnviado = sendEmail($email, $subject, $message, $emailFrom, $smtpHost, $smtpPort);
    endwhile;

    if ($emailEnviado) {
        $_SESSION['msg'] = "Senha TEMPORARIA enviada para o e-mail cadastrado.";
    } else if (!isset($email)) {
        $_SESSION['msg'] = "E-mail NAO cadastrado.";
    } else {
        $_SESSION['msg'] = "Erro ao enviar e-mail. Tente novamente.";
    }

    echo "<script>location.href='login.php';</script>";

// =============================================================================================================
// SE FOR ENVIO DE EMAIL PARA ATUALIZAÇÃO EM LICITAÇÃO
// =============================================================================================================
} else if (isset($idLicitacao) && !empty($idLicitacao)) {

    // Prepared Statement para evitar SQL Injection
    $querySelectAtualizacao = "SELECT ADM.NM_ADM, A.EMAIL_ADM, DL.* 
                                FROM ATUALIZACAO A 
                                LEFT JOIN USUARIO ADM ON ADM.ID_ADM = A.ID_ADM
                                LEFT JOIN DETALHE_LICITACAO DL ON A.ID_LICITACAO = DL.ID_LICITACAO
                                WHERE ADM.STATUS = 'A'
                                AND A.DT_EXC_ATUALIZACAO IS NULL
                                AND A.ID_LICITACAO = :idLicitacao";

    $querySelectAtualizacao2 = $pdoCAT->prepare($querySelectAtualizacao);
    $querySelectAtualizacao2->bindParam(':idLicitacao', $idLicitacao, PDO::PARAM_INT);
    $querySelectAtualizacao2->execute();

    while ($registros = $querySelectAtualizacao2->fetch(PDO::FETCH_ASSOC)) :
        $nmUsuario = $registros['NM_ADM'];
        $email = $registros['EMAIL_ADM'];
        $codLicitacao = $registros['COD_LICITACAO'];

        $subject = '=?UTF-8?B?' . base64_encode('Licitacao Atualizada - ' . $codLicitacao) . '?=';
        
        // Email de atualização de licitação
        $message = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f1f5f9;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 24px; font-weight: 700; margin: 0;">
                                Licitacao Atualizada
                            </h1>
                            <p style="color: #94a3b8; font-size: 14px; margin: 8px 0 0 0;">
                                Portal de Compras CESAN
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #334155; font-size: 16px; margin: 0 0 24px 0; line-height: 1.6;">
                                Ola <strong style="color: #0f172a;">' . htmlspecialchars($nmUsuario) . '</strong>,
                            </p>
                            
                            <!-- Alert Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #dbeafe; border-left: 4px solid #3b82f6; border-radius: 0 12px 12px 0; padding: 20px;">
                                        <p style="color: #1e40af; font-size: 15px; margin: 0; line-height: 1.5;">
                                            A licitacao de codigo <strong>' . htmlspecialchars($codLicitacao) . '</strong> sofreu atualizacoes.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #64748b; font-size: 15px; margin: 0 0 32px 0; line-height: 1.6;">
                                Acesse o portal para visualizar as alteracoes e obter mais informacoes.
                            </p>
                            
                            <!-- Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="background-color: #0f172a; border-radius: 10px;">
                                                    <a href="' . $linkLicitacao . '" style="display: block; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; padding: 14px 32px;">
                                                        Ver Detalhes da Licitacao
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #94a3b8; font-size: 13px; margin: 24px 0 0 0; text-align: center;">
                                Ou acesse: <a href="' . $linkLicitacao . '" style="color: #3b82f6;">' . $linkLicitacao . '</a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 40px; text-align: center;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0 0 8px 0;">
                                Este e um e-mail automatico. Por favor, nao responda.
                            </p>
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                ' . date('Y') . ' CESAN - Companhia Espirito Santense de Saneamento
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        sendEmail($email, $subject, $message, $emailFrom, $smtpHost, $smtpPort);

    endwhile;

    $_SESSION['msg'] = "Licitacao atualizada com sucesso.";

    echo "<script>location.href='index.php';</script>";
}