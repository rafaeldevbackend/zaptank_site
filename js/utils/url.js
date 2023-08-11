function URLSearchParamsPolyfill(queryString) {
	
	this.params = {};

	if (queryString) {
		queryString = queryString.replace(/^\?/, '');
		var pairs = queryString.split('&');

		for (var i = 0; i < pairs.length; i++) {
			var keyValue = pairs[i].split('=');
			var key = decodeURIComponent(keyValue[0]);
			var value = decodeURIComponent(keyValue[1] || '');

			this.params[key] = value;
		}
	}

	this.get = function (key) {
		return this.params.hasOwnProperty(key) ? this.params[key] : null;
	};

	this.has = function (key) {
		return this.params.hasOwnProperty(key);
	};

	this.getAllKeys = function () {
		return Object.keys(this.params);
	};
}