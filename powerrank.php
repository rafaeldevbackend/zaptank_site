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
   <title><?php echo $Title; ?> Rank poder</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
               <div id="main_form">
                  <span class="login100-form-title p-b-55">RANKING DE PODER</span>
                  <div class="card card-hover-shadow">
                        <div class="tab-content">
                           <div class="tab-pane active" id="rankpoder">
                                 <table class="table table-striped">
                                    <thead>
                                       <tr>
                                          <th scope="col">Top</th>
                                          <th scope="col">Nome do jogador</th>
                                          <th scope="col">Nível do jogador</th>
                                          <th scope="col">Partidas jogadas</th>
                                          <th scope="col">Partidas ganhas</th>
                                          <th scope="col">Poder</th>
                                       </tr>
                                    </thead>
                                    <tbody id="rank_list"></tbody>
                                 </table>
                        </div>
                     </div>
                  </div>
				  <div id="error"></div>
                  <div class="container-login100-form-btn p-t-25"><a class="server-form-btn" style="color:white;" href="/rank?suv=<?php echo $i ?>">Voltar</a></div>
               </div>
            </div>
         </div>
      </div>
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

         var url = `${api_url}/rank/poder/list/${suv}`;
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
				  
                  var tbody = document.getElementById('rank_list');

                  data.forEach(function(character, index) {
					  
					 var tr = document.createElement('tr');
					  
					 tr.innerHTML = `
                        <th scope="row">${(index + 1)}</th>
                        <td>${character.nickname}</td>
                        <td>${character.level}</td>
                        <td>${character.matches}</td>
                        <td>${character.wins}</td>
                        <td>${character.power}</td>
                     `;
					 
					 tbody.appendChild(tr);
                  });

               } else if(xhr.status == 401) {
                  displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
                  setTimeout(function(){
                     window.location.href = '/selectserver?logout=true';
                  }, 1500);
               } else {
					displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
					setTimeout(function(){
						window.location.href = '/';							
					}, 2000);
               }						
            }
         };
         
         xhr.send();
	  </script>
   </body>
</html>