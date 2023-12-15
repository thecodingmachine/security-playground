# security-playground

cp .env.dist .env
docker compose up -d
Accéder au conteneur web : docker exec -ti app web
composer install
bin/console do:mi:mi
bin/console doctrine:fixtures:load
bin/console doctrine:fixtures:load