# PeaksProject
********* Installation

-Apres la recuperation des sources git
	Git clone *******(mettre en public surement)
	**********Ou envoyer le fichier zip

-Aller dans PeaksProject/Server/marvel
$ cd PeaksProject/Server/marvel/

-Lancer la comande
$ composer install

-Dans le fichier .env modifier le nom de l'hote et le mot de passe du owner de mysql
-Creation de la base de donnée
$	php bin/console doctrine:database:create

-Creation du schéma de la base de données
	php bin/console doctrine:schema:create

La base de données est vide, vous pouvez créer vos propres user et vous connecter pour selectionner vos heros preferes

-Lancer le serveur symfony
	php -S 127.0.0.1:8000 -t public




-Placer le fichier FO dans le server web apache local, puis lancer le fichier index.html via le navigateur


*********** tests
initalisation de la bdd

-Suppression de la base de donnée
	php bin/console doctrine:database:drop --force

-Creation de la base de donnée
	php bin/console doctrine:database:create

-Creation du schéma de la base de données
	php bin/console doctrine:schema:create

-Chargement des fixtures
	php bin/console doctrine:fixtures:load
=> répondre yes

lancement des tests
./bin/phpunit

