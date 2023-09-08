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
                   echo '<button id="pay_pagarme" class="btn btn-block btn-primary custom-checkbox-card-btn">Gerar QrCode</button>';
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
                   echo '<button id="pay_picpay" class="btn btn-block btn-primary custom-checkbox-card-btn">Gerar QrCode</button>';
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
<div id="error"></div>
<?php
   if(isset($_SESSION['alertpix'])){
   	echo $_SESSION['alertpix'];
   	unset($_SESSION['alertpix']);
   }
   ?>
<script type="text/javascript">function copyarea(){var copyText=document.getElementById("aleatory"); copyText.select(); copyText.setSelectionRange(0, 99999); navigator.clipboard.writeText(copyText.value); alert("Chave aleatória copiada: " + copyText.value);}</script>
<div class="container-login100-form-btn p-t-25"><a class="server-form-btn" style="color:white;" href="/viplist?page=vipitemlist&server=<?php echo $i ?>">Voltar</a></div>
<script type="text/javascript">
	var error_div = document.getElementById('error');
	
	var generateQrcodePagarme = function(event){
		event.preventDefault();
		
		var invoice_token = usp.get('show');
		
		var url = `${api_url}/payment/pix/pagarme/new/${suv}`;
		var params = `invoice_id=${invoice_token}`;
		var jwt_hash = getCookie('jwt_authentication_hash');
		
		var xhr = new XMLHttpRequest();
				
		xhr.open('POST', url, true);
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhr.setRequestHeader('Content-type', 'application/json');
		xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);
		
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4) {
				if(xhr.status == 200) {
					var response = JSON.parse(xhr.responseText);
					if(response.success == true) {
						error_div.innerHTML = `<div class='alert alert-success'></div>`;									
					} else {
						error_div.innerHTML = `<div class='alert alert-danger ocult-time'>${response.message}</div>`;
					}	
				} else if(xhr.status == 401) {
					error_div.innerHTML = `<div class='alert alert-danger ocult-time'>A sessão expirou, faça o login novamente.</div>`;
					setTimeout(function(){
						window.location.href = '/selectserver?logout=true';
					}, 1000);
				} else {
					console.log("Erro na solicitação. Código do status: " + xhr.status);
				}						
			}
		};
		
		xhr.send(params);
	};
	
	var generateQrcodePicpay = function(event){
		event.preventDefault();
		
		var invoice_token = usp.get('show');
		
		var url = `${api_url}/payment/pix/picpay/new/${suv}`;
		var params = `invoice_id=${invoice_token}`;
		var jwt_hash = getCookie('jwt_authentication_hash');
		
		var xhr = new XMLHttpRequest();
				
		xhr.open('POST', url, true);
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhr.setRequestHeader('Content-type', 'application/json');
		xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);
		
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4) {
				if(xhr.status == 200) {
					var response = JSON.parse(xhr.responseText);
					if(response.success == true) {
						error_div.innerHTML = `<div class='alert alert-success'></div>`;									
					} else {
						error_div.innerHTML = `<div class='alert alert-danger ocult-time'>${response.message}</div>`;
					}	
				} else if(xhr.status == 401) {
					error_div.innerHTML = `<div class='alert alert-danger ocult-time'>A sessão expirou, faça o login novamente.</div>`;
					setTimeout(function(){
						window.location.href = '/selectserver?logout=true';
					}, 1000);
				} else {
					console.log("Erro na solicitação. Código do status: " + xhr.status);
				}						
			}
		};
		
		xhr.send(params);
	};
	
	document.getElementById('pay_pagarme').addEventListener('click', generateQrcodePagarme);
	document.getElementById('pay_picpay').addEventListener('click', generateQrcodePicpay);
</script>