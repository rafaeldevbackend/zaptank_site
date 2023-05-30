<?php
class Loading
{
    public function pageTry($Connect, $BaseServer, $BaseUser, $Dados, $Ddtank, $ServerList, $Extra, $ServerIDN)
    {
        $CheckAccount = $ServerList->userServer($Connect, $BaseServer, $BaseUser);
        if ($CheckAccount >= 1)
        {
			$query = $Connect->query("SELECT PassWord FROM Db_Center.dbo.Mem_UserInfo WHERE Email = '$_SESSION[UserName]'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
	           $passmd5 = strtoupper($infoBase['PassWord']);
            }
			if ($_SESSION['PassWord'] != $passmd5){session_destroy();header("Location: /");exit();}
            $ServerList->checkServer($Connect, $BaseServer, addslashes($ServerIDN) , $Extra);
            $GetServerInfo = $ServerList->serverInfo($Connect, $BaseServer, addslashes($ServerIDN));
            $infoServer = $ServerList->serverInfo($Connect, $BaseServer, addslashes($ServerIDN));
            foreach ($infoServer as $serverInfo) foreach ($GetServerInfo as $ServerInfo)
            {
                $ServerIDN = $ServerInfo['ID'];
            }
            $CheckBanned = $Dados->checkBanned($Connect, $BaseUser);
            if ($CheckBanned == 0)
            {
                header("Location: selectserver");
                $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>Sua conta foi suspensa, entre em contato com o suporte para obter mais informações.</div>";
				exit();
            }
        }
        else
        {
            header("Location: selectserver");
            $_SESSION['alert_newaccount'] = "<div class='alert alert-danger ocult-time'>O servidor que você tentou acessar não existe ou está restrito para um pequeno número de pessoas.</div>";
			exit();
        }
    }
}
?>