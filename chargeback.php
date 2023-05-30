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
		$AreaID = $infoBase['AreaID'];
		$QuestUrl = $infoBase['QuestUrl'];
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
   <title><?php echo $Title; ?> Obter novamente</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
			 <span class="login100-form-title p-b-55">
                  Pacotes de chargeback disponíveis.
             </span>
             <?php // $Pacotes->ShowChargeBack($Connect, $BaseServer, $BaseUser, $QuestUrl, $AreaID); ?>
			 <div class="alert alert-danger" role="alert">A coleta estará disponível no dia 05/05/2023 às 4:00 A.M</div>
			 <div class="alert alert-warning" role="alert">É proibido a venda de itens, contas ou cupons dentro do jogo por dinheiro real, caso descumpra essa regra reservamos o direito de desabilitar a conta imediatamente da plataforma.</div>
			 <div class="container-login100-form-btn p-t-25" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/serverlist?suv=<?php echo $i ?>">voltar</a></div>
            </div>
         </div>
      </div>
      <script async src="./assets/main.js"></script>
   </body>
</html>