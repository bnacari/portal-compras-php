<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

//Load Composer's autoloader
require '../vendor/autoload.php';
include_once 'bd/conexao.php';

$idVisita = filter_input(INPUT_GET, 'idVisita', FILTER_SANITIZE_SPECIAL_CHARS);

$queryVisita = "SELECT V.*, L.NM_LOCAL, P.NM_PUBLICO
                FROM [VisitaAgendada].[dbo].[VISITA] V
                LEFT JOIN LOCAL L ON L.ID_LOCAL = V.ID_LOCAL
                LEFT JOIN PUBLICO P ON P.ID_PUBLICO = V.ID_PUBLICO
                WHERE V.ID_VISITA = $idVisita
                ORDER BY ID_VISITA";

$queryVisita2 = $pdoCAT->query($queryVisita);

while ($registros = $queryVisita2->fetch(PDO::FETCH_ASSOC)) :

    $nmResponsavel = $registros['NM_RESP_VISITA'];
    $emailResponsavel = $registros['EMAIL_RESP_VISITA'];
    $telResponsavel = $registros['TEL_RESP_VISITA'];
    $localVisitado = $registros['NM_LOCAL'];
    $respSolicitacao = $registros['NM_PUBLICO'];
    $tipoVisita = $registros['TIPO_VISITA'];
    $dtINIVisita = $registros['DT_INI_VISITA'];
    $dtFIMVisita = $registros['DT_FIM_VISITA'];
    $turnoVisita = $registros['TURNO_VISITA'];
    $numVisitantes = $registros['NUM_VISITANTES'];
    $objVisita = $registros['OBJ_VISITA'];
    $deficiente = $registros['DEFICIENTE'];
    $necessidadeDeficiente = $registros['NECESSIDADE_DEFICIENTE'];
    $nmInstituicao = $registros['NM_INSTITUICAO'];
    $endInstituicao = $registros['END_INSTITUICAO'];
    $bairroInstituicao = $registros['BAIRRO_INSTITUICAO'];
    $cidadeInstituicao = $registros['CIDADE_INSTITUICAO'];
    $telInstituicao = $registros['TEL_INSTITUICAO'];
    $emailInstituicao = $registros['EMAIL_INSTITUICAO'];

    $dtINIVisitaFormatada = date("Ymd\THis\Z", strtotime($dtINIVisita . ' +3 hours'));
    $dtFIMVisitaFormatada = date("Ymd\THis\Z", strtotime($dtFIMVisita . ' +3 hours'));

endwhile;

$icsContent = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Your Organization//Your App//EN
METHOD:PUBLISH
BEGIN:VEVENT
SUMMARY:$localVisitado
DESCRIPTION:$objVisita
DTSTART:$dtINIVisitaFormatada
DTEND:$dtFIMVisitaFormatada
LOCATION:$localVisitado
END:VEVENT
END:VCALENDAR";
//fim do anexo do calendário

$mail = new PHPMailer(true);
$serverName = $_SERVER['SERVER_NAME'];
$link = $serverName . '/viewVisita.php?idVisita=' . $idVisita;

try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'app-mail.sistemas.cesan.com.br';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = false;                                   //Enable SMTP authentication
    $mail->Username   = 'educa.ambiental@cesan.com.br';                     //SMTP username
    $mail->Port       = 25;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->SMTPAutoTLS = false;

    //Recipients
    $mail->setFrom('educa.ambiental@cesan.com.br', 'CESAN - Visita Agendada');
    $mail->addAddress('educa.ambiental@cesan.com.br', 'CESAN - Visita Agendada');
    // $mail->addAddress('bruno.nacari@cesan.com.br', 'CESAN - Visita Agendada');
    $mail->addCC($emailResponsavel, 'CESAN - Visita Agendada');     //Add a recipient

    //trecho que anexa o calendário ao e-mail enviado
    $icsFileName = 'VisitaAgendada.ics';
    $mail->addStringAttachment($icsContent, $icsFileName, 'base64', 'text/calendar');

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->CharSet = 'UTF-8';                      // Set charset to UTF-8
    $mail->Encoding = 'base64';
    $mail->Subject = 'Visita criada por: ' . $nmResponsavel . '(Cód.: ' . $idVisita . ')';
    $mail->Body    = '<b>Solicitação de Visita cadastrada com sucesso. </b></br></br>';
    $mail->Body    .= 'Nome do Solicitante: <b>' . $nmResponsavel . '</b></br>';
    $mail->Body    .= 'Telefone de Contato: <b>' . $telResponsavel . '</b></br>';
    $mail->Body    .= 'E-mail de Contato: <b>' . $emailResponsavel . '</b></br>';
    $mail->Body    .= 'Foi solicitada uma visita em: <b>' . $localVisitado . '</b></br>';
    $mail->Body    .= 'Público Solicitante: <b>' . $respSolicitacao . '</b></br>';
    $mail->Body    .= 'Quando: <b>' . $dtINIVisita . '</b></br>';
    $mail->Body    .= 'Período: <b>' . $turnoVisita . '</b></br>';
    $mail->Body    .= 'Número de Participantes: <b>' . $numVisitantes . '</b></br>';
    $mail->Body    .= 'Objetivo da Visita: <b>' . $objVisita . '</b></br></br>';
    $mail->Body    .= '<b>Link de acesso: </b><a href="' . $link . '">' . $link . '</a></br>';

    $mail->send();

    // $_SESSION['msg'] = "<p class='center red-text'>" . '<strong>Visita ' . $idVisita . ' cadastrada com sucesso.' . "</p>";

    echo "<script>location.href='consultarLicitacao.php';</script>";

    // echo "<script>window.history.back();</script>";

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
