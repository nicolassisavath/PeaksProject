var contentDisplayer = document.querySelector("#main");
var modal = document.querySelector("#heroDetailsModal");

//******************* TOOLS
function displayHeroes(limit = 20, offset = 100){
	var url = baseUrl + 'getCharactersList?limit=' + limit + "&offset=" + offset;
	request("GET", baseUrl + 'getCharactersList', displayHeroesCbk);
}

function createHeroCard(hero)
{
	let card = document.createElement('div');
	let cardName = document.createElement('div');
	// let cardPicture = document.createElement('div');
	let cardPicture = document.createElement('img');
	let cardDescription = document.createElement('div');
	card.appendChild(cardName);
	card.appendChild(cardPicture);
	card.appendChild(cardDescription);

	cardName.innerHTML = hero['name'];
	cardName.classList.add('cardName');

	urlPicture = hero['path'] + "/portrait_xlarge." + hero['extension']
	cardPicture.src = urlPicture;
	// cardPicture.style.background = 'url('+urlPicture+') center';
	// cardPicture.setAttribute("onclick", "getDetails("+hero['id']+")");
	cardPicture.setAttribute("onclick", "getDetails(this, " + hero['id'] + ")");

	cardPicture.classList.add('cardPicture');

	cardDescription.innerHTML = (hero['description'] == '' ? "No Description" : hero['description']);
	cardDescription.classList.add('cardDescription');

	card.classList.add('card');
	
	contentDisplayer.appendChild(card);
}


// function getDetails(id) {
// 	url = baseUrl + 'getThreeFirstComicsByCharacterId?id=' + id;
// 	request("GET", url, displayModalCbk);
// }
function getDetails(element, id) {
	// console.log(element.parentElement);
	// var card = element.parentElement;

	// //On recupère les données déjà présentes dans le DOM 
	// //pour ne pas faire de requête sur le héro
	// var name = card.querySelector(".cardName").innerText;
	// var description = card.querySelector(".cardDescription").innerText;
	// var image = card.querySelector(".cardPicture").src;
	// var newImage = image.replace("portrait_xlarge", "portrait_uncanny");

	// console.log(name +" " +description +" " +newImage );
	transfertDetailsToModal(element);
	url = baseUrl + 'getThreeFirstComicsByCharacterId?id=' + id;
	request("GET", url, displayModalCbk);
}

function transfertDetailsToModal(element){
	console.log(element.parentElement);
	var card = element.parentElement;

	//On recupère les données déjà présentes dans le DOM 
	//pour ne pas faire de requête sur le héro
	modal.querySelector("#modalName").innerText = card.querySelector(".cardName").innerText;
	modal.querySelector("#modalDescription").innerText = card.querySelector(".cardDescription").innerText;

	var image= card.querySelector(".cardPicture").src;
	modal.querySelector("#modalImage").src = image.replace("portrait_xlarge", "portrait_uncanny");
}

//******************* EVENTS
window.onclick = function(event) {
	if (event.target == modal) {
		display(modal, false);
	}
}

//******************* CALLBACKS
function displayHeroesCbk(xhr){
	if (xhr.status == 200) {
		var heroes = JSON.parse(xhr.responseText);

		heroes.forEach(hero => createHeroCard(hero));
	}
}

function displayModalCbk(xhr){
	if (xhr.status == 200) {
		var response = JSON.parse(xhr.responseText);
		console.log(response)

		var count = response['data']['total'];
		modal.querySelector('#appNumberInComics').innerText = count;

		var comics = response['data']['results'];

		var list = modal.querySelector('#firstAppeareances');
		list.innerHTML = '';
		comics.forEach(comic =>{
			var item = document.createElement('li');
			list.appendChild(item);
			item.innerHTML = comic['title'];
			// var title = comic['title'];
		})

		display(modal);
	}
}