<?php
class Entrar
{

    public function LockOutEndDate()
    {
        $new_time = date("Y-m-d H:i:s");
        $start_date = new DateTime($new_time);
        $since_start = $start_date->diff(new DateTime($_SESSION['PostDate']));
        $v = $since_start->s - 1;

        if ($v < 0)
        {
			$error = "logs/logslogin.txt";
			$user_ip = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER['REMOTE_ADDR'];
            file_put_contents($error, $user_ip . PHP_EOL . basename(__FILE__, '.php') . PHP_EOL, FILE_APPEND | LOCK_EX);
            if (!empty($_SESSION['LockOutEndDate']))
            {
                $duration = 59;
                $new_time = date("Y-m-d H:i:s", strtotime("+$duration sec"));
                $_SESSION['LockOutEndDate'] = $new_time;
            }
        }
    }

    public function LoginSystem($Connect, $BaseServer, $app, $email, $p)
    {
        date_default_timezone_set("America/Sao_Paulo");
        $a0 = new DateTime($_SESSION['LockOutEndDate']);
        $a1 = $a0->diff(new DateTime(date('Y-m-d H:i:s')));

        if ($_SESSION['LockOutEndDate'] < date('Y-m-d H:i:s') || $a1->s == 0)
        {
			$stmt = $Connect->prepare("SELECT COUNT(*) AS CheckUser FROM $BaseServer.dbo.Mem_UserInfo WHERE Email = :email");
			$stmt->bindParam(':email', $email);
			$stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $infoBase)
            {
                $CheckUser = $infoBase['CheckUser'];
            }
            if ($CheckUser == 0)
            {
                $_SESSION['msg'] = "<div class='alert alert-danger ocult-time'>Não foi possível encontrar esse usuário em nosso banco dados...</div>";
                if (isset($_SESSION['PostDate']))
                {
                    $this->LockOutEndDate();
                }
                $_SESSION['PostDate'] = date("Y-m-d H:i:s");
            }
            else
            {
				$stmt = $Connect->prepare("SELECT UserId, IsBanned, Telefone FROM $BaseServer.dbo.Mem_UserInfo WHERE Email = :email");
			    $stmt->bindParam(':email', $email);
				$stmt->execute();
                $result = $stmt->fetchAll();
                foreach ($result as $infoBase)
                {
                    $UserId = $infoBase['UserId'];
                    $IsBanned = $infoBase['IsBanned'];
                    $Telefone = $infoBase['Telefone'];
                }
				$stmt = $Connect->prepare("SELECT COUNT(*) AS CheckPass FROM $BaseServer.dbo.Mem_UserInfo WHERE Password = :p AND UserId = :UserId");
			    $stmt->bindParam(':p', $p);
				$stmt->bindParam(':UserId', $UserId);
				$stmt->execute();	
                $result = $stmt->fetchAll();
                foreach ($result as $infoBase)
                {
                    $CheckPass = $infoBase['CheckPass'];
                }
                if ($CheckPass == 0)
                {
                    $_SESSION['msg'] = "<div class='alert alert-danger ocult-time'>Opa! encontramos esse usuário no nosso banco de dados mas parece que a senha está incorreta....</div>";
                    if (isset($_SESSION['PostDate']))
                    {
                        $this->LockOutEndDate();
                    }
                    $_SESSION['PostDate'] = date("Y-m-d H:i:s");
                }
                else if (!$IsBanned)
                {
                    $_SESSION['UserName'] = $email;
                    $_SESSION['UserId'] = $UserId;
                    $_SESSION['PassWord'] = $p;
                    $_SESSION['Telefone'] = $Telefone;
                    $_SESSION['Status'] = "Conectado";
                    $_SESSION['msg'] = "<div class='alert alert-success ocult-time'>Login bem-sucedido, você será redirecionado em breve...</div>";
					echo "<meta http-equiv='refresh' content='2;url=/selectserver' />";
                }
                else
                {
                    if (isset($_SESSION['PostDate']))
                    {
                        $this->LockOutEndDate();
                    }
                    $_SESSION['PostDate'] = date("Y-m-d H:i:s");
                    $_SESSION['msg'] = "<div class='alert alert-danger ocult-time'>Sua conta foi suspensa, entre em contato com o suporte para obter mais informações.</div>";
                }
            }
        }
        else
        {
            if (isset($_SESSION['PostDate']))
            {
                $this->LockOutEndDate();
            }
            $_SESSION['PostDate'] = date("Y-m-d H:i:s");
            $_SESSION['msg'] = "<div class='alert alert-danger ocult-time'>Seu acesso está restrito aguarde $a1->s segundos e tente novamente.</div>";
        }
    }
}
?>