<?php
   include 'globalconn.php';
   include 'getconnect.php';
   include 'loadautoloader.php';
   
   // header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   // header("Cache-Control: post-check=0, pre-check=0", false);
   // header("Pragma: no-cache");
   
   $Connect = Connect::getConnection();
   
   ?>
<!DOCTYPE html>
<html lang="pt-br">
   <head>
   <title><?php echo $Title; ?> Baixar DDTank</title>
   <meta name="description" content="Baixar DDTank, jogue grátis.">
   <?php include 'Controllers/header.php'; ?>
   </head>
   <?php
      $val = "";
	  if (!empty($_GET['suv'])){$val = 'serverlist?suv='.$_GET['suv'].'';}else{$val = '';};
      if (!empty($_GET['page']))
      {
          switch ($_GET['page'])
          {
              case 'launcher':
                  echo '<meta http-equiv="refresh" content="0;url=/logger/InstaladorZapTank.zip?v=12" /><body> <div class="limiter"> <div class="container-login100"> <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30"> <span class="login100-form-title p-b-55"> Problemas ao clicar em permitir? solução abaixo. </span> <center><img alt="DDTank" class="p-b-55" src="/assets/img/login/permission.webp"></center> <center><img alt="DDTank" class="p-b-55" width="950" src="/assets/img/login/solution.webp"></center> <a target="_blank" class="login100-form-title p-b-55" href="/logger/InstaladorZapTank.zip?v=12">O seu download vai começar automaticamente. Se não começar, clique aqui.</a> <div class="container-login100-form-btn p-t-25" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/'.$val.'">voltar</a></div></div></div></div></body>';
              break;
			  default:
			  header("location: /download");
              exit();
			  break;
          }
      }
      else
      {
          echo '<body> <div class="limiter"> <div class="container-login100"> <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30"> <div class="row"> <span class="login100-form-title p-b-55"> Aplicativos de login de jogo </span> <div class="col-sm-6 col-lg-4 mb-3 mb-sm-5"> <div class="card custom-checkbox-card-lg checked"> <div class="card-header d-block text-center"> <div class="mb-3"> <a class="payment-gateway jwc_pay" data-platform="pix" name="gateway" value="pix"><img alt="DDTank" width="200" src="/assets/img/login/mxnitro.webp" alt="DDTank"></a> </div><p class="card-text font-weight-bold text-primary">Navegador de Desktop</p></div><div class="card-body-stretched"> <a target="_blank" href="/logger/mxnitro.exe" class="btn btn-block btn-primary custom-checkbox-card-btn">Download</a> </div></div></div><div class="col-sm-6 col-lg-4 mb-3 mb-sm-5"> <div class="card custom-checkbox-card-lg checked"> <div class="card-header d-block text-center"> <div class="mb-3"> <a class="payment-gateway jwc_pay" data-platform="pix" name="gateway" value="pix"><img width="210" src="/assets/img/login/puffin.webp" alt="DDTank"></a> </div><p class="card-text font-weight-bold text-primary">Navegador para Android/IOS</p></div><div class="card-body-stretched"> <a target="_blank" href="https://www.puffin.com/" class="btn btn-block btn-primary custom-checkbox-card-btn">Download</a> </div></div></div><div class="col-sm-6 col-lg-4 mb-3 mb-sm-5"> <div class="card custom-checkbox-card-lg checked"> <div class="card-header d-block text-center"> <div class="mb-3"> <a class="payment-gateway jwc_pay" data-platform="pix" name="gateway" value="pix"><img width="75" src="/assets/img/login/centbrowser.webp" alt="DDTank"></a> </div><p class="card-text font-weight-bold text-primary">Navegador de Desktop</p></div><div class="card-body-stretched"> <a target="_blank" href="/logger/centbrowser.exe" class="btn btn-block btn-primary custom-checkbox-card-btn">Download</a> </div></div></div><div class="col-sm-6 col-lg-4 mb-3 mb-sm-5"> <div class="card custom-checkbox-card-lg checked"> <div class="card-header d-block text-center"> <div class="mb-3"> <a class="payment-gateway jwc_pay" data-platform="pix" name="gateway" value="pix"><img width="153" src="/assets/img/login/ucbrowser.webp" alt="DDTank"></a> </div><p class="card-text font-weight-bold text-primary">Navegador de Desktop</p></div><div class="card-body-stretched"> <a target="_blank" href="/logger/UCBrowser.exe" class="btn btn-block btn-primary custom-checkbox-card-btn">Download</a> </div></div></div><div class="col-sm-6 col-lg-4 mb-3 mb-sm-5"> <div class="card custom-checkbox-card-lg checked"> <div class="card-header d-block text-center"> <div class="mb-3"> <a class="payment-gateway jwc_pay" data-platform="pix" name="gateway" value="pix"><img width="76" src="/assets/img/login/cliente.webp" alt="DDTank"></a> </div><p class="card-text font-weight-bold text-primary">Launcher Oficial ZapTank (Recomendado)</p></div><div class="card-body-stretched"> <a href="/download?page=launcher" class="btn btn-block btn-primary custom-checkbox-card-btn">Download</a> </div></div></div><div class="col-sm-6 col-lg-4 mb-3 mb-sm-5"> <div class="card custom-checkbox-card-lg checked"> <div class="card-header d-block text-center"> <div class="mb-3"> <a class="payment-gateway jwc_pay" data-platform="pix" name="gateway" value="pix"><img width="99" src="/assets/img/login/mx5.webp" alt="DDTank"></a> </div><p class="card-text font-weight-bold text-primary">Navegador de Desktop</p></div><div class="card-body-stretched"> <a target="_blank" href="/logger/mx5.exe" class="btn btn-block btn-primary custom-checkbox-card-btn">Download</a> </div></div></div><div class="container-login100-form-btn p-t-25" style="float:left;"><a class="change-form-btn" style="color:white;font-size:15px;" href="/'.$val.'">voltar</a></div></div></div></div></div></body>';
      }
      ?>
</html>