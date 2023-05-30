if(typeof title_effect == "undefined"){
	title_effect = {};
	title_effect.original = '';
	title_effect.tickerFlag = false;
	title_effect.tickerMsg = 'É a sua vez!';
	title_effect.tickerSpacer = ' ';
	title_effect.tickerMsgud = title_effect.tickerMsg;
	title_effect.tickerSpeed = 350;
	
	title_effect.tickerBegin = function(msg,speed,spacer) {
		title_effect.tickerStop();
		if(msg != null) title_effect.tickerMsg = msg;
		if(speed != null) title_effect.tickerSpeed = speed;
		if(spacer != null) title_effect.tickerSpacer = spacer;
		title_effect.tickerMsgud = " " + title_effect.tickerMsg;
		window.setTimeout("title_effect.tickerBeginRoll()", title_effect.tickerSpeed + 1);
	}
	
	title_effect.tickerBeginRoll = function() {
		title_effect.tickerFlag = true;
		window.setTimeout("title_effect.tickerRoll()", title_effect.tickerSpeed);
	}
	
	title_effect.tickerRoll = function(){
		if (title_effect.tickerMsgud.length == 1) title_effect.tickerMsgud = title_effect.tickerSpacer + title_effect.tickerMsg; 
		title_effect.tickerMsgud = title_effect.tickerMsgud.substring(1, title_effect.tickerMsgud.length); 
		document.title = title_effect.tickerMsgud.substring(0, title_effect.tickerMsg.length);
		if(title_effect.tickerFlag) window.setTimeout("title_effect.tickerRoll()", title_effect.tickerSpeed);
		else document.title = title_effect.original;
	}

	title_effect.tickerStop = function(msg){
		if(msg != null) title_effect.original = msg;
		title_effect.tickerFlag = false;
	}
	
	$(document).ready(function(){title_effect.original = document.title;})
}