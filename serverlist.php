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
        $Release = $infoBase['Release'];
        $Temporada = $infoBase['Temporada'];
        $Maintenance = $infoBase['Maintenance'];
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

if (!empty($_SESSION['UserId']))
{
    $DataAtual = date('d/m/Y H:i:s');
    $query = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET LastLoginActivityDate='$DataAtual' WHERE UserID='$_SESSION[UserId]'");
}
else
{
    session_destroy();
    header("Location: /");
    exit();
}

if (!empty($_GET['page']) && !empty($_GET['ref']))
{
    $_SESSION['charge'] = "<div class='alert alert-success'>Sua recarga foi processada com sucesso, verifique seu correio dentro do jogo!</div>";
}

if (!empty($_GET['page'])) switch ($_GET['page'])
{
    case 'success' : $_SESSION['alert'] = "<div class='alert alert-success'>Sua recarga foi enviada para o seu correio!</div>";
break;
}

if (strstr($_SERVER['HTTP_USER_AGENT'], 'LoggerZapTank'))
{
    header("Location: /discontinued");
}

if (strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank'))
{
    if ($_SERVER['HTTP_USER_AGENT'] != 'Mozilla/5.0 (Windows NT 6.1; Win86; x86; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chromium/106.0.0.0 Safari/537.36 LauncherZapTank/108')
    {
        header("Location: /discontinued");
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
   <head>
   <title><?php echo $Title; ?> Central do Jogo</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login100 p-l-50 p-r-50 p-t-40">
               <div class="p-t-20">
			   <div id="main_form">
                  <h6 style="color:white;"><?php if(isset($UserName)){echo $UserName;} ?> </h6>
				  <p id="account_info"></p>
				  <div id="ticket_info" style="display: none;">
					<a class="badge badge-pill badge-danger" href="/viewtickets?suv=<?= $i; ?>"><span id="ticket_count">0</span>&nbsp;Tickets não foram respondidos</a>
				  </div>                 
				  <a class="badge badge-pill badge-info" href="/ticket?suv=<?php echo $i ?>"><span>Precisa de suporte? Clique aqui</span></a>
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
                  <script>setTimeout(faceAnmite, 400); function faceAnmite (){var faceObj=$('#p_picture').find('.f_face').find('img'); var current=faceObj.data('current'); var faceTrans=[0, 397, 264.8, 397]; current++; if (current==4){current=0;}faceObj.data('current', current); faceObj.css('transform', 'translateX(-' + faceTrans[current] + 'px)'); if (current > 0){setTimeout(faceAnmite, 100);}else{setTimeout(faceAnmite, 2000);}}</script>
					 <div class='alert alert-dark' id="survey" style="display: none;">Participe de uma pesquisa e ganhe um código de itens grátis!<a class='change-form-btn' style='color:white;font-size:15px;' href='/opinionreward?suv=<?= $i; ?>'>Participar da pesquisa</a></div>
                     <div class='alert alert-primary' id="promotion" style="display: none;">Você ganhou 15% de bônus na sua primeira recarga essa oferta é válida apenas hoje!<a class='change-form-btn' style='color:white;font-size:15px;' href='/viplist?page=vipitemlist&server=<?= $i; ?>'>APROVEITAR PROMOÇÃO</a></div>
					 <div class="alert alert-warning" id="chargeback" style="display: none;">Você tem recargas referente à temporada <?= ($Temporada - 1) ?> para coletar! <a class="change-form-btn" style="color:white;font-size:15px;" href="/chargeback?suv=<?= $i; ?>">coletar agora</a></div>
					<?php						
                        if (isset($_SESSION['alert']))
                        {
                            echo $_SESSION['alert'];
                            unset($_SESSION['alert']);
                        }
						if (isset($_SESSION['charge']))
                        {
                            echo $_SESSION['charge'];
                            unset($_SESSION['charge']);
                        }
						
                        ?>
                  </div>
				  <div id="alert_message"></div>
                  <!--<div class='alert alert-dark'>Todas as recargas estão com recompensas em dobro. Oferta expira em <b class="glow" id="timer">Carregando...</b><a class="change-form-btn" style="color:white;font-size:15px;" href="/viplist?page=vipitemlist&server=<?php echo $i; ?>">aproveitar promoção</a></div>-->
				  <!--<div class='alert alert-warning'>Todas as recargas estão com 10% de desconto aproveite.<a class="change-form-btn" style="color:white;font-size:15px;" href="/viplist?page=vipitemlist&server=<?php echo $i; ?>">aproveitar promoção</a></div>-->
				  <!--<div class='alert alert-dark'>A temporada 15 será encerrada no dia 29/05/2023 às 4:00 A.M<a class="change-form-btn" style="color:white;font-size:15px;" href="/selectwhats">Ficar ciente das novidades.</a></div>-->
                  <?php if (strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank')){if ($_SERVER['HTTP_USER_AGENT'] != 'Mozilla/5.0 (Windows NT 6.1; Win86; x86; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chromium/106.0.0.0 Safari/537.36 LauncherZapTank/108'){echo "<div class='alert alert-info'>Uma nova versão do Launcher está disponível!<a class='change-form-btn' style='color:white;font-size:15px;' href='/download?page=launcher'>Atualize agora!</a></div>";}} ?>
				  <div class="container-login100-form-btn p-t-10"><a class="play-form-btn shinyfont" style="color:white;" href="play?sid=<?php echo $i ?>">Entrar no jogo</a></div>
                  <div class="container-login100-form-btn p-t-20" style="width:47%;float:left;margin-left:5px"><a class="change-form-btn" style="color:white;font-size:15px;" href="/account?suv=<?php echo $i ?>">Configurações</a></div>
                  <div class="container-login100-form-btn p-t-20" style="width:47%;float:left;margin-left:10px;"><a class="paymoney-form-btn" style="color:white;font-size:15px;" href="/viplist?page=vipitemlist&server=<?php echo $i ?>">Recarregar</a></div>
                  <div class="container-login100-form-btn p-t-20" style="width:47%;float:left;margin-left:5px"><a class="server-form-btn" style="color:white;font-size:13px;" href="/rank?suv=<?php echo $i ?>">TOP Rank</a></div>
                  <div class="container-login100-form-btn p-t-20" style="width:47%;float:left;margin-left:10px;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/backpack?suv=<?php echo $i ?>">Mochila&nbsp;<span class="badge badge-light" id="bagItemCount"></span></a></div>
                  <?php if (!strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank')){echo '<div class="container-login100-form-btn p-t-25" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/download?suv='.$i.'">Baixar DDTank</a></div>';}?>
				  <div class="container-login100-form-btn p-t-20"><a class="close-form-btn" style="color:white;" href="/selectserver">Selecionar Servidor</a></div>
				  <div class="error" id="error">
                  <div class="p-t-10"></div>
               </div>
			   </div>
            </div>
         </div>
      </div>
	  <?php // echo '<a href="#" style="position:fixed;width:60px;height:60px;bottom:120px;right:40px;z-index:1000;" target="_blank"> <img alt="DDTank" style="border-radius: 50%" height="64px" width="64px" src="\assets\img\login\cliente.webp"></a>' ; ?>
      <div class="fixed-bottom text-center p-0 text-white footer">Você precisa de suporte? <a href="/ticket?suv=<?php echo $i ?>">Clique aqui e abra um ticket.</a></div>
      <style>.glow{font-size:13px;color:#fff;animation:1s ease-in-out infinite alternate glow}@-webkit-keyframes glow{from{text-shadow:0 0 5px #fff,0 0 5px #fff,0 0 5px #212121,0 0 5px #212121,0 0 5px #212121,0 0 6px #212121,0 0 7px #212121}to{text-shadow:0 0 5px #fff,0 0 3px #ba0f0f,0 0 2px #ba0f0f,0 0 3px #ba0f0f,0 0 3px #ba0f0f,0 0 4px #ba0f0f,0 0 5px #ba0f0f}}</style>
      <script language="javascript"> function viewtickets(){location.assign("/viewtickets");}</script>
      <script language="javascript"> function checkmail(){location.assign("/checkmail");}</script>
      <script language="javascript"> function rules(){location.assign("/rules");}</script>
	  <!--<script language="javascript">var countDownDate=new Date("May 28, 2023 00:00:00").getTime();var x=setInterval(function(){var now=new Date().getTime(); var distance=countDownDate - now; var days=Math.floor(distance / (1000 * 60 * 60 * 24)); var hours=Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)); var minutes=Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)); var seconds=Math.floor((distance % (1000 * 60)) / 1000); document.getElementById("timer").innerHTML=days + "d " + hours + "h " + minutes + "m " + seconds + "s "; if (distance < 0){clearInterval(x); document.getElementById("timer").innerHTML="Expirou";}}, 1000);</script>-->
	  <script type="text/javascript" src="./js/utils/cookie.js"></script>
	  <script type="text/javascript" src="./js/config.js"></script>
	  <script type="text/javascript" src="./js/utils/url.js"></script>
	  <script type="text/javascript">
		var usp = new URLSearchParamsPolyfill(window.location.search);
		
		var suv = usp.get('suv');
		
		function listInfo() {
			
			var url = `${api_url}/serverlist/${suv}`;
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
						if(response.status_code == 'banned_user') {
							window.location.href = '/selectserver?error_code=2'
						} else if(response.status_code == 'list_info') {
							var info = response.info;
							var alerts = response.alerts;
							
							if(info.verified == false) {
								document.getElementById('account_info').innerText = 'Sua conta não tem e-mail verificado.';
							} else {
								document.getElementById('account_info').innerText = `Sua conta foi criada em ${info.created_at}`;
							}
							
							if(alerts.ticket.show == true) {
								document.getElementById('ticket_info').style.display = 'block';
								document.getElementById('ticket_count').innerText = alerts.ticket.data;
							}
							
							if(alerts.survey.show == true) {
								document.getElementById('survey').style.display = 'block';
							}
							
							if(alerts.promotion.show == true) {
								document.getElementById('promotion').style.display = 'block';
							}
							
							if(alerts.chargeback.show == true) {
								document.getElementById('chargeback').style.display = 'block';
							}
							
							if(alerts.backpack.show == true) {
								document.getElementById('bagItemCount').innerText = alerts.backpack.data;
							}
						}
					} else if(xhr.status == 401) {
						displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
						setTimeout(function(){
							window.location.href = '/selectserver?logout=true';
						}, 3000);
					} else {
						displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
					}
				}
			};
        
			xhr.send();
            
		}
		
		listInfo();
			
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
				}
            }
        };
        
        xhr.send();
		
		if(usp.get('error_code') != null) {
			
			var error_code = usp.get('error_code');
			
			switch(error_code) {
				case '3': 
					document.getElementById('alert_message').innerHTML = `<div class='alert alert-danger ocult-time'>O servidor que você tentou entrar ainda não foi inaugurado, para mais informações visite nosso <a target="_blank" href=<?php echo $WhatsApp; ?>><font color='green'>Grupo do WhatsApp</font></a>!</div>`;
					break;
				case '4':
					document.getElementById('alert_message').innerHTML = `<div class='alert alert-danger ocult-time'>O servidor que você tentou entrar está em manutenção, para mais informações visite nosso <a target="_blank" href=<?php echo $WhatsApp; ?>><font color='green'>Grupo do WhatsApp</font></a>!</div>`;
					break;
			}
		}	
	  </script>
   </body>
</html>