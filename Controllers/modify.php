<?php
class Modify
{

    public function nickServer($Connect, $newname, $BaseUser)
    {
        $query = $Connect->query("SELECT COUNT(*) AS NickHave FROM $BaseUser.dbo.Sys_Users_Detail WHERE NickName = '" . $newname . "'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $NickHave = $infoBase['NickHave'];
        }
        return $NickHave;
    }

    public function trocarNome($Connect, $newname, $captchaResult, $coderandom1, $coderandom2, $checkTotal, $BaseUser, $UserName, $NickName)
    {
        $illegalChar = array(".", ",", "?", "!", "'" ,"\\", ":", "(" , ")" , "/" , '"', ";", "-", "+", "<", ">", "%", "~", "€", "$", "[", "]", "{", "}", "@", "&", "#", "*", "„");
	    $newname = str_replace($illegalChar , '', $newname);

		$NickCheck = $this->nickServer($Connect, $newname, $BaseUser);
        if ($UserName == null || $NickName == null)
        {
            $_SESSION['alert_trocarnome'] = "<div class='alert alert-danger ocult-time'>Ocorreu um erro, faça login novamente.</div>";
            echo "<meta http-equiv='refresh' content='5;url=/' />";
            session_destroy();
        }
        else if ($NickCheck == 0)
        {
            $query = $Connect->query("SELECT VerifiedEmail FROM Db_Center.dbo.Mem_UserInfo WHERE Email = '$UserName'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $VerifiedEmail = $infoBase['VerifiedEmail'];
            }
			$query = $Connect->query("SELECT State, UserID FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$UserName'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $State = $infoBase['State'];
				$UserID = $infoBase['UserID'];
            }
            if ($VerifiedEmail == 0)
            {
                $_SESSION['alert_trocarnome'] = "<div class='alert alert-danger ocult-time'>Por segurança, para alterar seu nome você deve verificar seu e-mail.</div>";
                echo "<meta http-equiv='refresh' content='3;url=checkmail' />";
            }
            else if ($captchaResult != $checkTotal)
            {
                $_SESSION['alert_trocarnome'] = "<div class='alert alert-danger ocult-time'>A resposta do código está errada tente novamente.</div>";
            }
			else if ($State != 0)
			{
				$_SESSION['alert_trocarnome'] = "<div class='alert alert-warning ocult-time'>Sua conta está online, saia do jogo para alterar seu nome.</div>";
			}
            else
            {
                if (empty($newname))
                {
                    $_SESSION['alert_trocarnome'] = "<div class='alert alert-danger ocult-time'>Você não digitou seu nome...</div>";
                }
                if (strlen($newname) < 3 || strlen($newname) > 16)
                {
                    $_SESSION['alert_trocarnome'] = "<div class='alert alert-danger ocult-time'>O Nome do seu personagem deve ter entre 3 a 16 caracteres...</div>";
                }
                else
                {
                    $query = $Connect->query("UPDATE $BaseUser.dbo.Sys_Users_Detail SET NickName=N'$newname' WHERE UserName='$UserName'");
                    $query = $Connect->query("UPDATE $BaseUser.dbo.Consortia SET ChairmanName=N'$newname' WHERE ChairmanID='$UserID'");
                    $query = $Connect->query("UPDATE $BaseUser.dbo.Consortia_Users SET UserName=N'$newname' WHERE UserID='$UserID'");
                    $_SESSION['alert_trocarnome'] = "<div class='alert alert-success ocult-time'>Seu nome foi alterado com sucesso!</div>";
                }
            }
        }
        else
        {
            $_SESSION['alert_trocarnome'] = "<div class='alert alert-danger ocult-time'>Já existe um usuário com este nick, por favor escolha outro.</div>";
        }
    }

    public function curlRequest($url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($c);
        curl_close($c);
        return $data;
    }

    public function AwardGiftCode($Connect, $GiftCode, $BaseServer, $BaseTank, $ServerID, $AreaID, $QuestUrl, $BaseUser)
    {
        if (empty($GiftCode))
        {
            $_SESSION['alert_giftcode'] = "<div class='alert alert-danger ocult-time'>Você não digitou um código de item...</div>";
        }
        else
        {
            $GiftCode = strtoupper($GiftCode);
            $query = $Connect->query("SELECT COUNT(*) AS Code FROM Db_Center.dbo.Award_GiftCode WHERE Code = '$GiftCode' AND IsActive='1' AND ServerID = '$ServerID'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $CheckCode = $infoBase['Code'];
            }
            $query = $Connect->query("SELECT COUNT(*) AS CheckItem FROM Db_Center.dbo.User_Award_GiftCode WHERE UserName = '$_SESSION[UserName]' AND ServerID = '$ServerID' AND Code = '$GiftCode'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $CheckItem = $infoBase['CheckItem'];
            }
            $query = $Connect->query("SELECT * FROM $BaseUser.dbo.Sys_Users_Detail where UserName = '$_SESSION[UserName]'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
               $UserID = $infoBase['UserID'];
               $NickAtual = $infoBase['NickName'];
            }
            if ($CheckCode == 0)
            {
                $_SESSION['alert_giftcode'] = "<div class='alert alert-danger ocult-time'>Código de itens não encontrado!</div>";
            }
            else if ($CheckItem > 0)
            {
                $_SESSION['alert_giftcode'] = "<div class='alert alert-danger ocult-time'>Você já resgatou esse código uma vez.</div>";
            }
            else
            {
                $query = $Connect->query("SELECT A.ID, A.TemplateID, A.Count, A.Type, A.Code, B.Name, B.Pic, B.CategoryID, B.NeedSex FROM $BaseServer.dbo.Award_GiftCode A LEFT JOIN $BaseTank.dbo.Shop_Goods B ON A.TemplateID = B.TemplateID WHERE ServerID = '$ServerID' ORDER BY Date DESC");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $ID = $infoBase['ID'];
                    $TemplateID = $infoBase['TemplateID'];
                    $Count = $infoBase['Count'];
                    $Name = $infoBase['Name'];
                    $image = $infoBase['Pic'];
                    $CategoryID = $infoBase['CategoryID'];
                    $NeedSex = $infoBase['NeedSex'];
                    $Type = $infoBase['Type'];
                    $Code = strtoupper($infoBase['Code']);
                    if ($GiftCode == $Code)
                    {
                        $hoje = date('d/m/Y');
                        $_SESSION['alert_giftcode'] = "<div class='alert alert-success ocult-time'>Sucesso no resgate! sua recompensa foi enviada para seu correio!</div>";
                        $query = $Connect->query("EXECUTE $BaseUser.dbo.SP_Admin_SendUserItem
      				 @ItemID = '$TemplateID'
      				,@UserID = '$UserID'
      				,@TemplateID = '$TemplateID'
      				,@Place = '0'
      				,@Count = '$Count'
      				,@IsJudge = '0'
      				,@Color = ''
      				,@IsExist = '1'
      				,@StrengthenLevel = '0'
      				,@AttackCompose = '0'
      				,@DefendCompose = '0'
      				,@LuckCompose = '0'
      				,@AgilityCompose = '0'
      				,@IsBinds = '1'
      				,@ValidDate = '0'
      				,@BagType = '0'
      				,@ID = '0'
      				,@SenderID = '0'
      				,@Sender = 'Sistema'
  				    ,@ReceiverID = '$UserID'
      				,@Receiver = '$NickAtual'
  				    ,@Title = 'Recompensas de Código!'
  				    ,@Content = 'Esse item foi enviado por você através do nosso sistema de código.'
      				,@IsRead = '0'
      				,@IsDelR = '0'
      				,@IfDelS = '0'
      				,@IsDelete = '0'
      				,@Annex1 = '0'
      				,@Annex2 = '0'
      				,@Gold = '0'
      				,@Money = '0'");
                        $query = $Connect->query("INSERT INTO $BaseServer.dbo.User_Award_GiftCode (UserName, Count, ServerID, Code) VALUES ('$_SESSION[UserName]', '$Count', '$ServerID', '$Code')");
                        $this->curlRequest("$QuestUrl/UpdateMailByUserID.ashx?UserID=$UserID&AreaID=$AreaID&key=TqUserZap777");
                    }
                }
            }
        }
    }
}
?>