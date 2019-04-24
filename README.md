# PeaksProject
********* Installation

-Apres la recuperation des sources git, aller dans PeaksProject/Server/marvel:
 cd PeaksProject/Server/marvel/

-Lancer la comande:
 composer install

-Dans le fichier .env modifier le nom de l'hote et le mot de passe du owner de mysql
-Creation de la base de données:
	php bin/console doctrine:database:create

-Creation du schéma de la base de données:
	php bin/console doctrine:schema:create

La base de données est vide, vous pouvez créer vos propres users et vous connecter pour selectionner vos heros preferes, ou vous pouvez charger quelques données dans les fixtures:
	php bin/console doctrine:fixtures:load
=> répondre yes

-Lancer le serveur symfony:
	php -S 127.0.0.1:8000 -t public

-Placer le dossier Client dans le server web apache local, puis lancer le fichier index.html via le navigateur


*********** tests
-Lancer le serveur symfony:
	php -S 127.0.0.1:8000 -t public
****initalisation de la bdd

-Suppression de la base de donnée:
	php bin/console doctrine:database:drop --force

-Creation de la base de donnée:
	php bin/console doctrine:database:create

-Creation du schéma de la base de données:
	php bin/console doctrine:schema:create

-Chargement des fixtures:
	php bin/console doctrine:fixtures:load
=> répondre yes

****lancement des tests:
./bin/phpunit

