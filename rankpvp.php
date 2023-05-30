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
		$BaseTank = $infoBase['BaseTank'];
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
   <title><?php echo $Title; ?> Rank pvp</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
               <div id="main_form">
                  <span class="login100-form-title p-b-55">RANKING DE BATALHAS</span>
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
                                    <tbody>
                                       <tr>
                                          <th scope="row">1</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 168, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 178, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 188, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 198, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 208, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">2</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 169, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 179, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 189, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 199, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 209, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">3</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 170, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 180, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 190, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 200, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 210, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">4</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 171, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 181, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 191, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 201, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 211, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">5</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 172, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 182, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 192, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 202, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 212, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">6</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 173, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 183, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 193, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 203, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 213, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">7</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 174, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 184, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 194, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 204, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 214, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">8</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 175, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 185, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 195, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 205, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 215, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">9</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 176, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 186, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 196, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 206, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 216, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">10</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 177, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 187, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 197, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 207, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 217, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                    </tbody>
                                 </table>
                           </div>
                     </div>
                  </div>
                  <div class="container-login100-form-btn p-t-25"><a class="server-form-btn" style="color:white;" href="/rank?suv=<?php echo $i ?>">Voltar</a></div>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>