# FRENCH 
*(English version below)*

Environnement: Php 7.1.3, Symfony 4.2.2, MariaDB 10.1.37 (Xampp, compatible MySQL 5.6) - Vous pouvez changer facilement la base de données.

Système de gestion de fidélité clients, pour petites et grandes entreprises.

Notre cas d'études était une chaîne de laser game, mais l'application peut être facilement adaptée à d'autres coeur de métier.
Les clubs laser fonctionnent essentiellement comme des hôtels: ils louent un bien immobilier et doivent maximiser leurs réservations.

Si vous souhaitez l'étendre ou l'adapter, n'hésitez pas à nous contacter : cboucajay@gmail.com, huguesww3@gmail.com

Vous trouverez les slides de notre présentation dans la partie Wiki de ce projet, mais une bonne démo vaut 1000 mots.
Démo sur [https://youtu.be/Vgqx2-oihkw](https://youtu.be/Vgqx2-oihkw)

Pré-requis: Php 7.1+ et composer https://getcomposer.org/

Installation:
- Clonez le projet
- Allez en ligne de commande dans le répertoire du projet
- Lancez : composer install
- Puis : php bin/console d:d:c 
(doctrine:database:create - Nous avons utilisé Xampp avec Maria DB / MySQL - vous pouvez changer la base en ouvrant le fichier .env
cherchez la ligne DATABASE_URL=mysql://root:@127.0.0.1:3306/shinigami
remplacez-la par n'importe quelle base, Oracle ou PostgreSQL, avec un mot de passe valide - C'est fait ?...)
- Lancez : php bin/console d:m:m
(doctrine:migrations:migrate - d'abord doctrine:migrations:diff, si vous avez changé la base)
- Puis : php bin/console d:f:l
(doctrine:fixtures:load - peuplera votre base avec 6 boutiques, 85 clients, et 17 employés - Ils changeront à 
chaque exécution de cette commande, avec noms et adresses aléatoires, historique commercial cohérent.
Pour le vérifier, gardez une liste des clients dans un onglet, lancez la commande, et ouvrez la liste à nouveau dans un autre onglet)

Une fois prêt :
- Lancez le serveur : php bin/console s:r 
- Ouvrez un navigateur
- Allez sur http://localhost:8000 pour visiter la partie Front du site (espace clients)
- Et http://localhost:8000/admin pour la partie Admin (reservée au staff)

Comment vous connecter (et voir la plupart des choses intéressantes) :
- Côté Front, connectez-vous avec clientX@gmail.com, mot de passe clientX - où X est un chiffre de 1 à 85, il y a 85 clients en base
(vous pouvez aussi créer un compte, mais en tant que nouveau client vous n'aurez pas d'historique)
- Côté Admin, login avec staffX@gmail.com, mot de passe staffX - où X est un chiffre, actuellement de 1 de 17

- Côté Admin, vous pouvez vous connecter en tant qu'administrateur - Nous vous donnons nos identifiants, faites en bon usage :-)
hugueswf3@gmail.com, mot de passe hugues
cboucajay@gmail.com, mot de passe cecile
Vous aurez accès à tout, y compris les options réservées aux administrateurs.

Vous pouvez aussi utiliser deux navigateurs, un pour le Front http://localhost:8000 et un pour l'Admin http://localhost:8000/admin
Vous pourrez alors suivre les opérations en temps réel.

Un dernier mot : l'application est plutôt vaste, assurez-vous d'essayer plusieurs fonctionnalités.
- Côté Front, connectez-vous en tant que divers clients pour voir leur activité, demandez une Carte de Fidélité, inscrivez-vous ou changez vos données.
- Côté Admin, recherchez des clients en tapant une partie de leur nom (à gauche, sous le menu de nav), vérifiez des centres, affichez la liste des activités client, consultez des fiches individuelles, et leur onglet activité, désactivez ou réactivez des cartes de fidélité, modifiez et réaffectez le personnel.

Essayez tout ce qu'il vous plaît, et envoyez-nous un mot si vous l'appréciez.

Hugues et Cecile


# ENGLISH

This is a proof of concept for a loyalty management system, for small to large businesses.

The website and the presentation are still in French, but should you need an english version, ask us.

Our case study was a laser tag club brand, but this can be easily adapted to other businesses.
Laser tag clubs operate, at the core, like hotels: they rent property and must maximize bookings.

You can download our presentation slides in the wiki area of this repo, but a good demo is probably better.
Live demo at [https://youtu.be/Vgqx2-oihkw](https://youtu.be/Vgqx2-oihkw)

Requirements: Php 7.1+ and composer https://getcomposer.org/)

How to install :
- Clone this project
- With a terminal go to the project directory
- Run : composer install
- Then run : php bin/console d:d:c 
(doctrine:database:create - For this project, we used Xampp with Maria DB / MySQL - you can change the target database by opening the .env file
search for the line DATABASE_URL=mysql://root:@127.0.0.1:3306/shinigami
and just replace it with any other database, Oracle or PostgreSQL, along with a valid password - Got there ? Then...)
- Run : php bin/console d:m:m
(doctrine:migrations:migrate - if you changed the database, run first doctrine:migrations:diff)
- Then run : php bin/console d:f:l
(doctrine:fixtures:load - this will populate your database with 6 shops, 85 original customers, and 17 employees - they will change everytime you run this command, complete with random names and addresses, and coherent business history.
Check it, just keep the customers list open in a browser tab, run this command, and open the list again in another tab)

Anyway you should be ready:
- Start the server: php bin/console s:r 
- Fire your favorite browser
- Navigate to http://localhost:8000 to visit the Front part of the website (customers area)
- And http://localhost:8000/admin to visit the Admin part of the website (restricted area)

How to log in (and see most of the interesting things) :
- Front area, login on clientX@gmail.com, password clientX - where X is a number, we have 85 customers in the db, so X will range from 1 to 85
(you can also create an account, but as a new customer, you will have no previous activity)
- Admin area, login on staffX@gmail.com, password staffX - where X is a number, currently from 1 to 17

- Admin area, you can also log in as an admin -we'll let you use our credentials :-)
hugueswf3@gmail.com, password hugues
cboucajay@gmail.com, password cecile
Then you'll have access to everything, including options restricted to the administrators.

You can also switch between two browsers, one for the Front http://localhost:8000 and one for the Admin http://localhost:8000/admin to follow operations in real time.

One last word : this application has a lot to it, so make sure you try several things in the menus.
- On the Front side, log in as various customers to see your activity, order a Loyalty Card, register or change your information.
- On the Admin side, search for specific customers by typing part of their name (left, below the nav menu), check individual centers, get the global customers activity list, look up individual customers, check their activity tab, deactivate or reactivate loyalty cards, edit things and reassign staff.

Try everything in the app, and drop us a word if you like it.

Hugues and Cecile

