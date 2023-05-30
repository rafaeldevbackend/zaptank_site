<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('session.cookie_httponly', 1);
if (empty($_SERVER['HTTP_USER_AGENT'])){header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); header("Cache-Control: post-check=0, pre-check=0", false); header("Pragma: no-cache"); http_response_code(200);echo 'Status Code 400 Bad Request You have not provided a required User-Agent header for this site. We were unable to process your request due to an internal error. If you are using third-party tools, please disable and try again.'; exit();}
function sanitize_output($buffer){$search=array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s','/<!--(.|\s)*?-->/');$replace=array('>','<','\\1','');$buffer=preg_replace($search,$replace,$buffer);return $buffer;}ob_start("sanitize_output");
define('DB_HOST'        , "DESKTOP-EFPSBMB\RAFAEL");
define('DB_USER'        , "sa");
define('DB_PASSWORD'    , "123456");
define('DB_DRIVER'      , "sqlsrv");
define('DB_NAME'        , "Db_Tank_102");
$Title = "ZapTank -";
$picPayToken = '123456';
$Face = "https://fb.me/zaptankoficial";
$WhatsApp = "/selectwhats";
$WEBSITE = "https://redezaptank.com.br";
$BaseServer = "Db_Center";
$Resource = "https://cdn.redezaptank.com.br/resourcev127/image/";
$SMTP_HOST = "123456";
$SMTP_EMAIL = "123456";
$SMTP_PASSWORD = "123456";
$PrivateKeyPagarme = base64_encode("123456");
$KeyPublicCrypt = "7c101d33045ac148dac1f571f3e482f1644156cb46b8f13d75f132dae56d2476";
$KeyPrivateCrypt = "ae50f388f09b9d7729f342df31abe712a7791f9bd1831e9a3db702112aad2590";
?>