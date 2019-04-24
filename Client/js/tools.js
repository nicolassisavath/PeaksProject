/*
Functions and variables utilized in other script files
 */


var baseUrl = "http://localhost:8000/api/";
var prefixHeroes = "marvel/";
var prefixUser = "user/";
var loader = document.querySelector(".loaderModal");
	

//Make ajax requests to the server
function request(method, url, callback, data = null, displayLoader = true) {
	var xhr = new XMLHttpRequest();

	if (xhr == null){
		notify("Your browser does not support ajax requests. <br />Please update your browser version.");
		return;
	}

	xhr.onload = function(){
		display(loader, false);
		callback(this);
	}

	xhr.ontimeout = function(){
		notify("The server did not respond in suffiscient time.");
		display(loader, false);
	}

	xhr.open(method, url, true);

	xhr.timeout = 30000;// set a timeout of 30 s
	if (displayLoader)
		display(loader);
	if (data){
		data = JSON.stringify(data);
		xhr.setRequestHeader("Content-Type", "application/json");
	}
	
	xhr.send(data);
}

//Display or hide an element
function display(element, display = true){
	if (display)
		element.classList.remove("hidden");
	else
		element.classList.add("hidden");
}

//Display the connected status
function isAuthent() {
	return localStorage.getItem("userId");
}

//Notify the user in the sidebar
function notify(message) {
	var notifications = document.querySelector('#notifications');
	notifications.innerHTML = message;
}