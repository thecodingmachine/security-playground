# security-playground

## Prerequisites

Install Docker & docker-compose on your own following these links :

* Docker: [Get Docker](https://docs.docker.com/engine/installation)
* docker-compose: [Install Docker Compose](https://docs.docker.com/compose/install)

## Setting up the environment

Start by cloning this repository.

Then copy the file `.env.dist` to a file named `.env`. For instance:

```bash
cp .env.dist .env 
```

**Next, make sure that there is no application running on port 80**, then start all the Docker containers with the
following commands:

```bash
docker-compose up -d
```

Enter your web container with docker exec :

```bash
docker exec -ti app bash
```

Install dependencies :

```bash
composer install
```

Run migrations :

```bash
bin/console doctrine:migration:migrate
```

Run fixtures :

```bash
bin/console doctrine:fixtures:load
```
