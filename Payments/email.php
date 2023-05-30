<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
http_response_code(200);

if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Mandrill-Webhook/1.0'))
{
    $error = "../logs/logsmandrill.txt";
    file_put_contents($error, $_SERVER['HTTP_USER_AGENT'] . PHP_EOL, FILE_APPEND | LOCK_EX);
	die('Acesso Negado!');
}
else
{
	require_once ('../getconnect.php');
    require_once ('../globalconn.php');
    $Connect = Connect::getConnection();
    $restult_data_decode = json_decode($_POST['mandrill_events']);
}

if ($restult_data_decode != null)
{
    foreach ($restult_data_decode as $event)
    {
        $email = $event->msg->email;
        $status = $event->event;

        if (strpos($status, "hard_bounce") !== false && $email != null)
        {
			$query = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET BadMail='1' WHERE Email='$email'");
        }
        else if (strpos($status, "spam") !== false)
        {
			$query = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET BadMail='1' WHERE Email='$email'");
			$query = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET VerifiedEmail='0' WHERE Email='$email'");
        }
		else if (strpos($status, "soft_bounce") !== false)
        {
			$description = $event->msg->bounce_description;
			if ($description == "invalid_domain")
			{
				$query = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET BadMail='1' WHERE Email='$email'");
			    $query = $Connect->query("UPDATE Db_Center.dbo.Mem_UserInfo SET VerifiedEmail='0' WHERE Email='$email'");
			}
        }
		
		// $error = "../logs/logsmandrill.txt";
        // file_put_contents($error, $status . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
?>