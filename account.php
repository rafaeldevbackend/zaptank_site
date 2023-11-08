<?php
include 'globalconn.php';
include 'getconnect.php';

$Connect = Connect::getConnection();
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

if (!empty($_GET['suv']))
{
    $i = $_GET['suv'];
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
    header("Location: selectserver");
    $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Não foi possível encontrar o servidor.</div>";
    exit();
}

if (empty($ID) || empty($BaseUser))
{
    header("Location: selectserver");
    exit();
}

$query = $Connect->query("SELECT COUNT(*) AS UserName FROM $BaseUser.dbo.Sys_Users_Detail where UserName = '$UserName'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $CountUser = $infoBase['UserName'];
}

if ($CountUser == 0)
{
    header("Location: /selectserver?nvic=new&sid=$i");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
   <title><?php echo $Title; ?> Configurações do Personagem</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login100 p-l-50 p-r-50 p-t-40">
			   <div class="p-t-20" id="main_form">
				  <span class="login100-form-title">Configurações do Personagem</span>
				   <?php                     
                     if ($_SESSION['verifiedEmail'] == 0)
                     {
						echo '<p>Para liberar novas funções confirme seu e-mail.</p>';
                     }
                   ?>
                  <div class="subpage-content" style="">
                     <div class="player_profile">
                        <div class="">
                           <div class="p_border" style="">
                              <div class="f_nick" style="">
                              </div>                              
                              <div class="p_picture" id="p_picture"></div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <script>
                     setTimeout(faceAnmite, 400);
                     function faceAnmite () {
                               var faceObj = $('#p_picture').find('.f_face').find('img');
                               var current = faceObj.data('current');
                               var faceTrans = [0, 397, 264.8, 397];
                               current++;
                     
                               if (current == 4) {
                                   current = 0;
                               }
                     
                               faceObj.data('current', current);
                               faceObj.css('transform', 'translateX(-' + faceTrans[current] + 'px)');
                     
                               if (current > 0) {
                                   setTimeout(faceAnmite, 100);
                               } else {
                                   setTimeout(faceAnmite, 2000);
                               }
                     
                           }
                  </script>
                  <div class="wrap-input100 validate-input m-b-16">
                     <a href="changename?suv=<?php echo $i ?>"><input class="input100" disabled placeholder="Alterar nome"></a>
                     <span class="focus-input100"></span>
                     <span class="symbol-input100">
                     <span class="lnr lnr-pencil"></span>
                     </span>
                  </div>
                     <div class="wrap-input100 validate-input m-b-16">
                     <a href="clearbag?suv=<?php echo $i ?>"><input class="input100" disabled placeholder="Limpar Mochila"></a>
                     <span class="focus-input100"></span>
                     <span class="symbol-input100">
                     <span class="lnr lnr-trash"></span>
                     </span>
                     </div>
					 <div class="wrap-input100 validate-input m-b-16">
                     <a href="giftcode?suv=<?php echo $i ?>"><input class="input100" type="password" disabled placeholder="Resgatar código de presente"></a>
                     <span class="focus-input100"></span>
                     <span class="symbol-input100">
                     <span class="lnr lnr-gift"></span>
                     </span>
                  </div>
				  <div id="error"></div>
                     <div class="text-center w-full">
                     <a class="input-label-secondary" href="serverlist?suv=<?php echo $i ?>">
                     Voltar
                     </a>
                     </div>
					 <div class="p-t-25"></div>
               </div>
            </div>
         </div>
      </div>
      <div class="fixed-bottom text-center p-0 text-white footer">Você precisa de suporte? <a href="/ticket?suv=<?php echo $i ?>">Clique aqui e abra um ticket.</a></div>
	   <script language="javascript"> function checkmail(){location.assign("/checkmail");}</script>
	   <script type="text/javascript" src="./js/utils/cookie.js"></script>
	   <script type="text/javascript" src="./js/config.js"></script>
	   <script type="text/javascript" src="./js/utils/url.js"></script>
	   <script type="text/javascript">
		
			var usp = new URLSearchParamsPolyfill(window.location.search);
			var suv = usp.get('suv');
			
			var url = `${api_url}/character/style/${suv}`;
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

						var character = response.data.character;
						
						var picture = document.getElementById('p_picture');
						
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
	   </script>
   </body>
</html>