<?php
   include 'globalconn.php';
   include 'getconnect.php';
   
   $Connect = Connect::getConnection(); 
   $_SESSION['Status'] = "Conectado";
   
   
   if (session_status() !== PHP_SESSION_ACTIVE) {
   session_start(['cookie_lifetime' => 2592000,'cookie_secure' => true,'cookie_httponly' => true]);
   }
   
   include 'loadautoloader.php';
   include 'Objects/gerenciamento.php';
   
   $Dados->Destroy();
   ?>
<!DOCTYPE html>
<html lang="pt-br">
   <title><?php echo $Title; ?> Recarga</title>
   <?php include 'Controllers/header.php'; ?>
   </head>
   <body>
      <div class="limiter">
         <div class="container-login100">
            <div class="wrap-login200 p-l-50 p-r-50 p-t-77 p-b-30">
			<div class="row">
             <div class="col-sm-6 col-lg-4 mb-3 mb-sm-5">
                     <div class="card custom-checkbox-card-lg checked">
                        <!-- Header -->
                        <div class="card-header d-block text-center">
                           <small class="card-subtitle">Comprar cupons</small>
                           <div class="mb-3">
                              <a href="#" class="payment-gateway jwc_pay" data-platform="pix" name="gateway" value="pix"><img alt="DDTank" width="50" src="<?php echo $Resource ?>unfrightprop/gift/icon.png" alt="DDTank"></a>
                           </div>
                           <span>Loja de cupons</span>
                           <p class="card-text font-weight-bold text-primary">Promoções incríveis</p>
                        </div>
                        <!-- End Header -->
                        <!-- Body -->
                        <div class="card-body">
                           <ul class="list-checked list-checked-primary list-unstyled-py-2">
                              <li class="list-checked-item">Compra Blindada</li>
                           </ul>
                        </div>
                        <!-- End Body -->
                        <!-- Footer -->
                        <div class="card-body-stretched">
                           <?php echo '<a target="_blank" href="recarga" class="btn btn-block btn-primary custom-checkbox-card-btn">Comprar</a>' ?>
                        </div>
                     </div>
                  </div>
				  <!--
                        <div class="card-header d-block text-center">
                           <small class="card-subtitle">Comprar Itens</small>
                           <div class="mb-3">
                              <a href="#" class="payment-gateway jwc_pay" data-platform="pix" name="gateway" value="pix"><img alt="DDTank" width="50" src="<?php echo $Resource ?>unfrightprop/suijikayin/icon.png" alt="DDTank"></a>
                           </div>
                           <span>Garantia perpétua.</span>
                           <p class="card-text font-weight-bold text-primary">Permanente</p>
                        </div>
                        <div class="card-body">
                           <ul class="list-checked list-checked-primary list-unstyled-py-2">
                              <li class="list-checked-item">Compra Blindada</li>
                           </ul>
                        </div>
                        <div class="card-body-stretched">
                           <?php echo '<a target="_blank" href="#" class="btn btn-block btn-primary custom-checkbox-card-btn">Comprar</a>' ?>
                        </div>
                     </div>
                  </div>
				  -->
            </div>
         </div>
		 </div>
      </div>
      <script async src="./assets/main.js"></script>
   </body>
</html>