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
}
?>