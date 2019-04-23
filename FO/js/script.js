window.onload = function(){
	//*********INITIALIZATION

	//Display Connection Forms or hide them if user already connected
	displayIsConnectedForms();

	//Display notifications
	displayIsConnectedNotifications();

	//Display the heroes cards
	displayHeroes();

	//Display the favourites
	displayFavourites();
}