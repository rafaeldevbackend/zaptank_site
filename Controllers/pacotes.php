<?php
class Pacotes
{
    public function playerMoney($Connect, $BaseServer)
    {
        $query = $Connect->query("SELECT SUM(Price) AS TotalMoney FROM $BaseServer.dbo.Vip_Data WHERE Status = 'Aprovada' AND UserName = '$_SESSION[UserName]'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $TotalMoney = $infoBase["TotalMoney"];
        }
        if ($TotalMoney == null)
        {
            echo "0";
        }
        else
        {
            echo $TotalMoney;
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

    public function CheckIsExist($Connect, $VipID)
    {
        $query = $Connect->query("SELECT COUNT(*) AS VipID FROM Db_Center.dbo.Vip_List_Item WHERE VipID = '$VipID'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $VipID = $infoBase["VipID"];
        }
        return $VipID;
    }

    public function ShowChargeBack($Connect, $BaseServer, $BaseUser, $QuestUrl, $AreaID)
    {
        if (isset($_POST["sendchargeback"]))
        {
            $UserName = $_SESSION["UserName"];
            $query = $Connect->query("SELECT COUNT(*) AS IsChargeBack FROM $BaseServer.dbo.Vip_Data where UserName = '$UserName' AND IsChargeBack = '1'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $Count = $infoBase["IsChargeBack"];
            }
            if ($Count > 0)
            {
                $query = $Connect->query("SELECT UserID, NickName FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$UserName'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $UserID = $infoBase["UserID"];
                    $NickName = $infoBase["NickName"];
                }
                $PacoteID = 0;
                $query = $Connect->query("SELECT * FROM $BaseServer.dbo.Vip_Data WHERE UserName = '$UserName' AND IsChargeBack = '1' AND Status = 'Aprovada'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $PacoteID = $infoBase["PacoteID"];
                    $Status = $infoBase["Status"];
                    $Price = $infoBase["Price"];
                    $Method = $infoBase["Method"];
                    $Reference = $infoBase["ID"];
                }
                $ItemID = 0;
                switch ($PacoteID)
                {
                    case "1":
                        $ItemID = 1128000;
                    break;
                    case "2":
                        $ItemID = 1128001;
                    break;
                    case "3":
                        $ItemID = 1128002;
                    break;
                    case "4":
                        $ItemID = 1128003;
                    break;
                    case "5":
                        $ItemID = 1128004;
                    break;
                    case "6":
                        $ItemID = 1128005;
                    break;
                    case "7":
                        $ItemID = 1128006;
                    break;
                    case "8":
                        $ItemID = 1128007;
                    break;
                    case "9":
                        $ItemID = 1128008;
                    break;
                    case "10":
                        $ItemID = 1128009;
                    break;
                }
                if ($ItemID > 0)
                {
                    $query = $Connect->query("EXECUTE $BaseUser.dbo.SP_Admin_SendUserItem
				@ItemID = '0'
			   ,@UserID = '$UserID'
			   ,@TemplateID = '$ItemID'
			   ,@Place = '0'
			   ,@Count = '1'
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
			   ,@Receiver = '$NickName'
			   ,@Title = 'ChargeBack'
			   ,@Content = 'Esse item foi enviado por você através do nosso sistema de ChargeBack, caso apague esse e-mail nao enviaremos suas recompensas novamente.'
			   ,@IsRead = '0'
			   ,@IsDelR = '0'
			   ,@IfDelS = '0'
			   ,@IsDelete = '0'
			   ,@Annex1 = '0'
			   ,@Annex2 = '0'
			   ,@Gold = '0'
			   ,@Money = '0'");
                    $this->curlRequest("$QuestUrl/UpdateMailByUserID.ashx?UserID=$UserID&AreaID=$AreaID&key=TqUserZap777");
                    $query = $Connect->query("UPDATE $BaseServer.dbo.Vip_Data SET IsChargeBack = '0' WHERE ID = '$Reference' AND Status = 'Aprovada' AND IsChargeBack = '1'");
                    echo "<div class='alert alert-success ocult-time'>Enviado com sucesso!</div>";
                }
                else
                {
                    echo "<div class='alert alert-danger ocult-time'>Não foi possível processar sua solicitação, caso o problema persista abra um ticket na central do jogo.</div>";
                }
            }
            else
            {
                echo "<div class='alert alert-danger ocult-time'>Não foi possível processar sua solicitação, caso o problema persista abra um ticket na central do jogo.</div>";
            }
        }
        $UserName = $_SESSION["UserName"];
        $query = $Connect->query("SELECT COUNT(*) AS IsChargeBack FROM $BaseServer.dbo.Vip_Data where UserName = '$UserName' AND IsChargeBack = '1' AND Status = 'Aprovada'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Count = $infoBase["IsChargeBack"];
        }
        $query = $Connect->query("SELECT * FROM $BaseServer.dbo.Vip_Data WHERE UserName = '$UserName' AND Status = 'Aprovada' AND IsChargeBack = '1'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $ID = $infoBase["ID"];
            $PacoteID = $infoBase["PacoteID"];
            $Status = $infoBase["Status"];
            $Price = $infoBase["Price"];
            $Method = $infoBase["Method"];
            $Date = $infoBase["Date"];
            $IsChargeBack = $infoBase["IsChargeBack"];
            if ($Status == "Aprovada" && $IsChargeBack == 1)
            {
                echo "<form method='POST' id='frmLogin' autocomplete='off'> <div class='card' style='max-width: 200rem;'> <div class='card-body'> <h4 class='card-subtitle'>Pacote de cupons à coletar</h4> <p>Transação concluída na data de $Date</p><h6>Preço <span class='semi-bold'>" . $Price . " BRL</span> </h6> <div class='pull-right' align='right'> <button type='submit' name='sendchargeback' class='btn btn-outline-primary'>Receber Agora!</button> </div></div></div></br></form>";
            }
        }
        if ($Count < 1)
        {
            echo "<div class='alert alert-danger'>Você não possui nenhuma fatura para mostrar.</div>";
        }
    }

    public function vipInfo($Connect, $BaseServer, $VipRequest, $Resource, $TankServer, $Ddtank, $KeyPublicCrypt, $KeyPrivateCrypt, $VipID)
    {
        $ServerIDN = $Ddtank->DecryptText($KeyPublicCrypt, $KeyPrivateCrypt, $_GET["server"]);

        switch ($VipRequest)
        {
            case "600":
                $query = $Connect->query("SELECT Name FROM $BaseServer.dbo.Vip_List WHERE ID = '$VipID' AND ServerID = '$ServerIDN'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $Name = $infoBase["Name"];
                }
                echo $Name;
            break;
            case "601":
                $query = $Connect->query("SELECT A.TemplateID, A.Count, B.CategoryID, B.NeedSex, B.Pic FROM $BaseServer.dbo.Vip_List_Item A LEFT JOIN $TankServer.dbo.Shop_Goods B ON A.TemplateID = B.TemplateID WHERE VipID = '$VipID'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $TemplateID = $infoBase["TemplateID"];
                    $Count = $infoBase["Count"];
                    $CategoryID = $infoBase["CategoryID"];
                    $NeedSex = $infoBase["NeedSex"];
                    $image = $infoBase["Pic"];
                    switch ($NeedSex)
                    {
                        case "1":
                            $ml = "m";
                        break;
                        case "2":
                            $ml = "f";
                        break;
                        default:
                            $ml = "f";
                        break;
                    }
                    switch ($CategoryID)
                    {
                        case 1:
                            $link = "equip/" . $ml . "/head/" . $image . "/icon_1.png?lv=semcache";
                        break;
                        case 2:
                            $link = "equip/" . $ml . "/glass/" . $image . "/icon_1.png?lv=semcache";
                        break;
                        case 3:
                            $link = "equip/" . $ml . "/hair/" . $image . "/icon_1.png?lv=semcache";
                        break;
                        case 5:
                            $link = "equip/" . $ml . "/cloth/" . $image . "/icon_1.png?lv=semcache";
                        break;
                        case 6:
                            $link = "equip/" . $ml . "/face/" . $image . "/icon_1.png?lv=semcache";
                        break;
                        case 7:
                            $link = "arm/" . $image . "/00.png?lv=semcache";
                        break;
                        case 8:
                            $link = "equip/armlet/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 9:
                            $link = "equip/ring/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 11:
                            $link = "unfrightprop/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 13:
                            $link = "equip/" . $ml . "/suits/" . $image . "/icon_1.png?lv=semcache";
                        break;
                        case 15:
                            $link = "equip/wing/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 14:
                            $link = "equip/necklace/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 17:
                            $link = "equip/offhand/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 16:
                            $link = "specialprop/chatBall/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 18:
                            $link = "cardbox/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 19:
                            $link = "prop/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 20:
                            $link = "prop/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 24:
                            $link = "unfrightprop/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 35:
                            $link = "unfrightprop/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 34:
                            $link = "unfrightprop/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 40:
                            $link = "unfrightprop/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 50:
                            $link = "petequip/arm/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 52:
                            $link = "petequip/cloth/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 51:
                            $link = "petequip/hat/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 62:
                            $link = "unfrightprop/" . $image . "/icon.png?lv=semcache";
                        break;
                        case 72:
                            $link = "unfrightprop/" . $image . "/icon.png?lv=semcache";
                        break;
                        default:
                            $link = null;
                        break;
                    }
                    echo "<div class='item-shop right' valign='middle'><a><img alt='DDTank' height='78' src='" . $Resource . "" . $link . "'><br><center>Quantidade<br><strong><a>(x" . $Count . ")</a></strong></center></div>";
                }
            break;
        }
    }

    public function newInvoice($Connect, $BaseServer, $name, $number, $email, $Ddtank, $KeyPublicCrypt, $KeyPrivateCrypt, $Page)
    {
        $ServerIDN = $Ddtank->DecryptText($KeyPublicCrypt, $KeyPrivateCrypt, addslashes($_GET["server"]));

        $illegalChar = [".", ",", "?", "!", "'", "\\", ":", "(", ")", "/", '"', ";", "-", "+", "<", ">", "%", "~", "€", "$", "[", "]", "{", "}", "@", "&", "#", "*", "„", ];
        $name = str_replace($illegalChar, "", $name);

        if (empty($name) || empty($number) || empty($email))
        {
            $_SESSION["alert_listarpacote"] = "<div class='alert alert-danger ocult-time'>Você não preencheu todos os dados.</div>";
        }
        elseif (strlen($name) < 3 || strlen($name) > 100)
        {
            $_SESSION["alert_listarpacote"] = "<div class='alert alert-danger ocult-time'>Seu nome deve ser maior que 3 e menor que 100 caracteres...</div>";
        }
        elseif (strlen($number) != 19)
        {
            $_SESSION["alert_listarpacote"] = "<div class='alert alert-danger ocult-time'>Seu número de telefone deve conter 19 caracteres...</div>";
        }
        elseif (empty($ServerIDN))
        {
            $_SESSION["alert_listarpacote"] = "<div class='alert alert-danger ocult-time'>Ocorreu um erro interno, por favor gere sua fatura novamente...</div>";
        }
        else
        {
            //Configurações Básicas
            $User = $_SESSION["UserName"];
            $Pacote = $Page;
            $Definir = "Pendente";
            $Forma = "Fatura";
            //Final das Configurações Básicas.
            $query = $Connect->query("SELECT ValuePrice FROM $BaseServer.dbo.Vip_List WHERE ID = '" . $Pacote . "'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $ValuePrice = $infoBase["ValuePrice"];
            }
            $ID = 0;
            $query = $Connect->query("INSERT INTO $BaseServer.dbo.Vip_Data (PacoteID, UserName, Method, Date, Price, Status, Name, Number, ServerID, IsChargeBack, PicPayLink) VALUES ('$Pacote', '$User', '$Forma', getdate(), '$ValuePrice', '$Definir', N'$name', '$number', '$ServerIDN', '0', '#')");
            $query = $Connect->query("SELECT TOP 1 ID FROM $BaseServer.dbo.Vip_Data WHERE UserName = '" . $User . "' ORDER BY ID DESC");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $ID = $infoBase["ID"];
            }
            if ($ID == 0)
            {
                $_SESSION["alert_listarpacote"] = "<div class='alert alert-danger ocult-time'>Ocorreu um problema durante a compra, tente novamente mais tarde.</div>";
            }
            else
            {
                $EncryptID = $Ddtank->EncryptText($KeyPublicCrypt, $KeyPrivateCrypt, $ID);
                $EncryptServerID = $Ddtank->EncryptText($KeyPublicCrypt, $KeyPrivateCrypt, $ServerIDN);
                if (!empty($EncryptID) && !empty($EncryptServerID))
                {
                    header("location: ?page=invoice&show=" . $EncryptID . "&server=" . $EncryptServerID . "");
                }
                else
                {
                    $_SESSION["alert_listarpacote"] = "<div class='alert alert-danger ocult-time'>Ocorreu um problema durante a compra, tente novamente mais tarde.</div>";
                }
            }
        }
    }

    public function invoiceInfo($Connect, $BaseServer, $Packet, $RequestPacket, $Ddtank, $KeyPublicCrypt, $KeyPrivateCrypt)
    {
        $query = $Connect->query("SELECT * FROM $BaseServer.dbo.Vip_Data WHERE ID = '$Packet' AND UserName = '$_SESSION[UserName]'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $Name = $infoBase["Name"];
            $Nome = "$Name";
            $Number = $infoBase["Number"];
            $Date = $infoBase["Date"];
            $Price = $infoBase["Price"];
        }
        switch ($RequestPacket)
        {
            case "1":
                $query = $Connect->query("SELECT COUNT(*) AS HaveInvoice FROM $BaseServer.dbo.Vip_Data WHERE ID = '$Packet' AND UserName = '$_SESSION[UserName]'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $HaveInvoice = $infoBase["HaveInvoice"];
                }
                if ($HaveInvoice == 1)
                {
                    $query = $Connect->query("SELECT COUNT(*) AS HaveInvoicePay FROM $BaseServer.dbo.Vip_Data WHERE ID = '$Packet' AND UserName = '$_SESSION[UserName]' AND Status = 'Aprovada'");
                    $result = $query->fetchAll();
                    foreach ($result as $infoBase)
                    {
                        $HaveInvoicePay = $infoBase["HaveInvoicePay"];
                    }
                    if ($HaveInvoicePay == 1)
                    {
                        $_SESSION["alert_listarpacote"] = "<div class='alert alert-danger ocult-time'> Essa fatura já foi paga anteriormente.</div>";
                    }
                }
            break;
            case "2":
                echo $Nome;
            break;
            case "4":
                echo $Number;
            break;
            case "5":
                echo $Date;
            break;
            case "6":
                echo $Price;
            break;
        }
    }
}
?>