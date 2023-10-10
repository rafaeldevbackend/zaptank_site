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
   <title><?php echo $Title; ?> Selecionar</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login100 p-l-50 p-r-50 p-t-77 p-b-30">
               <div id="main_form">
               <h6 style="color:white;"><?php if(isset($_SESSION['UserName'])){echo $_SESSION['UserName'];} ?> </h6>
				  <?php
					 if ($_SESSION['verifiedEmail'] == 0)
                     {
                        echo '<p>Sua conta não tem e-mail verificado.</p>';
                     }
					 else
					 {
						echo '<p>E-mail verificado, sua conta está segura.</p>';
					 }
                     ?>
                  <div class="subpage-content" style="">
                     <div class="player_profile">
                        <div class="">
                           <div class="p_border" style="">
                              <div class="f_nick" style="">
                              </div>
                              <?php $string=$Ddtank->GetStyle($Connect, $BaseUser); if ($string !=null){$arr=explode(',', $string); $head=explode('|', $arr[0]); $head1=$Ddtank->GetPicAndSex($Connect, $BaseUser, $head[0]); $head2=explode('|', $head1); $eff=explode('|', $arr[3]); $eff1=$Ddtank->GetPicAndSex($Connect, $BaseUser, $eff[0]); $eff2=explode('|', $eff1); $hair=explode('|', $arr[2]); $hair1=$Ddtank->GetPicAndSex($Connect, $BaseUser, $hair[0]); $hair2=explode('|', $hair1); $face=explode('|', $arr[5]); $face1=$Ddtank->GetPicAndSex($Connect, $BaseUser, $face[0]); $face2=explode('|', $face1); $cloth=explode('|', $arr[4]); $cloth1=$Ddtank->GetPicAndSex($Connect, $BaseUser, $cloth[0]); $cloth2=explode('|', $cloth1); $arm=explode('|', $arr[6]); $sex=($Ddtank->GetSex($Connect, $BaseUser)) ? 'm': 'f';}else{$sex='m';}?>
                              <div class="p_picture" id="p_picture">
                                 <div class="f_head"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php if(!empty($head2[1])){echo $head2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/head/<?= (!empty($head[1])) ? ($head[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_hair"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php if(!empty($hair2[1])){echo $hair2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/hair/<?= (!empty($hair[1])) ? ($hair[1]): 'default' ?>/1/B/show.png"></div>
                                 <div class="f_effect"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php if(!empty($eff2[1])){echo $eff2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/eff/<?= (!empty($eff[1])) ? ($eff[1]): 'default' ?>/1/show.png"></div>
								         <div class="f_face"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/<?php if(!empty($face2[1])){echo $face2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/face/<?= (!empty($face[1])) ? ($face[1]): 'default' ?>/1/show.png" style="transform: translateX(0px);"></div>
								         <div class="f_effect"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php if(!empty($cloth2[1])){echo $cloth2[1] -1 ? 'f': 'm';}else{echo $Ddtank->GetSex($Connect, $BaseUser) ? 'm': 'f';}	?>/cloth/<?= (!empty($cloth[1])) ? ($cloth[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_arm">
                                    <img alt="DDTank" src="<?php echo $Resource ?>arm/<?= (!empty($arm[1]))? ($arm[1]): 'axe' ?>/1/0/show.png"> 
                                 </div>
                                 <div class="i_grade" style="background-image: url('../assets/images/grade/<?php echo $Ddtank->GetLevel($Connect, $BaseUser) ?>.png');"></div>
                              </div>
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
				  <div class="container-login100-form-btn p-t-10"><a class="play-form-btn" style="color:white;" href="/globalrank?suv=<?php echo $i ?>">Ranking Temporadas</a></div>
				  <div class="container-login100-form-btn p-t-10"><a class="paymoney-form-btn" style="color:white;" href="/onlinerankingtime?suv=<?php echo $i ?>">Ranking Tempo Online</a></div>
				  <div class="container-login100-form-btn p-t-10"><a class="close-form-btn" style="color:white;" href="/powerrank?suv=<?php echo $i ?>">Ranking PODER</a></div>
				  <div class="container-login100-form-btn p-t-10"><a class="server-form-btn" style="color:white;" href="/rankpvp?suv=<?php echo $i ?>">Ranking PVP</a></div>
				  <div class="container-login100-form-btn p-t-10" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/serverlist?suv=<?php echo $i ?>">Voltar</a></div>
               </div>
            </div>
         </div>
      </div>
      <div class="fixed-bottom text-center p-0 text-white footer">Você precisa de suporte? <a href="/ticket?suv=<?php echo $i ?>">Clique aqui e abra um ticket.</a></div>
   </body>
</html>