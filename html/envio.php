<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

//Load Composer's autoloader
require '../vendor/autoload.php';
include_once 'bd/conexao.php';

$emailUsuario = filter_input(INPUT_GET, 'emailUsuario', FILTER_SANITIZE_SPECIAL_CHARS);
$idLicitacao = filter_input(INPUT_GET, 'idLicitacao', FILTER_SANITIZE_NUMBER_INT);

$mail = new PHPMailer(true);

$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$porta = $_SERVER['SERVER_PORT'];

$linkLicitacao = "$protocolo://$host/viewLicitacao.php?idLicitacao=" . $idLicitacao;
$linkLogin = "$protocolo://$host/login.php";
$linkEsqueciSenha = "$protocolo://$host/trocaSenhaUsuario.php";


try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'app-mail.sistemas.cesan.com.br';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = false;                                   //Enable SMTP authentication
    $mail->Username   = 'compras@cesan.com.br';                     //SMTP username
    $mail->Port       = 25;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->SMTPAutoTLS = false;

    //Recipients
    $mail->setFrom('compras@cesan.com.br', 'CESAN - Portal de Compras');

    // SE FOR RECUPERAÇÃO DE SENHA =======================================================================================================================
    if (isset($emailUsuario)) {

        // Gerar uma senha temporária aleatória
        $novaSenha = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10);

        // Criptografar a nova senha temporária
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Atualizar a senha no banco de dados
        $queryUpdateSenha = "UPDATE USUARIO SET SENHA = :senha WHERE EMAIL_ADM = :email";
        $stmt = $pdoCAT->prepare($queryUpdateSenha);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':email', $emailUsuario);
        $stmt->execute();

        $querySelectPerfil = "SELECT * FROM USUARIO WHERE EMAIL_ADM LIKE '$emailUsuario'";
        $querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
        while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
            $nmUsuario = $registros['NM_ADM'];
            $email = $registros['EMAIL_ADM'];

            $mail->addAddress($email, 'Portal de Compras | CESAN');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->CharSet = 'UTF-8';                      // Set charset to UTF-8
            $mail->Encoding = 'base64';
            $mail->Subject = 'Recuperação de Senha';
            $mail->Body    = '<b>Solicitação de Recuperação de Senha do Portal de Compras da Cesan</b></br></br>';
            $mail->Body    .= ' Nome do Solicitante: <b>' . $nmUsuario . '</b></br>';
            $mail->Body    .= ' E-mail de Contato: <b>' . $email . '</b></br>';
            $mail->Body    .= ' Senha: <b>' . $novaSenha . '</b></br>';
            $mail->Body    .= ' Link de acesso: <a href="' . $linkLogin . '">' . $linkLogin . '</a></br>';
            $mail->Body    .= ' Após realizar o login no sistema, acesse o menu <a href="' . $linkEsqueciSenha . '">TROCAR SENHA</a> e troque sua senha.</br>';

            $mail->send();
        endwhile;

        $_SESSION['msg'] = "Senha TEMPORÁRIA enviada para o e-mail cadastrado.";

        echo "<script>location.href='login.php';</script>";

        if (!isset($email)) {
            $_SESSION['msg'] = "E-mail NÃO cadastrado.";

            echo "<script>location.href='login.php';</script>";
        }

        // SE FOR ENVIO DE EMAIL PARA ATUALIZAÇÃO EM LICITAÇÃO =======================================================================================================================
    } else if (isset($idLicitacao)) {

        $querySelectAtualizacao = "SELECT ADM.NM_ADM, A.EMAIL_ADM, DL.* 
                                    FROM ATUALIZACAO A 
                                    LEFT JOIN USUARIO ADM ON ADM.ID_ADM = A.ID_ADM
                                    LEFT JOIN DETALHE_LICITACAO DL ON A.ID_LICITACAO = DL.ID_LICITACAO
                                    WHERE ADM.STATUS LIKE 'A'
                                    AND A.DT_EXC_ATUALIZACAO IS NULL
                                    AND A.ID_LICITACAO = $idLicitacao
                                    ";

        $querySelectAtualizacao2 = $pdoCAT->query($querySelectAtualizacao);

        while ($registros = $querySelectAtualizacao2->fetch(PDO::FETCH_ASSOC)) :
            $nmUsuario = $registros['NM_ADM'];
            $email = $registros['EMAIL_ADM'];

            $codLicitacao = $registros['COD_LICITACAO'];
            $statusLicitacao = $registros['STATUS_LICITACAO'];
            $objLicitacao = $registros['OBJETO_LICITACAO'];
            $respLicitacao = $registros['PREG_RESP_LICITACAO'];
            $dtAbertura = $registros['DT_ABER_LICITACAO'];
            $dtIniSessao = $registros['DT_INI_SESS_LICITACAO'];
            $modoLicitacao = $registros['MODO_LICITACAO'];
            $criterioLicitacao = $registros['CRITERIO_LICITACAO'];
            $regimeLicitacao = $registros['REGIME_LICITACAO'];
            $formaLicitacao = $registros['FORMA_LICITACAO'];
            $vlLicitacao = $registros['VL_LICITACAO'];
            $localLicitacao = $registros['LOCAL_ABER_LICITACAO'];
            $identificadorLicitacao = $registros['IDENTIFICADOR_LICITACAO'];
            $obsLicitacao = $registros['OBS_LICITACAO'];

            $mail->addAddress($email, 'CESAN - Portal de Compras');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->CharSet = 'UTF-8';                      // Set charset to UTF-8
            $mail->Encoding = 'base64';
            $mail->Subject = 'Licitação Atualizada';
            $mail->Body    = '<b>A licitação de código ' . $codLicitacao . ' sofreu atualizações. </b></br></br>';
            $mail->Body    .= ' Acesse o site <a href="' . $linkLicitacao . '">' . $linkLicitacao . '</a> para maiores informações.</br>';

            $mail->send();

        // var_dump($email);
        endwhile;

        $_SESSION['msg'] = "Licitação atualizada com sucesso.";

        echo "<script>location.href='index.php';</script>";
    }
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
