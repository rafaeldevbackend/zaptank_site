<?php
include 'globalconn.php';

$_SESSION['Status'] = "Conectado";

if (session_status() !== PHP_SESSION_ACTIVE)
{
    session_start(['cookie_lifetime' => 2592000, 'cookie_secure' => true, 'cookie_httponly' => true]);
}

include 'loadautoloader.php';
include 'Objects/gerenciamento.php';

$Dados->Destroy();

$UserName = $_SESSION['UserName'] ?? 0;

if (empty($UserName) || $UserName == 0)
{
    session_destroy();
    header("Location: /");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
   <title><?php echo $Title; ?> Obter novamente</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
			 <span class="login100-form-title p-b-55">
                  Pacotes de chargeback disponíveis.
             </span>
			 <div id="alert"></div>
			 <div id="data"></div>
			 <div class="alert alert-warning" role="alert">É proibido a venda de itens, contas ou cupons dentro do jogo por dinheiro real, caso descumpra essa regra reservamos o direito de desabilitar a conta imediatamente da plataforma.</div>
			 <div id="error"></div>
			 <div class="container-login100-form-btn p-t-25" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/serverlist?suv=<?php echo $i ?>">voltar</a></div>
            </div>
         </div>
      </div>
      <script async src="./assets/main.js"></script>
	  <script type="text/javascript" src="./js/utils/cookie.js"></script>
	  <script type="text/javascript" src="./js/config.js"></script>
	  <script type="text/javascript" src="./js/utils/url.js"></script>
	  <script type="text/javascript" src="./js/functions.js"></script>
	  <script type="text/javascript">
		
		var usp = new URLSearchParamsPolyfill(window.location.search);
			
		var suv = usp.get('suv');	
			
		if(suv == null || suv == '') {
			window.location.href = 'selectserver';
		}
		
		checkServerSuv(suv);
		checkCharacter(suv);
		
		function checkChargeBack() {
			
			var container_alert = document.getElementById('alert');
			
			var url = `${api_url}/chargeback/check/${suv}`;
			var jwt_hash = getCookie('jwt_authentication_hash');

			var xhr = new XMLHttpRequest();

			xhr.open('GET', url, true);
			xhr.setRequestHeader('Content-type', 'application/json');	
			xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);

			xhr.onreadystatechange = function() {
			  if(xhr.readyState == 4) {
				if(xhr.status == 200) {
					
				  var response = JSON.parse(xhr.responseText);
				  
				  if(response.enable_chargeback == false) {
					container_alert.innerHTML = `<div class="alert alert-danger">${response.content}</div>`;  
				  } else if(response.collect_chargeback == true) {
					var data = response.data;
					 
					data.forEach(function(invoice){
						var container_invoice = document.createElement('div');
						
						container_invoice.innerHTML = `
							<div class='card' style='max-width: 200rem;'> 
								<div class='card-body'> 
									<h4 class='card-subtitle'>Pacote de cupons à coletar</h4> 
									<p>Transação concluída na data de ${invoice.recharge_date}</p>
									<h6>Preço <span class='semi-bold'>${invoice.price} BRL</span> </h6> 
									<div class='pull-right' align='right'> 
										<button value="${invoice.id}" type='button' onclick="collect(event)" class='btn btn-outline-primary'>Receber Agora!</button> 
									</div>
								</div>
							</div>
							</br>
						`;
						document.getElementById('data').appendChild(container_invoice);
					});
				  } else {
					container_alert.innerHTML = `<div class="alert alert-danger">${response.content}</div>`; 
				  }
				} else if(xhr.status == 401) {
					displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
					setTimeout(function(){
						window.location.href = '/selectserver?logout=true';
					}, 3000);
				} else {
					console.log("Erro na solicitação. Código do status: " + xhr.status);
				}						
			  }
			};

			xhr.send();	
		}
		
		checkChargeBack();
		
		function collect(event) {
			
			var target = event.target;
			var invoice = target.value;
			
			var url = `${api_url}/chargeback/collect/${suv}`;
			var params = `invoice_id=${invoice}`;
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
							setTimeout(function(){
								window.location.reload();
							}, 1500);
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
		}
	  </script>
   </body>
</html>