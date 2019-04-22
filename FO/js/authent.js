
var signInBtn = document.querySelector("#signIn");
var signUpBtn = document.querySelector("#signUp");
// var displaySignUpFormBtn = document.querySelector("#displaySignUpForm");
// var displaySignInFormBtn = document.querySelector("#displaySignInForm");
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

//Display or hide an element
function display(element, display = true){
	if (display)
		element.classList.remove("hidden");
	else
		element.classList.add("hidden");
}

//*********EVENTS
logoutBtn.onclick = function(){
	if (localStorage.getItem("connecté"))
		localStorage.removeItem("connecté");
	displayIsConnectedForms(false);
}

signInBtn.onclick = function(e){
	e.preventDefault();
	//on recupère les valeurs de login et password
	var login = document.querySelector('#signInForm>input[name="login"]').value;
	var pwd = document.querySelector('#signInForm>input[name="password"]').value;

	if (login == '' || pwd == '') 
		alert('Tous les champs ne sont pas remplis'); // voir si on fait une notif
	else {
		var data = {
			login : login,
			password : pwd
		}
		request("POST", baseUrl + 'user/compare', signInBtnCbk, data);
	}
}

signUpBtn.onclick = function(e){
	e.preventDefault();
	//on recupère les valeurs de login et password
	var login = document.querySelector('#signUpForm>input[name="login"]').value;
	var pwd = document.querySelector('#signUpForm>input[name="password"]').value;
	var pwdConf = document.querySelector('#signUpForm>input[name="passwordConf"]').value;

	if (login == '' || pwd == '' || pwdConf == '') 
		alert('Tous les champs ne sont pas remplis');// voir si on fait une notif
	else if (pwd != pwdConf)
		alert('Vos mots de passes sont differents')
	else {
		var data = {
			login : login,
			password : pwd
		}
		request("POST", baseUrl + 'user/balance', signUpBtnCbk, data);
	}
}

//*********CALLBACKS
function signInBtnCbk(xhr) {
	if (xhr.status == 200) {
		var response = JSON.parse(xhr.responseText);
		console.log(response);
		localStorage.setItem("connecté", true);
		displayIsConnectedForms(true);
		//Get the favourites of the user
		//Display Add favourite on heroes cards
	}
	else
		alert(xhr.responseText);
}

function signUpBtnCbk(xhr) {
	if (xhr.status == 200) {
		var response = JSON.parse(xhr.responseText);
		console.log(response);
		//Montrer le sign in form
	}
	else
		alert(xhr.responseText);
}