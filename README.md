# Pet Shop API test
This project is the API task. that will demonstrate the requested features/endpoint.
Using **Laravel 11**

## Requirements 
- PHP 8.x
- Laravel 11

## Installation 
You are not required to install php in your machine. All you have to do is to install [Docker](https://www.docker.com/ "Docker") in your machine.

#### Set up the application
The application is out-of-the-box app. All you have to do is to run the following commands in the root folder. And you will have a ready application:
```bash
# Copy .env.example file to .env
cp .env.example .env

# Create api server
docker-compose up -d

# Generate app key
docker exec -it petshop_api php artisan migrate

# Seed the database
docker exec -it petshop_api php artisan db:seed

# Generate rsa private/public key for JWT
docker exec -it petshop_api php artisan app:generate-token-keys
```

#### Shut down the application
Stoping the docker server is reversible as necessary datas as stored in binding volumes. you can stop the server by running the following command.
```bash
# Stop the server
docker-compose down
```

##### Important
For documentation purpose the application database will be seeded automatically everyday to make sure of the datas integrity for proper API testing.

## Application details
- Application url : http://localhost:7001
- Application Swagger API Url : http://localhost:7001/api/documentation#/