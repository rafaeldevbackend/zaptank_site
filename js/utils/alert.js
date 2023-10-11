function displayMessage(type, message, time) {
	
    var errorDiv = document.getElementById('error');
	
	if(time == null) {
		var timeout = 2500;
	} else {
		var timeout = time;
	}

    if (!errorDiv || message == '') {
        return;
    }

    var cssClass;

    if (type === 'error') {
        cssClass = 'alert alert-danger ocult-time';
    } else if (type === 'success') {
        cssClass = 'alert alert-success ocult-time';
    } else {
        return;
    }

    var alert = document.createElement('div');
    alert.className = cssClass;
    alert.innerHTML = message;

    var existingAlert = errorDiv.querySelector('.alert');
    if (existingAlert) {
        $(existingAlert).remove();
    }

    errorDiv.appendChild(alert);

	setTimeout(function(){
		$(alert)
			.fadeTo(500, 0)
			.slideUp(500, function () {
				$(this).remove();
			});
	}, timeout);
}
