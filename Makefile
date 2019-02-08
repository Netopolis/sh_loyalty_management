sync:
	git pull
	composer install
#	make syncdb

installdb:
	php bin/console doctrine:schema:validate
	php bin/console doctrine:database:create
#	make syncdb

regendb:
#   régénère la db, puis charge les fixtures
#   suppose qu'il y ait déjà un fichier de migration à jour, sinon --> php bin/console doctrine:migrations:diff
    php bin/console doctrine:database:drop --if-exists --force
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
#   on vérifie que tout soit OK
    php bin/console doctrine:schema:validate
    php bin/console doctrine:fixtures:load

#syncdb:
#	php bin/console doctrine:migrations:migrate
#	php bin/console doctrine:fixtures:load