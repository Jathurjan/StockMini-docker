::: {align="center"}
# 📦 StockMini

### 🛒 Laravel Inventory Management System \| Dockerized DevOps Practice Project

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Nginx](https://img.shields.io/badge/Nginx-Alpine-009639?style=for-the-badge&logo=nginx&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**A simple inventory management application containerized with Docker
Compose.**

[🚀 Quick Start](#-quick-start) · [🏗️
Architecture](#️-docker-architecture) · [🧰 DevOps
Practice](#-devops-practice) · [🐛 Troubleshooting](#-troubleshooting)
:::

------------------------------------------------------------------------

## 📖 About the Project

**StockMini** is a Laravel-based inventory management application built
as a hands-on project to practise application containerization and
deployment fundamentals.

The application runs as three separate services:

-   **Laravel + PHP-FPM** --- application runtime
-   **Nginx** --- web server and reverse proxy to PHP-FPM
-   **MySQL** --- relational database

Docker Compose manages the services, their network, and persistent
database storage.

## ✨ Technology Stack

  Technology       Purpose
  ---------------- ------------------------------------------------
  Laravel 13       PHP web application framework
  PHP 8.4-FPM      Runs the Laravel application
  Nginx Alpine     Handles HTTP requests and serves public assets
  MySQL 8.4        Stores application data
  Docker           Builds and runs containers
  Docker Compose   Manages the multi-container application

## 🏗️ Docker Architecture

``` text
                 🌐 Web Browser
               http://localhost:8000
                        |
                        v
              ┌──────────────────┐
              │   Nginx Container│
              │   Host: 8000     │
              │   Container: 80  │
              └────────┬─────────┘
                       |
                 FastCGI :9000
                       |
                       v
              ┌──────────────────┐
              │ Laravel + PHP-FPM│
              │   App Container  │
              └────────┬─────────┘
                       |
                  MySQL :3306
                       |
                       v
              ┌──────────────────┐
              │  MySQL Container │
              │   Database       │
              └──────────────────┘

       Docker Compose network: stockmini_default
       Persistent database volume: mysql_data
```

### 🔌 Service and port details

  ---------------------------------------------------------------------------------
  Service          Image / Build           Container port Host access
  ---------------- ---------------- --------------------- -------------------------
  `app`            Built from              9000 (PHP-FPM) Internal Docker network
                   `Dockerfile`                           only

  `nginx`          `nginx:alpine`                      80 `http://localhost:8000`

  `db`             `mysql:8.4`                       3306 Internal Docker network
                                                          only
  ---------------------------------------------------------------------------------

> **Note:** PHP-FPM port `9000` and MySQL port `3306` are not published
> to the Windows host in the current Compose configuration. Nginx
> reaches PHP-FPM using `app:9000`, and Laravel reaches MySQL using
> `db:3306`.

## 📋 Prerequisites

Install these tools before starting:

-   [Docker Desktop](https://www.docker.com/products/docker-desktop/)
    --- make sure the Docker Engine is running.
-   [Git](https://git-scm.com/) --- optional, for cloning and version
    control.
-   A terminal such as PowerShell.

## 🚀 Quick Start

### 1️⃣ Get the project

If the project is already on your computer, open PowerShell in the
project directory.

To clone a repository, use your own Git repository URL:

``` powershell
git clone <YOUR_REPOSITORY_URL>
cd StockMini
```

### 2️⃣ Configure environment variables

The Docker build excludes `.env` through `.dockerignore`, so the local
`.env` file is not copied into the application image.

Make sure the `app` service in `docker-compose.yml` receives the
required Laravel and database environment variables. At minimum,
configure:

``` yaml
services:
  app:
    environment:
      APP_ENV: production
      APP_DEBUG: "false"
      APP_KEY: "base64:YOUR_VALID_LARAVEL_KEY"
      DB_CONNECTION: mysql
      DB_HOST: db
      DB_PORT: 3306
      DB_DATABASE: stockmini
      DB_USERNAME: stockmini_user
      DB_PASSWORD: stockmini_pass
```

The `APP_KEY` must be a valid Laravel key. Generate one if needed:

``` powershell
docker compose run --rm app php artisan key:generate --show
```

Copy the generated value into the `APP_KEY` setting. Do not commit real
application keys or production passwords to a public repository. For
real deployments, use an untracked environment file or your deployment
platform's secret manager.

The MySQL service must use matching database credentials, for example:

``` yaml
  db:
    image: mysql:8.4
    environment:
      MYSQL_DATABASE: stockmini
      MYSQL_USER: stockmini_user
      MYSQL_PASSWORD: stockmini_pass
      MYSQL_ROOT_PASSWORD: CHANGE_THIS_ROOT_PASSWORD
    volumes:
      - mysql_data:/var/lib/mysql
```

> The values above are examples for local practice. Replace sample
> passwords and keep secrets out of Git.

### 3️⃣ Validate the Compose configuration

``` powershell
docker compose config
```

This checks and renders the Compose configuration. It does **not** build
images or start containers.

### 4️⃣ Build the application image

``` powershell
docker compose build
```

This builds the Laravel/PHP-FPM image using the project's `Dockerfile`.
It does not start the services.

### 5️⃣ Start all services

``` powershell
docker compose up -d
```

The `-d` option runs the containers in detached mode (in the
background).

To build the image and start the services in one command:

``` powershell
docker compose up -d --build
```

### 6️⃣ Run database migrations

On the first run, create the database tables inside the **Docker MySQL
database**:

``` powershell
docker compose exec app php artisan migrate
```

If Laravel is configured with `APP_ENV=production`, Artisan may ask for
confirmation. Review the target database, then confirm. For an
intentional deployment migration where confirmation must be bypassed:

``` powershell
docker compose exec app php artisan migrate --force
```

### 7️⃣ Check the containers

``` powershell
docker compose ps
```

The `app`, `nginx`, and `db` services should show as running (`Up`).

### 8️⃣ Open StockMini

Visit:

**http://localhost:8000**

🎉 StockMini should now be available through the Nginx container.

------------------------------------------------------------------------

## 🧰 DevOps Practice

Use StockMini to practise the following tasks and understand how the
services work together.

### 🔍 Container management

``` powershell
# List services and their status
docker compose ps

# Start services
docker compose start

# Stop services without removing containers
docker compose stop

# Restart services
docker compose restart

# Follow logs from all services
docker compose logs -f

# Follow logs from one service
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f db

# Open a shell inside the app container
docker compose exec app sh
```

### 🏗️ Build and deployment practice

``` powershell
# Build or rebuild images
docker compose build

# Rebuild and recreate services
docker compose up -d --build

# Recreate only the app service after changing its environment
docker compose up -d --force-recreate app

# Clear Laravel's cached configuration
docker compose exec app php artisan config:clear

# Run Laravel migrations
docker compose exec app php artisan migrate
```

### 🗄️ Database and persistent storage

The Compose file declares a named volume called `mysql_data` and mounts
it at MySQL's data directory:

``` yaml
volumes:
  - mysql_data:/var/lib/mysql
```

This keeps database files outside the lifecycle of an individual MySQL
container.

``` powershell
# List Docker volumes
docker volume ls

# Inspect the project database volume
docker volume inspect stockmini_mysql_data
```

**Important difference:**

``` powershell
# Stop and remove containers and the Compose network.
# The named database volume is kept.
docker compose down

# Stop and remove containers, network, AND named volumes.
# This deletes the StockMini Docker database data.
docker compose down -v
```

⚠️ Do not use `docker compose down -v` unless you intentionally want to
delete the Docker database volume and its data.

### 🧪 Suggested practice challenges

-   [ ] Stop and start each service; observe the effect on the
    application.
-   [ ] Inspect the Nginx, Laravel, and MySQL logs.
-   [ ] Rebuild the Laravel image after changing the Dockerfile.
-   [ ] Add a health check for the MySQL service.
-   [ ] Learn how Compose service names provide internal DNS (`app`,
    `db`, `nginx`).
-   [ ] Practise backing up and restoring the MySQL database.
-   [ ] Move secrets out of the committed Compose file and into a secure
    environment configuration.
-   [ ] Deploy the application to a Linux server with a domain and
    HTTPS.

## 🐛 Troubleshooting

### 500 --- Server Error

Check the Laravel application log:

``` powershell
docker compose exec app sh -c "tail -n 80 storage/logs/laravel.log"
```

Check service logs:

``` powershell
docker compose logs app --tail=80
docker compose logs nginx --tail=80
docker compose logs db --tail=80
```

### `No application encryption key has been specified`

Ensure the `app` service receives a valid `APP_KEY` environment
variable. Then recreate the app container and clear Laravel's
configuration cache:

``` powershell
docker compose up -d --force-recreate app
docker compose exec app php artisan config:clear
```

Do not publish or commit the key.

### `Unsupported cipher or incorrect key length`

Check that `APP_KEY` is a valid Laravel-generated key and that the
configured cipher is compatible with its key length. Generate a valid
key with:

``` powershell
docker compose run --rm app php artisan key:generate --show
```

Update the app service's `APP_KEY`, recreate the app container, and
clear the configuration cache.

### `Table 'stockmini.sessions' doesn't exist`

The Docker MySQL database is separate from a local WampServer database.
Run migrations against the Docker database:

``` powershell
docker compose exec app php artisan migrate
```

If the application uses database sessions, make sure a sessions-table
migration exists in the project. If it does not, create it with:

``` powershell
docker compose exec app php artisan make:session-table
docker compose exec app php artisan migrate
```

### Database connection errors

Check that the Laravel app uses the Compose service name as its database
host:

``` yaml
DB_HOST: db
DB_PORT: 3306
```

Inside a Compose network, `localhost` refers to the current container,
not the MySQL container.

## 🧹 Stop the project

``` powershell
docker compose down
```

This removes the containers and Compose network but keeps the named
`mysql_data` volume.

To start again later:

``` powershell
docker compose up -d
```

## 📁 Main Docker files

``` text
StockMini/
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── nginx/
│   └── default.conf
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
└── ...
```

The Laravel folders shown above are part of the application. The key
Docker configuration files are `Dockerfile`, `docker-compose.yml`,
`.dockerignore`, and `nginx/default.conf`.

## 🎯 Learning Outcome

Through this project, I practised:

-   Containerizing a Laravel application with PHP-FPM.
-   Serving the application through Nginx.
-   Running MySQL in a separate container.
-   Connecting services through a Docker Compose network.
-   Persisting database data with a named volume.
-   Managing environment variables and Laravel application keys.
-   Running migrations inside a container.
-   Troubleshooting application errors through container and Laravel
    logs.

------------------------------------------------------------------------

::: {align="center"}
### 💻 Built for hands-on Full Stack & DevOps learning

**StockMini --- Learn. Build. Containerize. Deploy.**
:::
