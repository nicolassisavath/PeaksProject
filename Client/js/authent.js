var signInBtn = document.querySelector("#signIn");
var signUpBtn = document.querySelector("#signUp");
var signInForm = document.querySelector("#signInForm");
var signUpForm = document.querySelector("#signUpForm");
var logoutBtn = document.querySelector("#logout");
var favouritesContainer = document.querySelector('#favouritesContainer')


/* 
 * Display the signInForm, the signUpForm and logout button
 * in function of the connected status
 */
function displayIsConnectedForms(){
	display(signInForm, !isAuthent());
	display(signUpForm, !isAuthent());
	display(logoutBtn, isAuthent());
}

/*
 * Display notifications according to the conncetion status
 */
function displayIsConnectedNotifications(){
	if (isAuthent())
		notify("You are connected. <br /> You can click on heroes cards and then add them to your favourites.");
	else
		notify("Connect to choose your favourite heroes.");
}

//*********EVENTS
logoutBtn.onclick = function(){
	localStorage.removeItem("connected");
	localStorage.removeItem("userId");
	localStorage.removeItem("favouritesId");
	favouritesContainer.innerHTML = '';

	displayIsConnectedForms();
	notify("You are not connected anymore.");
}

/* 
 * Call the server side api to check login & password
 */
signInBtn.onclick = function(e){
	e.preventDefault();
	// on recupère les valeurs de login et password
	var login = document.querySelector('#signInForm>input[name="login"]').value;
	var pwd = document.querySelector('#signInForm>input[name="password"]').value;

	if (login == '' || pwd == '') 
		notify('Some fields are missing !');
	else {
		var data = {
			login : login,
			password : pwd
		}
		request("POST", baseUrl + prefixUser +'login', signInBtnCbk, data);
	}
}

/* 
 * Call the server side api to create a new account
 */
signUpBtn.onclick = function(e){
	e.preventDefault();
	//on recupère les valeurs de login et password
	var login = document.querySelector('#signUpForm>input[name="login"]').value;
	var pwd = document.querySelector('#signUpForm>input[name="password"]').value;
	var pwdConf = document.querySelector('#signUpForm>input[name="passwordConf"]').value;

	if (login == '' || pwd == '' || pwdConf == '') 
		notify('Some fields are missing !');
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

/*
 * Handle the connection:
 *		- notify the connected user
 *		- save his favourites in localStorage
 *		- display his favourites
 */
function signInBtnCbk(xhr) {
	var response = JSON.parse(xhr.responseText);
	if (xhr.status == 200) {
		localStorage.setItem("connected", true);
		localStorage.setItem("userId", response['userId']);
		displayIsConnectedForms();
		notify("You are connected. You can select your favourite heroes.");

		var favouritesId = response['favourites'];
		localStorage.setItem("favouritesId", favouritesId);
		displayFavourites();
	}
	else
		notify(response['status']);
}

/*
 * Notify the user of his creation account status
 */
function signUpBtnCbk(xhr) {
	var response = JSON.parse(xhr.responseText);
	if (xhr.status == 200) {
		notify("Creation succeeded! <br />You can connect now to your account.");
	}
	else
		notify(response['status']);
}
