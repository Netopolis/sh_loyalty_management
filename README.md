*** ENGLISH ***

This is a proof of concept for a loyalty management system, for small to large businesses.

The app and presentation are still in French, but should you need an english version ask us.

Our case study was a laser tag club brand, but this can be easily adapted to other businesses.

Laser tag clubs operate essentially like hotels: they rent property and must maximize bookings.

You can download our presentation slides in the wiki area, but a good demo is better. Watch on youtube [https://www.youtube.com/watch?v=Vgqx2-oihkw](https://www.youtube.com/watch?v=Vgqx2-oihkw)

Requirements: Php 7.1+ and composer https://getcomposer.org/

Install:
- Clone this project
- From a command-line Interface (CLI), go to the project directory
- Run : composer install
- Then run : php bin/console d:d:c 
(doctrine:database:create - For this project, we used Xampp with Maria DB / MySQL - you can change the target database by opening the .env file
search for the line DATABASE_URL=mysql://root:@127.0.0.1:3306/shinigami
and just replace it with a database of your choice, Oracle or PostgreSQL, along with a valid password - Got there ? Then...)
- Run : php bin/console d:m:m
(doctrine:migrations:migrate - if you've changed the database, run first doctrine:migrations:diff)
- Then run : php bin/console d:f:l
(doctrine:fixtures:load - will populate your database with 6 shops, 85 original customers, and 17 employees - they WILL change
dynamically everytime you run this command, complete with random names and addresses, and coherent business history.
To check it, just keep the customers list open in a browser tab, run this command, and open the list again in another tab)

You should now be ready:
- Start the server: php bin/console s:r 
- Start your browser
- Navigate to http://localhost:8000 to visit the Front end of the website (customers area)
- And to http://localhost:8000/admin to visit the Admin back end of the website (restricted area)

How to log in (and see most of the interesting things) :
- Front area, login on clientX@gmail.com, password clientX - where X is a number, we have 85 customers in the db, so X will range from 1 to 85
(you can also create an account, but as a new customer, you will have no previous activity)
- Admin area, login on staffX@gmail.com, password staffX - where X is a number, currently from 1 to 17

- Admin area, you can also log in as an admin - you use our credentials :-)
hugueswf3@gmail.com, password hugues
cboucajay@gmail.com, password cecile
Then you will be able to see everything, including options restricted to the administrators.

You can also switch between two browsers, one for the Front end http://localhost:8000 and one for the Admin back end http://localhost:8000/admin
Then you will be able to follow operations in real time.

Last words : make sure you go down into the menus and try several things.
- On the Front end, log in as various customers to see your activity, order a Loyalty Card, register or change your information.
- On the Admin end, search for specific customers by typing part of their name (left, below the nav menu), check individual centers,
get the global customers activity list, look up individual customers, check their activity tab, deactivate or reactivate loyalty cards,
edit things and reassign staff.

Try everything in the app, and drop us a word if you like it.

Hugues and Cecile




*** FRENCH *** (English version above)

Ce logiciel est libre.

Environnement: Php 7.1.3, Symfony 4.2.2, MariaDB 10.1.37 (Xampp, compatible MySQL 5.6) - Vous pouvez facilement changer la base de données.

C'est la peuve de concept d'un système de gestion de fidélité clients, pour petites et grandes entreprises.

Notre cas d'études était une chaîne de centres de laser game, mais l'application peut être facilement adaptée à d'autres coeur de métier.
Les clubs laser opèrent essentiellement comme les hôtels: ils louent un bien immobilier et doivent maximiser leurs réservations.

Si vous souhaitez l'étendre, n'hésitez pas à nous contacter : cboucajay@gmail.com, huguesww3@gmail.com

Vous trouverez les slides de notre présentation dans la partie Wiki de ce projet, mais une bonne démo vaut mieux. Démo sur youtube [https://youtu.be/43YRfoXb7Eo](https://youtu.be/43YRfoXb7Eo))

Il vous faudra: Php 7.1+ et composer https://getcomposer.org/

Installation:
- Clonez le projet
- En ligne de commande, allez dans le répertoire du projet
- Lancez : composer install
- Puis : php bin/console d:d:c 
(doctrine:database:create - Nous avons utilisé Xampp avec Maria DB / MySQL - vous pouvez changer la base de données en ouvrant le fichier .env
cherchez la ligne DATABASE_URL=mysql://root:@127.0.0.1:3306/shinigami
et remplacez la simplement par n'importe quelle base, Oracle ou PostgreSQL, avec un mot de passe valide - C'est fait ? Allons-y...)
- Lancez : php bin/console d:m:m
(doctrine:migrations:migrate - faites d'abord doctrine:migrations:diff, si vous avez changé de base)
- Puis : php bin/console d:f:l
(doctrine:fixtures:load - ce qui peuplera votre base avec 6 boutiques, 85 clients, et 17 employés - Ils changeront tous dynamiquement 
chaque fois que vous exécuterez cette commande, avec noms et adresses aléatoires, et historique commercial cohérent.
Pour le vérifier, gardez une liste des clients dans un onglet, lancez la commande, et ouvrez la liste à nouveau dans un autre onglet)

Si vous êtes prêt :
- Lancez le serveur : php bin/console s:r 
- Puis votre navigateur
- Allez sur http://localhost:8000 pour visiter la partie Front du site (espace clients)
- Et sur http://localhost:8000/admin pour voir la partie Admin (reservée au staff)

Comment vous connecter (et voir la plupart des choses intéressantes) :
- Côté Front, connectez-vous avec clientX@gmail.com, mot de passe clientX - où X est un chiffre de 1 à 85, nous avons 85 clients en base
(vous pouvez aussi créer un compte, mais en tant que nouveau client vous n'aurez pas d'historique d'activité)
- Côté Admin, login avec staffX@gmail.com, mot de passe staffX - où X est un chiffre, actuellement de 1 de 17

- Côté Admin, vous pouvez vous connecter en tant qu'administrateur - Nous vous donnons nos identifiants, faites en bon usage :-)
hugueswf3@gmail.com, mot de passe hugues
cboucajay@gmail.com, mot de passe cecile
Alors vous pourrez tout voir, y compris les options réservées aux administrateurs.

Vous pouvez aussi utiliser deux navigateurs, un pour le Front http://localhost:8000 et un pour l'Admin http://localhost:8000/admin
Vous pourrez alors suivre les opérations en temps réel.

Un dernier mot : pour tout voir, assurez-vous de descendre dans les menus et d'essayer plusieurs choses.
- Côté Front, connectez-vous en tant que divers clients pour voir leur activité, demandez une Carte de Fidélité, inscrivez-vous ou changez vos données.
- Côté Admin, recherchez des clients précis en tapant une partie de leur nom (à gauche, sous le menu de nav), vérifiez des centres,
affichez la liste des activités client, consultez des fiches individuelles, vérifiez leur onglet activité, désactivez ou réactivez des cartes de fidélité,
modifiez des données et réaffectez le personnel.

Essayez tout ce que vous voulez dans l'application, et envoyez-nous un mot si elle vous a plu.

Hugues et Cecile




