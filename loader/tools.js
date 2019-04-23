window.onload = function(){
	console.log('hey');
var baseUrl = "http://www.mocky.io/v2/5cbebc84300000f2069ce232?mocky-delay=1000ms";
var resultsDisplayer = document.querySelector("#resultsDisplayer");
var loader = document.querySelector('.loader');
var requestBtn = document.querySelector('#requestBtn');

	function display(element, display = true) {
		if (display)
			element.classList.add("hidden");
		else
			element.classList.remove("hidden");
	}

	function request(method, url, callback = null, data = null) {
		var xhr = new XMLHttpRequest();

		xhr.onload = function(){
			if (callback != null)
				callback(this);
				display(loader, false);
		}

		display(loader);
		xhr.open(method, url);
		if (data){
			data = JSON.stringify(data);
			xhr.setRequestHeader("Content-Type", "application/json");
		}
		xhr.send(data);
	}



	requestBtn.onclick = function() {
	console.log('click');

		request("GET", baseUrl, displayData);
	}

	function displayData(xhr){
		if (xhr.status == 200){
			var response = JSON.parse(xhr.responseText);
			resultsDisplayer.innerHTML = response['loader'];
		}
		else {
			resultsDisplayer.innerHTML = "Something went wrong";	
		}
	}




}