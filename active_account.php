<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title><?php echo $Title; ?> Ative sua conta</title>
    <meta name="description" content="Jogue grátis no maior servidor de DDTank atualizado com cupons por cada batalha! Divirta-se com todas as armas, pets e itens.">
    <?php include 'Controllers/header.php'; ?>
</head>
<body>
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
                        window.location.href = '/selectserver?alert_code=1';
                    } else {
                        window.location.href = '/selectserver';
                    }
                } else {
					displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
					setTimeout(function(){
						window.location.href = '/';							
					}, 2000);
                }						
            }
        };
        
        xhr.send();
    </script>
</body>
</html>