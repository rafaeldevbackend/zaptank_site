<?php

if (session_status() !== PHP_SESSION_ACTIVE)
{
    session_start(['cookie_lifetime' => 2592000, 'cookie_secure' => true, 'cookie_httponly' => true]);
}

$UserName = $_SESSION['UserName'] ?? 0;

if (empty($UserName) || $UserName == 0)
{
    session_destroy();
    header("Location: /");
    exit();
}

$IsFirstCharge = $_SESSION['isFirstCharge'];

$query = $Connect->query("SELECT NickName FROM $BaseUser.dbo.Sys_Users_Detail where UserName = '$UserName'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $NickName = $infoBase['NickName'];
}
?>
<style type="text/css">.div-only-mobile{display:none}@media screen and (max-width:1001px){.div-no-mobile{display:none}.div-only-mobile{display:block}}.inv {display: none;}</style>
<div class="card-body row">
<span class="login100-form-title p-b-30">
Você está comprando para a conta: <b style="color:orange!important;"><?php echo $NickName ?></b>
</span>
<div class="col-sm mb-3 mb-sm-0">
   <div class="card h-100">
      <div class="card-body">
         <h4 class="step-title">Já é quase seu!</h4>
         <span onClick="checkrules()" class="badge badge-pill badge-danger">Regras da Plataforma</span>
         <br>
         </br>
		 <div id="items">
        
		 <?php // $Pacotes->vipInfo($Connect, $BaseServer, $VipRequest = "601", $Resource, $BaseTank, $Ddtank, $KeyPublicCrypt, $KeyPrivateCrypt, 10); ?>
		 
		 </div>
      </div>
      <div class="card-body-stretched"><a class="btn btn-block btn-primary" href="/serverlist?suv=<?php echo $i ?>">Voltar</a></div>
   </div>
</div>
<div class="card col-sm mb-3 mb-sm-0">
   <form method="post">
      <div class="card-body">
         <div class="form-group">
            <input name="name" id="fullname" type="text" class="form-control" placeholder="Qual o seu nome real ?" maxlength="25" required autofocus>
         </div>
         <div class="form-group">
            <input name="number" id="number" type="text" data-mask="(+55) 00 90000-0000" class="form-control" placeholder="Qual seu número de telefone ?" value="<?php if (!empty($_SESSION['Telefone'])){echo preg_replace('/[^0-9]/', '', $_SESSION['Telefone']);} ?>" minlength="16" min="16" autocomplete="off" maxlength="16" required>
         </div>
         <div class="form-group">
            <input name="email" id="email" type="email" class="form-control" placeholder="Qual seu e-mail para contato" value="<?php if(isset($_SESSION['UserName'])){echo $_SESSION['UserName'];} ?>" required>
         </div>
         <select id="target" name="selval" class="form-control">
			<?php			
				$query = $Connect->query("SELECT * FROM $BaseServer.dbo.Vip_List WHERE ServerID = $DecryptServer");
				$result = $query->fetchAll();
				foreach ($result as $infoBase) {
					$ID = $infoBase['ID'];
					$Price = $infoBase['ValuePrice'];
					if ($ID == '10')
					   echo '<option value="' . $ID . '" selected>Pacote Cupons ' . $ID . ' - ' . $Price . ' BRL</option>';
					else
						echo '<option value="' . $ID . '">Pacote Cupons ' . $ID . ' - ' . $Price . ' BRL</option>';
				}		 
			?>
		 </select>
         <div class="error" id="error"></div>
         <button class="btn btn-primary btn-sm shiny" style="width:98%;float:left;margin-left:5px;font-size:15px;" type="submit" name="buyVip" id="buyVip">Continuar</button>
		 <div class="p-t-40"></div>
      </div>
</div>
</div>
<div class="alert text-center hidemobile" role="alert">
<img class="align-space" src="assets/selo-google-site-seguro.png" alt="GOOGLE SITE SEGURO" width="130" height="46"> <img class="align-space" src="assets/pix.svg" alt="PAGAR COM PIX" width="130" height="46">  <img class="align-space" src="assets/picpay.png" alt="PAGAR COM PICPAY" width="130" height="46">
</div>
</form>
<script type="text/javascript">$("body").on("submit","form",function(){return $(this).submit(function(){return!1}),!0})</script>
<script language="javascript"> function checkrules(){location.assign("/rules");}</script>
<script async src="./assets/jquery.mask.min.js"></script>
<script type="text/javascript" src="./js/utils/cookie.js"></script>
<script type="text/javascript" src="./js/config.js"></script>
<script type="text/javascript" src="./js/utils/url.js"></script>
<script type="text/javascript" src="./js/functions.js"></script>
<script type="text/javascript">
	var error_div = document.getElementById('error');
	
	var usp = new URLSearchParamsPolyfill(window.location.search);
			
	var suv = usp.get('server');	
		
	if(suv == null || suv == '') {
		window.location.href = 'selectserver';
	}

	function getVipInfo(vip) {
		var url = `${api_url}/vip/${vip}/details/${suv}`;
		var jwt_hash = getCookie('jwt_authentication_hash');
		
		var xhr = new XMLHttpRequest();
		
		xhr.open('GET', url, true);
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhr.setRequestHeader('Content-type', 'application/json');
		xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);
		
		xhr.onloadstart = function() {
			document.getElementById('items').innerHTML = '<div class="loader"></div>';	
		};
		
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4) {
				if(xhr.status == 200) {
					var response = JSON.parse(xhr.responseText);
					var items = response.data.items;
					
					if(items.length > 0) {
						setTimeout(function() {
							var items_container = document.getElementById('items');
							items_container.innerHTML = '';
							
							items.forEach(function(item) {
								
								var item_container = document.createElement('div');
								item_container.classList.add('item-shop', 'right');
								item_container.setAttribute('valign', 'middle');
								
								item_container.innerHTML =  `
									<a>
									<img alt='DDTank' height='78' src="${item.image}"><br>
									<center>Quantidade<br>
										<strong>
											<a>(x${item.count})</a>
										</strong>
									</center>
								`;
								items_container.appendChild(item_container);
							});							
						}, 1000);
					}
				} else if(xhr.status == 401) {
					displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
					setTimeout(function(){
						window.location.href = '/selectserver?logout=true';
					}, 1000);
				} else {
					displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
					setTimeout(function(){
						window.location.href = '/';							
					}, 2000);
				}						
			}
		};
		
		xhr.send();
	}
		
	document.getElementById("buyVip").addEventListener("click", function(event) {

		event.preventDefault();

		var full_name = document.getElementById("fullname").value.trim();
		var phone = document.getElementById("number").value.trim();
		var email = document.getElementById("email").value.trim();
		var vip_package = document.getElementById("target").value.trim();

		if (full_name == "" || phone == "" || email == "" || vip_package == "") {
			displayMessage(type = 'error', message = 'Você não preencheu todos os campos solicitados.');
		}else if (full_name.length < 3 || full_name.length > 100) {
			displayMessage(type = 'error', message = 'Seu nome deve ser maior que 3 e menor que 100 caracteres...');
		} else if(phone.length != 19) {
			displayMessage(type = 'error', message = 'Seu número de telefone deve conter 19 caracteres...');
		} else {
			var url = `${api_url}/invoice/new/${suv}`;
			var params = `full_name=${encodeURIComponent(full_name)}&phone=${phone}&email=${encodeURIComponent(email)}&vip_package=${vip_package}`;
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
						if(response.success == true && response.data.redirect != '') {
							window.location.href = response.data.redirect;
						} else {
							displayMessage(type = 'error', message = response.message);
						}
					} else if(xhr.status == 401) {
						displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
						setTimeout(function(){
							window.location.href = '/selectserver?logout=true';
						}, 1000);
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
	});
	
	document.getElementById('target').addEventListener('change', function(event) {		
		var select = event.target;
		var vip = select.value;
		getVipInfo(vip);
	});
	
	document.addEventListener("DOMContentLoaded", function() {
		var select = document.getElementById('target');
		var vip = select.value;
		getVipInfo(vip);
	});	
</script>