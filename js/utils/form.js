 function getSelectedValueFromInputRadio(nameAttribute) {
	
	var radioButtons = document.getElementsByName(nameAttribute);

	var selectedValue;
				
	for (var i = 0; i < radioButtons.length; i++) {
		if (radioButtons[i].checked) {
			selectedValue = radioButtons[i].value;
			break;
		}
	}
	
	return selectedValue;
 }