var heroesDisplayer = document.querySelector("#heroesContainer");// Container of the heroes cards
var modal = document.querySelector("#heroDetailsModal");// Modal for hero details
var cardPictureFormat = "portrait_xlarge";// Format of the cards pictures
var modalPictureFormat = "portrait_uncanny";// Format of the details modal picture
var sizePage = 20;// Number of hero cards par page
var initialOffset = 100;// Default offset of pagination

//******************* TOOLS

/*
 * Call Server API that returns list of heroes according to offset and limit
 * Called at the onload of the page and on pagination button onclick.
 */
function displayHeroes(offset = initialOffset, limit = sizePage){
	var url = baseUrl + prefixHeroes + "getCharactersList?";
	url += "limit=" + limit;
	url += "&offset=" + offset;
	request("GET", url, displayHeroesCbk);
}

/*
 * Display heroes cards and the pagination element
 */
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
	else
		notify("An internal error occured.");
}

/*** CARD HERO***/

/*
 * Return card to display hero with 
 * name
 * picture 
 * description or "Remove from favourites" button
 */
function createHeroCard(hero, isFavorite = false)
{
	let card = document.createElement('div');
	card.classList.add('card');

	// Name
	let cardName = createCardName(hero);
	card.appendChild(cardName);

	// Picture
	let cardPicture = createCardPicture(hero);
	card.appendChild(cardPicture);

	// Card Footer
	let cardFooter = createCardFooter(hero, isFavorite);
	card.appendChild(cardFooter);

	return card;
}

/*
 * Return card Name element
 */
function createCardName(hero){
	let cardName = document.createElement('div');
	cardName.classList.add('cardName');

	cardName.innerHTML = hero['name'];

	return cardName;
}

/*
 * Return card Picture element
 */
function createCardPicture(hero){
	let cardPicture = document.createElement('img');
	cardPicture.classList.add('cardPicture');

	// Set the source url of the picture
	urlPicture = hero['path'] + "/" + cardPictureFormat + "." + hero['extension']
	cardPicture.src = urlPicture;

	// Add event onclick on picture => display modal details of the hero
	cardPicture.setAttribute("onclick", "getModalDetails(this, " + hero['id'] + ")");

	return cardPicture;
}

/*
 * Return card Footer element
 */
function createCardFooter(hero, isFavouriteCard){
	let cardFooter = document.createElement('div');
	cardFooter.classList.add('cardDescription');

	if (isFavouriteCard){// We create and put the button "Remove from favourites"
	// To test
	let removeFromFavouritesBtn = createRemoveBtn(hero);
	cardFooter.appendChild(removeFromFavouritesBtn);

		// let removeFromFavouritesBtn = document.createElement('button');
		// removeFromFavouritesBtn.classList.add('btn');
		// // Add event onclick on button => call server api to remove the hero from user favourites
		// removeFromFavouritesBtn.setAttribute("onclick", "removeFromFavourites(" + hero['id'] + ")")
		// removeFromFavouritesBtn.innerHTML = "Remove from favourites"
		// cardFooter.append(removeFromFavouritesBtn);
	}
	else //  We put the description
		cardFooter.innerHTML = (hero['description'] == '' ? "No Description" : hero['description']);

	return cardFooter;
}

/*
 * Return "Remove from favourites" button
 */
function createRemoveBtn(hero){
	let removeFromFavouritesBtn = document.createElement('button');
	removeFromFavouritesBtn.classList.add('btn');

	// Add event onclick on button => call server api to remove the hero from user favourites
	removeFromFavouritesBtn.setAttribute("onclick", "removeFromFavourites(" + hero['id'] + ")");
	removeFromFavouritesBtn.innerHTML = "Remove from favourites";

	return removeFromFavouritesBtn;
}

/*
 * Test, need to bind to modal=> displayModalCbk
 * Return "Add to favourites" button
 */
function createAddBtn(hero){
	let addToFavouritesBtn = document.createElement('button');
	addToFavouritesBtn.classList.add('btn');

	// Add event onclick on button => call server api to add the hero to user favourites
	addToFavouritesBtn.setAttribute("onclick", "addToFavourites(" + hero['id'] + ")");
	addToFavouritesBtn.innerHTML = "Add to favourites";

	return addToFavouritesBtn;
}
/*** CARD HERO ***/

/*** DETAILS MODAL HERO ***/

/*
 * Call Server API that returns hero details according to offset and limit
 * Called when hero card picture onclick.
 */
function getModalDetails(pictureElmt, id) {
	// put the hero id on the modal
	// for removing/adding to favourites server api calls
	modal.setAttribute("data-id", id);

	// transfer hero data from the card to the modal
	var cardElmt = pictureElmt.parentElement;
	transferDetailsToModal(cardElmt, id);

	// request additional data about comics and display them in the modal
	url = baseUrl + prefixHeroes + 'getThreeFirstComicsByCharacterId?id=' + id;
	request("GET", url, displayModalCbk); 
}

/*
 * Transfer the name/image et description from the hero card to the modal
 * Avoid a request to the marvel character api
 */
function transferDetailsToModal(card, id){
	//On transfère les données déjà présentes dans la card
	//pour éviter une requête supplémentaire sur le héros

	//Name
	modal.querySelector("#modalName").innerText = card.querySelector(".cardName").innerText;
	//Description ?? cas du favoris...
	modal.querySelector("#modalDescription").innerText = card.querySelector(".cardDescription").innerText;
	//Image
	var image = card.querySelector(".cardPicture").src;
	modal.querySelector("#modalImage").src = image.replace(cardPictureFormat, modalPictureFormat);
}

/*
 * Display in the modal:
 *	- the number of appearances in comics
 *	- the 3 first comics
 *	- the Add/Remove favourite button
 */
function displayModalCbk(xhr){
	if (xhr.status == 200) {
		var response = JSON.parse(xhr.responseText)['data'];

		//Number of appearances in comics
		var count = response['total'];
		modal.querySelector('#appNumberInComics').innerText = count;

		//3 first commics
		var comics = response['results'];
		var firstAppeareances = modal.querySelector('#firstAppeareances');
		firstAppeareances.innerHTML = '';
		comics.forEach(comic =>{
			var item = document.createElement('li');
			firstAppeareances.appendChild(item);
			item.innerHTML = comic['title'];
		})

		//Display or hide "Add Favourite" button
		var addFavBtn = modal.querySelector("#favoriteBtn");
		var heroId = modal.getAttribute("data-id");
		var showFavBtn = isAuthent() && !isHeroFavourite(heroId);
		display(addFavBtn, showFavBtn);

		if (showFavBtn)
			addFavBtn.setAttribute("onclick", "addToFavourites(" + heroId + ")");
		//???????? Gerer le removeFavBtn??? => ajouter sur html
		// Ou gerer le createFavouriteBtn comme createRemoveBtn
		
		display(modal);
	}
	else
		notify("An internal error occured.");
}

/*
 * We hide the modal if user clicks on it
 */
window.onclick = function(event) {
	if (event.target == modal) 
		display(modal, false);
}

/*** DETAILS MODAL HERO ***/

/*** PAGINATION OF HEROES CARDS ***/

/*
 * Display the pagination element
 */
function paginate(offset, limit, total){
	var paginationContainer = document.querySelector("#paginationContainer");
	var pagination = document.querySelector("#pagination");//????Encore utile?

	paginationContainer.innerHTML = '';

	var max = Math.ceil(total/limit); // get the maximim number of pages

	if (max <= 1){
		return;
	}
	else {
		var pagination = document.createElement('select');
		// Add event onchange => display heroes with the selected offset
		pagination.setAttribute("onclick", "displayHeroes(this.value)");
		for(var i = 1; i <= max; i++){
			var option = document.createElement('option');
			option.value = (i-1) * limit; // put the corresponding offset as value
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

/*** PAGINATION OF HEROES CARDS ***/

//******************* FAVOURITES

/*
 * Return if the hero is already a favourite of the connected user
 */
function isHeroFavourite(heroId){
	var favouritesId = localStorage.getItem("favouritesId").split(",");

	return favouritesId.indexOf(heroId) != -1 ;
}

/*
 * Call the server side api to add the hero to the user's favourites
 */
function addToFavourites(heroId){
	if (!isAuthent())
		return;
	else{
		// we send the userId and heroId to server side api
		var data = {
			userId : localStorage.getItem("userId"),
			heroId : heroId
		}
		request("POST", baseUrl + prefixUser +'addToFavourites', addToFavouritesCbk, data);
	}
}

/*
 * Update the favourites stored in localStorage
 * and display them in the side bar
 */
function addToFavouritesCbk(xhr) {
	var response = JSON.parse(xhr.responseText);

	if (xhr.status == 200) {
		notify("The hero has been added to favourites");
		heroId = response['addedFavouriteId'];
		updateFavouritesStorage(heroId);
		displayFavourites();
	}
	else
		notify(response["response"]);
}

/*
 * Call the server side api to get details of character by id
 */
function displayFavourites(){
	//Exit if no favorites stored
	if (localStorage.getItem("favouritesId") == null)
		return ;
	
	favouritesId = localStorage.getItem("favouritesId").split(",");

	favouritesContainer.innerHTML = '';
	favouritesId.forEach(favouriteId => {
		url = baseUrl + prefixHeroes + "getCharacterById?id=" + favouriteId;
		request("GET", url, displayFavouriteCbk);
	});
}

/*
 * Display the favourites in the side bar
 */
function displayFavouriteCbk(xhr){
	if (xhr.status == 200) {
		var hero = JSON.parse(xhr.responseText);
		var cardFavourite = createHeroCard(hero, true);  
		favouritesContainer.appendChild(cardFavourite);
	}	
	else
		notify("An error occured.");
}

/*
 * Call the server side api to remove the hero from the user's favourites
 */
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

/*
 * Update the favourites stored in localStorage
 * and display them in the side bar
 */
function removeFromFavouritesCbk(xhr){
	var response = JSON.parse(xhr.responseText);

	if (xhr.status == 200) {
		notify("The hero has been removed from favourites");
		heroId = response['removedFavouriteId'];
		updateFavouritesStorage(heroId, false);
		displayFavourites();
	}
	else
		notify(response["response"]);
}

/*
 * Update the favourites stored in localStorage
 */
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
}

//******************* FAVOURITES