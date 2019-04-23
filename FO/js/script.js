window.onload = function(){
	//*********INITIALIZATION

	//Display Connection Forms or hide them if user already connected
	displayIsConnectedForms(isAuthent());

	//Display notifications
	displayIsConnectedNotifications(isAuthent());

	//Display the heroes cards
	displayHeroes();

	//Display the favourites
	displayFavourites();
}