<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json');
http_response_code(200);

$req = $_SERVER['REQUEST_METHOD'];

if ($req != 'POST')
{
   die('Method not defined for CloudFront instance. Contact: admin@redezaptank.com.br');
}

$json_convertido = file_get_contents('php://input');
$response = json_decode($json_convertido);
$refid = 0;
if (!empty($response))
{
	$refid = $response->referenceId;
}

require_once ('../getconnect.php');
require_once ('../globalconn.php');
$Connect = Connect::getConnection();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '.././supplier/phpmailer/phpmailer/src/PHPMailer.php';
require '.././supplier/phpmailer/phpmailer/src/Exception.php';
require '.././supplier/phpmailer/phpmailer/src/SMTP.php';

if ($_GET['referenceId'] != null || $refid != null)
{
    if ($_GET['referenceId'] != null)
    {
        $referenceId = $_GET['referenceId'];
    }
    if ($refid != null)
    {
        $referenceId = $refid;
    }
    $url = 'https://appws.picpay.com/ecommerce/public/payments/' . $referenceId . '/status';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-Picpay-Token: ' . $picPayToken]);
    $result = curl_exec($ch);
    if (curl_errno($ch))
    {
        $error = "../logs/logspicpay.txt";
        file_put_contents($error, curl_error($ch) . PHP_EOL, FILE_APPEND | LOCK_EX);
        die('Error: ' . curl_error($ch));
    }
    curl_close($ch);

    $resposta = json_decode($result);
    
	if (!empty($resposta->status))
	{
        $value = $resposta->status;
	}
}

if (!isset($value))
{
	$error = "../logs/logspicpay.txt";
    file_put_contents($error, $result . PHP_EOL, FILE_APPEND | LOCK_EX);
	exit();
}

if ($value == 'paid' || $value == 'completed')
{
	$RefDecode = base64_decode($referenceId);;
    $stmt = $Connect->prepare("SELECT PacoteID, UserName, Price, Status, ServerID FROM Db_Center.dbo.Vip_Data WHERE ID = :RefDecode");
	$stmt->bindParam(':RefDecode', $RefDecode);
	$stmt->execute();
    $result = $stmt->fetchAll();
    foreach ($result as $infoBase)
    {
        $PacoteID = $infoBase['PacoteID'];
        $UserName = $infoBase['UserName'];
        $Price = $infoBase['Price'];
        $Status = $infoBase['Status'];
	    $ServerID = $infoBase['ServerID'];
    }

    if ($Status != null)
    {
        if ($Status == 'Pendente')
        {
            function curlRequest($url)
            {
                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, $url);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($c, CURLOPT_HEADER, false);
                curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
                $data = curl_exec($c);
                curl_close($c);
                return $data;
            }
            $stmt = $Connect->prepare("SELECT BaseUser, AreaID, QuestUrl FROM Db_Center.dbo.Server_List WHERE ID = :ServerID");
	        $stmt->bindParam(':ServerID', $ServerID);
	        $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $db_server = $data['BaseUser'];
			$AreaID = $data['AreaID'];
		    $QuestUrl = $data['QuestUrl'];
            // Gera o id do registro
            $stmt = $Connect->prepare("SELECT MAX(CAST(ChargeID AS INT)) AS MAX_VALUE FROM $db_server.dbo.Charge_Money");
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $data_atual = date('d-m-Y h:i:s');
            // Verifica quantidade de cupons a enviar
            $stmt = $Connect->prepare("SELECT Count FROM Db_Center.dbo.Vip_List_Item WHERE TemplateID = '-200' AND VipID = :PacoteID");
	        $stmt->bindParam(':PacoteID', $PacoteID);
	        $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $infoBase)
            {
                $Count = $infoBase['Count'];
            }
            // Obtem NickName do Usuário
            $stmt = $Connect->prepare("SELECT NickName, UserID from $db_server.dbo.Sys_Users_Detail WHERE UserName = :UserName");
	        $stmt->bindParam(':UserName', $UserName);
	        $stmt->execute();
            $data3 = $stmt->fetch(PDO::FETCH_ASSOC);
            $NickName = $data3['NickName'];
            $UserID = $data3['UserID'];
            $VerifiedEmail = 0;
            $IsFirstCharge = 0;
            $stmt = $Connect->prepare("SELECT VerifiedEmail, IsFirstCharge FROM Db_Center.dbo.Mem_UserInfo WHERE Email = :UserName");
	        $stmt->bindParam(':UserName', $UserName);
	        $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $infoBase)
            {
                $VerifiedEmail = $infoBase['VerifiedEmail'];
                $IsFirstCharge = $infoBase['IsFirstCharge'];
            }
            if ($IsFirstCharge)
            {
                $percentage = 15;
                $new_width = ($percentage / 100) * $Count;
                $Count += $new_width;
                $stmt = $Connect->prepare("UPDATE Db_Center.dbo.Mem_UserInfo SET IsFirstCharge='False' WHERE Email= :UserName");
	            $stmt->bindParam(':UserName', $UserName);
	            $stmt->execute();

            }
            $stmt = $Connect->prepare("UPDATE Db_Center.dbo.Vip_Data SET Status='Aprovada' WHERE ID= :RefDecode");
	        $stmt->bindParam(':RefDecode', $RefDecode);
	        $stmt->execute();
            $stmt = $Connect->prepare("UPDATE Db_Center.dbo.Vip_Data SET Method='PicPay' WHERE ID= :RefDecode");
	        $stmt->bindParam(':RefDecode', $RefDecode);
	        $stmt->execute();
            $stmt = $Connect->prepare("INSERT INTO $db_server.dbo.Charge_Money(UserName, Money, Date, CanUse, PayWay, NeedMoney) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->execute(array(
                $UserName,
                $Count,
                $data_atual,
                1,
                'PicPay',
                $Price
            ));
			curlRequest("$QuestUrl/UpdateMailByUserID.ashx?UserID=$UserID&AreaID=$AreaID&key=TqUserZap777");			
            if ($VerifiedEmail == 1)
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
                $mail->addAddress('' . $UserName . '', 'DDTank'); // E-mail do usuário
                $mail->isHTML(true);
                $mail->Subject = 'DDTank - Comprovante de Recarga!';
                $mail->Body = '<style>@import url(https://fonts.googleapis.com/css?family=Roboto); body{font-family: "Roboto", sans-serif; font-size: 48px;}</style><table cellpadding="0" cellspacing="0" border="0" style="padding:0;margin:0 auto;width:100%;max-width:620px"> <tbody> <tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr><tr> <td style="padding:0;margin:0;font-size:1px">&nbsp;</td><td style="padding:0;margin:0" width="590"> <span class="im"> <table width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr style="background-color:#fff"> <td style="padding:11px 23px 8px 15px;float:right;font-size:12px;font-weight:300;line-height:1;color:#666;font-family:" Proxima Nova",Helvetica,Arial,sans-serif"> <p style="float:right">' . $UserName . '</p></td></tr></tbody> </table> <table bgcolor="#d65900" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td height="0"></td></tr><tr> <td align="center" style="display:none"> <img width="90" style="width:90px;text-align:center"> </td></tr><tr> <td height="0"></td></tr><tr> <td class="m_-5336645264442155576title m_-5336645264442155576bold" style="padding:63px 33px;text-align:center" align="center"> <span class="m_-5336645264442155576mail__title" style=""> <h1> <font color="#ffffff">Você realizou uma recarga no valor de R$' . $Price . ' sua recarga foi aprovada e entregue. Número do Pedido: ' . $referenceId . '</b> </font> </h1> </span> </td></tr><tr> <td style="text-align:center;padding:0"> <div id="m_-5336645264442155576responsive-width" class="m_-5336645264442155576responsive-width" width="78.2% !important" style="width:77.8%!important;margin:0 auto;background-color:#fbee00;display:none"> <div style="height:50px;margin:0 auto">&nbsp;</div></div></td></tr></tbody> </table> </span> <div id="m_-5336645264442155576div-table-wrapper" class="m_-5336645264442155576div-table-wrapper" style="text-align:center;margin:0 auto"> <table class="m_-5336645264442155576main-card-shadow" bgcolor="#ffffff" align="center" border="0" cellpadding="0" cellspacing="0" style="border:none;padding:48px 33px 0;text-align:center"> <tbody> <td align="center"><p class="m_-5336645264442155576mail__text-card m_-5336645264442155576bold" style="text-decoration:none;font-family:"Proxima Nova",Arial,Helvetica,sans-serif;text-align:center;line-height:16px;max-width:390px;width:100%;margin:0 auto 44px;font-size:14px;color:#999">O ZapTank enviou este e-mail pois você optou por recebê-lo ao cadastrar-se no site.</p></td></tr></tbody> </table> </div></td><td style="padding:0;margin:0;font-size:1px">&nbsp;</td></tr><tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr></tbody></table><small class="text-muted"> <?php setlocale(LC_TIME, "pt_BR", "pt_BR.utf-8", "pt_BR.utf-8", "portuguese"); date_default_timezone_set("America/Sao_Paulo"); echo strftime("%A, %d de %B de %Y", strtotime("today"));?> </small>';
                $mail->AltBody = 'DDTank - Comprovante de Recarga!';
                $mail->SMTPDebug = 0; // errors and messages - 1 Enable 0 Disable
                $mail->send();
            }
			echo json_encode(1);
	        exit();
        }
        else
        {
			echo json_encode(1);
	        exit();
        }
    }
    else
    {
		echo json_encode(0);
	    exit();
    }
}
else if ($value == 'analysis' || $value == 'refunded' || $value == 'chargeback')
{
    $error = "../logs/logspicpay.txt";
    file_put_contents($error, $result . PHP_EOL, FILE_APPEND | LOCK_EX);
}
else
{
	 echo json_encode(0);
	 exit();
}
?>