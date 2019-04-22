var heroesDisplayer = document.querySelector("#heroesContainer");
var modal = document.querySelector("#heroDetailsModal");
var cardPictureFormat = "portrait_xlarge";
var modalPictureFormat = "portrait_uncanny";
var sizePage = 20;
var initialOffset = 100;

//******************* TOOLS
function displayHeroes(offset = initialOffset, limit = sizePage){
	var url = baseUrl + prefixHeroes + 'getCharactersList?limit=' + limit + "&offset=" + offset;
	request("GET", url, displayHeroesCbk);
}

function createHeroCard(hero, isFavorite = false)
{
	let card = document.createElement('div');
	card.classList.add('card');

	//Name
	let cardName = document.createElement('div');
	card.appendChild(cardName);
	cardName.innerHTML = hero['name'];
	cardName.classList.add('cardName');

	//Picture
	let cardPicture = document.createElement('img');
	card.appendChild(cardPicture);
	urlPicture = hero['path'] + "/portrait_xlarge." + hero['extension']
	cardPicture.src = urlPicture;
	cardPicture.setAttribute("onclick", "getModalDetails(this, " + hero['id'] + ")");
	cardPicture.classList.add('cardPicture');

	//Card Footer
	let cardFooter = document.createElement('div');
	card.appendChild(cardFooter);

	if (isFavorite){
		//Remove from favourite Btn
		let removeFromFavouritesBtn = document.createElement('button');
		removeFromFavouritesBtn.setAttribute("onclick", "removeFromFavourites(" + hero['id'] + ")")
		removeFromFavouritesBtn.innerHTML = "Remove from favourites"
		removeFromFavouritesBtn.classList.add('btn');
		cardFooter.append(removeFromFavouritesBtn);
	}
	else {
		//Description
		cardFooter.innerHTML = (hero['description'] == '' ? "No Description" : hero['description']);
		cardFooter.classList.add('cardDescription');
	}

	return card;
}

function getModalDetails(element, id) {
	// transfer hero data from the card to the modal
	transfertDetailsToModal(element, id);

	// request additional data about comics and display in the modal
	url = baseUrl + prefixHeroes + 'getThreeFirstComicsByCharacterId?id=' + id;
	request("GET", url, displayModalCbk); 
}

function transfertDetailsToModal(element, id){
	modal.setAttribute("data-id", id);
	var card = element.parentElement;

	//On recupère les données déjà présentes dans le DOM 
	//pour ne pas faire de requête sur le héro
	modal.querySelector("#modalName").innerText = card.querySelector(".cardName").innerText;
	modal.querySelector("#modalDescription").innerText = card.querySelector(".cardDescription").innerText;

	var image= card.querySelector(".cardPicture").src;
	modal.querySelector("#modalImage").src = image.replace("portrait_xlarge", "portrait_uncanny");
}

function paginate(offset, limit, total){
	var paginationContainer = document.querySelector("#paginationContainer");
	var pagination = document.querySelector("#pagination");

	paginationContainer.innerHTML = '';

	var max = Math.ceil(total/limit); // get the maximim number of pages
	if (max > 1){
		var pagination = document.createElement('select');
		pagination.setAttribute("onclick", "displayHeroes(this.value)");
		for(var i = 1; i <= max; i++){
			var option = document.createElement('option');
			option.value = (i-1) * limit;
			option.innerHTML = i;
			if (option.value == offset)
				option.selected = true;
			pagination.append(option);
		}
		paginationContainer.append(pagination);

		//Before Button
		if (offset != 0){ //don't show befoore nutton on first page
			var before = document.createElement('a');
			before.innerHTML = "&laquo";
			beforeOffset = offset - limit;
			before.setAttribute("onclick", "displayHeroes(" + beforeOffset + ")");
			before.classList.add('paginationBtn');
			paginationContainer.prepend(before);
		}

		//After Button
		if ( (offset/limit)+1 != max ){ //don't show after button if on last page
			var after = document.createElement('a');
			after.innerHTML = "&raquo;";
			afterOffset = offset + limit;
			after.setAttribute("onclick", "displayHeroes(" + afterOffset + ")");
			after.classList.add('paginationBtn');
			paginationContainer.append(after);
		}
	}
}
//******************* EVENTS
window.onclick = function(event) {
	if (event.target == modal) 
		display(modal, false);
}

//******************* CALLBACKS
function displayHeroesCbk(xhr){
	if (xhr.status == 200) {
		var response = JSON.parse(xhr.responseText);

		//Pagination
		var offset = response['offset'];
		var limit = response['limit'];
		var total = response['total'];
		paginate(offset, limit, total);

		//Dislay Heroes Cards
		var heroes = response['heroes'];
		heroesDisplayer.innerHTML = '';
	
		heroes.forEach(hero => {
			var card = createHeroCard(hero);  
			heroesDisplayer.appendChild(card);
		});
	}
}

function displayModalCbk(xhr){
	if (xhr.status == 200) {
		var response = JSON.parse(xhr.responseText)['data'];

		//Number of appearances in comics
		var count = response['total'];
		modal.querySelector('#appNumberInComics').innerText = count;

		//3 first commics
		var comics = response['results'];
		var list = modal.querySelector('#firstAppeareances');
		list.innerHTML = '';
		comics.forEach(comic =>{
			var item = document.createElement('li');
			list.appendChild(item);
			item.innerHTML = comic['title'];
		})

		//Displpay or hide Add Favourite button
		var addFavBtn = modal.querySelector("#favoriteBtn");
		var heroId = modal.getAttribute("data-id");
		var showFavBtn = isAuthent() && !isHeroFavourite(heroId);
		display(addFavBtn, showFavBtn);

		if (showFavBtn)
			addFavBtn.setAttribute("onclick", "addToFavourites(" + heroId + ")");
		
		display(modal);
	}
}

function isHeroFavourite(heroId){
	favouritesId = localStorage.getItem("favouritesId").split(",");
	return favouritesId.indexOf(heroId) != -1 ;
}


function addToFavourites(heroId){
	if (!isAuthent())
		return;
	else{
		var data = {
			userId : localStorage.getItem("userId"),
			heroId : heroId
		}
		request("POST", baseUrl + prefixUser +'addToFavourites', addToFavouritesCbk, data);
	}
}

function addToFavouritesCbk(xhr) {
	var response = JSON.parse(xhr.responseText);

	if (xhr.status == 200) {
		notify("The hero has been added to favourites");
		heroId = response['addedFavouriteId'];
		updateFavouritesStorage(heroId);
	}
	else
		notify(response["response"]);
}

function displayFavourites(){
	favouritesId = localStorage.getItem("favouritesId").split(",");

	if (favouritesId == null)
		return;
	favouritesContainer.innerHTML = '';
	favouritesId.forEach(favouriteId => {
		request("GET", baseUrl + prefixHeroes + "getCharacterById?id=" + favouriteId, displayFavouriteCbk)
	});
}

function displayFavouriteCbk(xhr){
	if (xhr.status == 200) {
		var hero = JSON.parse(xhr.responseText);
		var cardFavourite = createHeroCard(hero, true);  
		favouritesContainer.appendChild(cardFavourite);
	}	
	else
		notify("An error occured.");
}


function removeFromFavourites(heroId) {
	if (!isAuthent())
		return;
	else{
		var data = {
			userId : localStorage.getItem("userId"),
			heroId : heroId
		}
		url = baseUrl + prefixUser +'removeFromFavourites';
		request("POST", url, removeFromFavouritesCbk, data);
	}
}

function removeFromFavouritesCbk(xhr){
	var response = JSON.parse(xhr.responseText);

	if (xhr.status == 200) {
		notify("The hero has been removed from favourites");
		heroId = response['removedFavouriteId'];
		updateFavouritesStorage(heroId, false);
	}
	else
		notify(response["response"]);
}


function updateFavouritesStorage(heroId, add = true) {
	favouritesId = localStorage.getItem("favouritesId").split(",");

	if (add) 
		favouritesId.push(heroId);
	else {
		var index = favouritesId.indexOf(heroId);
		if (index > -1)
		  favouritesId.splice(index, 1);
	}
	localStorage.setItem("favouritesId", favouritesId);
	displayFavourites();
}