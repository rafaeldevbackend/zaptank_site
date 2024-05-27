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

if(isset($_GET['suv']) && !empty($_GET['suv'])) {
	$i = $_GET['suv'];
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
			   <div id="tickets"></div>
               <div id="error"></div>
               <div class="container-login100-form-btn p-t-25" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/serverlist?suv=<?php echo $i ?>">Voltar!</a></div>
            </div>
         </div>
      </div>
      <script type="text/javascript">function copyarea(){var copyText=document.getElementById("aleatory"); copyText.select(); copyText.setSelectionRange(0, 99999); navigator.clipboard.writeText(copyText.value); alert("Mensagem copiada: " + copyText.value);}</script>
      <script type="text/javascript">$("body").on("submit","form",function(){return $(this).submit(function(){return!1}),!0})</script>
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
			
		function renderTickets() {
			
			var url = `${api_url}/ticket/list/${suv}`;
			var jwt_hash = getCookie('jwt_authentication_hash');
			
			var xhr = new XMLHttpRequest();
			
			xhr.open('GET', url, true);
			xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xhr.setRequestHeader('Content-type', 'application/json');
			xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);
			
			xhr.onloadstart = function() {
				document.getElementById('tickets').innerHTML = '<div class="loader"></div>';
			};
			
			xhr.onreadystatechange = function() {
				if(xhr.readyState == 4) {
					if(xhr.status == 200) {
						
						var response = JSON.parse(xhr.responseText);
						var tickets = response.data;
						var tickets_container = document.getElementById('tickets');
						
						if(tickets.length == 0) {
							document.getElementById('error').innerHTML = '<div class="alert alert-danger" role="alert">Por aqui está tudo simplesmente esplêndido os problemas já foram solucionados.</div>';
							return;
						}
						
						setTimeout(function(){							
							document.getElementById('tickets').innerHTML = '';
							tickets.forEach(function(ticket){
								var ticket_container = document.createElement('div');
								ticket_container.innerHTML = `
									<div class='card' style='max-width: 200rem;'> 
										<div class='card-body'> 
											<h4 class='card-subtitle'>Motivo do Ticket: ${ticket.subject}</h4> 
											<p>Nick do jogador: ${ticket.nick_name}</p>
											<p>Login do jogador: ${ticket.user_name}</p>
											<p>Servidor do jogador: ${ticket.server_id}</p>
											<p>ID do jogador: ${ticket.user_id}</p>
											<p>Data do Ticket: ${ticket.ticket_created}</p>
											<p>Número para contato: ${ticket.phone}</p><br>
											<textarea class='form-control' disabled id='aleatory' rows='5'>${ticket.ticket_description}</textarea><br>
											<button class='btn btn-block btn-primary custom-checkbox-card-btn' onclick='copyarea()'>Copiar mensagem do Ticket</button><br>
											<form> 
												<select class='input100' size='1' style='border:0;' id="select_reason_${ticket.ticket_id}"> 
													<option value='0' selected>Por favor, selecione um motivo para fechar o ticket.</option> 
													<option value='1'>O jogador já foi contactado e o problema foi solucionado.</option> 
													<option value='2'>Não foi possível entrar em contato com o jogador.</option> 
													<option value='3'>O texto do ticket não é legível ou está mal formatado.</option> 
													<option value='4'>Encerrar sem nenhum aviso (use para tickets duplicados)</option> 
												</select></br> 
												<div class='pull-right' align='right'>
													<button type="button" value='${ticket.ticket_id}' onclick=closeTicket(event) class='btn btn-outline-primary close_ticket'>Encerrar Ticket</button>
												</div>
												<div id="closing_alert_${ticket.ticket_id}" style="margin-top: 15px"></div>
											</form> 
										</div>
									</div></br>
								`;
								tickets_container.appendChild(ticket_container);
							});
						}, 1500);
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
		
		checkPermission(suv).then(function(response) {
			renderTickets();
		});
		
		function closeTicket(event) {
			event.preventDefault();
			
			var target = event.target;
			var ticket_id = target.value;
			var close = document.getElementById(`select_reason_${ticket_id}`).value;
			
			var url = `${api_url}/ticket/close/${suv}`;
			var params = `ticket_id=${ticket_id}&close=${close}`;
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
						var alert = document.getElementById(`closing_alert_${ticket_id}`);
						
						if(response.success == true) {							
							alert.innerHTML = `<div class='alert alert-success'>${response.message}</div>`;
							setTimeout(function(){
								window.location.reload();
							}, 6000);
						} else {
							alert.innerHTML = `<div class='alert alert-danger'>${response.message}</div>`;
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
	  </script>
   </body>
</html>