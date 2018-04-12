# Tap and Go

## Getting started

### 1. Clone the repository

```bash
git clone https://github.com/noemi-salaun/tap-and-go.git
cd tap-and-go
```

### 2. Start the docker environment

#### The easy way with Makefile
```bash
make start
```

#### The hard way
```bash
docker-compose up -d
docker-compose exec php bash
```
Then in the container shell 
```bash
composer install
yarn install
yarn run encore dev

./bin/console doctrine:database:create
./bin/console doctrine:migrations:migrate -n
./bin/console doctrine:fixtures:load -n
```

### 3. Play with it

The project comes with fixtures for Cities and Stations, and 2 in-memory users.

#### Admin pages

- Go to `http://localhost:8000` to see the admin page
    - **login:** admin
    - **password:** admin
    
#### JSON API

*The API is protected with JWT authentication*

- Start a REST client like Postman
- Make a POST to `http://localhost:8000/api/login_check` with params:
    - **`_username`**: john
    - **`_password`**: john
- Take the value of the token inside the response
- Use this token in an `Authorization` header with value:
    - `Bearer <jwt_token>` 
- Make a call on the API:
    - **GET** `/api/cities?page=0&limit=10`
    - **GET** `/api/stations/<cityId>?page=0&limit=10`
    - **GET** `/api/stations/near?lat=xx.xx&lng=yy.yy&radius=10`
    - **POST** `/api/stations/<stationId>/take`
    - **POST** `/api/stations/<stationId>/drop`
    
### 4. Unit tests

*Unit tests should be perform against original fixtures.
Because of the coordinates request algorithm, in-memory database with `sqlite` was not an option.*

```bash
make test
```
This command will reset the fixtures and run the unit tests.

### 5. SCSS compilation

Assets compilation is handle by [Symfony Webpack-Encore](https://symfony.com/doc/current/frontend.html).

```bash
make encore
```
This command will run webpack-encore in watch mode, and recompile the assets for every changes.

### 6. Make commands
- `make php`: get inside the php container with bash
- `make s=<container> sh`: get inside a container with sh
- `make start`: destroy and restart Docker
- `make db`: destroy and recreate the database
- `make test`: run `make db` then run the unit tests
- `make encore`: run webpack-encore in watch mode