<style>
button:disabled {
    color: white;
}
</style>
<div class="card-body row">
   <span class="login100-form-title">Preço do Pacote: <b style="color:orange!important;"><span class="price"></span></b> BRL</span>
   <span class="login100-form-title p-b-30">Nome da conta que irá receber: <b style="color:orange!important;"><span id="character"></span></b></span>
   <div class="col-sm-6 col-lg-4 mb-3 mb-sm-5">
      <div class="card custom-checkbox-card-lg checked">
         <div class="card-header d-block text-center">
            <small class="card-subtitle">Pagamento via PIX Escaneie o código QR abaixo</small>
            <div class="mb-3">
				<img alt="Pagamento por Pix DDTank" width="220" height="202" id="qrcode_image_openpix" src="assets/img/login/pix.webp" />    
				<div id="key_openpix"></div>
            </div>
            <span>Você deve pagar</span>
            <p class="card-text font-weight-bold text-primary"><span class="price"></span> BRL</p>
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
               <button id="pay_openpix" class="btn btn-block btn-primary custom-checkbox-card-btn">Gerar QrCode</button>
            </div>
         </form>
      </div>
   </div>
   <div class="col-sm-6 col-lg-4 mb-3 mb-sm-5">
      <div class="card custom-checkbox-card-lg checked">
         <div class="card-header d-block text-center">
            <small class="card-subtitle">Abra o PicPay em seu telefone e escaneie o código abaixo:</small>
            <div class="mb-3">
               <img alt="Pagamento por PicPay DDTank" width="202" id="qrcode_image_picpay" src="" />
            </div>
            <span>Você deve pagar</span>
            <p class="card-text font-weight-bold text-primary"><span class="price"></span> BRL</p>
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
               <button id="pay_picpay" class="btn btn-block btn-primary custom-checkbox-card-btn">Gerar QrCode</button>
            </div>
         </form>
      </div>
   </div>
</div>
<div id="error"></div>
<div id="data"></div>
<script type="text/javascript">function copyarea(){var copyText=document.getElementById("aleatory"); copyText.select(); copyText.setSelectionRange(0, 99999); navigator.clipboard.writeText(copyText.value); alert("Chave aleatória copiada: " + copyText.value);}</script>
<div class="container-login100-form-btn p-t-25"><a class="server-form-btn" style="color:white;" href="/viplist?page=vipitemlist&server=<?php echo $i ?>">Voltar</a></div>
<script type="text/javascript">
	var error_div = document.getElementById('error');
	
	var invoice_token = usp.get('show');
	
	if(invoice_token == null || invoice_token == '') {
		window.location.href = 'selectserver';
	}
	
	var url = `${api_url}/invoice/details/${suv}?invoice_id=${invoice_token}`;
    var jwt_hash = getCookie('jwt_authentication_hash');
   
    var xhr = new XMLHttpRequest();
         
    xhr.open('GET', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('Content-type', 'application/json');
    xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);
   
    xhr.onreadystatechange = function() {
       if(xhr.readyState == 4) {
          if(xhr.status == 200) {
             var response = JSON.parse(xhr.responseText);
			 if(response.status_code == 'unknow_invoice') {
				displayMessage(type = 'error', message = response.message);
				setTimeout(function(){
					window.location.href = 'selectserver';
				}, 2000);
			 } else {
				render(response.data);
			 }
          } else if(xhr.status == 401) {
             displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
             setTimeout(function(){
                window.location.href = '/selectserver?logout=true';
             }, 1000);
          } else {
             console.log("Erro na solicitação. Código do status: " + xhr.status);
          }						
       }
    };
   
    xhr.send();
	
	var render = function(data) {
		
		var invoice = data.invoice;
		var character = data.character;
		
		if(invoice.status == 'Aprovada') {
			displayMessage(type = 'error', message = 'A fatura acessada já foi paga!');
			setTimeout(function(){
				window.location.href = `/serverlist?suv=${suv}`;
			}, 2500);
		}
		
		var priceElements = document.getElementsByClassName("price");
		for (var i = 0; i < priceElements.length; i++) {
			var el = priceElements[i];
			el.innerText = invoice.price;
		}
		document.getElementById('character').innerText = character.nickname;
		
		if(invoice.qrcode_openpix != '') {
			document.getElementById('qrcode_image_openpix').src = invoice.qrcode_openpix;
			document.getElementById('pay_openpix').disabled = true;
		} else {
			document.getElementById('qrcode_image_openpix').src = 'assets/img/login/pix.webp';			
		}
		
		if(invoice.key_openpix != '') {
			document.getElementById('key_openpix').innerHTML = `
				<div class="alert alert-info">
					<p style="color:#202020!important">
						PIX - Aponte a câmera para o QRCode ou use a chave aleatória logo abaixo:
					</p>
				</div>
				<textarea disabled id="aleatory" class="form-control" rows="3">
					${invoice.key_openpix}
				</textarea><br>
				<button class="btn btn-block btn-primary custom-checkbox-card-btn" onclick="copyarea()">
					Copiar Chave Aleatória
				</button>
			`;
		}
		
		if(invoice.qrcode_picpay != '') {
			document.getElementById('qrcode_image_picpay').src = invoice.qrcode_picpay;
			document.getElementById('pay_picpay').disabled = true;
		} else {
			document.getElementById('qrcode_image_picpay').src = 'assets/img/login/picpay.webp';			
		}
	};
	
	var generateQrcodeOpenpix = function(event) {
		event.preventDefault();
		
		var url = `${api_url}/payment/pix/openpix/new/${suv}`;
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
						window.location.reload();
					} else {
						displayMessage(type = 'error', message = response.message);
					}	
				} else if(xhr.status == 401) {
					displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
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
						displayMessage(type = 'success', message = response.message);		
						window.location.reload();									
					} else {
						displayMessage(type = 'error', message = response.message);
					}	
				} else if(xhr.status == 401) {
					displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
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
	
	document.getElementById('pay_openpix').addEventListener('click', generateQrcodeOpenpix);
	document.getElementById('pay_picpay').addEventListener('click', generateQrcodePicpay);
</script>