<?php
class Mochila
{

    public function showCountbag($Connect, $BaseServer, $ServerID)
    {
        $query = $Connect->query("SELECT COUNT(*) AS HaveItemBag FROM $BaseServer.dbo.Bag_Goods WHERE UserName = '$_SESSION[UserName]' AND Status = '0' AND ServerID = '$ServerID'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $HaveItemBag = $infoBase['HaveItemBag'];
        }
        echo $HaveItemBag;
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

    public function virtualBag($Connect, $BaseServer, $Resource, $Extra, $ServerID, $BaseTank)
    {
        $query = $Connect->query("SELECT COUNT(*) AS HaveItemBag FROM $BaseServer.dbo.Bag_Goods WHERE UserName = '$_SESSION[UserName]'");
        $result = $query->fetchAll();
        foreach ($result as $infoBase)
        {
            $HaveItemBag = $infoBase['HaveItemBag'];
        }
        if ($HaveItemBag != 0)
        {
			$query = $Connect->query("SELECT A.ID, A.UserName, A.TemplateID, A.Count, A.Status$ServerID, B.CategoryID, B.NeedSex, B.Pic FROM $BaseServer.dbo.Bag_Goods A LEFT JOIN $BaseTank.dbo.Shop_Goods B ON A.TemplateID = B.TemplateID WHERE UserName = '$_SESSION[UserName]'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $ID = $infoBase['ID'];
                $TemplateID = $infoBase['TemplateID'];
                $Count = $infoBase['Count'];
                $CategoryID = $infoBase['CategoryID'];
                $NeedSex = $infoBase['NeedSex'];
                $image = $infoBase['Pic'];
			    $Status = $infoBase['Status'.$ServerID.''];
                if ($Status != 0)
                {
                    echo "<div class='item-shop'> <div class='align-top parent'><div align='center' valign='middle'><img alt='DDTank' height='78' src='" . $Resource . "" . $Extra->loadImage($NeedSex, $CategoryID, $image) . "'><br><strong><a>Quantidade <br>" . $Count . "</a></strong></div><center><div class='line'></div><input type='hidden' name='questi' value='" . $TemplateID . "'> <input type='hidden' name='questii' value='" . $ID . "'> <button disabled class='btn btn-dark'>Coletado</button> </div></div>";
                }
                else
                {
                    echo "<form class='js-validate' method='post'><div class='item-shop'> <div class='align-top parent'><div align='center' valign='middle'><img alt='DDTank' height='78' src='" . $Resource . "" . $Extra->loadImage($NeedSex, $CategoryID, $image) . "'><br><strong><a>Quantidade <br>" . $Count . "</a></strong></div><center><div class='line'></div><input type='hidden' name='questi' value='" . $TemplateID . "'> <input type='hidden' name='questii' value='" . $ID . "'> <button type='submit' name='sendbagitem' class='btn btn-dark'>Enviar</button> </div></div></form>";
                }
            }
        }
        else
        {
            echo '<div class="alert alert-danger" role="alert">Sua mochila está vazia!</div>';
        }
    }

    public function sendItem($Connect, $BaseServer, $ServerID, $AreaID, $QuestUrl, $BaseUser)
    {
        if (isset($_POST['sendbagitem']))
        {
            $TemplateID = addslashes($_POST['questi']);
            $ID = addslashes($_POST['questii']);
            $query = $Connect->query("SELECT COUNT(*) AS CheckItem FROM $BaseServer.dbo.Bag_Goods WHERE ID = '$ID' AND TemplateID = '$TemplateID' AND UserName = '$_SESSION[UserName]' AND Status$ServerID = '0'");
            $result = $query->fetchAll();
            foreach ($result as $infoBase)
            {
                $CheckItem = $infoBase['CheckItem'];
            }
            if ($CheckItem == 0)
            {
                $_SESSION['alert'] = "<div class='alert alert-danger ocult-danger'>Não conseguimos processar sua solicitação...</div>";
            }
            else
            {
                $query = $Connect->query("SELECT * FROM $BaseServer.dbo.Bag_Goods WHERE ID = '$ID' AND TemplateID = '$TemplateID' AND UserName = '$_SESSION[UserName]' AND Status$ServerID = '0'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $ID = $infoBase['ID'];
                    $UserNameD = $infoBase['UserName'];
                    $TemplateID = $infoBase['TemplateID'];
                    $Count = $infoBase['Count'];
                }
                if ($TemplateID == "-200")
                {
                    $Dinheiro = "" . $Count . "";
                    $Count = "0";
                }
                else
                {
                    $Dinheiro = "0";
                }

                $query = $Connect->query("SELECT NickName, UserID FROM $BaseUser.dbo.Sys_Users_Detail WHERE UserName = '$UserNameD'");
                $result = $query->fetchAll();
                foreach ($result as $infoBase)
                {
                    $NickName = $infoBase['NickName'];
                    $UserID = $infoBase['UserID'];
                }

                $query = $Connect->query("EXECUTE $BaseUser.dbo.SP_Admin_SendUserItem
                 @ItemID = '" . $TemplateID . "'
                ,@UserID = '0'
                ,@TemplateID = '" . $TemplateID . "'
                ,@Place = '0'
                ,@Count = '" . $Count . "'
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
                ,@Title = 'Mochila Virtual!'
                ,@Content = 'Este item foi enviado por você através do nosso sistema de mochila virtual, por favor, nao apague este e-mail, você nao poderá obter esta recompensa novamente nesta temporada.'
                ,@IsRead = '0'
                ,@IsDelR = '0'
                ,@IfDelS = '0'
                ,@IsDelete = '0'
                ,@Annex1 = '0'
                ,@Annex2 = '0'
                ,@Gold = '0'
                ,@Money = '$Dinheiro'");
                $query = $Connect->query("UPDATE $BaseServer.dbo.Bag_Goods SET Status$ServerID = '1' WHERE TemplateID = '$TemplateID' AND ID = '$ID' AND UserName = '$_SESSION[UserName]'");
                $this->curlRequest("$QuestUrl/UpdateMailByUserID.ashx?UserID=$UserID&AreaID=$AreaID&key=TqUserZap777");
                $_SESSION['alert'] = "<div class='alert alert-success ocult-time'>O item foi enviado para seu correio!</div>";
            }
        }
    }

}