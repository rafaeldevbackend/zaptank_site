<?php
$Pacote = $Ddtank->DecryptText($KeyPublicCrypt, $KeyPrivateCrypt, addslashes($_GET['show']));

$Pacotes->invoiceInfo($Connect, $BaseServer, $Pacote, $RequestPacket = "1", $Ddtank, $KeyPublicCrypt, $KeyPrivateCrypt);

$query = $Connect->query("SELECT A.ID, A.Price, B.Name FROM Db_Center.dbo.Vip_Data A LEFT JOIN Db_Center.dbo.Vip_List B ON A.Price = B.ValuePrice WHERE A.ID = '$Pacote'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $Price = $infoBase['Price'];
    $Name = $infoBase['Name'];
}

$query = $Connect->query("SELECT COUNT(*) AS HaveInvoice FROM Db_Center.dbo.Vip_Data WHERE ID = '$Pacote' AND UserName = '$_SESSION[UserName]'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $HaveInvoice = $infoBase['HaveInvoice'];
}
if ($HaveInvoice == 0)
{
    $_SESSION['msg'] = "<div class='alert alert-danger ocult-time'>Essa fatura não existe.</div>";
    header('Location: /serverlist?suv='.$_GET['server'].'');
    exit();
}

$query = $Connect->query("SELECT Status, Name, Number, KeyRef, PixDataImage, OrderNum, PicPayQrCode, PicPayLink from Db_Center.dbo.Vip_Data WHERE ID = '$Pacote' AND UserName = '$_SESSION[UserName]'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $Status = $infoBase['Status'];
    $PlayerName = $infoBase['Name'];
    $Telefone = $infoBase['Number'];
    $showkey = $infoBase['KeyRef'];
    $qrcodeurl = $infoBase['PixDataImage'];
    $refcode = $infoBase['OrderNum'];
    $PicPayQrCode = $infoBase['PicPayQrCode'];
    $PicPayLink = $infoBase['PicPayLink'];
}

$query = $Connect->query("SELECT UserId from Db_Center.dbo.Mem_UserInfo WHERE Email = '$_SESSION[UserName]'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $UserId = $infoBase['UserId'];
}

if (!empty($_GET['server']))
{
    $i = $_GET['server'];
    $DecryptServer = $Ddtank->DecryptText($KeyPublicCrypt, $KeyPrivateCrypt, $i);
    $query = $Connect->query("SELECT * FROM Db_Center.dbo.Server_List WHERE ID = '$DecryptServer'");
    $result = $query->fetchAll();
    foreach ($result as $infoBase)
    {
        $ID = $infoBase['ID'];
        $BaseUser = $infoBase['BaseUser'];
    }
}
else
{
    header("Location: selectserver?suv=$i");
    $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Não foi possível encontrar o servidor.</div>";
    exit();
}

if (empty($ID) || empty($BaseUser))
{
    header("Location: selectserver?suv=$i");
    exit();
}

$query = $Connect->query("SELECT NickName from $BaseUser.dbo.Sys_Users_Detail where UserName = '$_SESSION[UserName]'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $NickName = $infoBase['NickName'];
}

if ($Status == 'Aprovada')
{
    $_SESSION['msg'] = "<div class='alert alert-success ocult-time'>A fatura acessada já foi paga!</div>";
    header("Location: /serverlist?suv=$i");
    exit();
}

$nome = "$Name - ZapTank";
$url = "$WEBSITE";

// $main_btn_charge = $pagar->ButtonCharge($Pacote, $nome, floatval($Price), $url);
// $button_mp = $pagar->ButtonMP($Pacote, $nome, floatval($Price), $url);
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

if (isset($_POST['pay_picpay']))
{
    $RefEncode = base64_encode($Pacote);
    $ValorPicPay = $Price;

    //Credenciais do Pic-pay
    $returnUrl = "https://redezaptank.com.br/selectserver?page=purchase&ref=";
    //Dados da fatura
    //URL após o pagamento bem sucedido
    $callbackUrl = "https://redezaptank.com.br/Payments/picpay?referenceId=$RefEncode";
    $expiresAt = date('Y-m-d', strtotime("+3 day", strtotime(date('Y-m-d'))));
    //Dados do comprador
    $firstName = "$PlayerName";
    $document = "#";
    $email = $_SESSION['UserName'];
    $phone = "$Telefone";
    $dados = ["referenceId" => $RefEncode, "callbackUrl" => $callbackUrl, "returnUrl" => $returnUrl . $RefEncode, "value" => $ValorPicPay, "expiresAt" => $expiresAt, "buyer" => ["firstName" => $firstName, "lastName" => '', "document" => $document, "email" => $email, "phone" => $phone]];

    $ch = curl_init('https://appws.picpay.com/ecommerce/public/payments');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dados));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['x-picpay-token: ' . $picPayToken]);

    $res = curl_exec($ch);
    curl_close($ch);

    $retorno = json_decode($res);
	
    if (!empty($retorno->qrcode->base64))
    {
        $PicPayLink = $retorno->paymentUrl;
        $PicPayQrCode = $retorno->qrcode->base64;
        $query = $Connect->query("UPDATE Db_Center.dbo.Vip_Data SET PicPayQrCode = '$PicPayQrCode' WHERE ID='$Pacote'");
        $query = $Connect->query("UPDATE Db_Center.dbo.Vip_Data SET PicPayLink = '$PicPayLink' WHERE ID='$Pacote'");
    }
	else
	{
		$_SESSION['alertpix'] = "<div class='alert alert-danger ocult-time'>Ocorreu um erro interno, por favor tente novamente mais tarde...</div>";
	}
}

if (isset($_POST['pix']))
{
    // Gera um novo cliente //
    $clientdata = json_encode(array(
        "name" => $PlayerName,
        "type" => "company",
        "document_type" => "CNPJ",
        "document" => "18727053000174",
        "email" => $_SESSION['UserName']
    ));

    $PricePix = str_replace(".", "", $Price);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.pagar.me/core/v5/customers");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{'phones':{'mobile_phone':{'country_code':'55','area_code':'24','number':'992540781'}},'name':'$PlayerName','email':'$_SESSION[UserName]','code':$UserId,'document':'18727053000174','document_type':'CNPJ','type':'company'}");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Authorization: Basic $PrivateKeyPagarme"]);
    $newclient = curl_exec($ch);

    if (empty($newclient))
    {
        $_SESSION['alertpix'] = "<div class='alert alert-danger ocult-time'>Ocorreu um erro interno, por favor tente novamente mais tarde...</div>";
    }
    else
    {
        $kpg = base64_encode($Pacote);
        $value = json_decode($newclient);
        // var_dump($value);
        curl_close($ch);
        $clientid = $value->id;
        // var_dump($clientid);
        // Gera um novo cliente //
        // var_dump($value);
        // Gera Qrcode usando dados acima //
        $qr_ch = curl_init();
        curl_setopt($qr_ch, CURLOPT_URL, "https://api.pagar.me/core/v5/orders/");
        curl_setopt($qr_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($qr_ch, CURLOPT_HEADER, false);
        curl_setopt($qr_ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($qr_ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($qr_ch, CURLOPT_POST, true);
        curl_setopt($qr_ch, CURLOPT_POSTFIELDS, "{'items':[{'amount':$PricePix,'description':'Após o pagamento o produto será enviado instantaneamente, número do pedido: $kpg','quantity':1}],'payments':[{'Pix':{'expires_in':3600},'payment_method':'pix'}],'customer_id':'$clientid','antifraud_enabled':false}");
        curl_setopt($qr_ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Authorization: Basic $PrivateKeyPagarme"]);
        $qr = curl_exec($qr_ch);
        $qrcode = json_decode($qr);
        curl_close($qr_ch);
        // Gera Qrcode usando dados acima //
        // var_dump($qrcode);
        if (!empty($qrcode))
        {
            $refcode = $qrcode->id;
            $showkey = $qrcode->charges[0]->last_transaction->qr_code;
            $qrurl = $qrcode->charges[0]->last_transaction->qr_code_url;
            $image = curlRequest($qrurl);
            $datapix = 'data:image/jpg;base64,' . base64_encode($image);
			$qrcodeurl = $datapix;
            $query = $Connect->query("UPDATE Db_Center.dbo.Vip_Data SET KeyRef = '$showkey' WHERE ID='$Pacote'");
            $query = $Connect->query("UPDATE Db_Center.dbo.Vip_Data SET PixDataImage = '$datapix' WHERE ID='$Pacote'");
            $query = $Connect->query("UPDATE Db_Center.dbo.Vip_Data SET OrderNum = '$refcode' WHERE ID='$Pacote'");
        }
        else
        {
            $showkey = 'Não foi possível gerar a chave aleatória, gere um novo QrCode.';
        }

        $stmt = $Connect->prepare("UPDATE Db_Center.dbo.Vip_Data SET Method = 'PIX' WHERE ID = :Reference");
        $stmt->bindParam('Reference', $Pacote);
        $stmt->execute();
    }
}
?>
<div class="card-body row">
   <span class="login100-form-title">Preço do Pacote: <b style="color:orange!important;"><?php echo $Price ?></b> BRL</span>
   <span class="login100-form-title p-b-30">Nome da conta que irá receber: <b style="color:orange!important;"><?php echo $NickName ?></b></span>
   <div class="col-sm-6 col-lg-4 mb-3 mb-sm-5">
      <div class="card custom-checkbox-card-lg checked">
         <div class="card-header d-block text-center">
            <small class="card-subtitle">Pagamento via PIX Escaneie o código QR abaixo</small>
            <div class="mb-3">
               <?php
                  if (empty($qrcodeurl))
                  {
                     echo '<img alt="Pagamento por Pix DDTank" width="220" height="202" src="assets/img/login/pix.webp" />';
                  }
                  else
                  {
                     echo '<img alt="Pagamento por Pix DDTank" width="200" height="200" class="qr-img" src="' . $qrcodeurl . '" />';    
                     $urlpix = '/Payments/pagarme?invoiceNumber=' . $refcode . '';
                  echo "<script type='text/javascript'>var i=1;function myLoopPix(){setTimeout(function(){! function(){ $.ajax({type: 'POST', url: '$urlpix', dataType: 'json',async: true, contentType:'application/json', success: function(response){if (response==1){window.location.href='/serverlist?page=success';}}});}(), ++i && myLoopPix()}, 3500)}myLoopPix();</script>";										
                  }      
                  
                  if (!empty($showkey) && $showkey != "Não foi possível gerar a chave aleatória, gere um novo QrCode.")
                  {
                     $_SESSION['alertpix'] = "<div class='alert alert-info'><b style='color:#202020!important'>PIX - Aponte a câmera para o QRCode ou use a chave aleatória logo abaixo:</p></div><textarea disabled id='aleatory' class='form-control' rows='3'>$showkey</textarea><br><button class='btn btn-block btn-primary custom-checkbox-card-btn' onclick='copyarea()'>Copiar Chave Aleatória</button>";
                  }
                  
                  ?>
            </div>
            <span>Você deve pagar</span>
            <p class="card-text font-weight-bold text-primary"><?php echo $Price ?> BRL</p>
         </div>
         <div class="card-body">
            <ul class="list-checked list-checked-primary list-unstyled-py-2">
               <center>
                  <li class="list-checked-item">Envio Instantâneo</li>
               </center>
            </ul>
         </div>
         <form method="post">
            <div class="card-body-stretched">
               <?php
                  if ($showkey == null)
                  {
                   echo '<button name="pix" class="btn btn-block btn-primary custom-checkbox-card-btn">Gerar QrCode</button>';
                  }
                  else
                  {
                    echo '<button disabled class="btn btn-block btn-primary custom-checkbox-card-btn"><p style="color:white;">Gerar QrCode</p></button>';
                  }
                  ?>
            </div>
         </form>
      </div>
   </div>
   <div class="col-sm-6 col-lg-4 mb-3 mb-sm-5">
      <div class="card custom-checkbox-card-lg checked">
         <div class="card-header d-block text-center">
            <small class="card-subtitle">Abra o PicPay em seu telefone e escaneie o código abaixo:</small>
            <div class="mb-3">
               <?php
                  if (empty($PicPayQrCode))
                  {					 
                     echo '<img alt="Pagamento por PicPay DDTank" width="202" src="assets/img/login/picpay.webp" />';
                  }
                  else
                  {
                     echo '<img alt="Pagamento por PicPay DDTank" width="200" class="qr-img" src="' . $PicPayQrCode . '" />';
                     $urlpicpay = '/Payments/picpay?referenceId=' . base64_encode($Pacote) . '';
                     echo "<script type='text/javascript'>var i=1;function myLoopPicPay(){setTimeout(function(){! function(){ $.ajax({type: 'POST', url: '$urlpicpay', dataType: 'json',async: true, contentType:'application/json', success: function(response){if (response==1){window.location.href='/serverlist?suv=$i&page=success';}}});}(), ++i && myLoopPicPay()}, 5000)}myLoopPicPay();</script>";						
                  }
                  
                  ?>
            </div>
            <span>Você deve pagar</span>
            <p class="card-text font-weight-bold text-primary"><?php echo $Price ?> BRL</p>
         </div>
         <div class="card-body">
            <ul class="list-checked list-checked-primary list-unstyled-py-2">
               <center>
                  <li class="list-checked-item">Envio Instantâneo</li>
               </center>
            </ul>
         </div>
         <form method="post">
            <div class="card-body-stretched">
               <?php
                  if (empty($PicPayQrCode))
                  {
                   echo '<button name="pay_picpay" class="btn btn-block btn-primary custom-checkbox-card-btn">Gerar QrCode</button>';
                  }
                  else
                  {
                    echo '<button disabled class="btn btn-block btn-primary custom-checkbox-card-btn"><p style="color:white;">Gerar QrCode</p></button>';
                  }
                  ?>
            </div>
         </form>
      </div>
   </div>
</div>
<?php
   if(isset($_SESSION['alertpix'])){
   	echo $_SESSION['alertpix'];
   	unset($_SESSION['alertpix']);
   }
   ?>
<script type="text/javascript">function copyarea(){var copyText=document.getElementById("aleatory"); copyText.select(); copyText.setSelectionRange(0, 99999); navigator.clipboard.writeText(copyText.value); alert("Chave aleatória copiada: " + copyText.value);}</script>
<div class="container-login100-form-btn p-t-25"><a class="server-form-btn" style="color:white;" href="/viplist?page=vipitemlist&server=<?php echo $i ?>">Voltar</a></div>