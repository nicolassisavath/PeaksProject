var prefixUser = "user/";
var signInBtn = document.querySelector("#signIn");
var signUpBtn = document.querySelector("#signUp");
var signInForm = document.querySelector("#signInForm");
var signUpForm = document.querySelector("#signUpForm");
var logoutBtn = document.querySelector("#logout");


//Display the signInForm, the signUpForm and logout button
//in function of the connected status
function displayIsConnectedForms(connected = true){
	display(signInForm, !connected);
	display(signUpForm, !connected);
	display(logoutBtn, connected);
}

function displayIsConnectedNotifications(connected = true){
	if (connected)
		notify("You are connected. <br /> You can click on heroes cards and then add them to your favourites.");
	else
		notify("Connect to choose your favourite heroes.");
}

//*********EVENTS
logoutBtn.onclick = function(){
	if (localStorage.getItem("connected")){
		localStorage.removeItem("connected");
		localStorage.removeItem("userId");
	}
	displayIsConnectedForms(false);
	notify("You are not connected anymore.");
}

signInBtn.onclick = function(e){
	e.preventDefault();
	//on recupère les valeurs de login et password
	var login = document.querySelector('#signInForm>input[name="login"]').value;
	var pwd = document.querySelector('#signInForm>input[name="password"]').value;

	if (login == '' || pwd == '') 
		notify('Some fields are missing !'); // voir si on fait une notif
	else {
		var data = {
			login : login,
			password : pwd
		}
		request("POST", baseUrl + prefixUser +'login', signInBtnCbk, data);
	}
}

signUpBtn.onclick = function(e){
	e.preventDefault();
	//on recupère les valeurs de login et password
	var login = document.querySelector('#signUpForm>input[name="login"]').value;
	var pwd = document.querySelector('#signUpForm>input[name="password"]').value;
	var pwdConf = document.querySelector('#signUpForm>input[name="passwordConf"]').value;

	if (login == '' || pwd == '' || pwdConf == '') 
		notify('Some fields are missing !');// voir si on fait une notif
	else if (pwd != pwdConf)
		notify('Passwords are different !');
	else {
		var data = {
			login : login,
			password : pwd
		}
		request("POST", baseUrl + prefixUser + 'create', signUpBtnCbk, data);
	}
}

//*********CALLBACKS
function signInBtnCbk(xhr) {
	var response = JSON.parse(xhr.responseText);
	if (xhr.status == 200) {
		localStorage.setItem("connected", true);
		localStorage.setItem("userId", response['userId']);
		displayIsConnectedForms(true);
		notify("You can select your favourite heroes.");
		//Get the favourites of the user
	}
	else
		notify(response['status']);
}

function signUpBtnCbk(xhr) {
	var response = JSON.parse(xhr.responseText);
	if (xhr.status == 200) {
		notify("Your account is created.You can connect now.");
		//Notifier
	}
	else
		notify(response['status']);
}