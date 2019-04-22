var baseUrl = "http://localhost:8000/";
	
function request(method, url, callback = null, data = null) {
	var xhr = new XMLHttpRequest();

	xhr.onload = function(){
		if (callback != null)
			callback(this);
	}

	xhr.open(method, url);
	if (data){
		data = JSON.stringify(data);
		xhr.setRequestHeader("Content-Type", "application/json");
	}
	xhr.send(data);
}