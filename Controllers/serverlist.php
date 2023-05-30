<?php
   class ServerList
   {
   	public function execute($Connect, $BaseServer, $TankUser, $TankServer, $nickname, $gener, $username, $password)
   	{
   		$Connect->query("EXEC $TankUser.dbo.SP_Users_Active @UserID='',@Attack=0,@Colors=N',,,,,,',@ConsortiaID=0,@Defence=0,@Gold=0,@GP=1,@Grade=1,@Luck=0,@Money=0,@Style=N',,,,,,',@Agility=0,@State=0,@UserName=N'$username',@PassWord=N'$password',@Sex='$gener',@Hide=1111111111,@ActiveIP=N'',@Skin=N'',@Site=N''");
   		if($gener == 1){
   			$Connect->query("EXEC $TankUser.dbo.SP_Users_RegisterNotValidate @UserName=N'$username',@PassWord=N'$password',@NickName=N'$nickname',@BArmID=7008,@BHairID=3158,@BFaceID=6103,@BClothID=5160,@BHatID=1142,@GArmID=7008,@GHairID=3158,@GFaceID=6103,@GClothID=5160,@GHatID=1142,@ArmColor=N'',@HairColor=N'',@FaceColor=N'',@ClothColor=N'',@HatColor=N'',@Sex='$gener',@StyleDate=0");
   		}else{
   			$Connect->query("EXEC $TankUser.dbo.SP_Users_RegisterNotValidate @UserName=N'$username',@PassWord=N'$password',@NickName=N'$nickname',@BArmID=7008,@BHairID=3244,@BFaceID=6204,@BClothID=5276,@BHatID=1214,@GArmID=7008,@GHairID=3244,@GFaceID=6202,@GClothID=5276,@GHatID=1214,@ArmColor=N'',@HairColor=N'',@FaceColor=N'',@ClothColor=N'',@HatColor=N'',@Sex='$gener',@StyleDate=0");
   		}
   		$Connect->query("EXEC $TankUser.dbo.SP_Users_LoginWeb @UserName=N'$username',@Password=N'',@FirstValidate=0,@NickName=N'$nickname'");
   		return 1;
   	}
   	public function serverHave($Connect, $BaseServer, $Server)
   	{
   		$query = $Connect->query("SELECT COUNT(*) AS HaveServer FROM $BaseServer.dbo.Server_List WHERE ID = '". $Server ."'");
   		$result = $query->fetchAll();
   		foreach($result as $infoBase)
   		{
   			$HaveServer = $infoBase['HaveServer'];
   		}
   		return $HaveServer;
   	}
   	public function serverInfo($Connect, $BaseServer, $Server)
   	{
   		$query = $Connect->query("SELECT * FROM $BaseServer.dbo.Server_List WHERE ID = '". $Server ."'");
   		$result = $query->fetchAll();
   		return $result;
   	}
   	public function userServer($Connect, $TankServer, $TankUser)
   	{
   		$query = $Connect->query("SELECT COUNT(*) AS UserHave FROM $TankUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
   		$result = $query->fetchAll();
   		foreach($result as $infoBase)
   		{
   			$UserHave = $infoBase['UserHave'];
   		}
   		return $UserHave;
   	}
   	public function checkServer($Connect, $BaseServer, $Server, $Extra)
   	{
   		$query = $Connect->query("SELECT COUNT(*) AS HaveServer FROM $BaseServer.dbo.Server_List WHERE ID = '". $Server ."'");
   		$result = $query->fetchAll();
   		foreach($result as $infoBase)
   		{
   			$HaveServer = $infoBase['HaveServer'];
   		}
   		if($HaveServer == 1)
   		{
   			$infoServers = $this->serverInfo($Connect, $BaseServer, $Server);
   			foreach($infoServers as $infoServer)
   			{
   				$TankServer = $infoServer['BaseTank'];
   				$TankUser = $infoServer['BaseUser'];
   			}
   			$UserHave = $this->userServer($Connect, $TankServer, $TankUser);
   			if($UserHave >= 1)
   			{
   			}
   			else
   			{
		    header("Location: serverlist");
			exit();
   			}
   		}
   		else
   		{
		header("Location: serverlist");
        $_SESSION['msg'] = "<div class='alert alert-danger ocult-time'>O servidor que você tentou acessar não existe ou está restrito para um pequeno número de pessoas.</div>";
		exit();
   		}
   	}
   }
   ?>