<?php
include 'globalconn.php';
include 'getconnect.php';

$Connect = Connect::getConnection();
$_SESSION['Status'] = "Conectado";

if (session_status() !== PHP_SESSION_ACTIVE)
{
    session_start(['cookie_lifetime' => 2592000, 'cookie_secure' => true, 'cookie_httponly' => true]);
}

include 'loadautoloader.php';
include 'Objects/gerenciamento.php';

$Dados->Destroy();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './supplier/phpmailer/phpmailer/src/PHPMailer.php';
require './supplier/phpmailer/phpmailer/src/Exception.php';
require './supplier/phpmailer/phpmailer/src/SMTP.php';

if (!empty($_GET['suv']))
{
    $i = $_GET['suv'];
}
else
{
    header("Location: selectserver");
    $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Não foi possível encontrar o servidor.</div>";
    exit();
}

if (isset($_POST['ticket']))
{
    $ID = addslashes($_POST["ticket"]);
    $select = addslashes($_POST["select"]);

    if ($select != 0)
    {
        $query = $Connect->query("SELECT Email FROM Db_Center.dbo.Tickets WHERE ID = '$ID'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Email = $infoBase['Email'];
        }

        if (!empty($Email))
        {
            $EncMail = $Ddtank->EncryptText($KeyPublicCrypt, $KeyPrivateCrypt, $Email);
        }

        $query = $Connect->query("UPDATE Db_Center.dbo.Tickets SET Status = '1' WHERE ID='$ID'");
		$query = $Connect->query("UPDATE Db_Center.dbo.Tickets SET SolvedBy = '$_SESSION[UserName]' WHERE ID='$ID'");
		
        $_SESSION['alert_viewtickets'] = "<div class='alert alert-success ocult-time'>O Ticket foi fechado e o jogador foi notificado, por favor, certifique-se de ter resolvido o problema do jogador para evitar tickets duplicados.</div>";
        if ($select == 1)
        {
			$EncRef = $Ddtank->EncryptText($KeyPublicCrypt, $KeyPrivateCrypt, $ID);
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
            $mail->Subject = 'DDTank - Seu ticket foi fechado!';
            $mail->Body = '<style>@import url(https://fonts.googleapis.com/css?family=Roboto);body{font-family: "Roboto", sans-serif; font-size: 48px;}</style> <table cellpadding="0" cellspacing="0" border="0" style="padding:0;margin:0 auto;width:100%;max-width:620px"> <tbody> <tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr><tr> <td style="padding:0;margin:0;font-size:1px">&nbsp;</td><td style="padding:0;margin:0" width="590"> <span class="im"> <table width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr style="background-color:#fff"> <td style="padding:11px 23px 8px 15px;float:right;font-size:12px;font-weight:300;line-height:1;color:#666;font-family:"Proxima Nova",Helvetica,Arial,sans-serif"> <p style="float:right">' . $Email . '</p></td></tr></tbody> </table> <table bgcolor="#d65900" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td height="0"></td></tr><tr> <td align="center" style="display:none"><img alt="DDTank" width="90" style="width:90px;text-align:center"></td></tr><tr> <td height="0"></td></tr><tr> <td class="m_-5336645264442155576title m_-5336645264442155576bold" style="padding:63px 33px;text-align:center" align="center"><span class="m_-5336645264442155576mail__title" style=""><h1><font color="#ffffff">Um Administrador verificou e resolveu o seu caso, foram aplicadas as medidas necessárias para solucionar o seu problema, que tal avaliar o atendimento do nosso estagiário? Ficaremos muito felizes com sua contribuição :)</font></h1></span></td></tr><tr> <td style="text-align:center;padding:0"> <div id="m_-5336645264442155576responsive-width" class="m_-5336645264442155576responsive-width" width="78.2% !important" style="width:77.8%!important;margin:0 auto;background-color:#fbee00;display:none"> <div style="height:50px;margin:0 auto">&nbsp;</div></div></td></tr></tbody> </table> </span> <div id="m_-5336645264442155576div-table-wrapper" class="m_-5336645264442155576div-table-wrapper" style="text-align:center;margin:0 auto"> <table class="m_-5336645264442155576main-card-shadow" bgcolor="#ffffff" align="center" border="0" cellpadding="0" cellspacing="0" style="border:none;padding:48px 33px 0;text-align:center"> <tbody> <tr> <td align="center"> <table class="m_-5336645264442155576mail__buttons-container" align="center" width="200" border="0" cellpadding="0" cellspacing="0" style="border-radius:4px;height:48px;width:240px;table-layout:fixed;margin:32px auto"> <tbody> <tr> <td style="border-radius:4px;height:30px;font-family:"Proxima nova",Helvetica,Arial,sans-serif" bgcolor="#d65900"><a href="https://redezaptank.com.br/evaluation?ref=' . $EncRef . '" style="padding:10px 3px;display:block;font-family:Arial,Helvetica,sans-serif;font-size:16px;color:#fff;text-decoration:none;text-align:center" target="_blank" data-saferedirecturl="https://redezaptank.com.br/evaluation?ref=' . $EncRef . '">Avaliar atendimento</a></td></tr></tbody> </table> </td></tr><tr> <td align="center"> <p class="m_-5336645264442155576mail__text-card m_-5336645264442155576bold" style="text-decoration:none;font-family:"Proxima Nova",Arial,Helvetica,sans-serif;text-align:center;line-height:16px;max-width:390px;width:100%;margin:0 auto 0;font-size:14px;color:#999">O ZapTank enviou este e-mail pois você optou por recebê-lo ao cadastrar-se no site. Se você não deseja receber e-mails, <a href="https://redezaptank.com.br/unsubscribemaillist?mail=' . $EncMail . '" style="color:rgb(227, 72, 0);text-decoration:none" target="_blank" data-saferedirecturl="">cancele o recebimento</p></td></tr></tbody> </table> </div></td><td style="padding:0;margin:0;font-size:1px">&nbsp;</td></tr><tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr></tbody> </table><small class="text-muted"><?php setlocale(LC_TIME, "pt_BR", "pt_BR.utf-8", "pt_BR.utf-8", "portuguese"); date_default_timezone_set("America/Sao_Paulo"); echo strftime("%A, %d de %B de %Y", strtotime("today"));?></small> </p></div></div>';
            $mail->AltBody = 'DDTank - Seu ticket foi fechado!';
			$mail->send();
        }
        if ($select == 2)
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
            $mail->Subject = 'DDTank - Seu ticket foi fechado!';
            $mail->Body = '<style>@import url(https://fonts.googleapis.com/css?family=Roboto);body{font-family: "Roboto", sans-serif; font-size: 48px;}</style> <table cellpadding="0" cellspacing="0" border="0" style="padding:0;margin:0 auto;width:100%;max-width:620px"> <tbody> <tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr><tr> <td style="padding:0;margin:0;font-size:1px">&nbsp;</td><td style="padding:0;margin:0" width="590"> <span class="im"> <table width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr style="background-color:#fff"> <td style="padding:11px 23px 8px 15px;float:right;font-size:12px;font-weight:300;line-height:1;color:#666;font-family:"Proxima Nova",Helvetica,Arial,sans-serif"> <p style="float:right">' . $Email . '</p></td></tr></tbody> </table> <table bgcolor="#d65900" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td height="0"></td></tr><tr> <td align="center" style="display:none"><img alt="DDTank" width="90" style="width:90px;text-align:center"></td></tr><tr> <td height="0"></td></tr><tr> <td class="m_-5336645264442155576title m_-5336645264442155576bold" style="padding:63px 33px;text-align:center" align="center"><span class="m_-5336645264442155576mail__title" style=""><h1><font color="#ffffff">Você abriu um ticket e falhamos em entrar em contato com você através dos métodos fornecidos, para obter suporte por favor abra outro ticket com meios de contatos válidos.</font></h1></span></td></tr><tr> <td style="text-align:center;padding:0"> <div id="m_-5336645264442155576responsive-width" class="m_-5336645264442155576responsive-width" width="78.2% !important" style="width:77.8%!important;margin:0 auto;background-color:#fbee00;display:none"> <div style="height:50px;margin:0 auto">&nbsp;</div></div></td></tr></tbody> </table> </span> <div id="m_-5336645264442155576div-table-wrapper" class="m_-5336645264442155576div-table-wrapper" style="text-align:center;margin:0 auto"> <table class="m_-5336645264442155576main-card-shadow" bgcolor="#ffffff" align="center" border="0" cellpadding="0" cellspacing="0" style="border:none;padding:48px 33px 0;text-align:center"> <tbody> <tr> <td align="center"> <table class="m_-5336645264442155576mail__buttons-container" align="center" width="200" border="0" cellpadding="0" cellspacing="0" style="border-radius:4px;height:48px;width:240px;table-layout:fixed;margin:32px auto"> <tbody> <tr> <td style="border-radius:4px;height:30px;font-family:"Proxima nova",Helvetica,Arial,sans-serif" bgcolor="#d65900"><a href="https://redezaptank.com.br/" style="padding:10px 3px;display:block;font-family:Arial,Helvetica,sans-serif;font-size:16px;color:#fff;text-decoration:none;text-align:center" target="_blank" data-saferedirecturl="https://redezaptank.com.br/">Voltar</a></td></tr></tbody> </table> </td></tr><tr> <td align="center"> <p class="m_-5336645264442155576mail__text-card m_-5336645264442155576bold" style="text-decoration:none;font-family:"Proxima Nova",Arial,Helvetica,sans-serif;text-align:center;line-height:16px;max-width:390px;width:100%;margin:0 auto 0;font-size:14px;color:#999">O ZapTank enviou este e-mail pois você optou por recebê-lo ao cadastrar-se no site. Se você não deseja receber e-mails, <a href="https://redezaptank.com.br/unsubscribemaillist?mail=' . $EncMail . '" style="color:rgb(227, 72, 0);text-decoration:none" target="_blank" data-saferedirecturl="">cancele o recebimento</p></td></tr></tbody> </table> </div></td><td style="padding:0;margin:0;font-size:1px">&nbsp;</td></tr><tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr></tbody> </table><small class="text-muted"><?php setlocale(LC_TIME, "pt_BR", "pt_BR.utf-8", "pt_BR.utf-8", "portuguese"); date_default_timezone_set("America/Sao_Paulo"); echo strftime("%A, %d de %B de %Y", strtotime("today"));?></small> </p></div></div>';
            $mail->AltBody = 'DDTank - Seu ticket foi fechado!';
			$mail->send();
        }
		if ($select == 3)
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
            $mail->Subject = 'DDTank - Seu ticket foi fechado!';
            $mail->Body = '<style>@import url(https://fonts.googleapis.com/css?family=Roboto);body{font-family: "Roboto", sans-serif; font-size: 48px;}</style> <table cellpadding="0" cellspacing="0" border="0" style="padding:0;margin:0 auto;width:100%;max-width:620px"> <tbody> <tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr><tr> <td style="padding:0;margin:0;font-size:1px">&nbsp;</td><td style="padding:0;margin:0" width="590"> <span class="im"> <table width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr style="background-color:#fff"> <td style="padding:11px 23px 8px 15px;float:right;font-size:12px;font-weight:300;line-height:1;color:#666;font-family:"Proxima Nova",Helvetica,Arial,sans-serif"> <p style="float:right">' . $Email . '</p></td></tr></tbody> </table> <table bgcolor="#d65900" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td height="0"></td></tr><tr> <td align="center" style="display:none"><img alt="DDTank" width="90" style="width:90px;text-align:center"></td></tr><tr> <td height="0"></td></tr><tr> <td class="m_-5336645264442155576title m_-5336645264442155576bold" style="padding:63px 33px;text-align:center" align="center"><span class="m_-5336645264442155576mail__title" style=""><h1><font color="#ffffff">Você abriu um ticket e não conseguimos compreender as informações descritas, para obter suporte por favor abra outro ticket com mais informações detalhadas.</font></h1></span></td></tr><tr> <td style="text-align:center;padding:0"> <div id="m_-5336645264442155576responsive-width" class="m_-5336645264442155576responsive-width" width="78.2% !important" style="width:77.8%!important;margin:0 auto;background-color:#fbee00;display:none"> <div style="height:50px;margin:0 auto">&nbsp;</div></div></td></tr></tbody> </table> </span> <div id="m_-5336645264442155576div-table-wrapper" class="m_-5336645264442155576div-table-wrapper" style="text-align:center;margin:0 auto"> <table class="m_-5336645264442155576main-card-shadow" bgcolor="#ffffff" align="center" border="0" cellpadding="0" cellspacing="0" style="border:none;padding:48px 33px 0;text-align:center"> <tbody> <tr> <td align="center"> <table class="m_-5336645264442155576mail__buttons-container" align="center" width="200" border="0" cellpadding="0" cellspacing="0" style="border-radius:4px;height:48px;width:240px;table-layout:fixed;margin:32px auto"> <tbody> <tr> <td style="border-radius:4px;height:30px;font-family:"Proxima nova",Helvetica,Arial,sans-serif" bgcolor="#d65900"><a href="https://redezaptank.com.br/" style="padding:10px 3px;display:block;font-family:Arial,Helvetica,sans-serif;font-size:16px;color:#fff;text-decoration:none;text-align:center" target="_blank" data-saferedirecturl="https://redezaptank.com.br/">Voltar</a></td></tr></tbody> </table> </td></tr><tr> <td align="center"> <p class="m_-5336645264442155576mail__text-card m_-5336645264442155576bold" style="text-decoration:none;font-family:"Proxima Nova",Arial,Helvetica,sans-serif;text-align:center;line-height:16px;max-width:390px;width:100%;margin:0 auto 0;font-size:14px;color:#999">O ZapTank enviou este e-mail pois você optou por recebê-lo ao cadastrar-se no site. Se você não deseja receber e-mails, <a href="https://redezaptank.com.br/unsubscribemaillist?mail=' . $EncMail . '" style="color:rgb(227, 72, 0);text-decoration:none" target="_blank" data-saferedirecturl="">cancele o recebimento</p></td></tr></tbody> </table> </div></td><td style="padding:0;margin:0;font-size:1px">&nbsp;</td></tr><tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr></tbody> </table><small class="text-muted"><?php setlocale(LC_TIME, "pt_BR", "pt_BR.utf-8", "pt_BR.utf-8", "portuguese"); date_default_timezone_set("America/Sao_Paulo"); echo strftime("%A, %d de %B de %Y", strtotime("today"));?></small> </p></div></div>';
            $mail->AltBody = 'DDTank - Seu ticket foi fechado!';
			$mail->send();
        }
    }
    else
    {
        $_SESSION['alert_viewtickets'] = "<div class='alert alert-danger ocult-time'>Para fechar esse ticket você deve selecionar um motivo.</div>";
    }
}

if (!$Ddtank->AdminPermission($Connect))
{
    $_SESSION['msg'] = "<div class='alert alert-danger ocult-time'>Parece que você tentou acessar uma página que não tem permissão</div>";
    header("Location: serverlist");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
   <title><?php echo $Title; ?> Tickets</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
               <span class="login100-form-title p-b-55">
               Tickets de Usuários Abertos
               </span>       
               <?php
                  $query = $Connect->query("SELECT * FROM Db_Center.dbo.Tickets WHERE Status = '0' ORDER BY Data");
                  $result = $query->fetchAll();
                  if ($result != null && $result[0] != 0)
                  {
                      foreach ($result as $infoBase)
                      {
                          $ID = $infoBase['ID'];
                          $UserName = $infoBase['UserName'];
                          $NickName = $infoBase['NickName'];
                          $UserID = $infoBase['UserID'];
                          $Texto = $infoBase['Texto'];
                          $Data = $infoBase['Data'];
                          $Number = $infoBase['Number'];
                          $CheckBox = $infoBase['CheckBox'];
						  $ServerID = $infoBase['ServerID'];
                          echo "<div class='card' style='max-width: 200rem;'> <div class='card-body'> <h4 class='card-subtitle'>Motivo do Ticket: $CheckBox</h4> <p>Nick do jogador: $NickName</p><p>Login do jogador: $UserName</p><p>Servidor do jogador: $ServerID</p><p>ID do jogador: $UserID</p><p>Data do Ticket: $Data</p><p>Número para contato: $Number</p><br><textarea class='form-control' disabled id='aleatory' rows='5'>$Texto</textarea> <br><button class='btn btn-block btn-primary custom-checkbox-card-btn' onclick='copyarea()'>Copiar mensagem do Ticket</button> <br><form method='post'> <select class='input100' size='1' style='border:0;' name='select'> <option value='0' selected>Por favor, selecione um motivo para fechar o ticket.</option> <option value='1'>O jogador já foi contactado e o problema foi solucionado.</option> <option value='2'>Não foi possível entrar em contato com o jogador.</option> <option value='3'>O texto do ticket não é legível ou está mal formatado.</option> <option value='4'>Encerrar sem nenhum aviso (use para tickets duplicados)</option> </select></br> <div class='pull-right' align='right'><button name='ticket' value='$ID' class='btn btn-outline-primary'>Encerrar Ticket</button></div></form> </div></div></br>";
                      }
                  }
                  else
                  {
                      echo '<div class="alert alert-danger" role="alert">Por aqui está tudo simplesmente esplêndido os problemas já foram solucionados.</div>';
                  }
                  
                  ?>
               <?php
                  if (isset($_SESSION['alert_viewtickets']))
                  {
                      echo $_SESSION['alert_viewtickets'];
                      unset($_SESSION['alert_viewtickets']);
                  }
                  ?>
               <div class="container-login100-form-btn p-t-25" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/serverlist?suv=<?php echo $i ?>">Voltar!</a></div>
            </div>
         </div>
      </div>
      <script type="text/javascript">function copyarea(){var copyText=document.getElementById("aleatory"); copyText.select(); copyText.setSelectionRange(0, 99999); navigator.clipboard.writeText(copyText.value); alert("Mensagem copiada: " + copyText.value);}</script>
      <script type="text/javascript">$("body").on("submit","form",function(){return $(this).submit(function(){return!1}),!0})</script>
   </body>
</html>