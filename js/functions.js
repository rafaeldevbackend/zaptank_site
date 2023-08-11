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
			window.location.href = '/selectserver';
		  }
		} else {
		  console.log("Erro na solicitação. Código do status: " + xhr.status);
		}						
	  }
	};

	xhr.send();
}

function checkCharacter(suv) {
	
	var url = `${api_url}/character/check`;
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
		  console.log("Erro na solicitação. Código do status: " + xhr.status);
		}						
	  }
	};

	xhr.send();	
}