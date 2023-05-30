<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

if (!empty($_GET['server']))
{
    $i = $_GET['server'];
    $DecryptServer = $Ddtank->DecryptText($KeyPublicCrypt, $KeyPrivateCrypt, $i);
    $query = $Connect->query("SELECT * FROM Db_Center.dbo.Server_List WHERE ID = '$DecryptServer'");
    $result = $query->fetchAll();
    foreach ($result as $infoBase)
    {
        $ID = $infoBase['ID'];
        $BaseUser = $infoBase['BaseUser'];
        $Release = $infoBase['Release'];
        $Temporada = $infoBase['Temporada'];
        $Maintenance = $infoBase['Maintenance'];;
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
   <title><?php echo $Title; ?> Recarga</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-40 p-b-30">
         <?php
            if (!empty($_GET['page']) && !empty($_GET['server']))
            {
                switch ($_GET['page'])
                {
                    case 'vipitemlist':
                        require_once "Internal/listarpacote.php";
                    break;
					     case 'invoice':
                        require_once "Internal/fatura.php";
                    break;
                    case 'card':
                     require_once "Internal/card.php";
                    break;
                    default:
                        echo '
                        <div class="alert alert-primary media" role="alert">
                        <i class="tio-warning mt-1 mr-1"></i>
                        <div class="media-body" role="alert">
                        Desculpe, não conseguimos encontrar as <a class="alert-link">informações</a> que você deseja, clique <a class="alert-link" href="javascript:history.back()"><font color="#ffffff">aqui</a></font> para retornar à página anterior.
                        </div>
                        </div>            
                        ';
                    break;
                }
               }
               else
               {
                  echo "<div class='alert alert-danger ocult-time'>Não foi possível carregar fatura...</div>";
                  echo "<script>window.setTimeout(function(){window.location='serverlist';}, 2000); </script>";
               }
            ?>
            </div>
         </div>
      </div>
      <script async src="./assets/main.js"></script>
   </body>
</html>