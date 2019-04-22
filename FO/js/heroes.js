var prefixHeroes = "marvel/";
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

function createHeroCard(hero)
{
	let card = document.createElement('div');
	let cardName = document.createElement('div');
	let cardPicture = document.createElement('img');
	let cardDescription = document.createElement('div');
	card.appendChild(cardName);
	card.appendChild(cardPicture);
	card.appendChild(cardDescription);

	cardName.innerHTML = hero['name'];
	cardName.classList.add('cardName');

	urlPicture = hero['path'] + "/portrait_xlarge." + hero['extension']
	cardPicture.src = urlPicture;
	cardPicture.setAttribute("onclick", "getModalDetails(this, " + hero['id'] + ")");
	cardPicture.classList.add('cardPicture');

	cardDescription.innerHTML = (hero['description'] == '' ? "No Description" : hero['description']);
	cardDescription.classList.add('cardDescription');

	card.classList.add('card');
	
	heroesDisplayer.appendChild(card);
}

function getModalDetails(element, id) {
	transfertDetailsToModal(element, id);// transfer hero data from the card to the modal

	url = baseUrl + prefixHeroes + 'getThreeFirstComicsByCharacterId?id=' + id;
	// request additional data about comics and display in the modal
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
	if (event.target == modal) {
		display(modal, false);
	}
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
		heroes.forEach(hero => createHeroCard(hero));
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

		//Add Favourite button if user is connected
		if (isAuthent()){
			var addFavBtn = document.createElement('button');
			addFavBtn.innerHTML = "Add to Favourites";

			var heroId = modal.getAttribute("data-id");
			addFavBtn.setAttribute("onclick", "addToFavourites(" + heroId + ")");
			addFavBtn.classList.add('btn');
			modal.querySelector('.dataModal').append(addFavBtn);
		}
		display(modal);
	}
}


function addToFavourites(heroId){
	if (!isAuthent())
		return
	else{
		var data = {
			userId : localStorage.getItem("userId"),
			heroId : heroId
		}
		// request("POST", baseUrl + prefixUser +'addToFavourites', addToFavouritesCbk, data);
	}
}