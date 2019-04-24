/*
Functions and variables utilized in other script files
 */


var baseUrl = "http://localhost:8000/api/";
var prefixHeroes = "marvel/";
var prefixUser = "user/";
var loader = document.querySelector(".loaderModal");
	
function request(method, url, callback = null, data = null) {
	var xhr = new XMLHttpRequest();

	xhr.onload = function(){
		if (callback != null){
			display(loader, false);
			callback(this);
		}

	}

	display(loader);
	xhr.open(method, url);
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