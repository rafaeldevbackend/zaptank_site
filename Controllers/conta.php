<?php
class Conta
{
	public function checkConnection($Connect, $TankUser)
	{
		$query = $Connect->query("SELECT COUNT(*) AS CheckConnect FROM $TankUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]' AND State = '0'");
		$result = $query->fetchAll();
		foreach($result as $infoBase)
		{
			$CheckConnect = $infoBase['CheckConnect'];
		}
		return $CheckConnect;
	}
	public function checkAccount($Connect, $TankUser)
	{
		$query = $Connect->query("SELECT COUNT(*) AS CheckAccount FROM $TankUser.dbo.Sys_Users_Detail WHERE UserName = '$_SESSION[UserName]'");
		$result = $query->fetchAll();
		foreach($result as $infoBase)
		{
			$CheckAccount = $infoBase['CheckAccount'];
		}
		return $CheckAccount;
	}
	public function checkPassword($Connect, $BaseServer, $Password)
	{
		$query = $Connect->query("SELECT COUNT(*) AS CheckPass FROM $BaseServer.dbo.Mem_UserInfo WHERE UserId = '$_SESSION[UserId]' AND Password = '$Password'");
		$result = $query->fetchAll();
		foreach($result as $infoBase)
		{
			$CheckPass = $infoBase['CheckPass'];
		}
		return $CheckPass;
	}
}
?>