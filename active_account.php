<?php
http_response_code(200);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './supplier/phpmailer/phpmailer/src/PHPMailer.php';
require './supplier/phpmailer/phpmailer/src/Exception.php';
require './supplier/phpmailer/phpmailer/src/SMTP.php';

if (!empty($_GET['token']))
{
    require_once ('getconnect.php');
    require_once ('globalconn.php');

    $Connect = Connect::getConnection();

    if (session_status() !== PHP_SESSION_ACTIVE)
    {
        session_start(['cookie_lifetime' => 2592000, 'cookie_secure' => true, 'cookie_httponly' => true]);
    }

    $token = $_GET['token'];

    $query = $Connect->query("SELECT userID, active, token FROM Db_Center.dbo.activate_email WHERE token='$token'");
    $result = $query->fetchAll();
    foreach ($result as $infoBase)
    {
        $userID = $infoBase['userID'];
        $IsActive = $infoBase['active'];
        $IsExist = $infoBase['token'];
    }
	
	if (empty($userID))
    {
        header("Location: /");
        exit();
    }
    if ($IsActive == 0)
    {
        $query = $Connect->query("SELECT Email, VerifiedEmail FROM Db_Center.dbo.Mem_UserInfo WHERE UserId='$userID'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Email = $infoBase['Email'];
            $VerifiedEmail = $infoBase['VerifiedEmail'];
        }
        if (!$VerifiedEmail)
        {
            $query = $Connect->query("DELETE FROM Db_Center.dbo.activate_email WHERE token='$IsExist'");
            $query = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET VerifiedEmail='1' WHERE UserId='$userID'");
			$query = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET BadMail='0' WHERE UserId='$userID'");
            if ($IsExist != null)
            {
                $mail = new PHPMailer;
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host = $SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Username = $SMTP_EMAIL; // E-mail SMTP
                $mail->Password = $SMTP_PASSWORD;
                $mail->Port = 587;
                $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true, ]];
                $mail->setFrom('noreply@redezaptank.com.br', 'DDTank'); // E-mail SMTP
                $mail->addAddress('' . $Email . '', 'DDTank'); // E-mail do usuário
                $mail->isHTML(true);
                $mail->Subject = 'Conta ativada!';
                $mail->Body = '<style>@import url(https://fonts.googleapis.com/css?family=Roboto);body{font-family: "Roboto", sans-serif; font-size: 48px;}</style> <table cellpadding="0" cellspacing="0" border="0" style="padding:0;margin:0 auto;width:100%;max-width:620px"> <tbody> <tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr><tr> <td style="padding:0;margin:0;font-size:1px">&nbsp;</td><td style="padding:0;margin:0" width="590"> <span class="im"> <table width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr style="background-color:#fff"> <td style="padding:11px 23px 8px 15px;float:right;font-size:12px;font-weight:300;line-height:1;color:#666;font-family:"Proxima Nova",Helvetica,Arial,sans-serif"> <p style="float:right">' . $Email . '</p></td></tr></tbody> </table> <table bgcolor="#d65900" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td height="0"></td></tr><tr> <td align="center" style="display:none"><img alt="DDTank" width="90" style="width:90px;text-align:center"></td></tr><tr> <td height="0"></td></tr><tr> <td class="m_-5336645264442155576title m_-5336645264442155576bold" style="padding:63px 33px;text-align:center" align="center"><span class="m_-5336645264442155576mail__title" style=""><h1><font color="#ffffff">Conta ativada com sucesso!</font></h1></span></td></tr><tr> <td style="text-align:center;padding:0"> <div id="m_-5336645264442155576responsive-width" class="m_-5336645264442155576responsive-width" width="78.2% !important" style="width:77.8%!important;margin:0 auto;background-color:#fbee00;display:none"> <div style="height:50px;margin:0 auto">&nbsp;</div></div></td></tr></tbody> </table> </span> <div id="m_-5336645264442155576div-table-wrapper" class="m_-5336645264442155576div-table-wrapper" style="text-align:center;margin:0 auto"> <table class="m_-5336645264442155576main-card-shadow" bgcolor="#ffffff" align="center" border="0" cellpadding="0" cellspacing="0" style="border:none;padding:48px 33px 0;text-align:center"> <tbody> <tr> <td align="center"> <table class="m_-5336645264442155576mail__buttons-container" align="center" width="200" border="0" cellpadding="0" cellspacing="0" style="border-radius:4px;height:48px;width:240px;table-layout:fixed;margin:32px auto"> <tbody> <tr> <td style="border-radius:4px;height:30px;font-family:"Proxima nova",Helvetica,Arial,sans-serif" bgcolor="#d65900"><a href="https://redezaptank.com.br/" style="padding:10px 3px;display:block;font-family:Arial,Helvetica,sans-serif;font-size:16px;color:#fff;text-decoration:none;text-align:center" target="_blank" data-saferedirecturl="https://redezaptank.com.br/">Jogar Agora</a></td></tr></tbody> </table> </td></tr><tr> <td align="center"> <p class="m_-5336645264442155576mail__text-card m_-5336645264442155576bold" style="text-decoration:none;font-family:"Proxima Nova",Arial,Helvetica,sans-serif;text-align:center;line-height:16px;max-width:390px;width:100%;margin:0 auto 0;font-size:14px;color:#999">Saudações do ZapTank, Obrigado por se inscrever em nosso servidor. Agora você tem acesso a nosso jogo e toda plataforma online.</p></td></tr></tbody> </table> </div></td><td style="padding:0;margin:0;font-size:1px">&nbsp;</td></tr><tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr></tbody> </table><small class="text-muted"><?php setlocale(LC_TIME, "pt_BR", "pt_BR.utf-8", "pt_BR.utf-8", "portuguese"); date_default_timezone_set("America/Sao_Paulo"); echo strftime("%A, %d de %B de %Y", strtotime("today"));?></small> </p></div></div>';
                $mail->AltBody = 'Conta ativada!';
                $mail->send();
                if (empty($_SESSION['Status']))
			    {
				    $_SESSION['msg'] = "<div class='alert alert-success ocult-time'>Sua conta foi ativada com sucesso!</div>";
                    header("location: /");
                    exit();
				}
                if (isset($_SESSION['Status']) == "Conectado")
                {
                    $_SESSION['alert_newaccount'] = "<div class='alert alert-success ocult-time'>Sua conta foi ativada com sucesso!</div>";
                    header("location: /selectserver");
                    exit();
                }
                else
                {
                    $_SESSION['msg'] = "<div class='alert alert-success ocult-time'>Sua conta foi ativada com sucesso!</div>";
                    header("location: /");
                    exit();
                }
            }
        }
        else
        {
			if (empty($_SESSION['Status']))
			{
			    $_SESSION['msg'] = "<div class='alert alert-success ocult-time'>Sua conta foi ativada com sucesso!</div>";
                header("location: /");
                exit();
			}
            if (isset($_SESSION['Status']) == "Conectado")
            {
                header("location: /selectserver");
                $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Seu token de acesso expirou ou não existe, pode ser que você tenha tentado acessar uma página que não tenha permissão.</div>";
                exit();
            }
            else
            {
                header("location: /selectserver");
                $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Seu token de acesso expirou ou não existe, pode ser que você tenha tentado acessar uma página que não tenha permissão.</div>";
                exit();
            }
        }
    }
    else
    {
        header("location: /selectserver");
        $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Seu token de acesso expirou ou não existe, pode ser que você tenha tentado acessar uma página que não tenha permissão.</div>";
        exit();
    }
}
else
{
    if (!empty($_SESSION['Status']) && isset($_SESSION['Status']) == "Conectado")
    {
        header("location: /selectserver");
        $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Seu token de acesso expirou ou não existe, pode ser que você tenha tentado acessar uma página que não tenha permissão.</div>";
        exit();
    }
    else
    {
        header("location: /selectserver");
        $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Seu token de acesso expirou ou não existe, pode ser que você tenha tentado acessar uma página que não tenha permissão.</div>";
        exit();
    }
}
?>