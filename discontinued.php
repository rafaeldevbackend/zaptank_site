<?php
   include 'globalconn.php';
   include 'getconnect.php';
   $Connect = Connect::getConnection(); 
   ?>
<!DOCTYPE html>
<html lang="pt-br">
   <title><?php echo $Title; ?> Rules</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
               <span class="login100-form-title">
                  A versão do Launcher que você está utilizando foi descontinuada, por favor baixe a nova versão para continuar.
               </span>
               </br>               
               <div id="main_form">
                  <a class="btn btn-block btn-primary" href="/download">Baixar Novo Launcher</a>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>