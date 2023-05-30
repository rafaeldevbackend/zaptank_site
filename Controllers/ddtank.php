<?php
class Ddtank
{

    public static function safe_b64encode($string = '')
    {
        $data = base64_encode($string);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);
        return $data;
    }

    public static function safe_b64decode($string = '')
    {
        $data = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($data) % 4;
        if ($mod4)
        {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function EncryptText($KeyPublicCrypt, $KeyPrivateCrypt, string $message)
    {
        $KeySK = pack('H*', $KeyPublicCrypt);
        $KeyPK = pack('H*', $KeyPrivateCrypt);
        $KeysKP = sodium_crypto_box_keypair_from_secretkey_and_publickey($KeySK, $KeyPK);
        $Nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
        $Text = sodium_crypto_box($message, $Nonce, $KeysKP);
        return $this->safe_b64encode($Nonce . $Text);
    }

    public function DecryptText($KeyPublicCrypt, $KeyPrivateCrypt, string $message)
    {
        try
        {
            $Resultado = $this->safe_b64decode($message);
            $Text = mb_substr($Resultado, SODIUM_CRYPTO_BOX_NONCEBYTES, null, '8bit');
            $Nonce = mb_substr($Resultado, 0, SODIUM_CRYPTO_BOX_NONCEBYTES, '8bit');
            $KeySK = pack('H*', $KeyPublicCrypt);
            $KeyPK = pack('H*', $KeyPrivateCrypt);
            $KeysKP = sodium_crypto_box_keypair_from_secretkey_and_publickey($KeySK, $KeyPK);
            $TextEcho = sodium_crypto_box_open($Text, $Nonce, $KeysKP);
            return ($TextEcho ? : '0');
        }
        catch(Exception $e)
        {
            // echo 'Exceção capturada: ', $e->getMessage() , "\n";
        }
    }

    public function GetLevel($Connect, $BaseTank)
    {
        if (!empty($_SESSION['UserName']))
        {
			$Grade = 1;
            $query = $Connect->query("SELECT Grade FROM $BaseTank.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $Grade = $infoBase['Grade'];
            }
            return $Grade;
        }
    }

    public function GetStyle($Connect, $BaseTank)
    {
        if (!empty($_SESSION['UserName']))
        {
			$Style = 0;
            $query = $Connect->query("SELECT Style FROM $BaseTank.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $Style = $infoBase['Style'];
            }
            return $Style;
        }
    }
	
	public function GetPicAndSex($Connect, $BaseTank, $TemplateID)
    {
			$value = 0;
            $query = $Connect->query("SELECT Pic, NeedSex FROM $BaseTank.dbo.Shop_Goods WHERE TemplateID ='$TemplateID'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $Pic = $infoBase['Pic'];
				$NeedSex = $infoBase['NeedSex'];
				$value = "$Pic|$NeedSex";
            }
            return $value;
    }

    public function GetSex($Connect, $BaseTank)
    {
        if (!empty($_SESSION['UserName']))
        {
            $query = $Connect->query("SELECT Sex FROM $BaseTank.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $Sex = $infoBase['Sex'];
            }
            return $Sex;
        }
    }

    public function GetSexByTemporada($Connect, $num, $ServerID)
    {
        $query = $Connect->query("SELECT Sex FROM Db_Center.dbo.Rank_Temporada WHERE Rank = '$num' AND ServerID = '$ServerID'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Sex = $infoBase['Sex'];
        }
        return $Sex;
    }

    public function GetStyleTemporada($Connect, $num, $ServerID)
    {
        $query = $Connect->query("SELECT Style FROM Db_Center.dbo.Rank_Temporada WHERE Rank = '$num' AND ServerID = '$ServerID'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Style = $infoBase['Style'];
        }
        return $Style;
    }

    public function GetLevelTemporada($Connect, $num, $ServerID)
    {
        $query = $Connect->query("SELECT Level FROM Db_Center.dbo.Rank_Temporada WHERE Rank = '$num' AND ServerID = '$ServerID'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Level = $infoBase['Level'];
        }
        return $Level;
    }

    public function DetectLogger($Connect, $BaseTank, $BaseUser)
    {
        $query = $Connect->query("UPDATE Sys_Users_Detail SET IsLogger = 'True' WHERE UserID='$_SESSION[UserID]'");
    }

    public function PicPaySelect($Connect, $BaseTank, $BaseUser, $Price)
    {
        $query = $Connect->query("SELECT ID FROM Db_Center.dbo.Vip_List WHERE ValuePrice = '$Price'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $ID = $infoBase['ID'];
        }
        return $ID;
    }
	
    public function Personagens($Connect, $TankServer, $TankUser)
    {
        $query = $Connect->query("SELECT Count (1) FROM $TankUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Count = $infoBase['1'];
        }
        return $Count;
    }
	
    public function AdminPermission($Connect)
    {
        $query = $Connect->query("SELECT COUNT(*) AS UserName FROM Db_Center.dbo.Admin_Permission WHERE UserName = '$_SESSION[UserName]'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $UserName = $infoBase['UserName'];
        }
        return $UserName;
    }
	
    public function Servidor($Connect, $Request, $BaseTank, $BaseUser)
    {
        switch ($Request)
        {
            case '102':
                $query = $Connect->query("SELECT COUNT(*) AS Cadastros FROM $BaseUser.dbo.Sys_Users_Detail");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Cadastros = $infoBase['Cadastros'];
                }
                echo $Cadastros * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '103':
                $query = $Connect->query("SELECT COUNT(*) AS Sociedades FROM $BaseUser.dbo.Consortia");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Sociedades = $infoBase['Sociedades'];
                }
                echo $Sociedades;
            break;
            case '104':
                $query = $Connect->query("SELECT COUNT(*) AS Itens FROM $BaseUser.dbo.Sys_Users_Goods");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Itens = $infoBase['Itens'];
                }
                echo $Itens;
            break;
            case '105':
                $query = $Connect->query("SELECT Date FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Date = $infoBase['Date'];
                }
                $Date;
                echo date("d-m-Y", strtotime($Date));
            break;
            case '106':
                $query = $Connect->query("SELECT COUNT(*) AS Itens FROM $BaseTank.dbo.Shop_Goods");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Itens = $infoBase['Itens'];
                }
                echo $Itens * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '107':
                $query = $Connect->query("SELECT COUNT(*) AS Eventos FROM $BaseTank.dbo.Active");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Eventos = $infoBase['Eventos'];
                }
                echo $Eventos * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '108':
                $query = $Connect->query("SELECT COUNT(*) AS Montarias FROM $BaseTank.dbo.Mount_Draw_Template");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Montarias = $infoBase['Montarias'];
                }
                echo $Montarias * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '109':
                $query = $Connect->query("SELECT COUNT(*) AS Instâncias FROM $BaseTank.dbo.Pve_Info");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Instâncias = $infoBase['Instâncias'];
                }
                echo $Instâncias * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '110':
                $query = $Connect->query("SELECT COUNT(*) AS Quest FROM $BaseTank.dbo.Quest");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Quest = $infoBase['Quest'];
                }
                echo $Quest * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '112':
                $query = $Connect->query("SELECT COUNT(*) AS PetTemplate FROM $BaseTank.dbo.Pet_Template_Info");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $PetTemplate = $infoBase['PetTemplate'];
                }
                echo $PetTemplate * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '113':
                $query = $Connect->query("SELECT COUNT(*) AS PetForm FROM $BaseTank.dbo.Pet_Form_Data");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $PetForm = $infoBase['PetForm'];
                }
                echo $PetForm * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '114':
                $query = $Connect->query("SELECT COUNT(*) AS Mapas FROM $BaseTank.dbo.Game_Map");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Mapas = $infoBase['Mapas'];
                }
                echo $Mapas * 1; // Quantidade de usuários, exemplo: 1 = Usuários reais / 2 = Duplicado / 3 = Triplicado em diante.
                
            break;
            case '115':
                $query = $Connect->query("SELECT COUNT(*) AS Contas FROM $BaseUser.dbo.Sys_Users_Detail Where UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Contas = $infoBase['Contas'];
                }
                echo $Contas;
            break;
        }
    }
    public function Jogador($Connect, $Request, $BaseTank, $BaseUser)
    {
        switch ($Request)
        {
            case '106':
                $query = $Connect->query("SELECT Money FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Money = $infoBase['Money'];
                }
                echo $Money;
            break;
            case '107':
                $query = $Connect->query("SELECT Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '108':
                $query = $Connect->query("SELECT A.Grade + 1 AS Grade, B.GP, A.GP AS PGP FROM $BaseUser.dbo.Sys_Users_Detail A LEFT JOIN $BaseUser.dbo.LevelInfo B ON A.Grade + 1 = B.Grade WHERE UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $GP = $infoBase['GP']; //GP Do Nivel + 1
                    $PGP = $infoBase['PGP']; //GP Do Jogador
                    
                }
                $Quero = $GP;
                $Tenho = $PGP;
                $Resposta = $Quero - $Tenho;
                $Solution = $Resposta / $Quero * 100;
                $Solution2 = intval($Solution);
                $Solution3 = 100 - $Solution2;
                echo "$Solution3%";
            break;
            case '109':
                $query = $Connect->query("select sum(OnlineTime) as Time from $BaseUser.dbo.Sys_Users_Detail Where UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Time = $infoBase['Time'];
                }
                echo $Time / 60;
            break;
        }
    }
    public function Poder($Connect, $Request, $BaseTank, $BaseUser)
    {
        switch ($Request)
        {
            case '109':
                $query = $Connect->query("SELECT FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '110':
                $query = $Connect->query("SELECT Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '111':
                $query = $Connect->query("SELECT Win, Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                    $Total = $infoBase['Total'];
                }
                $Derrotas = $Total - $Win;
                echo $Derrotas;
            break;
            case '112':
                $query = $Connect->query("SELECT Win, Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                    $Total = $infoBase['Total'];
                }
                $Derrota = $Total - $Win;
                $Taxa = $Derrota / $Total * 100;
                $Taxa = intval($Taxa);
                $Taxada = 100 - $Taxa;
                echo $Taxada;
            break;
            case '113':
                $query = $Connect->query("SELECT hp FROM $BaseUser.dbo.Sys_Users_Fight WHERE UserID = '$UserID'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $hp = $infoBase['hp'];
                }
                echo $hp;
            break;
        }
    }
    public function Atributo($Connect, $Request, $BaseTank, $BaseUser)
    {
        switch ($Request)
        {
            case '114':
                $query = $Connect->query("SELECT Attack FROM $BaseUser.dbo.Sys_Users_Fight WHERE UserID = '$UserID'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Attack = $infoBase['Attack'];
                }
                echo $Attack;
            break;
            case '115':
                $query = $Connect->query("SELECT Defence FROM $BaseUser.dbo.Sys_Users_Fight WHERE UserID = '$UserID'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Defence = $infoBase['Defence'];
                }
                echo $Defence;
            break;
            case '116':
                $query = $Connect->query("SELECT Agility FROM $BaseUser.dbo.Sys_Users_Fight WHERE UserID = '$UserID'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Agility = $infoBase['Agility'];
                }
                echo $Agility;
            break;
            case '117':
                $query = $Connect->query("SELECT Luck FROM $BaseUser.dbo.Sys_Users_Fight WHERE UserID = '$UserID'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Luck = $infoBase['Luck'];
                }
                echo $Luck;
            break;
        }
    }
    public function Rank($Connect, $Request, $BaseTank, $BaseUser)
    {
        switch ($Request)
        {
            case '118':
                $query = $Connect->query("SELECT TOP 1 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '119':
                $query = $Connect->query("SELECT TOP 2 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '120':
                $query = $Connect->query("SELECT TOP 3 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '121':
                $query = $Connect->query("SELECT TOP 4 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '122':
                $query = $Connect->query("SELECT TOP 5 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '123':
                $query = $Connect->query("SELECT TOP 6 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '124':
                $query = $Connect->query("SELECT TOP 7 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '125':
                $query = $Connect->query("SELECT TOP 8 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '126':
                $query = $Connect->query("SELECT TOP 9 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '127':
                $query = $Connect->query("SELECT TOP 10 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '128':
                $query = $Connect->query("SELECT TOP 1 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '129':
                $query = $Connect->query("SELECT TOP 2 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '130':
                $query = $Connect->query("SELECT TOP 3 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '131':
                $query = $Connect->query("SELECT TOP 4 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '132':
                $query = $Connect->query("SELECT TOP 5 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '133':
                $query = $Connect->query("SELECT TOP 6 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '134':
                $query = $Connect->query("SELECT TOP 7 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '135':
                $query = $Connect->query("SELECT TOP 8 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '136':
                $query = $Connect->query("SELECT TOP 9 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '137':
                $query = $Connect->query("SELECT TOP 10 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '138':
                $query = $Connect->query("SELECT TOP 1 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '139':
                $query = $Connect->query("SELECT TOP 2 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '140':
                $query = $Connect->query("SELECT TOP 3 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '141':
                $query = $Connect->query("SELECT TOP 4 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '142':
                $query = $Connect->query("SELECT TOP 5 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '143':
                $query = $Connect->query("SELECT TOP 6 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '144':
                $query = $Connect->query("SELECT TOP 7 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '145':
                $query = $Connect->query("SELECT TOP 8 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '146':
                $query = $Connect->query("SELECT TOP 9 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '147':
                $query = $Connect->query("SELECT TOP 10 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '148':
                $query = $Connect->query("SELECT TOP 1 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '149':
                $query = $Connect->query("SELECT TOP 2 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '150':
                $query = $Connect->query("SELECT TOP 3 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '151':
                $query = $Connect->query("SELECT TOP 4 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '152':
                $query = $Connect->query("SELECT TOP 5 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '153':
                $query = $Connect->query("SELECT TOP 6 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '154':
                $query = $Connect->query("SELECT TOP 7 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '155':
                $query = $Connect->query("SELECT TOP 8 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '156':
                $query = $Connect->query("SELECT TOP 9 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '157':
                $query = $Connect->query("SELECT TOP 10 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '158':
                $query = $Connect->query("SELECT TOP 1 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '159':
                $query = $Connect->query("SELECT TOP 2 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '160':
                $query = $Connect->query("SELECT TOP 3 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '161':
                $query = $Connect->query("SELECT TOP 4 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '162':
                $query = $Connect->query("SELECT TOP 5 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '163':
                $query = $Connect->query("SELECT TOP 6 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '164':
                $query = $Connect->query("SELECT TOP 7 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '165':
                $query = $Connect->query("SELECT TOP 8 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '166':
                $query = $Connect->query("SELECT TOP 9 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '167':
                $query = $Connect->query("SELECT TOP 10 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY FightPower DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '168':
                $query = $Connect->query("SELECT TOP 1 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '169':
                $query = $Connect->query("SELECT TOP 2 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '170':
                $query = $Connect->query("SELECT TOP 3 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '171':
                $query = $Connect->query("SELECT TOP 4 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '172':
                $query = $Connect->query("SELECT TOP 5 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '173':
                $query = $Connect->query("SELECT TOP 6 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '174':
                $query = $Connect->query("SELECT TOP 7 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '175':
                $query = $Connect->query("SELECT TOP 8 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '176':
                $query = $Connect->query("SELECT TOP 9 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '177':
                $query = $Connect->query("SELECT TOP 10 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['NickName'];
                }
                echo $FightPower;
            break;
            case '178':
                $query = $Connect->query("SELECT TOP 1 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '179':
                $query = $Connect->query("SELECT TOP 2 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '180':
                $query = $Connect->query("SELECT TOP 3 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '181':
                $query = $Connect->query("SELECT TOP 4 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '182':
                $query = $Connect->query("SELECT TOP 5 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '183':
                $query = $Connect->query("SELECT TOP 6 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '184':
                $query = $Connect->query("SELECT TOP 7 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '185':
                $query = $Connect->query("SELECT TOP 8 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '186':
                $query = $Connect->query("SELECT TOP 9 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '187':
                $query = $Connect->query("SELECT TOP 10 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Grade'];
                }
                echo $FightPower;
            break;
            case '188':
                $query = $Connect->query("SELECT TOP 1 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '189':
                $query = $Connect->query("SELECT TOP 2 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '190':
                $query = $Connect->query("SELECT TOP 3 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '191':
                $query = $Connect->query("SELECT TOP 4 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '192':
                $query = $Connect->query("SELECT TOP 5 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '193':
                $query = $Connect->query("SELECT TOP 6 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '194':
                $query = $Connect->query("SELECT TOP 7 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '195':
                $query = $Connect->query("SELECT TOP 8 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '196':
                $query = $Connect->query("SELECT TOP 9 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '197':
                $query = $Connect->query("SELECT TOP 10 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Total'];
                }
                echo $FightPower;
            break;
            case '198':
                $query = $Connect->query("SELECT TOP 1 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '199':
                $query = $Connect->query("SELECT TOP 2 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '200':
                $query = $Connect->query("SELECT TOP 3 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '201':
                $query = $Connect->query("SELECT TOP 4 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '202':
                $query = $Connect->query("SELECT TOP 5 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '203':
                $query = $Connect->query("SELECT TOP 6 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '204':
                $query = $Connect->query("SELECT TOP 7 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true'ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '205':
                $query = $Connect->query("SELECT TOP 8 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '206':
                $query = $Connect->query("SELECT TOP 9 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '207':
                $query = $Connect->query("SELECT TOP 10 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['Win'];
                }
                echo $FightPower;
            break;
            case '208':
                $query = $Connect->query("SELECT TOP 1 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '209':
                $query = $Connect->query("SELECT TOP 2 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '210':
                $query = $Connect->query("SELECT TOP 3 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '211':
                $query = $Connect->query("SELECT TOP 4 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '212':
                $query = $Connect->query("SELECT TOP 5 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '213':
                $query = $Connect->query("SELECT TOP 6 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '214':
                $query = $Connect->query("SELECT TOP 7 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '215':
                $query = $Connect->query("SELECT TOP 8 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '216':
                $query = $Connect->query("SELECT TOP 9 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '217':
                $query = $Connect->query("SELECT TOP 10 FightPower FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY Win DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $FightPower = $infoBase['FightPower'];
                }
                echo $FightPower;
            break;
            case '218':
                $query = $Connect->query("SELECT TOP 1 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '219':
                $query = $Connect->query("SELECT TOP 2 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '220':
                $query = $Connect->query("SELECT TOP 3 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '221':
                $query = $Connect->query("SELECT TOP 4 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '222':
                $query = $Connect->query("SELECT TOP 5 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '223':
                $query = $Connect->query("SELECT TOP 6 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '224':
                $query = $Connect->query("SELECT TOP 7 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '225':
                $query = $Connect->query("SELECT TOP 8 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '226':
                $query = $Connect->query("SELECT TOP 9 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '227':
                $query = $Connect->query("SELECT TOP 10 Price from $BaseTank.dbo.eventoPntsCategory WHERE IsExist ='true' ORDER BY price DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Price = $infoBase['Price'];
                }
                echo $Price;
            break;
            case '228':
                $query = $Connect->query("SELECT TOP 1 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '229':
                $query = $Connect->query("SELECT TOP 2 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '230':
                $query = $Connect->query("SELECT TOP 3 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '231':
                $query = $Connect->query("SELECT TOP 4 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '232':
                $query = $Connect->query("SELECT TOP 5 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '233':
                $query = $Connect->query("SELECT TOP 6 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '234':
                $query = $Connect->query("SELECT TOP 7 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '235':
                $query = $Connect->query("SELECT TOP 8 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '236':
                $query = $Connect->query("SELECT TOP 9 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '237':
                $query = $Connect->query("SELECT TOP 10 NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['NickName'];
                }
                echo $OnlineTime;
            break;
            case '238':
                $query = $Connect->query("SELECT TOP 1 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '239':
                $query = $Connect->query("SELECT TOP 2 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '240':
                $query = $Connect->query("SELECT TOP 3 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '241':
                $query = $Connect->query("SELECT TOP 4 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '242':
                $query = $Connect->query("SELECT TOP 5 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '243':
                $query = $Connect->query("SELECT TOP 6 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '244':
                $query = $Connect->query("SELECT TOP 7 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '245':
                $query = $Connect->query("SELECT TOP 8 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '246':
                $query = $Connect->query("SELECT TOP 9 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '247':
                $query = $Connect->query("SELECT TOP 10 Grade FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Grade = $infoBase['Grade'];
                }
                echo $Grade;
            break;
            case '248':
                $query = $Connect->query("SELECT TOP 1 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '249':
                $query = $Connect->query("SELECT TOP 2 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '250':
                $query = $Connect->query("SELECT TOP 3 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '251':
                $query = $Connect->query("SELECT TOP 4 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '252':
                $query = $Connect->query("SELECT TOP 5 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '253':
                $query = $Connect->query("SELECT TOP 6 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '254':
                $query = $Connect->query("SELECT TOP 7 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '255':
                $query = $Connect->query("SELECT TOP 8 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '256':
                $query = $Connect->query("SELECT TOP 9 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '257':
                $query = $Connect->query("SELECT TOP 10 Total FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Total = $infoBase['Total'];
                }
                echo $Total;
            break;
            case '258':
                $query = $Connect->query("SELECT TOP 1 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '259':
                $query = $Connect->query("SELECT TOP 2 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '260':
                $query = $Connect->query("SELECT TOP 3 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '261':
                $query = $Connect->query("SELECT TOP 4 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '262':
                $query = $Connect->query("SELECT TOP 5 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '263':
                $query = $Connect->query("SELECT TOP 6 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '264':
                $query = $Connect->query("SELECT TOP 7 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '265':
                $query = $Connect->query("SELECT TOP 8 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '266':
                $query = $Connect->query("SELECT TOP 9 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '267':
                $query = $Connect->query("SELECT TOP 10 Win FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Win = $infoBase['Win'];
                }
                echo $Win;
            break;
            case '268':
                $query = $Connect->query("SELECT TOP 1 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '269':
                $query = $Connect->query("SELECT TOP 2 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '270':
                $query = $Connect->query("SELECT TOP 3 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '271':
                $query = $Connect->query("SELECT TOP 4 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '272':
                $query = $Connect->query("SELECT TOP 5 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '273':
                $query = $Connect->query("SELECT TOP 6 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '274':
                $query = $Connect->query("SELECT TOP 7 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '275':
                $query = $Connect->query("SELECT TOP 8 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '276':
                $query = $Connect->query("SELECT TOP 9 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
            case '277':
                $query = $Connect->query("SELECT TOP 10 OnlineTime FROM $BaseUser.dbo.Sys_Users_Detail WHERE IsExist ='true' ORDER BY OnlineTime DESC;");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $OnlineTime = $infoBase['OnlineTime'];
                }
                echo $OnlineTime;
            break;
        }
    }
}
?>