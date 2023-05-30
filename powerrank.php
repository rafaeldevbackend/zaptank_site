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
                                    <tbody>
                                       <tr>
                                          <th scope="row">1</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 128, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 138, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 148, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 158, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 118, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">2</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 129, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 139, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 149, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 159, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 119, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">3</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 130, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 140, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 150, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 160, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 120, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">4</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 131, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 141, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 151, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 161, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 121, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">5</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 132, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 142, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 152, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 162, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 122, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">6</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 133, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 143, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 153, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 163, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 123, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">7</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 134, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 144, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 154, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 164, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 124, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">8</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 135, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 145, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 155, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 165, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 125, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">9</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 136, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 146, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 156, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 166, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 126, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">10</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 137, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 147, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 157, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 167, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 127, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
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