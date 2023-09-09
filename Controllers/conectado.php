<?php
class Conectado
{
    public function checkAdmin($Connect, $BaseUser, $UserID)
    {
        $query = $Connect->query("SELECT COUNT(*) AS CheckAdmin FROM $BaseUser.dbo.comandos WHERE userid = '" . $UserID . "'");
        $result = $query->fetchAll();
        foreach ($result as $commandInfo)
        {
            $CheckAdmin = $commandInfo['CheckAdmin'];
        }
        return $CheckAdmin;
    }
    public function checkBanned($Connect, $BaseUser)
    {
        $query = $Connect->query("SELECT COUNT(*) AS CheckBanned FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]' AND IsExist = 1");
        $result = $query->fetchAll();
        foreach ($result as $bannedInfo)
        {
            $CheckBanned = $bannedInfo['CheckBanned'];
        }
        return $CheckBanned;
    }
    public function checkBadMail($Connect)
    {
        $query = $Connect->query("SELECT COUNT(*) AS BadMail FROM Db_Center.dbo.Mem_UserInfo WHERE Email = '$_SESSION[UserName]' AND BadMail = '1' AND VerifiedEmail = '0'");
        $result = $query->fetchAll();
        foreach ($result as $bannedInfo)
        {
            $BadMail = $bannedInfo['BadMail'];
        }
        return $BadMail;
    }
    public function NickName($Connect, $TankUser)
    {
        $query = $Connect->query("SELECT TOP 1 NickName FROM $TankUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $NickName = $infoBase['NickName'];
        }
        echo $NickName;
    }
    public function NickName2($Connect, $TankUser)
    {
        $query = $Connect->query("SELECT TOP 2 NickName FROM $TankUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $NickName = $infoBase['NickName'];
        }
        echo $NickName;
    }
    public function gameState($Connect, $TankUser)
    {
        $query = $Connect->query("SELECT TOP 1 State FROM $TankUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $State = $infoBase['State'];
        }
        if ($State == 1)
        {
            echo "<font color='blue'>conectado</font>";
        }
        else
        {
            $query = $Connect->query("SELECT TOP 1 IsExist FROM $TankUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $IsExist = $infoBase['IsExist'];
            }
            if ($IsExist == 0)
            {
                echo "<font color='purple'>desconectado²</font>";
            }
            else
            {
                echo "<font color='red'>desconectado</font>";
            }
        }
    }

    public function StartSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start(['cookie_lifetime' => 2592000, 'cookie_secure' => true, 'cookie_httponly' => true]);
			if (empty($_SESSION['LockOutEndDate']))
            {
				 $_SESSION['LockOutEndDate'] = date("Y-m-d H:i:s");
			}
            if (empty($_SESSION['ReferenceLocation']))
            {
                if (!empty($_SERVER['HTTP_REFERER']))
                {
                    if (str_contains($_SERVER['HTTP_REFERER'], 'google'))
                    {
                        $_SESSION['ReferenceLocation'] = 'Google';
						setcookie('ReferenceLocation', 'Google');
                    }
                    else if (str_contains($_SERVER['HTTP_REFERER'], 'facebook'))
                    {
                        $_SESSION['ReferenceLocation'] = 'Facebook';
						setcookie('ReferenceLocation', 'Facebook');
                    }
                    else
                    {
                        $_SESSION['ReferenceLocation'] = 'Other';
						setcookie('ReferenceLocation', 'Other');
                    }
                }
                else
                {
                    $_SESSION['ReferenceLocation'] = 'Other';
					setcookie('ReferenceLocation', 'Other');
                }
            }
            else if (!empty($_GET['fbclid']))
            {
                $_SESSION['ReferenceLocation'] = 'Facebook';
				setcookie('ReferenceLocation', 'Facebook');
            }
            else if (!empty($_GET['gclid']))
            {
                $_SESSION['ReferenceLocation'] = 'Google';
				setcookie('ReferenceLocation', 'Google');
            }
        }
    }

    public function CheckConnect()
    {
        if (isset($_SESSION))
        {
            if (isset($_SESSION['Status']) == "Conectado")
            {
                header("Location: selectserver");
                exit();
            }
        }
    }

    public function Destroy()
    {
        if (!empty($_GET['logout']))
        {
            switch ($_GET['logout'])
            {
                case 'true':
                    session_destroy();
                    header("Location: /");
                    exit();
                break;
            }
        }
        if (!empty($_SESSION['Status']) <> "Conectado")
        {
            header("Location: /");
            exit();
        }
    }
}
?>