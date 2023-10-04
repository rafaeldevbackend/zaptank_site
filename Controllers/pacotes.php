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