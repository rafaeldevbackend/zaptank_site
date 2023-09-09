function displayMessage(type, message) {
	
    var errorDiv = document.getElementById('error');

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
    alert.textContent = message;

    errorDiv.appendChild(alert);

	setTimeout(function(){
		$(alert)
			.fadeTo(500, 0)
			.slideUp(500, function () {
				$(this).remove();
			});
	}, 750);
}
