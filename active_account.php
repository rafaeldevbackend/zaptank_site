<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title><?php echo $Title; ?> Ative sua conta</title>
    <meta name="description" content="Jogue grátis no maior servidor de DDTank atualizado com cupons por cada batalha! Divirta-se com todas as armas, pets e itens.">
    <?php include 'Controllers/header.php'; ?>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 p-l-50 p-r-50 p-t-30 p-b-30">                
                <div id="error"></div>
                <div class="d-grid gap-2">
                    <a href="/selectserver">
                        <button class="btn btn-light">Página inicial</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="./js/config.js"></script>
    <script type="text/javascript" src="./js/utils/url.js"></script>
    <script type="text/javascript" src="./js/utils/alert.js"></script>
    <script type="text/javascript">
        var error_div = document.getElementById('error');

        var usp = new URLSearchParamsPolyfill(window.location.search);
		var token = usp.get('token');

        if(token == null || token == '') {
			error_div.innerHTML = '<div class="alert alert-danger">Seu token de acesso expirou ou não existe, pode ser que você tenha tentado acessar uma página que não tenha permissão.</div>';
            setTimeout(function(){
				window.location.href = '/selectserver';				
			}, 6000);
		}

        var url = `${api_url}/account/email/activate/${token}`;
        
        var xhr = new XMLHttpRequest();
        
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('Content-type', 'application/json');
        
        xhr.onreadystatechange = function() {
            if(xhr.readyState == 4) {
                if(xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(response.success == true) {
                        error_div.innerHTML = `<div class="alert alert-success">${response.message}</div>`;
                        setTimeout(function(){
                            window.location.href = '/selectserver';				
                        }, 3000);
                    } else {
                        error_div.innerHTML = `<div class="alert alert-danger">${response.message}</div>`;
                        setTimeout(function(){
                            window.location.href = '/selectserver';				
                        }, 6000);
                    }
                } else {
                    displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
                }						
            }
        };
        
        xhr.send();
    </script>
</body>
</html>