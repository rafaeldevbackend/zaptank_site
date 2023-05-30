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

$query = $Connect->query("SELECT COUNT(*) AS IsChargeBack FROM Db_Center.dbo.Vip_Data where UserName = '$UserName' AND IsChargeBack = '1' AND Status = 'Aprovada'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $Count = $infoBase['IsChargeBack'];
}

if ($Count > 0)
{
    $_SESSION['important'] = '<div class="alert alert-warning">Você tem recargas referente à temporada ' . $Temporada - 1 . ' para coletar! <a class="change-form-btn" style="color:white;font-size:15px;" href="/chargeback?suv='.$i.'">coletar agora</a></div>';
}

$query = $Connect->query("SELECT COUNT(*) AS HaveItemBag FROM Db_Center.dbo.Bag_Goods WHERE UserName = '$UserName' AND Status$DecryptServer = '0'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $HaveItemBag = $infoBase['HaveItemBag'];
}

$query = $Connect->query("SELECT VerifiedEmail, Telefone, CreateDate, IsBanned, Opinion, BadMail, IsFirstCharge, PassWord FROM Db_Center.dbo.Mem_UserInfo WHERE Email = '$UserName'");
$result = $query->fetchAll();
foreach ($result as $infoBase)
{
    $VerifiedEmail = $infoBase['VerifiedEmail'];
    $Telefone = $infoBase['Telefone'];
    $CreateDate = $infoBase['CreateDate'];
    $IsBanned = $infoBase['IsBanned'];
    $Opinion = $infoBase['Opinion'];
    $BadMail = $infoBase['BadMail'];
    $IsFirstCharge = $infoBase['IsFirstCharge'];
    $passmd5 = strtoupper($infoBase['PassWord']);
}

if ($IsBanned)
{
    session_destroy();
    header("Location: /");
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

if ($_SESSION['PassWord'] != $passmd5)
{
    session_destroy();
    header("Location: /");
    exit();
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
                  <?php                     
                     if ($VerifiedEmail == 0)
                     {
                        echo '<p>Sua conta não tem e-mail verificado.</p>';
                     }
                     else
                     {
                        echo '<p>Sua conta foi criada em ' . date('d/m/Y', strtotime($CreateDate)) . '</p>';
                     }
                     
                     $query = $Connect->query("SELECT COUNT(*) AS Status FROM $BaseServer.dbo.Tickets where Status = '0'");
                     $result = $query->fetchAll();
                     foreach($result as $infoBase){
                     $Status = $infoBase['Status'];
                     }
                     
                     if ($Ddtank->AdminPermission($Connect))
                     {
                        echo '<a class="badge badge-pill badge-danger" href="/viewtickets?suv=' . $i . '"><span>' . $Status . ' Tickets não foram respondidos</span></a>';
                     }
                     ?>
				  <a class="badge badge-pill badge-info" href="/ticket?suv=<?php echo $i ?>"><span>Precisa de suporte? Clique aqui</span></a>
                  <div class="subpage-content" style="">
                     <div class="player_profile">
                        <div class="">
                           <div class="p_border" style="">
                              <div class="f_nick" style="">
                              </div>
                              <?php $string=$Ddtank->GetStyle($Connect, $BaseUser ); if ($string !=null){$arr=explode(',', $string); $head=explode('|', $arr[0]); $head1=$Ddtank->GetPicAndSex($Connect, $BaseUser , $head[0]); $head2=explode('|', $head1); $eff=explode('|', $arr[3]); $eff1=$Ddtank->GetPicAndSex($Connect, $BaseUser , $eff[0]); $eff2=explode('|', $eff1); $hair=explode('|', $arr[2]); $hair1=$Ddtank->GetPicAndSex($Connect, $BaseUser , $hair[0]); $hair2=explode('|', $hair1); $face=explode('|', $arr[5]); $face1=$Ddtank->GetPicAndSex($Connect, $BaseUser , $face[0]); $face2=explode('|', $face1); $cloth=explode('|', $arr[4]); $cloth1=$Ddtank->GetPicAndSex($Connect, $BaseUser , $cloth[0]); $cloth2=explode('|', $cloth1); $arm=explode('|', $arr[6]); $sex=($Ddtank->GetSex($Connect, $BaseUser)) ? 'm': 'f';}else{$sex='m';}?>
                              <div class="p_picture" id="p_picture">
                                 <div class="f_head"><img alt="" src="<?php echo $Resource ?>equip/<?php if(!empty($head2[1])){echo $head2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/head/<?= (!empty($head[1])) ? ($head[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_hair"><img alt="" src="<?php echo $Resource ?>equip/<?php if(!empty($hair2[1])){echo $hair2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/hair/<?= (!empty($hair[1])) ? ($hair[1]): 'default' ?>/1/B/show.png"></div>
                                 <div class="f_effect"><img alt="" src="<?php echo $Resource ?>equip/<?php if(!empty($eff2[1])){echo $eff2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/eff/<?= (!empty($eff[1])) ? ($eff[1]): 'default' ?>/1/show.png"></div>
								 <div class="f_face"><img alt="" data-current="0" src="<?php echo $Resource ?>equip/<?php if(!empty($face2[1])){echo $face2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/face/<?= (!empty($face[1])) ? ($face[1]): 'default' ?>/1/show.png" style="transform: translateX(0px);"></div>
								 <div class="f_effect"><img alt="" src="<?php echo $Resource ?>equip/<?php if(!empty($cloth2[1])){echo $cloth2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/cloth/<?= (!empty($cloth[1])) ? ($cloth[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_arm">
                                    <img alt="" src="<?php echo $Resource ?>arm/<?= (!empty($arm[1]))? ($arm[1]): 'axe' ?>/1/0/show.png"> 
                                 </div>
                                 <div class="i_grade" style="background-image: url('../assets/images/grade/<?php echo $Ddtank->GetLevel($Connect, $BaseUser ) ?>.png');"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <script>setTimeout(faceAnmite, 400); function faceAnmite (){var faceObj=$('#p_picture').find('.f_face').find('img'); var current=faceObj.data('current'); var faceTrans=[0, 397, 264.8, 397]; current++; if (current==4){current=0;}faceObj.data('current', current); faceObj.css('transform', 'translateX(-' + faceTrans[current] + 'px)'); if (current > 0){setTimeout(faceAnmite, 100);}else{setTimeout(faceAnmite, 2000);}}</script>
                  <div class="error">
                     <?php
					    if ($Opinion == 0)
						{
							echo "<div class='alert alert-dark'>Participe de uma pesquisa e ganhe um código de itens grátis!<a class='change-form-btn' style='color:white;font-size:15px;' href='/opinionreward?suv=$i'>Participar da pesquisa</a></div>";
						}

						if ($IsFirstCharge == 1 && $Release)
						{
							echo "<div class='alert alert-primary'>Você ganhou 15% de bônus na sua primeira recarga essa oferta é válida apenas hoje!<a class='change-form-btn' style='color:white;font-size:15px;' href='/viplist?page=vipitemlist&server=$i'>APROVEITAR PROMOÇÃO</a></div>";
						}
						
                        if (isset($_SESSION['msg']))
                        {
                            echo $_SESSION['msg'];
                            unset($_SESSION['msg']);
                        }
                        if (isset($_SESSION['alert']))
                        {
                            echo $_SESSION['alert'];
                            unset($_SESSION['alert']);
                        }
                        if (isset($_SESSION['important']))
                        {
                            echo $_SESSION['important'];
                            unset($_SESSION['important']);
                        }
						if (isset($_SESSION['charge']))
                        {
                            echo $_SESSION['charge'];
                            unset($_SESSION['charge']);
                        }
						
                        ?>
                  </div>
                  <!--<div class='alert alert-dark'>Todas as recargas estão com recompensas em dobro. Oferta expira em <b class="glow" id="timer">Carregando...</b><a class="change-form-btn" style="color:white;font-size:15px;" href="/viplist?page=vipitemlist&server=<?php echo $i; ?>">aproveitar promoção</a></div>-->
				  <!--<div class='alert alert-warning'>Todas as recargas estão com 10% de desconto aproveite.<a class="change-form-btn" style="color:white;font-size:15px;" href="/viplist?page=vipitemlist&server=<?php echo $i; ?>">aproveitar promoção</a></div>-->
				  <!--<div class='alert alert-dark'>A temporada 15 será encerrada no dia 29/05/2023 às 4:00 A.M<a class="change-form-btn" style="color:white;font-size:15px;" href="/selectwhats">Ficar ciente das novidades.</a></div>-->
                  <?php if (strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank')){if ($_SERVER['HTTP_USER_AGENT'] != 'Mozilla/5.0 (Windows NT 6.1; Win86; x86; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chromium/106.0.0.0 Safari/537.36 LauncherZapTank/108'){echo "<div class='alert alert-info'>Uma nova versão do Launcher está disponível!<a class='change-form-btn' style='color:white;font-size:15px;' href='/download?page=launcher'>Atualize agora!</a></div>";}} ?>
				  <div class="container-login100-form-btn p-t-10"><a class="play-form-btn shinyfont" style="color:white;" href="play?sid=<?php echo $i ?>">Entrar no jogo</a></div>
                  <div class="container-login100-form-btn p-t-20" style="width:47%;float:left;margin-left:5px"><a class="change-form-btn" style="color:white;font-size:15px;" href="/account?suv=<?php echo $i ?>">Configurações</a></div>
                  <div class="container-login100-form-btn p-t-20" style="width:47%;float:left;margin-left:10px;"><a class="paymoney-form-btn" style="color:white;font-size:15px;" href="/viplist?page=vipitemlist&server=<?php echo $i ?>">Recarregar</a></div>
                  <div class="container-login100-form-btn p-t-20" style="width:47%;float:left;margin-left:5px"><a class="server-form-btn" style="color:white;font-size:13px;" href="/rank?suv=<?php echo $i ?>">TOP Rank</a></div>
                  <div class="container-login100-form-btn p-t-20" style="width:47%;float:left;margin-left:10px;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/backpack?suv=<?php echo $i ?>">Mochila&nbsp;<span class="badge badge-light"><?php if ($HaveItemBag > 0) echo $HaveItemBag ?></span></a></div>
                  <?php if (!strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank')){echo '<div class="container-login100-form-btn p-t-25" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/download?suv='.$i.'">Baixar DDTank</a></div>';}?>
				  <div class="container-login100-form-btn p-t-20"><a class="close-form-btn" style="color:white;" href="/selectserver">Selecionar Servidor</a></div>
                  <div class="p-t-40"></div>
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
   </body>
</html>