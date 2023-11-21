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

$i = $_GET['suv'];

?>
<!DOCTYPE html>
<html lang="pt-br">
   <title><?php echo $Title; ?> Mochila permanente</title>
   <?php include 'Controllers/header.php'; ?>
   <style>
	#bag-items {
		margin-top: 30px;
		display: flex;
		justify-content: flex-start;
		flex-wrap: wrap;
	}
	
	.item {
		width: 125px;
		margin-bottom: 30px;
	}
   </style>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
               <span class="login100-form-title p-b-55">
               Os itens da mochila podem ser coletados uma vez por temporada, todos os itens ficam armazenados permanentemente na sua mochila para sempre.
               </span>
               <div id="main_form">
                  <div class="card bg-light mb-3" style="max-width: 80rem;">
                     <div class="card-body">
                        <h5 class="card-title">Itens da sua Mochila</h5>
						<div id="bag-items"></div>
						<div id="loader" style="text-align: center;"></div>
                     </div>
                  </div>
                  <div class="error" id="error"></div>
                  <div class="container-login100-form-btn p-t-25"><a class="server-form-btn" style="color:white;" href="/serverlist?suv=<?php echo $i ?>">Voltar</a></div>
               </div>
            </div>
         </div>
      </div>
      <div class="fixed-bottom text-center p-0 text-white footer">Você precisa de suporte? <a href="/ticket?suv=<?php echo $i ?>">Clique aqui e abra um ticket.</a></div>
	  <script type="text/javascript" src="./js/utils/cookie.js"></script>
	  <script type="text/javascript" src="./js/config.js"></script>
	  <script type="text/javascript" src="./js/utils/url.js"></script>
	  <script type="text/javascript" src="./js/functions.js"></script>
	  <script type="text/javascript">
		
		var error_div = document.getElementById('error');
		
		var usp = new URLSearchParamsPolyfill(window.location.search);
			
		var suv = usp.get('suv');	
			
		if(suv == null || suv == '') {
			window.location.href = 'selectserver';
		}
		
		checkServerSuv(suv);
		checkCharacter(suv);
		
		var url = `${api_url}/account/email/verified/check`;
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
				   if(response.email_is_verified == false) {
					  error_div.innerHTML = `<div class="alert alert-danger">Para ter acesso ao sistema de mochilas você deve ter uma conta com e-mail verificado.</div>`;
					  setTimeout(function(){
						window.location.href = 'checkmail';  
					  }, 3000);
				   } else {
						backpackItemsRequest(suv);
				   }
				} else if(xhr.status == 401) {
				   displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
				   setTimeout(function(){
					  window.location.href = '/selectserver?logout=true';
				   }, 3000);
				} else {
					displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
					setTimeout(function(){
						window.location.href = '/';							
					}, 2000);
				}						
			}
		};
      
		xhr.send();
		
		function backpackItemsRequest(suv) {
			var url = `${api_url}/backpack/list/${suv}`;
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
						var data = response.data;
						var items = response.data.items;
						
						if(items.length > 0) {	
						
							var items_container = document.getElementById('bag-items');
						
							setTimeout(function(){
								var loader = document.getElementById('loader');
								loader.innerHTML = '';
								
								for(var i=0; i < items.length; i++) {								
									var item = items[i];
									var item_container = document.createElement('div');
									item_container.classList.add('item');
									
									if(item.status == 1) {
										item_container.innerHTML = `
											<div class='align-top parent'>
												<div align='center' valign='middle'>
													<img alt='DDTank' height='78' src=${data.resource}/${item.image_path}>
													<br>
													<strong><a>Quantidade <br>${item.count}</a></strong>
												</div><center>
												<div class='line'></div>
												<button disabled class='btn btn-dark'>Coletado</button> 
											</div>
										`;
									} else {
										item_container.innerHTML = `
											<div class='align-top parent'>
												<div align='center' valign='middle'>
													<img alt='DDTank' height='78' src=${data.resource}/${item.image_path}>
													<br>,
													<strong><a>Quantidade <br>${item.count}</a></strong>
												</div>
												<center>
													<div class='line'></div>
													<button class='btn btn-dark' id='sendBagItem' onclick=sendBagItem('${item.questi}','${item.questii}')>Enviar</button> 	
											</div>
										`;
									}
									
									items_container.appendChild(item_container);
								}				
							}, 2000);							
						} else {
							setTimeout(function(){
								var loader = document.getElementById('loader');
								loader.innerHTML = '';
								error_div.innerHTML = `<div class="alert alert-danger">Sua mochila está vazia!</div>`;								
							}, 1500);
						}					
					} else if(xhr.status == 401) {
						displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
						setTimeout(function(){
							window.location.href = '/selectserver?logout=true';
						}, 3000);
					} else {
						displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
						setTimeout(function(){
							window.location.href = '/';							
						}, 2000);
					}
				}
			};
			
			
			xhr.onprogress = function() {
				var loader = document.getElementById('loader');
				loader.innerHTML = '<div class="loader"></div>';
			};

			xhr.send();
		}
		
		function sendBagItem(questi, questii) {
			
			var button = event.target;
			
			var url = `${api_url}/backpack/item/send/${suv}`;
			var params = `questi=${questi}&questii=${questii}`;
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
							displayMessage(type = "success", message = response.message);
							button.innerText = 'Coletado';
							button.disabled = true;
						} else {
							displayMessage(type = "error", message = response.message);
						}
					} else if(xhr.status == 401) {
						displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
						setTimeout(function(){
							window.location.href = '/selectserver?logout=true';
						}, 3000);
					} else {
						displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
						setTimeout(function(){
							window.location.href = '/';							
						}, 2000);
					}
				}
			};
			
			xhr.send(params);			
		}
	  </script>
   </body>
</html>