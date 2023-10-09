<?php

include 'globalconn.php';

if (session_status() !== PHP_SESSION_ACTIVE)
{
    session_start(['cookie_lifetime' => 86400, 'cookie_secure' => false, 'cookie_httponly' => true]);
}

include 'loadautoloader.php';
include 'Objects/gerenciamento.php';
$Dados->Destroy();

$CryptServer = $_GET['sid'];

if (!strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank') && !strstr($_SERVER['HTTP_USER_AGENT'], 'Chrome/87.0.4280.66'))
{
    echo '<script>var FlashDetect=new function(){var self=this; self.installed=false; self.raw=""; self.major=-1; self.minor=-1; self.revision=-1; self.revisionStr=""; var activeXDetectRules=[{"name":"ShockwaveFlash.ShockwaveFlash.7", "version":function(obj){return getActiveXVersion(obj);}},{"name":"ShockwaveFlash.ShockwaveFlash.6", "version":function(obj){var version="6,0,21"; try{obj.AllowScriptAccess="always"; version=getActiveXVersion(obj);}catch(err){}return version;}},{"name":"ShockwaveFlash.ShockwaveFlash", "version":function(obj){return getActiveXVersion(obj);}}]; var getActiveXVersion=function(activeXObj){var version=-1; try{version=activeXObj.GetVariable("$version");}catch(err){}return version;}; var getActiveXObject=function(name){var obj=-1; try{obj=new ActiveXObject(name);}catch(err){obj={activeXError:true};}return obj;}; var parseActiveXVersion=function(str){var versionArray=str.split(","); return{"raw":str, "major":parseInt(versionArray[0].split(" ")[1], 10), "minor":parseInt(versionArray[1], 10), "revision":parseInt(versionArray[2], 10), "revisionStr":versionArray[2]};}; var parseStandardVersion=function(str){var descParts=str.split(/ +/); var majorMinor=descParts[2].split(/\./); var revisionStr=descParts[3]; return{"raw":str, "major":parseInt(majorMinor[0], 10), "minor":parseInt(majorMinor[1], 10), "revisionStr":revisionStr, "revision":parseRevisionStrToInt(revisionStr)};}; var parseRevisionStrToInt=function(str){return parseInt(str.replace(/[a-zA-Z]/g, ""), 10) || self.revision;}; self.majorAtLeast=function(version){return self.major >=version;}; self.minorAtLeast=function(version){return self.minor >=version;}; self.revisionAtLeast=function(version){return self.revision >=version;}; self.versionAtLeast=function(major){var properties=[self.major, self.minor, self.revision]; var len=Math.min(properties.length, arguments.length); for(i=0; i<len; i++){if(properties[i]>=arguments[i]){if(i+1<len && properties[i]==arguments[i]){continue;}else{return true;}}else{return false;}}}; self.FlashDetect=function(){if(navigator.plugins && navigator.plugins.length>0){var type="application/x-shockwave-flash"; var mimeTypes=navigator.mimeTypes; if(mimeTypes && mimeTypes[type] && mimeTypes[type].enabledPlugin && mimeTypes[type].enabledPlugin.description){var version=mimeTypes[type].enabledPlugin.description; var versionObj=parseStandardVersion(version); self.raw=versionObj.raw; self.major=versionObj.major; self.minor=versionObj.minor; self.revisionStr=versionObj.revisionStr; self.revision=versionObj.revision; self.installed=true;}}else if(navigator.appVersion.indexOf("Mac")==-1 && window.execScript){var version=-1; for(var i=0; i<activeXDetectRules.length && version==-1; i++){var obj=getActiveXObject(activeXDetectRules[i].name); if(!obj.activeXError){self.installed=true; version=activeXDetectRules[i].version(obj); if(version!=-1){var versionObj=parseActiveXVersion(version); self.raw=versionObj.raw; self.major=versionObj.major; self.minor=versionObj.minor; self.revision=versionObj.revision; self.revisionStr=versionObj.revisionStr;}}}}}();};FlashDetect.JS_RELEASE="1.0.4";</script>';
    echo '<script type="text/javascript">if(!FlashDetect.installed){alert("Para jogar o ZapTank é necessário baixar um navegador com flash, ou nosso logger. Iremos te encaminhar para o download."); window.location.href="/download";}else{}</script>';
}
?>
<head>
   <title><?php echo $Title; ?> Tela de Jogo</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta content="DorGasF" name="author" />
   <link rel="icon" type="image/png" sizes="32x32" href="favicon.ico">
   <link rel="apple-touch-icon-precomposed" href="favicon.ico" />
   <link rel="stylesheet" href="/assets/css/bootstrapv2.min.css">
   <script async src="/assets/css/jqueryplay.min.js"></script>
   <script src="/assets/jquery-3.6.1.min.js"></script>
   <style type="text/css">body{overflow: hidden;}.Hide{display:none}@media only screen and (max-width:1026px){#nav{display:none}}@media only screen and (max-height:749px){#nav{display:none}}</style>
   <button class="btn btn-dark Show" id="nav" type="button" style="position:fixed;bottom:8px;right:8px">Ocultar</button>
   <button class="btn btn-dark Hide" id="nav" type="button" style="position:fixed;bottom:8px;right:8px">Mostrar</button>
</head>
<?php
    if (!strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank'))
    {
        echo ("<script>function toLocation(patch, msg){alert(msg); window.location='play?sid=$CryptServer';}</script>");
        echo ('<script type="text/javascript">window.onbeforeunload=function (e){return "Deseja realmente fechar o ZapTank?";}; </script>');
    }
    if (strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank'))
    {
        echo ("<script>function toLocation(patch, msg){if (msg=='Desculpe, você está desconectado. Por favor atualize e faça o login novamente.'){window.location.href='play?sid=$CryptServer';}}</script>");
    } 
   ?>
<body onselectstart="return false">
   <?php
    if (!strstr($_SERVER['HTTP_USER_AGENT'], 'LauncherZapTank'))
    {
		echo '<div id="target"> <nav id="nav" class="navbar navbar-expand-lg"> <a class="navbar-brand" href="/serverlist?suv=' . $CryptServer . '"> <img alt="DDTank" width="100px" height="65px" src="/assets/img/login/new-logo.webp" alt="Logo"> </a> <div class="collapse navbar-collapse" id="navbarSupportedContent"> <ul class="navbar-nav mr-auto"></ul> <nav class="my-2 my-md-0 mr-md-3"> <ul class="navbar-nav mr-auto"> <li class="nav-item"> <a href="' . $WhatsApp . '" target="_blank" class="nav-link">WhatsApp</a> </li><li class="nav-item"> <a href="' . $Face . '" target="_blank" class="nav-link">Página do Facebook</a> </li><li class="nav-item nav-recarregue"> <a href="viplist?page=vipitemlist&server=' . $CryptServer . '" target="_blank" class="nav-link" style="color:white">Recarregar</a> </li><li class="nav-item"> <a href="download?page=launcher" target="_blank" class="nav-link">Baixar Logger</a> </li><li class="nav-item"> <a href="play?logout=true" class="nav-link">Deslogar</a> </li></ul> </nav> </div></nav></div>';
	}
	else
	{
		echo '<div id="target"> <nav id="nav" class="navbar navbar-expand-lg"> <a class="navbar-brand" href="serverlist?suv=' . $CryptServer . '"> <img alt="DDTank" width="100px" height="65px" src="/assets/img/login/new-logo.webp" alt="Logo"> </a> <div class="collapse navbar-collapse" id="navbarSupportedContent"> <ul class="navbar-nav mr-auto"></ul> <nav class="my-2 my-md-0 mr-md-3"> <ul class="navbar-nav mr-auto"> <li class="nav-item"> <a href="/serverlist?suv=' . $CryptServer . '" class="nav-link">Voltar</a> </li><li class="nav-item"></li><li class="nav-item"> <a href="' . $WhatsApp . '" target="_blank" class="nav-link">WhatsApp</a> </li><li class="nav-item"> <a href="' . $Face . '" target="_blank" class="nav-link">Página do Facebook</a> </li><li class="nav-item nav-recarregue"> <a href="viplist?page=vipitemlist&server=' . $CryptServer . '" class="nav-link" style="color:#fff">Recarregar</a> </li><li class="nav-item"> <a href="play?logout=true" class="nav-link">Deslogar</a> </li></ul> </nav> </div></nav></div>';
	}
    ?>
	<tr>
      <td valign="middle">
         <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
               <td align="center">
                  <div id="gameContainer" >
                     <div id="playgame" ></div>
					 <div id="object"></div>
                     <div id="loading">
                     </div>
                  </div>
               </td>
            </tr>
         </table>
      </td>
   </tr>
   <script type="text/javascript"> $('.Show').click(function(){$('#target').hide(0); $('.Show').hide(0); $('.Hide').show(0);});$('.Hide').click(function(){$('#target').show(0); $('.Show').show(0); $('.Hide').hide(0);});$('.toggle').click(function(){$('#target').toggle('slow');}); </script>
   <script async type="text/javascript" src="assets/js/title.js"></script>
   <script async src="https://www.googletagmanager.com/gtag/js?id=G-HV0M50WNC8"></script>
   <script>window.dataLayer=window.dataLayer || []; function gtag(){dataLayer.push(arguments);}gtag('js', new Date()); gtag('config', 'G-HV0M50WNC8');</script>
   <style>body{background-image:url(/assets/img/login/back-game.webp);background-position:center center;margin-bottom:20px!important;background-repeat:no-repeat;background-color:rgb(22,23,20)!important}*,html,body,embed,object{cursor:url(/assets/img/login/default.cur),default;!important}table{margin:20px auto}.jogar{margin-top:13%}.logger{margin-top:-40px}a:hover{cursor:url(/assets/img/login/link.cur),pointer;!important}</style>
   <script type="text/javascript" src="./js/utils/cookie.js"></script>
   <script type="text/javascript" src="./js/config.js"></script>
   <script type="text/javascript" src="./js/utils/url.js"></script>
   <script type="text/javascript" src="./js/functions.js"></script>
   <script type="text/javascript">
		var usp = new URLSearchParamsPolyfill(window.location.search);
			
		var suv = usp.get('sid');	
			
		if(suv == null || suv == '') {
			window.location.href = 'serverlist';
		}
		
		checkServerSuv(suv);
		checkCharacter(suv);
		
		var url = `${api_url}/play/${suv}`;
        var jwt_hash = getCookie('jwt_authentication_hash');
        
        var xhr = new XMLHttpRequest();
        
        xhr.open('GET', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('Content-type', 'application/json');
        xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);
        
        xhr.onreadystatechange = function() {
            if(xhr.readyState == 4) {
                if(xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);					
					if(response.status_code == 'banned_user') {
						window.location.href = 'selectserver?error_code=2';
					} else if(response.status_code == 'server_was_not_opened' || response.status_code == 'server_maintenance') {
						window.location.href = response.redirect;
					} else {
						document.getElementById("object").innerHTML = response.data;
					}
                } else if(xhr.status == 401) {
                    displayMessage(type = 'error', message = 'A sessão expirou, faça o login novamente.');
                    setTimeout(function(){
                        window.location.href = '/selectserver?logout=true';
                    }, 1000);
                } else {
                    console.log("Erro na solicitação. Código do status: " + xhr.status);
                }						
            }
        };
        
        xhr.send();
   </script>
</body>