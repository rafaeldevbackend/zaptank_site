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

   $query = $Connect->query("SELECT TOP 1 * FROM $BaseServer.dbo.Rank_Temporada WHERE ServerID = '$ID'");
   $result = $query->fetchAll();
   foreach ($result as $infoBase)
   {
       $Nome1 = $infoBase['Nome'];
       $Level1 = $infoBase['Level'];
       $PartidasJogadas1 = $infoBase['PartidasJogadas'];
       $PartidasGanhas1 = $infoBase['PartidasGanhas'];
       $Poder1 = $infoBase['Poder'];
       $Style1 = $infoBase['Style'];
       $Sex1 = $infoBase['Sex'];
   }

   $query = $Connect->query("SELECT TOP 2 * FROM $BaseServer.dbo.Rank_Temporada WHERE ServerID = '$ID'");
   $result = $query->fetchAll();
   foreach ($result as $infoBase)
   {
       $Nome2 = $infoBase['Nome'];
       $Level2 = $infoBase['Level'];
       $PartidasJogadas2 = $infoBase['PartidasJogadas'];
       $PartidasGanhas2 = $infoBase['PartidasGanhas'];
       $Poder2 = $infoBase['Poder'];
       $Style2 = $infoBase['Style'];
       $Sex2 = $infoBase['Sex'];
   }

   $query = $Connect->query("SELECT TOP 3 * FROM $BaseServer.dbo.Rank_Temporada WHERE ServerID = '$ID'");
   $result = $query->fetchAll();
   foreach ($result as $infoBase)
   {
       $Nome3 = $infoBase['Nome'];
       $Level3 = $infoBase['Level'];
       $PartidasJogadas3 = $infoBase['PartidasJogadas'];
       $PartidasGanhas3 = $infoBase['PartidasGanhas'];
       $Poder3 = $infoBase['Poder'];
       $Style3 = $infoBase['Style'];
       $Sex3 = $infoBase['Sex'];
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
                           <div class="p_border" style="">
                              <div class="f_nick" style="">
                              </div>
                              <?php
                                 $string1 = $Ddtank->GetStyleTemporada($Connect, 1, $ID);
                                 $string2 = $Ddtank->GetStyleTemporada($Connect, 2, $ID);
                                 $string3 = $Ddtank->GetStyleTemporada($Connect, 3, $ID);
                                 
                                 $arr1 = explode(',', $string1);
                                 
                                 $head1 = explode('|', $arr1[0]);
                                 
                                 $eff1 = explode('|', $arr1[3]);
                                 
                                 $hair1 = explode('|', $arr1[2]);
                                 
                                 $face1 = explode('|', $arr1[5]);
                                 
                                 $cloth1 = explode('|', $arr1[4]);
                                 
                                 $sex1 = ($Ddtank->GetSexByTemporada($Connect, 1, $ID)) ? 'm': 'f';
                                 
                                 $arr2 = explode(',', $string2);
                                 
                                 $head2 = explode('|', $arr2[0]);
                                 
                                 $eff2 = explode('|', $arr2[3]);
                                 
                                 $hair2 = explode('|', $arr2[2]);
                                 
                                 $face2 = explode('|', $arr2[5]);
                                 
                                 $cloth2 = explode('|', $arr2[4]);
                                 
                                 $sex2 = ($Ddtank->GetSexByTemporada($Connect, 2, $ID)) ? 'm': 'f';
                                 
                                 $arr3 = explode(',', $string3);
                                 
                                 $head3 = explode('|', $arr3[0]);
                                 
                                 $eff3 = explode('|', $arr3[3]);
                                 
                                 $hair3 = explode('|', $arr3[2]);
                                 
                                 $face3 = explode('|', $arr3[5]);
                                 
                                 $cloth3 = explode('|', $arr3[4]);
                                 
                                 $arm1 = explode('|', $arr1[6]);
                                 $arm2 = explode('|', $arr2[6]);
                                 $arm3 = explode('|', $arr3[6]);
                                 
                                 $sex3 = ($Ddtank->GetSexByTemporada($Connect, 3, $ID)) ? 'm': 'f';
                                 
                                 ?>
                              <div class="p_picture" id="p_picture">
                                 <!--<div class="f_head"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex1 ?>/head/<?= (!empty($head1[1]))? ($head1[1]): 'default' ?>/1/show.png"></div>-->
                                 <div class="f_hair"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex1 ?>/hair/<?= (!empty($hair1[1]))? ($hair1[1]): 'default' ?>/1/B/show.png"></div>
                                 <div class="f_effect"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex1 ?>/eff/<?= (!empty($eff1[1]))? ($eff1[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_face"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/<?php echo $sex1 ?>/face/<?= (!empty($face1[1]))? ($face1[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_cloth"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/<?php echo $sex1 ?>/cloth/<?= (!empty($cloth1[1]))? ($cloth1[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_arm">
                                    <img src="<?php echo $Resource ?>arm/<?= (!empty($arm1[1]))? ($arm1[1]): 'axe' ?>/1/0/show.png"> 
                                 </div>
                                 <div class="i_grade" style="background-image: url('../assets/images/grade/<?php echo $Ddtank->GetLevelTemporada($Connect, 1, $ID) ?>.png');"></div>
                              </div>
                              <div class="p_picture2" id="p_picture2">
                                 <!--<div class="f_head"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex2 ?>/head/<?= (!empty($head2[1]))? ($head2[1]): 'default' ?>/1/show.png"></div>-->
                                 <div class="f_hair"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex2 ?>/hair/<?= (!empty($hair2[1]))? ($hair2[1]): 'default' ?>/1/B/show.png"></div>
                                 <div class="f_effect"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex2 ?>/eff/<?= (!empty($eff2[1]))? ($eff2[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_face"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/<?php echo $sex2 ?>/face/<?= (!empty($face2[1]))? ($face2[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_cloth"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/<?php echo $sex2 ?>/cloth/<?= (!empty($cloth2[1]))? ($cloth2[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_arm">
                                    <img src="<?php echo $Resource ?>arm/<?= (!empty($arm2[1]))? ($arm2[1]): 'axe' ?>/1/0/show.png"> 
                                 </div>
                                 <div class="i_grade" style="background-image: url('../assets/images/grade/<?php echo $Ddtank->GetLevelTemporada($Connect, 2, $ID) ?>.png');"></div>
                              </div>
                              <div class="p_picture3" id="p_picture3">
                                 <!--<div class="f_head"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex3 ?>/head/<?= (!empty($head3[1]))? ($head3[1]): 'default' ?>/1/show.png"></div>-->
                                 <div class="f_hair"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex3 ?>/hair/<?= (!empty($hair3[1]))? ($hair3[1]): 'default' ?>/1/B/show.png"></div>
                                 <div class="f_effect"><img alt="DDTank" src="<?php echo $Resource ?>equip/<?php echo $sex3 ?>/eff/<?= (!empty($eff3[1]))? ($eff3[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_face"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/<?php echo $sex3 ?>/face/<?= (!empty($face3[1]))? ($face3[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_cloth"><img alt="DDTank" data-current="0" src="<?php echo $Resource ?>equip/<?php echo $sex3 ?>/cloth/<?= (!empty($cloth3[1]))? ($cloth3[1]): 'default' ?>/1/show.png"></div>
                                 <div class="f_arm">
                                    <img alt="DDTank" src="<?php echo $Resource ?>arm/<?= (!empty($arm3[1]))? ($arm3[1]): 'axe' ?>/1/0/show.png"> 
                                 </div>
                                 <div class="i_grade" style="background-image: url('../assets/images/grade/<?php echo $Ddtank->GetLevelTemporada($Connect, 3, $ID) ?>.png');"></div>
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
                                    <tbody>
                                       <tr>
                                          <th scope="row">1</th>
                                          <td><?php echo $Nome1 ?></td>
                                          <td><?php echo $Level1 ?></td>
                                          <td><?php echo $PartidasJogadas1 ?></td>
                                          <td><?php echo $PartidasGanhas1 ?></td>
                                          <td><?php echo $Poder1 ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">2</th>
                                          <td><?php echo $Nome2 ?></td>
                                          <td><?php echo $Level2 ?></td>
                                          <td><?php echo $PartidasJogadas2 ?></td>
                                          <td><?php echo $PartidasGanhas2 ?></td>
                                          <td><?php echo $Poder2 ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">3</th>
                                          <td><?php echo $Nome3 ?></td>
                                          <td><?php echo $Level3 ?></td>
                                          <td><?php echo $PartidasJogadas3 ?></td>
                                          <td><?php echo $PartidasGanhas3 ?></td>
                                          <td><?php echo $Poder3 ?></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                     </div>
                  </div>
                  </br>
                  <div class="alert alert-light" role="alert">
                     Os jogadores que se manterem até o final da temporada no RANK estarão expostos nesse mural até o final da próxima temporada, as recompensas do hall da fama são as seguintes: TOP 1 <a style="color:green!important">R$99,99</a> em recarga + Título <b style="color:black!important">Rei da Temporada</b>, TOP 2 <a style="color:green!important">R$69,99</a> em recarga + Título <b style="color:black!important">Imperador da Temporada</b>, TOP 3 <a style="color:green!important">R$29,99</a> em recarga + Título <b style="color:black!important">Duque da Temporada</b>, caso o jogador não responda o nosso contato a recarga será destinada ao jogador do rank anterior. Jogadores do Rank 4 ao 10 receberão uma recarga de R$14,99.
                  </div>
                  <div class="container-login100-form-btn p-t-25"><a class="server-form-btn" style="color:white;" href="/rank?suv=<?php echo $i ?>">Voltar</a></div>
               </div>
            </div>
         </div>
      </div>
      <div class="fixed-bottom text-center p-0 text-white footer">Você precisa de suporte? <a href="/ticket?suv=<?php echo $i ?>">Clique aqui e abra um ticket.</a></div>
   </body>
</html>