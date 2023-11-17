function checkServerSuv(suv) {
	
	var url = `${api_url}/server/check/${suv}`;
	var jwt_hash = getCookie('jwt_authentication_hash');

	var xhr = new XMLHttpRequest();

	xhr.open('GET', url, true);
	xhr.setRequestHeader('Content-type', 'application/json');	
	xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);

	xhr.onreadystatechange = function() {
	  if(xhr.readyState == 4) {
		if(xhr.status == 200) {
		  var response = JSON.parse(xhr.responseText);
		  if(response.suv_token_is_valid == false) {
			window.location.href = '/selectserver?error_code=1';
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
}

function checkCharacter(suv) {
	
	var url = `${api_url}/character/check/${suv}`;
	var jwt_hash = getCookie('jwt_authentication_hash');

	var xhr = new XMLHttpRequest();

	xhr.open('GET', url, true);
	xhr.setRequestHeader('Content-type', 'application/json');	
	xhr.setRequestHeader('Authorization', `Bearer ${jwt_hash}`);

	xhr.onreadystatechange = function() {
	  if(xhr.readyState == 4) {
		if(xhr.status == 200) {
		  var response = JSON.parse(xhr.responseText);
		  if(response.character_is_created == false) {
			window.location.href = `/selectserver?nvic=new&sid=${suv}`;
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
}

function updateSession(session, value, csrf_token) {

	var url = './update_session.php';
	var params = `session=${session}&value=${value}&csrf_token=${csrf_token}`;

	var xhr = new XMLHttpRequest();

	xhr.open('POST', url, true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("Content-type", "application/json");

	xhr.onreadystatechange = function() {
		if(xhr.readyState === 4) {
			if(xhr.status === 200) {
				var response = JSON.parse(xhr.responseText);
				if(response.success == false) {
					displayMessage(type = 'error', message = 'Houve um erro interno');
				}                     
			} else {
				displayMessage(type = 'error', message = 'Houve um erro interno, se o problema persistir contate o administrador.');
			}
		}
	};

	xhr.send(params);	
}	