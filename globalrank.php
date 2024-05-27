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

if(isset($_GET['suv']) && !empty($_GET['suv'])) {
	$i = $_GET['suv'];
}

?>
<!DOCTYPE html>
<html lang="pt-br">
   <title><?php echo $Title; ?> Rank Temporadas</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
               <div id="main_form">
                  <span class="login100-form-title p-b-5">RANKING DE TEMPORADAS</span>
                  <div class="subpage-content" style="">
                     <div class="player_profile">
                        <div class="">
                           <div class="p_border" id="characters" style="">
                              <div class="f_nick" style="">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <span class="badge badge-pill badge-dark">Ranking da Temporada Anterior</span>
                  <div class="card card-hover-shadow">
                        <div class="tab-content">
                           <div class="tab-pane active" id="powerrank">
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
                  </br>
                  <div class="alert alert-light" role="alert">
                     Os jogadores que se manterem até o final da temporada no RANK estarão expostos nesse mural até o final da próxima temporada, as recompensas do hall da fama são as seguintes: TOP 1 <a style="color:green!important">R$99,99</a> em recarga + Título <b style="color:black!important">Rei da Temporada</b>, TOP 2 <a style="color:green!important">R$69,99</a> em recarga + Título <b style="color:black!important">Imperador da Temporada</b>, TOP 3 <a style="color:green!important">R$29,99</a> em recarga + Título <b style="color:black!important">Duque da Temporada</b>, caso o jogador não responda o nosso contato a recarga será destinada ao jogador do rank anterior. Jogadores do Rank 4 ao 10 receberão uma recarga de R$14,99.
                  </div>
				  <div id="error"></div>
                  <div class="container-login100-form-btn p-t-25"><a class="server-form-btn" style="color:white;" href="/rank?suv=<?php echo $i ?>">Voltar</a></div>
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

         var url = `${api_url}/rank/temporada/list/${suv}`;
         var jwt_hash = getCookie('jwt_authentication_hash');

         var xhr = new XMLHttpRequest();

         xhr.open('GET', url, true);
         xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
         xhr.setRequestHeader('Content-type', 'application/json');
         xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);
		 
		 xhr.onloadstart = function(){
			document.getElementById('characters').innerHTML = '<div class="loader"></div>'; 
			/*document.getElementById('rank_list').innerHTML = `
				<tr>
					<td colspan="6">
						<div class="loader"></div>
					</td>
				</tr>
			`;*/
		 };
		 
         xhr.onreadystatechange = function() {
            if(xhr.readyState == 4) {
               if(xhr.status == 200) {
                  var response = JSON.parse(xhr.responseText);
				  var data = response.data;
				  
				  var container_characters = document.getElementById('characters');
                  var tbody = document.getElementById('rank_list');
				  
				  setTimeout(function(){
					  document.getElementById('characters').innerHTML = '';
					  document.getElementById('rank_list').innerHTML = '';
					  
					  data.forEach(function(character, index) {
						  
						 var picture = document.createElement('div');
						 
						 var id = (index == 0) ? 'p_picture' : 'p_picture' + (index + 1);
						 picture.setAttribute('id', id);
						 picture.classList.add(id);
						 
						 var hair = character.style.hair;
						 var effect = character.style.effect;
						 var face = character.style.face;
						 var cloth = character.style.cloth;
						 var arm = character.style.arm;

						 picture.innerHTML = `
							 <div class="f_hair"><img alt="DDTank" src="<?php echo $Resource ?>equip/${hair.sex}/hair/${hair.pic}/1/B/show.png"></div>
							 <div class="f_effect"><img alt="DDTank" src="<?php echo $Resource ?>equip/${effect.sex}/eff/${effect.pic}/1/show.png"></div>
							 <div class="f_face"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/${face.sex}/face/${face.pic}/1/show.png"></div>
							 <div class="f_cloth"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/${cloth.sex}/cloth/${cloth.pic}/1/show.png"></div>
							 <div class="f_arm">
								 <img src="<?php echo $Resource ?>arm/${arm.pic}/1/0/show.png"> 
							 </div>
							 <div class="i_grade" style="background-image: url('../assets/images/grade/${character.level}.png');"></div>
						 `;
						 
						 container_characters.appendChild(picture);
						  
						 var tr = document.createElement('tr');
						  
						 tr.innerHTML = `
							<th scope="row">${character.rank}</th>
							<td>${character.nickname}</td>
							<td>${character.level}</td>
							<td>${character.matches}</td>
							<td>${character.wins}</td>
							<td>${character.power}</td>
						 `;
						 
						 tbody.appendChild(tr);
					  });
				  }, 1000);
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