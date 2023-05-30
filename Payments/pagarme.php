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

require_once ('../getconnect.php');
require_once ('../globalconn.php');
$Connect = Connect::getConnection();

$useragent = $_SERVER['HTTP_USER_AGENT'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '.././supplier/phpmailer/phpmailer/src/PHPMailer.php';
require '.././supplier/phpmailer/phpmailer/src/Exception.php';
require '.././supplier/phpmailer/phpmailer/src/SMTP.php';

// $json_convertido = file_get_contents('php://input');
// $response = json_decode($json_convertido);
// $error = "../logs/logspagarme.txt";
// file_put_contents($error, $useragent . PHP_EOL, FILE_APPEND | LOCK_EX);

$reference = 0;
$data = 0;
$qrcode = 0;
$isactive = false;
if ($useragent == 'RestSharp/106.6.7.0')
{
    $response = file_get_contents('php://input');
    $data = json_decode($response);
    if ($data->type == 'charge.paid' || $data->type == 'order.paid' && $data->data->status == 'paid')
    {
        $isactive = true;
    }
}
else if (!empty($_GET['invoiceNumber']))
{
    $invoicenumber = $_GET['invoiceNumber'];
    $qr_ch = curl_init();
    curl_setopt($qr_ch, CURLOPT_URL, "https://api.pagar.me/core/v5/orders/$invoicenumber");
    curl_setopt($qr_ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($qr_ch, CURLOPT_HEADER, false);
    curl_setopt($qr_ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($qr_ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($qr_ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Authorization: Basic $PrivateKeyPagarme"]);
    $qr = curl_exec($qr_ch);
    $qrcode = json_decode($qr);
    curl_close($qr_ch);
	if (empty($qrcode->status))
	{
		die('missing information');
	}
	else if ($qrcode->status == 'paid')
	{
        $isactive = true;
	}
}
if ($isactive)
{
    if (!empty($data))
    {
        $reference = $data->data->order->id;
    }
    else
    {
        $reference = $qrcode->id;
    }
    $stmt = $Connect->prepare("SELECT PacoteID, UserName, Status, Price, ServerID FROM Db_Center.dbo.Vip_Data WHERE OrderNum = :reference");
	$stmt->bindParam(':reference', $reference);
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
        $stmt = $Connect->prepare("SELECT BaseUser, AreaID, QuestUrl from Db_Center.dbo.Server_List WHERE ID = $ServerID");
        // $stmt->execute(array(
            // 1
        // ));
		$stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $db_server = $data['BaseUser'];
		$AreaID = $data['AreaID'];
		$QuestUrl = $data['QuestUrl'];
        $stmt = $Connect->prepare("SELECT MAX(CAST(ChargeID AS INT)) AS MAX_VALUE FROM $db_server.dbo.Charge_Money");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // Obtem NickName do Usuário
        $stmt = $Connect->prepare("SELECT NickName, UserID from $db_server.dbo.Sys_Users_Detail WHERE UserName = '$UserName'");
        $stmt->execute();
        $data3 = $stmt->fetch(PDO::FETCH_ASSOC);
        $NickName = $data3['NickName'];
        $UserID = $data3['UserID'];
        // Verifica quantidade de cupons a enviar
        $query = $Connect->query("SELECT Count FROM Db_Center.dbo.Vip_List_Item WHERE TemplateID = '-200' AND VipID = '$PacoteID'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Count = $infoBase['Count'];
        }
        $VerifiedEmail = 0;
        $IsFirstCharge = 0;
        $query = $Connect->query("SELECT VerifiedEmail, IsFirstCharge FROM Db_Center.dbo.Mem_UserInfo WHERE Email = '$UserName'");
        $result = $query->fetchAll();
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
            $stmt = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET IsFirstCharge='False' WHERE Email='$UserName'");
        }
        $data_atual = date('d-m-Y h:i:s');
        $stmt = $Connect->query("UPDATE Db_Center.dbo.Vip_Data SET Status='Aprovada' WHERE OrderNum='$reference'");
        $stmt = $Connect->query("UPDATE Db_Center.dbo.Vip_Data SET Method='PIX' WHERE OrderNum='$reference'");
        $stmt = $Connect->prepare("INSERT INTO $db_server.dbo.Charge_Money(UserName, Money, Date, CanUse, PayWay, NeedMoney) VALUES(?, ?, ?, ?, ?, ?)");
        $stmt->execute(array(
            $UserName,
            $Count,
            $data_atual,
            1,
            'PIX',
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
            $mail->Body = '<style>@import url(https://fonts.googleapis.com/css?family=Roboto); body{font-family: "Roboto", sans-serif; font-size: 48px;}</style><table cellpadding="0" cellspacing="0" border="0" style="padding:0;margin:0 auto;width:100%;max-width:620px"> <tbody> <tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr><tr> <td style="padding:0;margin:0;font-size:1px">&nbsp;</td><td style="padding:0;margin:0" width="590"> <span class="im"> <table width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr style="background-color:#fff"> <td style="padding:11px 23px 8px 15px;float:right;font-size:12px;font-weight:300;line-height:1;color:#666;font-family:" Proxima Nova",Helvetica,Arial,sans-serif"> <p style="float:right">' . $UserName . '</p></td></tr></tbody> </table> <table bgcolor="#d65900" width="100%" cellspacing="0" cellpadding="0" border="0"> <tbody> <tr> <td height="0"></td></tr><tr> <td align="center" style="display:none"> <img width="90" style="width:90px;text-align:center"> </td></tr><tr> <td height="0"></td></tr><tr> <td class="m_-5336645264442155576title m_-5336645264442155576bold" style="padding:63px 33px;text-align:center" align="center"> <span class="m_-5336645264442155576mail__title" style=""> <h1> <font color="#ffffff">Você realizou uma recarga no valor de R$' . $Price . ' sua recarga foi aprovada e entregue. Número do Pedido: ' . base64_encode($reference) . '</b> </font> </h1> </span> </td></tr><tr> <td style="text-align:center;padding:0"> <div id="m_-5336645264442155576responsive-width" class="m_-5336645264442155576responsive-width" width="78.2% !important" style="width:77.8%!important;margin:0 auto;background-color:#fbee00;display:none"> <div style="height:50px;margin:0 auto">&nbsp;</div></div></td></tr></tbody> </table> </span> <div id="m_-5336645264442155576div-table-wrapper" class="m_-5336645264442155576div-table-wrapper" style="text-align:center;margin:0 auto"> <table class="m_-5336645264442155576main-card-shadow" bgcolor="#ffffff" align="center" border="0" cellpadding="0" cellspacing="0" style="border:none;padding:48px 33px 0;text-align:center"> <tbody> <tr> <td align="center"><p class="m_-5336645264442155576mail__text-card m_-5336645264442155576bold" style="text-decoration:none;font-family:"Proxima Nova",Arial,Helvetica,sans-serif;text-align:center;line-height:16px;max-width:390px;width:100%;margin:0 auto 44px;font-size:14px;color:#999">O ZapTank enviou este e-mail pois você optou por recebê-lo ao cadastrar-se no site.</p></td></tr></tbody> </table> </div></td><td style="padding:0;margin:0;font-size:1px">&nbsp;</td></tr><tr> <td colspan="3" style="padding:0;margin:0;font-size:1px;height:1px" height="1">&nbsp;</td></tr></tbody></table><small class="text-muted"> <?php setlocale(LC_TIME, "pt_BR", "pt_BR.utf-8", "pt_BR.utf-8", "portuguese"); date_default_timezone_set("America/Sao_Paulo"); echo strftime("%A, %d de %B de %Y", strtotime("today"));?> </small>';
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
?>