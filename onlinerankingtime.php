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
   <title><?php echo $Title; ?> Rank tempo Online</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
               <div id="main_form">
			      <span class="login100-form-title p-b-55">RANKING TEMPO ONLINE</span>
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
                                          <th scope="col">Minutos Online</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                          <th scope="row">1</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 228, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 238, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 248, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 258, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 268, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">2</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 229, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 239, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 249, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 259, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 269, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">3</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 230, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 240, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 250, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 260, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 270, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">4</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 231, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 241, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 251, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 261, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 271, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">5</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 232, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 242, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 252, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 262, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 272, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">6</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 233, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 243, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 253, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 263, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 273, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">7</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 234, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 244, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 254, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 264, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 274, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">8</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 235, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 245, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 255, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 265, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 275, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">9</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 236, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 246, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 256, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 266, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 276, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                       </tr>
                                       <tr>
                                          <th scope="row">10</th>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 237, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 247, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 257, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 267, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
                                          <td><?php $Ddtank->Rank($Connect, $Request = 277, ''.$BaseTank.'', ''.$BaseUser.''); ?></td>
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